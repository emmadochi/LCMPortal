<?php
namespace App\Utilities;

use App\Models\User;
use App\Models\Membership;
use App\Models\Notification;

class AINotificationSystem {
    
    /**
     * Send personalized notifications based on member preferences and behavior
     */
    public static function sendPersonalizedNotification($userId, $type, $contentTemplate, $data = []) {
        $userModel = new User();
        $membershipModel = new Membership();
        $notificationModel = new Notification();
        
        try {
            $user = $userModel->find($userId);
            if (!$user) {
                return false;
            }
            
            // Get user preferences and behavior data
            $preferences = self::getUserPreferences($userId);
            $behaviorData = self::getUserBehaviorData($userId);
            
            // Personalize content
            $personalizedContent = self::personalizeContent($contentTemplate, $user, $data, $behaviorData);
            
            // Determine optimal communication channel
            $channel = self::determineOptimalChannel($userId, $type, $preferences, $behaviorData);
            
            // Send notification
            $result = self::sendViaChannel($userId, $type, $personalizedContent, $channel, $data);
            
            if ($result) {
                // Log notification
                $notificationModel->create([
                    'user_id' => $userId,
                    'type' => $type,
                    'title' => self::generateTitle($type),
                    'message' => $personalizedContent['message'],
                    'channel' => $channel,
                    'status' => 'sent'
                ]);
                
                return true;
            }
            
        } catch (\Exception $e) {
            error_log('Error sending personalized notification: ' . $e->getMessage());
        }
        
        return false;
    }
    
    /**
     * Get user communication preferences
     */
    private static function getUserPreferences($userId) {
        // In a real implementation, this would pull from user preference settings
        // For now, we'll use defaults based on user behavior
        return [
            'preferred_channels' => ['email', 'in_app'], // Default channels
            'quiet_hours' => ['start' => '22:00', 'end' => '07:00'],
            'frequency_preference' => 'moderate' // low, moderate, high
        ];
    }
    
    /**
     * Analyze user behavior data for personalization
     */
    private static function getUserBehaviorData($userId) {
        $membershipModel = new Membership();
        $behaviorData = [
            'engagement_level' => 'medium',
            'preferred_times' => [],
            'communication_history' => [],
            'interests' => []
        ];
        
        try {
            // Get membership data
            $memberships = $membershipModel->getByUserId($userId);
            if (!empty($memberships)) {
                $membership = $memberships[0];
                $behaviorData['engagement_level'] = self::categorizeEngagement($membership['engagement_score']);
            }
            
            // Analyze communication history would go here
            // For now, we'll simulate some data
            
        } catch (\Exception $e) {
            error_log('Error getting user behavior data: ' . $e->getMessage());
        }
        
        return $behaviorData;
    }
    
    /**
     * Categorize engagement level
     */
    private static function categorizeEngagement($score) {
        if ($score >= 75) return 'high';
        if ($score >= 40) return 'medium';
        return 'low';
    }
    
    /**
     * Personalize content template with user data
     */
    private static function personalizeContent($template, $user, $data, $behaviorData) {
        $personalized = $template;
        
        // Replace placeholders with user data
        $replacements = [
            '{{first_name}}' => $user['first_name'],
            '{{last_name}}' => $user['last_name'],
            '{{full_name}}' => trim($user['first_name'] . ' ' . $user['last_name']),
            '{{engagement_level}}' => $behaviorData['engagement_level']
        ];
        
        // Add data replacements
        foreach ($data as $key => $value) {
            $replacements["{{{$key}}}"] = $value;
        }
        
        // Apply replacements
        foreach ($replacements as $placeholder => $replacement) {
            $personalized = str_replace($placeholder, $replacement, $personalized);
        }
        
        // Adjust tone based on engagement level
        if ($behaviorData['engagement_level'] === 'high') {
            $personalized['tone'] = 'warm and encouraging';
        } elseif ($behaviorData['engagement_level'] === 'low') {
            $personalized['tone'] = 'gentle and inviting';
        } else {
            $personalized['tone'] = 'friendly and supportive';
        }
        
        return $personalized;
    }
    
    /**
     * Determine optimal communication channel
     */
    private static function determineOptimalChannel($userId, $type, $preferences, $behaviorData) {
        $availableChannels = ['email', 'sms', 'in_app', 'push'];
        $preferredChannels = $preferences['preferred_channels'];
        
        // Emergency/high priority goes to multiple channels
        if (strpos($type, 'urgent') !== false || strpos($type, 'important') !== false) {
            return ['email', 'in_app']; // Multi-channel for important messages
        }
        
        // Engagement level affects channel choice
        if ($behaviorData['engagement_level'] === 'high') {
            // Highly engaged users can handle more channels
            return array_intersect($preferredChannels, $availableChannels);
        } elseif ($behaviorData['engagement_level'] === 'low') {
            // Less engaged users - stick to preferred channels
            return [$preferredChannels[0]]; // Just the primary preferred channel
        }
        
        // Default - use first preferred channel
        return [$preferredChannels[0]];
    }
    
    /**
     * Send notification via specified channel(s)
     */
    private static function sendViaChannel($userId, $type, $content, $channels, $data = []) {
        $success = false;
        
        foreach ((array)$channels as $channel) {
            switch ($channel) {
                case 'email':
                    $success = self::sendEmail($userId, $type, $content, $data);
                    break;
                case 'sms':
                    $success = self::sendSMS($userId, $content, $data);
                    break;
                case 'in_app':
                    $success = self::sendInAppNotification($userId, $type, $content, $data);
                    break;
                case 'push':
                    $success = self::sendPushNotification($userId, $content, $data);
                    break;
            }
            
            if ($success) {
                // Log successful delivery
                error_log("Notification sent via {$channel} to user {$userId}");
            }
        }
        
        return $success;
    }
    
    /**
     * Send email notification
     */
    private static function sendEmail($userId, $type, $content, $data = []) {
        $userModel = new User();
        $user = $userModel->find($userId);
        
        if (!$user || !$user['email']) {
            return false;
        }
        
        $subject = $content['subject'] ?? self::generateSubject($type);
        $message = $content['message'] ?? 'No message content';
        
        // In production, integrate with PHPMailer or similar
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: Church Portal <noreply@church.com>\r\n";
        
        try {
            return mail($user['email'], $subject, $message, $headers);
        } catch (\Exception $e) {
            error_log('Email sending error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send SMS notification (stub - would integrate with SMS service)
     */
    private static function sendSMS($userId, $content, $data = []) {
        // This would integrate with Twilio, Nexmo, or similar SMS service
        error_log("SMS notification to user {$userId}: " . ($content['message'] ?? ''));
        return true; // Simulate success
    }
    
    /**
     * Send in-app notification
     */
    private static function sendInAppNotification($userId, $type, $content, $data = []) {
        $notificationModel = new Notification();
        
        try {
            return $notificationModel->create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $content['title'] ?? self::generateTitle($type),
                'message' => $content['message'] ?? '',
                'link' => $data['link'] ?? null,
                'status' => 'unread'
            ]);
        } catch (\Exception $e) {
            error_log('In-app notification error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send push notification (stub - would integrate with push service)
     */
    private static function sendPushNotification($userId, $content, $data = []) {
        // This would integrate with Firebase, OneSignal, or similar
        error_log("Push notification to user {$userId}: " . ($content['message'] ?? ''));
        return true; // Simulate success
    }
    
    /**
     * Predict best timing for outreach
     */
    public static function optimizeTiming($userId, $messageType) {
        $behaviorData = self::getUserBehaviorData($userId);
        $preferences = self::getUserPreferences($userId);
        
        // Default optimal times based on message type
        $optimalTimes = [
            'encouragement' => ['09:00', '14:00', '19:00'],
            'reminder' => ['08:00', '12:00', '17:00'],
            'invitation' => ['10:00', '15:00', '18:00'],
            'urgent' => ['immediate'] // Send immediately
        ];
        
        $times = $optimalTimes[$messageType] ?? ['10:00', '15:00'];
        
        // Respect quiet hours
        $quietStart = strtotime($preferences['quiet_hours']['start']);
        $quietEnd = strtotime($preferences['quiet_hours']['end']);
        
        // Filter out quiet hours
        $filteredTimes = [];
        foreach ($times as $time) {
            if ($time === 'immediate') {
                $filteredTimes[] = $time;
                continue;
            }
            
            $timeStamp = strtotime($time);
            if ($timeStamp < $quietStart || $timeStamp > $quietEnd) {
                $filteredTimes[] = $time;
            }
        }
        
        // Return best time (first non-quiet hour option)
        return !empty($filteredTimes) ? $filteredTimes[0] : '10:00';
    }
    
    /**
     * Generate subject line based on notification type
     */
    private static function generateSubject($type) {
        $subjects = [
            'welcome' => 'Welcome to Our Church Family!',
            'follow_up' => 'We\'d Love to Connect With You',
            'event_invitation' => 'Special Invitation Just for You',
            'encouragement' => 'A Word of Encouragement',
            'reminder' => 'Friendly Reminder',
            'urgent' => 'Important Message - Please Read'
        ];
        
        return $subjects[$type] ?? 'Message From Your Church';
    }
    
    /**
     * Generate title based on notification type
     */
    private static function generateTitle($type) {
        $titles = [
            'welcome' => 'Welcome!',
            'follow_up' => 'Let\'s Connect',
            'event_invitation' => 'Special Invitation',
            'encouragement' => 'Encouraging Word',
            'reminder' => 'Reminder',
            'urgent' => 'Important Notice'
        ];
        
        return $titles[$type] ?? 'Church Message';
    }
    
    /**
     * Batch send notifications to multiple users
     */
    public static function batchSendNotifications($userIds, $type, $contentTemplate, $data = []) {
        $results = [
            'successful' => 0,
            'failed' => 0,
            'details' => []
        ];
        
        foreach ($userIds as $userId) {
            $success = self::sendPersonalizedNotification($userId, $type, $contentTemplate, $data);
            if ($success) {
                $results['successful']++;
            } else {
                $results['failed']++;
            }
            $results['details'][] = ['user_id' => $userId, 'success' => $success];
        }
        
        return $results;
    }
}