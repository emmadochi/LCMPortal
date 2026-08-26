<?php
namespace App\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Report;
use App\Models\FinanceRecord;
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\SearchHelper;

class UnitHeadController extends BaseController {
    private $unitModel;
    private $userModel;
    private $attendanceModel;
    private $reportModel;
    private $financeModel;

    // Active assignment details
    protected $churchId;
    protected $churchName;
    protected $unitId;
    protected $unitName;
    protected $assignments;

    public function __construct() {
        parent::__construct();
        
        $this->unitModel = new Unit();
        $this->userModel = new User();
        $this->attendanceModel = new Attendance();
        $this->reportModel = new Report();
        $this->financeModel = new FinanceRecord();

        // Check if user is Unit Head
        $this->assignments = $this->session->get('unit_head_assignments', []);
        if (empty($this->assignments)) {
            $this->redirect('/unauthorized');
        }

        // Initialize active assignment from request or session
        $reqChurchId = (int)$this->request->get('church_id', $this->request->post('church_id', 0));
        $reqUnitId = (int)$this->request->get('unit_id', $this->request->post('unit_id', 0));

        $active = null;

        if ($reqChurchId && $reqUnitId) {
            // Validate that the request assignment belongs to user
            foreach ($this->assignments as $assign) {
                if ((int)$assign['church_id'] === $reqChurchId && (int)$assign['unit_id'] === $reqUnitId) {
                    $active = $assign;
                    // Persist selected assignment in session
                    $this->session->set('active_unit_head_church_id', $reqChurchId);
                    $this->session->set('active_unit_head_unit_id', $reqUnitId);
                    break;
                }
            }
        }

        if (!$active) {
            // Retrieve from session if stored
            $sessChurchId = (int)$this->session->get('active_unit_head_church_id');
            $sessUnitId = (int)$this->session->get('active_unit_head_unit_id');
            if ($sessChurchId && $sessUnitId) {
                foreach ($this->assignments as $assign) {
                    if ((int)$assign['church_id'] === $sessChurchId && (int)$assign['unit_id'] === $sessUnitId) {
                        $active = $assign;
                        break;
                    }
                }
            }
        }

        if (!$active) {
            // Default to first assignment
            $active = $this->assignments[0];
            $this->session->set('active_unit_head_church_id', (int)$active['church_id']);
            $this->session->set('active_unit_head_unit_id', (int)$active['unit_id']);
        }

        $this->churchId = (int)$active['church_id'];
        $this->churchName = $active['church_name'];
        $this->unitId = (int)$active['unit_id'];
        $this->unitName = $active['unit_name'];
    }

    /**
     * Helper to prepare common layout data
     */
    private function getCommonData($title) {
        return [
            'title' => $title,
            'pageTitle' => $title,
            'churchId' => $this->churchId,
            'churchName' => $this->churchName,
            'unitId' => $this->unitId,
            'unitName' => $this->unitName,
            'assignments' => $this->assignments
        ];
    }

    /**
     * Dashboard View
     */
    public function dashboard() {
        $stats = [
            'members_count' => 0,
            'reports_count' => 0,
            'attendance_count' => 0,
            'avg_attendance' => 0,
            'net_balance' => 0.0
        ];

        // 1. Members
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM unit_user uu JOIN users u ON uu.user_id = u.id WHERE uu.unit_id = ? AND u.church_id = ?");
        $stmt->bind_param("ii", $this->unitId, $this->churchId);
        $stmt->execute();
        $stats['members_count'] = $stmt->get_result()->fetch_assoc()['count'] ?? 0;

        // 2. Reports
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM reports WHERE unit_id = ?");
        $stmt->bind_param("i", $this->unitId);
        $stmt->execute();
        $stats['reports_count'] = $stmt->get_result()->fetch_assoc()['count'] ?? 0;

        // 3. Attendance Events Count
        $stmt = $db->prepare("SELECT COUNT(DISTINCT event_date, event_type) as count FROM attendance WHERE unit_id = ? AND church_id = ?");
        $stmt->bind_param("ii", $this->unitId, $this->churchId);
        $stmt->execute();
        $stats['attendance_count'] = $stmt->get_result()->fetch_assoc()['count'] ?? 0;

        // 4. Average Attendance
        $stmt = $db->prepare("SELECT AVG(cnt) as avg FROM (
            SELECT COUNT(*) as cnt 
            FROM attendance 
            WHERE unit_id = ? AND church_id = ? AND status = 'present' 
            GROUP BY event_date, event_type 
            ORDER BY event_date DESC 
            LIMIT 5
        ) t");
        $stmt->bind_param("ii", $this->unitId, $this->churchId);
        $stmt->execute();
        $stats['avg_attendance'] = round($stmt->get_result()->fetch_assoc()['avg'] ?? 0, 1);

        // 5. Finances
        $stmt = $db->prepare("SELECT SUM(CASE WHEN transaction_type = 'income' THEN amount ELSE -amount END) as balance FROM finance_records WHERE unit_id = ? AND church_id = ?");
        $stmt->bind_param("ii", $this->unitId, $this->churchId);
        $stmt->execute();
        $stats['net_balance'] = (float)($stmt->get_result()->fetch_assoc()['balance'] ?? 0.0);

        // Recent narrative reports
        $recentReports = $this->reportModel->getReportsWithDetails(['unit_id' => $this->unitId], 'created_at DESC LIMIT 5');

        // Recent finance transactions
        $recentFinance = $this->financeModel->getFinanceWithDetails(['unit_id' => $this->unitId, 'church_id' => $this->churchId], 'transaction_date DESC, created_at DESC LIMIT 5');

        $data = $this->getCommonData('Unit Head Dashboard');
        $data['stats'] = $stats;
        $data['recentReports'] = $recentReports;
        $data['recentFinance'] = $recentFinance;

        $this->render('unit-head/dashboard', $data);
    }

    /**
     * Members List & Management
     */
    public function members() {
        $db = \App\Core\Database::getInstance();
        
        // Members of this unit at this branch
        $stmt = $db->prepare("
            SELECT u.*, uu.role as unit_role, uu.joined_at 
            FROM users u
            JOIN unit_user uu ON u.id = uu.user_id
            WHERE uu.unit_id = ? AND u.church_id = ?
            ORDER BY u.last_name, u.first_name
        ");
        $stmt->bind_param("ii", $this->unitId, $this->churchId);
        $stmt->execute();
        $members = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get candidates to be assigned (active members of this church branch who are not in this unit)
        $stmt = $db->prepare("
            SELECT id, first_name, last_name, email 
            FROM users 
            WHERE church_id = ? AND status = 'active'
            AND id NOT IN (SELECT user_id FROM unit_user WHERE unit_id = ?)
            ORDER BY last_name, first_name
        ");
        $stmt->bind_param("ii", $this->churchId, $this->unitId);
        $stmt->execute();
        $candidates = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $data = $this->getCommonData('Manage Unit Members');
        $data['members'] = $members;
        $data['candidates'] = $candidates;
        $data['csrf_token'] = Security::generateCSRFToken();

        $this->render('unit-head/members', $data);
    }

    /**
     * Assign member to unit
     */
    public function assignMember() {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->json(['success' => false, 'message' => 'Invalid security token'], 400);
        }

        $userId = (int)$this->request->post('user_id');
        $role = $this->request->post('role', 'member');

        if (!$userId) {
            $this->json(['success' => false, 'message' => 'Please select a user'], 400);
        }

        // Verify candidate is an active user at this branch
        $user = $this->userModel->find($userId);
        if (!$user || (int)$user['church_id'] !== $this->churchId || $user['status'] !== 'active') {
            $this->json(['success' => false, 'message' => 'Invalid candidate selected'], 400);
        }

        if ($this->unitModel->assignMember($this->unitId, $userId, $role)) {
            ActivityLog::log(
                $this->session->get('user_id'),
                'assign',
                'Unit',
                $this->unitId,
                "Unit Head assigned {$user['first_name']} {$user['last_name']} to {$this->unitName}"
            );
            $this->json(['success' => true, 'message' => 'Member assigned successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to assign member or member is already assigned'], 500);
        }
    }

    /**
     * Remove member from unit
     */
    public function removeMember() {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->json(['success' => false, 'message' => 'Invalid security token'], 400);
        }

        $userId = (int)$this->request->post('user_id');

        if (!$userId) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        // Verify member belongs to this church branch
        $user = $this->userModel->find($userId);
        if (!$user || (int)$user['church_id'] !== $this->churchId) {
            $this->json(['success' => false, 'message' => 'User does not belong to your branch'], 403);
        }

        if ($this->unitModel->removeMember($this->unitId, $userId)) {
            ActivityLog::log(
                $this->session->get('user_id'),
                'remove',
                'Unit',
                $this->unitId,
                "Unit Head removed {$user['first_name']} {$user['last_name']} from {$this->unitName}"
            );
            $this->json(['success' => true, 'message' => 'Member removed successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to remove member'], 500);
        }
    }

    /**
     * Attendance list
     */
    public function attendance() {
        $db = \App\Core\Database::getInstance();
        
        // Members list to show in roll call
        $stmt = $db->prepare("
            SELECT u.id, u.first_name, u.last_name, u.email, u.age_group
            FROM users u
            JOIN unit_user uu ON u.id = uu.user_id
            WHERE uu.unit_id = ? AND u.church_id = ?
            ORDER BY u.last_name, u.first_name
        ");
        $stmt->bind_param("ii", $this->unitId, $this->churchId);
        $stmt->execute();
        $members = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $eventTypes = Attendance::getEventTypes();

        $data = $this->getCommonData('Record Unit Attendance');
        $data['members'] = $members;
        $data['eventTypes'] = $eventTypes;
        $data['csrf_token'] = Security::generateCSRFToken();

        $this->render('unit-head/attendance', $data);
    }

    /**
     * Save Attendance (AJAX or form)
     */
    public function markAttendance() {
        $validation = $this->validate([
            'event_date' => 'required',
            'event_type' => 'required'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/my-unit/attendance');
        }

        $eventDate = $this->request->post('event_date');
        $eventType = $this->request->post('event_type');
        $description = trim($this->request->post('service_description', ''));
        $notes = trim($this->request->post('notes', ''));
        $marks = $this->request->post('attendance', []); // Format: [user_id => 'present'|'absent']

        // Clean out existing marks for the same date and type to support overrides
        $this->attendanceModel->deleteForService($this->unitId, $eventDate, $eventType);

        // Load unit members
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("
            SELECT u.id 
            FROM users u
            JOIN unit_user uu ON u.id = uu.user_id
            WHERE uu.unit_id = ? AND u.church_id = ?
        ");
        $stmt->bind_param("ii", $this->unitId, $this->churchId);
        $stmt->execute();
        $memberIds = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'id');

        $rows = [];
        $recordedBy = $this->session->get('user_id');

        foreach ($memberIds as $userId) {
            $status = isset($marks[$userId]) ? $marks[$userId] : 'absent';
            $isFirstTimer = $this->attendanceModel->isFirstTimerAtChurch($userId, $this->churchId, $eventDate) ? 1 : 0;
            
            $rows[] = [
                'unit_id' => $this->unitId,
                'church_id' => $this->churchId,
                'user_id' => $userId,
                'event_date' => $eventDate,
                'event_type' => $eventType,
                'status' => $status,
                'is_first_timer' => $isFirstTimer,
                'service_description' => $description,
                'notes' => $notes
            ];
        }

        if (!empty($rows)) {
            $this->attendanceModel->createBatch($rows, $recordedBy);
            ActivityLog::log(
                $recordedBy,
                'attendance_mark',
                'Unit',
                $this->unitId,
                "Recorded attendance for unit {$this->unitName} on {$eventDate}"
            );
            $this->session->setFlash('success', 'Attendance recorded successfully.');
        } else {
            $this->session->setFlash('error', 'No members found to record attendance.');
        }

        $this->redirect('/my-unit/attendance');
    }

    /**
     * reports list
     */
    public function reports() {
        $reports = $this->reportModel->getReportsWithDetails(['unit_id' => $this->unitId], 'r.created_at DESC');
        $reportTypes = ['weekly', 'event', 'departmental', 'outreach', 'media', 'technical', 'other'];

        $data = $this->getCommonData('narrative Reports');
        $data['reports'] = $reports;
        $data['reportTypes'] = $reportTypes;
        $data['csrf_token'] = Security::generateCSRFToken();

        $this->render('unit-head/reports', $data);
    }

    /**
     * Submit narrative report
     */
    public function storeReport() {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/my-unit/reports');
        }

        $validation = $this->validate([
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:10',
            'report_type' => 'required'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/my-unit/reports');
        }

        $data = [
            'unit_id' => $this->unitId,
            'user_id' => $this->session->get('user_id'),
            'title' => $this->request->post('title'),
            'content' => $this->request->post('content'),
            'report_type' => $this->request->post('report_type'),
            'status' => 'submitted', // Auto-submitted for Unit Heads
            'submitted_at' => date('Y-m-d H:i:s')
        ];

        $reportId = $this->reportModel->create($data);
        if ($reportId) {
            ActivityLog::log(
                $this->session->get('user_id'),
                'create',
                'Report',
                $reportId,
                "Unit Head submitted narrative report: {$data['title']}"
            );
            $this->session->setFlash('success', 'Report submitted successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to submit report.');
        }

        $this->redirect('/my-unit/reports');
    }

    /**
     * Finance page
     */
    public function finance() {
        $records = $this->financeModel->getFinanceWithDetails(['unit_id' => $this->unitId, 'church_id' => $this->churchId], 'transaction_date DESC, created_at DESC');

        $data = $this->getCommonData('Unit Finances');
        $data['records'] = $records;
        $data['csrf_token'] = Security::generateCSRFToken();

        $this->render('unit-head/finance', $data);
    }

    /**
     * Record Income/Expense
     */
    public function storeFinance() {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/my-unit/finance');
        }

        $validation = $this->validate([
            'transaction_type' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required',
            'transaction_date' => 'required'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/my-unit/finance');
        }

        $data = [
            'church_id' => $this->churchId,
            'unit_id' => $this->unitId,
            'recorded_by' => $this->session->get('user_id'),
            'transaction_type' => $this->request->post('transaction_type'),
            'amount' => (float)$this->request->post('amount'),
            'category' => $this->request->post('category'),
            'description' => trim($this->request->post('description', '')),
            'transaction_date' => $this->request->post('transaction_date'),
            'payment_method' => $this->request->post('payment_method', 'cash'),
            'reference_number' => trim($this->request->post('reference_number', ''))
        ];

        $recordId = $this->financeModel->create($data);
        if ($recordId) {
            ActivityLog::log(
                $this->session->get('user_id'),
                'create',
                'FinanceRecord',
                $recordId,
                "Unit Head recorded finance transaction ({$data['transaction_type']}): " . number_format($data['amount'], 2)
            );
            $this->session->setFlash('success', 'Transaction recorded successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to record transaction.');
        }

        $this->redirect('/my-unit/finance');
    }
}
