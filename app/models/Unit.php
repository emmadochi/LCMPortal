<?php
namespace App\Models;

class Unit extends BaseModel {
    protected $table = 'units';
    protected $fillable = ['name', 'description', 'status'];

    /**
     * Get unit members
     */
    public function getMembers($unitId) {
        $sql = "SELECT u.*, uu.role, uu.joined_at 
                FROM users u 
                INNER JOIN unit_user uu ON u.id = uu.user_id 
                WHERE uu.unit_id = ? 
                ORDER BY u.first_name, u.last_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $unitId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get unit directors
     */
    public function getDirectors($unitId) {
        $sql = "SELECT u.*, ud.assigned_at 
                FROM users u 
                INNER JOIN unit_directors ud ON u.id = ud.user_id 
                WHERE ud.unit_id = ? 
                ORDER BY u.first_name, u.last_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $unitId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Assign member to unit
     */
    public function assignMember($unitId, $userId, $role = 'member') {
        // Check if already assigned
        $check = $this->db->prepare("SELECT id FROM unit_user WHERE user_id = ? AND unit_id = ?");
        $check->bind_param("ii", $userId, $unitId);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            return false; // Already assigned
        }

        $sql = "INSERT INTO unit_user (unit_id, user_id, role) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iis", $unitId, $userId, $role);
        return $stmt->execute();
    }

    /**
     * Remove member from unit
     */
    public function removeMember($unitId, $userId) {
        $sql = "DELETE FROM unit_user WHERE unit_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $unitId, $userId);
        return $stmt->execute();
    }

    /**
     * Assign director to unit
     */
    public function assignDirector($unitId, $userId) {
        // Check if already assigned
        $check = $this->db->prepare("SELECT id FROM unit_directors WHERE user_id = ? AND unit_id = ?");
        $check->bind_param("ii", $userId, $unitId);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            return false; // Already assigned
        }

        $sql = "INSERT INTO unit_directors (unit_id, user_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $unitId, $userId);
        return $stmt->execute();
    }

    /**
     * Remove director from unit
     */
    public function removeDirector($unitId, $userId) {
        $sql = "DELETE FROM unit_directors WHERE unit_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $unitId, $userId);
        return $stmt->execute();
    }

    /**
     * Get all units (active and inactive), e.g. for church unit assignment dropdown
     */
    public function getAllUnits() {
        return $this->findAll([], 'name ASC');
    }

    /**
     * Get all active units
     */
    public function getActiveUnits() {
        return $this->findAll(['status' => 'active'], 'name ASC');
    }

    /**
     * Get unit statistics
     */
    public function getStatistics($unitId) {
        $stats = [
            'members_count' => 0,
            'directors_count' => 0,
            'reports_count' => 0,
            'attendance_count' => 0
        ];

        // Count members
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM unit_user WHERE unit_id = ?");
        $stmt->bind_param("i", $unitId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stats['members_count'] = $result['count'] ?? 0;

        // Count directors
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM unit_directors WHERE unit_id = ?");
        $stmt->bind_param("i", $unitId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stats['directors_count'] = $result['count'] ?? 0;

        // Count reports (if table exists)
        try {
            $result = $this->db->query("SHOW TABLES LIKE 'reports'");
            if ($result && $result->num_rows > 0) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM reports WHERE unit_id = ?");
                $stmt->bind_param("i", $unitId);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $stats['reports_count'] = $result['count'] ?? 0;
            }
        } catch (\Exception $e) {
            // Table doesn't exist yet
        }

        // Count attendance records (if table exists)
        try {
            $result = $this->db->query("SHOW TABLES LIKE 'attendance'");
            if ($result && $result->num_rows > 0) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM attendance WHERE unit_id = ?");
                $stmt->bind_param("i", $unitId);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $stats['attendance_count'] = $result['count'] ?? 0;
            }
        } catch (\Exception $e) {
            // Table doesn't exist yet
        }

        return $stats;
    }
}

