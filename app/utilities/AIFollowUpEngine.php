<?php
namespace App\Utilities;

use App\Models\Membership;
use App\Models\FollowUp;
use App\Models\Attendance;
use App\Models\Report;

class AIFollowUpEngine {
    
    /**
     * Analyze member data and generate follow-up recommendations
     */
    public static function generateFollowUps($memberId = null) {
        $recommendations = [];
        $membershipModel = new Membership();
        $followUpModel = new FollowUp();
        
        try {
            if ($memberId) {
                // Analyze specific member
                $recommendations = self::analyzeSingleMember($memberId);
            } else {
                // Analyze all members
                $members = $membershipModel->getActiveMemberships();
                foreach ($members as $member) {
                    $memberRecommendations = self::analyzeSingleMember($member['user_id']);
                    if (!empty($memberRecommendations)) {
                        $recommendations[$member['user_id']] = $memberRecommendations;
                    }
                }
            }
            
        } catch (\Exception $e) {
            error_log('Error in AIFollowUpEngine::generateFollowUps: ' . $e->getMessage());
        }
        
        return $recommendations;
    }
    
    /**
     * Analyze a single member and generate recommendations
     */
    private static function analyzeSingleMember($memberId) {
        $recommendations = [];
        $membershipModel = new Membership();
        $followUpModel = new FollowUp();
        $attendanceModel = new Attendance();
        $reportModel = new Report();
        
        try {
            // Get member data
            $memberships = $membershipModel->getByUserId($memberId);
            if (empty($memberships)) {
                return $recommendations;
            }
            
            $membership = $memberships[0]; // Get primary membership
            
            // Check engagement score
            $engagementScore = $membership['engagement_score'] ?? 0;
            
            // Low engagement recommendations
            if ($engagementScore < 30) {
                $recommendations[] = [
                    'type' => 'engagement_boost',
                    'priority' => 'high',
                    'reason' => 'Low engagement score (' . $engagementScore . ')',
                    'action' => 'Schedule personal visitation',
                    'days_from_now' => 3
                ];
            } elseif ($engagementScore < 50) {
                $recommendations[] = [
                    'type' => 'engagement_check',
                    'priority' => 'medium',
                    'reason' => 'Moderate engagement score (' . $engagementScore . ')',
                    'action' => 'Send encouraging message',
                    'days_from_now' => 7
                ];
            }
            
            // Check attendance patterns
            $recentAttendance = $attendanceModel->getUserAttendance($memberId, 30);
            $attendanceCount = count($recentAttendance);
            
            if ($attendanceCount === 0) {
                $recommendations[] = [
                    'type' => 'missing_member',
                    'priority' => 'high',
                    'reason' => 'No attendance in last 30 days',
                    'action' => 'Immediate follow-up required',
                    'days_from_now' => 1
                ];
            } elseif ($attendanceCount < 3) {
                $recommendations[] = [
                    'type' => 'infrequent_attender',
                    'priority' => 'medium',
                    'reason' => 'Low attendance (' . $attendanceCount . ' times in 30 days)',
                    'action' => 'Check-in call or message',
                    'days_from_now' => 5
                ];
            }
            
            // Check for new convert status
            if ($membership['membership_type'] === 'member' && 
                $membership['join_date'] && 
                strtotime($membership['join_date']) > strtotime('-60 days')) {
                
                $recommendations[] = [
                    'type' => 'new_convert_nurture',
                    'priority' => 'medium',
                    'reason' => 'Recently joined congregation',
                    'action' => 'Discipleship follow-up',
                    'days_from_now' => 10
                ];
            }
            
            // Check tithe status
            if ($membership['tithe_status'] === 'irregular' || $membership['tithe_status'] === 'non_tither') {
                $recommendations[] = [
                    'type' => 'financial_discussion',
                    'priority' => 'low',
                    'reason' => 'Irregular tithing pattern',
                    'action' => 'Gentle encouragement discussion',
                    'days_from_now' => 14
                ];
            }
            
            // Check for overdue follow-ups
            $overdueFollowUps = $followUpModel->getOverdueFollowUps();
            foreach ($overdueFollowUps as $followUp) {
                if ($followUp['member_id'] == $memberId) {
                    $recommendations[] = [
                        'type' => 'overdue_followup',
                        'priority' => 'urgent',
                        'reason' => 'Overdue follow-up from ' . date('M j', strtotime($followUp['due_date'])),
                        'action' => 'Complete overdue follow-up immediately',
                        'days_from_now' => 0
                    ];
                    break; // Only show one overdue notice
                }
            }
            
        } catch (\Exception $e) {
            error_log('Error analyzing member ' . $memberId . ': ' . $e->getMessage());
        }
        
        return $recommendations;
    }
    
    /**
     * Predict member engagement risk
     */
    public static function predictEngagementRisk($memberId) {
        $riskFactors = [];
        $riskLevel = 'low'; // low, medium, high
        $membershipModel = new Membership();
        $attendanceModel = new Attendance();
        
        try {
            $memberships = $membershipModel->getByUserId($memberId);
            if (empty($memberships)) {
                return ['level' => 'unknown', 'factors' => []];
            }
            
            $membership = $memberships[0];
            
            // Factor 1: Attendance frequency (40% weight)
            $recentAttendance = $attendanceModel->getUserAttendance($memberId, 90);
            $attendanceCount = count($recentAttendance);
            
            if ($attendanceCount === 0) {
                $riskFactors['attendance'] = 'critical - no attendance in 90 days';
                $riskLevel = 'high';
            } elseif ($attendanceCount < 4) {
                $riskFactors['attendance'] = 'concerning - low attendance (' . $attendanceCount . ' times)';
                if ($riskLevel !== 'high') $riskLevel = 'medium';
            } elseif ($attendanceCount < 8) {
                $riskFactors['attendance'] = 'monitoring - moderate attendance';
                if ($riskLevel === 'low') $riskLevel = 'medium';
            } else {
                $riskFactors['attendance'] = 'healthy - good attendance pattern';
            }
            
            // Factor 2: Engagement score (30% weight)
            $engagementScore = $membership['engagement_score'] ?? 0;
            
            if ($engagementScore < 25) {
                $riskFactors['engagement_score'] = 'very low (' . $engagementScore . ')';
                $riskLevel = 'high';
            } elseif ($engagementScore < 50) {
                $riskFactors['engagement_score'] = 'low (' . $engagementScore . ')';
                if ($riskLevel !== 'high') $riskLevel = 'medium';
            } elseif ($engagementScore < 75) {
                $riskFactors['engagement_score'] = 'moderate (' . $engagementScore . ')';
                if ($riskLevel === 'low') $riskLevel = 'medium';
            } else {
                $riskFactors['engagement_score'] = 'good (' . $engagementScore . ')';
            }
            
            // Factor 3: Membership duration (20% weight)
            if ($membership['join_date']) {
                $joinDate = new \DateTime($membership['join_date']);
                $now = new \DateTime();
                $interval = $now->diff($joinDate);
                $months = ($interval->y * 12) + $interval->m;
                
                if ($months < 3) {
                    $riskFactors['tenure'] = 'new member (' . $months . ' months)';
                    // New members naturally have lower engagement initially
                } elseif ($months > 24 && $engagementScore < 40) {
                    $riskFactors['tenure'] = 'long-term low engagement (' . $months . ' months)';
                    if ($riskLevel !== 'high') $riskLevel = 'medium';
                } else {
                    $riskFactors['tenure'] = 'stable (' . $months . ' months)';
                }
            }
            
            // Factor 4: Tithe status (10% weight)
            if ($membership['tithe_status'] === 'non_tither') {
                $riskFactors['tithe'] = 'non-tither';
                if ($riskLevel !== 'high') $riskLevel = 'medium';
            } elseif ($membership['tithe_status'] === 'irregular') {
                $riskFactors['tithe'] = 'irregular tither';
                if ($riskLevel === 'low') $riskLevel = 'medium';
            } else {
                $riskFactors['tithe'] = 'regular tither';
            }
            
        } catch (\Exception $e) {
            error_log('Error predicting engagement risk for member ' . $memberId . ': ' . $e->getMessage());
            return ['level' => 'unknown', 'factors' => ['error' => 'Analysis failed']];
        }
        
        return [
            'level' => $riskLevel,
            'factors' => $riskFactors,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Automated follow-up scheduling
     */
    public static function scheduleFollowUps($criteria = []) {
        $scheduled = 0;
        $followUpModel = new FollowUp();
        
        try {
            // Run behavioral analysis to generate automatic follow-ups
            $followUpModel->generateBehavioralFollowUps();
            $scheduled++;
            
            // Additional criteria-based scheduling could go here
            if (!empty($criteria)) {
                // Custom scheduling logic based on criteria
            }
            
        } catch (\Exception $e) {
            error_log('Error in AIFollowUpEngine::scheduleFollowUps: ' . $e->getMessage());
        }
        
        return $scheduled;
    }
    
    /**
     * Get priority follow-up list for staff
     */
    public static function getPriorityFollowUpList($staffId = null, $limit = 10) {
        $followUpModel = new FollowUp();
        $priorityList = [];
        
        try {
            $pendingFollowUps = $followUpModel->getPendingFollowUps($staffId);
            
            // Sort by priority and due date
            usort($pendingFollowUps, function($a, $b) {
                $priorityOrder = ['urgent' => 4, 'high' => 3, 'medium' => 2, 'low' => 1];
                $priorityA = $priorityOrder[$a['priority']] ?? 0;
                $priorityB = $priorityOrder[$b['priority']] ?? 0;
                
                if ($priorityA !== $priorityB) {
                    return $priorityB - $priorityA; // Higher priority first
                }
                
                // Same priority - sort by due date
                return strtotime($a['due_date']) - strtotime($b['due_date']);
            });
            
            // Limit results
            $priorityList = array_slice($pendingFollowUps, 0, $limit);
            
        } catch (\Exception $e) {
            error_log('Error getting priority follow-up list: ' . $e->getMessage());
        }
        
        return $priorityList;
    }
}