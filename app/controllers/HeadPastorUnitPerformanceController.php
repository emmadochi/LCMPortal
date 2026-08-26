<?php
namespace App\Controllers;

use App\Models\Church;
use App\Models\Unit;
use App\Models\Attendance;
use App\Models\FinanceRecord;
use App\Models\Report;
use App\Models\Project;

class HeadPastorUnitPerformanceController extends BaseHeadPastorController {
    
    private $churchModel;
    private $unitModel;
    private $attendanceModel;
    private $financeModel;
    private $reportModel;
    private $projectModel;

    public function __construct() {
        parent::__construct();
        $this->churchModel = new Church();
        $this->unitModel = new Unit();
        $this->attendanceModel = new Attendance();
        $this->financeModel = new FinanceRecord();
        $this->reportModel = new Report();
        $this->projectModel = new Project();
    }

    /**
     * Unified performance dashboard showing all units
     */
    public function index() {
        $units = $this->churchModel->getChurchUnits($this->churchId);
        
        foreach ($units as &$unit) {
            $unit['metrics'] = $this->unitModel->getHealthMetrics($unit['unit_id']);
        }
        unset($unit);

        $this->render('head-pastor/performance/index', [
            'title' => 'Unit Performance Dashboard',
            'pageTitle' => 'Unit Performance',
            'units' => $units,
            'church' => $this->churchModel->find($this->churchId)
        ]);
    }

    /**
     * Individual unit health profile
     */
    public function show($church_id, $unit_id) {
        $unit = $this->unitModel->find($unit_id);
        if (!$unit) {
            $this->session->setFlash('error', 'Unit not found.');
            $this->redirect("/churches/{$this->churchId}/performance");
        }

        // Verify unit belongs to church
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        if (!in_array((int)$unit_id, $unitIds)) {
            $this->session->setFlash('error', 'Unauthorized access.');
            $this->redirect("/churches/{$this->churchId}/performance");
        }

        $metrics = $this->unitModel->getHealthMetrics($unit_id);
        
        // Detailed data for charts
        $attendanceHistory = $this->attendanceModel->getServicesWithCounts([$unit_id]);
        $attendanceHistory = array_slice($attendanceHistory, 0, 10); // Limit to 10 most recent
        $financeHistory = $this->financeModel->getFinanceWithDetails(['unit_id' => $unit_id], 'transaction_date DESC', 10);
        $activeProjects = $this->projectModel->findAll(['unit_id' => $unit_id, 'status' => 'active'], 'created_at DESC');
        $recentReports = $this->reportModel->getReportsByUnitIds([$unit_id], 'created_at DESC', 5);

        $this->render('head-pastor/performance/show', [
            'title' => $unit['name'] . ' Performance Profile',
            'pageTitle' => $unit['name'] . ' Health Profile',
            'unit' => $unit,
            'metrics' => $metrics,
            'attendanceHistory' => $attendanceHistory,
            'financeHistory' => $financeHistory,
            'activeProjects' => $activeProjects,
            'recentReports' => $recentReports,
            'church' => $this->churchModel->find($this->churchId)
        ]);
    }
}
