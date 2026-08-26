<?php
namespace App\Controllers;

use App\Models\FinanceRecord;
use App\Models\Attendance;
use App\Models\Church;
use App\Utilities\Security;

/**
 * AdminReportController
 *
 * Provides two admin-only reporting pages and their corresponding AJAX data endpoints:
 *  - /admin/finance-report          → financeReport()       (page)
 *  - /admin/finance-report/data     → financeReportData()   (AJAX JSON)
 *  - /admin/attendance-overview     → attendanceOverview()  (page)
 *  - /admin/attendance-overview/data→ attendanceOverviewData() (AJAX JSON)
 *
 * Access is restricted to users with the 'manage_users' (admin) permission.
 */
class AdminReportController extends BaseController
{
    private FinanceRecord $financeModel;
    private Attendance    $attendanceModel;
    private Church        $churchModel;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('manage_users');

        $this->financeModel    = new FinanceRecord();
        $this->attendanceModel = new Attendance();
        $this->churchModel     = new Church();
    }

    // -------------------------------------------------------------------------
    // Finance Report Page
    // -------------------------------------------------------------------------

    /**
     * Render the admin financial report page.
     * Initial data is loaded server-side; subsequent filter changes use AJAX.
     */
    public function financeReport(): void
    {
        $churches  = $this->churchModel->getChurches([]);
        $csrfToken = Security::generateCSRFToken();

        // Default date range: current year
        $defaultStart = date('Y-01-01');
        $defaultEnd   = date('Y-m-d');

        $this->render('admin/finance_report', [
            'title'      => 'Global Financial Report',
            'pageTitle'  => 'Global Financial Report',
            'churches'   => $churches,
            'csrf_token' => $csrfToken,
            'defaultStart' => $defaultStart,
            'defaultEnd'   => $defaultEnd,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/'],
                ['label' => 'Admin Reports'],
                ['label' => 'Financial Report', 'active' => true],
            ],
        ]);
    }

    /**
     * AJAX endpoint – return JSON finance data based on filters.
     *
     * Query params:
     *   start_date   (Y-m-d)
     *   end_date     (Y-m-d)
     *   church_ids[] (int[]) – empty = all churches
     */
    public function financeReportData(): void
    {
        $startDate  = $this->request->get('start_date') ?: date('Y-01-01');
        $endDate    = $this->request->get('end_date')   ?: date('Y-m-d');
        $churchIds  = $this->request->get('church_ids', []);
        if (!is_array($churchIds)) {
            $churchIds = array_filter(explode(',', $churchIds));
        }
        $churchIds = array_map('intval', array_filter($churchIds));

        // --- KPI Summary (all or filtered churches) ---
        $summary = $this->buildFinanceSummary($startDate, $endDate, $churchIds);

        // --- Monthly trend chart (last 12 months within range) ---
        $monthlyChart = $this->buildMonthlyTrend($startDate, $endDate, $churchIds);

        // --- Church-by-church comparison ---
        $churchComparison = $this->buildChurchComparison($startDate, $endDate, $churchIds);

        // --- Category breakdown (income + expense) ---
        $incomeCategories  = $this->buildCategoryBreakdown($startDate, $endDate, $churchIds, 'income');
        $expenseCategories = $this->buildCategoryBreakdown($startDate, $endDate, $churchIds, 'expense');

        // --- Recent transactions (latest 30) ---
        $recentTransactions = $this->buildRecentTransactions($startDate, $endDate, $churchIds);

        $this->json([
            'success' => true,
            'summary' => $summary,
            'monthly_chart' => $monthlyChart,
            'church_comparison' => $churchComparison,
            'income_categories' => $incomeCategories,
            'expense_categories' => $expenseCategories,
            'recent_transactions' => $recentTransactions,
        ]);
    }

    // -------------------------------------------------------------------------
    // Attendance Overview Page
    // -------------------------------------------------------------------------

    /**
     * Render the admin attendance overview page.
     */
    public function attendanceOverview(): void
    {
        $churches  = $this->churchModel->getChurches([]);
        $csrfToken = Security::generateCSRFToken();

        $defaultStart = date('Y-01-01');
        $defaultEnd   = date('Y-m-d');

        $this->render('admin/attendance_overview', [
            'title'        => 'Global Attendance Overview',
            'pageTitle'    => 'Global Attendance Overview',
            'churches'     => $churches,
            'csrf_token'   => $csrfToken,
            'defaultStart' => $defaultStart,
            'defaultEnd'   => $defaultEnd,
            'breadcrumbs'  => [
                ['label' => 'Dashboard', 'url' => '/'],
                ['label' => 'Admin Reports'],
                ['label' => 'Attendance Overview', 'active' => true],
            ],
        ]);
    }

    /**
     * AJAX endpoint – return JSON attendance data.
     *
     * Query params:
     *   period       weekly|monthly|yearly
     *   start_date   (Y-m-d)
     *   end_date     (Y-m-d)
     *   church_ids[] (int[])
     */
    public function attendanceOverviewData(): void
    {
        $period    = $this->request->get('period', 'monthly');
        if (!in_array($period, ['weekly', 'monthly', 'yearly'], true)) {
            $period = 'monthly';
        }
        $startDate = $this->request->get('start_date') ?: date('Y-01-01');
        $endDate   = $this->request->get('end_date')   ?: date('Y-m-d');
        $churchIds = $this->request->get('church_ids', []);
        if (!is_array($churchIds)) {
            $churchIds = array_filter(explode(',', $churchIds));
        }
        $churchIds = array_map('intval', array_filter($churchIds));

        // --- KPI Summary ---
        $summary = $this->buildAttendanceSummary($startDate, $endDate, $churchIds);

        // --- Trend chart (period-based) ---
        $trendChart = $this->buildAttendanceTrend($period, $startDate, $endDate, $churchIds);

        // --- Church attendance comparison ---
        $churchComparison = $this->buildChurchAttendanceComparison($startDate, $endDate, $churchIds);

        // --- Service type breakdown ---
        $serviceBreakdown = $this->buildServiceTypeBreakdown($startDate, $endDate, $churchIds);

        $this->json([
            'success'          => true,
            'summary'          => $summary,
            'trend_chart'      => $trendChart,
            'church_comparison'=> $churchComparison,
            'service_breakdown'=> $serviceBreakdown,
        ]);
    }

    // -------------------------------------------------------------------------
    // Finance Helpers
    // -------------------------------------------------------------------------

    private function buildFinanceSummary(string $start, string $end, array $churchIds): array
    {
        $db = \App\Core\Database::getInstance();

        $where  = ['fr.transaction_date >= ?', 'fr.transaction_date <= ?'];
        $params = [$start, $end];
        $types  = 'ss';

        if (!empty($churchIds)) {
            $placeholders = implode(',', array_fill(0, count($churchIds), '?'));
            $where[]  = "fr.church_id IN ({$placeholders})";
            $params   = array_merge($params, $churchIds);
            $types   .= str_repeat('i', count($churchIds));
        }

        $sql = "SELECT
                    SUM(CASE WHEN transaction_type='income'  THEN amount ELSE 0 END) AS total_income,
                    SUM(CASE WHEN transaction_type='expense' THEN amount ELSE 0 END) AS total_expense,
                    COUNT(*) AS transaction_count,
                    COUNT(DISTINCT fr.church_id) AS church_count
                FROM finance_records fr
                WHERE " . implode(' AND ', $where);

        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        $totalIncome  = (float)($row['total_income']  ?? 0);
        $totalExpense = (float)($row['total_expense'] ?? 0);

        return [
            'total_income'       => $totalIncome,
            'total_expense'      => $totalExpense,
            'net_balance'        => $totalIncome - $totalExpense,
            'transaction_count'  => (int)($row['transaction_count'] ?? 0),
            'church_count'       => (int)($row['church_count'] ?? 0),
        ];
    }

    private function buildMonthlyTrend(string $start, string $end, array $churchIds): array
    {
        $db = \App\Core\Database::getInstance();

        $where  = ['transaction_date >= ?', 'transaction_date <= ?'];
        $params = [$start, $end];
        $types  = 'ss';

        if (!empty($churchIds)) {
            $placeholders = implode(',', array_fill(0, count($churchIds), '?'));
            $where[]  = "church_id IN ({$placeholders})";
            $params   = array_merge($params, $churchIds);
            $types   .= str_repeat('i', count($churchIds));
        }

        $sql = "SELECT
                    DATE_FORMAT(transaction_date,'%Y-%m') AS month_key,
                    SUM(CASE WHEN transaction_type='income'  THEN amount ELSE 0 END) AS income,
                    SUM(CASE WHEN transaction_type='expense' THEN amount ELSE 0 END) AS expense
                FROM finance_records
                WHERE " . implode(' AND ', $where) . "
                GROUP BY month_key
                ORDER BY month_key ASC";

        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $labels  = [];
        $income  = [];
        $expense = [];

        foreach ($rows as $row) {
            $labels[]  = date('M Y', strtotime($row['month_key'] . '-01'));
            $income[]  = (float)$row['income'];
            $expense[] = (float)$row['expense'];
        }

        return ['labels' => $labels, 'income' => $income, 'expense' => $expense];
    }

    private function buildChurchComparison(string $start, string $end, array $churchIds): array
    {
        $db = \App\Core\Database::getInstance();

        $where  = ['fr.transaction_date >= ?', 'fr.transaction_date <= ?'];
        $params = [$start, $end];
        $types  = 'ss';

        if (!empty($churchIds)) {
            $placeholders = implode(',', array_fill(0, count($churchIds), '?'));
            $where[]  = "fr.church_id IN ({$placeholders})";
            $params   = array_merge($params, $churchIds);
            $types   .= str_repeat('i', count($churchIds));
        }

        $sql = "SELECT
                    fr.church_id,
                    c.name AS church_name,
                    SUM(CASE WHEN transaction_type='income'  THEN amount ELSE 0 END) AS total_income,
                    SUM(CASE WHEN transaction_type='expense' THEN amount ELSE 0 END) AS total_expense,
                    COUNT(*) AS transaction_count
                FROM finance_records fr
                LEFT JOIN churches c ON c.id = fr.church_id
                WHERE " . implode(' AND ', $where) . "
                GROUP BY fr.church_id, c.name
                ORDER BY c.name ASC";

        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $labels  = [];
        $income  = [];
        $expense = [];
        $table   = [];

        foreach ($rows as $row) {
            $labels[]  = $row['church_name'] ?? 'Church #' . $row['church_id'];
            $income[]  = (float)$row['total_income'];
            $expense[] = (float)$row['total_expense'];
            $table[]   = [
                'church_name'       => $row['church_name'] ?? 'Church #' . $row['church_id'],
                'total_income'      => (float)$row['total_income'],
                'total_expense'     => (float)$row['total_expense'],
                'net_balance'       => (float)$row['total_income'] - (float)$row['total_expense'],
                'transaction_count' => (int)$row['transaction_count'],
            ];
        }

        return ['labels' => $labels, 'income' => $income, 'expense' => $expense, 'table' => $table];
    }

    private function buildCategoryBreakdown(string $start, string $end, array $churchIds, string $type): array
    {
        $db = \App\Core\Database::getInstance();

        $where  = ['transaction_type = ?', 'transaction_date >= ?', 'transaction_date <= ?'];
        $params = [$type, $start, $end];
        $types  = 'sss';

        if (!empty($churchIds)) {
            $placeholders = implode(',', array_fill(0, count($churchIds), '?'));
            $where[]  = "church_id IN ({$placeholders})";
            $params   = array_merge($params, $churchIds);
            $types   .= str_repeat('i', count($churchIds));
        }

        $sql = "SELECT COALESCE(NULLIF(category,''), 'Other') AS cat, SUM(amount) AS total
                FROM finance_records
                WHERE " . implode(' AND ', $where) . "
                GROUP BY cat
                ORDER BY total DESC
                LIMIT 10";

        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $labels = [];
        $values = [];
        foreach ($rows as $row) {
            $labels[] = ucfirst($row['cat']);
            $values[] = (float)$row['total'];
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function buildRecentTransactions(string $start, string $end, array $churchIds): array
    {
        $db = \App\Core\Database::getInstance();

        $where  = ['fr.transaction_date >= ?', 'fr.transaction_date <= ?'];
        $params = [$start, $end];
        $types  = 'ss';

        if (!empty($churchIds)) {
            $placeholders = implode(',', array_fill(0, count($churchIds), '?'));
            $where[]  = "fr.church_id IN ({$placeholders})";
            $params   = array_merge($params, $churchIds);
            $types   .= str_repeat('i', count($churchIds));
        }

        $sql = "SELECT
                    fr.id, fr.transaction_date, fr.transaction_type, fr.description,
                    fr.amount, fr.category, c.name AS church_name
                FROM finance_records fr
                LEFT JOIN churches c ON c.id = fr.church_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY fr.transaction_date DESC, fr.created_at DESC
                LIMIT 30";

        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // -------------------------------------------------------------------------
    // Attendance Helpers
    // -------------------------------------------------------------------------

    private function buildAttendanceSummary(string $start, string $end, array $churchIds): array
    {
        $db = \App\Core\Database::getInstance();

        $where  = ['a.event_date >= ?', 'a.event_date <= ?'];
        $params = [$start, $end];
        $types  = 'ss';

        if (!empty($churchIds)) {
            $placeholders = implode(',', array_fill(0, count($churchIds), '?'));
            $where[]  = "a.church_id IN ({$placeholders})";
            $params   = array_merge($params, $churchIds);
            $types   .= str_repeat('i', count($churchIds));
        }

        $sql = "SELECT
                    SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) AS total_present,
                    SUM(CASE WHEN status='absent'  THEN 1 ELSE 0 END) AS total_absent,
                    SUM(CASE WHEN status='present' AND COALESCE(is_first_timer,0)=1 THEN 1 ELSE 0 END) AS first_timers,
                    COUNT(DISTINCT CONCAT(a.church_id,'-',a.event_date,'-',a.event_type)) AS total_services
                FROM attendance a
                WHERE " . implode(' AND ', $where);

        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        $present = (int)($row['total_present'] ?? 0);
        $absent  = (int)($row['total_absent']  ?? 0);
        $total   = $present + $absent;

        return [
            'total_present'   => $present,
            'total_absent'    => $absent,
            'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            'first_timers'    => (int)($row['first_timers']    ?? 0),
            'total_services'  => (int)($row['total_services']  ?? 0),
        ];
    }

    private function buildAttendanceTrend(string $period, string $start, string $end, array $churchIds): array
    {
        // Reuse existing Attendance model method
        $unitIds = [];
        if (!empty($churchIds)) {
            foreach ($churchIds as $cid) {
                $unitIds = array_merge($unitIds, $this->churchModel->getChurchUnitIds($cid));
            }
        }

        // The model's getChartDataByPeriod handles all/filtered scoping
        $data = $this->attendanceModel->getChartDataByPeriod($period, $unitIds, null);

        $labels      = [];
        $present     = [];
        $absent      = [];
        $firstTimers = [];

        foreach ($data as $row) {
            $labels[]      = $row['label'];
            $present[]     = $row['present'];
            $absent[]      = $row['absent'];
            $firstTimers[] = $row['first_timer'];
        }

        return [
            'labels'       => $labels,
            'present'      => $present,
            'absent'       => $absent,
            'first_timers' => $firstTimers,
        ];
    }

    private function buildChurchAttendanceComparison(string $start, string $end, array $churchIds): array
    {
        $db = \App\Core\Database::getInstance();

        $where  = ['a.event_date >= ?', 'a.event_date <= ?'];
        $params = [$start, $end];
        $types  = 'ss';

        if (!empty($churchIds)) {
            $placeholders = implode(',', array_fill(0, count($churchIds), '?'));
            $where[]  = "a.church_id IN ({$placeholders})";
            $params   = array_merge($params, $churchIds);
            $types   .= str_repeat('i', count($churchIds));
        }

        $sql = "SELECT
                    c.id AS church_id,
                    c.name AS church_name,
                    SUM(CASE WHEN a.status='present' THEN 1 ELSE 0 END) AS total_present,
                    SUM(CASE WHEN a.status='absent'  THEN 1 ELSE 0 END) AS total_absent,
                    SUM(CASE WHEN a.status='present' AND COALESCE(a.is_first_timer,0)=1 THEN 1 ELSE 0 END) AS first_timers
                FROM attendance a
                INNER JOIN churches c ON c.id = a.church_id
                WHERE " . implode(' AND ', $where) . "
                GROUP BY c.id, c.name
                ORDER BY total_present DESC";

        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $labels  = [];
        $present = [];
        $absent  = [];
        $table   = [];

        foreach ($rows as $row) {
            $totalRow = (int)$row['total_present'] + (int)$row['total_absent'];
            $rate     = $totalRow > 0 ? round(((int)$row['total_present'] / $totalRow) * 100, 1) : 0;

            $labels[]  = $row['church_name'];
            $present[] = (int)$row['total_present'];
            $absent[]  = (int)$row['total_absent'];
            $table[]   = [
                'church_name'    => $row['church_name'],
                'total_present'  => (int)$row['total_present'],
                'total_absent'   => (int)$row['total_absent'],
                'first_timers'   => (int)$row['first_timers'],
                'attendance_rate' => $rate,
            ];
        }

        return ['labels' => $labels, 'present' => $present, 'absent' => $absent, 'table' => $table];
    }

    private function buildServiceTypeBreakdown(string $start, string $end, array $churchIds): array
    {
        $db = \App\Core\Database::getInstance();

        $where  = ['a.event_date >= ?', 'a.event_date <= ?'];
        $params = [$start, $end];
        $types  = 'ss';

        if (!empty($churchIds)) {
            $placeholders = implode(',', array_fill(0, count($churchIds), '?'));
            $where[]  = "a.church_id IN ({$placeholders})";
            $params   = array_merge($params, $churchIds);
            $types   .= str_repeat('i', count($churchIds));
        }

        $sql = "SELECT
                    a.event_type,
                    SUM(CASE WHEN a.status='present' THEN 1 ELSE 0 END) AS total_present,
                    SUM(CASE WHEN a.status='absent'  THEN 1 ELSE 0 END) AS total_absent,
                    COUNT(DISTINCT CONCAT(a.church_id,'-',COALESCE(a.unit_id,0),'-',a.event_date)) AS sessions
                FROM attendance a
                WHERE " . implode(' AND ', $where) . "
                GROUP BY a.event_type
                ORDER BY total_present DESC";

        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $eventLabels = Attendance::getEventTypes();
        $result      = [];

        foreach ($rows as $row) {
            $present  = (int)$row['total_present'];
            $absent   = (int)$row['total_absent'];
            $total    = $present + $absent;
            $result[] = [
                'event_type'     => $row['event_type'],
                'label'          => $eventLabels[$row['event_type']] ?? ucfirst(str_replace('_', ' ', $row['event_type'])),
                'total_present'  => $present,
                'total_absent'   => $absent,
                'sessions'       => (int)$row['sessions'],
                'attendance_rate'=> $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ];
        }

        return $result;
    }
}
