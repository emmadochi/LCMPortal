<?php
namespace App\Models;

class Church extends BaseModel {
    protected $table = 'churches';
    protected $fillable = [
        'name', 'description', 'address', 'city', 'state', 'postal_code',
        'country', 'phone', 'email', 'website', 'established_date',
        'pastor_user_id', 'status', 'is_headquarters', 'created_by'
    ];

    /**
     * Get all churches with optional filters
     */
    public function getChurches($conditions = []) {
        $sql = "SELECT c.*, u.first_name as creator_first_name, u.last_name as creator_last_name,
                       CONCAT(pastor.first_name, ' ', pastor.last_name) as pastor_name,
                       (SELECT COUNT(*) FROM church_units cu WHERE cu.church_id = c.id) as unit_count,
                       (SELECT COUNT(*) FROM users u WHERE u.church_id = c.id) as member_count
                FROM churches c
                LEFT JOIN users u ON c.created_by = u.id
                LEFT JOIN users pastor ON c.pastor_user_id = pastor.id";
        
        $params = [];
        $types = "";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                if ($field === 'search') {
                    $where[] = "(c.name LIKE ? OR c.city LIKE ? OR c.state LIKE ?)";
                    $params[] = "%{$value}%";
                    $params[] = "%{$value}%";
                    $params[] = "%{$value}%";
                    $types .= "sss";
                } else {
                    $where[] = "c.{$field} = ?";
                    $params[] = $value;
                    $types .= is_int($value) ? "i" : "s";
                }
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY c.name ASC";
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get church by ID with details
     */
    public function getChurchWithDetails($id) {
        $sql = "SELECT c.*, u.first_name as creator_first_name, u.last_name as creator_last_name,
                       pastor.id as pastor_user_id, CONCAT(pastor.first_name, ' ', pastor.last_name) as pastor_name
                FROM churches c
                LEFT JOIN users u ON c.created_by = u.id
                LEFT JOIN users pastor ON c.pastor_user_id = pastor.id
                WHERE c.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get units associated with a church
     */
    public function getChurchUnits($churchId) {
        $sql = "SELECT cu.*, u.id as id, u.id as unit_id, u.name as name, u.name as unit_name, u.description as unit_description,
                       usr.first_name as assigner_first_name, usr.last_name as assigner_last_name,
                       uh.first_name as head_first_name, uh.last_name as head_last_name,
                       CONCAT(uh.first_name, ' ', uh.last_name) as unit_head_name
                FROM church_units cu
                JOIN units u ON cu.unit_id = u.id
                LEFT JOIN users usr ON cu.assigned_by = usr.id
                LEFT JOIN users uh ON cu.unit_head_user_id = uh.id
                WHERE cu.church_id = ?
                ORDER BY cu.is_primary DESC, u.name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $churchId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Assign Unit Head for a church-unit pair
     */
    public function assignUnitHead($churchId, $unitId, $userId) {
        $sql = "UPDATE church_units SET unit_head_user_id = ? WHERE church_id = ? AND unit_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iii", $userId, $churchId, $unitId);
        return $stmt->execute();
    }

    /**
     * Remove Unit Head for a church-unit pair
     */
    public function removeUnitHead($churchId, $unitId) {
        $sql = "UPDATE church_units SET unit_head_user_id = NULL WHERE church_id = ? AND unit_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $churchId, $unitId);
        return $stmt->execute();
    }

    /**
     * Get array of unit IDs associated with a church (for filtering records by church)
     */
    public function getChurchUnitIds($churchId) {
        $sql = "SELECT unit_id FROM church_units WHERE church_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $churchId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return array_column($rows, 'unit_id');
    }

    public function getChurchMemberUsers($churchId) {
        return $this->getAllChurchCongregation($churchId);
    }

    /**
     * Get all active members of a church across direct church assignments and all its units
     */
    public function getAllChurchCongregation($churchId = null) {
        if ($churchId) {
            $unitIds = $this->getChurchUnitIds($churchId);
            $unitPlaceholders = !empty($unitIds) ? implode(',', array_fill(0, count($unitIds), '?')) : '0';
            
            $sql = "SELECT DISTINCT u.*, CONCAT(u.first_name, ' ', u.last_name) as full_name 
                    FROM users u 
                    LEFT JOIN unit_user uu ON u.id = uu.user_id 
                    LEFT JOIN unit_directors ud ON u.id = ud.user_id 
                    WHERE (u.church_id = ? " . (!empty($unitIds) ? "OR uu.unit_id IN ({$unitPlaceholders}) OR ud.unit_id IN ({$unitPlaceholders})" : "") . ")
                    AND u.status = 'active' 
                    ORDER BY u.first_name ASC, u.last_name ASC";
            
            $stmt = $this->db->prepare($sql);
            $types = 'i';
            $params = [$churchId];
            if (!empty($unitIds)) {
                $types .= str_repeat('i', count($unitIds) * 2);
                $params = array_merge($params, $unitIds, $unitIds);
            }
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $stmt = $this->db->prepare("SELECT *, CONCAT(first_name, ' ', last_name) as full_name FROM users WHERE status = 'active' ORDER BY first_name ASC, last_name ASC");
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
    }

    /**
     * Get church members filtered by unit_user role (officer, secretary, treasurer = officers/leaders)
     *
     * @param int $churchId
     * @param array $unitRoles e.g. ['officer','secretary','treasurer']
     * @return array
     */
    public function getChurchMembersByUnitRole($churchId, array $unitRoles) {
        if (empty($unitRoles)) {
            return [];
        }
        $unitIds = $this->getChurchUnitIds($churchId);
        if (empty($unitIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
        $rolePlaceholders = implode(',', array_fill(0, count($unitRoles), '?'));
        $sql = "SELECT DISTINCT u.* FROM users u 
                INNER JOIN unit_user uu ON u.id = uu.user_id 
                WHERE uu.unit_id IN ({$placeholders}) AND uu.role IN ({$rolePlaceholders}) AND u.status = 'active'
                ORDER BY u.last_name ASC, u.first_name ASC";
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('i', count($unitIds)) . str_repeat('s', count($unitRoles));
        $params = array_merge($unitIds, $unitRoles);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get user IDs who are unit directors for any of this church's units (unit heads)
     */
    public function getChurchUnitDirectorUserIds($churchId) {
        $unitIds = $this->getChurchUnitIds($churchId);
        if (empty($unitIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
        $sql = "SELECT DISTINCT ud.user_id FROM unit_directors ud 
                INNER JOIN users u ON ud.user_id = u.id 
                WHERE ud.unit_id IN ({$placeholders}) AND u.status = 'active' AND u.church_id = ?";
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('i', count($unitIds)) . 'i';
        $params = array_merge($unitIds, [$churchId]);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return array_values(array_unique(array_map('intval', array_column($rows, 'user_id'))));
    }

    /**
     * Get distinct church IDs for the given unit IDs.
     * Useful to infer a user's church from unit membership.
     *
     * @param array $unitIds
     * @return int[]
     */
    public function getChurchIdsByUnitIds(array $unitIds) {
        $unitIds = array_values(array_filter(array_map('intval', $unitIds), function ($v) { return $v > 0; }));
        if (empty($unitIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
        $sql = "SELECT DISTINCT church_id FROM church_units WHERE unit_id IN ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('i', count($unitIds));
        $stmt->bind_param($types, ...$unitIds);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return array_values(array_map('intval', array_column($rows, 'church_id')));
    }

    /**
     * Get the church ID for a single unit (first association if unit belongs to multiple churches).
     *
     * @param int $unitId
     * @return int|null
     */
    public function getChurchIdForUnit($unitId) {
        $ids = $this->getChurchIdsByUnitIds([(int) $unitId]);
        return $ids ? (int) $ids[0] : null;
    }

    /**
     * Get membership statistics for the church (total, engagement bands, leaders, unit coordinators).
     * Engagement is based on present attendances in church units in the last 90 days:
     * inactive = 0, partially_active = 1-4, active = 5+.
     *
     * @param int $churchId
     * @return array{total_members: int, active_count: int, partially_active_count: int, inactive_count: int, leaders_count: int, unit_coordinators_count: int}
     */
    public function getMembershipStats($churchId) {
        $unitIds = $this->getChurchUnitIds($churchId);
        $stats = [
            'total_members' => 0,
            'active_count' => 0,
            'partially_active_count' => 0,
            'inactive_count' => 0,
            'leaders_count' => 0,
            'unit_coordinators_count' => 0,
        ];

        // Total distinct members (where users.church_id = ? and role != 'admin')
        $sql = "SELECT COUNT(*) AS total FROM users WHERE church_id = ? AND role != 'admin'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $churchId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stats['total_members'] = (int)($row['total'] ?? 0);

        // Engagement bands: present attendances in church units, last 90 days
        $placeholders = empty($unitIds) ? 'NULL' : implode(',', array_fill(0, count($unitIds), '?'));
        $types = empty($unitIds) ? '' : str_repeat('i', count($unitIds));

        $sql = "SELECT 
                    CASE 
                        WHEN COALESCE(att.present_count, 0) = 0 THEN 'inactive'
                        WHEN COALESCE(att.present_count, 0) BETWEEN 1 AND 4 THEN 'partially_active'
                        ELSE 'active'
                    END AS band,
                    COUNT(*) AS cnt
                FROM users u
                LEFT JOIN (
                    SELECT user_id, COUNT(*) AS present_count FROM attendance 
                    WHERE unit_id IN ({$placeholders}) AND status = 'present' 
                    AND event_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
                    GROUP BY user_id
                ) att ON u.id = att.user_id
                WHERE u.church_id = ? AND u.role != 'admin'
                GROUP BY band";
        
        $params = empty($unitIds) ? [] : $unitIds;
        $params[] = $churchId;
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types . 'i', ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if ($row['band'] === 'active') {
                $stats['active_count'] = (int)$row['cnt'];
            } elseif ($row['band'] === 'partially_active') {
                $stats['partially_active_count'] = (int)$row['cnt'];
            } else {
                $stats['inactive_count'] = (int)$row['cnt'];
            }
        }

        // Leaders: system role director/pastor/officer OR unit_user role officer/secretary/treasurer in this church's units
        $sql = "SELECT COUNT(DISTINCT u.id) AS cnt FROM users u
                LEFT JOIN unit_user uu ON u.id = uu.user_id
                WHERE u.church_id = ? AND u.status = 'active' AND u.role != 'admin'
                AND (u.role IN ('director','pastor','officer') OR uu.role IN ('officer','secretary','treasurer'))";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $churchId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stats['leaders_count'] = (int)($row['cnt'] ?? 0);

        // Unit coordinators (unit directors for this church's units)
        $coordinatorIds = $this->getChurchUnitDirectorUserIds($churchId);
        $stats['unit_coordinators_count'] = count($coordinatorIds);

        return $stats;
    }

    /**
     * Get paginated church members for the membership dashboard with optional filters.
     * Each row includes engagement_band (active/partially_active/inactive) from last 90 days attendance.
     *
     * @param int $churchId
     * @param array $filters ['unit_id' => int, 'engagement' => string, 'role' => string, 'search' => string]
     * @param int $page
     * @param int $perPage
     * @return array{data: array, total: int, current_page: int, per_page: int, total_pages: int}
     */
    public function getMembersForDashboard($churchId, array $filters = [], $page = 1, $perPage = 20) {
        $unitIds = $this->getChurchUnitIds($churchId);
        $placeholders = empty($unitIds) ? 'NULL' : implode(',', array_fill(0, count($unitIds), '?'));
        $types = empty($unitIds) ? '' : str_repeat('i', count($unitIds));
        
        $baseFrom = " FROM users u
        LEFT JOIN (
            SELECT user_id, COUNT(*) AS present_count FROM attendance 
            WHERE unit_id IN ({$placeholders}) AND status = 'present' 
            AND event_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
            GROUP BY user_id
        ) att ON u.id = att.user_id";

        $params = empty($unitIds) ? [] : $unitIds;
        $paramTypes = $types;

        $where = ["u.church_id = ?", "u.role != 'admin'"];
        $params[] = $churchId;
        $paramTypes .= 'i';

        if (!empty($filters['unit_id'])) {
            $where[] = "u.id IN (SELECT user_id FROM unit_user WHERE unit_id = ?)";
            $params[] = (int)$filters['unit_id'];
            $paramTypes .= 'i';
        }
        if (!empty($filters['engagement'])) {
            $band = $filters['engagement'];
            if ($band === 'active') {
                $where[] = "COALESCE(att.present_count, 0) >= 5";
            } elseif ($band === 'partially_active') {
                $where[] = "COALESCE(att.present_count, 0) BETWEEN 1 AND 4";
            } elseif ($band === 'inactive') {
                $where[] = "COALESCE(att.present_count, 0) = 0";
            }
        }
        if (!empty($filters['role'])) {
            $role = $this->db->real_escape_string($filters['role']);
            if (in_array($role, ['admin', 'director', 'officer', 'pastor', 'user'], true)) {
                $where[] = "u.role = ?";
                $params[] = $role;
                $paramTypes .= 's';
            }
        }
        if (!empty($filters['search'])) {
            $where[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
            $q = '%' . $this->db->real_escape_string($filters['search']) . '%';
            $params[] = $q;
            $params[] = $q;
            $params[] = $q;
            $paramTypes .= 'sss';
        }

        $whereClause = implode(' AND ', $where);

        // Count total
        $sqlCount = "SELECT COUNT(DISTINCT u.id) AS total " . $baseFrom . " WHERE " . $whereClause;
        $stmt = $this->db->prepare($sqlCount);
        $stmt->bind_param($paramTypes, ...$params);
        $stmt->execute();
        $total = (int)$stmt->get_result()->fetch_assoc()['total'];
        $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 0;
        $offset = ($page - 1) * $perPage;

        // Select page of members with engagement band and basic fields
        $select = "SELECT DISTINCT u.id, u.first_name, u.last_name, u.email, u.role AS system_role,
            CASE 
                WHEN COALESCE(att.present_count, 0) >= 5 THEN 'active'
                WHEN COALESCE(att.present_count, 0) BETWEEN 1 AND 4 THEN 'partially_active'
                ELSE 'inactive'
            END AS engagement_band";
        $sqlData = $select . $baseFrom . " WHERE " . $whereClause . " ORDER BY u.last_name ASC, u.first_name ASC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
        $stmt = $this->db->prepare($sqlData);
        $stmt->bind_param($paramTypes, ...$params);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Enrich with unit names and unit roles for display (batch per page)
        if (!empty($data) && !empty($unitIds)) {
            $userIds = array_column($data, 'id');
            $userIdsPlaceholders = implode(',', array_fill(0, count($userIds), '?'));
            $unitIdsPlaceholders2 = implode(',', array_fill(0, count($unitIds), '?'));
            $sqlUnits = "SELECT uu.user_id, u.name AS unit_name, uu.role AS unit_role 
                FROM unit_user uu 
                INNER JOIN units u ON u.id = uu.unit_id 
                WHERE uu.user_id IN ({$userIdsPlaceholders}) AND uu.unit_id IN ({$unitIdsPlaceholders2})
                ORDER BY uu.user_id, u.name";
            $stmtU = $this->db->prepare($sqlUnits);
            $stmtU->bind_param(str_repeat('i', count($userIds)) . $types, ...array_merge($userIds, $unitIds));
            $stmtU->execute();
            $unitRows = $stmtU->get_result()->fetch_all(MYSQLI_ASSOC);
            $byUser = [];
            foreach ($unitRows as $r) {
                $byUser[$r['user_id']][] = $r['unit_name'] . ($r['unit_role'] && $r['unit_role'] !== 'member' ? ' (' . $r['unit_role'] . ')' : '');
            }
            foreach ($data as &$row) {
                $row['units_display'] = isset($byUser[$row['id']]) ? implode(', ', $byUser[$row['id']]) : '';
            }
            unset($row);
        } else {
            foreach ($data as &$row) {
                $row['units_display'] = '';
            }
            unset($row);
        }

        return [
            'data' => $data,
            'total' => $total,
            'current_page' => (int)$page,
            'per_page' => (int)$perPage,
            'total_pages' => $totalPages,
        ];
    }

    /**
     * Assign unit to church
     */
    public function assignUnit($churchId, $unitId, $assignedBy, $isPrimary = false, $notes = null) {
        // Check if already assigned
        $sql = "SELECT id FROM church_units WHERE church_id = ? AND unit_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $churchId, $unitId);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            return false; // Already assigned
        }

        $sql = "INSERT INTO church_units (church_id, unit_id, assigned_by, is_primary, notes) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiiss", $churchId, $unitId, $assignedBy, $isPrimary, $notes);
        return $stmt->execute();
    }

    /**
     * Remove unit from church
     */
    public function removeUnit($churchId, $unitId) {
        $sql = "DELETE FROM church_units WHERE church_id = ? AND unit_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $churchId, $unitId);
        return $stmt->execute();
    }

    /**
     * Get churches by status
     */
    public function getChurchesByStatus($status = 'active') {
        return $this->getChurches(['status' => $status]);
    }

    /**
     * Get churches by location (state/city)
     */
    public function getChurchesByLocation($state = null, $city = null) {
        $conditions = [];
        if ($state) $conditions['state'] = $state;
        if ($city) $conditions['city'] = $city;
        return $this->getChurches($conditions);
    }

    /**
     * Get church statistics
     */
    public function getStatistics() {
        $stats = [];
        
        // Total churches
        $sql = "SELECT COUNT(*) as total FROM churches";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['total_churches'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Churches by status
        $sql = "SELECT status, COUNT(*) as count FROM churches GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $statusResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stats['by_status'] = [];
        foreach ($statusResults as $row) {
            $stats['by_status'][$row['status']] = $row['count'];
        }
        
        // Headquarters church
        $sql = "SELECT COUNT(*) as headquarters FROM churches WHERE is_headquarters = TRUE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['headquarters'] = $stmt->get_result()->fetch_assoc()['headquarters'];
        
        // Churches by denomination
        $sql = "SELECT denomination, COUNT(*) as count FROM churches 
                WHERE denomination IS NOT NULL AND denomination != '' 
                GROUP BY denomination ORDER BY count DESC LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['by_denomination'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        return $stats;
    }

    /**
     * Get denomination options
     */
    public function getDenominations() {
        return [
            'Baptist' => 'Baptist',
            'Methodist' => 'Methodist',
            'Presbyterian' => 'Presbyterian',
            'Lutheran' => 'Lutheran',
            'Catholic' => 'Catholic',
            'Anglican' => 'Anglican',
            'Pentecostal' => 'Pentecostal',
            'Non-Denominational' => 'Non-Denominational',
            'Evangelical' => 'Evangelical',
            'Other' => 'Other'
        ];
    }

    /**
     * Get church statuses
     */
    public function getStatuses() {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended'
        ];
    }

    /**
     * Check if church name already exists
     */
    public function nameExists($name, $excludeId = null) {
        $sql = "SELECT id FROM churches WHERE name = ?";
        $params = [$name];
        $types = "s";
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
            $types .= "i";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ? true : false;
    }

    /**
     * Get detailed church report data
     */
    public function getChurchReport($churchId, $startDate = null, $endDate = null) {
        $report = [];
        
        // Get church details
        $report['church'] = $this->getChurchWithDetails($churchId);
        if (!$report['church']) {
            return false;
        }
        
        // Get units and their data
        $report['units'] = $this->getChurchUnits($churchId);
        
        // Get attendance data for date range
        if ($startDate && $endDate) {
            $sql = "SELECT u.name as unit_name, COUNT(a.id) as attendance_count, 
                           DATE(a.date) as attendance_date
                    FROM church_units cu
                    JOIN units u ON cu.unit_id = u.id
                    LEFT JOIN attendance a ON u.id = a.unit_id 
                        AND a.date BETWEEN ? AND ?
                    WHERE cu.church_id = ?
                    GROUP BY u.id, DATE(a.date)
                    ORDER BY u.name, a.date";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssi", $startDate, $endDate, $churchId);
            $stmt->execute();
            $report['attendance_data'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        // Get financial data
        if ($startDate && $endDate) {
            $sql = "SELECT u.name as unit_name, 
                           SUM(CASE WHEN fr.type = 'income' THEN fr.amount ELSE 0 END) as total_income,
                           SUM(CASE WHEN fr.type = 'expense' THEN fr.amount ELSE 0 END) as total_expense
                    FROM church_units cu
                    JOIN units u ON cu.unit_id = u.id
                    LEFT JOIN finance_records fr ON u.id = fr.unit_id 
                        AND fr.date BETWEEN ? AND ?
                    WHERE cu.church_id = ?
                    GROUP BY u.id
                    ORDER BY u.name";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssi", $startDate, $endDate, $churchId);
            $stmt->execute();
            $report['financial_data'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        return $report;
    }

    /**
     * Export church data to CSV
     */
    public function exportToCSV($conditions = []) {
        $churches = $this->getChurches($conditions);
        
        $csvData = [];
        // Headers
        $csvData[] = ['ID', 'Name', 'Description', 'Address', 'City', 'State', 'Postal Code', 'Country', 'Phone', 'Email', 'Website', 'Established Date', 'Pastor', 'Status', 'Is Headquarters', 'Created By', 'Created At', 'Unit Count'];
        
        // Data rows
        foreach ($churches as $church) {
            $csvData[] = [
                $church['id'],
                $church['name'],
                substr($church['description'] ?? '', 0, 100) . (strlen($church['description'] ?? '') > 100 ? '...' : ''),
                $church['address'] ?? '',
                $church['city'] ?? '',
                $church['state'] ?? '',
                $church['postal_code'] ?? '',
                $church['country'] ?? '',
                $church['phone'] ?? '',
                $church['email'] ?? '',
                $church['website'] ?? '',
                $church['established_date'] ?? '',
                $church['pastor_name'] ?? '',
                $church['status'],
                $church['is_headquarters'] ? 'Yes' : 'No',
                $church['creator_first_name'] . ' ' . $church['creator_last_name'],
                $church['created_at'],
                $church['unit_count'] ?? 0
            ];
        }
        
        return $csvData;
    }

    /**
     * Get churches by denomination statistics
     */
    public function getDenominationStatistics() {
        $sql = "SELECT denomination, COUNT(*) as church_count,
                       COUNT(CASE WHEN status = 'active' THEN 1 END) as active_count,
                       COUNT(CASE WHEN is_headquarters = TRUE THEN 1 END) as headquarters_count
                FROM churches 
                WHERE denomination IS NOT NULL AND denomination != ''
                GROUP BY denomination
                ORDER BY church_count DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get geographic distribution statistics
     */
    public function getGeographicStatistics() {
        $sql = "SELECT state, COUNT(*) as church_count,
                       COUNT(CASE WHEN status = 'active' THEN 1 END) as active_count,
                       COUNT(CASE WHEN is_headquarters = TRUE THEN 1 END) as headquarters_count
                FROM churches 
                WHERE state IS NOT NULL AND state != ''
                GROUP BY state
                ORDER BY church_count DESC
                LIMIT 20";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get a church with its associated units and head pastor information
     */
    public function getChurchWithUnits($churchId)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as unit_name, u.id as unit_id,
                   CONCAT(hp.first_name, ' ', hp.last_name) as head_pastor_full_name,
                   CONCAT(pastor.first_name, ' ', pastor.last_name) as pastor_name,
                   creator.first_name as creator_first_name, creator.last_name as creator_last_name
            FROM churches c
            LEFT JOIN church_units cu ON c.id = cu.church_id
            LEFT JOIN units u ON cu.unit_id = u.id
            LEFT JOIN users hp ON c.head_pastor_user_id = hp.id
            LEFT JOIN users pastor ON c.pastor_user_id = pastor.id
            LEFT JOIN users creator ON c.created_by = creator.id
            WHERE c.id = ?
        ");
        $stmt->bind_param("i", $churchId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $church = null;
        $units = [];
        
        while ($row = $result->fetch_assoc()) {
            if (!$church) {
                $church = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'address' => $row['address'],
                    'city' => $row['city'],
                    'state' => $row['state'],
                    'postal_code' => $row['postal_code'],
                    'country' => $row['country'],
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'website' => $row['website'],
                    'established_date' => $row['established_date'],
                    'pastor_user_id' => $row['pastor_user_id'] ?? null,
                    'pastor_name' => $row['pastor_name'] ?? null,
                    'head_pastor_user_id' => $row['head_pastor_user_id'],
                    'head_pastor_name' => $row['head_pastor_full_name'],
                    'status' => $row['status'],
                    'is_headquarters' => $row['is_headquarters'],
                    'created_by' => $row['created_by'],
                    'creator_first_name' => $row['creator_first_name'] ?? '',
                    'creator_last_name' => $row['creator_last_name'] ?? '',
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at'],
                    'units' => []
                ];
            }
            
            if ($row['unit_id']) {
                $church['units'][] = [
                    'id' => $row['unit_id'],
                    'name' => $row['unit_name']
                ];
            }
        }
        
        return $church;
    }

    /**
     * Assign a head pastor to a church
     */
    public function assignHeadPastor($churchId, $userId)
    {
        $stmt = $this->db->prepare("UPDATE churches SET head_pastor_user_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $userId, $churchId);
        return $stmt->execute();
    }

    /**
     * Remove head pastor assignment from a church
     */
    public function removeHeadPastor($churchId)
    {
        $stmt = $this->db->prepare("UPDATE churches SET head_pastor_user_id = NULL WHERE id = ?");
        $stmt->bind_param("i", $churchId);
        return $stmt->execute();
    }

    /**
     * Get all users who can be assigned as head pastors
     */
    public function getPossibleHeadPastors()
    {
        $stmt = $this->db->prepare("
            SELECT u.id, u.first_name, u.last_name, u.email, u.role,
                   CONCAT(u.first_name, ' ', u.last_name) as full_name
            FROM users u
            WHERE u.status = 'active'
            ORDER BY u.last_name ASC, u.first_name ASC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Check if a user is a head pastor of any church
     */
    public function isHeadPastorOfAnyChurch($userId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM churches WHERE head_pastor_user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    /**
     * Get the church a user is head pastor of
     */
    public function getChurchByHeadPastor($userId)
    {
        $stmt = $this->db->prepare("
            SELECT c.*
            FROM churches c
            WHERE c.head_pastor_user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}