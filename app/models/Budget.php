<?php
namespace App\Models;

class Budget extends BaseModel {
    protected $table = 'budgets';
    protected $fillable = [
        'church_id', 'unit_id', 'title', 'fiscal_year', 'period_type', 
        'start_date', 'end_date', 'total_budget_amount', 'category', 
        'description', 'status', 'created_by'
    ];

    /**
     * Get budgets with actual expenditure computed from finance_records
     */
    public function getBudgetsWithActuals($churchId = null, $fiscalYear = null, $unitId = null, $status = null) {
        $sql = "SELECT b.*, 
                       c.name AS church_name, 
                       u.name AS unit_name,
                       creator.first_name AS creator_first_name,
                       creator.last_name AS creator_last_name
                FROM budgets b
                LEFT JOIN churches c ON b.church_id = c.id
                LEFT JOIN units u ON b.unit_id = u.id
                LEFT JOIN users creator ON b.created_by = creator.id
                WHERE 1=1";
        
        $params = [];
        $types = "";

        if ($churchId) {
            $sql .= " AND b.church_id = ?";
            $params[] = (int)$churchId;
            $types .= "i";
        }
        if ($fiscalYear) {
            $sql .= " AND b.fiscal_year = ?";
            $params[] = (int)$fiscalYear;
            $types .= "i";
        }
        if ($unitId) {
            $sql .= " AND b.unit_id = ?";
            $params[] = (int)$unitId;
            $types .= "i";
        }
        if ($status) {
            $sql .= " AND b.status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $sql .= " ORDER BY b.fiscal_year DESC, b.created_at DESC";

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $budgets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Compute actual expenses for each budget
        foreach ($budgets as &$budget) {
            $actualSpent = $this->calculateActualSpent($budget);
            $budget['actual_spent'] = $actualSpent;
            $target = (float)$budget['total_budget_amount'];
            $budget['remaining_amount'] = max(0, $target - $actualSpent);
            $budget['variance'] = $target - $actualSpent;
            $budget['utilization_pct'] = $target > 0 ? round(($actualSpent / $target) * 100, 1) : 0;
            
            if ($budget['utilization_pct'] >= 100) {
                $budget['health_status'] = 'exceeded';
                $budget['health_badge'] = 'danger';
            } elseif ($budget['utilization_pct'] >= 80) {
                $budget['health_status'] = 'caution';
                $budget['health_badge'] = 'warning';
            } else {
                $budget['health_status'] = 'healthy';
                $budget['health_badge'] = 'success';
            }
        }
        unset($budget);

        return $budgets;
    }

    /**
     * Calculate actual spent against a budget's constraints
     */
    public function calculateActualSpent($budget) {
        $sql = "SELECT COALESCE(SUM(amount), 0) AS total_spent 
                FROM finance_records 
                WHERE transaction_type = 'expense'
                  AND church_id = ?
                  AND transaction_date >= ?
                  AND transaction_date <= ?";
        
        $params = [
            (int)$budget['church_id'],
            $budget['start_date'],
            $budget['end_date']
        ];
        $types = "iss";

        if (!empty($budget['unit_id'])) {
            $sql .= " AND unit_id = ?";
            $params[] = (int)$budget['unit_id'];
            $types .= "i";
        }

        if (!empty($budget['category'])) {
            $sql .= " AND category = ?";
            $params[] = $budget['category'];
            $types .= "s";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return (float)($res['total_spent'] ?? 0);
    }

    /**
     * Get aggregate budget summary for a church / fiscal year
     */
    public function getBudgetSummary($churchId = null, $fiscalYear = null) {
        $budgets = $this->getBudgetsWithActuals($churchId, $fiscalYear, null, 'active');
        $totalBudgeted = 0;
        $totalSpent = 0;
        $activeCount = count($budgets);
        $exceededCount = 0;
        $cautionCount = 0;

        foreach ($budgets as $b) {
            $totalBudgeted += (float)$b['total_budget_amount'];
            $totalSpent += (float)$b['actual_spent'];
            if ($b['health_status'] === 'exceeded') {
                $exceededCount++;
            } elseif ($b['health_status'] === 'caution') {
                $cautionCount++;
            }
        }

        $remaining = max(0, $totalBudgeted - $totalSpent);
        $overallUtilization = $totalBudgeted > 0 ? round(($totalSpent / $totalBudgeted) * 100, 1) : 0;

        return [
            'total_budgeted' => $totalBudgeted,
            'total_spent' => $totalSpent,
            'remaining' => $remaining,
            'utilization_pct' => $overallUtilization,
            'active_count' => $activeCount,
            'exceeded_count' => $exceededCount,
            'caution_count' => $cautionCount
        ];
    }
}
