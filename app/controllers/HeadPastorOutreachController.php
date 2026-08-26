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
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\ExportHelper;
use App\Models\OutreachImage;
use App\Utilities\FileUpload;

class HeadPastorOutreachController extends BaseHeadPastorController {
    private $reportModel;
    private $churchModel;
    private $unitModel;
    private $imageModel;

    public function __construct() {
        parent::__construct();
        $this->reportModel = new OutreachReport();
        $this->churchModel = new Church();
        $this->unitModel = new Unit();
        $this->imageModel = new OutreachImage();
    }

    /**
     * Dashboard view for head pastor outreach management
     */
    public function index() {
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        $reports = $this->reportModel->getReportsWithDetailsByUnitIds($unitIds, 'r.program_date DESC, r.created_at DESC');
        
        // Calculate summaries
        $summary = [
            'total' => count($reports),
            'total_attendance' => 0,
            'total_first_timers' => 0,
            'total_budget' => 0,
            'total_actual' => 0,
            'efficiency' => 0
        ];

        foreach ($reports as $r) {
            $summary['total_attendance'] += (int)($r['total_attendance'] ?? 0);
            $summary['total_first_timers'] += (int)($r['first_timers_count'] ?? 0);
            $summary['total_budget'] += (float)($r['budget_total'] ?? 0);
            $summary['total_actual'] += (float)($r['actual_total'] ?? 0);
        }

        if ($summary['total_budget'] > 0) {
            $summary['efficiency'] = round(($summary['total_actual'] / $summary['total_budget']) * 100, 1);
        }

        $this->render('head-pastor/outreach/index', [
            'title' => 'Outreach Management - ' . $this->church['name'],
            'pageTitle' => 'Outreach Dashboard',
            'church' => $this->church,
            'summary' => $summary,
            'reports' => array_slice($reports, 0, 5), // Recent 5
            'unitIds' => $unitIds
        ]);
    }

    /**
     * List all outreach reports for the church
     */
    public function records() {
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        $reports = $this->reportModel->getReportsWithDetailsByUnitIds($unitIds, 'r.program_date DESC, r.created_at DESC');
        
        $this->render('head-pastor/outreach/records', [
            'title' => 'Outreach Records - ' . $this->church['name'],
            'pageTitle' => 'All Outreach Reports',
            'church' => $this->church,
            'reports' => $reports
        ]);
    }

    /**
     * Show create report form
     */
    public function create() {
        $units = $this->churchModel->getChurchUnits($this->churchId);
        $csrfToken = Security::generateCSRFToken();
        $statuses = OutreachReport::getStatuses();
        
        $this->render('head-pastor/outreach/create', [
            'title' => 'New Outreach Report - ' . $this->church['name'],
            'pageTitle' => 'Create Outreach Report',
            'church' => $this->church,
            'units' => $units,
            'csrf_token' => $csrfToken,
            'statuses' => $statuses
        ]);
    }

    /**
     * Store new outreach report
     */
    public function store() {
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/churches/{$this->churchId}/outreach/create");
        }

        $validation = $this->validate([
            'title' => 'required|min:3|max:255',
            'program_date' => 'required|date',
            'unit_id' => 'required'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/churches/{$this->churchId}/outreach/create");
        }

        // Ensure unit belongs to this church
        $unitId = (int)$this->request->post('unit_id');
        if (!$this->isUnitInChurch($unitId)) {
            $this->session->setFlash('error', 'Invalid unit selected.');
            $this->redirect("/churches/{$this->churchId}/outreach/create");
        }

        $data = [
            'church_id' => $this->churchId,
            'unit_id' => $unitId,
            'title' => $this->request->post('title'),
            'program_date' => $this->request->post('program_date'),
            'end_date' => $this->request->post('end_date') ?: null,
            'description' => $this->request->post('description') ?: null,
            'status' => $this->request->post('status', 'draft'),
            'total_attendance' => $this->request->post('total_attendance') !== '' ? (int)$this->request->post('total_attendance') : null,
            'first_timers_count' => $this->request->post('first_timers_count') !== '' ? (int)$this->request->post('first_timers_count') : null,
            'budget_total' => $this->request->post('budget_total') !== '' ? (float)$this->request->post('budget_total') : null,
            'actual_total' => $this->request->post('actual_total') !== '' ? (float)$this->request->post('actual_total') : null,
            'created_by' => $this->session->get('user_id')
        ];

        $reportId = $this->reportModel->create($data);
        
        if ($reportId) {
            $this->saveRelatedRows($reportId);
            $this->uploadGallery($reportId);
            ActivityLog::log($this->session->get('user_id'), 'create', 'OutreachReport', $reportId, "Created outreach report: {$data['title']}");
            $this->session->setFlash('success', 'Outreach report created successfully.');
            $this->redirect("/churches/{$this->churchId}/outreach/{$reportId}");
        } else {
            $this->session->setFlash('error', 'Failed to create report.');
            $this->redirect("/churches/{$this->churchId}/outreach/create");
        }
    }

    /**
     * Show single report details
     */
    public function show($id, $reportId) {
        $report = $this->reportModel->find($reportId);
        if (!$report) {
            $this->session->setFlash('error', 'Report not found.');
            $this->redirect("/churches/{$this->churchId}/outreach");
        }

        // Security check
        if ((int)$report['church_id'] !== (int)$this->churchId) {
            $this->session->setFlash('error', 'Access denied.');
            $this->redirect("/churches/{$this->churchId}/outreach");
        }

        $report['unit_name'] = $this->unitModel->find($report['unit_id'])['name'] ?? 'N/A';
        $report['creator_name'] = (new \App\Models\User())->getFullName($report['created_by']);

        $this->render('head-pastor/outreach/show', [
            'title' => $report['title'] . ' - ' . $this->church['name'],
            'pageTitle' => 'Report Details',
            'church' => $this->church,
            'report' => $report,
            'publicity' => $this->reportModel->getPublicity($reportId),
            'logistics' => $this->reportModel->getLogistics($reportId),
            'costs' => $this->reportModel->getCosts($reportId),
            'challenges' => $this->reportModel->getChallenges($reportId),
            'targets' => $this->reportModel->getTargets($reportId),
            'images' => $this->reportModel->getImages($reportId)
        ]);
    }

    /**
     * Show edit form
     */
    public function edit($id, $reportId) {
        $report = $this->reportModel->find($reportId);
        if (!$report || (int)$report['church_id'] !== (int)$this->churchId) {
            $this->session->setFlash('error', 'Report not found or access denied.');
            $this->redirect("/churches/{$this->churchId}/outreach");
        }

        $units = $this->churchModel->getChurchUnits($this->churchId);
        $csrfToken = Security::generateCSRFToken();
        $statuses = OutreachReport::getStatuses();

        $this->render('head-pastor/outreach/edit', [
            'title' => 'Edit Report - ' . $this->church['name'],
            'pageTitle' => 'Edit Outreach Report',
            'church' => $this->church,
            'report' => $report,
            'units' => $units,
            'csrf_token' => $csrfToken,
            'statuses' => $statuses,
            'publicity' => $this->reportModel->getPublicity($reportId),
            'logistics' => $this->reportModel->getLogistics($reportId),
            'costs' => $this->reportModel->getCosts($reportId),
            'challenges' => $this->reportModel->getChallenges($reportId),
            'targets' => $this->reportModel->getTargets($reportId),
            'images' => $this->reportModel->getImages($reportId)
        ]);
    }

    /**
     * Update outreach report
     */
    public function update($id, $reportId) {
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/churches/{$this->churchId}/outreach/{$reportId}/edit");
        }

        $report = $this->reportModel->find($reportId);
        if (!$report || (int)$report['church_id'] !== (int)$this->churchId) {
            $this->redirect("/churches/{$this->churchId}/outreach");
        }

        $validation = $this->validate([
            'title' => 'required|min:3|max:255',
            'program_date' => 'required|date',
            'unit_id' => 'required'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/churches/{$this->churchId}/outreach/{$reportId}/edit");
        }

        $data = [
            'unit_id' => (int)$this->request->post('unit_id'),
            'title' => $this->request->post('title'),
            'program_date' => $this->request->post('program_date'),
            'end_date' => $this->request->post('end_date') ?: null,
            'description' => $this->request->post('description') ?: null,
            'status' => $this->request->post('status', 'draft'),
            'total_attendance' => $this->request->post('total_attendance') !== '' ? (int)$this->request->post('total_attendance') : null,
            'first_timers_count' => $this->request->post('first_timers_count') !== '' ? (int)$this->request->post('first_timers_count') : null,
            'budget_total' => $this->request->post('budget_total') !== '' ? (float)$this->request->post('budget_total') : null,
            'actual_total' => $this->request->post('actual_total') !== '' ? (float)$this->request->post('actual_total') : null
        ];

        if ($this->reportModel->update($reportId, $data)) {
            $this->deleteRelatedRows($reportId);
            $this->saveRelatedRows($reportId);
            $this->uploadGallery($reportId);
            ActivityLog::log($this->session->get('user_id'), 'update', 'OutreachReport', $reportId, "Updated outreach report: {$data['title']}");
            $this->session->setFlash('success', 'Report updated successfully.');
            $this->redirect("/churches/{$this->churchId}/outreach/{$reportId}");
        } else {
            $this->session->setFlash('error', 'Failed to update report.');
            $this->redirect("/churches/{$this->churchId}/outreach/{$reportId}/edit");
        }
    }

    /**
     * Delete report
     */
    public function delete($id, $reportId) {
        $report = $this->reportModel->find($reportId);
        if (!$report || (int)$report['church_id'] !== (int)$this->churchId) {
            $this->session->setFlash('error', 'Access denied.');
            $this->redirect("/churches/{$this->churchId}/outreach");
        }

        // Delete physical gallery files
        $images = $this->imageModel->getByReportId($reportId);
        $uploader = new FileUpload('public/uploads/outreach');
        foreach ($images as $img) {
            $uploader->delete($img['file_path']);
        }

        if ($this->reportModel->delete($reportId)) {
            ActivityLog::log($this->session->get('user_id'), 'delete', 'OutreachReport', $reportId, "Deleted outreach report #{$reportId}");
            $this->session->setFlash('success', 'Report and associated gallery deleted successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to delete report.');
        }
        $this->redirect("/churches/{$this->churchId}/outreach");
    }

    /**
     * Export reports
     */
    public function export() {
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        $reports = $this->reportModel->getReportsWithDetailsByUnitIds($unitIds, 'r.program_date DESC');

        $headers = ['Title', 'Program Date', 'Unit', 'Status', 'Attendance', 'First Timers', 'Budget', 'Actual'];
        $rows = [];

        foreach ($reports as $r) {
            $rows[] = [
                $r['title'],
                $r['program_date'],
                $r['unit_name'] ?? 'N/A',
                ucfirst($r['status']),
                $r['total_attendance'] ?? 0,
                $r['first_timers_count'] ?? 0,
                number_format($r['budget_total'] ?? 0, 2),
                number_format($r['actual_total'] ?? 0, 2)
            ];
        }

        $filename = 'outreach_' . strtolower(str_replace(' ', '_', $this->church['name'])) . '_' . date('Y-m-d') . '.csv';
        ExportHelper::exportCSV($rows, $headers, $filename);
    }

    /**
     * Helper to verify unit belongs to church
     */
    private function isUnitInChurch($unitId) {
        $churchUnits = $this->churchModel->getChurchUnitIds($this->churchId);
        return in_array((int)$unitId, $churchUnits);
    }

    /**
     * Ported logic to save related tables
     */
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

    /**
     * AJAX/Form method to delete image
     */
    public function deleteImage($id, $reportId, $imageId) {
        $image = $this->imageModel->find($imageId);
        if (!$image || (int)$image['outreach_report_id'] !== (int)$reportId) {
            $this->session->setFlash('error', 'Image not found.');
            $this->redirect("/churches/{$this->churchId}/outreach/{$reportId}/edit");
        }

        // Delete from filesystem
        $uploader = new FileUpload('public/uploads/outreach');
        $uploader->delete($image['file_path']);

        // Delete from DB
        if ($this->imageModel->delete($imageId)) {
            $this->session->setFlash('success', 'Image deleted.');
        } else {
            $this->session->setFlash('error', 'Failed to delete image record.');
        }
        $this->redirect("/churches/{$this->churchId}/outreach/{$reportId}/edit");
    }

    /**
     * Helper to process gallery uploads
     */
    private function uploadGallery($reportId) {
        if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
            return;
        }

        $uploadPath = 'public/uploads/outreach';
        $uploader = new FileUpload($uploadPath, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        
        $files = $_FILES['images'];
        $count = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $fileData = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];

                $result = $uploader->upload($fileData, 'outreach_' . $reportId);
                if ($result['success']) {
                    $this->imageModel->create([
                        'outreach_report_id' => $reportId,
                        'file_path' => $result['filepath'],
                        'file_name' => $result['filename'],
                        'file_size' => $result['size']
                    ]);
                }
            }
        }
    }
}
