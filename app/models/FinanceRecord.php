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
    public function getFinanceWithDetailsByUnitIds(array $unitIds, $orderBy = null, $startDate = null, $endDate = null) {
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
}

