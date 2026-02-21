<?php
namespace App\Models;

class Property extends BaseModel {
    protected $table = 'properties';
    protected $fillable = [
        'category_id',
        'church_id',
        'name',
        'description',
        'status',
        'image_path',
        'location',
        'purchase_date',
        'purchase_cost',
        'serial_number',
        'notes',
        'created_by',
        'assigned_to_user_id',
    ];

    /**
     * Get all properties with category and creator info
     */
    public function getAllWithDetails($filters = []) {
        $sql = "SELECT p.*, 
                       pc.name AS category_name,
                       u.first_name AS creator_first_name,
                       u.last_name AS creator_last_name,
                       c.name AS church_name,
                       au.first_name AS assigned_first_name,
                       au.last_name AS assigned_last_name
                FROM properties p
                INNER JOIN property_categories pc ON p.category_id = pc.id
                LEFT JOIN users u ON p.created_by = u.id
                LEFT JOIN churches c ON p.church_id = c.id
                LEFT JOIN users au ON p.assigned_to_user_id = au.id
                WHERE 1=1";

        $params = [];
        $types = "";

        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = (int)$filters['category_id'];
            $types .= "i";
        }

        if (!empty($filters['status'])) {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        if (!empty($filters['church_id'])) {
            $sql .= " AND p.church_id = ?";
            $params[] = (int)$filters['church_id'];
            $types .= "i";
        }

        if (!empty($filters['assigned_to_user_id'])) {
            $sql .= " AND p.assigned_to_user_id = ?";
            $params[] = (int)$filters['assigned_to_user_id'];
            $types .= "i";
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.serial_number LIKE ?)";
            $search = "%{$filters['search']}%";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $types .= "sss";
        }

        $sql .= " ORDER BY p.created_at DESC";

        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = (int)$filters['limit'];
            $types .= "i";
        }

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get property with full details
     */
    public function getWithDetails($id) {
        $sql = "SELECT p.*, 
                       pc.name AS category_name,
                       pc.description AS category_description,
                       u.first_name AS creator_first_name,
                       u.last_name AS creator_last_name,
                       c.name AS church_name,
                       au.first_name AS assigned_first_name,
                       au.last_name AS assigned_last_name
                FROM properties p
                INNER JOIN property_categories pc ON p.category_id = pc.id
                LEFT JOIN users u ON p.created_by = u.id
                LEFT JOIN churches c ON p.church_id = c.id
                LEFT JOIN users au ON p.assigned_to_user_id = au.id
                WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update property status and log the change
     */
    public function updateStatus($id, $newStatus, $userId, $notes = null, $oldStatus = null) {
        if ($oldStatus === null) {
            $property = $this->find($id);
            $oldStatus = $property['status'] ?? null;
        }

        $updated = $this->update($id, ['status' => $newStatus]);
        
        if ($updated) {
            $logModel = new PropertyLog();
            $logModel->create([
                'property_id' => $id,
                'user_id' => $userId,
                'action' => 'status_change',
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'notes' => $notes
            ]);
        }

        return $updated;
    }

    /**
     * Assign property to a user (or unassign when $toUserId is null)
     */
    public function assignToUser($propertyId, $toUserId, $assignedByUserId, $notes = null) {
        $property = $this->find($propertyId);
        if (!$property) {
            return false;
        }

        $fromUserId = $property['assigned_to_user_id'] ?? null;
        $updated = $this->update($propertyId, [
            'assigned_to_user_id' => $toUserId ?: null,
        ]);

        if ($updated) {
            $assignmentLog = new PropertyAssignmentLog();
            $assignmentLog->create([
                'property_id' => $propertyId,
                'from_user_id' => $fromUserId ?: null,
                'to_user_id' => $toUserId ?: null,
                'assigned_by_user_id' => $assignedByUserId,
                'notes' => $notes,
            ]);

            $logModel = new PropertyLog();
            $action = $fromUserId && $toUserId ? 'reassigned'
                     : ($toUserId ? 'assigned' : 'unassigned');
            $logModel->logAction($propertyId, $assignedByUserId, $action, null, null, $notes);
        }

        return $updated;
    }

    /**
     * Transfer property between churches (and optionally relocate within a church)
     */
    public function transferToChurch($propertyId, $toChurchId, $toLocation, $userId, $notes = null) {
        $property = $this->find($propertyId);
        if (!$property) {
            return false;
        }

        $fromChurchId = $property['church_id'] ?? null;
        $fromLocation = $property['location'] ?? null;

        $updated = $this->update($propertyId, [
            'church_id' => $toChurchId,
            'location' => $toLocation !== '' ? $toLocation : null,
        ]);

        if ($updated) {
            $transferModel = new PropertyTransfer();
            $transferModel->create([
                'property_id' => $propertyId,
                'from_church_id' => $fromChurchId ?: null,
                'to_church_id' => $toChurchId,
                'from_location' => $fromLocation ?: null,
                'to_location' => $toLocation !== '' ? $toLocation : null,
                'user_id' => $userId,
                'notes' => $notes,
            ]);

            $logModel = new PropertyLog();
            $logModel->logAction($propertyId, $userId, 'transferred', null, null, $notes);
        }

        return $updated;
    }

    /**
     * Get status options
     */
    public static function getStatusOptions() {
        return [
            'available' => 'Available',
            'in_use' => 'In Use',
            'maintenance' => 'Under Maintenance',
            'damaged' => 'Damaged',
            'disposed' => 'Disposed',
            'lost' => 'Lost'
        ];
    }
}
