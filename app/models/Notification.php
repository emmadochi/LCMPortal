<?php
namespace App\Models;

class Notification extends BaseModel {
    protected $table = 'notifications';
    protected $fillable = ['user_id', 'type', 'title', 'message', 'link', 'image_path', 'is_read', 'read_at'];

    /**
     * Create a notification
     */
    public static function createNotification($userId, $type, $title, $message, $link = null, $imagePath = null) {
        $notification = new self();
        return $notification->create([
            'user_id' => $userId,
            'type' => $type, // info, success, warning, error
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'image_path' => $imagePath,
            'is_read' => 0
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id) {
        return $this->update($id, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get unread notifications for user
     */
    public function getUnreadNotifications($userId, $limit = 10) {
        return $this->findAll([
            'user_id' => $userId,
            'is_read' => 0
        ], 'created_at DESC', $limit);
    }

    /**
     * Get all notifications for user
     */
    public function getUserNotifications($userId, $limit = 50) {
        return $this->findAll([
            'user_id' => $userId
        ], 'created_at DESC', $limit);
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount($userId) {
        return $this->count([
            'user_id' => $userId,
            'is_read' => 0
        ]);
    }
}

