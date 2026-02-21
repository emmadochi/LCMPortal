<?php
namespace App\Controllers;

use App\Models\Membership;
use App\Models\FollowUp;
use App\Models\User;
use App\Models\Unit;
use App\Utilities\AIFollowUpEngine;
use App\Utilities\EventRecommendationEngine;

class MemberInsightsController extends BaseController {
    
    private $membershipModel;
    private $followUpModel;
    private $userModel;
    private $unitModel;
    
    public function __construct() {
        parent::__construct();
        $this->membershipModel = new Membership();
        $this->followUpModel = new FollowUp();
        $this->userModel = new User();
        $this->unitModel = new Unit();
        
        // Check permission - this should be accessible to directors and admins
        $this->authorize('view_dashboard'); // Using existing dashboard permission
    }
    
    /**
     * Main insights dashboard
     */
    public function dashboard() {
        $unitId = $this->request->get('unit_id', null);
        if ($unitId) {
            $unitId = (int)$unitId;
        }
        
        // Get comprehensive insights data
        $insightsData = $this->compileInsightsData($unitId);
        
        $this->render('member-insights/dashboard', [
            'title' => 'Member Insights Dashboard',
            'pageTitle' => 'Member Insights & Analytics',
            'insights' => $insightsData,
            'selectedUnit' => $unitId,
            'units' => $this->unitModel->getActiveUnits()
        ]);
    }
    
    /**
     * Detailed engagement report
     */
    public function engagementReport() {
        $unitId = $this->request->get('unit_id', null);
        if ($unitId) {
            $unitId = (int)$unitId;
        }
        
        $engagementData = $this->getDetailedEngagementData($unitId);
        
        $this->render('member-insights/engagement-report', [
            'title' => 'Engagement Report',
            'pageTitle' => 'Member Engagement Analysis',
            'engagementData' => $engagementData,
            'selectedUnit' => $unitId,
            'units' => $this->unitModel->getActiveUnits()
        ]);
    }
    
    /**
     * Predictive analytics
     */
    public function predictions() {
        $unitId = $this->request->get('unit_id', null);
        if ($unitId) {
            $unitId = (int)$unitId;
        }
        
        $predictionData = $this->getPredictionData($unitId);
        
        $this->render('member-insights/predictions', [
            'title' => 'Predictive Analytics',
            'pageTitle' => 'Future Trends & Predictions',
            'predictions' => $predictionData,
            'selectedUnit' => $unitId,
            'units' => $this->unitModel->getActiveUnits()
        ]);
    }
    
    /**
     * AI recommendations
     */
    public function recommendations() {
        $unitId = $this->request->get('unit_id', null);
        if ($unitId) {
            $unitId = (int)$unitId;
        }
        
        $recommendationData = $this->getRecommendationData($unitId);
        
        $this->render('member-insights/recommendations', [
            'title' => 'AI Recommendations',
            'pageTitle' => 'Actionable Recommendations',
            'recommendations' => $recommendationData,
            'selectedUnit' => $unitId,
            'units' => $this->unitModel->getActiveUnits()
        ]);
    }
    
    /**
     * Compile all insights data for dashboard
     */
    private function compileInsightsData($unitId = null) {
        $data = [
            'membership_stats' => [],
            'engagement_metrics' => [],
            'follow_up_insights' => [],
            'risk_assessment' => [],
            'trends' => []
        ];
        
        try {
            // Membership statistics
            $data['membership_stats'] = $this->membershipModel->getMembershipStats($unitId);
            
            // Engagement metrics
            $data['engagement_metrics'] = $this->getEngagementMetrics($unitId);
            
            // Follow-up insights
            $data['follow_up_insights'] = $this->getFollowUpInsights($unitId);
            
            // Risk assessment
            $data['risk_assessment'] = $this->getRiskAssessment($unitId);
            
            // Trends data
            $data['trends'] = $this->getTrendData($unitId);
            
        } catch (\Exception $e) {
            error_log('Error compiling insights data: ' . $e->getMessage());
        }
        
        return $data;
    }
    
    /**
     * Get engagement metrics
     */
    private function getEngagementMetrics($unitId = null) {
        $metrics = [
            'average_score' => 0,
            'highly_engaged' => 0,
            'moderately_engaged' => 0,
            'low_engagement' => 0,
            'engagement_distribution' => []
        ];
        
        try {
            $members = $this->membershipModel->getActiveMemberships($unitId);
            
            if (empty($members)) {
                return $metrics;
            }
            
            $scores = array_column($members, 'engagement_score');
            $metrics['average_score'] = round(array_sum($scores) / count($scores), 2);
            
            foreach ($scores as $score) {
                if ($score >= 75) {
                    $metrics['highly_engaged']++;
                } elseif ($score >= 40) {
                    $metrics['moderately_engaged']++;
                } else {
                    $metrics['low_engagement']++;
                }
            }
            
            // Distribution data for charts
            $distribution = [
                ['range' => '0-25', 'count' => 0],
                ['range' => '26-50', 'count' => 0],
                ['range' => '51-75', 'count' => 0],
                ['range' => '76-100', 'count' => 0]
            ];
            
            foreach ($scores as $score) {
                if ($score <= 25) {
                    $distribution[0]['count']++;
                } elseif ($score <= 50) {
                    $distribution[1]['count']++;
                } elseif ($score <= 75) {
                    $distribution[2]['count']++;
                } else {
                    $distribution[3]['count']++;
                }
            }
            
            $metrics['engagement_distribution'] = $distribution;
            
        } catch (\Exception $e) {
            error_log('Error getting engagement metrics: ' . $e->getMessage());
        }
        
        return $metrics;
    }
    
    /**
     * Get follow-up insights
     */
    private function getFollowUpInsights($unitId = null) {
        $insights = [
            'pending_count' => 0,
            'overdue_count' => 0,
            'completion_rate' => 0,
            'priority_breakdown' => [],
            'type_breakdown' => []
        ];
        
        try {
            $stats = $this->followUpModel->getFollowUpStats($unitId);
            
            $insights['pending_count'] = $stats['total_pending'];
            $insights['overdue_count'] = $stats['total_overdue'];
            $insights['priority_breakdown'] = $stats['by_priority'];
            $insights['type_breakdown'] = $stats['by_type'];
            
            $total = $stats['total_pending'] + $stats['total_completed'];
            if ($total > 0) {
                $insights['completion_rate'] = round(($stats['total_completed'] / $total) * 100, 1);
            }
            
        } catch (\Exception $e) {
            error_log('Error getting follow-up insights: ' . $e->getMessage());
        }
        
        return $insights;
    }
    
    /**
     * Get risk assessment data
     */
    private function getRiskAssessment($unitId = null) {
        $assessment = [
            'high_risk_members' => [],
            'moderate_risk_members' => [],
            'risk_factors' => [],
            'intervention_opportunities' => 0
        ];
        
        try {
            // Get members with low engagement scores
            $lowEngagementMembers = $this->membershipModel->getLowEngagementMembers(35, $unitId);
            
            foreach ($lowEngagementMembers as $member) {
                $riskData = AIFollowUpEngine::predictEngagementRisk($member['user_id']);
                
                if ($riskData['level'] === 'high') {
                    $assessment['high_risk_members'][] = [
                        'member' => $member,
                        'risk_data' => $riskData
                    ];
                } elseif ($riskData['level'] === 'medium') {
                    $assessment['moderate_risk_members'][] = [
                        'member' => $member,
                        'risk_data' => $riskData
                    ];
                }
            }
            
            // Identify intervention opportunities
            $assessment['intervention_opportunities'] = count($assessment['high_risk_members']) + 
                                                      count($assessment['moderate_risk_members']);
            
            // Common risk factors
            $assessment['risk_factors'] = $this->identifyCommonRiskFactors($lowEngagementMembers);
            
        } catch (\Exception $e) {
            error_log('Error getting risk assessment: ' . $e->getMessage());
        }
        
        return $assessment;
    }
    
    /**
     * Identify common risk factors among low-engagement members
     */
    private function identifyCommonRiskFactors($members) {
        $factors = [
            'new_members' => 0,
            'irregular_attendance' => 0,
            'non_tithers' => 0,
            'isolated_members' => 0
        ];
        
        foreach ($members as $member) {
            // Check if new member (joined < 90 days ago)
            if ($member['join_date'] && strtotime($member['join_date']) > strtotime('-90 days')) {
                $factors['new_members']++;
            }
            
            // Check tithe status
            if ($member['tithe_status'] === 'non_tither' || $member['tithe_status'] === 'irregular') {
                $factors['non_tithers']++;
            }
            
            // Could add more sophisticated isolation detection here
        }
        
        return $factors;
    }
    
    /**
     * Get trend data for charts
     */
    private function getTrendData($unitId = null) {
        $trends = [
            'engagement_trend' => [],
            'membership_growth' => [],
            'attendance_trend' => []
        ];
        
        try {
            // Engagement trend over last 6 months
            $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));
            
            $db = \App\Core\Database::getInstance();
            
            // Monthly engagement averages
            $sql = "SELECT DATE_FORMAT(m.created_at, '%Y-%m') as month, AVG(m.engagement_score) as avg_score
                    FROM memberships m";
            
            if ($unitId) {
                $sql .= " WHERE m.unit_id = ?";
            }
            
            $sql .= " GROUP BY month ORDER BY month ASC LIMIT 6";
            
            $stmt = $db->prepare($sql);
            if ($unitId) {
                $stmt->bind_param("i", $unitId);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $trends['engagement_trend'][] = [
                    'month' => date('M Y', strtotime($row['month'] . '-01')),
                    'score' => round($row['avg_score'], 2)
                ];
            }
            
        } catch (\Exception $e) {
            error_log('Error getting trend data: ' . $e->getMessage());
        }
        
        return $trends;
    }
    
    /**
     * Get detailed engagement data
     */
    private function getDetailedEngagementData($unitId = null) {
        $data = [
            'individual_scores' => [],
            'behavioral_patterns' => [],
            'improvement_areas' => []
        ];
        
        try {
            $members = $this->membershipModel->getActiveMemberships($unitId);
            
            foreach ($members as $member) {
                $user = $this->userModel->find($member['user_id']);
                if ($user) {
                    $data['individual_scores'][] = [
                        'user' => $user,
                        'membership' => $member,
                        'engagement_score' => $member['engagement_score']
                    ];
                }
            }
            
            // Sort by engagement score
            usort($data['individual_scores'], function($a, $b) {
                return $b['engagement_score'] - $a['engagement_score'];
            });
            
        } catch (\Exception $e) {
            error_log('Error getting detailed engagement data: ' . $e->getMessage());
        }
        
        return $data;
    }
    
    /**
     * Get prediction data
     */
    private function getPredictionData($unitId = null) {
        $predictions = [
            'engagement_forecast' => [],
            'membership_trends' => [],
            'risk_predictions' => []
        ];
        
        // Placeholder for predictive analytics
        // In a real implementation, this would use ML models
        
        return $predictions;
    }
    
    /**
     * Get recommendation data
     */
    private function getRecommendationData($unitId = null) {
        $recommendations = [
            'immediate_actions' => [],
            'strategic_initiatives' => [],
            'resource_needs' => []
        ];
        
        try {
            // Get AI-generated follow-up recommendations
            $aiRecommendations = AIFollowUpEngine::generateFollowUps();
            
            // Get event recommendations
            $eventRecommendations = [];
            $activeMembers = $this->membershipModel->getActiveMemberships($unitId);
            
            foreach (array_slice($activeMembers, 0, 5) as $member) { // Sample first 5 members
                $eventRecs = EventRecommendationEngine::recommendEvents($member['user_id'], 2);
                if (!empty($eventRecs)) {
                    $eventRecommendations[] = [
                        'member_id' => $member['user_id'],
                        'recommendations' => $eventRecs
                    ];
                }
            }
            
            $recommendations['immediate_actions'] = array_slice($aiRecommendations, 0, 10);
            $recommendations['strategic_initiatives'] = $eventRecommendations;
            
        } catch (\Exception $e) {
            error_log('Error getting recommendation data: ' . $e->getMessage());
        }
        
        return $recommendations;
    }
    
    /**
     * API endpoint for getting insights data (AJAX)
     */
    public function getInsightsData() {
        $unitId = $this->request->get('unit_id', null);
        if ($unitId) {
            $unitId = (int)$unitId;
        }
        
        $insightsData = $this->compileInsightsData($unitId);
        $this->json($insightsData);
    }
    
    /**
     * Export insights report
     */
    public function exportReport() {
        $format = $this->request->get('format', 'pdf');
        $unitId = $this->request->get('unit_id', null);
        if ($unitId) {
            $unitId = (int)$unitId;
        }
        
        $insightsData = $this->compileInsightsData($unitId);
        
        // This would integrate with export utilities
        // For now, returning JSON
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="member-insights-' . date('Y-m-d') . '.json"');
        echo json_encode($insightsData, JSON_PRETTY_PRINT);
        exit;
    }
}