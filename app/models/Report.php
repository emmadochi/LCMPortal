<?php
namespace App\Models;

class Report extends BaseModel {
    protected $table = 'reports';
    protected $fillable = ['unit_id', 'user_id', 'title', 'content', 'report_type', 'status', 'submitted_at'];

    /**
     * Get reports with unit and user info
     */
    public function getReportsWithDetails($conditions = [], $orderBy = null) {
        $sql = "SELECT r.*, u.name as unit_name, us.first_name, us.last_name, us.email as user_email 
                FROM reports r 
                LEFT JOIN units u ON r.unit_id = u.id 
                LEFT JOIN users us ON r.user_id = us.id";
        
        $params = [];
        $types = "";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "r.{$field} = ?";
                $params[] = $value;
                $types .= is_int($value) ? "i" : "s";
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        } else {
            $sql .= " ORDER BY r.created_at DESC";
        }
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get reports for a set of unit IDs (church-scoped view for super admin)
     */
    public function getReportsWithDetailsByUnitIds(array $unitIds, $orderBy = null) {
        if (empty($unitIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
        $sql = "SELECT r.*, u.name as unit_name, us.first_name, us.last_name, us.email as user_email 
                FROM reports r 
                LEFT JOIN units u ON r.unit_id = u.id 
                LEFT JOIN users us ON r.user_id = us.id
                WHERE r.unit_id IN ({$placeholders})";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        } else {
            $sql .= " ORDER BY r.created_at DESC";
        }
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('i', count($unitIds));
        $stmt->bind_param($types, ...$unitIds);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get report files
     */
    public function getFiles($reportId) {
        $fileModel = new ReportFile();
        return $fileModel->findAll(['report_id' => $reportId]);
    }

    /**
     * Get reports by multiple unit IDs with limit
     */
    public function getReportsByUnitIds(array $unitIds, $orderBy = 'created_at DESC', $limit = null) {
        if (empty($unitIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
        $sql = "SELECT r.*, u.name as unit_name, us.first_name, us.last_name, us.email as user_email 
                FROM reports r 
                LEFT JOIN units u ON r.unit_id = u.id 
                LEFT JOIN users us ON r.user_id = us.id
                WHERE r.unit_id IN ({$placeholders})";
        if ($orderBy) {
            $sql .= " ORDER BY r.{$orderBy}";
        }
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('i', count($unitIds));
        $stmt->bind_param($types, ...$unitIds);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

