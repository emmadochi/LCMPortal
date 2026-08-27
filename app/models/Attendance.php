<?php
namespace App\Models;

class Attendance extends BaseModel {
    protected $table = 'attendance';
    protected $fillable = ['unit_id', 'church_id', 'user_id', 'event_date', 'event_type', 'service_description', 'notes', 'status', 'is_first_timer', 'recorded_by'];

    /**
     * Event types for attendance (service types) with display labels.
     * Keys are stored in DB; values are shown in UI.
     */
    public static function getEventTypes() {
        return [
            'sunday_service'   => 'Sunday Service',
            'mid_week_service' => 'Mid Week Service',
            'workers_meeting'  => 'Workers Meeting',
            'special_service'  => 'Special Service',
            'prayer_meeting'   => 'Prayer Meeting',
            'outreach'         => 'Outreach',
            'training'         => 'Training',
            'other'            => 'Other',
        ];
    }

    /**
     * Get attendance with user and unit details
     */
    public function getAttendanceWithDetails($conditions = [], $orderBy = null) {
        $sql = "SELECT a.*, u.name as unit_name, 
                       us.first_name, us.last_name, us.email as user_email,
                       rb.first_name as recorded_by_first_name, rb.last_name as recorded_by_last_name
                FROM attendance a 
                LEFT JOIN units u ON a.unit_id = u.id 
                LEFT JOIN users us ON a.user_id = us.id
                LEFT JOIN users rb ON a.recorded_by = rb.id
                WHERE a.unit_id IS NOT NULL";
        
        $params = [];
        $types = "";
        
        if (!empty($conditions)) {
            foreach ($conditions as $field => $value) {
                $sql .= " AND a.{$field} = ?";
                $params[] = $value;
                $types .= is_int($value) ? "i" : "s";
            }
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        } else {
            $sql .= " ORDER BY a.event_date DESC, a.created_at DESC";
        }
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get list of services (grouped by unit/church + date + event type) with present/absent counts.
     * Used for the attendance index page (one row per service).
     *
     * @param array $unitIds Optional. When provided (e.g. church filter), only services for these units or church-wide for that church.
     * @param int|null $churchId Optional. When provided with unitIds, include church-wide services (unit_id IS NULL, church_id = churchId).
     */
    public function getServicesWithCounts(array $unitIds = [], $churchId = null) {
        $params = [];
        $types = '';
        $where = '1=1';

        if (!empty($unitIds) || $churchId) {
            $conditions = [];
            if (!empty($unitIds)) {
                $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
                $conditions[] = "a.unit_id IN ({$placeholders})";
                $params = array_merge($params, $unitIds);
                $types .= str_repeat('i', count($unitIds));
            }
            if ($churchId) {
                $conditions[] = "(a.church_id = ? AND a.unit_id IS NULL)";
                $params[] = $churchId;
                $types .= 'i';
            }
            $where = '(' . implode(' OR ', $conditions) . ')';
        }

        $sql = "SELECT 
                    a.unit_id, a.church_id, a.event_date, a.event_type,
                    MAX(a.service_description) AS service_description,
                    u.name AS unit_name,
                    c.name AS church_name,
                    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present_count,
                    SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) AS absent_count
                FROM attendance a
                LEFT JOIN units u ON a.unit_id = u.id
                LEFT JOIN churches c ON a.church_id = c.id
                WHERE {$where}
                GROUP BY a.unit_id, a.church_id, a.event_date, a.event_type
                ORDER BY a.event_date DESC, a.event_type ASC";

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get service detail: present and absent members with user info.
     * For unit service: pass unitId and eventDate, eventType; churchId = null.
     * For church-wide: pass churchId and eventDate, eventType; unitId = null.
     *
     * @return array ['present' => [...], 'absent' => [...], 'event_date', 'event_type', 'scope_label', 'church_id', 'unit_id']
     */
    public function getServiceDetail($unitId, $churchId, $eventDate, $eventType) {
        $params = [$eventDate, $eventType];
        $types = 'ss';
        if ($churchId && !$unitId) {
            $sql = "SELECT a.user_id, a.status, a.service_description FROM attendance a 
                    WHERE a.church_id = ? AND a.unit_id IS NULL AND a.event_date = ? AND a.event_type = ?";
            array_splice($params, 0, 0, [$churchId]);
            $types = 'iss';
        } else {
            $sql = "SELECT a.user_id, a.status, a.service_description FROM attendance a 
                    WHERE a.unit_id = ? AND a.event_date = ? AND a.event_type = ?";
            array_splice($params, 0, 0, [$unitId]);
            $types = 'iss';
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $presentIds = [];
        $absentIds = [];
        $serviceDescription = null;
        foreach ($rows as $row) {
            if ($row['status'] === 'present') {
                $presentIds[] = $row['user_id'];
            } else {
                $absentIds[] = $row['user_id'];
            }
            if (!empty($row['service_description'])) {
                $serviceDescription = $row['service_description'];
            }
        }

        $userModel = new User();
        $present = empty($presentIds) ? [] : $userModel->getUsersByIds($presentIds);
        $absent = empty($absentIds) ? [] : $userModel->getUsersByIds($absentIds);

        return [
            'present' => $present,
            'absent' => $absent,
            'event_date' => $eventDate,
            'event_type' => $eventType,
            'service_description' => $serviceDescription,
            'unit_id' => $unitId,
            'church_id' => $churchId,
        ];
    }

    /**
     * Get existing service description for a service (for pre-filling roll-call form).
     */
    public function getServiceDescriptionForService($unitId, $churchId, $eventDate, $eventType) {
        $params = [$eventDate, $eventType];
        $types = 'ss';
        if ($churchId && !$unitId) {
            $sql = "SELECT service_description FROM attendance WHERE church_id = ? AND unit_id IS NULL AND event_date = ? AND event_type = ? LIMIT 1";
            array_splice($params, 0, 0, [$churchId]);
            $types = 'iss';
        } else {
            $sql = "SELECT service_description FROM attendance WHERE unit_id = ? AND event_date = ? AND event_type = ? LIMIT 1";
            array_splice($params, 0, 0, [$unitId]);
            $types = 'iss';
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row && !empty($row['service_description']) ? $row['service_description'] : '';
    }

    /**
     * Get existing attendance marks for a unit + date + event type (for pre-filling roll-call form).
     * Returns [user_id => ['status' => 'present'|'absent', 'is_first_timer' => 0|1]]
     */
    public function getMarksForService($unitId, $eventDate, $eventType) {
        $sql = "SELECT user_id, status, COALESCE(is_first_timer, 0) AS is_first_timer FROM attendance 
                WHERE unit_id = ? AND event_date = ? AND event_type = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iss", $unitId, $eventDate, $eventType);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $map = [];
        foreach ($rows as $row) {
            $map[$row['user_id']] = [
                'status' => $row['status'],
                'is_first_timer' => (int) $row['is_first_timer'],
            ];
        }
        return $map;
    }

    /**
     * Get existing marks for a church-wide service (church_id + date + event type).
     * Returns [user_id => ['status' => 'present'|'absent', 'is_first_timer' => 0|1]]
     */
    public function getMarksForChurchWideService($churchId, $eventDate, $eventType) {
        if ($churchId) {
            $sql = "SELECT user_id, status, COALESCE(is_first_timer, 0) AS is_first_timer FROM attendance 
                    WHERE church_id = ? AND unit_id IS NULL AND event_date = ? AND event_type = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("iss", $churchId, $eventDate, $eventType);
        } else {
            $sql = "SELECT user_id, status, COALESCE(is_first_timer, 0) AS is_first_timer FROM attendance 
                    WHERE unit_id IS NULL AND event_date = ? AND event_type = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ss", $eventDate, $eventType);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $map = [];
        foreach ($rows as $row) {
            $map[$row['user_id']] = [
                'status' => $row['status'],
                'is_first_timer' => (int) $row['is_first_timer'],
            ];
        }
        return $map;
    }

    /**
     * Delete all attendance records for a given service (unit + date + event type) before re-submitting roll-call
     */
    public function deleteForService($unitId, $eventDate, $eventType) {
        $sql = "DELETE FROM attendance WHERE unit_id = ? AND event_date = ? AND event_type = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iss", $unitId, $eventDate, $eventType);
        return $stmt->execute();
    }

    /**
     * Delete all attendance records for a church-wide service.
     */
    public function deleteForChurchWideService($churchId, $eventDate, $eventType) {
        if ($churchId) {
            $sql = "DELETE FROM attendance WHERE church_id = ? AND unit_id IS NULL AND event_date = ? AND event_type = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("iss", $churchId, $eventDate, $eventType);
        } else {
            $sql = "DELETE FROM attendance WHERE unit_id IS NULL AND event_date = ? AND event_type = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ss", $eventDate, $eventType);
        }
        return $stmt->execute();
    }

    /**
     * Check if this is the user's first time at this church (no prior present attendance at this church before eventDate).
     *
     * @param int $userId
     * @param int|null $churchId
     * @param string $eventDate Y-m-d
     * @return bool
     */
    public function isFirstTimerAtChurch($userId, $churchId, $eventDate) {
        if (!$churchId) {
            return false;
        }
        $sql = "SELECT 1 FROM attendance a 
                WHERE a.user_id = ? AND a.status = 'present' AND a.event_date < ? 
                AND (a.church_id = ? OR (a.church_id IS NULL AND a.unit_id IN (SELECT unit_id FROM church_units WHERE church_id = ?))) 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isii", $userId, $eventDate, $churchId, $churchId);
        $stmt->execute();
        return $stmt->get_result()->num_rows === 0;
    }

    /**
     * Get attendance segment counts for a service: returning/first-timer × adult/child/teen (6 segments).
     * Only counts present. Null age_group treated as 'adult'.
     *
     * @param int|null $unitId For unit service; null for church-wide.
     * @param int|null $churchId For church-wide service; for unit service pass church for consistency.
     * @param string $eventDate
     * @param string $eventType
     * @return array{returning_adults, returning_children, returning_teens, first_timer_adults, first_timer_children, first_timer_teens}
     */
    public function getServiceSegmentCounts($unitId, $churchId, $eventDate, $eventType) {
        $params = [$eventDate, $eventType];
        $types = 'ss';
        if ($churchId && !$unitId) {
            $sql = "SELECT COALESCE(a.is_first_timer, 0) AS is_first_timer, COALESCE(NULLIF(us.age_group,''), 'adult') AS age_group, COUNT(*) AS cnt
                    FROM attendance a
                    INNER JOIN users us ON a.user_id = us.id
                    WHERE a.church_id = ? AND a.unit_id IS NULL AND a.event_date = ? AND a.event_type = ? AND a.status = 'present'
                    GROUP BY COALESCE(a.is_first_timer, 0), COALESCE(NULLIF(us.age_group,''), 'adult')";
            array_splice($params, 0, 0, [$churchId]);
            $types = 'iss';
        } else {
            $sql = "SELECT COALESCE(a.is_first_timer, 0) AS is_first_timer, COALESCE(NULLIF(us.age_group,''), 'adult') AS age_group, COUNT(*) AS cnt
                    FROM attendance a
                    INNER JOIN users us ON a.user_id = us.id
                    WHERE a.unit_id = ? AND a.event_date = ? AND a.event_type = ? AND a.status = 'present'
                    GROUP BY COALESCE(a.is_first_timer, 0), COALESCE(NULLIF(us.age_group,''), 'adult')";
            array_splice($params, 0, 0, [$unitId]);
            $types = 'iss';
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $out = [
            'returning_adults' => 0, 'returning_children' => 0, 'returning_teens' => 0,
            'first_timer_adults' => 0, 'first_timer_children' => 0, 'first_timer_teens' => 0,
        ];
        foreach ($rows as $row) {
            $key = ($row['is_first_timer'] ? 'first_timer_' : 'returning_') . ($row['age_group'] === 'child' ? 'children' : ($row['age_group'] === 'teen' ? 'teens' : 'adults'));
            if (isset($out[$key])) {
                $out[$key] = (int) $row['cnt'];
            }
        }
        return $out;
    }

    /**
     * Get aggregated attendance data for charts by period (weekly, monthly, yearly).
     * Returns array of { label, present, absent, first_timer } in chronological order.
     *
     * @param string $period One of 'weekly', 'monthly', 'yearly'
     * @param array $unitIds Optional. When provided (e.g. church filter), only these units or church-wide for that church.
     * @param int|null $churchId Optional. When provided with unitIds, include church-wide services.
     * @return array
     */
    public function getChartDataByPeriod($period, array $unitIds = [], $churchId = null) {
        $params = [];
        $types = '';
        $where = '1=1';

        if (!empty($unitIds) || $churchId) {
            $conditions = [];
            if (!empty($unitIds)) {
                $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
                $conditions[] = "a.unit_id IN ({$placeholders})";
                $params = array_merge($params, $unitIds);
                $types .= str_repeat('i', count($unitIds));
            }
            if ($churchId) {
                $conditions[] = "(a.church_id = ? AND a.unit_id IS NULL)";
                $params[] = $churchId;
                $types .= 'i';
            }
            $where = '(' . implode(' OR ', $conditions) . ')';
        }

        $groupBy = '';
        $orderBy = '';
        $limit = 12;
        $labelExpr = "a.event_date";

        switch ($period) {
            case 'weekly':
                $groupBy = "YEARWEEK(a.event_date, 3)";
                $orderBy = "yw DESC";
                $limit = 12;
                $labelExpr = "MIN(a.event_date)";
                break;
            case 'monthly':
                $groupBy = "YEAR(a.event_date), MONTH(a.event_date)";
                $orderBy = "yr DESC, mn DESC";
                $limit = 12;
                $labelExpr = "DATE_FORMAT(a.event_date, '%Y-%m-01')";
                break;
            case 'yearly':
                $groupBy = "YEAR(a.event_date)";
                $orderBy = "yr DESC";
                $limit = 5;
                $labelExpr = "CONCAT(YEAR(a.event_date), '-01-01')";
                break;
            default:
                $period = 'monthly';
                $groupBy = "YEAR(a.event_date), MONTH(a.event_date)";
                $orderBy = "yr DESC, mn DESC";
                $limit = 12;
                $labelExpr = "DATE_FORMAT(a.event_date, '%Y-%m-01')";
        }
        $params[] = $limit;
        $types .= 'i';

        $selectGroup = $groupBy;
        if ($period === 'weekly') {
            $selectGroup = "YEARWEEK(a.event_date, 3) AS yw, " . $labelExpr . " AS period_label";
        } elseif ($period === 'monthly') {
            $selectGroup = "YEAR(a.event_date) AS yr, MONTH(a.event_date) AS mn, " . $labelExpr . " AS period_label";
        } else {
            $selectGroup = "YEAR(a.event_date) AS yr, " . $labelExpr . " AS period_label";
        }

        $sql = "SELECT {$selectGroup},
                    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present,
                    SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) AS absent,
                    SUM(CASE WHEN a.status = 'present' AND COALESCE(a.is_first_timer, 0) = 1 THEN 1 ELSE 0 END) AS first_timer
                FROM attendance a
                WHERE {$where}
                GROUP BY {$groupBy}
                ORDER BY {$orderBy}
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $out = [];
        foreach ($rows as $row) {
            $rawLabel = $row['period_label'] ?? $row['yw'] ?? '';
            if ($period === 'weekly' && $rawLabel) {
                $label = date('M j', strtotime($rawLabel)) . ' (week)';
            } elseif ($period === 'monthly' && $rawLabel) {
                $label = date('M Y', strtotime($rawLabel));
            } elseif ($period === 'yearly' && $rawLabel) {
                $label = date('Y', strtotime($rawLabel));
            } else {
                $label = (string) $rawLabel;
            }
            $out[] = [
                'label' => $label,
                'present' => (int) ($row['present'] ?? 0),
                'absent' => (int) ($row['absent'] ?? 0),
                'first_timer' => (int) ($row['first_timer'] ?? 0),
            ];
        }
        return array_reverse($out);
    }

    /**
     * Create multiple attendance records (batch for roll-call submit).
     * Rows may include church_id and null unit_id for church-wide.
     *
     * @param array $rows Each: unit_id (or null), church_id (optional), user_id, event_date, event_type, status
     * @param int $recordedBy User ID of the person recording
     */
    public function createBatch(array $rows, $recordedBy) {
        $count = 0;
        foreach ($rows as $row) {
            $row['recorded_by'] = (int) $recordedBy;
            if ($this->create($row)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get attendance history for a specific user within a given number of days
     * 
     * @param int $userId The user ID
     * @param int $days Number of days to look back (default 90)
     * @return array Array of attendance records
     */
    public function getUserAttendance($userId, $days = 90) {
        try {
            $sql = "SELECT a.*, u.name as unit_name,
                    CASE a.event_type
                        WHEN 'sunday_service' THEN 'Sunday Service'
                        WHEN 'mid_week_service' THEN 'Mid Week Service'
                        WHEN 'workers_meeting' THEN 'Workers Meeting'
                        WHEN 'special_service' THEN 'Special Service'
                        WHEN 'prayer_meeting' THEN 'Prayer Meeting'
                        WHEN 'outreach' THEN 'Outreach'
                        WHEN 'training' THEN 'Training'
                        ELSE a.event_type
                    END as event_type_label
                    FROM attendance a
                    LEFT JOIN units u ON a.unit_id = u.id
                    WHERE a.user_id = ? 
                    AND a.event_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    ORDER BY a.event_date DESC, a.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ii", $userId, $days);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log('Error getting user attendance: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent attendance records for multiple unit IDs
     */
    public function getRecentAttendanceByUnitIds(array $unitIds, $limit = 10) {
        if (empty($unitIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
        $sql = "SELECT a.*, u.name as unit_name,
                CASE a.event_type
                    WHEN 'sunday_service' THEN 'Sunday Service'
                    WHEN 'mid_week_service' THEN 'Mid Week Service'
                    WHEN 'workers_meeting' THEN 'Workers Meeting'
                    WHEN 'special_service' THEN 'Special Service'
                    WHEN 'prayer_meeting' THEN 'Prayer Meeting'
                    WHEN 'outreach' THEN 'Outreach'
                    WHEN 'training' THEN 'Training'
                    ELSE a.event_type
                END as event_type_label
                FROM attendance a
                LEFT JOIN units u ON a.unit_id = u.id
                WHERE a.unit_id IN ({$placeholders})
                ORDER BY a.event_date DESC, a.created_at DESC
                LIMIT {$limit}";
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('i', count($unitIds));
        $stmt->bind_param($types, ...$unitIds);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get attendance summary per unit for a specific church
     */
    public function getAttendanceSummaryByUnit(int $churchId) {
        $sql = "SELECT 
                    u.id as unit_id,
                    u.name as unit_name,
                    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as total_present,
                    SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as total_absent,
                    COUNT(DISTINCT a.event_date, a.event_type) as services_counted
                FROM units u
                JOIN church_units cu ON u.id = cu.unit_id AND cu.church_id = ?
                LEFT JOIN attendance a ON u.id = a.unit_id
                GROUP BY u.id, u.name
                ORDER BY unit_name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $churchId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
