<?php
namespace App\Controllers;

use App\Models\Attendance;
use App\Models\Unit;
use App\Models\Church;
use App\Models\User;
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\ExportHelper;

class HeadPastorAttendanceController extends BaseHeadPastorController {
    private $attendanceModel;
    private $unitModel;
    private $churchModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->attendanceModel = new Attendance();
        $this->unitModel = new Unit();
        $this->churchModel = new Church();
        $this->userModel = new User();
    }

    /**
     * Dashboard view for head pastor attendance management
     */
    public function index() {
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        $services = $this->attendanceModel->getServicesWithCounts($unitIds, $this->churchId);
        
        // Get unit breakdown summary
        $unitSummaries = $this->attendanceModel->getAttendanceSummaryByUnit($this->churchId);

        // Get chart data
        $chartData = $this->attendanceModel->getChartDataByPeriod('monthly', $unitIds, $this->churchId);

        // Event type breakdown for distribution chart
        $eventTypeBreakdown = $this->attendanceModel->getEventTypeBreakdown($unitIds, $this->churchId);

        // Congregation size
        $congregation = $this->churchModel->getAllChurchCongregation($this->churchId);
        $totalCongregation = count($congregation);

        $totalPresentAll = 0;
        $totalAbsentAll = 0;
        $firstTimersAll = 0;
        foreach ($chartData as $cd) {
            $totalPresentAll += (int)($cd['present'] ?? 0);
            $totalAbsentAll += (int)($cd['absent'] ?? 0);
            $firstTimersAll += (int)($cd['first_timer'] ?? 0);
        }

        $totalServicesCount = count($services);
        $avgAttendance = $totalServicesCount > 0 ? round($totalPresentAll / $totalServicesCount, 1) : 0;

        $this->render('head-pastor/attendance/index', [
            'title' => 'Attendance Management - ' . $this->church['name'],
            'pageTitle' => 'Attendance Dashboard',
            'church' => $this->church,
            'services' => array_slice($services, 0, 10), // Recent 10 services
            'allServicesCount' => $totalServicesCount,
            'chartData' => $chartData,
            'unitIds' => $unitIds,
            'unitSummaries' => $unitSummaries,
            'eventTypeBreakdown' => $eventTypeBreakdown,
            'totalCongregation' => $totalCongregation,
            'totalPresentAll' => $totalPresentAll,
            'avgAttendance' => $avgAttendance,
            'firstTimersAll' => $firstTimersAll
        ]);
    }

    /**
     * Return attendance chart data as JSON for AJAX
     */
    public function chartData() {
        $period = strtolower($this->request->get('period', 'monthly'));
        if (!in_array($period, ['weekly', 'monthly', 'yearly'], true)) {
            $period = 'monthly';
        }
        
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        $data = $this->attendanceModel->getChartDataByPeriod($period, $unitIds, $this->churchId);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    /**
     * Generate attendance report for a period
     */
    public function report() {
        $startDate = $this->request->get('start_date', date('Y-m-01'));
        $endDate = $this->request->get('end_date', date('Y-m-t'));
        
        $segmentCounts = $this->attendanceModel->getServiceSegmentCounts(
            null, // Multi-service report
            $this->churchId,
            $startDate,
            $endDate
        );
        
        $this->render('head-pastor/attendance/report', [
            'title' => 'Attendance Report - ' . $this->church['name'],
            'pageTitle' => 'Attendance Report',
            'church' => $this->church,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'segmentCounts' => $segmentCounts
        ]);
    }

    /**
     * List all attendance records for the church
     */
    public function records() {
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        $services = $this->attendanceModel->getServicesWithCounts($unitIds, $this->churchId);
        
        $this->render('head-pastor/attendance/records', [
            'title' => 'Attendance Records - ' . $this->church['name'],
            'pageTitle' => 'Attendance Records',
            'church' => $this->church,
            'services' => $services
        ]);
    }

    /**
     * Show roll-call marking interface
     */
    public function mark() {
        $unitId = (int) $this->request->get('unit_id', 0);
        $eventDate = $this->request->get('event_date', date('Y-m-d'));
        $eventType = $this->request->get('event_type', '');

        $units = [];
        $churchUnits = $this->churchModel->getChurchUnits($this->churchId);
        foreach ($churchUnits as $cu) {
            $units[] = ['id' => $cu['unit_id'], 'name' => $cu['unit_name']];
        }
        $units[] = ['id' => 0, 'name' => 'All church (main service)'];

        $members = [];
        $existingMarks = [];
        $eventTypes = Attendance::getEventTypes();
        $serviceDescription = '';
        $isChurchWide = ($unitId === 0);

        if ($unitId >= 0 && $eventDate && $eventType) {
            if ($isChurchWide) {
                $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
                $members = $this->userModel->getUsersByUnitIds($unitIds, 'first_name ASC, last_name ASC');
                $existingMarks = $this->attendanceModel->getMarksForChurchWideService($this->churchId, $eventDate, $eventType);
            } else {
                // Verify unit belongs to this church
                $unitChurchId = $this->churchModel->getChurchIdForUnit($unitId);
                if ($unitChurchId != $this->churchId) {
                    $this->session->setFlash('error', 'Access denied to this unit.');
                    $this->redirect("/churches/{$this->churchId}/attendance/mark");
                }
                $members = $this->unitModel->getMembers($unitId);
                $existingMarks = $this->attendanceModel->getMarksForService($unitId, $eventDate, $eventType);
            }
            $serviceDescription = $this->attendanceModel->getServiceDescriptionForService($unitId ?: null, $this->churchId, $eventDate, $eventType);
        }

        $csrfToken = Security::generateCSRFToken();
        $ageGroups = \App\Models\User::getAgeGroups();

        $this->render('head-pastor/attendance/mark', [
            'title' => 'Mark Attendance - ' . $this->church['name'],
            'pageTitle' => 'Mark Attendance',
            'church' => $this->church,
            'csrf_token' => $csrfToken,
            'units' => $units,
            'eventTypes' => $eventTypes,
            'unit_id' => $unitId,
            'event_date' => $eventDate,
            'event_type' => $eventType,
            'service_description' => $serviceDescription,
            'members' => $members,
            'existingMarks' => $existingMarks,
            'ageGroups' => $ageGroups,
            'isChurchWide' => $isChurchWide
        ]);
    }

    /**
     * Store roll-call attendance
     */
    public function storeMark() {
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/churches/{$this->churchId}/attendance/mark");
        }

        $unitId = (int) $this->request->post('unit_id', 0);
        $eventDate = $this->request->post('event_date', '');
        $eventType = $this->request->post('event_type', '');
        $serviceDescription = trim($this->request->post('service_description', ''));
        $marks = $this->request->post('marks', []);

        $eventTypes = Attendance::getEventTypes();
        if (!isset($eventTypes[$eventType])) {
            $this->session->setFlash('error', 'Invalid event type.');
            $this->redirect("/churches/{$this->churchId}/attendance/mark");
        }

        $isChurchWide = ($unitId === 0);
        if (!$isChurchWide) {
            $unitChurchId = $this->churchModel->getChurchIdForUnit($unitId);
            if ($unitChurchId != $this->churchId) {
                $this->session->setFlash('error', 'Access denied to this unit.');
                $this->redirect("/churches/{$this->churchId}/attendance/mark");
            }
        }

        if (!$eventDate || !$eventType) {
            $this->session->setFlash('error', 'Date and service type are required.');
            $this->redirect("/churches/{$this->churchId}/attendance/mark");
        }

        if (empty($marks) || !is_array($marks)) {
            $this->session->setFlash('error', 'Please mark at least one member.');
            $this->redirect("/churches/{$this->churchId}/attendance/mark");
        }

        $recordedBy = $this->session->get('user_id');
        $firstTimers = $this->request->post('first_timer', []);
        if (!is_array($firstTimers)) {
            $firstTimers = [];
        }

        if ($isChurchWide) {
            $this->attendanceModel->deleteForChurchWideService($this->churchId, $eventDate, $eventType);
            $rows = [];
            foreach ($marks as $userId => $status) {
                $userId = (int) $userId;
                if ($userId <= 0) continue;
                $status = ($status === 'absent') ? 'absent' : 'present';
                $isFirstTimer = !empty($firstTimers[$userId]) || ($status === 'present' && $this->attendanceModel->isFirstTimerAtChurch($userId, $this->churchId, $eventDate));
                $rows[] = [
                    'unit_id' => null,
                    'church_id' => $this->churchId,
                    'user_id' => $userId,
                    'event_date' => $eventDate,
                    'event_type' => $eventType,
                    'service_description' => $serviceDescription ?: null,
                    'status' => $status,
                    'is_first_timer' => $status === 'present' && $isFirstTimer ? 1 : 0,
                    'notes' => ''
                ];
            }
        } else {
            $this->attendanceModel->deleteForService($unitId, $eventDate, $eventType);
            $rows = [];
            foreach ($marks as $userId => $status) {
                $userId = (int) $userId;
                if ($userId <= 0) continue;
                $status = ($status === 'absent') ? 'absent' : 'present';
                $isFirstTimer = !empty($firstTimers[$userId]) || ($status === 'present' && $this->attendanceModel->isFirstTimerAtChurch($userId, $this->churchId, $eventDate));
                $rows[] = [
                    'unit_id' => $unitId,
                    'church_id' => $this->churchId,
                    'user_id' => $userId,
                    'event_date' => $eventDate,
                    'event_type' => $eventType,
                    'service_description' => $serviceDescription ?: null,
                    'status' => $status,
                    'is_first_timer' => $status === 'present' && $isFirstTimer ? 1 : 0,
                    'notes' => ''
                ];
            }
        }

        $count = $this->attendanceModel->createBatch($rows, $recordedBy);

        ActivityLog::log(
            $recordedBy,
            'create',
            'Attendance',
            0,
            "Roll-call: {$count} records for {$eventType} on {$eventDate} (Church: {$this->church['name']})"
        );

        $this->session->setFlash('success', "Attendance recorded for {$count} member(s).");
        $this->redirect("/churches/{$this->churchId}/attendance");
    }

    /**
     * Show service detail
     */
    public function showService() {
        $unitId = (int) $this->request->get('unit_id', 0);
        $eventDate = $this->request->get('event_date', '');
        $eventType = $this->request->get('event_type', '');

        if (!$eventDate || !$eventType) {
            $this->session->setFlash('error', 'Date and service type are required.');
            $this->redirect("/churches/{$this->churchId}/attendance");
        }

        $isChurchWide = ($unitId === 0);
        if (!$isChurchWide) {
            $unitChurchId = $this->churchModel->getChurchIdForUnit($unitId);
            if ($unitChurchId != $this->churchId) {
                $this->session->setFlash('error', 'Access denied.');
                $this->redirect("/churches/{$this->churchId}/attendance");
            }
        }

        $detail = $this->attendanceModel->getServiceDetail(
            $isChurchWide ? null : $unitId,
            $isChurchWide ? $this->churchId : null,
            $eventDate,
            $eventType
        );

        $scopeLabel = '';
        if ($isChurchWide) {
            $scopeLabel = $this->church['name'] . ' (All church)';
        } else {
            $unit = $this->unitModel->find($unitId);
            $scopeLabel = $unit ? $unit['name'] : 'Unit #' . $unitId;
        }

        $eventTypes = Attendance::getEventTypes();
        $eventTypeLabel = $eventTypes[$eventType] ?? ucfirst(str_replace('_', ' ', $eventType));
        $segmentCounts = $this->attendanceModel->getServiceSegmentCounts(
            $isChurchWide ? null : $unitId,
            $this->churchId,
            $eventDate,
            $eventType
        );

        $this->render('head-pastor/attendance/show_service', [
            'title' => 'Service Detail - ' . $this->church['name'],
            'pageTitle' => $eventTypeLabel . ' — ' . date('M j, Y', strtotime($eventDate)),
            'church' => $this->church,
            'detail' => $detail,
            'scopeLabel' => $scopeLabel,
            'eventTypeLabel' => $eventTypeLabel,
            'segmentCounts' => $segmentCounts
        ]);
    }

    /**
     * Export attendance
     */
    public function export() {
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        $services = $this->attendanceModel->getServicesWithCounts($unitIds, $this->churchId);

        $rows = [];
        $eventTypeLabels = Attendance::getEventTypes();
        
        foreach ($services as $svc) {
            $present = (int)($svc['present_count'] ?? 0);
            $absent = (int)($svc['absent_count'] ?? 0);
            $unitId = $svc['unit_id'] ?? null;
            $scopeLabel = ($unitId === null) ? $this->church['name'] . ' (All church)' : ($svc['unit_name'] ?? ('Unit #' . (int)$unitId));

            $segments = $this->attendanceModel->getServiceSegmentCounts(
                ($unitId === null) ? null : (int)$unitId,
                $this->churchId,
                $svc['event_date'],
                $svc['event_type']
            );

            $rows[] = [
                'date' => $svc['event_date'],
                'event_type' => $eventTypeLabels[$svc['event_type']] ?? str_replace('_', ' ', $svc['event_type'] ?? ''),
                'scope' => $scopeLabel,
                'present' => $present,
                'absent' => $absent,
                'returning_adults' => $segments['returning_adults'],
                'returning_children' => $segments['returning_children'],
                'returning_teens' => $segments['returning_teens'],
                'first_timer_adults' => $segments['first_timer_adults'],
                'first_timer_children' => $segments['first_timer_children'],
                'first_timer_teens' => $segments['first_timer_teens'],
            ];
        }

        $headers = ['Date', 'Event Type', 'Scope', 'Present', 'Absent', 'Returning Adults', 'Returning Children', 'Returning Teens', 'First Timer Adults', 'First Timer Children', 'First Timer Teens'];
        $filename = 'attendance_' . strtolower(str_replace(' ', '_', $this->church['name'])) . '_' . date('Y-m-d_His') . '.csv';

        ExportHelper::exportCSV($rows, $headers, $filename);
    }
}
