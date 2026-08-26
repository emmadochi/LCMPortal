<?php
namespace App\Controllers;

use App\Models\FinanceRecord;
use App\Models\Church;
use App\Models\Unit;
use App\Utilities\Security;
use App\Utilities\ExportHelper;


class HeadPastorFinanceController extends BaseHeadPastorController {
    
    
    private $financeModel;
    private $churchModel;
    private $unitModel;
    
    public function __construct() {
        parent::__construct();
        $this->financeModel = new FinanceRecord();
        $this->churchModel = new Church();
        $this->unitModel = new Unit();
    }
    
    
    
    /**
     * Dashboard view for head pastor financial management
     */
    public function index() {
        // Get church units for financial filtering
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        
        // Get financial summary
        $summary = $this->financeModel->getSummaryByChurch($this->churchId);
        $incomeTotal = 0;
        $expenseTotal = 0;
        foreach ($summary as $item) {
            if ($item['transaction_type'] === 'income') {
                $incomeTotal = (float)$item['total'];
            } elseif ($item['transaction_type'] === 'expense') {
                $expenseTotal = (float)$item['total'];
            }
        }
        
        // Get recent transactions (church-wide)
        $recentTransactions = $this->financeModel->getFinanceWithDetails(['church_id' => $this->churchId], 'f.transaction_date DESC, f.created_at DESC');
        $recentTransactions = array_slice($recentTransactions, 0, 10); // Limit to 10
        
        // Get monthly trends
        $monthlyData = $this->financeModel->getMonthlyTotalsByChurch($this->churchId);
        
        // Get categories breakdowns
        $incomeCategories = $this->financeModel->getCategoryBreakdownByChurch($this->churchId, null, null, 'income');
        $expenseCategories = $this->financeModel->getCategoryBreakdownByChurch($this->churchId, null, null, 'expense');
        
        // Get unit breakdown summary
        $unitSummaries = $this->financeModel->getSummaryByUnitsInChurch($this->churchId);
        
        $this->render('head-pastor/finance/index', [
            'title' => 'Financial Management - ' . $this->church['name'],
            'pageTitle' => 'Financial Dashboard',
            'church' => $this->church,
            'incomeTotal' => $incomeTotal,
            'expenseTotal' => $expenseTotal,
            'balance' => $incomeTotal - $expenseTotal,
            'recentTransactions' => $recentTransactions,
            'monthlyData' => $monthlyData,
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'unitIds' => $unitIds,
            'unitSummaries' => $unitSummaries
        ]);
    }
    
    /**
     * List all financial records for the head pastor's church
     */
    public function records() {
        // Get filters
        $startDate = $this->request->get('start_date');
        $endDate = $this->request->get('end_date');
        $category = $this->request->get('category');
        $type = $this->request->get('type');
        $search = trim($this->request->get('search', ''));
        
        // Get church units
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        
        $conditions = ['church_id' => $this->churchId];
        if ($type && in_array($type, ['income', 'expense'])) {
            $conditions['transaction_type'] = $type;
        }
        if ($category) {
            $conditions['category'] = $category;
        }
        
        // Fetch records scoped by church
        $transactions = $this->financeModel->getFinanceWithDetails(
            $conditions,
            'f.transaction_date DESC, f.created_at DESC',
            $startDate,
            $endDate
        );
        
        // Apply search filter if present
        if ($search) {
            $transactions = array_filter($transactions, function($transaction) use ($search) {
                $searchableFields = ['description', 'category', 'first_name', 'last_name'];
                foreach ($searchableFields as $field) {
                    if (isset($transaction[$field]) && stripos($transaction[$field], $search) !== false) {
                        return true;
                    }
                }
                return false;
            });
        }
        
        // Get unique categories for filter dropdown
        $categories = [];
        foreach ($transactions as $transaction) {
            if (!empty($transaction['category'])) {
                $categories[$transaction['category']] = $transaction['category'];
            }
        }
        sort($categories);
        
        $this->render('head-pastor/finance/records', [
            'title' => 'Financial Records - ' . $this->church['name'],
            'pageTitle' => 'Financial Records',
            'church' => $this->church,
            'transactions' => array_values($transactions),
            'categories' => $categories,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'category' => $category,
                'type' => $type,
                'search' => $search
            ]
        ]);
    }
    
    /**
     * Show single financial record details
     */
    public function show($id, $transactionId) {
        $transaction = $this->financeModel->getFinanceWithDetails(['id' => $transactionId]);
        
        if (empty($transaction)) {
            $this->session->setFlash('error', 'Transaction not found.');
            $this->redirect("/churches/{$this->churchId}/finance/records");
        }
        
        $transaction = $transaction[0];
        
        // Security check: Ensure the transaction belongs to this church
        if ($transaction['church_id'] != $this->churchId) {
            $this->session->setFlash('error', 'Access denied. You do not have permission to view this record.');
            $this->redirect("/churches/{$this->churchId}/finance/records");
        }
        
        $this->render('head-pastor/finance/show', [
            'title' => 'Transaction Details - ' . $this->church['name'],
            'pageTitle' => 'Transaction Details',
            'church' => $this->church,
            'churchId' => $this->churchId,
            'transaction' => $transaction
        ]);
    }
    
    /**
     * Show create form for new financial record
     */
    public function create() {
        // Get church units for assignment
        $units = $this->churchModel->getChurchUnits($this->churchId);
        
        // Get active members for member assignment
        $userModel = new \App\Models\User();
        $members = $this->churchModel->getChurchMemberUsers($this->churchId);
        
        $csrfToken = Security::generateCSRFToken();
        
        $this->render('head-pastor/finance/create', [
            'title' => 'Record New Transaction - ' . $this->church['name'],
            'pageTitle' => 'New Financial Record',
            'church' => $this->church,
            'units' => $units,
            'members' => $members,
            'csrf_token' => $csrfToken
        ]);
    }
    
    /**
     * Store new financial record
     */
    public function store() {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/churches/{$this->churchId}/finance/create");
        }
        
        // Validate input
        $validation = $this->validate([
            'transaction_type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|max:100',
            'description' => 'required|max:500',
            'transaction_date' => 'required|date'
        ]);
        
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/churches/{$this->churchId}/finance/create");
        }
        
        $data = [
            'church_id' => $this->churchId,
            'unit_id' => $this->request->post('unit_id') ? (int)$this->request->post('unit_id') : null,
            'member_id' => $this->request->post('member_id') ? (int)$this->request->post('member_id') : null,
            'recorded_by' => $this->session->get('user_id'),
            'transaction_type' => $this->request->post('transaction_type'),
            'amount' => (float)$this->request->post('amount'),
            'category' => trim($this->request->post('category')),
            'description' => trim($this->request->post('description')),
            'transaction_date' => $this->request->post('transaction_date'),
            'payment_method' => trim($this->request->post('payment_method', '')),
            'reference_number' => trim($this->request->post('reference_number', ''))
        ];
        
        $id = $this->financeModel->create($data);
        if ($id) {
            // Log activity
            \App\Models\ActivityLog::log(
                $this->session->get('user_id'),
                'create',
                'FinanceRecord',
                $id,
                "Created financial record: {$data['transaction_type']} of {$data['amount']} for {$data['description']}"
            );
            
            $this->session->setFlash('success', 'Financial record created successfully.');
            $this->redirect("/churches/{$this->churchId}/finance/records");
        } else {
            $this->session->setFlash('error', 'Failed to create financial record.');
            $this->redirect("/churches/{$this->churchId}/finance/create");
        }
    }
    
    /**
     * Export financial records to CSV
     */
    public function export() {
        $startDate = $this->request->get('start_date');
        $endDate = $this->request->get('end_date');
        $type = $this->request->get('type');
        
        // Get church units
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        
        $conditions = [];
        if ($type && in_array($type, ['income', 'expense'])) {
            $conditions['transaction_type'] = $type;
        }
        
        $transactions = [];
        if (!empty($unitIds)) {
            $transactions = $this->financeModel->getFinanceWithDetailsByUnitIds(
                $unitIds,
                'f.transaction_date DESC',
                $startDate,
                $endDate
            );
        }
        
        // Prepare CSV data
        $headers = ['Date', 'Type', 'Category', 'Description', 'Amount', 'Unit', 'Recorded By'];
        $rows = [];
        
        foreach ($transactions as $transaction) {
            $rows[] = [
                $transaction['transaction_date'],
                ucfirst($transaction['transaction_type']),
                $transaction['category'] ?? 'N/A',
                $transaction['description'] ?? '',
                number_format($transaction['amount'], 2),
                $transaction['unit_name'] ?? 'Church-wide',
                trim(($transaction['first_name'] ?? '') . ' ' . ($transaction['last_name'] ?? ''))
            ];
        }
        
        $filename = 'financial_records_' . date('Y-m-d_H-i-s') . '.csv';
        ExportHelper::exportCSV($rows, $headers, $filename);
    }
    
    /**
     * Generate financial report
     */
    public function report() {
        $startDate = $this->request->get('start_date', date('Y-m-01'));
        $endDate = $this->request->get('end_date', date('Y-m-t'));
        
        // Get summary data
        $summary = $this->financeModel->getSummaryByChurch($this->churchId, $startDate, $endDate);
        $incomeTotal = 0;
        $expenseTotal = 0;
        foreach ($summary as $item) {
            if ($item['transaction_type'] === 'income') {
                $incomeTotal = (float)$item['total'];
            } elseif ($item['transaction_type'] === 'expense') {
                $expenseTotal = (float)$item['total'];
            }
        }
        
        // Get category breakdowns
        $incomeCategories = $this->financeModel->getCategoryBreakdownByChurch($this->churchId, $startDate, $endDate, 'income');
        $expenseCategories = $this->financeModel->getCategoryBreakdownByChurch($this->churchId, $startDate, $endDate, 'expense');
        
        // Get monthly trends for the period
        $monthlyData = $this->financeModel->getMonthlyTotalsByChurch($this->churchId, $startDate, $endDate);
        
        $this->render('head-pastor/finance/report', [
            'title' => 'Financial Report - ' . $this->church['name'],
            'pageTitle' => 'Financial Report',
            'church' => $this->church,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'incomeTotal' => $incomeTotal,
            'expenseTotal' => $expenseTotal,
            'balance' => $incomeTotal - $expenseTotal,
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'monthlyData' => $monthlyData
        ]);
    }

    /**
     * Cashflow Statement for Head Pastor
     */
    public function cashflow() {
        $year = (int)($this->request->get('year') ?: date('Y'));
        $cashflowData = $this->financeModel->getCashflowStatement($this->churchId, $year);
        $yoyData = $this->financeModel->getYearOverYearComparison($this->churchId, $year);

        $this->render('finance/cashflow', [
            'title' => 'Cashflow Statement - ' . $this->church['name'],
            'pageTitle' => 'Cashflow Statement & Year-over-Year Growth',
            'cashflow' => $cashflowData,
            'yoy' => $yoyData,
            'currentChurch' => $this->church,
            'churchId' => $this->churchId,
            'selectedYear' => $year,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => ''],
                ['label' => 'Finances', 'url' => "churches/{$this->churchId}/finance"],
                ['label' => 'Cashflow', 'active' => true]
            ]
        ]);
    }

    /**
     * Financial Audit Trail for Head Pastor
     */
    public function auditTrail() {
        $logs = $this->financeModel->getFinancialAuditLogs($this->churchId, 100);

        $this->render('finance/audit_trail', [
            'title' => 'Financial Audit Trail - ' . $this->church['name'],
            'pageTitle' => 'Financial Audit Trail & Change Log',
            'logs' => $logs,
            'currentChurch' => $this->church,
            'churchId' => $this->churchId,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => ''],
                ['label' => 'Finances', 'url' => "churches/{$this->churchId}/finance"],
                ['label' => 'Audit Trail', 'active' => true]
            ]
        ]);
    }
}