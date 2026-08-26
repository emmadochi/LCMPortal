<?php
namespace App\Controllers;

use App\Models\Budget;
use App\Models\Church;
use App\Models\Unit;
use App\Models\ActivityLog;
use App\Utilities\Validator;
use App\Utilities\Security;

class BudgetController extends BaseController {
    protected Budget $budgetModel;
    protected Church $churchModel;
    protected Unit $unitModel;

    public function __construct() {
        parent::__construct();
        $this->budgetModel = new Budget();
        $this->churchModel = new Church();
        $this->unitModel = new Unit();
    }

    /**
     * Resolve effective church_id based on session/role
     */
    protected function resolveChurchId($churchId = null) {
        if ($this->session->isHeadPastor()) {
            return (int)$this->session->getHeadPastorChurchId();
        }
        if ($churchId) {
            return (int)$churchId;
        }
        if ($this->session->get('church_id')) {
            return (int)$this->session->get('church_id');
        }
        return null;
    }

    /**
     * Budget Dashboard & List
     */
    public function index($churchId = null) {
        $effectiveChurchId = $this->resolveChurchId($churchId);
        $fiscalYear = (int)($this->request->get('year') ?: date('Y'));
        $unitId = $this->request->get('unit_id') ? (int)$this->request->get('unit_id') : null;
        $status = $this->request->get('status') ?: null;

        $budgets = $this->budgetModel->getBudgetsWithActuals($effectiveChurchId, $fiscalYear, $unitId, $status);
        $summary = $this->budgetModel->getBudgetSummary($effectiveChurchId, $fiscalYear);

        // Churches list for Admin filter
        $churches = $this->churchModel->getChurches([]);
        $currentChurch = $effectiveChurchId ? $this->churchModel->find($effectiveChurchId) : null;
        
        // Units for current church
        $units = $effectiveChurchId ? $this->churchModel->getChurchUnits($effectiveChurchId) : [];

        $this->render('budgets/index', [
            'title' => 'Budget Management',
            'pageTitle' => 'Budget Management & Performance',
            'budgets' => $budgets,
            'summary' => $summary,
            'churches' => $churches,
            'currentChurch' => $currentChurch,
            'units' => $units,
            'churchId' => $effectiveChurchId,
            'selectedYear' => $fiscalYear,
            'selectedUnit' => $unitId,
            'selectedStatus' => $status,
            'csrfToken' => Security::generateCSRFToken(),
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => ''],
                ['label' => 'Finances', 'url' => 'finance'],
                ['label' => 'Budgets', 'active' => true]
            ]
        ]);
    }

    /**
     * Create Budget Form
     */
    public function create($churchId = null) {
        $effectiveChurchId = $this->resolveChurchId($churchId);
        $churches = $this->churchModel->getChurches([]);
        $units = $effectiveChurchId ? $this->churchModel->getChurchUnits($effectiveChurchId) : [];

        $categories = [
            'General Operations',
            'Missions & Outreach',
            'Building & Facility',
            'Events & Worship',
            'Welfare & Care',
            'Media & Tech',
            'Children & Youth',
            'Admin & Legal',
            'Other'
        ];

        $this->render('budgets/create', [
            'title' => 'Create Budget',
            'pageTitle' => 'Add New Budget Allocation',
            'churches' => $churches,
            'units' => $units,
            'churchId' => $effectiveChurchId,
            'categories' => $categories,
            'csrfToken' => Security::generateCSRFToken(),
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => ''],
                ['label' => 'Budgets', 'url' => 'budgets'],
                ['label' => 'Create', 'active' => true]
            ]
        ]);
    }

    /**
     * Store Budget
     */
    public function store($churchId = null) {
        $data = $this->request->post();
        
        $validator = new Validator();
        $rules = [
            'title' => 'required|max:255',
            'fiscal_year' => 'required|numeric',
            'total_budget_amount' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];

        if (!$validator->validate($data, $rules)) {
            $this->session->setFlash('errors', $validator->getErrors());
            $redirectUrl = $churchId ? "churches/{$churchId}/budgets/create" : "budgets/create";
            $this->response->redirect($redirectUrl);
            return;
        }

        $targetChurchId = $this->resolveChurchId($data['church_id'] ?? $churchId);
        if (!$targetChurchId) {
            $this->session->setFlash('error', 'Please select a valid church for this budget.');
            $this->response->redirect('budgets/create');
            return;
        }

        $budgetData = [
            'church_id' => $targetChurchId,
            'unit_id' => !empty($data['unit_id']) ? (int)$data['unit_id'] : null,
            'title' => trim($data['title']),
            'fiscal_year' => (int)$data['fiscal_year'],
            'period_type' => $data['period_type'] ?? 'annual',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'total_budget_amount' => (float)$data['total_budget_amount'],
            'category' => !empty($data['category']) ? trim($data['category']) : null,
            'description' => trim($data['description'] ?? ''),
            'status' => $data['status'] ?? 'active',
            'created_by' => (int)$this->session->get('user_id')
        ];

        $budgetId = $this->budgetModel->create($budgetData);

        if ($budgetId) {
            ActivityLog::log(
                $this->session->get('user_id'),
                'budget_created',
                'Budget',
                $budgetId,
                "Created budget: {$budgetData['title']} of amount \${$budgetData['total_budget_amount']} for fiscal year {$budgetData['fiscal_year']}"
            );

            $this->session->setFlash('success', 'Budget created successfully.');
            $redirectUrl = $churchId ? "churches/{$churchId}/budgets" : "budgets";
            $this->response->redirect($redirectUrl);
        } else {
            $this->session->setFlash('error', 'Failed to create budget. Please try again.');
            $this->response->redirect('budgets/create');
        }
    }

    /**
     * Edit Budget Form
     */
    public function edit($id) {
        $budget = $this->budgetModel->find($id);
        if (!$budget) {
            $this->session->setFlash('error', 'Budget not found.');
            $this->response->redirect('budgets');
            return;
        }

        $effectiveChurchId = $budget['church_id'];
        $churches = $this->churchModel->getChurches([]);
        $units = $effectiveChurchId ? $this->churchModel->getChurchUnits($effectiveChurchId) : [];

        $categories = [
            'General Operations',
            'Missions & Outreach',
            'Building & Facility',
            'Events & Worship',
            'Welfare & Care',
            'Media & Tech',
            'Children & Youth',
            'Admin & Legal',
            'Other'
        ];

        $this->render('budgets/edit', [
            'title' => 'Edit Budget',
            'pageTitle' => 'Edit Budget - ' . htmlspecialchars($budget['title']),
            'budget' => $budget,
            'churches' => $churches,
            'units' => $units,
            'categories' => $categories,
            'csrfToken' => Security::generateCSRFToken(),
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => ''],
                ['label' => 'Budgets', 'url' => 'budgets'],
                ['label' => 'Edit', 'active' => true]
            ]
        ]);
    }

    /**
     * Update Budget
     */
    public function update($id) {
        $budget = $this->budgetModel->find($id);
        if (!$budget) {
            $this->session->setFlash('error', 'Budget not found.');
            $this->response->redirect('budgets');
            return;
        }

        $data = $this->request->post();
        
        $validator = new Validator();
        $rules = [
            'title' => 'required|max:255',
            'fiscal_year' => 'required|numeric',
            'total_budget_amount' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];

        if (!$validator->validate($data, $rules)) {
            $this->session->setFlash('errors', $validator->getErrors());
            $this->response->redirect("budgets/{$id}/edit");
            return;
        }

        $updateData = [
            'title' => trim($data['title']),
            'fiscal_year' => (int)$data['fiscal_year'],
            'period_type' => $data['period_type'] ?? 'annual',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'total_budget_amount' => (float)$data['total_budget_amount'],
            'category' => !empty($data['category']) ? trim($data['category']) : null,
            'unit_id' => !empty($data['unit_id']) ? (int)$data['unit_id'] : null,
            'description' => trim($data['description'] ?? ''),
            'status' => $data['status'] ?? 'active'
        ];

        $updated = $this->budgetModel->update($id, $updateData);

        if ($updated) {
            ActivityLog::log(
                $this->session->get('user_id'),
                'budget_updated',
                'Budget',
                $id,
                "Updated budget: {$updateData['title']} (\${$updateData['total_budget_amount']})"
            );

            $this->session->setFlash('success', 'Budget updated successfully.');
            $this->response->redirect('budgets');
        } else {
            $this->session->setFlash('error', 'Failed to update budget.');
            $this->response->redirect("budgets/{$id}/edit");
        }
    }

    /**
     * Delete Budget
     */
    public function delete($id) {
        $budget = $this->budgetModel->find($id);
        if (!$budget) {
            $this->session->setFlash('error', 'Budget not found.');
            $this->response->redirect('budgets');
            return;
        }

        $this->budgetModel->delete($id);
        
        ActivityLog::log(
            $this->session->get('user_id'),
            'budget_deleted',
            'Budget',
            $id,
            "Deleted budget: {$budget['title']}"
        );

        $this->session->setFlash('success', 'Budget deleted successfully.');
        $this->response->redirect('budgets');
    }

    /**
     * Export Budgets
     */
    public function export($churchId = null, $format = 'csv') {
        $effectiveChurchId = $this->resolveChurchId($churchId);
        $fiscalYear = (int)($this->request->get('year') ?: date('Y'));
        $budgets = $this->budgetModel->getBudgetsWithActuals($effectiveChurchId, $fiscalYear);

        $filename = "budget_report_" . ($effectiveChurchId ? "church_{$effectiveChurchId}_" : "all_") . "{$fiscalYear}." . $format;
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Church', 'Unit', 'Title', 'Category', 'Fiscal Year', 'Period', 'Budgeted Amount', 'Actual Spent', 'Remaining', 'Utilization %', 'Status', 'Health']);

        foreach ($budgets as $b) {
            fputcsv($output, [
                $b['id'],
                $b['church_name'] ?? 'Global',
                $b['unit_name'] ?? 'All Units',
                $b['title'],
                $b['category'] ?? 'General',
                $b['fiscal_year'],
                $b['period_type'],
                number_format($b['total_budget_amount'], 2, '.', ''),
                number_format($b['actual_spent'], 2, '.', ''),
                number_format($b['remaining_amount'], 2, '.', ''),
                $b['utilization_pct'] . '%',
                $b['status'],
                $b['health_status']
            ]);
        }

        fclose($output);
        exit;
    }
}
