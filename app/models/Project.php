<?php
namespace App\Models;

class Project extends BaseModel {
    protected $table = 'projects';
    protected $fillable = ['title', 'description', 'start_date', 'end_date', 'status', 'priority', 'budget', 'created_by'];

    /**
     * Get projects with details
     */
    public function getProjectsWithDetails($conditions = [], $orderBy = null) {
        $sql = "SELECT p.*, us.first_name, us.last_name, us.email as creator_email 
                FROM projects p 
                LEFT JOIN users us ON p.created_by = us.id";
        
        $params = [];
        $types = "";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "p.{$field} = ?";
                $params[] = $value;
                $types .= is_int($value) ? "i" : "s";
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        } else {
            $sql .= " ORDER BY p.created_at DESC";
        }
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get projects that have at least one unit in the given unit IDs (church-scoped view for super admin)
     */
    public function getProjectsWithDetailsByUnitIds(array $unitIds, $orderBy = null) {
        if (empty($unitIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
        $sql = "SELECT DISTINCT p.*, us.first_name, us.last_name, us.email as creator_email 
                FROM projects p 
                INNER JOIN project_units pu ON p.id = pu.project_id AND pu.unit_id IN ({$placeholders})
                LEFT JOIN users us ON p.created_by = us.id";
        if ($orderBy) {
            $sql .= " ORDER BY " . (strpos($orderBy, 'p.') === 0 ? $orderBy : 'p.' . $orderBy);
        } else {
            $sql .= " ORDER BY p.created_at DESC";
        }
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('i', count($unitIds));
        $stmt->bind_param($types, ...$unitIds);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get project units
     */
    public function getProjectUnits($projectId) {
        $sql = "SELECT pu.*, u.name as unit_name 
                FROM project_units pu 
                LEFT JOIN units u ON pu.unit_id = u.id 
                WHERE pu.project_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

