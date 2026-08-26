<?php
namespace App\Models;

class FinanceRecord extends BaseModel {
    protected $table = 'finance_records';
    // Primary ownership is at church level; unit is optional (church-wide vs unit-specific)
    protected $fillable = ['church_id', 'unit_id', 'user_id', 'member_id', 'recorded_by', 'transaction_type', 'amount', 'category', 'description', 'transaction_date', 'payment_method', 'reference_number'];
    
    // Note: Migration originally used 'recorded_by' instead of 'user_id'. Model now supports both fields.

    /**
     * Get finance records for a set of unit IDs (e.g. church's units), optionally filtered by date range.
     */
    public function getFinanceWithDetailsByUnitIds(array $unitIds, $orderBy = null, $startDate = null, $endDate = null, $limit = null) {
        if (empty($unitIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
        $sql = "SELECT f.*, u.name as unit_name, us.first_name, us.last_name 
                FROM finance_records f 
                LEFT JOIN units u ON f.unit_id = u.id 
                LEFT JOIN users us ON f.recorded_by = us.id
                WHERE f.unit_id IN ({$placeholders})";
        $params = $unitIds;
        $types = str_repeat('i', count($unitIds));
        if ($startDate) {
            $sql .= " AND f.transaction_date >= ?";
            $params[] = $startDate;
            $types .= 's';
        }
        if ($endDate) {
            $sql .= " AND f.transaction_date <= ?";
            $params[] = $endDate;
            $types .= 's';
        }
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        } else {
            $sql .= " ORDER BY f.transaction_date DESC, f.created_at DESC";
        }
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = (int)$limit;
            $types .= 'i';
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get finance records with optional date range (for global view, no unit filter).
     */
    public function getFinanceWithDetails($conditions = [], $orderBy = null, $startDate = null, $endDate = null) {
        $sql = "SELECT 
                    f.*, 
                    u.name as unit_name, 
                    us.first_name, 
                    us.last_name,
                    member.first_name AS member_first_name,
                    member.last_name AS member_last_name
                FROM finance_records f 
                LEFT JOIN units u ON f.unit_id = u.id 
                LEFT JOIN users us ON f.recorded_by = us.id
                LEFT JOIN users member ON f.member_id = member.id";
        $params = [];
        $types = "";
        $where = [];
        if (!empty($conditions)) {
            foreach ($conditions as $field => $value) {
                $where[] = "f.{$field} = ?";
                $params[] = $value;
                $types .= is_int($value) ? "i" : "s";
            }
        }
        if ($startDate) {
            $where[] = "f.transaction_date >= ?";
            $params[] = $startDate;
            $types .= 's';
        }
        if ($endDate) {
            $where[] = "f.transaction_date <= ?";
            $params[] = $endDate;
            $types .= 's';
        }
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        } else {
            $sql .= " ORDER BY f.transaction_date DESC, f.created_at DESC";
        }
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get monthly income/expense totals for chart (by unit IDs or all if unitIds null).
     * Returns array of [ 'label' => 'Jan 2024', 'income' => x, 'expense' => y ].
     */
    public function getMonthlyTotals(array $unitIds = null, $startDate = null, $endDate = null) {
        $where = ["1=1"];
        $params = [];
        $types = '';
        if ($unitIds !== null && !empty($unitIds)) {
            $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
            $where[] = "unit_id IN ({$placeholders})";
            $params = array_merge($params, $unitIds);
            $types .= str_repeat('i', count($unitIds));
        }
        if ($startDate) {
            $where[] = "transaction_date >= ?";
            $params[] = $startDate;
            $types .= 's';
        }
        if ($endDate) {
            $where[] = "transaction_date <= ?";
            $params[] = $endDate;
            $types .= 's';
        }
        $sql = "SELECT 
                    DATE_FORMAT(transaction_date, '%Y-%m') AS month_key,
                    SUM(CASE WHEN transaction_type = 'income' THEN amount ELSE 0 END) AS income,
                    SUM(CASE WHEN transaction_type = 'expense' THEN amount ELSE 0 END) AS expense
                FROM finance_records
                WHERE " . implode(" AND ", $where) . "
                GROUP BY month_key
                ORDER BY month_key ASC";
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'label' => date('M Y', strtotime($row['month_key'] . '-01')),
                'income' => (float)($row['income'] ?? 0),
                'expense' => (float)($row['expense'] ?? 0),
            ];
        }
        return $result;
    }

    /**
     * Get category breakdown for chart (income or expense by category).
     * Returns array of [ 'category' => label, 'total' => amount ].
     */
    public function getCategoryBreakdown(array $unitIds = null, $startDate = null, $endDate = null, $transactionType = 'income') {
        $where = ["transaction_type = ?"];
        $params = [$transactionType];
        $types = 's';
        if ($unitIds !== null && !empty($unitIds)) {
            $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
            $where[] = "unit_id IN ({$placeholders})";
            $params = array_merge($params, $unitIds);
            $types .= str_repeat('i', count($unitIds));
        }
        if ($startDate) {
            $where[] = "transaction_date >= ?";
            $params[] = $startDate;
            $types .= 's';
        }
        if ($endDate) {
            $where[] = "transaction_date <= ?";
            $params[] = $endDate;
            $types .= 's';
        }
        $sql = "SELECT category, SUM(amount) AS total
                FROM finance_records
                WHERE " . implode(" AND ", $where) . "
                GROUP BY category
                ORDER BY total DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'category' => $row['category'] ?: 'other',
                'total' => (float)($row['total'] ?? 0),
            ];
        }
        return $result;
    }

    /**
     * Get summary for a set of unit IDs (church-scoped)
     */
    public function getSummaryByUnitIds(array $unitIds, $startDate = null, $endDate = null) {
        if (empty($unitIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
        $sql = "SELECT transaction_type, SUM(amount) as total, COUNT(*) as count
                FROM finance_records WHERE unit_id IN ({$placeholders})";
        $params = $unitIds;
        $types = str_repeat('i', count($unitIds));
        if ($startDate) {
            $sql .= " AND transaction_date >= ?";
            $params[] = $startDate;
            $types .= 's';
        }
        if ($endDate) {
            $sql .= " AND transaction_date <= ?";
            $params[] = $endDate;
            $types .= 's';
        }
        $sql .= " GROUP BY transaction_type";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get summary statistics (optionally for a single unit)
     */
    public function getSummary($unitId = null, $startDate = null, $endDate = null) {
        $sql = "SELECT 
                    transaction_type,
                    SUM(amount) as total,
                    COUNT(*) as count
                FROM finance_records";
        
        $params = [];
        $types = "";
        $where = [];
        
        if ($unitId) {
            $where[] = "unit_id = ?";
            $params[] = $unitId;
            $types .= "i";
        }
        
        if ($startDate) {
            $where[] = "transaction_date >= ?";
            $params[] = $startDate;
            $types .= "s";
        }
        
        if ($endDate) {
            $where[] = "transaction_date <= ?";
            $params[] = $endDate;
            $types .= "s";
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " GROUP BY transaction_type";
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get summary statistics scoped by church_id (all records for a church, unit_id may be NULL or any unit under that church).
     */
    public function getSummaryByChurch(int $churchId, $startDate = null, $endDate = null) {
        $sql = "SELECT 
                    transaction_type,
                    SUM(amount) as total,
                    COUNT(*) as count
                FROM finance_records
                WHERE church_id = ?";

        $params = [$churchId];
        $types = "i";

        if ($startDate) {
            $sql .= " AND transaction_date >= ?";
            $params[] = $startDate;
            $types .= "s";
        }

        if ($endDate) {
            $sql .= " AND transaction_date <= ?";
            $params[] = $endDate;
            $types .= "s";
        }

        $sql .= " GROUP BY transaction_type";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get financial summary for all units in a church
     */
    public function getSummaryByUnitsInChurch(int $churchId, $startDate = null, $endDate = null) {
        $sql = "SELECT 
                    u.id as unit_id,
                    u.name as unit_name,
                    SUM(CASE WHEN fr.transaction_type = 'income' THEN fr.amount ELSE 0 END) as total_income,
                    SUM(CASE WHEN fr.transaction_type = 'expense' THEN fr.amount ELSE 0 END) as total_expense,
                    COUNT(fr.id) as transaction_count
                FROM units u
                JOIN church_units cu ON u.id = cu.unit_id AND cu.church_id = ?
                LEFT JOIN finance_records fr ON u.id = fr.unit_id";
        
        $params = [$churchId];
        $types = "i";

        if ($startDate) {
            $sql .= " AND fr.transaction_date >= ?";
            $params[] = $startDate;
            $types .= "s";
        }

        if ($endDate) {
            $sql .= " AND fr.transaction_date <= ?";
            $params[] = $endDate;
            $types .= "s";
        }

        $sql .= " GROUP BY u.id, u.name ORDER BY unit_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get summary statistics grouped by church (for global admin view).
     * Returns one row per church: church_id, church_name, total_income, total_expense, transaction_count.
     */
    public function getSummaryByChurches($startDate = null, $endDate = null) {
        $where = ["1=1"];
        $params = [];
        $types = "";

        if ($startDate) {
            $where[] = "fr.transaction_date >= ?";
            $params[] = $startDate;
            $types .= "s";
        }

        if ($endDate) {
            $where[] = "fr.transaction_date <= ?";
            $params[] = $endDate;
            $types .= "s";
        }

        $sql = "SELECT 
                    fr.church_id,
                    c.name AS church_name,
                    SUM(CASE WHEN fr.transaction_type = 'income' THEN fr.amount ELSE 0 END) AS total_income,
                    SUM(CASE WHEN fr.transaction_type = 'expense' THEN fr.amount ELSE 0 END) AS total_expense,
                    COUNT(*) AS transaction_count
                FROM finance_records fr
                LEFT JOIN churches c ON c.id = fr.church_id
                WHERE " . implode(' AND ', $where) . "
                GROUP BY fr.church_id, c.name
                ORDER BY c.name ASC";

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get monthly totals scoped by church_id.
     */
    public function getMonthlyTotalsByChurch(int $churchId, $startDate = null, $endDate = null) {
        $where = ["church_id = ?"];
        $params = [$churchId];
        $types = 'i';

        if ($startDate) {
            $where[] = "transaction_date >= ?";
            $params[] = $startDate;
            $types .= 's';
        }
        if ($endDate) {
            $where[] = "transaction_date <= ?";
            $params[] = $endDate;
            $types .= 's';
        }

        $sql = "SELECT 
                    DATE_FORMAT(transaction_date, '%Y-%m') AS month_key,
                    SUM(CASE WHEN transaction_type = 'income' THEN amount ELSE 0 END) AS income,
                    SUM(CASE WHEN transaction_type = 'expense' THEN amount ELSE 0 END) AS expense
                FROM finance_records
                WHERE " . implode(" AND ", $where) . "
                GROUP BY month_key
                ORDER BY month_key ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'label' => date('M Y', strtotime($row['month_key'] . '-01')),
                'income' => (float)($row['income'] ?? 0),
                'expense' => (float)($row['expense'] ?? 0),
            ];
        }
        return $result;
    }

    /**
     * Get all financial records scoped by church_id
     */
    public function getByChurchId(int $churchId, $orderBy = null, $startDate = null, $endDate = null) {
        $sql = "SELECT 
                    f.*, 
                    u.name as unit_name, 
                    us.first_name, 
                    us.last_name,
                    member.first_name AS member_first_name,
                    member.last_name AS member_last_name
                FROM finance_records f 
                LEFT JOIN units u ON f.unit_id = u.id 
                LEFT JOIN users us ON f.recorded_by = us.id
                LEFT JOIN users member ON f.member_id = member.id
                WHERE f.church_id = ?";
        
        $params = [$churchId];
        $types = "i";
        
        if ($startDate) {
            $sql .= " AND f.transaction_date >= ?";
            $params[] = $startDate;
            $types .= 's';
        }
        
        if ($endDate) {
            $sql .= " AND f.transaction_date <= ?";
            $params[] = $endDate;
            $types .= 's';
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        } else {
            $sql .= " ORDER BY f.transaction_date DESC, f.created_at DESC";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get category breakdown scoped by church_id.
     */
    public function getCategoryBreakdownByChurch(int $churchId, $startDate = null, $endDate = null, $transactionType = 'income') {
        $where = ["transaction_type = ?", "church_id = ?"];
        $params = [$transactionType, $churchId];
        $types = 'si';

        if ($startDate) {
            $where[] = "transaction_date >= ?";
            $params[] = $startDate;
            $types .= 's';
        }
        if ($endDate) {
            $where[] = "transaction_date <= ?";
            $params[] = $endDate;
            $types .= 's';
        }

        $sql = "SELECT category, SUM(amount) AS total
                FROM finance_records
                WHERE " . implode(" AND ", $where) . "
                GROUP BY category
                ORDER BY total DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'category' => $row['category'] ?: 'other',
                'total' => (float)($row['total'] ?? 0),
            ];
        }
        return $result;
    }

    /**
     * Generate structured Cashflow Statement (Monthly breakdown for a year)
     */
    public function getCashflowStatement($churchId = null, $year = null) {
        $year = $year ?: date('Y');
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthNum = str_pad($m, 2, '0', STR_PAD_LEFT);
            $monthKey = "{$year}-{$monthNum}";
            $months[$monthKey] = [
                'month_name' => date('F', mktime(0, 0, 0, $m, 10)),
                'month_short' => date('M', mktime(0, 0, 0, $m, 10)),
                'month_key' => $monthKey,
                'operating_inflows' => 0.0,
                'operating_outflows' => 0.0,
                'net_cashflow' => 0.0,
                'closing_balance' => 0.0,
                'categories_in' => [],
                'categories_out' => []
            ];
        }

        // Fetch all transactions for the year
        $startDate = "{$year}-01-01";
        $endDate = "{$year}-12-31";

        $sql = "SELECT 
                    DATE_FORMAT(transaction_date, '%Y-%m') AS month_key,
                    transaction_type,
                    category,
                    SUM(amount) AS total
                FROM finance_records
                WHERE transaction_date >= ? AND transaction_date <= ?";
        
        $params = [$startDate, $endDate];
        $types = "ss";

        if ($churchId) {
            $sql .= " AND church_id = ?";
            $params[] = (int)$churchId;
            $types .= "i";
        }

        $sql .= " GROUP BY month_key, transaction_type, category ORDER BY month_key ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as $row) {
            $mKey = $row['month_key'];
            if (isset($months[$mKey])) {
                $amount = (float)$row['total'];
                $cat = $row['category'] ?: 'Uncategorized';
                if ($row['transaction_type'] === 'income') {
                    $months[$mKey]['operating_inflows'] += $amount;
                    $months[$mKey]['categories_in'][$cat] = ($months[$mKey]['categories_in'][$cat] ?? 0) + $amount;
                } else {
                    $months[$mKey]['operating_outflows'] += $amount;
                    $months[$mKey]['categories_out'][$cat] = ($months[$mKey]['categories_out'][$cat] ?? 0) + $amount;
                }
            }
        }

        // Calculate running balance and net cashflow
        $runningBalance = 0.0;
        $totalInflowYear = 0.0;
        $totalOutflowYear = 0.0;

        foreach ($months as &$m) {
            $m['net_cashflow'] = $m['operating_inflows'] - $m['operating_outflows'];
            $runningBalance += $m['net_cashflow'];
            $m['closing_balance'] = $runningBalance;
            $totalInflowYear += $m['operating_inflows'];
            $totalOutflowYear += $m['operating_outflows'];
        }
        unset($m);

        return [
            'year' => $year,
            'months' => array_values($months),
            'total_inflow' => $totalInflowYear,
            'total_outflow' => $totalOutflowYear,
            'net_annual_cashflow' => $totalInflowYear - $totalOutflowYear
        ];
    }

    /**
     * Get Year-over-Year (YoY) Growth & Comparison
     */
    public function getYearOverYearComparison($churchId = null, $currentYear = null) {
        $currentYear = $currentYear ?: date('Y');
        $previousYear = $currentYear - 1;

        $currentData = $this->getCashflowStatement($churchId, $currentYear);
        $previousData = $this->getCashflowStatement($churchId, $previousYear);

        $incomeGrowth = $previousData['total_inflow'] > 0 
            ? round((($currentData['total_inflow'] - $previousData['total_inflow']) / $previousData['total_inflow']) * 100, 1) 
            : 0;

        $expenseGrowth = $previousData['total_outflow'] > 0 
            ? round((($currentData['total_outflow'] - $previousData['total_outflow']) / $previousData['total_outflow']) * 100, 1) 
            : 0;

        $netGrowth = $previousData['net_annual_cashflow'] != 0 
            ? round((($currentData['net_annual_cashflow'] - $previousData['net_annual_cashflow']) / abs($previousData['net_annual_cashflow'])) * 100, 1) 
            : 0;

        return [
            'current_year' => $currentYear,
            'previous_year' => $previousYear,
            'current' => $currentData,
            'previous' => $previousData,
            'income_growth_pct' => $incomeGrowth,
            'expense_growth_pct' => $expenseGrowth,
            'net_growth_pct' => $netGrowth
        ];
    }

    /**
     * Get financial audit trail logs from activity_logs
     */
    public function getFinancialAuditLogs($churchId = null, $limit = 50) {
        $sql = "SELECT a.*, u.first_name, u.last_name, u.role 
                FROM activity_logs a
                LEFT JOIN users u ON a.user_id = u.id
                WHERE a.action IN ('finance_created', 'finance_updated', 'finance_deleted', 'budget_created', 'budget_updated', 'budget_deleted', 'pledge_created', 'pledge_payment_recorded')
                   OR a.action LIKE '%finance%' 
                   OR a.action LIKE '%budget%' 
                   OR a.action LIKE '%pledge%'";
        
        $params = [];
        $types = "";

        if ($churchId) {
            $sql .= " AND (u.church_id = ? OR a.description LIKE ?)";
            $params[] = (int)$churchId;
            $params[] = "%church #" . $churchId . "%";
            $types .= "is";
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT ?";
        $params[] = (int)$limit;
        $types .= "i";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}


