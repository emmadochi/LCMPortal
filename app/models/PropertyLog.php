<?php
namespace App\Models;

class PropertyLog extends BaseModel {
    protected $table = 'property_logs';
    protected $fillable = [
        'property_id', 'user_id', 'action', 'old_status',
        'new_status', 'notes'
    ];

    /**
     * Get logs for a property
     */
    public function getPropertyLogs($propertyId, $limit = 50) {
        $sql = "SELECT pl.*, 
                       u.first_name as user_first_name,
                       u.last_name as user_last_name
                FROM property_logs pl
                LEFT JOIN users u ON pl.user_id = u.id
                WHERE pl.property_id = ?
                ORDER BY pl.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $propertyId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Log a property action
     */
    public function logAction($propertyId, $userId, $action, $oldStatus = null, $newStatus = null, $notes = null) {
        return $this->create([
            'property_id' => $propertyId,
            'user_id' => $userId,
            'action' => $action,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes
        ]);
    }
}
