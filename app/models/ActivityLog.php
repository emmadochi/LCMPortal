<?php
namespace App\Models;

class ActivityLog extends BaseModel {
    protected $table = 'activity_logs';
    protected $fillable = ['user_id', 'action', 'model_type', 'model_id', 'description', 'ip_address', 'user_agent'];

    /**
     * Log an activity
     * 
     * @param int $userId User ID
     * @param string $action Action performed
     * @param string $modelType Model type (e.g., 'User', 'Unit', 'Report')
     * @param int|null $modelId Model ID
     * @param string|null $description Additional description
     * @return int|false Log ID or false on failure
     */
    public static function log($userId, $action, $modelType = null, $modelId = null, $description = null) {
        $log = new self();
        
        // Verify user exists before logging
        if ($userId) {
            $userModel = new \App\Models\User();
            $user = $userModel->find($userId);
            if (!$user) {
                // User doesn't exist, log with null user_id
                $userId = null;
                if ($description) {
                    $description = "[User ID: {$userId} not found] " . $description;
                } else {
                    $description = "Activity by non-existent user ID: {$userId}";
                }
            }
        }
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        return $log->create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);
    }

    /**
     * Build WHERE clause and params for conditions + optional search
     */
    private function buildWhereClause($conditions, $search, &$params, &$types) {
        $where = [];
        if (!empty($conditions)) {
            foreach ($conditions as $field => $value) {
                $where[] = "al.{$field} = ?";
                $params[] = $value;
                $types .= is_int($value) ? "i" : "s";
            }
        }
        if ($search !== null && $search !== '') {
            $where[] = "(al.description LIKE ? OR CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) LIKE ? OR u.email LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'sss';
        }
        return $where;
    }

    /**
     * Count activity logs with optional filters and search
     */
    public function countWithFilters($conditions = [], $search = null) {
        $sql = "SELECT COUNT(*) as total FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id";
        $params = [];
        $types = "";
        $where = $this->buildWhereClause($conditions, $search, $params, $types);
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Get activity logs with user details (optionally paginated with search)
     */
    public function getLogsWithDetails($conditions = [], $orderBy = null, $limit = 100, $offset = 0, $search = null) {
        $sql = "SELECT al.*, u.first_name, u.last_name, u.email 
                FROM activity_logs al 
                LEFT JOIN users u ON al.user_id = u.id";
        
        $params = [];
        $types = "";
        $where = $this->buildWhereClause($conditions, $search, $params, $types);
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        } else {
            $sql .= " ORDER BY al.created_at DESC";
        }
        
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

