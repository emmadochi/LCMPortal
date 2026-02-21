<?php
namespace App\Utilities;

use App\Models\Project;
use App\Models\Attendance;
use App\Models\FollowUp;
use App\Models\User;

class EventFollowUpManager {
    
    /**
     * Post-event engagement automation
     */
    public static function postEventFollowUp($eventId) {
        $projectModel = new Project();
        $attendanceModel = new Attendance();
        $followUpModel = new FollowUp();
        $userModel = new User();
        
        $results = [
            'event_id' => $eventId,
            'thank_you_sent' => 0,
            'feedback_collected' => 0,
            'follow_ups_created' => 0,
            'errors' => []
        ];
        
        try {
            $event = $projectModel->find($eventId);
            if (!$event) {
                $results['errors'][] = 'Event not found';
                return $results;
            }
            
            // Get event attendees
            $attendees = $attendanceModel->getEventAttendees($eventId);
            
            if (empty($attendees)) {
                $results['errors'][] = 'No attendees found for this event';
                return $results;
            }
            
            // Send thank you messages
            $results['thank_you_sent'] = self::sendThankYouMessages($attendees, $event);
            
            // Collect feedback from attendees
            $results['feedback_collected'] = self::collectFeedback($attendees, $event);
            
            // Create follow-ups for special cases
            $results['follow_ups_created'] = self::createEventBasedFollowUps($attendees, $event);
            
            // Update event status
            $projectModel->update($eventId, ['status' => 'completed']);
            
        } catch (\Exception $e) {
            $results['errors'][] = 'Error in post-event follow-up: ' . $e->getMessage();
            error_log('EventFollowUpManager error: ' . $e->getMessage());
        }
        
        return $results;
    }
    
    /**
     * Send thank you messages to event attendees
     */
    private static function sendThankYouMessages($attendees, $event) {
        $notificationSystem = new AINotificationSystem();
        $sentCount = 0;
        
        $thankYouTemplate = [
            'subject' => 'Thank You for Attending {{event_title}}',
            'message' => '<p>Dear {{first_name}},</p>
                          <p>Thank you for joining us at <strong>{{event_title}}</strong> on {{event_date}}. 
                          We hope you were blessed and encouraged by our time together.</p>
                          <p>Your presence makes a difference in our church family!</p>
                          <p>Blessings,<br>The {{church_name}} Team</p>',
            'title' => 'Thank You for Attending!'
        ];
        
        foreach ($attendees as $attendee) {
            $data = [
                'event_title' => $event['title'],
                'event_date' => date('F j, Y', strtotime($event['start_date'])),
                'church_name' => 'Church Community' // Would come from config
            ];
            
            $success = $notificationSystem->sendPersonalizedNotification(
                $attendee['user_id'], 
                'event_thank_you', 
                $thankYouTemplate, 
                $data
            );
            
            if ($success) {
                $sentCount++;
            }
        }
        
        return $sentCount;
    }
    
    /**
     * Collect feedback from event attendees
     */
    private static function collectFeedback($attendees, $event) {
        $notificationSystem = new AINotificationSystem();
        $feedbackCount = 0;
        
        $feedbackTemplate = [
            'subject' => 'Share Your Feedback - {{event_title}}',
            'message' => '<p>Dear {{first_name}},</p>
                          <p>We hope you enjoyed <strong>{{event_title}}</strong>! 
                          Your feedback helps us improve and better serve our church family.</p>
                          <p>Would you mind taking a moment to share your thoughts?</p>
                          <p><a href="{{feedback_link}}" style="background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;">
                          Share Your Feedback</a></p>
                          <p>Thank you for helping us grow!</p>',
            'title' => 'We Value Your Feedback'
        ];
        
        foreach ($attendees as $attendee) {
            // Only send feedback requests to engaged members
            if (($attendee['engagement_score'] ?? 50) > 30) {
                $data = [
                    'event_title' => $event['title'],
                    'feedback_link' => '/feedback/event/' . $event['id'] // Would be actual route
                ];
                
                $success = $notificationSystem->sendPersonalizedNotification(
                    $attendee['user_id'],
                    'event_feedback',
                    $feedbackTemplate,
                    $data
                );
                
                if ($success) {
                    $feedbackCount++;
                }
            }
        }
        
        return $feedbackCount;
    }
    
    /**
     * Create follow-ups based on event attendance and behavior
     */
    private static function createEventBasedFollowUps($attendees, $event) {
        $followUpModel = new FollowUp();
        $createdCount = 0;
        
        foreach ($attendees as $attendee) {
            $userId = $attendee['user_id'];
            $engagementScore = $attendee['engagement_score'] ?? 50;
            
            // First-time attendees get special follow-up
            if (self::isFirstTimeAttendee($userId, $event['start_date'])) {
                $followUpModel->create([
                    'member_id' => $userId,
                    'type' => 'new_visitor',
                    'status' => 'pending',
                    'due_date' => date('Y-m-d', strtotime('+3 days')),
                    'priority' => 'high',
                    'notes' => 'First-time attendee at ' . $event['title']
                ]);
                $createdCount++;
            }
            
            // Low-engagement attendees who attended get encouragement follow-up
            elseif ($engagementScore < 40) {
                $followUpModel->create([
                    'member_id' => $userId,
                    'type' => 'event_encouragement',
                    'status' => 'pending',
                    'due_date' => date('Y-m-d', strtotime('+7 days')),
                    'priority' => 'medium',
                    'notes' => 'Attended ' . $event['title'] . ' - good sign of interest'
                ]);
                $createdCount++;
            }
            
            // High-interest events trigger invitation to next similar event
            if (self::isHighInterestEvent($event)) {
                // Check if they're interested in more events
                if (rand(1, 100) > 70) { // 30% chance to invite to next event
                    $followUpModel->create([
                        'member_id' => $userId,
                        'type' => 'event_invitation',
                        'status' => 'pending',
                        'due_date' => date('Y-m-d', strtotime('+14 days')),
                        'priority' => 'low',
                        'notes' => 'Interested in ' . $event['title'] . ' - invite to similar events'
                    ]);
                    $createdCount++;
                }
            }
        }
        
        return $createdCount;
    }
    
    /**
     * Check if user is a first-time attendee for events of this type
     */
    private static function isFirstTimeAttendee($userId, $eventDate) {
        $attendanceModel = new Attendance();
        
        // Look for previous attendance in similar events
        $previousAttendance = $attendanceModel->getUserAttendance($userId, 90); // Last 90 days
        
        // If they have very limited attendance history, likely first-time
        return count($previousAttendance) <= 1;
    }
    
    /**
     * Determine if event is high-interest based on characteristics
     */
    private static function isHighInterestEvent($event) {
        $title = strtolower($event['title'] ?? '');
        $description = strtolower($event['description'] ?? '');
        $combinedText = $title . ' ' . $description;
        
        $highInterestKeywords = [
            'conference', 'retreat', 'special', 'celebration', 
            'launch', 'dedication', 'anniversary', 'festival'
        ];
        
        foreach ($highInterestKeywords as $keyword) {
            if (strpos($combinedText, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Process all completed events that need follow-up
     */
    public static function processCompletedEvents() {
        $projectModel = new Project();
        $processedEvents = [];
        
        try {
            // Get recently completed events (last 7 days)
            $recentEvents = $projectModel->getCompletedProjects(7);
            
            foreach ($recentEvents as $event) {
                // Check if follow-up already processed
                if (!self::isFollowUpProcessed($event['id'])) {
                    $result = self::postEventFollowUp($event['id']);
                    $processedEvents[] = $result;
                }
            }
            
        } catch (\Exception $e) {
            error_log('Error processing completed events: ' . $e->getMessage());
        }
        
        return $processedEvents;
    }
    
    /**
     * Check if event follow-up has already been processed
     */
    private static function isFollowUpProcessed($eventId) {
        // This would check a flag or log entry
        // For now, returning false to allow processing
        return false;
    }
    
    /**
     * Send reminder to non-attendees of important events
     */
    public static function sendNonAttendeeReminders($eventId) {
        $projectModel = new Project();
        $attendanceModel = new Attendance();
        $userModel = new User();
        $membershipModel = new \App\Models\Membership();
        
        $results = [
            'reminders_sent' => 0,
            'errors' => []
        ];
        
        try {
            $event = $projectModel->find($eventId);
            if (!$event) {
                $results['errors'][] = 'Event not found';
                return $results;
            }
            
            // Get expected attendees (members who usually attend similar events)
            $expectedAttendees = self::getExpectedAttendees($eventId);
            
            // Get actual attendees
            $actualAttendees = array_column($attendanceModel->getEventAttendees($eventId), 'user_id');
            
            // Find non-attendees
            $nonAttendees = array_diff($expectedAttendees, $actualAttendees);
            
            $notificationSystem = new AINotificationSystem();
            $reminderTemplate = [
                'subject' => 'We Missed You at {{event_title}}',
                'message' => '<p>Dear {{first_name}},</p>
                              <p>We noticed you weren\'t able to join us for <strong>{{event_title}}</strong> 
                              on {{event_date}}. We hope everything is well with you.</p>
                              <p>We\'d love to connect and see how you\'re doing. 
                              Please reach out if there\'s anything we can pray about with you.</p>
                              <p>Blessings,<br>The Pastoral Team</p>',
                'title' => 'We Hope You\'re Doing Well'
            ];
            
            foreach ($nonAttendees as $userId) {
                $user = $userModel->find($userId);
                if ($user) {
                    $data = [
                        'event_title' => $event['title'],
                        'event_date' => date('F j, Y', strtotime($event['start_date']))
                    ];
                    
                    $success = $notificationSystem->sendPersonalizedNotification(
                        $userId,
                        'event_missed',
                        $reminderTemplate,
                        $data
                    );
                    
                    if ($success) {
                        $results['reminders_sent']++;
                    }
                }
            }
            
        } catch (\Exception $e) {
            $results['errors'][] = 'Error sending non-attendee reminders: ' . $e->getMessage();
            error_log('Non-attendee reminder error: ' . $e->getMessage());
        }
        
        return $results;
    }
    
    /**
     * Get list of members expected to attend based on history
     */
    private static function getExpectedAttendees($eventId) {
        $projectModel = new Project();
        $attendanceModel = new Attendance();
        $membershipModel = new \App\Models\Membership();
        
        $expected = [];
        
        try {
            $event = $projectModel->find($eventId);
            if (!$event) {
                return $expected;
            }
            
            // Get members with high engagement scores
            $activeMembers = $membershipModel->getActiveMemberships();
            
            foreach ($activeMembers as $member) {
                $engagementScore = $member['engagement_score'] ?? 0;
                
                // Members with engagement score > 50 are likely to attend
                if ($engagementScore > 50) {
                    $expected[] = $member['user_id'];
                }
            }
            
        } catch (\Exception $e) {
            error_log('Error getting expected attendees: ' . $e->getMessage());
        }
        
        return $expected;
    }
    
    /**
     * Generate event impact report
     */
    public static function generateImpactReport($eventId) {
        $projectModel = new Project();
        $attendanceModel = new Attendance();
        $followUpModel = new FollowUp();
        
        $report = [
            'event_id' => $eventId,
            'attendance_metrics' => [],
            'follow_up_metrics' => [],
            'engagement_impact' => [],
            'recommendations' => []
        ];
        
        try {
            $event = $projectModel->find($eventId);
            if (!$event) {
                return $report;
            }
            
            // Attendance metrics
            $attendees = $attendanceModel->getEventAttendees($eventId);
            $report['attendance_metrics'] = [
                'total_attendees' => count($attendees),
                'first_time_attendees' => self::countFirstTimeAttendees($attendees, $event['start_date']),
                'average_engagement_score' => self::calculateAverageEngagement($attendees)
            ];
            
            // Follow-up metrics
            $eventFollowUps = $followUpModel->findAll([
                'type' => ['new_visitor', 'event_encouragement', 'event_invitation']
            ]);
            
            $report['follow_up_metrics'] = [
                'total_follow_ups_created' => count($eventFollowUps),
                'pending_follow_ups' => count(array_filter($eventFollowUps, function($f) { return $f['status'] === 'pending'; })),
                'completed_follow_ups' => count(array_filter($eventFollowUps, function($f) { return $f['status'] === 'completed'; }))
            ];
            
            // Engagement impact analysis
            $report['engagement_impact'] = self::analyzeEngagementImpact($attendees);
            
            // Recommendations
            $report['recommendations'] = self::generateRecommendations($report);
            
        } catch (\Exception $e) {
            error_log('Error generating impact report: ' . $e->getMessage());
        }
        
        return $report;
    }
    
    /**
     * Helper methods for impact report
     */
    private static function countFirstTimeAttendees($attendees, $eventDate) {
        $count = 0;
        foreach ($attendees as $attendee) {
            if (self::isFirstTimeAttendee($attendee['user_id'], $eventDate)) {
                $count++;
            }
        }
        return $count;
    }
    
    private static function calculateAverageEngagement($attendees) {
        if (empty($attendees)) {
            return 0;
        }
        
        $total = array_sum(array_column($attendees, 'engagement_score'));
        return round($total / count($attendees), 2);
    }
    
    private static function analyzeEngagementImpact($attendees) {
        $beforeAvg = 0;
        $afterAvg = self::calculateAverageEngagement($attendees);
        
        return [
            'engagement_lift' => $afterAvg - $beforeAvg,
            'high_engagement_gained' => count(array_filter($attendees, function($a) { return ($a['engagement_score'] ?? 0) > 70; })),
            'low_engagement_opportunities' => count(array_filter($attendees, function($a) { return ($a['engagement_score'] ?? 100) < 40; }))
        ];
    }
    
    private static function generateRecommendations($report) {
        $recommendations = [];
        
        if ($report['attendance_metrics']['first_time_attendees'] > 0) {
            $recommendations[] = 'Focus on integrating new visitors into community groups';
        }
        
        if ($report['engagement_impact']['low_engagement_opportunities'] > 5) {
            $recommendations[] = 'Develop targeted follow-up strategy for less engaged attendees';
        }
        
        if ($report['follow_up_metrics']['pending_follow_ups'] > 10) {
            $recommendations[] = 'Consider additional staff resources for follow-up activities';
        }
        
        return $recommendations;
    }
}