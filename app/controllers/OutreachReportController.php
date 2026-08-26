<?php
namespace App\Controllers;

use App\Models\OutreachReport;
use App\Models\OutreachPublicity;
use App\Models\OutreachLogistic;
use App\Models\OutreachCost;
use App\Models\OutreachChallenge;
use App\Models\OutreachTarget;
use App\Models\Church;
use App\Models\Unit;
use App\Models\Event;
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\ExportHelper;

class OutreachReportController extends BaseController {
    private $reportModel;
    private $churchModel;
    private $unitModel;
    private $eventModel;

    public function __construct() {
        parent::__construct();
        $this->reportModel = new OutreachReport();
        $this->churchModel = new Church();
        $this->unitModel = new Unit();
        $this->eventModel = new Event();
        
        // Allow head pastors to access outreach reports
        // They have 'view_all_reports' permission which should be sufficient for viewing
        // For create/update/delete, we'll check 'manage_reports' or 'create_reports' in specific methods
        if (!$this->session->hasPermission('manage_reports') && 
            !$this->session->hasPermission('view_all_reports') && 
            !$this->session->hasPermission('create_reports')) {
            $this->redirect('/unauthorized');
        }
    }

    /**
     * List outreach reports with optional church filter and filters
     */
    public function index($churchId = null) {
        // Automatically redirect Head Pastors to their dedicated church-scoped dashboard
        // if they access the base /outreach-reports route
        if ($this->session->isHeadPastor() && $churchId === null) {
            $hId = $this->session->getHeadPastorChurchId();
            $this->redirect("/churches/{$hId}/outreach");
        }

        $churchIdParam = (int) $this->request->get('church_id', 0);
        if (!$churchId && $churchIdParam) {
            $churchId = $churchIdParam;
        }
        
        if ($churchId) {
            $this->checkChurchAccess($churchId);
        }

        $status = $this->request->get('status', '');
        $search = $this->request->get('search', '');
        $churchFilter = null;
        $reports = [];

        if ($churchId) {
            $church = $this->churchModel->find($churchId);
            if ($church) {
                $unitIds = $this->churchModel->getChurchUnitIds($churchId);
                $reports = $this->reportModel->getReportsWithDetailsByUnitIds($unitIds, 'program_date DESC, created_at DESC');
                $churchFilter = ['id' => $churchId, 'name' => $church['name']];
            }
        }

        if ($churchFilter === null) {
            $conditions = [];
            if ($status !== '') {
                $conditions['status'] = $status;
            }
            $reports = $this->reportModel->getReportsWithDetails($conditions, 'program_date DESC, created_at DESC');
        }

        if ($search !== '') {
            $term = trim($search);
            $reports = array_filter($reports, function ($r) use ($term) {
                return stripos($r['title'] ?? '', $term) !== false
                    || stripos($r['description'] ?? '', $term) !== false
                    || stripos($r['church_name'] ?? '', $term) !== false
                    || stripos($r['unit_name'] ?? '', $term) !== false;
            });
        }

        $churches = $this->churchModel->getChurches([]);
        $statuses = OutreachReport::getStatuses();
        $csrfToken = Security::generateCSRFToken();

        $this->render('outreach-reports/index', [
            'title' => 'Outreach & Event Reports',
            'pageTitle' => $churchFilter ? 'Outreach Reports — ' . $churchFilter['name'] : 'Outreach & Event Reports',
            'reports' => array_values($reports),
            'churches' => $churches,
            'statuses' => $statuses,
            'churchFilter' => $churchFilter,
            'search' => $search,
            'status' => $status,
            'get_church_id' => $this->request->get('church_id', ''),
            'csrf_token' => $csrfToken,
            'breadcrumbs' => [
                ['label' => 'Outreach Reports', 'url' => '/outreach-reports'],
                ['label' => 'All', 'active' => true]
            ]
        ]);
    }

    /**
     * Show create form
     */
    public function create($churchId = null) {
        $churchIdParam = (int) $this->request->get('church_id', 0);
        if (!$churchId && $churchIdParam) {
            $churchId = $churchIdParam;
        }

        if ($churchId) {
            $this->checkChurchAccess($churchId);
        }

        // Only users with manage_reports or create_reports permission can create reports
        if (!$this->session->hasPermission('manage_reports') && !$this->session->hasPermission('create_reports')) {
            $this->session->setFlash('error', 'You do not have permission to create outreach reports.');
            $this->redirect('/outreach-reports');
        }
        
        $csrfToken = Security::generateCSRFToken();
        $churches = $this->churchModel->getChurches([]);
        $units = $this->unitModel->getActiveUnits();
        $events = $this->eventModel->getEventsWithDetails([], 'start_date DESC');
        $statuses = OutreachReport::getStatuses();

        $this->render('outreach-reports/create', [
            'title' => 'New Outreach Report',
            'pageTitle' => 'New Outreach / Event Report',
            'csrf_token' => $csrfToken,
            'churches' => $churches,
            'units' => $units,
            'events' => $events,
            'statuses' => $statuses,
            'report' => null,
            'breadcrumbs' => [
                ['label' => 'Outreach Reports', 'url' => '/outreach-reports'],
                ['label' => 'Create', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new outreach report and related records
     */
    public function store($churchId = null) {
        $churchIdParam = (int) $this->request->post('church_id', 0);
        if (!$churchId && $churchIdParam) {
            $churchId = $churchIdParam;
        }

        if ($churchId) {
            $this->checkChurchAccess($churchId);
        }

        // Only users with manage_reports or create_reports permission can create reports
        if (!$this->session->hasPermission('manage_reports') && !$this->session->hasPermission('create_reports')) {
            $this->session->setFlash('error', 'You do not have permission to create outreach reports.');
            $this->redirect('/outreach-reports');
        }
        
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/outreach-reports/create');
        }

        $validation = $this->validate([
            'title' => 'required|min:3|max:255',
            'program_date' => 'required|date'
        ]);
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/outreach-reports/create');
        }

        $data = [
            'event_id' => $this->request->post('event_id') ? (int) $this->request->post('event_id') : null,
            'church_id' => $this->request->post('church_id') ? (int) $this->request->post('church_id') : null,
            'unit_id' => $this->request->post('unit_id') ? (int) $this->request->post('unit_id') : null,
            'title' => $this->request->post('title'),
            'program_date' => $this->request->post('program_date'),
            'end_date' => $this->request->post('end_date') ?: null,
            'description' => $this->request->post('description') ?: null,
            'status' => $this->request->post('status', 'draft'),
            'total_attendance' => $this->request->post('total_attendance') !== '' ? (int) $this->request->post('total_attendance') : null,
            'first_timers_count' => $this->request->post('first_timers_count') !== '' ? (int) $this->request->post('first_timers_count') : null,
            'budget_total' => $this->request->post('budget_total') !== '' ? (float) $this->request->post('budget_total') : null,
            'actual_total' => $this->request->post('actual_total') !== '' ? (float) $this->request->post('actual_total') : null,
            'created_by' => $this->session->get('user_id')
        ];

        $reportId = $this->reportModel->create($data);
        if (!$reportId) {
            $this->session->setFlash('error', 'Failed to create report.');
            $this->redirect('/outreach-reports/create');
        }

        $this->saveRelatedRows($reportId);

        ActivityLog::log(
            $this->session->get('user_id'),
            'create',
            'OutreachReport',
            $reportId,
            "Created outreach report: {$data['title']}"
        );

        $this->session->setFlash('success', 'Outreach report created successfully.');
        $this->redirect('/outreach-reports/' . $reportId);
    }

    /**
     * Show single report with all sections
     */
    public function show($idOrChurchId, $id = null) {
        if ($id === null) {
            $id = $idOrChurchId;
            $churchId = null;
        } else {
            $churchId = $idOrChurchId;
        }

        if ($churchId) {
            $this->checkChurchAccess($churchId);
        }

        $report = $this->reportModel->find($id);
        if (!$report) {
            $this->session->setFlash('error', 'Report not found.');
            $this->redirect('/outreach-reports');
        }

        $report = array_merge($report, [
            'creator_name' => null,
            'church_name' => null,
            'unit_name' => null,
            'event_title' => null
        ]);
        $withDetails = $this->reportModel->getReportsWithDetails(['id' => $id]);
        if (!empty($withDetails)) {
            $report = array_merge($report, $withDetails[0]);
        }

        $publicity = $this->reportModel->getPublicity($id);
        $logistics = $this->reportModel->getLogistics($id);
        $costs = $this->reportModel->getCosts($id);
        $challenges = $this->reportModel->getChallenges($id);
        $targets = $this->reportModel->getTargets($id);

        $this->render('outreach-reports/show', [
            'title' => $report['title'],
            'pageTitle' => $report['title'],
            'report' => $report,
            'publicity' => $publicity,
            'logistics' => $logistics,
            'costs' => $costs,
            'challenges' => $challenges,
            'targets' => $targets,
            'breadcrumbs' => [
                ['label' => 'Outreach Reports', 'url' => '/outreach-reports'],
                ['label' => $report['title'], 'active' => true]
            ]
        ]);
    }

    /**
     * Show edit form
     */
    public function edit($idOrChurchId, $id = null) {
        if ($id === null) {
            $id = $idOrChurchId;
            $churchId = null;
        } else {
            $churchId = $idOrChurchId;
        }

        if ($churchId) {
            $this->checkChurchAccess($churchId);
        }

        // Only users with manage_reports or create_reports permission can edit reports
        if (!$this->session->hasPermission('manage_reports') && !$this->session->hasPermission('create_reports')) {
            $this->session->setFlash('error', 'You do not have permission to edit outreach reports.');
            $this->redirect('/outreach-reports');
        }
        
        $report = $this->reportModel->find($id);
        if (!$report) {
            $this->session->setFlash('error', 'Report not found.');
            $this->redirect('/outreach-reports');
        }

        $csrfToken = Security::generateCSRFToken();
        $churches = $this->churchModel->getChurches([]);
        $units = $this->unitModel->getActiveUnits();
        $events = $this->eventModel->getEventsWithDetails([], 'start_date DESC');
        $statuses = OutreachReport::getStatuses();
        $publicity = $this->reportModel->getPublicity($id);
        $logistics = $this->reportModel->getLogistics($id);
        $costs = $this->reportModel->getCosts($id);
        $challenges = $this->reportModel->getChallenges($id);
        $targets = $this->reportModel->getTargets($id);

        $this->render('outreach-reports/edit', [
            'title' => 'Edit Report',
            'pageTitle' => 'Edit: ' . $report['title'],
            'csrf_token' => $csrfToken,
            'report' => $report,
            'churches' => $churches,
            'units' => $units,
            'events' => $events,
            'statuses' => $statuses,
            'publicity' => $publicity,
            'logistics' => $logistics,
            'costs' => $costs,
            'challenges' => $challenges,
            'targets' => $targets,
            'breadcrumbs' => [
                ['label' => 'Outreach Reports', 'url' => '/outreach-reports'],
                ['label' => $report['title'], 'url' => '/outreach-reports/' . $id],
                ['label' => 'Edit', 'active' => true]
            ]
        ]);
    }

    /**
     * Update outreach report and related records
     */
    public function update($idOrChurchId, $id = null) {
        if ($id === null) {
            $id = $idOrChurchId;
            $churchId = null;
        } else {
            $churchId = $idOrChurchId;
        }

        if ($churchId) {
            $this->checkChurchAccess($churchId);
        }

        // Only users with manage_reports or create_reports permission can update reports
        if (!$this->session->hasPermission('manage_reports') && !$this->session->hasPermission('create_reports')) {
            $this->session->setFlash('error', 'You do not have permission to update outreach reports.');
            $this->redirect('/outreach-reports');
        }
        
        $report = $this->reportModel->find($id);
        if (!$report) {
            $this->session->setFlash('error', 'Report not found.');
            $this->redirect('/outreach-reports');
        }

        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/outreach-reports/' . $id . '/edit');
        }

        $validation = $this->validate([
            'title' => 'required|min:3|max:255',
            'program_date' => 'required|date'
        ]);
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/outreach-reports/' . $id . '/edit');
        }

        $data = [
            'event_id' => $this->request->post('event_id') ? (int) $this->request->post('event_id') : null,
            'church_id' => $this->request->post('church_id') ? (int) $this->request->post('church_id') : null,
            'unit_id' => $this->request->post('unit_id') ? (int) $this->request->post('unit_id') : null,
            'title' => $this->request->post('title'),
            'program_date' => $this->request->post('program_date'),
            'end_date' => $this->request->post('end_date') ?: null,
            'description' => $this->request->post('description') ?: null,
            'status' => $this->request->post('status', 'draft'),
            'total_attendance' => $this->request->post('total_attendance') !== '' ? (int) $this->request->post('total_attendance') : null,
            'first_timers_count' => $this->request->post('first_timers_count') !== '' ? (int) $this->request->post('first_timers_count') : null,
            'budget_total' => $this->request->post('budget_total') !== '' ? (float) $this->request->post('budget_total') : null,
            'actual_total' => $this->request->post('actual_total') !== '' ? (float) $this->request->post('actual_total') : null
        ];

        $this->reportModel->update($id, $data);
        $this->deleteRelatedRows($id);
        $this->saveRelatedRows($id);

        ActivityLog::log(
            $this->session->get('user_id'),
            'update',
            'OutreachReport',
            $id,
            "Updated outreach report: {$data['title']}"
        );

        $this->session->setFlash('success', 'Report updated successfully.');
        $this->redirect('/outreach-reports/' . $id);
    }

    /**
     * Delete report (and related records via CASCADE)
     */
    public function delete($idOrChurchId, $id = null) {
        if ($id === null) {
            $id = $idOrChurchId;
            $churchId = null;
        } else {
            $churchId = $idOrChurchId;
        }

        if ($churchId) {
            $this->checkChurchAccess($churchId);
        }

        // Only users with manage_reports permission can delete reports
        if (!$this->session->hasPermission('manage_reports')) {
            $this->session->setFlash('error', 'You do not have permission to delete outreach reports.');
            $this->redirect('/outreach-reports');
        }
        
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/outreach-reports');
        }
        $report = $this->reportModel->find($id);
        if (!$report) {
            $this->session->setFlash('error', 'Report not found.');
            $this->redirect('/outreach-reports');
        }
        $title = $report['title'];
        if ($this->reportModel->delete($id)) {
            ActivityLog::log($this->session->get('user_id'), 'delete', 'OutreachReport', $id, "Deleted outreach report: {$title}");
            $this->session->setFlash('success', 'Report deleted.');
        } else {
            $this->session->setFlash('error', 'Failed to delete report.');
        }
        $this->redirect('/outreach-reports');
    }

    /**
     * Export outreach reports (CSV, Excel, JSON, PDF)
     */
    public function export($churchId = null) {
        $churchIdParam = (int) $this->request->get('church_id', 0);
        if (!$churchId && $churchIdParam) {
            $churchId = $churchIdParam;
        }

        if ($churchId) {
            $this->checkChurchAccess($churchId);
        }

        // Only users with manage_reports or view_all_reports permission can export
        if (!$this->session->hasPermission('manage_reports') && !$this->session->hasPermission('view_all_reports')) {
            $this->session->setFlash('error', 'You do not have permission to export outreach reports.');
            $this->redirect('/outreach-reports');
        }
        
        $reports = [];
        if ($churchId) {
            $church = $this->churchModel->find($churchId);
            if ($church) {
                $unitIds = $this->churchModel->getChurchUnitIds($churchId);
                $reports = $this->reportModel->getReportsWithDetailsByUnitIds($unitIds, 'program_date DESC');
            }
        }
        if (empty($reports)) {
            $reports = $this->reportModel->getReportsWithDetails([], 'program_date DESC, created_at DESC');
        }

        $headers = ['Title', 'Program Date', 'Church', 'Unit', 'Status', 'Attendance', 'First Timers', 'Budget', 'Actual', 'Created By'];
        $rows = [];
        foreach ($reports as $r) {
            $rows[] = [
                $r['title'] ?? '',
                $r['program_date'] ?? '',
                $r['church_name'] ?? 'N/A',
                $r['unit_name'] ?? 'N/A',
                $r['status'] ?? '',
                $r['total_attendance'] ?? '',
                $r['first_timers_count'] ?? '',
                $r['budget_total'] ?? '',
                $r['actual_total'] ?? '',
                trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''))
            ];
        }

        $format = strtolower($this->request->get('format', 'csv'));
        $suffix = $churchId ? '_church_' . $churchId : '';
        $baseName = 'outreach_reports' . $suffix . '_' . date('Y-m-d_His');
        switch ($format) {
            case 'json':
                ExportHelper::exportJSON(array_map(function ($r) {
                    return [
                        'title' => $r['title'],
                        'program_date' => $r['program_date'],
                        'church' => $r['church_name'] ?? null,
                        'unit' => $r['unit_name'] ?? null,
                        'status' => $r['status'],
                        'total_attendance' => $r['total_attendance'],
                        'first_timers_count' => $r['first_timers_count'],
                        'budget_total' => $r['budget_total'],
                        'actual_total' => $r['actual_total']
                    ];
                }, $reports), $baseName . '.json');
                break;
            case 'pdf':
                ExportHelper::exportPDF($rows, $headers, 'Outreach Reports Export', $baseName . '.pdf');
                break;
            case 'excel':
            case 'xls':
            case 'xlsx':
                ExportHelper::exportExcel($rows, $headers, $baseName . '.xls');
                break;
            default:
                ExportHelper::exportCSV($rows, $headers, $baseName . '.csv');
                break;
        }
    }

    private function saveRelatedRows($reportId) {
        $publicity = $this->request->post('publicity');
        if (is_array($publicity)) {
            $pubModel = new OutreachPublicity();
            foreach ($publicity as $i => $row) {
                if (empty($row['channel'])) continue;
                $pubModel->create([
                    'outreach_report_id' => $reportId,
                    'channel' => $row['channel'],
                    'details' => $row['details'] ?? null,
                    'estimated_reach' => isset($row['estimated_reach']) && $row['estimated_reach'] !== '' ? (int) $row['estimated_reach'] : null,
                    'cost' => isset($row['cost']) && $row['cost'] !== '' ? (float) $row['cost'] : null,
                    'sort_order' => $i
                ]);
            }
        }

        $logistics = $this->request->post('logistics');
        if (is_array($logistics)) {
            $logModel = new OutreachLogistic();
            foreach ($logistics as $i => $row) {
                if (empty($row['description'])) continue;
                $logModel->create([
                    'outreach_report_id' => $reportId,
                    'category' => $row['category'] ?? 'other',
                    'description' => $row['description'],
                    'notes' => $row['notes'] ?? null,
                    'sort_order' => $i
                ]);
            }
        }

        $costs = $this->request->post('costs');
        if (is_array($costs)) {
            $costModel = new OutreachCost();
            foreach ($costs as $i => $row) {
                if ($row['category'] === '' && (float)($row['budgeted_amount'] ?? 0) === 0.0 && (float)($row['actual_amount'] ?? 0) === 0.0) continue;
                $costModel->create([
                    'outreach_report_id' => $reportId,
                    'category' => $row['category'] ?? 'other',
                    'budgeted_amount' => isset($row['budgeted_amount']) && $row['budgeted_amount'] !== '' ? (float) $row['budgeted_amount'] : null,
                    'actual_amount' => isset($row['actual_amount']) && $row['actual_amount'] !== '' ? (float) $row['actual_amount'] : null,
                    'notes' => $row['notes'] ?? null,
                    'sort_order' => $i
                ]);
            }
        }

        $challenges = $this->request->post('challenges');
        if (is_array($challenges)) {
            $challModel = new OutreachChallenge();
            foreach ($challenges as $i => $row) {
                if (empty($row['description'])) continue;
                $challModel->create([
                    'outreach_report_id' => $reportId,
                    'description' => $row['description'],
                    'category' => $row['category'] ?? null,
                    'severity' => $row['severity'] ?? null,
                    'sort_order' => $i
                ]);
            }
        }

        $targets = $this->request->post('targets');
        if (is_array($targets)) {
            $targModel = new OutreachTarget();
            foreach ($targets as $i => $row) {
                if (empty($row['target_name'])) continue;
                $targModel->create([
                    'outreach_report_id' => $reportId,
                    'target_name' => $row['target_name'],
                    'target_value' => (float) ($row['target_value'] ?? 0),
                    'actual_value' => isset($row['actual_value']) && $row['actual_value'] !== '' ? (float) $row['actual_value'] : null,
                    'unit' => $row['unit'] ?? null,
                    'notes' => $row['notes'] ?? null,
                    'sort_order' => $i
                ]);
            }
        }
    }

    private function deleteRelatedRows($reportId) {
        $id = (int) $reportId;
        $tables = ['outreach_publicity', 'outreach_logistics', 'outreach_costs', 'outreach_challenges', 'outreach_targets'];
        $db = $this->reportModel->db;
        foreach ($tables as $table) {
            $stmt = $db->prepare("DELETE FROM {$table} WHERE outreach_report_id = ?");
            if ($stmt) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
            }
        }
    }
    private function checkChurchAccess($churchId) {
        if (!$churchId) return true;
        
        $userRole = $this->session->get('user_role');
        
        // Admins can access any church
        if ($userRole === 'admin') {
            return true;
        }
        
        // Head pastors can only access their assigned church
        if ($this->session->isHeadPastor()) {
            $headPastorChurchId = $this->session->getHeadPastorChurchId();
            if ($headPastorChurchId == $churchId) {
                return true;
            }
        }
        
        // If we get here, user doesn't have access
        $this->session->setFlash('error', 'You do not have permission to access these reports.');
        $this->redirect('/unauthorized');
        exit;
    }
}
