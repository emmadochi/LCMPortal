<?php
namespace App\Utilities;

use App\Models\Notification;
use App\Models\User;

/**
 * NotificationHelper - Utility for sending notifications
 */
class NotificationHelper {
    /**
     * Send notification to user
     */
    public static function notify($userId, $type, $title, $message, $link = null) {
        try {
            return Notification::createNotification($userId, $type, $title, $message, $link);
        } catch (\Exception $e) {
            error_log('Notification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to multiple users
     */
    public static function notifyMultiple($userIds, $type, $title, $message, $link = null) {
        $results = [];
        foreach ($userIds as $userId) {
            $results[] = self::notify($userId, $type, $title, $message, $link);
        }
        return $results;
    }

    /**
     * Send notification to all admins
     */
    public static function notifyAdmins($type, $title, $message, $link = null) {
        try {
            $userModel = new User();
            $admins = $userModel->findAll(['role' => 'admin', 'status' => 'active']);
            $adminIds = array_column($admins, 'id');
            return self::notifyMultiple($adminIds, $type, $title, $message, $link);
        } catch (\Exception $e) {
            error_log('Admin notification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email notification (basic implementation).
     * For production, integrate with PHPMailer or a transactional provider (SendGrid, Mailgun, SES).
     */
    public static function sendEmail($to, $subject, $message, $isHtml = true) {
        $config = require __DIR__ . '/../../config/config.php';
        $fromEmail = $config['mail']['from_email'] ?? 'noreply@churchportal.local';
        $fromName  = $config['mail']['from_name'] ?? 'Church Portal';
        $headers = "MIME-Version: 1.0" . "\r\n";
        if ($isHtml) {
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        }
        $headers .= "From: {$fromName} <{$fromEmail}>" . "\r\n";

        try {
            return mail($to, $subject, $message, $headers);
        } catch (\Exception $e) {
            error_log('Email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification with email
     */
    public static function notifyWithEmail($userId, $type, $title, $message, $link = null, $sendEmail = false) {
        // Create in-app notification
        $notificationId = self::notify($userId, $type, $title, $message, $link);
        
        // Send email if requested
        if ($sendEmail && $notificationId) {
            try {
                $userModel = new User();
                $user = $userModel->find($userId);
                if ($user && !empty($user['email'])) {
                    $emailMessage = "<h2>{$title}</h2><p>{$message}</p>";
                    if ($link) {
                        $emailMessage .= "<p><a href=\"{$link}\">View Details</a></p>";
                    }
                    self::sendEmail($user['email'], $title, $emailMessage, true);
                }
            } catch (\Exception $e) {
                error_log('Email notification error: ' . $e->getMessage());
            }
        }
        
        return $notificationId;
    }
}

