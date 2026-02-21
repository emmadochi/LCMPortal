<?php
namespace App\Models;

class Media extends BaseModel {
    protected $table = 'media';
    protected $fillable = ['unit_id', 'uploaded_by', 'file_name', 'file_path', 'file_type', 'file_size', 'title', 'description', 'category', 'tags'];
    
    // Note: Media table uses 'uploaded_by' to reference the user who uploaded the file.

    /**
     * Get media with details
     */
    public function getMediaWithDetails($conditions = [], $orderBy = null) {
        $sql = "SELECT m.*, u.name as unit_name, us.first_name, us.last_name 
                FROM media m 
                LEFT JOIN units u ON m.unit_id = u.id 
                LEFT JOIN users us ON m.uploaded_by = us.id";
        
        $params = [];
        $types = "";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "m.{$field} = ?";
                $params[] = $value;
                $types .= is_int($value) ? "i" : "s";
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        } else {
            $sql .= " ORDER BY m.created_at DESC";
        }
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

