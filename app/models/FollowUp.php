<?php
namespace App\Models;

class FollowUp extends BaseModel {
    protected $table = 'follow_ups';
    protected $fillable = [
        'member_id', 'type', 'status', 'due_date', 
        'completed_date', 'assigned_to', 'notes', 'priority'
    ];
    
    /**
     * Get pending follow-ups
     */
    public function getPendingFollowUps($assignedTo = null, $unitId = null) {
        // First check if memberships table exists
        $result = $this->db->query("SHOW TABLES LIKE 'memberships'");
        
        if ($result && $result->num_rows > 0) {
            // Memberships table exists
            $sql = "SELECT f.*, u.first_name, u.last_name, u.email, m.membership_type
                    FROM follow_ups f
                    JOIN users u ON f.member_id = u.id
                    LEFT JOIN memberships m ON f.member_id = m.user_id
                    WHERE f.status = 'pending'";
        } else {
            // Memberships table doesn't exist
            $sql = "SELECT f.*, u.first_name, u.last_name, u.email, NULL as membership_type
                    FROM follow_ups f
                    JOIN users u ON f.member_id = u.id
                    WHERE f.status = 'pending'";
        }
        
        $params = [];
        $types = '';
        
        if ($assignedTo) {
            $sql .= " AND f.assigned_to = ?";
            $params[] = $assignedTo;
            $types .= 'i';
        }
        
        if ($unitId && $result && $result->num_rows > 0) {
            $sql .= " AND m.unit_id = ?";
            $params[] = $unitId;
            $types .= 'i';
        }
        
        $sql .= " ORDER BY f.priority DESC, f.due_date ASC";
        
        $stmt = $this->db->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get overdue follow-ups
     */
    public function getOverdueFollowUps($unitId = null) {
        // First check if memberships table exists
        $result = $this->db->query("SHOW TABLES LIKE 'memberships'");
        
        if ($result && $result->num_rows > 0) {
            // Memberships table exists
            $sql = "SELECT f.*, u.first_name, u.last_name, u.email, m.membership_type
                    FROM follow_ups f
                    JOIN users u ON f.member_id = u.id
                    LEFT JOIN memberships m ON f.member_id = m.user_id
                    WHERE f.status = 'pending' AND f.due_date < CURDATE()";
        } else {
            // Memberships table doesn't exist
            $sql = "SELECT f.*, u.first_name, u.last_name, u.email, NULL as membership_type
                    FROM follow_ups f
                    JOIN users u ON f.member_id = u.id
                    WHERE f.status = 'pending' AND f.due_date < CURDATE()";
        }
        
        $params = [];
        $types = '';
        
        if ($unitId && $result && $result->num_rows > 0) {
            $sql .= " AND m.unit_id = ?";
            $params[] = $unitId;
            $types .= 'i';
        }
        
        $sql .= " ORDER BY f.due_date ASC";
        
        $stmt = $this->db->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get follow-up history for a member
     */
    public function getMemberFollowUpHistory($memberId) {
        return $this->findAll(
            ['member_id' => $memberId], 
            'created_at DESC'
        );
    }
    
    /**
     * Create automated follow-up based on triggers
     */
    public function createAutomatedFollowUp($memberId, $type, $daysFromNow = 7, $priority = 'medium') {
        $dueDate = date('Y-m-d', strtotime("+$daysFromNow days"));
        
        $data = [
            'member_id' => $memberId,
            'type' => $type,
            'status' => 'pending',
            'due_date' => $dueDate,
            'priority' => $priority,
            'notes' => 'Automatically generated follow-up'
        ];
        
        return $this->create($data);
    }
    
    /**
     * Mark follow-up as completed
     */
    public function markCompleted($id, $notes = null) {
        $data = [
            'status' => 'completed',
            'completed_date' => date('Y-m-d H:i:s')
        ];
        
        if ($notes) {
            $data['notes'] = $notes;
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Get follow-up statistics
     */
    public function getFollowUpStats($unitId = null) {
        $stats = [
            'total_pending' => 0,
            'total_overdue' => 0,
            'total_completed' => 0,
            'by_priority' => [],
            'by_type' => []
        ];
        
        try {
            $db = $this->db;
            
            // Get stats by status
            $statuses = ['pending', 'overdue', 'completed'];
            foreach ($statuses as $status) {
                $sql = "SELECT COUNT(*) as count FROM follow_ups f";
                $params = [];
                $types = '';
                
                if ($status === 'overdue') {
                    $sql .= " WHERE f.status = 'pending' AND f.due_date < CURDATE()";
                } else {
                    $sql .= " WHERE f.status = ?";
                    $params[] = $status;
                    $types .= 's';
                }
                
                if ($unitId) {
                    $sql .= " AND f.member_id IN (SELECT user_id FROM memberships WHERE unit_id = ?)";
                    $params[] = $unitId;
                    $types .= 'i';
                }
                
                $stmt = $db->prepare($sql);
                if ($params) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $stats["total_$status"] = $result['count'] ?? 0;
            }
            
            // Get stats by priority
            $priorities = ['low', 'medium', 'high', 'urgent'];
            foreach ($priorities as $priority) {
                $sql = "SELECT COUNT(*) as count FROM follow_ups f WHERE f.priority = ?";
                $params = [$priority];
                $types = 's';
                
                if ($unitId) {
                    $sql .= " AND f.member_id IN (SELECT user_id FROM memberships WHERE unit_id = ?)";
                    $params[] = $unitId;
                    $types .= 'i';
                }
                
                $stmt = $db->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $stats['by_priority'][$priority] = $result['count'] ?? 0;
            }
            
            // Get stats by type
            $typesList = ['new_convert', 'prayer_request', 'counseling', 'visitation', 'general'];
            foreach ($typesList as $type) {
                $sql = "SELECT COUNT(*) as count FROM follow_ups f WHERE f.type = ?";
                $params = [$type];
                $types = 's';
                
                if ($unitId) {
                    $sql .= " AND f.member_id IN (SELECT user_id FROM memberships WHERE unit_id = ?)";
                    $params[] = $unitId;
                    $types .= 'i';
                }
                
                $stmt = $db->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $stats['by_type'][$type] = $result['count'] ?? 0;
            }
            
        } catch (\Exception $e) {
            error_log('Error getting follow-up stats: ' . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Get follow-ups due today
     */
    public function getDueToday($assignedTo = null) {
        $sql = "SELECT f.*, u.first_name, u.last_name, u.email
                FROM follow_ups f
                JOIN users u ON f.member_id = u.id
                WHERE f.status = 'pending' AND DATE(f.due_date) = CURDATE()";
        
        $params = [];
        $types = '';
        
        if ($assignedTo) {
            $sql .= " AND f.assigned_to = ?";
            $params[] = $assignedTo;
            $types .= 'i';
        }
        
        $sql .= " ORDER BY f.priority DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Auto-generate follow-ups based on member behavior
     */
    public function generateBehavioralFollowUps() {
        try {
            $db = $this->db;
            
            // Get members who haven't attended in 30 days
            $sql = "SELECT DISTINCT u.id, u.first_name, u.last_name
                    FROM users u
                    JOIN memberships m ON u.id = m.user_id
                    LEFT JOIN attendance a ON u.id = a.user_id AND a.event_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    WHERE m.status = 'active' AND a.id IS NULL";
            
            $result = $db->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    // Check if follow-up already exists
                    $checkStmt = $db->prepare("SELECT id FROM follow_ups WHERE member_id = ? AND type = 'visitation' AND status = 'pending'");
                    $checkStmt->bind_param("i", $row['id']);
                    $checkStmt->execute();
                    
                    if ($checkStmt->get_result()->num_rows === 0) {
                        $this->createAutomatedFollowUp($row['id'], 'visitation', 3, 'high');
                    }
                }
            }
            
            // Get new converts (joined in last 30 days)
            $sql = "SELECT u.id, u.first_name, u.last_name
                    FROM users u
                    JOIN memberships m ON u.id = m.user_id
                    WHERE m.join_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                    AND m.membership_type = 'member'";
            
            $result = $db->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    // Check if follow-up already exists
                    $checkStmt = $db->prepare("SELECT id FROM follow_ups WHERE member_id = ? AND type = 'new_convert' AND status = 'pending'");
                    $checkStmt->bind_param("i", $row['id']);
                    $checkStmt->execute();
                    
                    if ($checkStmt->get_result()->num_rows === 0) {
                        $this->createAutomatedFollowUp($row['id'], 'new_convert', 7, 'medium');
                    }
                }
            }
            
        } catch (\Exception $e) {
            error_log('Error generating behavioral follow-ups: ' . $e->getMessage());
        }
    }
    
    /**
     * Get common follow-up types
     */
    public function getFollowUpTypes() {
        return [
            'new_member_welcome',
            'engagement_check',
            'prayer_request',
            'discipleship',
            'ministry_invitation',
            'visitation',
            'new_convert',
            'new_visitor',
            'encouragement',
            'feedback_request'
        ];
    }
    
    /**
     * Get follow-ups with member details
     */
    public function getFollowUpsWithDetails($filters = []) {
        $followUps = [];
        
        try {
            $db = $this->db;
            
            $sql = "SELECT f.*, u.first_name, u.last_name, u.email,
                           assigned.first_name as assigned_first_name, 
                           assigned.last_name as assigned_last_name
                    FROM follow_ups f
                    JOIN users u ON f.member_id = u.id
                    LEFT JOIN users assigned ON f.assigned_to = assigned.id";
            
            $params = [];
            $types = '';
            
            // Apply filters
            $whereConditions = [];
            
            if (!empty($filters['search'])) {
                $searchTerm = '%' . $filters['search'] . '%';
                $whereConditions[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= 'sss';
            }
            
            if (!empty($filters['status'])) {
                if ($filters['status'] === 'overdue') {
                    $whereConditions[] = "f.status = 'pending' AND f.due_date < CURDATE()";
                } else {
                    $whereConditions[] = "f.status = ?";
                    $params[] = $filters['status'];
                    $types .= 's';
                }
            }
            
            if (!empty($filters['priority'])) {
                $whereConditions[] = "f.priority = ?";
                $params[] = $filters['priority'];
                $types .= 's';
            }
            
            if (!empty($filters['type'])) {
                $whereConditions[] = "f.type = ?";
                $params[] = $filters['type'];
                $types .= 's';
            }
            
            if (!empty($filters['assigned_to'])) {
                if ($filters['assigned_to'] === 'unassigned') {
                    $whereConditions[] = "f.assigned_to IS NULL";
                } else {
                    $whereConditions[] = "f.assigned_to = ?";
                    $params[] = $filters['assigned_to'];
                    $types .= 'i';
                }
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " ORDER BY f.created_at DESC";
            
            $stmt = $db->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $followUps[] = $row;
            }
            
        } catch (\Exception $e) {
            error_log('Error getting follow-ups with details: ' . $e->getMessage());
        }
        
        return $followUps;
    }
    
    /**
     * Get follow-up with member details
     */
    public function getFollowUpWithDetails($id) {
        try {
            $db = $this->db;
            
            $sql = "SELECT f.*, u.first_name, u.last_name, u.email,
                           assigned.first_name as assigned_first_name, 
                           assigned.last_name as assigned_last_name
                    FROM follow_ups f
                    JOIN users u ON f.member_id = u.id
                    LEFT JOIN users assigned ON f.assigned_to = assigned.id
                    WHERE f.id = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_assoc();
            
        } catch (\Exception $e) {
            error_log('Error getting follow-up with details: ' . $e->getMessage());
            return null;
        }
    }
}