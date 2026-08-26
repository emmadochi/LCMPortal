<?php
namespace App\Controllers;

use App\Models\Attendance;
use App\Models\Unit;
use App\Models\Church;
use App\Models\User;
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\ExportHelper;

class AttendanceController extends BaseController {
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
        
        // Check permission
        if (!$this->session->hasPermission('manage_attendance') && !$this->session->hasPermission('manage_unit_attendance')) {
            $this->authorize('manage_attendance'); // This will trigger the redirection
        }
    }

    /**
     * List attendance by service (one row per event/date/unit or church-wide) with present/absent counts.
     * Optionally scoped to a church for super admin.
     */
    public function index() {
        $churchId = (int) $this->request->get('church_id', 0);
        $churchFilter = null;
        $unitIds = [];
        $services = [];

        if ($churchId && $this->session->get('user_role') === 'admin') {
            $church = $this->churchModel->find($churchId);
            if ($church) {
                $unitIds = $this->churchModel->getChurchUnitIds($churchId);
                $churchFilter = ['id' => $churchId, 'name' => $church['name']];
                $services = $this->attendanceModel->getServicesWithCounts($unitIds, $churchId);
            }
        } elseif ($this->session->isHeadPastor()) {
            $headId = $this->session->getHeadPastorChurchId();
            $church = $this->churchModel->find($headId);
            if ($church) {
                $unitIds = $this->churchModel->getChurchUnitIds($headId);
                $churchFilter = ['id' => $headId, 'name' => $church['name']];
                $services = $this->attendanceModel->getServicesWithCounts($unitIds, $headId);
            }
        }

        if ($churchFilter === null) {
            $services = $this->attendanceModel->getServicesWithCounts([], null);
        }
        
        $this->render('attendance/index', [
            'title' => 'Attendance',
            'pageTitle' => $churchFilter ? 'Attendance — ' . $churchFilter['name'] : 'Attendance Records',
            'services' => $services,
            'churchFilter' => $churchFilter
        ]);
    }

    /**
     * Return attendance chart data as JSON for AJAX (weekly, monthly, yearly).
     * Query: period=weekly|monthly|yearly, church_id= (optional, for admin church filter).
     */
    public function chartData() {
        $period = strtolower($this->request->get('period', 'monthly'));
        if (!in_array($period, ['weekly', 'monthly', 'yearly'], true)) {
            $period = 'monthly';
        }
        $churchId = (int) $this->request->get('church_id', 0);
        $unitIds = [];
        if ($churchId && $this->session->get('user_role') === 'admin') {
            $church = $this->churchModel->find($churchId);
            if ($church) {
                $unitIds = $this->churchModel->getChurchUnitIds($churchId);
            }
        }
        $data = $this->attendanceModel->getChartDataByPeriod($period, $unitIds, $churchId ?: null);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    /**
     * Show service detail: summary (present/absent counts) and lists of members present and absent.
     * Query: unit_id (optional), church_id (optional), event_date, event_type.
     * When church_id set and unit_id empty/0 → church-wide service.
     */
    public function showService() {
        $unitId = (int) $this->request->get('unit_id', 0);
        $churchId = (int) $this->request->get('church_id', 0);
        $eventDate = $this->request->get('event_date', '');
        $eventType = $this->request->get('event_type', '');

        if (!$eventDate || !$eventType) {
            $this->session->setFlash('error', 'Date and service type are required.');
            $this->redirect('/attendance');
        }

        $isChurchWide = ($churchId > 0 && $unitId <= 0);
        if (!$isChurchWide && $unitId <= 0) {
            $this->session->setFlash('error', 'Unit or church is required.');
            $this->redirect('/attendance');
        }

        $detail = $this->attendanceModel->getServiceDetail(
            $isChurchWide ? null : $unitId,
            $isChurchWide ? $churchId : null,
            $eventDate,
            $eventType
        );

        $scopeLabel = '';
        if ($isChurchWide && $churchId) {
            $church = $this->churchModel->find($churchId);
            $scopeLabel = $church ? $church['name'] . ' (All church)' : 'All church';
        } else {
            $unit = $this->unitModel->find($unitId);
            $scopeLabel = $unit ? $unit['name'] : 'Unit #' . $unitId;
        }

        $eventTypes = Attendance::getEventTypes();
        $eventTypeLabel = $eventTypes[$eventType] ?? ucfirst(str_replace('_', ' ', $eventType));
        $segmentCounts = $this->attendanceModel->getServiceSegmentCounts(
            $isChurchWide ? null : $unitId,
            $churchId ?: ($unitId ? $this->churchModel->getChurchIdForUnit($unitId) : null),
            $eventDate,
            $eventType
        );

        $this->render('attendance/show_service', [
            'title' => 'Service Attendance',
            'pageTitle' => $eventTypeLabel . ' — ' . date('M j, Y', strtotime($eventDate)),
            'detail' => $detail,
            'scopeLabel' => $scopeLabel,
            'eventTypeLabel' => $eventTypeLabel,
            'segmentCounts' => $segmentCounts,
            'breadcrumbs' => [
                ['label' => 'Attendance', 'url' => $churchId ? '/attendance?church_id=' . $churchId : '/attendance'],
                ['label' => 'Service detail', 'active' => true]
            ]
        ]);
    }

    /**
     * Show create form
     */
    public function create() {
        $csrfToken = Security::generateCSRFToken();
        $units = $this->unitModel->getActiveUnits();
        $eventTypes = Attendance::getEventTypes();
        
        $this->render('attendance/create', [
            'title' => 'Record Attendance',
            'pageTitle' => 'Record Attendance',
            'csrf_token' => $csrfToken,
            'units' => $units,
            'eventTypes' => $eventTypes,
            'breadcrumbs' => [
                ['label' => 'Attendance', 'url' => '/attendance'],
                ['label' => 'Create', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new attendance record
     */
    public function store() {
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/attendance/create');
        }

        $validation = $this->validate([
            'unit_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'event_date' => 'required|date',
            'event_type' => 'required'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/attendance/create');
        }

        $eventTypes = Attendance::getEventTypes();
        $eventType = $this->request->post('event_type', '');
        if (!isset($eventTypes[$eventType])) {
            $this->session->setFlash('error', 'Invalid event type.');
            $this->redirect('/attendance/create');
        }

        $unitId = (int)$this->request->post('unit_id');
        $userId = (int)$this->request->post('user_id');
        $eventDate = $this->request->post('event_date');
        $churchId = $this->churchModel->getChurchIdForUnit($unitId);
        $isFirstTimer = $churchId && $this->attendanceModel->isFirstTimerAtChurch($userId, $churchId, $eventDate);
        $data = [
            'unit_id' => $unitId,
            'church_id' => $churchId,
            'user_id' => $userId,
            'event_date' => $eventDate,
            'event_type' => $eventType,
            'service_description' => trim($this->request->post('service_description', '')),
            'notes' => $this->request->post('notes', ''),
            'status' => 'present',
            'is_first_timer' => $isFirstTimer ? 1 : 0,
            'recorded_by' => $this->session->get('user_id')
        ];

        $id = $this->attendanceModel->create($data);
        
        if ($id) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'create',
                'Attendance',
                $id,
                "Recorded attendance for event: {$data['event_type']}"
            );
            
            $this->session->setFlash('success', 'Attendance recorded successfully.');
            $this->redirect('/attendance');
        } else {
            $this->session->setFlash('error', 'Failed to record attendance.');
            $this->redirect('/attendance/create');
        }
    }

    /**
     * Roll-call attendance marking: select church (optional), then unit or "All church", service type, date → mark present/absent → submit.
     * When a church is selected, the unit dropdown includes "All church (main service)" to mark entire church.
     */
    public function mark() {
        $churchId = (int) $this->request->get('church_id', 0);
        $unitId = (int) $this->request->get('unit_id', 0);
        $eventDate = $this->request->get('event_date', date('Y-m-d'));
        $eventType = $this->request->get('event_type', '');

        $isAdmin = ($this->session->get('user_role') === 'admin');

        // If church_id is not provided (common when navigating via sidebar), infer it from the user's units.
        // This enables "All church (main service)" for non-admin roles tied to a single church.
        if ($churchId <= 0 && !$isAdmin) {
            if ($this->session->isHeadPastor()) {
                $churchId = $this->session->getHeadPastorChurchId();
            } else {
                $userId = (int) $this->session->get('user_id', 0);
                if ($userId > 0) {
                    $userUnits = $this->userModel->getUnits($userId);
                    $unitIds = array_map(function ($u) { return (int)($u['id'] ?? 0); }, $userUnits);
                    $churchIds = $this->churchModel->getChurchIdsByUnitIds($unitIds);
                    if (count($churchIds) === 1) {
                        $churchId = (int) $churchIds[0];
                    }
                }
            }
        }

        $churches = [];
        if ($isAdmin) {
            $churches = $this->churchModel->getChurches(['status' => 'active']);
            if (empty($churches)) {
                $churches = $this->churchModel->getChurches([]);
            }
        }

        $units = [];
        $churchFilter = null;
        // If a church is selected via query, always scope units to that church (permission already enforced).
        // This also enables the "All church (main service)" option for non-admin roles.
        if ($churchId) {
            $church = $this->churchModel->find($churchId);
            if ($church) {
                $churchUnits = $this->churchModel->getChurchUnits($churchId);
                foreach ($churchUnits as $cu) {
                    $units[] = ['id' => $cu['unit_id'], 'name' => $cu['unit_name']];
                }
                $units[] = ['id' => 0, 'name' => 'All church (main service)'];
                $churchFilter = ['id' => $churchId, 'name' => $church['name']];
            }
        }
        if (empty($units)) {
            $units = $this->unitModel->getActiveUnits();
        }

        $members = [];
        $existingMarks = [];
        $eventTypes = Attendance::getEventTypes();
        $serviceDescription = '';
        $isChurchWide = ($churchId > 0 && $unitId === 0);

        if ($unitId >= 0 && $eventDate && $eventType) {
            if ($isChurchWide) {
                $unitIds = $this->churchModel->getChurchUnitIds($churchId);
                $members = $this->userModel->getUsersByUnitIds($unitIds, 'first_name ASC, last_name ASC');
                $existingMarks = $this->attendanceModel->getMarksForChurchWideService($churchId, $eventDate, $eventType);
            } else {
                $members = $this->unitModel->getMembers($unitId);
                $existingMarks = $this->attendanceModel->getMarksForService($unitId, $eventDate, $eventType);
            }
            $serviceDescription = $this->attendanceModel->getServiceDescriptionForService($unitId, $churchId, $eventDate, $eventType);
        }

        $csrfToken = Security::generateCSRFToken();
        $churchFilter = null;
        if ($churchId) {
            $church = $this->churchModel->find($churchId);
            if ($church) {
                $churchFilter = ['id' => $churchId, 'name' => $church['name']];
            }
        }
        $ageGroups = \App\Models\User::getAgeGroups();

        $this->render('attendance/mark', [
            'title' => 'Mark Attendance',
            'pageTitle' => 'Mark Attendance (Roll-call)',
            'csrf_token' => $csrfToken,
            'churches' => $churches,
            'units' => $units,
            'eventTypes' => $eventTypes,
            'unit_id' => $unitId,
            'event_date' => $eventDate,
            'event_type' => $eventType,
            'service_description' => $serviceDescription,
            'members' => $members,
            'existingMarks' => $existingMarks,
            'ageGroups' => $ageGroups,
            'church_id' => $churchId,
            'churchFilter' => $churchFilter,
            'isChurchWide' => $isChurchWide,
            'breadcrumbs' => [
                ['label' => 'Attendance', 'url' => $churchId ? '/attendance?church_id=' . $churchId : '/attendance'],
                ['label' => 'Mark (roll-call)', 'active' => true]
            ]
        ]);
    }

    /**
     * Store roll-call attendance (batch present/absent per member).
     * Handles both unit-specific and church-wide (unit_id=0, church_id set).
     */
    public function markStore() {
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/attendance/mark');
        }

        $unitId = (int) $this->request->post('unit_id', 0);
        $churchId = (int) $this->request->post('church_id', 0);
        $eventDate = $this->request->post('event_date', '');
        $eventType = $this->request->post('event_type', '');
        $serviceDescription = trim($this->request->post('service_description', ''));
        $marks = $this->request->post('marks', []);

        $eventTypes = Attendance::getEventTypes();
        if (!isset($eventTypes[$eventType])) {
            $this->session->setFlash('error', 'Invalid event type.');
            $this->redirect('/attendance/mark');
        }

        $isChurchWide = ($churchId > 0 && $unitId === 0);
        if (!$isChurchWide && $unitId <= 0) {
            $this->session->setFlash('error', 'Unit or church is required.');
            $this->redirect('/attendance/mark');
        }
        if (!$eventDate || !$eventType) {
            $this->session->setFlash('error', 'Date and service type are required.');
            $this->redirect('/attendance/mark');
        }

        if (empty($marks) || !is_array($marks)) {
            $this->session->setFlash('error', 'Please mark at least one member.');
            $this->redirect('/attendance/mark');
        }

        $recordedBy = $this->session->get('user_id');

        $firstTimers = $this->request->post('first_timer', []);
        if (!is_array($firstTimers)) {
            $firstTimers = [];
        }
        $effectiveChurchId = $isChurchWide ? $churchId : $this->churchModel->getChurchIdForUnit($unitId);

        if ($isChurchWide) {
            $this->attendanceModel->deleteForChurchWideService($churchId, $eventDate, $eventType);
            $rows = [];
            foreach ($marks as $userId => $status) {
                $userId = (int) $userId;
                if ($userId <= 0) continue;
                $status = ($status === 'absent') ? 'absent' : 'present';
                $isFirstTimer = !empty($firstTimers[$userId]) || ($status === 'present' && $effectiveChurchId && $this->attendanceModel->isFirstTimerAtChurch($userId, $effectiveChurchId, $eventDate));
                $rows[] = [
                    'unit_id' => null,
                    'church_id' => $churchId,
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
                $isFirstTimer = !empty($firstTimers[$userId]) || ($status === 'present' && $effectiveChurchId && $this->attendanceModel->isFirstTimerAtChurch($userId, $effectiveChurchId, $eventDate));
                $rows[] = [
                    'unit_id' => $unitId,
                    'church_id' => $effectiveChurchId,
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

        $context = $isChurchWide ? "church-wide (Church ID: {$churchId})" : "Unit ID: {$unitId}";
        ActivityLog::log(
            $recordedBy,
            'create',
            'Attendance',
            0,
            "Roll-call: {$count} attendance records for {$eventType} on {$eventDate} ({$context})"
        );

        $this->session->setFlash('success', "Attendance recorded for {$count} member(s).");
        $redirect = '/attendance';
        if ($churchId) {
            $redirect = '/attendance?church_id=' . $churchId;
        }
        $this->redirect($redirect);
    }

    /**
     * Show single attendance record
     */
    public function show($id) {
        $attendance = $this->attendanceModel->find($id);
        
        if (!$attendance) {
            $this->session->setFlash('error', 'Attendance record not found.');
            $this->redirect('/attendance');
        }
        
        $this->render('attendance/show', [
            'title' => 'Attendance Record',
            'pageTitle' => 'Attendance Record',
            'attendance' => $attendance
        ]);
    }

    /**
     * Export attendance services summary.
     * Supports: csv, excel, json, pdf
     */
    public function export() {
        $churchId = (int) $this->request->get('church_id', 0);
        $churchFilter = null;
        $unitIds = [];
        $services = [];

        if ($churchId && $this->session->get('user_role') === 'admin') {
            $church = $this->churchModel->find($churchId);
            if ($church) {
                $unitIds = $this->churchModel->getChurchUnitIds($churchId);
                $churchFilter = ['id' => $churchId, 'name' => $church['name']];
                $services = $this->attendanceModel->getServicesWithCounts($unitIds, $churchId);
            }
        }

        if ($churchFilter === null) {
            $services = $this->attendanceModel->getServicesWithCounts([], null);
        }

        $rows = [];
        $eventTypeLabels = Attendance::getEventTypes();
        $churchModel = $this->churchModel;
        foreach ($services as $svc) {
            $present = (int)($svc['present_count'] ?? 0);
            $absent = (int)($svc['absent_count'] ?? 0);
            $unitId = $svc['unit_id'] ?? null;
            $churchId = !empty($svc['church_id']) ? (int)$svc['church_id'] : null;
            $scopeLabel = '';
            if ($churchId && ($unitId === null || $unitId === '')) {
                $scopeLabel = ($svc['church_name'] ?? 'Church') . ' (All church)';
            } else {
                $scopeLabel = $svc['unit_name'] ?? ('Unit #' . (int)$unitId);
            }
            $segments = $this->attendanceModel->getServiceSegmentCounts(
                ($churchId && ($unitId === null || $unitId === '')) ? null : (int)$unitId,
                $churchId ?: ($unitId ? $churchModel->getChurchIdForUnit($unitId) : null),
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
        $format = strtolower($this->request->get('format', 'csv'));
        $suffix = $churchId ? '_church_' . $churchId : '_all';
        $baseName = 'attendance' . $suffix . '_' . date('Y-m-d_His');

        switch ($format) {
            case 'json':
                ExportHelper::exportJSON($rows, $baseName . '.json');
                break;
            case 'pdf':
                ExportHelper::exportPDF($rows, $headers, 'Attendance Export', $baseName . '.pdf');
                break;
            case 'excel':
            case 'xls':
            case 'xlsx':
                ExportHelper::exportExcel($rows, $headers, $baseName . '.xls');
                break;
            case 'csv':
            default:
                ExportHelper::exportCSV($rows, $headers, $baseName . '.csv');
                break;
        }
    }

    /**
     * Show personal attendance history for the logged-in user
     */
    public function myHistory() {
        $userId = $this->session->get('user_id');
        $churchId = $this->session->get('church_id');
        
        $records = $this->attendanceModel->findAll([
            'user_id' => $userId
        ], 'event_date DESC');
        
        // Enrich with unit names
        foreach ($records as &$record) {
            if ($record['unit_id']) {
                $unit = $this->unitModel->find($record['unit_id']);
                $record['unit_name'] = $unit ? $unit['name'] : 'N/A';
            } else {
                $record['unit_name'] = 'General Service';
            }
        }

        $this->render('attendance/my_history', [
            'title' => 'My Attendance History',
            'pageTitle' => 'My Attendance History',
            'records' => $records,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/'],
                ['label' => 'Attendance History', 'active' => true]
            ]
        ]);
    }
}

