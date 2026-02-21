<?php
namespace App\Utilities;

use App\Models\User;
use App\Models\Membership;
use App\Models\Project; // Events are stored in projects table
use App\Models\Attendance;

class EventRecommendationEngine {
    
    /**
     * Recommend events to members based on interests and history
     */
    public static function recommendEvents($userId, $limit = 5) {
        $userModel = new User();
        $membershipModel = new Membership();
        $projectModel = new Project();
        $attendanceModel = new Attendance();
        
        $recommendations = [];
        
        try {
            $user = $userModel->find($userId);
            if (!$user) {
                return $recommendations;
            }
            
            // Get user's membership and interests
            $memberships = $membershipModel->getByUserId($userId);
            $interests = self::getUserInterests($userId, $memberships);
            $attendanceHistory = $attendanceModel->getUserAttendance($userId, 180); // Last 6 months
            
            // Get upcoming events
            $upcomingEvents = $projectModel->getUpcomingProjects(30); // Next 30 days
            
            // Score each event for relevance
            foreach ($upcomingEvents as $event) {
                $score = self::calculateEventScore($event, $interests, $attendanceHistory, $memberships);
                if ($score > 0) {
                    $recommendations[] = [
                        'event' => $event,
                        'score' => $score,
                        'reasons' => self::getScoringReasons($event, $interests, $attendanceHistory)
                    ];
                }
            }
            
            // Sort by score (highest first)
            usort($recommendations, function($a, $b) {
                return $b['score'] - $a['score'];
            });
            
            // Limit results
            $recommendations = array_slice($recommendations, 0, $limit);
            
        } catch (\Exception $e) {
            error_log('Error in EventRecommendationEngine::recommendEvents: ' . $e->getMessage());
        }
        
        return $recommendations;
    }
    
    /**
     * Get user interests based on membership and behavior
     */
    private static function getUserInterests($userId, $memberships) {
        $interests = [
            'categories' => [],
            'event_types' => [],
            'time_preferences' => [],
            'frequency' => 'moderate'
        ];
        
        try {
            // Analyze membership types
            foreach ($memberships as $membership) {
                switch ($membership['membership_type']) {
                    case 'pastor':
                        $interests['categories'][] = 'leadership';
                        $interests['categories'][] = 'teaching';
                        break;
                    case 'elder':
                        $interests['categories'][] = 'spiritual_growth';
                        $interests['categories'][] = 'counseling';
                        break;
                    case 'deacon':
                        $interests['categories'][] = 'service';
                        $interests['categories'][] = 'community';
                        break;
                    default:
                        $interests['categories'][] = 'general';
                        $interests['categories'][] = 'fellowship';
                }
            }
            
            // Analyze engagement level
            if (!empty($memberships)) {
                $primaryMembership = $memberships[0];
                $engagementScore = $primaryMembership['engagement_score'] ?? 50;
                
                if ($engagementScore > 75) {
                    $interests['frequency'] = 'high';
                    $interests['categories'][] = 'advanced';
                } elseif ($engagementScore < 30) {
                    $interests['frequency'] = 'low';
                    $interests['categories'][] = 'beginner';
                }
            }
            
            // Default interests
            $interests['event_types'] = ['service', 'fellowship', 'training', 'outreach'];
            $interests['time_preferences'] = ['weekend', 'evening'];
            
        } catch (\Exception $e) {
            error_log('Error getting user interests: ' . $e->getMessage());
        }
        
        return $interests;
    }
    
    /**
     * Calculate relevance score for an event
     */
    private static function calculateEventScore($event, $interests, $attendanceHistory, $memberships) {
        $score = 0;
        
        // Category matching (40% weight)
        $eventCategories = self::extractEventCategories($event);
        $matchingCategories = array_intersect($eventCategories, $interests['categories']);
        $score += count($matchingCategories) * 8; // 8 points per matching category
        
        // Event type matching (25% weight)
        $eventType = strtolower($event['title'] ?? '');
        foreach ($interests['event_types'] as $type) {
            if (strpos($eventType, $type) !== false) {
                $score += 5;
                break;
            }
        }
        
        // Attendance history correlation (20% weight)
        $similarEventsAttended = self::countSimilarEventsAttended($event, $attendanceHistory);
        $score += min($similarEventsAttended * 3, 10); // Max 10 points
        
        // Membership relevance (15% weight)
        if (!empty($memberships)) {
            $primaryMembership = $memberships[0];
            if (self::isEventRelevantToMembership($event, $primaryMembership)) {
                $score += 5;
            }
        }
        
        // Recency bonus for new events
        if (strtotime($event['start_date']) > strtotime('+14 days')) {
            $score += 2; // Small bonus for planning ahead
        }
        
        return min($score, 25); // Cap at 25 points
    }
    
    /**
     * Extract categories from event data
     */
    private static function extractEventCategories($event) {
        $categories = [];
        $title = strtolower($event['title'] ?? '');
        $description = strtolower($event['description'] ?? '');
        $combinedText = $title . ' ' . $description;
        
        // Category keywords
        $categoryKeywords = [
            'service' => ['service', 'worship', 'sunday', 'mass'],
            'fellowship' => ['fellowship', 'coffee', 'lunch', 'dinner', 'social'],
            'training' => ['training', 'class', 'workshop', 'seminar', 'study'],
            'outreach' => ['outreach', 'mission', 'evangelism', 'community', 'help'],
            'leadership' => ['leadership', 'board', 'committee', 'meeting', 'planning'],
            'youth' => ['youth', 'teen', 'children', 'kids', 'family'],
            'music' => ['choir', 'music', 'concert', 'worship_team'],
            'prayer' => ['prayer', 'intercession', 'watchnight'],
            'special' => ['conference', 'retreat', 'conference', 'special_event']
        ];
        
        foreach ($categoryKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($combinedText, $keyword) !== false) {
                    $categories[] = $category;
                    break; // Only count each category once
                }
            }
        }
        
        return !empty($categories) ? $categories : ['general'];
    }
    
    /**
     * Count similar events user has attended
     */
    private static function countSimilarEventsAttended($event, $attendanceHistory) {
        $count = 0;
        $eventCategories = self::extractEventCategories($event);
        
        foreach ($attendanceHistory as $attendance) {
            $attendedCategories = self::extractEventCategories([
                'title' => $attendance['event_title'] ?? '',
                'description' => $attendance['notes'] ?? ''
            ]);
            
            if (!empty(array_intersect($eventCategories, $attendedCategories))) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Check if event is relevant to membership
     */
    private static function isEventRelevantToMembership($event, $membership) {
        $membershipType = $membership['membership_type'];
        $eventTitle = strtolower($event['title'] ?? '');
        
        switch ($membershipType) {
            case 'pastor':
                return strpos($eventTitle, 'leadership') !== false || 
                       strpos($eventTitle, 'pastor') !== false ||
                       strpos($eventTitle, 'teaching') !== false;
            case 'elder':
                return strpos($eventTitle, 'counseling') !== false ||
                       strpos($eventTitle, 'spiritual') !== false;
            case 'deacon':
                return strpos($eventTitle, 'service') !== false ||
                       strpos($eventTitle, 'community') !== false;
            default:
                return true; // General relevance
        }
    }
    
    /**
     * Get human-readable reasons for scoring
     */
    private static function getScoringReasons($event, $interests, $attendanceHistory) {
        $reasons = [];
        
        $eventCategories = self::extractEventCategories($event);
        $matchingCategories = array_intersect($eventCategories, $interests['categories']);
        
        if (!empty($matchingCategories)) {
            $reasons[] = 'Matches your interests: ' . implode(', ', $matchingCategories);
        }
        
        $similarCount = self::countSimilarEventsAttended($event, $attendanceHistory);
        if ($similarCount > 0) {
            $reasons[] = "You've enjoyed similar events before";
        }
        
        return $reasons;
    }
    
    /**
     * Predict event attendance
     */
    public static function predictAttendance($eventId) {
        $projectModel = new Project();
        $attendanceModel = new Attendance();
        $membershipModel = new Membership();
        
        $prediction = [
            'expected_attendance' => 0,
            'confidence' => 0,
            'factors' => []
        ];
        
        try {
            $event = $projectModel->find($eventId);
            if (!$event) {
                return $prediction;
            }
            
            // Get historical data for similar events
            $similarEvents = self::findSimilarEvents($event);
            $historicalAverage = self::calculateHistoricalAverage($similarEvents);
            
            // Get current member base
            $activeMembers = $membershipModel->getActiveMemberships();
            $totalMembers = count($activeMembers);
            
            // Base prediction
            $prediction['expected_attendance'] = max(1, round($totalMembers * 0.3)); // Assume 30% baseline
            
            // Adjust based on historical data
            if ($historicalAverage > 0) {
                $prediction['expected_attendance'] = round(($prediction['expected_attendance'] + $historicalAverage) / 2);
                $prediction['factors'][] = "Based on similar event history";
            }
            
            // Adjust for event characteristics
            $adjustments = self::calculateEventAdjustments($event);
            $prediction['expected_attendance'] = round($prediction['expected_attendance'] * $adjustments['multiplier']);
            
            foreach ($adjustments['reasons'] as $reason) {
                $prediction['factors'][] = $reason;
            }
            
            // Confidence calculation
            $prediction['confidence'] = min(95, 60 + (count($similarEvents) * 5));
            
        } catch (\Exception $e) {
            error_log('Error predicting attendance: ' . $e->getMessage());
        }
        
        return $prediction;
    }
    
    /**
     * Find similar past events
     */
    private static function findSimilarEvents($event) {
        // This would query past events with similar characteristics
        // For now, returning empty array
        return [];
    }
    
    /**
     * Calculate historical average attendance
     */
    private static function calculateHistoricalAverage($events) {
        if (empty($events)) {
            return 0;
        }
        
        // Would calculate average from historical data
        return 25; // Placeholder
    }
    
    /**
     * Calculate event-specific adjustments
     */
    private static function calculateEventAdjustments($event) {
        $multiplier = 1.0;
        $reasons = [];
        
        $title = strtolower($event['title'] ?? '');
        $description = strtolower($event['description'] ?? '');
        $daysUntil = max(0, (strtotime($event['start_date']) - time()) / 86400);
        
        // Time factor - closer events get higher attendance
        if ($daysUntil <= 7) {
            $multiplier *= 1.3;
            $reasons[] = "Event is coming up soon";
        } elseif ($daysUntil > 30) {
            $multiplier *= 0.7;
            $reasons[] = "Event is far in the future";
        }
        
        // Special event boost
        if (strpos($title, 'special') !== false || strpos($title, 'conference') !== false) {
            $multiplier *= 1.5;
            $reasons[] = "Special event expected to draw larger crowd";
        }
        
        // Weekend vs weekday
        $eventDate = new \DateTime($event['start_date']);
        if ($eventDate->format('N') >= 6) { // Saturday or Sunday
            $multiplier *= 1.2;
            $reasons[] = "Weekend event typically has higher attendance";
        }
        
        return [
            'multiplier' => $multiplier,
            'reasons' => $reasons
        ];
    }
    
    /**
     * Get personalized event calendar for user
     */
    public static function getPersonalizedCalendar($userId, $daysAhead = 30) {
        $recommendedEvents = self::recommendEvents($userId, 10);
        $calendar = [];
        
        foreach ($recommendedEvents as $recommendation) {
            $event = $recommendation['event'];
            $startDate = strtotime($event['start_date']);
            
            if ($startDate <= strtotime("+$daysAhead days")) {
                $calendar[] = [
                    'event' => $event,
                    'recommendation_score' => $recommendation['score'],
                    'reasons' => $recommendation['reasons'],
                    'predicted_attendance' => self::predictAttendance($event['id'])
                ];
            }
        }
        
        // Sort by date
        usort($calendar, function($a, $b) {
            return strtotime($a['event']['start_date']) - strtotime($b['event']['start_date']);
        });
        
        return $calendar;
    }
}