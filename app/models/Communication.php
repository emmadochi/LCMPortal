<?php
namespace App\Models;

class Communication extends BaseModel {
    protected $table = 'communications';
    protected $fillable = [
        'subject', 'message', 'delivery_method', 'membership_types',
        'include_unengaged', 'send_immediately', 'scheduled_time',
        'sent_by', 'recipient_count', 'delivered_count', 'failed_count'
    ];

    /**
     * Get all communications with sender information
     */
    public function getCommunicationsWithSender($limit = 50) {
        $sql = "SELECT c.*, u.first_name, u.last_name, u.email as sender_email 
                FROM communications c 
                LEFT JOIN users u ON c.sent_by = u.id
                ORDER BY c.created_at DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get communication by ID with sender details
     */
    public function getCommunicationWithSender($id) {
        $sql = "SELECT c.*, u.first_name, u.last_name, u.email as sender_email 
                FROM communications c 
                LEFT JOIN users u ON c.sent_by = u.id
                WHERE c.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create a new communication record
     */
    public function createCommunication($data) {
        $sql = "INSERT INTO communications (
                    subject, message, delivery_method, membership_types,
                    include_unengaged, send_immediately, scheduled_time,
                    sent_by, recipient_count, delivered_count, failed_count, sent_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
        $membershipTypes = isset($data['membership_types']) ? json_encode($data['membership_types']) : null;
        $scheduledTime = isset($data['scheduled_time']) ? $data['scheduled_time'] : null;
        $sentAt = isset($data['sent_at']) ? $data['sent_at'] : null;
        
        $stmt->bind_param(
            "ssssiiiiiiis",
            $data['subject'],
            $data['message'],
            $data['delivery_method'],
            $membershipTypes,
            $data['include_unengaged'],
            $data['send_immediately'],
            $scheduledTime,
            $data['sent_by'],
            $data['recipient_count'],
            $data['delivered_count'],
            $data['failed_count'],
            $sentAt
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        return false;
    }

    /**
     * Update communication delivery statistics
     */
    public function updateDeliveryStats($id, $deliveredCount, $failedCount) {
        $sql = "UPDATE communications 
                SET delivered_count = ?, failed_count = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iii", $deliveredCount, $failedCount, $id);
        return $stmt->execute();
    }

    /**
     * Get recent communications for dashboard
     */
    public function getRecentCommunications($days = 30) {
        $sql = "SELECT c.*, u.first_name, u.last_name
                FROM communications c
                LEFT JOIN users u ON c.sent_by = u.id
                WHERE c.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $days);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}