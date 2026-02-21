<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Membership;
use App\Models\Unit;
use App\Models\Attendance;
use App\Utilities\Security;

class MemberDirectoryController extends BaseController {
    
    private $userModel;
    private $membershipModel;
    private $unitModel;
    private $attendanceModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->membershipModel = new Membership();
        $this->unitModel = new Unit();
        $this->attendanceModel = new Attendance();
        
        // Check permission - this should be accessible to admins and directors
        // Allow access if user is admin or head pastor
        $userRole = $this->session->get('user_role');
        $isHeadPastor = $this->session->isHeadPastor();
        
        if ($userRole !== 'admin' && !$isHeadPastor) {
            $this->authorize('manage_users');
        }
    }
    
    /**
     * Display list of all members
     */
    public function index() {
        $search = $this->request->get('search', '');
        $unitId = $this->request->get('unit_id', '');
        $membershipType = $this->request->get('membership_type', '');
        $status = $this->request->get('status', '');
        $sortBy = $this->request->get('sort_by', 'name');
        $sortOrder = $this->request->get('sort_order', 'asc');
        
        // Get all members with their membership details
        $members = $this->getAllMembersWithDetails([
            'search' => $search,
            'unit_id' => $unitId,
            'membership_type' => $membershipType,
            'status' => $status,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder
        ]);
        
        // Get filter options
        $units = $this->unitModel->getActiveUnits();
        $membershipTypes = ['visitor', 'member', 'elder', 'deacon', 'pastor'];
        $statuses = ['active', 'inactive', 'suspended', 'transferred'];
        
        $this->render('members/index', [
            'title' => 'Member Directory',
            'pageTitle' => 'Member Directory',
            'members' => $members,
            'units' => $units,
            'membershipTypes' => $membershipTypes,
            'statuses' => $statuses,
            'filters' => [
                'search' => $search,
                'unit_id' => $unitId,
                'membership_type' => $membershipType,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ]
        ]);
    }
    
    /**
     * Show detailed member profile
     */
    public function show($id) {
        $member = $this->userModel->find($id);
        
        if (!$member) {
            $this->session->setFlash('error', 'Member not found.');
            $this->redirect('/members');
        }
        
        // Get membership details
        $memberships = $this->membershipModel->getByUserId($id);
        $primaryMembership = !empty($memberships) ? $memberships[0] : null;
        
        // Get unit information
        $units = $this->userModel->getUnits($id);
        $directorUnits = $this->userModel->getDirectorUnits($id);
        
        // Get attendance history
        $attendanceHistory = $this->attendanceModel->getUserAttendance($id, 90); // Last 90 days
        
        // Get engagement score and predicted needs
        $engagementScore = $this->userModel->getEngagementScore($id);
        $predictedNeeds = $this->userModel->getPredictedNeeds($id);
        
        // Get follow-up history
        $followUpHistory = $this->userModel->getFollowUpHistory($id);
        
        // Get recent activity
        $recentActivity = $this->getRecentActivity($id);
        
        $this->render('members/show', [
            'title' => $member['first_name'] . ' ' . $member['last_name'] . ' - Profile',
            'pageTitle' => $member['first_name'] . ' ' . $member['last_name'],
            'member' => $member,
            'primaryMembership' => $primaryMembership,
            'memberships' => $memberships,
            'units' => $units,
            'directorUnits' => $directorUnits,
            'attendanceHistory' => $attendanceHistory,
            'engagementScore' => $engagementScore,
            'predictedNeeds' => $predictedNeeds,
            'followUpHistory' => $followUpHistory,
            'recentActivity' => $recentActivity
        ]);
    }
    
    /**
     * Get all members with comprehensive details
     */
    private function getAllMembersWithDetails($filters = []) {
        $members = [];
        
        try {
            $db = \App\Core\Database::getInstance();
            
            $sql = "SELECT 
                        u.id,
                        u.first_name,
                        u.last_name,
                        u.email,
                        u.role,
                        u.status as user_status,
                        u.created_at as user_created,
                        m.id as membership_id,
                        m.membership_type,
                        m.status as membership_status,
                        m.join_date,
                        m.baptism_date,
                        m.tithe_status,
                        m.engagement_score,
                        m.created_at as membership_created,
                        un.id as unit_id,
                        un.name as unit_name
                    FROM users u
                    LEFT JOIN memberships m ON u.id = m.user_id
                    LEFT JOIN units un ON m.unit_id = un.id
                    WHERE u.role != 'admin'"; // Exclude admin accounts from member directory
            
            $params = [];
            $types = '';
            
            // Apply filters
            if (!empty($filters['search'])) {
                $searchTerm = '%' . $filters['search'] . '%';
                $sql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= 'sss';
            }
            
            if (!empty($filters['unit_id'])) {
                $sql .= " AND m.unit_id = ?";
                $params[] = $filters['unit_id'];
                $types .= 'i';
            }
            
            if (!empty($filters['membership_type'])) {
                $sql .= " AND m.membership_type = ?";
                $params[] = $filters['membership_type'];
                $types .= 's';
            }
            
            if (!empty($filters['status'])) {
                $sql .= " AND m.status = ?";
                $params[] = $filters['status'];
                $types .= 's';
            }
            
            // Apply sorting
            $allowedSorts = ['name', 'email', 'membership_type', 'engagement_score', 'join_date'];
            $sortColumn = in_array($filters['sort_by'], $allowedSorts) ? $filters['sort_by'] : 'name';
            
            if ($sortColumn === 'name') {
                $sortColumn = 'u.first_name, u.last_name';
            }
            
            $sortOrder = strtolower($filters['sort_order']) === 'desc' ? 'DESC' : 'ASC';
            $sql .= " ORDER BY {$sortColumn} {$sortOrder}";
            
            $stmt = $db->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Process results and enrich with additional data
            while ($row = $result->fetch_assoc()) {
                // Get additional attendance data
                $attendanceCount = $this->getAttendanceCount($row['id'], 30);
                $lastAttendance = $this->getLastAttendanceDate($row['id']);
                
                // Enrich the member data
                $members[] = [
                    'id' => $row['id'],
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email'],
                    'role' => $row['role'],
                    'user_status' => $row['user_status'],
                    'membership_type' => $row['membership_type'],
                    'membership_status' => $row['membership_status'],
                    'join_date' => $row['join_date'],
                    'baptism_date' => $row['baptism_date'],
                    'tithe_status' => $row['tithe_status'],
                    'engagement_score' => $row['engagement_score'],
                    'unit_name' => $row['unit_name'],
                    'unit_id' => $row['unit_id'],
                    'attendance_count_30_days' => $attendanceCount,
                    'last_attendance_date' => $lastAttendance,
                    'days_since_last_attendance' => $lastAttendance ? floor((time() - strtotime($lastAttendance)) / 86400) : null
                ];
            }
            
        } catch (\Exception $e) {
            error_log('Error getting members with details: ' . $e->getMessage());
        }
        
        return $members;
    }
    
    /**
     * Get attendance count for a member in specified days
     */
    private function getAttendanceCount($userId, $days = 30) {
        try {
            $db = \App\Core\Database::getInstance();
            $sql = "SELECT COUNT(*) as count 
                    FROM attendance 
                    WHERE user_id = ? 
                    AND event_date >= DATE_SUB(NOW(), INTERVAL ? DAY)";
            
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ii", $userId, $days);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            return $result['count'] ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get last attendance date for a member
     */
    private function getLastAttendanceDate($userId) {
        try {
            $db = \App\Core\Database::getInstance();
            $sql = "SELECT MAX(event_date) as last_date 
                    FROM attendance 
                    WHERE user_id = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            return $result['last_date'];
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Get recent activity for a member
     */
    private function getRecentActivity($userId, $limit = 10) {
        $activity = [];
        
        try {
            $db = \App\Core\Database::getInstance();
            
            // Get recent attendance
            $sql = "SELECT 'attendance' as activity_type, event_date as activity_date, 
                           'Attended service/event' as description
                    FROM attendance 
                    WHERE user_id = ?
                    ORDER BY event_date DESC
                    LIMIT ?";
            
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ii", $userId, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $activity[] = $row;
            }
            
            // Get recent reports (if any)
            $sql = "SELECT 'report' as activity_type, created_at as activity_date,
                           CONCAT('Submitted ', report_type, ' report') as description
                    FROM reports 
                    WHERE user_id = ?
                    ORDER BY created_at DESC
                    LIMIT ?";
            
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ii", $userId, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $activity[] = $row;
            }
            
            // Sort all activities by date
            usort($activity, function($a, $b) {
                return strtotime($b['activity_date']) - strtotime($a['activity_date']);
            });
            
            // Limit to requested amount
            $activity = array_slice($activity, 0, $limit);
            
        } catch (\Exception $e) {
            error_log('Error getting recent activity: ' . $e->getMessage());
        }
        
        return $activity;
    }
    
    /**
     * Export member directory
     */
    public function export() {
        $format = $this->request->get('format', 'csv');
        $members = $this->getAllMembersWithDetails();
        
        $headers = [
            'ID', 'Name', 'Email', 'Role', 'Membership Type', 'Status', 
            'Join Date', 'Engagement Score', 'Unit', 'Attendance (30 days)', 
            'Days Since Last Attendance', 'Tithe Status'
        ];
        
        $data = [];
        foreach ($members as $member) {
            $data[] = [
                'id' => $member['id'],
                'name' => $member['first_name'] . ' ' . $member['last_name'],
                'email' => $member['email'],
                'role' => $member['role'],
                'membership_type' => $member['membership_type'] ?? 'N/A',
                'status' => $member['membership_status'] ?? 'N/A',
                'join_date' => $member['join_date'] ?? 'N/A',
                'engagement_score' => $member['engagement_score'] ?? 0,
                'unit' => $member['unit_name'] ?? 'N/A',
                'attendance_30_days' => $member['attendance_count_30_days'],
                'days_since_attendance' => $member['days_since_last_attendance'] ?? 'N/A',
                'tithe_status' => $member['tithe_status'] ?? 'N/A'
            ];
        }
        
        $filename = 'member_directory_' . date('Y-m-d_His') . '.' . $format;
        
        if ($format === 'json') {
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo json_encode($data, JSON_PRETTY_PRINT);
            exit;
        } else {
            // CSV export
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
            
            fclose($output);
            exit;
        }
    }
}