<?php
namespace App\Models;

class MembershipType extends BaseModel {
    protected $table = 'membership_types';
    protected $fillable = ['name', 'description', 'is_active'];
    
    /**
     * Find membership type by name
     */
    public function findByName($name) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE LOWER(name) = LOWER(?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Get active membership types
     */
    public function getActiveTypes() {
        return $this->findAll(['is_active' => true], 'name ASC');
    }
    
    /**
     * Check if membership type is in use by any members
     */
    public function isInUse($id) {
        try {
            // Check if any memberships are using this type
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM memberships WHERE membership_type_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            return ($result['count'] ?? 0) > 0;
        } catch (\Exception $e) {
            // If memberships table doesn't exist yet, check the old way
            try {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM memberships WHERE membership_type = (SELECT name FROM membership_types WHERE id = ?)");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                return ($result['count'] ?? 0) > 0;
            } catch (\Exception $e2) {
                return false;
            }
        }
    }
    
    /**
     * Get membership type statistics
     */
    public function getTypeStatistics() {
        $stats = [];
        
        try {
            // Get count of members per membership type
            $stmt = $this->db->prepare("
                SELECT mt.name, mt.description, COUNT(m.id) as member_count
                FROM membership_types mt
                LEFT JOIN memberships m ON mt.id = m.membership_type_id AND m.status = 'active'
                GROUP BY mt.id, mt.name, mt.description
                ORDER BY mt.name
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $stats[] = [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'member_count' => $row['member_count']
                ];
            }
            
        } catch (\Exception $e) {
            error_log('Error getting membership type statistics: ' . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Get default membership types if table is empty
     */
    public function getDefaultTypes() {
        return [
            ['name' => 'Visitor', 'description' => 'Guest or new visitor', 'is_active' => true],
            ['name' => 'Member', 'description' => 'Regular church member', 'is_active' => true],
            ['name' => 'Leader', 'description' => 'Team or group leader', 'is_active' => true],
            ['name' => 'Staff', 'description' => 'Church staff member', 'is_active' => true],
            ['name' => 'Elder', 'description' => 'Church elder or board member', 'is_active' => true],
            ['name' => 'Deacon', 'description' => 'Church deacon or servant', 'is_active' => true],
            ['name' => 'Pastor', 'description' => 'Lead pastor or minister', 'is_active' => true]
        ];
    }
    
    /**
     * Seed default membership types
     */
    public function seedDefaultTypes() {
        $existingTypes = $this->findAll();
        
        if (empty($existingTypes)) {
            $defaultTypes = $this->getDefaultTypes();
            foreach ($defaultTypes as $type) {
                $this->create($type);
            }
        }
    }
}