<?php
namespace App\Models;

class Membership extends BaseModel {
    protected $table = 'memberships';
    protected $fillable = [
        'user_id', 'unit_id', 'membership_type', 'status', 
        'join_date', 'baptism_date', 'tithe_status', 'engagement_score'
    ];
    
    /**
     * Get membership by user ID
     */
    public function getByUserId($userId) {
        return $this->findAll(['user_id' => $userId]);
    }
    
    /**
     * Get membership by unit ID
     */
    public function getByUnitId($unitId) {
        return $this->findAll(['unit_id' => $unitId]);
    }
    
    /**
     * Get active memberships
     */
    public function getActiveMemberships($unitId = null) {
        $conditions = ['status' => 'active'];
        if ($unitId) {
            $conditions['unit_id'] = $unitId;
        }
        return $this->findAll($conditions);
    }
    
    /**
     * Calculate engagement score for a member
     */
    public function calculateEngagementScore($userId) {
        $score = 0;
        
        try {
            $db = $this->db;
            
            // Attendance factor (40% of score)
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM attendance WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $attendanceCount = $result['count'] ?? 0;
            $score += min($attendanceCount * 2, 40); // Max 40 points for attendance
            
            // Report submission factor (20% of score)
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM reports WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $reportCount = $result['count'] ?? 0;
            $score += min($reportCount * 3, 20); // Max 20 points for reports
            
            // Recent activity factor (20% of score)
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM activity_logs WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $recentActivity = $result['count'] ?? 0;
            $score += min($recentActivity, 20); // Max 20 points for recent activity
            
            // Membership duration factor (20% of score)
            $stmt = $db->prepare("SELECT join_date FROM memberships WHERE user_id = ? AND status = 'active' LIMIT 1");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            if ($result && $result['join_date']) {
                $joinDate = new \DateTime($result['join_date']);
                $now = new \DateTime();
                $interval = $now->diff($joinDate);
                $months = ($interval->y * 12) + $interval->m;
                $score += min($months, 20); // Max 20 points for tenure
            }
            
        } catch (\Exception $e) {
            error_log('Error calculating engagement score: ' . $e->getMessage());
        }
        
        return min($score, 100); // Cap at 100
    }
    
    /**
     * Update engagement score for a member
     */
    public function updateEngagementScore($userId) {
        $score = $this->calculateEngagementScore($userId);
        
        $stmt = $this->db->prepare("UPDATE memberships SET engagement_score = ? WHERE user_id = ?");
        $stmt->bind_param("ii", $score, $userId);
        return $stmt->execute();
    }
    
    /**
     * Get members with low engagement scores
     */
    public function getLowEngagementMembers($threshold = 30, $unitId = null) {
        $sql = "SELECT m.*, u.first_name, u.last_name, u.email 
                FROM memberships m 
                JOIN users u ON m.user_id = u.id 
                WHERE m.engagement_score < ? AND m.status = 'active'";
        
        $params = [$threshold];
        if ($unitId) {
            $sql .= " AND m.unit_id = ?";
            $params[] = $unitId;
        }
        
        $sql .= " ORDER BY m.engagement_score ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(str_repeat('i', count($params)), ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get membership statistics
     */
    public function getMembershipStats($unitId = null) {
        $stats = [
            'total_active' => 0,
            'by_type' => [],
            'by_tithe_status' => [],
            'avg_engagement' => 0
        ];
        
        try {
            $db = $this->db;
            
            // Total active members
            $sql = "SELECT COUNT(*) as count FROM memberships WHERE status = 'active'";
            $params = [];
            if ($unitId) {
                $sql .= " AND unit_id = ?";
                $params[] = $unitId;
            }
            
            $stmt = $db->prepare($sql);
            if ($params) {
                $stmt->bind_param("i", $params[0]);
            }
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stats['total_active'] = $result['count'] ?? 0;
            
            // Members by type
            $sql = "SELECT membership_type, COUNT(*) as count FROM memberships WHERE status = 'active'";
            if ($unitId) {
                $sql .= " AND unit_id = ?";
            }
            $sql .= " GROUP BY membership_type";
            
            $stmt = $db->prepare($sql);
            if ($unitId) {
                $stmt->bind_param("i", $unitId);
            }
            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            foreach ($result as $row) {
                $stats['by_type'][$row['membership_type']] = $row['count'];
            }
            
            // Members by tithe status
            $sql = "SELECT tithe_status, COUNT(*) as count FROM memberships WHERE status = 'active'";
            if ($unitId) {
                $sql .= " AND unit_id = ?";
            }
            $sql .= " GROUP BY tithe_status";
            
            $stmt = $db->prepare($sql);
            if ($unitId) {
                $stmt->bind_param("i", $unitId);
            }
            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            foreach ($result as $row) {
                $stats['by_tithe_status'][$row['tithe_status']] = $row['count'];
            }
            
            // Average engagement score
            $sql = "SELECT AVG(engagement_score) as avg_score FROM memberships WHERE status = 'active'";
            if ($unitId) {
                $sql .= " AND unit_id = ?";
            }
            
            $stmt = $db->prepare($sql);
            if ($unitId) {
                $stmt->bind_param("i", $unitId);
            }
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stats['avg_engagement'] = round($result['avg_score'] ?? 0, 2);
            
        } catch (\Exception $e) {
            error_log('Error getting membership stats: ' . $e->getMessage());
        }
        
        return $stats;
    }
}