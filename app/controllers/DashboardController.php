<?php
namespace App\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Models\Church;
use App\Models\Report;
use App\Models\Attendance;
use App\Models\FinanceRecord;
use App\Models\ActivityLog;
use App\Models\FollowUp;
use App\Models\EvangelismReport;
use App\Models\Notification;

class DashboardController extends BaseController {
    public function index() {
        $unitModel = new Unit();
        $userModel = new User();
        
        $isHeadPastor = $this->session->isHeadPastor();
        $headPastorChurchId = $isHeadPastor ? $this->session->getHeadPastorChurchId() : null;
        
        // Get basic statistics
        $totalChurches = 0;
        $totalMembers = 0;
        $totalUnits = 0;
        $totalUsers = 0;
        
        try {
            $churchModel = new Church();
            if ($isHeadPastor && $headPastorChurchId) {
                $totalChurches = 1;
            } else {
                $totalChurches = $churchModel->count(['status' => 'active']);
                if ($totalChurches === 0) {
                    $totalChurches = $churchModel->count();
                }
            }
        } catch (\Exception $e) {
            error_log("DashboardController: Error getting church count: " . $e->getMessage());
        }

        try {
            if ($isHeadPastor && $headPastorChurchId) {
                $churchModel = new Church();
                $totalUnits = count($churchModel->getChurchUnitIds($headPastorChurchId));
            } else {
                $totalUnits = $unitModel->count(['status' => 'active']);
            }
        } catch (\Exception $e) {
            error_log("DashboardController: Error getting unit count: " . $e->getMessage());
        }
        
        try {
            if ($isHeadPastor && $headPastorChurchId) {
                $churchModel = new Church();
                $totalUsers = count($churchModel->getChurchMemberUsers($headPastorChurchId));
                $totalMembers = $totalUsers;
            } else {
                $totalUsers = $userModel->count(['status' => 'active']);
                $totalMembers = $totalUsers;
            }
        } catch (\Exception $e) {
            error_log("DashboardController: Error getting user count: " . $e->getMessage());
        }
        
        // Get reports statistics
        $totalReports = 0;
        $reportsByMonth = [];
        try {
            $reportModel = new Report();
            $db = \App\Core\Database::getInstance();
            
            if ($isHeadPastor && $headPastorChurchId) {
                $churchModel = new Church();
                $unitIds = $churchModel->getChurchUnitIds($headPastorChurchId);
                if (!empty($unitIds)) {
                    $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
                    $types = str_repeat('i', count($unitIds));
                    
                    // Total scoped reports
                    $stmt = $db->prepare("SELECT COUNT(*) as total FROM reports WHERE unit_id IN ($placeholders)");
                    $stmt->bind_param($types, ...$unitIds);
                    $stmt->execute();
                    $totalReports = (int)$stmt->get_result()->fetch_assoc()['total'];
                    
                    // Reports by month scoped
                    $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
                            FROM reports 
                            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) AND unit_id IN ($placeholders)
                            GROUP BY month 
                            ORDER BY month ASC";
                    $stmtM = $db->prepare($sql);
                    $stmtM->bind_param($types, ...$unitIds);
                    $stmtM->execute();
                    $result = $stmtM->get_result();
                } else {
                    $totalReports = 0;
                    $result = false;
                }
            } else {
                $totalReports = $reportModel->count();
                $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
                        FROM reports 
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                        GROUP BY month 
                        ORDER BY month ASC";
                $result = $db->query($sql);
            }
            
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
            $db = \App\Core\Database::getInstance();
            
            if ($isHeadPastor && $headPastorChurchId) {
                $churchModel = new Church();
                $unitIds = $churchModel->getChurchUnitIds($headPastorChurchId);
                if (!empty($unitIds)) {
                    $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
                    $types = str_repeat('i', count($unitIds));
                    
                    // Total scoped attendance records
                    $stmt = $db->prepare("SELECT COUNT(*) as total FROM attendance WHERE unit_id IN ($placeholders)");
                    $stmt->bind_param($types, ...$unitIds);
                    $stmt->execute();
                    $totalAttendance = (int)$stmt->get_result()->fetch_assoc()['total'];
                    
                    // Attendance by month scoped
                    $sql = "SELECT DATE_FORMAT(event_date, '%Y-%m') as month, COUNT(*) as count 
                            FROM attendance 
                            WHERE event_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH) AND unit_id IN ($placeholders)
                            GROUP BY month 
                            ORDER BY month ASC";
                    $stmtM = $db->prepare($sql);
                    $stmtM->bind_param($types, ...$unitIds);
                    $stmtM->execute();
                    $result = $stmtM->get_result();
                } else {
                    $totalAttendance = 0;
                    $result = false;
                }
            } else {
                $totalAttendance = $attendanceModel->count();
                $sql = "SELECT DATE_FORMAT(event_date, '%Y-%m') as month, COUNT(*) as count 
                        FROM attendance 
                        WHERE event_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                        GROUP BY month 
                        ORDER BY month ASC";
                $result = $db->query($sql);
            }
            
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
            
            if ($isHeadPastor && $headPastorChurchId) {
                $churchModel = new Church();
                $unitIds = $churchModel->getChurchUnitIds($headPastorChurchId);
                if (!empty($unitIds)) {
                    $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
                    $types = str_repeat('i', count($unitIds));
                    
                    // Get scoped summary
                    $stmtS = $db->prepare("SELECT transaction_type, SUM(amount) as total 
                                            FROM finance_records 
                                            WHERE unit_id IN ($placeholders)
                                            GROUP BY transaction_type");
                    $stmtS->bind_param($types, ...$unitIds);
                    $stmtS->execute();
                    $resultS = $stmtS->get_result();
                    if ($resultS) {
                        while ($row = $resultS->fetch_assoc()) {
                            $financeSummary[$row['transaction_type']] = (float)$row['total'];
                        }
                    }
                    
                    // Get scoped finance by month for last 6 months
                    $sql = "SELECT DATE_FORMAT(transaction_date, '%Y-%m') as month, 
                                   transaction_type, SUM(amount) as total 
                            FROM finance_records 
                            WHERE transaction_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH) AND unit_id IN ($placeholders)
                            GROUP BY month, transaction_type 
                            ORDER BY month ASC";
                    $stmtM = $db->prepare($sql);
                    $stmtM->bind_param($types, ...$unitIds);
                    $stmtM->execute();
                    $result = $stmtM->get_result();
                } else {
                    $result = false;
                }
            } else {
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
            }
            
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
            if ($isHeadPastor && $headPastorChurchId) {
                $churchModel = new Church();
                $unitIds = $churchModel->getChurchUnitIds($headPastorChurchId);
                if (!empty($unitIds)) {
                    $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
                    $types = str_repeat('i', count($unitIds));
                    $stmt = $db->prepare("SELECT * FROM units WHERE id IN ($placeholders) AND status = 'active' ORDER BY created_at DESC LIMIT 5");
                    $stmt->bind_param($types, ...$unitIds);
                    $stmt->execute();
                    $recentUnits = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                }
            } else {
                $recentUnits = $unitModel->findAll(['status' => 'active'], 'created_at DESC', 5);
            }
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

        // Enhanced role-based data for different pastor types
        $headPastorData = null;
        $directorData = null;
        $pastorData = null;
        $pastorDirectorData = null;
        
        if ($currentUserId) {
            // Regular Pastor data (non-head pastors)
            if ($this->session->get('user_role') === 'pastor' && !$this->session->isHeadPastor()) {
                try {
                    // Get pastor's units and recent reports they have access to
                    $userUnits = $unitModel->getUserUnits($currentUserId);
                    $unitIds = array_column($userUnits, 'id');
                    
                    // Get recent reports across all units the pastor has access to
                    $recentReports = [];
                    if (!empty($unitIds)) {
                        $reportModel = new Report();
                        $recentReports = $reportModel->getReportsByUnitIds($unitIds, 'created_at DESC', 5);
                    }
                    
                    // Get recent attendance data for units
                    $recentAttendance = [];
                    if (!empty($unitIds)) {
                        $attendanceModel = new Attendance();
                        $recentAttendance = $attendanceModel->getRecentAttendanceByUnitIds($unitIds, 5);
                    }
                    
                    $pastorData = [
                        'units' => $userUnits,
                        'recent_reports' => $recentReports,
                        'recent_attendance' => $recentAttendance,
                        'units_count' => count($userUnits)
                    ];
                } catch (\Exception $e) {
                    error_log("DashboardController: Error getting Pastor data: " . $e->getMessage());
                }
            }
            
            // Head Pastor data
            if ($this->session->isHeadPastor()) {
                try {
                    $churchModel = new \App\Models\Church();
                    $headPastorChurchId = $this->session->getHeadPastorChurchId();
                    $church = $churchModel->find($headPastorChurchId);
                    
                    if ($church) {
                        $unitIds = $churchModel->getChurchUnitIds($headPastorChurchId);
                        
                        // Get church stats
                        $membersCount = count($churchModel->getChurchMemberUsers($headPastorChurchId));
                        $unitsCount = count($unitIds);
                        
                        // Get recent finance for this church
                        $recentFinance = [];
                        if (!empty($unitIds)) {
                            $financeModel = new FinanceRecord();
                            $recentFinance = $financeModel->getFinanceWithDetailsByUnitIds(
                                array_slice($unitIds, 0, 5),
                                'f.transaction_date DESC',
                                null,
                                null,
                                5
                            );
                        }
                        
                        $headPastorData = [
                            'church' => $church,
                            'members_count' => $membersCount,
                            'units_count' => $unitsCount,
                            'recent_finance' => $recentFinance
                        ];
                    }
                } catch (\Exception $e) {
                    error_log("DashboardController: Error getting Head Pastor data: " . $e->getMessage());
                }
            }
            
            // Director data
            if ($this->session->isDirector()) {
                try {
                    $userModel = new User();
                    $directorUnits = $this->session->getDirectorUnits();
                    $unitsData = [];
                    $db = \App\Core\Database::getInstance();
                    
                    foreach ($directorUnits as $unit) {
                        // Get unit members count
                        $membersStmt = $db->prepare("SELECT COUNT(*) as count FROM unit_user WHERE unit_id = ?");
                        $membersStmt->bind_param("i", $unit['id']);
                        $membersStmt->execute();
                        $membersResult = $membersStmt->get_result()->fetch_assoc();
                        $membersCount = $membersResult['count'] ?? 0;
                        
                        // Get recent reports for this unit
                        $recentReports = [];
                        $reportsStmt = $db->prepare("SELECT * FROM reports WHERE unit_id = ? ORDER BY created_at DESC LIMIT 3");
                        $reportsStmt->bind_param("i", $unit['id']);
                        $reportsStmt->execute();
                        $recentReports = $reportsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        
                        $unitsData[] = [
                            'unit' => $unit,
                            'members_count' => $membersCount,
                            'recent_reports' => $recentReports
                        ];
                    }
                    
                    $directorData = [
                        'units' => $unitsData,
                        'total_units' => count($directorUnits)
                    ];
                } catch (\Exception $e) {
                    error_log("DashboardController: Error getting Director data: " . $e->getMessage());
                }
            }
            
            // Pastor-Director specific data (pastors who are also unit directors)
            if ($this->session->get('is_pastor_director')) {
                try {
                    // Combine pastor and director data with enhanced features
                    $directorUnits = $this->session->getDirectorUnits();
                    $allPastorReports = [];
                    $allPastorAttendance = [];
                    
                    foreach ($directorUnits as $unit) {
                        // Get reports for units this pastor-director manages
                        $reportModel = new Report();
                        $unitReports = $reportModel->findAll(['unit_id' => $unit['id']], 'created_at DESC', 3);
                        $allPastorReports = array_merge($allPastorReports, $unitReports);
                        
                        // Get attendance for units this pastor-director manages
                        $attendanceModel = new Attendance();
                        $unitAttendance = $attendanceModel->findAll(['unit_id' => $unit['id']], 'event_date DESC', 3);
                        $allPastorAttendance = array_merge($allPastorAttendance, $unitAttendance);
                    }
                    
                    // Sort and limit the combined data
                    usort($allPastorReports, function($a, $b) {
                        return strtotime($b['created_at']) - strtotime($a['created_at']);
                    });
                    usort($allPastorAttendance, function($a, $b) {
                        return strtotime($b['event_date']) - strtotime($a['event_date']);
                    });
                    
                    $pastorDirectorData = [
                        'director_units' => $directorUnits,
                        'recent_reports' => array_slice($allPastorReports, 0, 5),
                        'recent_attendance' => array_slice($allPastorAttendance, 0, 5),
                        'units_count' => count($directorUnits),
                        'role_type' => 'pastor_director'
                    ];
                } catch (\Exception $e) {
                    error_log("DashboardController: Error getting Pastor-Director data: " . $e->getMessage());
                }
            }
        }
        
        if ($this->session->get('user_role') === 'user') {
            $evangelismReportModel = new EvangelismReport();
            $notificationModel = new Notification();
            $userId = (int)$this->session->get('user_id');

            $user = $userModel->find($userId);
            $churchModel = new Church();
            $church = !empty($user['church_id']) ? $churchModel->find($user['church_id']) : null;

            $evangelismReports = $evangelismReportModel->getReportsByUserId($userId) ?: [];
            $totalSoulsWon = 0;
            foreach ($evangelismReports as $er) {
                $totalSoulsWon += (int)($er['souls_won'] ?? 0);
            }

            $notifications = $notificationModel->getUnreadNotifications($userId) ?: [];
            
            // Enhanced Member Data
            $assignedUnits = $userModel->getUnits($userId) ?: [];
            $attendanceSummary = $userModel->getPersonalAttendanceSummary($userId, 6) ?: [];
            $givingSummary = $userModel->getPersonalGivingSummary($userId) ?: ['total' => 0, 'this_year' => 0, 'last_transaction' => null];
            $engagementScore = $userModel->getEngagementScore($userId);
            $aiInsights = $userModel->getAIInsights($userId);
            
            // Pledges summary
            $pledgeModel = new \App\Models\Pledge();
            $myPledges = $pledgeModel->getPledgesByMember($userId) ?: [];
            $activePledgesCount = 0;
            $totalPledged = 0.0;
            $totalPaid = 0.0;
            foreach ($myPledges as $p) {
                $totalPledged += (float)($p['target_amount'] ?? 0);
                $totalPaid += (float)($p['amount_paid'] ?? 0);
                if (($p['status'] ?? '') !== 'fulfilled') {
                    $activePledgesCount++;
                }
            }

            // Monthly attendance trend over past 6 months
            $attendanceTrend = [];
            try {
                $db = \App\Core\Database::getInstance();
                $attSql = "SELECT DATE_FORMAT(event_date, '%b') as month_label, COUNT(*) as cnt 
                           FROM attendance 
                           WHERE user_id = ? AND status = 'present' AND event_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                           GROUP BY DATE_FORMAT(event_date, '%Y-%m'), DATE_FORMAT(event_date, '%b')
                           ORDER BY MIN(event_date) ASC";
                $attStmt = $db->prepare($attSql);
                $attStmt->bind_param("i", $userId);
                $attStmt->execute();
                $attendanceTrend = $attStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } catch (\Exception $e) {
                error_log('Error getting member attendance trend: ' . $e->getMessage());
            }

            // Unit Leadership Data (if member is a director)
            $isUnitHead = $this->session->isDirector();
            $managedUnits = [];
            if ($isUnitHead) {
                $directorUnits = $this->session->getDirectorUnits();
                foreach ($directorUnits as $unit) {
                    $unitStats = $unitModel->getHealthMetrics($unit['id']);
                    $managedUnits[] = [
                        'unit' => $unit,
                        'stats' => $unitStats
                    ];
                }
            }

            $this->render('dashboard/user_dashboard', [
                'title' => 'Member Dashboard',
                'pageTitle' => 'My Dashboard',
                'user' => $user,
                'church' => $church,
                'evangelismReports' => $evangelismReports,
                'totalSoulsWon' => $totalSoulsWon,
                'notifications' => $notifications,
                'assignedUnits' => $assignedUnits,
                'attendanceSummary' => $attendanceSummary,
                'attendanceTrend' => $attendanceTrend,
                'givingSummary' => $givingSummary,
                'engagementScore' => $engagementScore,
                'aiInsights' => $aiInsights,
                'activePledgesCount' => $activePledgesCount,
                'totalPledged' => $totalPledged,
                'totalPaid' => $totalPaid,
                'isUnitHead' => $isUnitHead,
                'managedUnits' => $managedUnits
            ]);
            return;
        }

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'pageTitle' => 'Dashboard',
            'totalChurches' => $totalChurches,
            'totalMembers' => $totalMembers,
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
            'myFollowUps' => $myFollowUps,
            'headPastorData' => $headPastorData,
            'directorData' => $directorData,
            'pastorData' => $pastorData,
            'pastorDirectorData' => $pastorDirectorData
        ]);
    }
}

