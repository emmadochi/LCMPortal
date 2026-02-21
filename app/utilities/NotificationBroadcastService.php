<?php
namespace App\Utilities;

use App\Models\Church;
use App\Models\Notification;
use App\Models\NotificationBroadcast;
use App\Models\User;
use App\Models\ActivityLog;

/**
 * Service for broadcast notifications: in-app + email, with role-based, church, or unit audience.
 */
class NotificationBroadcastService {
    /** Batch size for email sending to avoid timeouts */
    const EMAIL_BATCH_SIZE = 50;

    /**
     * Send a broadcast notification.
     *
     * @param int $sentByUserId User ID sending the broadcast
     * @param string $title Notification title
     * @param string $message Notification body
     * @param string|null $link Optional URL
     * @param string|null $imagePath Optional path to attached image
     * @param string $audienceType 'all' | 'roles' | 'church_members' | 'church_by_unit_role' | 'unit_members'
     * @param array $audienceRoles Role keys when audienceType is 'roles'
     * @param string $channels 'in_app' | 'email' | 'both'
     * @param array|null $audienceScope Extra scope: ['church_id'=>int, 'unit_ids'=>[], 'unit_roles'=>[]]
     * @return array ['success' => bool, 'recipient_count' => int, 'message' => string, 'broadcast_id' => int|null]
     */
    public static function sendBroadcast($sentByUserId, $title, $message, $link, $imagePath, $audienceType, array $audienceRoles, $channels, array $audienceScope = null) {
        $recipients = self::resolveRecipients($audienceType, $audienceRoles, $audienceScope ?? []);
        $recipientCount = count($recipients);
        if ($recipientCount === 0) {
            return [
                'success' => false,
                'recipient_count' => 0,
                'message' => 'No recipients match the selected audience.',
                'broadcast_id' => null
            ];
        }

        $sendEmail = ($channels === 'email' || $channels === 'both');
        $sendInApp = ($channels === 'in_app' || $channels === 'both');

        $notificationModel = new Notification();
        $created = 0;
        $emailSent = 0;

        $appUrl = (function () {
            $config = require __DIR__ . '/../../config/config.php';
            return rtrim($config['app_url'] ?? 'http://localhost', '/');
        })();

        foreach ($recipients as $user) {
            $userId = (int) $user['id'];
            if ($sendInApp) {
                $id = Notification::createNotification($userId, 'info', $title, $message, $link, $imagePath);
                if ($id) {
                    $created++;
                }
            }
            if ($sendEmail && !empty($user['email'])) {
                $emailBody = "<h2>" . htmlspecialchars($title) . "</h2><p>" . nl2br(htmlspecialchars($message)) . "</p>";
                if ($imagePath) {
                    $imageUrl = $appUrl . '/' . ltrim($imagePath, '/');
                    $emailBody .= "<p><img src=\"" . htmlspecialchars($imageUrl) . "\" alt=\"Attachment\" style=\"max-width:100%;height:auto;\" /></p>";
                }
                if ($link) {
                    $emailBody .= "<p><a href=\"" . htmlspecialchars($link) . "\">View in portal</a></p>";
                }
                if (NotificationHelper::sendEmail($user['email'], $title, $emailBody, true)) {
                    $emailSent++;
                }
            }
        }

        $broadcastModel = new NotificationBroadcast();
        $audienceRolesStr = $audienceType === 'roles' ? implode(',', $audienceRoles) : null;
        $scopeJson = !empty($audienceScope) ? json_encode($audienceScope) : null;
        $broadcastId = $broadcastModel->create([
            'sent_by_user_id' => $sentByUserId,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'image_path' => $imagePath,
            'audience_type' => $audienceType,
            'audience_roles' => $audienceRolesStr,
            'audience_scope' => $scopeJson,
            'channels' => $channels,
            'recipient_count' => $recipientCount
        ]);

        ActivityLog::log(
            $sentByUserId,
            'notification_broadcast',
            'NotificationBroadcast',
            $broadcastId ?: null,
            "Sent broadcast to {$recipientCount} recipient(s): {$title}"
        );

        return [
            'success' => true,
            'recipient_count' => $recipientCount,
            'in_app_count' => $created,
            'email_count' => $emailSent,
            'message' => "Notification sent to {$recipientCount} recipient(s).",
            'broadcast_id' => $broadcastId
        ];
    }

    /**
     * Resolve recipients based on audience type and scope.
     *
     * @param string $audienceType
     * @param array $audienceRoles
     * @param array $audienceScope ['church_id'=>int, 'unit_ids'=>[], 'unit_roles'=>[]]
     * @return array User rows
     */
    private static function resolveRecipients($audienceType, array $audienceRoles, array $audienceScope) {
        $userModel = new User();

        switch ($audienceType) {
            case 'all':
                return $userModel->getActiveUsers();
            case 'roles':
                return $userModel->getActiveUsersByRoles($audienceRoles);
            case 'church_members': {
                $churchId = (int) ($audienceScope['church_id'] ?? 0);
                if ($churchId <= 0) {
                    return [];
                }
                $churchModel = new Church();
                return $churchModel->getChurchMemberUsers($churchId);
            }
            case 'church_unit_heads': {
                $churchId = (int) ($audienceScope['church_id'] ?? 0);
                if ($churchId <= 0) {
                    return [];
                }
                $churchModel = new Church();
                $userIds = $churchModel->getChurchUnitDirectorUserIds($churchId);
                if (empty($userIds)) {
                    return [];
                }
                return $userModel->getActiveUsersByIds($userIds);
            }
            case 'church_by_unit_role': {
                $churchId = (int) ($audienceScope['church_id'] ?? 0);
                $unitRoles = $audienceScope['unit_roles'] ?? [];
                if ($churchId <= 0 || empty($unitRoles)) {
                    return [];
                }
                $churchModel = new Church();
                $roles = is_array($unitRoles) ? $unitRoles : [$unitRoles];
                return $churchModel->getChurchMembersByUnitRole($churchId, $roles);
            }
            case 'unit_members': {
                $unitIds = $audienceScope['unit_ids'] ?? [];
                if (empty($unitIds)) {
                    return [];
                }
                $unitIds = array_values(array_map('intval', (array) $unitIds));
                $unitIds = array_filter($unitIds, function ($id) { return $id > 0; });
                if (empty($unitIds)) {
                    return [];
                }
                return $userModel->getActiveUsersByUnitIds($unitIds);
            }
            default:
                return [];
        }
    }
}
