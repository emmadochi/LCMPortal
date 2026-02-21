<?php
namespace App\Models;

use App\Utilities\Security;
use App\Models\FollowUp;

class User extends BaseModel {
    protected $table = 'users';
    protected $fillable = ['email', 'password', 'first_name', 'last_name', 'profile_picture', 'age_group', 'role', 'status'];

    /**
     * Age group options for attendance segment reporting (no exact age required).
     */
    public static function getAgeGroups() {
        return [
            'adult'  => 'Adult',
            'child'  => 'Child',
            'teen'   => 'Teen',
        ];
    }

    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Authenticate user
     */
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }

        if ($user['status'] !== 'active') {
            return false;
        }

        if (!Security::verifyPassword($password, $user['password'])) {
            return false;
        }

        return $user;
    }

    /**
     * Create user with hashed password
     */
    public function createUser($data) {
        if (isset($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        }
        return $this->create($data);
    }

    /**
     * Update user password
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = Security::hashPassword($newPassword);
        return $this->update($userId, ['password' => $hashedPassword]);
    }

    /**
     * Get all users (e.g. for dropdowns in church create/edit, event organizers)
     */
    public function getAllUsers() {
        return $this->findAll([], 'last_name ASC, first_name ASC');
    }

    /**
     * Get all active users (for broadcast notifications).
     */
    public function getActiveUsers() {
        return $this->findAll(['status' => 'active'], 'last_name ASC, first_name ASC');
    }

    /**
     * Get active members for follow-up member dropdown and staff assignment.
     * Returns active users ordered by name (used as both members and assignable staff).
     */
    public function getActiveMembers() {
        return $this->getActiveUsers();
    }

    /**
     * Get follow-up history for a member (for member profile Follow-ups tab).
     *
     * @param int $memberId User/member ID
     * @return array
     */
    public function getFollowUpHistory($memberId) {
        $followUpModel = new FollowUp();
        return $followUpModel->getMemberFollowUpHistory($memberId);
    }

    /**
     * Get active users by IDs
     */
    public function getActiveUsersByIds(array $ids) {
        if (empty($ids)) {
            return [];
        }
        $ids = array_values(array_unique(array_map('intval', $ids)));
        $ids = array_filter($ids, function ($id) { return $id > 0; });
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM {$this->table} WHERE id IN ({$placeholders}) AND status = 'active' ORDER BY last_name ASC, first_name ASC";
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('i', count($ids));
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get active users with any of the given roles (for role-based broadcast).
     *
     * @param array $roles e.g. ['director', 'pastor']
     * @return array
     */
    public function getActiveUsersByRoles(array $roles) {
        if (empty($roles)) {
            return [];
        }
        $roles = array_map(function ($r) {
            return $this->db->real_escape_string($r);
        }, $roles);
        $placeholders = implode(',', array_fill(0, count($roles), '?'));
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active' AND role IN ({$placeholders}) ORDER BY last_name ASC, first_name ASC";
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('s', count($roles));
        $stmt->bind_param($types, ...$roles);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get users with role 'pastor' for church pastor dropdown
     */
    public function getPastors() {
        return $this->findAll(['role' => 'pastor', 'status' => 'active'], 'last_name ASC, first_name ASC');
    }

    /**
     * Get user's units
     */
    public function getUnits($userId) {
        $sql = "SELECT u.*, uu.role, uu.joined_at 
                FROM units u 
                INNER JOIN unit_user uu ON u.id = uu.unit_id 
                WHERE uu.user_id = ? 
                ORDER BY u.name";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get units where user is director
     */
    public function getDirectorUnits($userId) {
        $sql = "SELECT u.*, ud.assigned_at 
                FROM units u 
                INNER JOIN unit_directors ud ON u.id = ud.unit_id 
                WHERE ud.user_id = ? 
                ORDER BY u.name";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Check if user has permission
     */
    public function hasPermission($userId, $permission) {
        $user = $this->find($userId);
        if (!$user) {
            return false;
        }

        // Admin has all permissions
        if ($user['role'] === 'admin') {
            return true;
        }

        // Define role-based permissions
        $permissions = $this->getRolePermissions($user['role']);
        return in_array($permission, $permissions);
    }

    /**
     * Get permissions for a role
     */
    private function getRolePermissions($role) {
        $rolePermissions = [
            'admin' => [
                'manage_users',
                'manage_units',
                'manage_reports',
                'view_all_reports',
                'manage_finance',
                'manage_media',
                'manage_projects',
                'manage_properties',
                'view_dashboard',
                'send_broadcast_notifications'
            ],
            'director' => [
                'manage_units',
                'manage_reports',
                'view_unit_reports',
                'manage_unit_finance',
                'manage_unit_media',
                'manage_unit_projects',
                'view_dashboard'
            ],
            'officer' => [
                'create_reports',
                'view_unit_reports',
                'view_dashboard'
            ],
            'pastor' => [
                'view_all_reports',
                'view_dashboard'
            ],
            'user' => [
                'view_dashboard'
            ]
        ];

        return $rolePermissions[$role] ?? [];
    }

    /**
     * Get users who belong to any of the given unit IDs (e.g. church membership via units for super admin church view)
     */
    public function getUsersByUnitIds(array $unitIds, $orderBy = 'last_name ASC') {
        if (empty($unitIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
        $sql = "SELECT DISTINCT us.* FROM users us 
                INNER JOIN unit_user uu ON us.id = uu.user_id 
                WHERE uu.unit_id IN ({$placeholders})";
        if ($orderBy) {
            $sql .= " ORDER BY us." . $orderBy;
        }
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('i', count($unitIds));
        $stmt->bind_param($types, ...$unitIds);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get active users who belong to any of the given unit IDs (for notifications etc.)
     */
    public function getActiveUsersByUnitIds(array $unitIds, $orderBy = 'last_name ASC, first_name ASC') {
        if (empty($unitIds)) {
            return [];
        }
        $unitIds = array_values(array_map('intval', array_unique($unitIds)));
        $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
        $sql = "SELECT DISTINCT us.* FROM users us 
                INNER JOIN unit_user uu ON us.id = uu.user_id 
                WHERE uu.unit_id IN ({$placeholders}) AND us.status = 'active'";
        if ($orderBy) {
            $sql .= " ORDER BY us." . $orderBy;
        }
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('i', count($unitIds));
        $stmt->bind_param($types, ...$unitIds);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get users by array of IDs (e.g. for service detail present/absent lists).
     *
     * @param array $ids User IDs
     * @param string $orderBy e.g. 'last_name ASC, first_name ASC'
     * @return array
     */
    public function getUsersByIds(array $ids, $orderBy = 'last_name ASC, first_name ASC') {
        if (empty($ids)) {
            return [];
        }
        $ids = array_map('intval', array_unique($ids));
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM users WHERE id IN ({$placeholders})";
        if ($orderBy) {
            $sql .= " ORDER BY " . $orderBy;
        }
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('i', count($ids));
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get user's full name
     */
    public function getFullName($userId) {
        $user = $this->find($userId);
        if (!$user) {
            return '';
        }
        return trim($user['first_name'] . ' ' . $user['last_name']);
    }

    /**
     * Calculate engagement score for a user based on attendance, activity, and participation
     * Returns a score from 0-100
     */
    public function getEngagementScore($userId) {
        $score = 0;
        
        try {
            $db = $this->db;
            
            // Attendance factor (40% of score)
            // Count present attendances in last 90 days
            $stmt = $db->prepare("
                SELECT COUNT(*) as count 
                FROM attendance 
                WHERE user_id = ? 
                AND status = 'present'
                AND event_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $attendanceCount = $result['count'] ?? 0;
            // Scale: 0-20 attendances = 0-40 points
            $score += min($attendanceCount * 2, 40);
            
            // Report submission factor (20% of score)
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM reports WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $reportCount = $result['count'] ?? 0;
            $score += min($reportCount * 2, 20); // Max 20 points for reports
            
            // Recent activity factor (20% of score)
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM activity_logs WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $recentActivity = $result['count'] ?? 0;
            $score += min($recentActivity, 20); // Max 20 points for recent activity
            
            // Unit participation factor (20% of score)
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM unit_user WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $unitCount = $result['count'] ?? 0;
            $score += min($unitCount * 5, 20); // Max 20 points for unit participation
            
        } catch (\Exception $e) {
            error_log('Error calculating engagement score: ' . $e->getMessage());
        }
        
        return min(round($score), 100); // Cap at 100
    }

    /**
     * Get AI-generated insights and recommendations for a user
     */
    public function getAIInsights($userId) {
        $insights = [];
        $score = $this->getEngagementScore($userId);
        
        // Get user data for insights
        $user = $this->find($userId);
        $units = $this->getUnits($userId);
        $directorUnits = $this->getDirectorUnits($userId);
        
        // Check recent attendance
        $db = $this->db;
        $stmt = $db->prepare("
            SELECT COUNT(*) as recent_attendance 
            FROM attendance 
            WHERE user_id = ? 
            AND status = 'present' 
            AND event_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $recentAttendance = $result['recent_attendance'] ?? 0;
        
        // Generate insights based on engagement score
        if ($score >= 75) {
            $insights[] = [
                'type' => 'success',
                'icon' => 'trending-up',
                'title' => 'High Engagement',
                'message' => 'This member shows excellent engagement with ' . $recentAttendance . ' recent attendances.'
            ];
        } elseif ($score >= 40) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'alert-circle',
                'title' => 'Moderate Engagement',
                'message' => 'Consider reaching out to improve participation.'
            ];
        } else {
            $insights[] = [
                'type' => 'danger',
                'icon' => 'alert-triangle',
                'title' => 'Low Engagement',
                'message' => 'This member may need additional support and encouragement.'
            ];
        }
        
        // Unit participation insights
        if (empty($units) && empty($directorUnits)) {
            $insights[] = [
                'type' => 'info',
                'icon' => 'users',
                'title' => 'No Unit Membership',
                'message' => 'Consider assigning this member to a unit to increase engagement.'
            ];
        }
        
        // Recommendations
        $recommendations = [];
        if ($score < 50) {
            $recommendations[] = 'Schedule a follow-up meeting to understand their needs';
            $recommendations[] = 'Invite them to upcoming events and activities';
        }
        if (empty($units)) {
            $recommendations[] = 'Assign to a unit that matches their interests';
        }
        if ($recentAttendance < 2) {
            $recommendations[] = 'Send a personal invitation to the next service';
        }
        
        return [
            'insights' => $insights,
            'recommendations' => $recommendations,
            'score' => $score
        ];
    }
}

