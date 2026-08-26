<?php
namespace App\Controllers;

use App\Models\Report;
use App\Models\ReportFile;
use App\Models\Unit;
use App\Models\Church;
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\SearchHelper;

class HeadPastorReportController extends BaseHeadPastorController {
    private $reportModel;
    private $unitModel;
    private $churchModel;
    private $reportFileModel;

    public function __construct() {
        parent::__construct();
        $this->reportModel = new Report();
        $this->unitModel = new Unit();
        $this->churchModel = new Church();
        $this->reportFileModel = new ReportFile();
    }

    /**
     * List all reports for units under the head pastor's church
     */
    public function index() {
        $search = $this->request->get('search', '');
        $reportType = $this->request->get('report_type', '');
        $status = $this->request->get('status', 'submitted'); // Default to submitted for HP
        $unitId = $this->request->get('unit_id', '');

        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        
        if ($unitId && !in_array((int)$unitId, $unitIds)) {
            $this->session->setFlash('error', 'Access denied to this unit.');
            $this->redirect("/churches/{$this->churchId}/unit-reports");
        }

        $activeUnitIds = $unitId ? [(int)$unitId] : $unitIds;
        $reports = $this->reportModel->getReportsWithDetailsByUnitIds($activeUnitIds, 'r.created_at DESC');

        // Apply filters
        if ($reportType || $status || $search) {
            $reports = array_filter($reports, function($report) use ($reportType, $status, $search) {
                if ($reportType && $report['report_type'] !== $reportType) return false;
                if ($status && $report['status'] !== $status) return false;
                if ($search) {
                    $searchTerm = SearchHelper::sanitize($search);
                    return stripos($report['title'], $searchTerm) !== false ||
                           stripos($report['content'] ?? '', $searchTerm) !== false ||
                           stripos($report['unit_name'] ?? '', $searchTerm) !== false;
                }
                return true;
            });
        }

        $units = $this->churchModel->getChurchUnits($this->churchId);
        $reportTypes = ['weekly', 'event', 'departmental', 'outreach', 'media', 'technical', 'other'];

        $this->render('head-pastor/reports/index', [
            'title' => 'Unit Reports - ' . $this->church['name'],
            'pageTitle' => 'Unit Narrative Reports',
            'church' => $this->church,
            'reports' => array_values($reports),
            'units' => $units,
            'reportTypes' => $reportTypes,
            'search' => $search,
            'report_type' => $reportType,
            'status' => $status,
            'unit_id' => $unitId
        ]);
    }

    /**
     * Show report detail
     */
    public function show($id, $reportId) {
        $report = $this->reportModel->find($reportId);
        
        if (!$report) {
            $this->session->setFlash('error', 'Report not found.');
            $this->redirect("/churches/{$this->churchId}/unit-reports");
        }

        // Security check: ensure unit belongs to this church
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        if (!in_array((int)$report['unit_id'], $unitIds)) {
            $this->session->setFlash('error', 'Access denied.');
            $this->redirect("/churches/{$this->churchId}/unit-reports");
        }

        $files = $this->reportModel->getFiles($reportId);
        $unit = $this->unitModel->find($report['unit_id']);

        $this->render('head-pastor/reports/show', [
            'title' => $report['title'] . ' - ' . $this->church['name'],
            'pageTitle' => 'Unit Report Details',
            'church' => $this->church,
            'report' => $report,
            'files' => $files,
            'unit' => $unit
        ]);
    }
}
