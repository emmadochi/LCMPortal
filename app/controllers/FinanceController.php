<?php

namespace App\Controllers;

use App\Models\FinanceRecord;
use App\Utilities\Validator;
use App\Utilities\ExportHelper;

/**
 * Finance Controller
 * 
 * Handles all finance-related operations with Church ID-based routing.
 * Extends BaseModuleController for consistent CRUD functionality.
 * 
 * @author Professional Development Team
 * @version 1.0
 */
class FinanceController extends BaseModuleController
{
    /**
     * Initialize finance module
     */
    protected function initializeModule()
    {
        $this->moduleName = 'finance';
        $this->viewPath = 'finance';
        $this->model = new FinanceRecord();
    }
    
    /**
     * Validate finance input data
     * 
     * @param array $data
     * @param int|null $id
     * @return array
     */
    protected function validateInput($data, $id = null)
    {
        $validator = new Validator();
        
        $rules = [
            'title' => 'required|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'date' => 'required|date',
            'category' => 'required|max:100'
        ];
        
        // Add description as optional
        if (!empty($data['description'])) {
            $rules['description'] = 'max:1000';
        }
        
        return $validator->validate($data, $rules);
    }
    
    /**
     * Get record title for breadcrumbs
     * 
     * @param array $record
     * @return string
     */
    protected function getRecordTitle($record)
    {
        return $record['title'] ?? 'Financial Record #' . $record['id'];
    }
    
    /**
     * Display financial dashboard (supports both global and single-church views)
     * 
     * @param int|null $churchId Church ID for single-church view, null for global view
     */
    public function index($churchId = null)
    {
        $this->requirePermission($this->permissions['index']);
        
        // Determine view scope based on church_id parameter
        if ($churchId) {
            $this->renderSingleChurchView($churchId);
        } else {
            $this->renderGlobalView();
        }
    }
    
    /**
     * Render global financial dashboard (all churches)
     */
    private function renderGlobalView()
    {
        $filters = $this->request->get();
        
        // Get filtered records from all churches
        $records = $this->getFilteredRecords($filters, null);
        
        // Prepare dashboard data
        $dashboardData = $this->prepareDashboardData($records);
        
        $csrfToken = \App\Utilities\Security::generateCSRFToken();
        
        $this->render($this->viewPath . '/dashboard_all', [
            'title' => 'Financial Dashboard - All Churches',
            'records' => $records,
            'filters' => $filters,
            'csrf_token' => $csrfToken,
            'summary' => $dashboardData['summary'],
            'chart_data' => $dashboardData['chart_data'],
            'church_breakdown' => $dashboardData['church_breakdown'],
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/'],
                ['label' => 'Finance', 'url' => '/finance'],
                ['label' => 'All Churches']
            ]
        ]);
    }
    
    /**
     * Render single church financial dashboard
     * 
     * @param int $churchId
     */
    private function renderSingleChurchView($churchId)
    {
        $churchId = $this->getChurchId($churchId);
        if (!$churchId) return;
        
        // Security: Validate user has access to this specific church
        if (!$this->validateChurchAccess($churchId)) {
            $this->session->setFlash('error', 'Access denied. You do not have permission to view this church.');
            $this->redirect('/unauthorized');
            return;
        }
        
        $filters = $this->request->get();
        
        // Get filtered records for specific church
        $records = $this->getFilteredRecords($filters, $churchId);
        
        // Prepare dashboard data
        $dashboardData = $this->prepareDashboardData($records);
        
        // Get church name for display
        $churchModel = new \App\Models\Church();
        $church = $churchModel->find($churchId);
        $churchName = $church['name'] ?? 'Church #' . $churchId;
        
        $csrfToken = \App\Utilities\Security::generateCSRFToken();
        
        $this->render($this->viewPath . '/dashboard_single', [
            'title' => 'Financial Management - ' . $churchName,
            'records' => $records,
            'churchId' => $churchId,
            'churchName' => $churchName,
            'filters' => $filters,
            'csrf_token' => $csrfToken,
            'summary' => $dashboardData['summary'],
            'chart_data' => $dashboardData['chart_data'],
            'breadcrumbs' => $this->getBreadcrumbs('index', $churchId)
        ]);
    }
    
    /**
     * Validate that the current user has access to the specified church
     * Prevents Head Pastors from accessing other churches via URL manipulation
     * 
     * @param int $churchId
     * @return bool
     */
    private function validateChurchAccess($churchId)
    {
        $userRole = $this->session->get('user_role');
        
        // Admins have access to all churches
        if ($userRole === 'admin') {
            return true;
        }
        
        // Head Pastors can only access their assigned church
        if ($this->session->isHeadPastor()) {
            $headPastorChurchId = $this->session->getHeadPastorChurchId();
            return $headPastorChurchId == $churchId;
        }
        
        // For other users, check if they have a church_id in session
        $sessionChurchId = $this->session->get('church_id');
        if ($sessionChurchId) {
            return $sessionChurchId == $churchId;
        }
        
        // No valid access found
        return false;
    }
    
    /**
     * Get filtered records based on request parameters
     * 
     * @param array $filters Request filters
     * @param int|null $churchId Church ID to filter by (null for all churches)
     * @return array Filtered records
     */
    private function getFilteredRecords($filters = [], $churchId = null)
    {
        $search = $filters['search'] ?? '';
        $type = $filters['type'] ?? '';
        $startDate = $filters['start_date'] ?? date('Y-m-01');
        $endDate = $filters['end_date'] ?? date('Y-m-d');
        
        // Get records based on church scope
        if ($churchId) {
            $records = $this->model->findAll(['church_id' => $churchId]);
            // Apply date filtering
            $records = array_filter($records, function($record) use ($startDate, $endDate) {
                return $record['transaction_date'] >= $startDate && $record['transaction_date'] <= $endDate;
            });
        } else {
            $records = $this->model->getFinanceWithDetails([], null, $startDate, $endDate);
            
            // Add church names
            $churchModel = new \App\Models\Church();
            $churches = $churchModel->getChurches();
            $churchMap = [];
            foreach ($churches as $church) {
                $churchMap[$church['id']] = $church['name'];
            }
            
            foreach ($records as &$record) {
                $record['church_name'] = $churchMap[$record['church_id']] ?? 'Unknown Church';
            }
        }
        
        // Apply search filter
        if (!empty($search)) {
            $search = strtolower($search);
            $records = array_filter($records, function($record) use ($search) {
                return strpos(strtolower($record['title'] ?? ''), $search) !== false ||
                       strpos(strtolower($record['category'] ?? ''), $search) !== false ||
                       strpos(strtolower($record['description'] ?? ''), $search) !== false;
            });
        }
        
        // Apply type filter
        if (!empty($type)) {
            $records = array_filter($records, function($record) use ($type) {
                return $record['transaction_type'] === $type;
            });
        }
        
        return array_values($records); // Re-index array
    }
    
    /**
     * Prepare dashboard data (summary statistics and chart data)
     * 
     * @param array $records Financial records
     * @return array Dashboard data including summary, chart_data, and church_breakdown
     */
    private function prepareDashboardData($records)
    {
        // Calculate summary
        $totalIncome = 0;
        $totalExpense = 0;
        $churchBreakdown = [];
        
        foreach ($records as $record) {
            $amount = (float)$record['amount'];
            $churchId = $record['church_id'];
            $churchName = $record['church_name'] ?? 'Church #' . $churchId;
            
            // Initialize church breakdown if needed
            if (!isset($churchBreakdown[$churchId])) {
                $churchBreakdown[$churchId] = [
                    'name' => $churchName,
                    'income' => 0,
                    'expense' => 0
                ];
            }
            
            // Accumulate totals
            if ($record['transaction_type'] === 'income') {
                $totalIncome += $amount;
                $churchBreakdown[$churchId]['income'] += $amount;
            } else {
                $totalExpense += $amount;
                $churchBreakdown[$churchId]['expense'] += $amount;
            }
        }
        
        // Calculate balances
        foreach ($churchBreakdown as &$church) {
            $church['balance'] = $church['income'] - $church['expense'];
        }
        
        // Prepare 6-month chart data
        $chartData = $this->prepareChartData($records);
        
        return [
            'summary' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'net_balance' => $totalIncome - $totalExpense,
                'record_count' => count($records)
            ],
            'chart_data' => $chartData,
            'church_breakdown' => $churchBreakdown
        ];
    }
    
    /**
     * Prepare monthly chart data for last 6 months
     * 
     * @param array $records Financial records
     * @return array Chart data with labels, income, and expense arrays
     */
    private function prepareChartData($records)
    {
        $monthlyLabels = [];
        $monthlyIncome = [];
        $monthlyExpense = [];
        
        // Generate last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = date('Y-m', strtotime("-{$i} month"));
            $monthLabel = date('M Y', strtotime("-{$i} month"));
            $monthlyLabels[] = $monthLabel;
            
            $monthIncome = 0;
            $monthExpense = 0;
            
            foreach ($records as $record) {
                $recordMonth = date('Y-m', strtotime($record['transaction_date']));
                if ($recordMonth === $monthDate) {
                    if ($record['transaction_type'] === 'income') {
                        $monthIncome += (float)$record['amount'];
                    } else {
                        $monthExpense += (float)$record['amount'];
                    }
                }
            }
            
            $monthlyIncome[] = $monthIncome;
            $monthlyExpense[] = $monthExpense;
        }
        
        return [
            'labels' => $monthlyLabels,
            'income' => $monthlyIncome,
            'expense' => $monthlyExpense
        ];
    }
    
    /**
     * Override create to add financial categories
     * 
     * @param int|null $churchId
     */
    public function create($churchId = null)
    {
        $this->requirePermission($this->permissions['create']);
        
        $churchId = $this->getChurchId($churchId);
        if (!$churchId) return;
        
        $csrfToken = \App\Utilities\Security::generateCSRFToken();
        
        $this->render($this->viewPath . '/create', [
            'title' => 'Create Financial Record',
            'churchId' => $churchId,
            'csrf_token' => $csrfToken,
            'breadcrumbs' => $this->getBreadcrumbs('create', $churchId)
        ]);
    }
    
    /**
     * Override edit to add financial categories
     * 
     * @param int $id
     * @param int|null $churchId
     */
    public function edit($id, $churchId = null)
    {
        $this->requirePermission($this->permissions['edit']);
        
        $churchId = $this->getChurchId($churchId);
        if (!$churchId) return;
        
        $record = $this->model->find($id);
        
        if (!$record || $record['church_id'] != $churchId) {
            $this->session->setFlash('error', 'Financial record not found.');
            $this->redirect("/finance/{$churchId}");
            return;
        }
        
        $csrfToken = \App\Utilities\Security::generateCSRFToken();
        
        $this->render($this->viewPath . '/edit', [
            'title' => 'Edit Financial Record',
            'record' => $record,
            'churchId' => $churchId,
            'csrf_token' => $csrfToken,
            'breadcrumbs' => $this->getBreadcrumbs('edit', $churchId, $record)
        ]);
    }
    
    /**
     * Export financial records to CSV/Excel/PDF
     * 
     * @param string|null $format Export format (csv, excel, pdf)
     * @param int|null $churchId Church ID to filter by (null for all churches)
     */
    public function export($format = null, $churchId = null)
    {
        $this->requirePermission($this->permissions['index']);
        
        // Validate format
        $format = strtolower($format ?? 'csv');
        if (!in_array($format, ['csv', 'excel', 'pdf'])) {
            $format = 'csv';
        }
        
        // Get filters from request
        $filters = $this->request->get();
        
        // Validate church access if church_id specified
        if ($churchId && !$this->validateChurchAccess($churchId)) {
            $this->session->setFlash('error', 'Access denied. You do not have permission to view this church.');
            $this->redirect('/unauthorized');
            return;
        }
        
        // Get filtered records
        if ($churchId) {
            $records = $this->getFilteredRecords($filters, $churchId);
            $exportTitle = 'Financial Report - Single Church';
        } else {
            $records = $this->getFilteredRecords($filters, null);
            $exportTitle = 'Financial Report - All Churches';
        }
        
        // Prepare data for export
        $exportData = [];
        foreach ($records as $record) {
            $exportData[] = [
                'date' => $record['transaction_date'],
                'church' => $record['church_name'] ?? 'N/A',
                'title' => $record['title'],
                'type' => ucfirst($record['transaction_type']),
                'category' => $record['category'],
                'amount' => '₦' . number_format((float)$record['amount'], 2),
                'description' => $record['description'] ?? ''
            ];
        }
        
        // Define column headers
        $headers = ['Date', 'Church', 'Title', 'Type', 'Category', 'Amount (₦)', 'Description'];
        
        // Generate filename with timestamp
        $timestamp = date('Y-m-d_H-i-s');
        $churchSuffix = $churchId ? "_church_{$churchId}" : '_all_churches';
        $filename = "financial_report{$churchSuffix}_{$timestamp}";
        
        // Export based on format
        switch ($format) {
            case 'csv':
                ExportHelper::exportCSV($exportData, $headers, $filename . '.csv');
                break;
                
            case 'excel':
                ExportHelper::exportExcel($exportData, $headers, $filename . '.xls');
                break;
                
            case 'pdf':
                // Add summary statistics for PDF
                $summary = $this->prepareDashboardData($records)['summary'];
                $extraHtml = $this->generatePdfSummary($summary, $filters, $churchId);
                ExportHelper::exportPDF($exportData, $headers, $exportTitle, $filename . '.pdf', $extraHtml);
                break;
        }
        
        exit;
    }
    
    /**
     * Generate summary HTML for PDF export
     * 
     * @param array $summary Summary statistics
     * @param array $filters Applied filters
     * @param int|null $churchId Church ID
     * @return string HTML summary
     */
    private function generatePdfSummary($summary, $filters, $churchId = null)
    {
        $html = '<div class="section-title">Summary Statistics</div>';
        $html .= '<table class="summary-table">';
        $html .= '<tr><th>Total Income</th><td>₦' . number_format($summary['total_income'], 2) . '</td></tr>';
        $html .= '<tr><th>Total Expenses</th><td>₦' . number_format($summary['total_expense'], 2) . '</td></tr>';
        $html .= '<tr><th>Net Balance</th><td>₦' . number_format($summary['net_balance'], 2) . '</td></tr>';
        $html .= '<tr><th>Total Transactions</th><td>' . $summary['record_count'] . '</td></tr>';
        $html .= '</table>';
        
        // Add filter information
        if (!empty($filters['start_date']) || !empty($filters['end_date'])) {
            $html .= '<div class="section-title">Date Range</div>';
            $html .= '<p>From: ' . ($filters['start_date'] ?? 'Beginning') . ' To: ' . ($filters['end_date'] ?? 'Present') . '</p>';
        }
        
        // Add church information
        if ($churchId) {
            $churchModel = new \App\Models\Church();
            $church = $churchModel->find($churchId);
            if ($church) {
                $html .= '<div class="section-title">Church Information</div>';
                $html .= '<p><strong>Name:</strong> ' . htmlspecialchars($church['name']) . '</p>';
                $html .= '<p><strong>ID:</strong> ' . $churchId . '</p>';
            }
        }
        
        return $html;
    }

    /**
     * Show personal giving history for the logged-in user
     */
    public function myRecords() {
        $userId = $this->session->get('user_id');
        
        $records = $this->model->findAll([
            'member_id' => $userId,
            'transaction_type' => 'income'
        ], 'transaction_date DESC');

        $this->render('finance/my_records', [
            'title' => 'My Giving History',
            'pageTitle' => 'My Giving History',
            'records' => $records,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/'],
                ['label' => 'Giving History', 'active' => true]
            ]
        ]);
    }

    /**
     * Export personal giving records to CSV or PDF
     */
    public function exportMyRecords($format = 'csv') {
        $userId = $this->session->get('user_id');
        $format = strtolower($format);
        if (!in_array($format, ['csv', 'pdf'])) {
            $format = 'csv';
        }

        $records = $this->model->findAll([
            'member_id' => $userId,
            'transaction_type' => 'income'
        ], 'transaction_date DESC');

        $exportData = [];
        $totalAmount = 0.0;
        foreach ($records as $record) {
            $amount = (float)$record['amount'];
            $totalAmount += $amount;
            $exportData[] = [
                'date' => $record['transaction_date'],
                'title' => $record['title'],
                'category' => $record['category'],
                'amount' => '₦' . number_format($amount, 2),
                'description' => $record['description'] ?? ''
            ];
        }

        $headers = ['Date', 'Title', 'Category', 'Amount (₦)', 'Description'];
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "my_giving_history_{$timestamp}";

        if ($format === 'csv') {
            ExportHelper::exportCSV($exportData, $headers, $filename . '.csv');
        } elseif ($format === 'pdf') {
            $extraHtml = '<div style="margin-bottom: 20px;">';
            $extraHtml .= '<h3>Summary Statistics</h3>';
            $extraHtml .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">';
            $extraHtml .= '<tr style="background: #f8f9fa;"><th style="padding: 8px; border: 1px solid #dee2e6; text-align: left;">Total Contributions</th>';
            $extraHtml .= '<td style="padding: 8px; border: 1px solid #dee2e6; font-weight: bold; color: #2e7d32;">₦' . number_format($totalAmount, 2) . '</td></tr>';
            $extraHtml .= '<tr><th style="padding: 8px; border: 1px solid #dee2e6; text-align: left;">Total Records</th>';
            $extraHtml .= '<td style="padding: 8px; border: 1px solid #dee2e6;">' . count($records) . '</td></tr>';
            $extraHtml .= '</table></div>';

            ExportHelper::exportPDF($exportData, $headers, 'My Giving History', $filename . '.pdf', $extraHtml);
        }
        exit;
    }

    /**
     * Display Cashflow Statement & YoY Analysis
     */
    public function cashflow($churchId = null) {
        $effectiveChurchId = $churchId ? (int)$churchId : ($this->session->isHeadPastor() ? (int)$this->session->getHeadPastorChurchId() : null);
        $year = (int)($this->request->get('year') ?: date('Y'));

        $cashflowData = $this->model->getCashflowStatement($effectiveChurchId, $year);
        $yoyData = $this->model->getYearOverYearComparison($effectiveChurchId, $year);

        $churchModel = new \App\Models\Church();
        $churches = $churchModel->getChurches([]);
        $currentChurch = $effectiveChurchId ? $churchModel->find($effectiveChurchId) : null;

        $this->render($this->viewPath . '/cashflow', [
            'title' => 'Cashflow Statement & Analytics',
            'pageTitle' => 'Cashflow Statement & Year-over-Year Growth',
            'cashflow' => $cashflowData,
            'yoy' => $yoyData,
            'churches' => $churches,
            'currentChurch' => $currentChurch,
            'churchId' => $effectiveChurchId,
            'selectedYear' => $year,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => ''],
                ['label' => 'Finance', 'url' => 'finance'],
                ['label' => 'Cashflow & Analytics', 'active' => true]
            ]
        ]);
    }

    /**
     * Display Financial Audit Trail
     */
    public function auditTrail($churchId = null) {
        $effectiveChurchId = $churchId ? (int)$churchId : ($this->session->isHeadPastor() ? (int)$this->session->getHeadPastorChurchId() : null);
        $logs = $this->model->getFinancialAuditLogs($effectiveChurchId, 100);

        $churchModel = new \App\Models\Church();
        $churches = $churchModel->getChurches([]);
        $currentChurch = $effectiveChurchId ? $churchModel->find($effectiveChurchId) : null;

        $this->render($this->viewPath . '/audit_trail', [
            'title' => 'Financial Audit Trail',
            'pageTitle' => 'Financial Audit Trail & Change Log',
            'logs' => $logs,
            'churches' => $churches,
            'currentChurch' => $currentChurch,
            'churchId' => $effectiveChurchId,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => ''],
                ['label' => 'Finance', 'url' => 'finance'],
                ['label' => 'Audit Trail', 'active' => true]
            ]
        ]);
    }
}