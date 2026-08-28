<?php
namespace App\Models;

use App\Core\Database;

class EvangelismConvert {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->ensureTablesExist();
    }

    private function ensureTablesExist() {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS evangelism_converts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                report_id INT NULL,
                soul_winner_id INT NOT NULL,
                church_id INT NULL,
                full_name VARCHAR(150) NOT NULL,
                phone VARCHAR(50) NULL,
                email VARCHAR(150) NULL,
                address TEXT NULL,
                gender ENUM('male', 'female', 'other') NULL,
                decision_type ENUM('salvation', 'rededication', 'healing_miracle', 'inquiry') DEFAULT 'salvation',
                prayer_requests TEXT NULL,
                status ENUM('new', 'in_followup', 'attending_service', 'baptized_holy_ghost', 'baptized_water', 'discipled', 'inactive') DEFAULT 'new',
                
                -- Spiritual Journey Milestones
                first_contact_done TINYINT(1) DEFAULT 0,
                attended_service TINYINT(1) DEFAULT 0,
                baptized_holy_ghost TINYINT(1) DEFAULT 0,
                baptized_water TINYINT(1) DEFAULT 0,
                foundation_class_enrolled TINYINT(1) DEFAULT 0,
                department_joined VARCHAR(100) NULL,
                
                next_followup_date DATE NULL,
                assigned_mentor_id INT NULL,
                pastoral_notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_soul_winner (soul_winner_id),
                INDEX idx_church (church_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $this->db->query("CREATE TABLE IF NOT EXISTS evangelism_followup_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                convert_id INT NOT NULL,
                user_id INT NOT NULL,
                contact_method ENUM('phone_call', 'whatsapp_sms', 'home_visit', 'church_meeting', 'prayer_session') DEFAULT 'phone_call',
                outcome ENUM('reached_receptive', 'reached_busy', 'not_answering', 'number_invalid', 'attended_service', 'prayer_answered') DEFAULT 'reached_receptive',
                notes TEXT NULL,
                milestone_updated VARCHAR(50) NULL,
                next_action_date DATE NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_convert (convert_id),
                INDEX idx_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $this->db->query("CREATE TABLE IF NOT EXISTS evangelism_pastoral_notes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                pastor_id INT NOT NULL,
                church_id INT NULL,
                message TEXT NOT NULL,
                badge_type VARCHAR(50) DEFAULT 'commendation',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_pastor (user_id),
                INDEX idx_pastor (pastor_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (\Exception $e) {
            error_log("EvangelismConvert ensureTablesExist error: " . $e->getMessage());
        }
    }

    public function getConvertsBySoulWinner($userId, $limit = 100) {
        try {
            $sql = "SELECT c.*, 
                           (SELECT COUNT(*) FROM evangelism_followup_logs f WHERE f.convert_id = c.id) as followup_count,
                           (SELECT MAX(created_at) FROM evangelism_followup_logs f WHERE f.convert_id = c.id) as last_contact_at
                    FROM evangelism_converts c
                    WHERE c.soul_winner_id = ?
                    ORDER BY c.created_at DESC
                    LIMIT ?";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("EvangelismConvert getConvertsBySoulWinner prepare failed: " . $this->db->error);
                return [];
            }
            $stmt->bind_param("ii", $userId, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        } catch (\Exception $e) {
            error_log("EvangelismConvert: Error getting converts: " . $e->getMessage());
            return [];
        }
    }

    public function getConvertById($id) {
        try {
            $sql = "SELECT c.*, u.name as soul_winner_name, u.email as soul_winner_email, ch.name as church_name
                    FROM evangelism_converts c
                    LEFT JOIN users u ON u.id = c.soul_winner_id
                    LEFT JOIN churches ch ON ch.id = c.church_id
                    WHERE c.id = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("EvangelismConvert getConvertById prepare failed: " . $this->db->error);
                return null;
            }
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result ? $result->fetch_assoc() : null;
        } catch (\Exception $e) {
            error_log("EvangelismConvert: Error finding convert: " . $e->getMessage());
            return null;
        }
    }

    public function createConvert($data) {
        try {
            $sql = "INSERT INTO evangelism_converts 
                    (report_id, soul_winner_id, church_id, full_name, phone, email, address, gender, decision_type, prayer_requests, status, next_followup_date)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("EvangelismConvert createConvert prepare failed: " . $this->db->error);
                return false;
            }

            $reportId = !empty($data['report_id']) ? (int)$data['report_id'] : null;
            $soulWinnerId = (int)$data['soul_winner_id'];
            $churchId = !empty($data['church_id']) ? (int)$data['church_id'] : null;
            $fullName = trim($data['full_name']);
            $phone = trim($data['phone'] ?? '');
            $email = trim($data['email'] ?? '');
            $address = trim($data['address'] ?? '');
            $gender = !empty($data['gender']) ? $data['gender'] : null;
            $decisionType = $data['decision_type'] ?? 'salvation';
            $prayerRequests = trim($data['prayer_requests'] ?? '');
            $status = $data['status'] ?? 'new';
            $nextFollowup = !empty($data['next_followup_date']) ? $data['next_followup_date'] : null;

            $stmt->bind_param("iiisssssssss", 
                $reportId, $soulWinnerId, $churchId, $fullName, $phone, $email, $address, $gender, $decisionType, $prayerRequests, $status, $nextFollowup
            );

            if ($stmt->execute()) {
                return $this->db->insert_id ?: true;
            }
            return false;
        } catch (\Exception $e) {
            error_log("EvangelismConvert: Error creating convert: " . $e->getMessage());
            return false;
        }
    }

    public function updateMilestone($convertId, $milestone, $value = 1) {
        try {
            $allowedMilestones = [
                'first_contact_done', 'attended_service', 'baptized_holy_ghost', 
                'baptized_water', 'foundation_class_enrolled', 'department_joined', 'status'
            ];

            if (!in_array($milestone, $allowedMilestones)) {
                return false;
            }

            if ($milestone === 'department_joined' || $milestone === 'status') {
                $sql = "UPDATE evangelism_converts SET {$milestone} = ? WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                if (!$stmt) return false;
                $valStr = (string)$value;
                $stmt->bind_param("si", $valStr, $convertId);
            } else {
                $sql = "UPDATE evangelism_converts SET {$milestone} = ? WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                if (!$stmt) return false;
                $valInt = (int)$value;
                $stmt->bind_param("ii", $valInt, $convertId);
            }

            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("EvangelismConvert: Error updating milestone: " . $e->getMessage());
            return false;
        }
    }

    public function addFollowupLog($convertId, $userId, $data) {
        try {
            $sql = "INSERT INTO evangelism_followup_logs 
                    (convert_id, user_id, contact_method, outcome, notes, milestone_updated, next_action_date)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("EvangelismConvert addFollowupLog prepare failed: " . $this->db->error);
                return false;
            }

            $contactMethod = $data['contact_method'] ?? 'phone_call';
            $outcome = $data['outcome'] ?? 'reached_receptive';
            $notes = trim($data['notes'] ?? '');
            $milestoneUpdated = $data['milestone_updated'] ?? null;
            $nextDate = !empty($data['next_action_date']) ? $data['next_action_date'] : null;

            $stmt->bind_param("iisssss", $convertId, $userId, $contactMethod, $outcome, $notes, $milestoneUpdated, $nextDate);
            
            if ($stmt->execute()) {
                // Update convert status and next follow-up date
                if (!empty($nextDate)) {
                    $this->db->query("UPDATE evangelism_converts SET next_followup_date = '{$nextDate}', status = 'in_followup', first_contact_done = 1 WHERE id = " . (int)$convertId);
                } else {
                    $this->db->query("UPDATE evangelism_converts SET status = 'in_followup', first_contact_done = 1 WHERE id = " . (int)$convertId);
                }
                return true;
            }
            return false;
        } catch (\Exception $e) {
            error_log("EvangelismConvert: Error adding followup: " . $e->getMessage());
            return false;
        }
    }

    public function getFollowupLogs($convertId) {
        try {
            $sql = "SELECT f.*, u.name as logged_by_name
                    FROM evangelism_followup_logs f
                    JOIN users u ON u.id = f.user_id
                    WHERE f.convert_id = ?
                    ORDER BY f.created_at DESC";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("EvangelismConvert getFollowupLogs prepare failed: " . $this->db->error);
                return [];
            }
            $stmt->bind_param("i", $convertId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        } catch (\Exception $e) {
            error_log("EvangelismConvert: Error getting followup logs: " . $e->getMessage());
            return [];
        }
    }

    public function getSoulWinnerCareStats($userId) {
        try {
            $sql = "SELECT 
                        COUNT(id) as total_converts,
                        COALESCE(SUM(first_contact_done), 0) as contacted_count,
                        COALESCE(SUM(attended_service), 0) as attended_church_count,
                        COALESCE(SUM(baptized_holy_ghost), 0) as holy_ghost_baptized_count,
                        COALESCE(SUM(baptized_water), 0) as water_baptized_count,
                        COALESCE(SUM(foundation_class_enrolled), 0) as foundation_enrolled_count,
                        COALESCE(SUM(CASE WHEN department_joined IS NOT NULL AND department_joined != '' THEN 1 ELSE 0 END), 0) as integrated_department_count
                    FROM evangelism_converts
                    WHERE soul_winner_id = ?";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("EvangelismConvert getSoulWinnerCareStats prepare failed: " . $this->db->error);
                return [
                    'total_converts' => 0,
                    'contacted_count' => 0,
                    'attended_church_count' => 0,
                    'holy_ghost_baptized_count' => 0,
                    'water_baptized_count' => 0,
                    'foundation_enrolled_count' => 0,
                    'integrated_department_count' => 0
                ];
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $res = $result ? $result->fetch_assoc() : [];
            
            return [
                'total_converts' => (int)($res['total_converts'] ?? 0),
                'contacted_count' => (int)($res['contacted_count'] ?? 0),
                'attended_church_count' => (int)($res['attended_church_count'] ?? 0),
                'holy_ghost_baptized_count' => (int)($res['holy_ghost_baptized_count'] ?? 0),
                'water_baptized_count' => (int)($res['water_baptized_count'] ?? 0),
                'foundation_enrolled_count' => (int)($res['foundation_enrolled_count'] ?? 0),
                'integrated_department_count' => (int)($res['integrated_department_count'] ?? 0),
            ];
        } catch (\Exception $e) {
            error_log("EvangelismConvert: Error getting care stats: " . $e->getMessage());
            return [
                'total_converts' => 0,
                'contacted_count' => 0,
                'attended_church_count' => 0,
                'holy_ghost_baptized_count' => 0,
                'water_baptized_count' => 0,
                'foundation_enrolled_count' => 0,
                'integrated_department_count' => 0
            ];
        }
    }

    public function addPastoralNote($soulWinnerId, $pastorId, $churchId, $message, $badgeType = 'commendation') {
        try {
            $sql = "INSERT INTO evangelism_pastoral_notes (user_id, pastor_id, church_id, message, badge_type) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("EvangelismConvert addPastoralNote prepare failed: " . $this->db->error);
                return false;
            }
            $stmt->bind_param("iiiss", $soulWinnerId, $pastorId, $churchId, $message, $badgeType);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("EvangelismConvert: Error adding pastoral note: " . $e->getMessage());
            return false;
        }
    }

    public function getPastoralNotes($userId) {
        try {
            $sql = "SELECT p.*, u.name as pastor_name, u.email as pastor_email, ch.name as church_name
                    FROM evangelism_pastoral_notes p
                    JOIN users u ON u.id = p.pastor_id
                    LEFT JOIN churches ch ON ch.id = p.church_id
                    WHERE p.user_id = ?
                    ORDER BY p.created_at DESC";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("EvangelismConvert getPastoralNotes prepare failed: " . $this->db->error);
                return [];
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        } catch (\Exception $e) {
            error_log("EvangelismConvert: Error getting pastoral notes: " . $e->getMessage());
            return [];
        }
    }
}
