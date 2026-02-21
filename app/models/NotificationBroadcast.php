<?php
namespace App\Models;

class NotificationBroadcast extends BaseModel {
    protected $table = 'notification_broadcasts';
    protected $fillable = [
        'sent_by_user_id', 'title', 'message', 'link', 'image_path',
        'audience_type', 'audience_roles', 'audience_scope', 'channels', 'recipient_count'
    ];

    /**
     * Get recent broadcasts with sender info
     */
    public function getBroadcastsWithSender($limit = 50) {
        $sql = "SELECT nb.*, u.first_name, u.last_name, u.email as sender_email
                FROM {$this->table} nb
                LEFT JOIN users u ON nb.sent_by_user_id = u.id
                ORDER BY nb.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
