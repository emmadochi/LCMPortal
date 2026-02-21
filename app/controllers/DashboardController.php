<?php
namespace App\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Models\Report;
use App\Models\Attendance;
use App\Models\FinanceRecord;
use App\Models\ActivityLog;
use App\Models\FollowUp;

class DashboardController extends BaseController {
    public function index() {
        $unitModel = new Unit();
        $userModel = new User();
        
        // Get basic statistics
        $totalUnits = 0;
        $totalUsers = 0;
        
        try {
            $totalUnits = $unitModel->count(['status' => 'active']);
        } catch (\Exception $e) {
            error_log("DashboardController: Error getting unit count: " . $e->getMessage());
        }
        
        try {
            $totalUsers = $userModel->count(['status' => 'active']);
        } catch (\Exception $e) {
            error_log("DashboardController: Error getting user count: " . $e->getMessage());
        }
        
        // Get reports statistics
        $totalReports = 0;
        $reportsByMonth = [];
        try {
            $reportModel = new Report();
            $totalReports = $reportModel->count();
            
            // Get reports by month for last 6 months
            $db = \App\Core\Database::getInstance();
            $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
                    FROM reports 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    GROUP BY month 
                    ORDER BY month ASC";
            $result = $db->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $reportsByMonth[] = [
                        'month' => date('M Y', strtotime($row['month'] . '-01')),
                        'count' => (int)$row['count']
                    ];
                }
            }
        } catch (\Exception $e) {
            // Reports table might not exist
        }
        
        // Get attendance statistics
        $totalAttendance = 0;
        $attendanceByMonth = [];
        try {
            $attendanceModel = new Attendance();
            $totalAttendance = $attendanceModel->count();
            
            // Get attendance by month for last 6 months
            $db = \App\Core\Database::getInstance();
            $sql = "SELECT DATE_FORMAT(event_date, '%Y-%m') as month, COUNT(*) as count 
                    FROM attendance 
                    WHERE event_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    GROUP BY month 
                    ORDER BY month ASC";
            $result = $db->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $attendanceByMonth[] = [
                        'month' => date('M Y', strtotime($row['month'] . '-01')),
                        'count' => (int)$row['count']
                    ];
                }
            }
        } catch (\Exception $e) {
            // Attendance table might not exist
        }
        
        // Get finance statistics
        $financeSummary = ['income' => 0, 'expense' => 0];
        $financeByMonth = [];
        try {
            $financeModel = new FinanceRecord();
            $db = \App\Core\Database::getInstance();
            
            // Get summary
            $sql = "SELECT transaction_type, SUM(amount) as total 
                    FROM finance_records 
                    GROUP BY transaction_type";
            $result = $db->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $financeSummary[$row['transaction_type']] = (float)$row['total'];
                }
            }
            
            // Get finance by month for last 6 months
            $sql = "SELECT DATE_FORMAT(transaction_date, '%Y-%m') as month, 
                           transaction_type, SUM(amount) as total 
                    FROM finance_records 
                    WHERE transaction_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    GROUP BY month, transaction_type 
                    ORDER BY month ASC";
            $result = $db->query($sql);
            if ($result) {
                $monthlyData = [];
                while ($row = $result->fetch_assoc()) {
                    $month = date('M Y', strtotime($row['month'] . '-01'));
                    if (!isset($monthlyData[$month])) {
                        $monthlyData[$month] = ['income' => 0, 'expense' => 0];
                    }
                    $monthlyData[$month][$row['transaction_type']] = (float)$row['total'];
                }
                $financeByMonth = $monthlyData;
            }
        } catch (\Exception $e) {
            // Finance table might not exist
        }
        
        // Get recent units
        $recentUnits = [];
        try {
            $recentUnits = $unitModel->findAll(['status' => 'active'], 'created_at DESC', 5);
        } catch (\Exception $e) {
            error_log("DashboardController: Error getting recent units: " . $e->getMessage());
        }

        // Get recent activity logs (for dashboard widget)
        $recentActivityLogs = [];
        try {
            $activityLogModel = new ActivityLog();
            $recentActivityLogs = $activityLogModel->getLogsWithDetails([], 'created_at DESC', 15);
        } catch (\Exception $e) {
            // activity_logs table might not exist
        }

        // Follow-ups assigned to the current user (who they are to follow up + details)
        $myFollowUps = [];
        $currentUserId = $this->session->get('user_id');
        if ($currentUserId) {
            try {
                $followUpModel = new FollowUp();
                $list = $followUpModel->getFollowUpsWithDetails([
                    'assigned_to' => $currentUserId,
                    'status' => 'pending'
                ]);
                $today = date('Y-m-d');
                foreach ($list as $f) {
                    if (!empty($f['due_date']) && $f['due_date'] < $today) {
                        $f['status'] = 'overdue';
                    }
                    $myFollowUps[] = $f;
                }
                usort($myFollowUps, function ($a, $b) {
                    $d1 = $a['due_date'] ?? '9999-99-99';
                    $d2 = $b['due_date'] ?? '9999-99-99';
                    return strcmp($d1, $d2);
                });
                $myFollowUps = array_slice($myFollowUps, 0, 10);
            } catch (\Exception $e) {
                // follow_ups table might not exist
            }
        }
        
        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'pageTitle' => 'Dashboard',
            'totalUnits' => $totalUnits,
            'totalUsers' => $totalUsers,
            'totalReports' => $totalReports,
            'totalAttendance' => $totalAttendance,
            'reportsByMonth' => $reportsByMonth,
            'attendanceByMonth' => $attendanceByMonth,
            'financeSummary' => $financeSummary,
            'financeByMonth' => $financeByMonth,
            'recentUnits' => $recentUnits,
            'recentActivityLogs' => $recentActivityLogs,
            'myFollowUps' => $myFollowUps
        ]);
    }
}

