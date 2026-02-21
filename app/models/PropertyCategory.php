<?php
namespace App\Models;

class PropertyCategory extends BaseModel {
    protected $table = 'property_categories';
    protected $fillable = ['name', 'description', 'created_by'];

    /**
     * Get all categories with property counts
     */
    public function getAllWithCounts() {
        $sql = "SELECT pc.*, 
                       COUNT(p.id) as property_count,
                       u.first_name as creator_first_name,
                       u.last_name as creator_last_name
                FROM property_categories pc
                LEFT JOIN properties p ON pc.id = p.category_id
                LEFT JOIN users u ON pc.created_by = u.id
                GROUP BY pc.id
                ORDER BY pc.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Check if category has properties
     */
    public function hasProperties($categoryId) {
        $sql = "SELECT COUNT(*) as count FROM properties WHERE category_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)$result['count'] > 0;
    }
}
