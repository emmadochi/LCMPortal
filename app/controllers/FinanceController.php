<?php
namespace App\Controllers;

use App\Models\FinanceRecord;
use App\Models\Unit;
use App\Models\Church;
use App\Models\User;
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\ExportHelper;

class FinanceController extends BaseController {
    private $financeModel;
    private $unitModel;
    private $churchModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->financeModel = new FinanceRecord();
        $this->unitModel = new Unit();
        $this->churchModel = new Church();
        $this->userModel = new User();
        
        // Check permission
        $this->authorize('manage_finance');
    }

    /**
     * List finance records with optional church and period filter. Supports charts and month/range selector.
     */
    public function index() {
        // Determine effective church scope based on role.
        $userId = (int) $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        $requestedChurchId = (int) $this->request->get('church_id', 0);

        $churchId = 0;
        if ($userRole === 'head_pastor') {
            // Head pastor is always scoped to their own church.
            $pastorChurch = $this->churchModel->getChurchByHeadPastor($userId);
            if ($pastorChurch) {
                $churchId = (int) $pastorChurch['id'];
            }
        } elseif ($userRole === 'admin') {
            // Admin can view global or a specific church via query param.
            $churchId = $requestedChurchId;
        } else {
            // Other roles: respect explicit query param if provided.
            $churchId = $requestedChurchId;
        }

        // In the new model, every record belongs to a church (church_id), and unit_id is optional.
        $churchFilter = null;
        $records = [];
        $summary = [];
        $chartMonthly = [];
        $chartIncomeByCategory = [];
        $chartExpenseByCategory = [];
        $churchSummaries = [];
        $chartChurches = [];

        if ($churchId) {
            $church = $this->churchModel->find($churchId);
            if ($church) {
                $churchFilter = ['id' => $churchId, 'name' => $church['name']];
            }
        }

        $periodType = $this->request->get('period', 'range');
        $startDate = null;
        $endDate = null;
        $period = $this->computePeriodDates($periodType);
        $startDate = $period['start_date'];
        $endDate = $period['end_date'];

        if ($churchId && $churchFilter) {
            // Church-scoped view: use church_id as primary filter; unit_id may be NULL (church-wide) or specific units.
            $conditions = ['church_id' => $churchId];
            $records = $this->financeModel->getFinanceWithDetails($conditions, 'transaction_date DESC', $startDate, $endDate);
            $summary = $this->financeModel->getSummaryByChurch($churchId, $startDate, $endDate);
            $chartMonthly = $this->financeModel->getMonthlyTotalsByChurch($churchId, $startDate, $endDate);
            $chartIncomeByCategory = $this->financeModel->getCategoryBreakdownByChurch($churchId, $startDate, $endDate, 'income');
            $chartExpenseByCategory = $this->financeModel->getCategoryBreakdownByChurch($churchId, $startDate, $endDate, 'expense');
        } else {
            // Global view (all churches)
            $records = $this->financeModel->getFinanceWithDetails([], 'transaction_date DESC', $startDate, $endDate);
            $summary = $this->financeModel->getSummary(null, $startDate, $endDate);
            $chartMonthly = $this->financeModel->getMonthlyTotals(null, $startDate, $endDate);
            $chartIncomeByCategory = $this->financeModel->getCategoryBreakdown(null, $startDate, $endDate, 'income');
            $chartExpenseByCategory = $this->financeModel->getCategoryBreakdown(null, $startDate, $endDate, 'expense');
            // Per-church summaries for global admin table and comparison chart
            $churchSummaries = $this->financeModel->getSummaryByChurches($startDate, $endDate);
            foreach ($churchSummaries as $row) {
                $income = (float)($row['total_income'] ?? 0);
                $expense = (float)($row['total_expense'] ?? 0);
                $net = $income - $expense;
                $label = $row['church_name'] ?? 'Unknown';
                $chartChurches[] = [
                    'label' => $label,
                    'income' => $income,
                    'expense' => $expense,
                    'net' => $net,
                ];
            }
        }

        $this->render('finance/index', [
            'title' => 'Finance',
            'pageTitle' => $churchFilter ? 'Finance — ' . $churchFilter['name'] : 'Finance',
            'records' => $records,
            'summary' => $summary,
            'chartMonthly' => $chartMonthly,
            'chartIncomeByCategory' => $chartIncomeByCategory,
            'chartExpenseByCategory' => $chartExpenseByCategory,
            'period' => $period,
            'churchFilter' => $churchFilter,
            'churchSummaries' => $churchSummaries,
            'chartChurches' => $chartChurches
        ]);
    }

    /**
     * Compute start_date and end_date from request period params. Returns period array with form values for the view.
     */
    private function computePeriodDates($periodType) {
        $now = new \DateTime();
        $period = [
            'period_type' => $periodType,
            'month' => (int) $this->request->get('month', $now->format('n')),
            'year' => (int) $this->request->get('year', $now->format('Y')),
            'from_month' => (int) $this->request->get('from_month', 1),
            'from_year' => (int) $this->request->get('from_year', $now->format('Y')),
            'to_month' => (int) $this->request->get('to_month', $now->format('n')),
            'to_year' => (int) $this->request->get('to_year', $now->format('Y')),
            'start_date' => null,
            'end_date' => null,
        ];

        if ($periodType === 'month') {
            $m = max(1, min(12, $period['month']));
            $y = max(2000, min(2100, $period['year']));
            $period['month'] = $m;
            $period['year'] = $y;
            $period['start_date'] = sprintf('%04d-%02d-01', $y, $m);
            $period['end_date'] = date('Y-m-t', strtotime($period['start_date']));
        } else {
            $fm = max(1, min(12, $period['from_month']));
            $fy = max(2000, min(2100, $period['from_year']));
            $tm = max(1, min(12, $period['to_month']));
            $ty = max(2000, min(2100, $period['to_year']));
            $period['from_month'] = $fm;
            $period['from_year'] = $fy;
            $period['to_month'] = $tm;
            $period['to_year'] = $ty;
            $period['start_date'] = sprintf('%04d-%02d-01', $fy, $fm);
            $endLast = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $ty, $tm)));
            $period['end_date'] = $endLast;
        }

        return $period;
    }

    /**
     * Show create form
     */
    public function create() {
        // Resolve church context similarly to index(): head pastors are locked to their church.
        $userId = (int) $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        $requestedChurchId = (int) $this->request->get('church_id', 0);

        $churchId = 0;
        if ($userRole === 'head_pastor') {
            $pastorChurch = $this->churchModel->getChurchByHeadPastor($userId);
            if ($pastorChurch) {
                $churchId = (int) $pastorChurch['id'];
            }
        } elseif ($userRole === 'admin') {
            $churchId = $requestedChurchId;
        } else {
            $churchId = $requestedChurchId;
        }

        $csrfToken = Security::generateCSRFToken();
        $units = $this->unitModel->getActiveUnits();
        $transactionTypes = ['income', 'expense'];
        $categories = ['offering', 'tithe', 'donation', 'event', 'equipment', 'maintenance', 'salary', 'utility', 'other'];
        $paymentMethods = ['cash', 'check', 'bank_transfer', 'mobile_money', 'card', 'other'];
        // All active users can be potential givers; for now we reuse all users ordered by name.
        $members = $this->userModel->getAllUsers();
        
        $this->render('finance/create', [
            'title' => 'Create Finance Record',
            'pageTitle' => 'Create Finance Record',
            'csrf_token' => $csrfToken,
            'units' => $units,
            'transactionTypes' => $transactionTypes,
            'categories' => $categories,
            'paymentMethods' => $paymentMethods,
            'churchId' => $churchId,
            'members' => $members,
            'breadcrumbs' => [
                [
                    'label' => 'Finance',
                    'url' => $churchId ? '/finance?church_id=' . $churchId : '/finance'
                ],
                ['label' => 'Create', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new finance record
     */
    public function store() {
        // Resolve church context from role + request. Head pastors are always scoped to their own church.
        $userId = (int) $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        $postedChurchId = (int) $this->request->post('church_id', 0);

        $churchId = 0;
        if ($userRole === 'head_pastor') {
            $pastorChurch = $this->churchModel->getChurchByHeadPastor($userId);
            if ($pastorChurch) {
                $churchId = (int) $pastorChurch['id'];
            }
        } elseif ($userRole === 'admin') {
            $churchId = $postedChurchId;
        } else {
            $churchId = $postedChurchId;
        }

        $redirectQuery = $churchId ? '?church_id=' . $churchId : '';

        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/finance/create' . $redirectQuery);
        }

        $validation = $this->validate([
            // unit_id is now optional; when present must be numeric
            'unit_id' => 'numeric',
            'member_id' => 'numeric',
            'transaction_type' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required',
            'transaction_date' => 'required|date'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/finance/create' . $redirectQuery);
        }

        $unitId = (int) $this->request->post('unit_id', 0);
        if ($unitId <= 0) {
            $unitId = null;
        }

        $memberId = (int) $this->request->post('member_id', 0);
        if ($memberId <= 0) {
            $memberId = null;
        }

        $currentUserId = (int) $this->session->get('user_id');

        $data = [
            'church_id' => $churchId ?: null,
            'unit_id' => $unitId,
            'member_id' => $memberId,
            // Store both user_id (if column exists) and recorded_by for compatibility with existing queries
            'user_id' => $currentUserId,
            'recorded_by' => $currentUserId,
            'transaction_type' => $this->request->post('transaction_type'),
            'amount' => (float)$this->request->post('amount'),
            'category' => $this->request->post('category'),
            'description' => $this->request->post('description', ''),
            'transaction_date' => $this->request->post('transaction_date'),
            'payment_method' => $this->request->post('payment_method', 'cash'),
            'reference_number' => $this->request->post('reference_number', '')
        ];

        $id = $this->financeModel->create($data);
        
        if ($id) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'create',
                'FinanceRecord',
                $id,
                "Created {$data['transaction_type']} record: {$data['amount']} ({$data['category']})"
            );
            
            $this->session->setFlash('success', 'Finance record created successfully.');
            $this->redirect('/finance' . $redirectQuery);
        } else {
            $this->session->setFlash('error', 'Failed to create finance record.');
            $this->redirect('/finance/create' . $redirectQuery);
        }
    }

    /**
     * Show single finance record
     */
    public function show($id) {
        $record = $this->financeModel->find($id);
        
        if (!$record) {
            $this->session->setFlash('error', 'Finance record not found.');
            $this->redirect('/finance');
        }
        
        $this->render('finance/show', [
            'title' => 'Finance Record',
            'pageTitle' => 'Finance Record',
            'record' => $record
        ]);
    }

    /**
     * Export finance records for the current period and scope.
     * Supports: csv, excel, json, pdf
     */
    public function export() {
        // Reuse period + church scoping logic from index()
        $userId = (int) $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        $requestedChurchId = (int) $this->request->get('church_id', 0);

        $churchId = 0;
        if ($userRole === 'head_pastor') {
            $pastorChurch = $this->churchModel->getChurchByHeadPastor($userId);
            if ($pastorChurch) {
                $churchId = (int) $pastorChurch['id'];
            }
        } elseif ($userRole === 'admin') {
            $churchId = $requestedChurchId;
        } else {
            $churchId = $requestedChurchId;
        }

        $periodType = $this->request->get('period', 'range');
        $period = $this->computePeriodDates($periodType);
        $startDate = $period['start_date'];
        $endDate = $period['end_date'];

        // Fetch detailed records (not just summaries)
        if ($churchId) {
            $conditions = ['church_id' => $churchId];
            $records = $this->financeModel->getFinanceWithDetails($conditions, 'transaction_date DESC', $startDate, $endDate);
        } else {
            $records = $this->financeModel->getFinanceWithDetails([], 'transaction_date DESC', $startDate, $endDate);
        }

        $data = [];
        foreach ($records as $row) {
            $data[] = [
                'date' => $row['transaction_date'],
                'type' => $row['transaction_type'],
                'amount' => $row['amount'],
                'category' => $row['category'],
                'unit' => $row['unit_name'] ?? '',
                'church' => $row['church_name'] ?? '',
                'description' => $row['description'] ?? '',
            ];
        }

        $headers = ['Date', 'Type', 'Amount', 'Category', 'Unit', 'Church', 'Description'];
        $format = strtolower($this->request->get('format', 'csv'));
        $suffix = $churchId ? '_church_' . $churchId : '_all';
        $baseName = 'finance' . $suffix . '_' . date('Y-m-d_His');

        switch ($format) {
            case 'json':
                ExportHelper::exportJSON($data, $baseName . '.json');
                break;
            case 'pdf':
                // Build summary and "charts" sections for inclusion in the PDF
                $summary = [];
                $chartMonthly = [];
                $chartIncomeByCategory = [];
                $chartExpenseByCategory = [];

                if ($churchId) {
                    $summary = $this->financeModel->getSummaryByChurch($churchId, $startDate, $endDate);
                    $chartMonthly = $this->financeModel->getMonthlyTotalsByChurch($churchId, $startDate, $endDate);
                    $chartIncomeByCategory = $this->financeModel->getCategoryBreakdownByChurch($churchId, $startDate, $endDate, 'income');
                    $chartExpenseByCategory = $this->financeModel->getCategoryBreakdownByChurch($churchId, $startDate, $endDate, 'expense');
                } else {
                    $summary = $this->financeModel->getSummary(null, $startDate, $endDate);
                    $chartMonthly = $this->financeModel->getMonthlyTotals(null, $startDate, $endDate);
                    $chartIncomeByCategory = $this->financeModel->getCategoryBreakdown(null, $startDate, $endDate, 'income');
                    $chartExpenseByCategory = $this->financeModel->getCategoryBreakdown(null, $startDate, $endDate, 'expense');
                }

                $totalIncome = 0.0;
                $totalExpense = 0.0;
                foreach ($summary as $item) {
                    if (($item['transaction_type'] ?? '') === 'income') {
                        $totalIncome = (float) ($item['total'] ?? 0);
                    } elseif (($item['transaction_type'] ?? '') === 'expense') {
                        $totalExpense = (float) ($item['total'] ?? 0);
                    }
                }
                $netTotal = $totalIncome - $totalExpense;

                $extraHtml = '';

                // Summary section
                $extraHtml .= '<h2 class="section-title">Summary</h2>';
                $extraHtml .= '<table class="summary-table">
    <thead>
        <tr>
            <th>Metric</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Income</td>
            <td>$' . number_format($totalIncome, 2) . '</td>
        </tr>
        <tr>
            <td>Total Expense</td>
            <td>$' . number_format($totalExpense, 2) . '</td>
        </tr>
        <tr>
            <td>Net Total</td>
            <td>$' . number_format($netTotal, 2) . '</td>
        </tr>
    </tbody>
</table>';

                // Monthly chart data as table
                if (!empty($chartMonthly)) {
                    $extraHtml .= '<h2 class="section-title">Income vs Expense by Month</h2>';
                    $extraHtml .= '<table>
    <thead>
        <tr>
            <th>Month</th>
            <th>Income</th>
            <th>Expense</th>
        </tr>
    </thead>
    <tbody>';
                    foreach ($chartMonthly as $row) {
                        $label = $row['label'] ?? '';
                        $income = (float) ($row['income'] ?? 0);
                        $expense = (float) ($row['expense'] ?? 0);
                        $extraHtml .= '<tr>
            <td>' . htmlspecialchars((string) $label) . '</td>
            <td>$' . number_format($income, 2) . '</td>
            <td>$' . number_format($expense, 2) . '</td>
        </tr>';
                    }
                    $extraHtml .= '
    </tbody>
</table>';
                }

                // Income by category
                if (!empty($chartIncomeByCategory)) {
                    $extraHtml .= '<h2 class="section-title">Income by Category</h2>';
                    $extraHtml .= '<table>
    <thead>
        <tr>
            <th>Category</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>';
                    foreach ($chartIncomeByCategory as $row) {
                        $category = $row['category'] ?? 'Other';
                        $total = (float) ($row['total'] ?? 0);
                        $extraHtml .= '<tr>
            <td>' . htmlspecialchars((string) $category) . '</td>
            <td>$' . number_format($total, 2) . '</td>
        </tr>';
                    }
                    $extraHtml .= '
    </tbody>
</table>';
                }

                // Expense by category
                if (!empty($chartExpenseByCategory)) {
                    $extraHtml .= '<h2 class="section-title">Expense by Category</h2>';
                    $extraHtml .= '<table>
    <thead>
        <tr>
            <th>Category</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>';
                    foreach ($chartExpenseByCategory as $row) {
                        $category = $row['category'] ?? 'Other';
                        $total = (float) ($row['total'] ?? 0);
                        $extraHtml .= '<tr>
            <td>' . htmlspecialchars((string) $category) . '</td>
            <td>$' . number_format($total, 2) . '</td>
        </tr>';
                    }
                    $extraHtml .= '
    </tbody>
</table>';
                }

                ExportHelper::exportPDF($data, $headers, 'Finance Export', $baseName . '.pdf', $extraHtml);
                break;
            case 'excel':
            case 'xls':
            case 'xlsx':
                ExportHelper::exportExcel($data, $headers, $baseName . '.xls');
                break;
            case 'csv':
            default:
                ExportHelper::exportCSV($data, $headers, $baseName . '.csv');
                break;
        }
    }
}

