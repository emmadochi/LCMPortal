<?php
namespace App\Models;

use App\Core\Database;

class EvangelismReport extends BaseModel {
    protected $table = 'evangelism_reports';

    public function getReportsByUserId($userId) {
        return $this->findAll(['user_id' => $userId], 'report_date DESC');
    }

    /**
     * Build date filter clause based on period
     */
    private function getPeriodWhereClause($period, $dateColumn = 'e.report_date') {
        switch ($period) {
            case 'week':
                return "$dateColumn >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            case 'month':
                return "$dateColumn >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            case 'quarter':
                return "$dateColumn >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
            case 'year':
                return "YEAR($dateColumn) = YEAR(CURDATE())";
            case 'all':
            default:
                return "1=1";
        }
    }

    /**
     * Get Ranked Leaderboard of Top Soul Winners
     */
    public function getLeaderboard($period = 'month', $churchId = null, $limit = 50) {
        try {
            $db = Database::getInstance();
            $periodClause = $this->getPeriodWhereClause($period, 'e.report_date');

            $params = [];
            $types = "";
            $churchClause = "1=1";

            if (!empty($churchId)) {
                $churchClause = "(e.church_id = ? OR (e.church_id IS NULL AND u.church_id = ?))";
                $params[] = (int)$churchId;
                $params[] = (int)$churchId;
                $types .= "ii";
            }

            $sql = "SELECT 
                        e.user_id,
                        u.name as user_name,
                        u.email as user_email,
                        u.profile_picture,
                        c.name as church_name,
                        (
                            SELECT un.name 
                            FROM unit_user uu 
                            JOIN units un ON un.id = uu.unit_id 
                            WHERE uu.user_id = e.user_id 
                            LIMIT 1
                        ) as unit_name,
                        SUM(e.souls_won) as total_souls,
                        COUNT(e.id) as report_count,
                        MAX(e.report_date) as latest_outreach
                    FROM evangelism_reports e
                    JOIN users u ON u.id = e.user_id
                    LEFT JOIN churches c ON (c.id = e.church_id OR c.id = u.church_id)
                    WHERE $periodClause AND $churchClause
                    GROUP BY e.user_id, u.name, u.email, u.profile_picture, c.name
                    ORDER BY total_souls DESC, latest_outreach DESC
                    LIMIT " . (int)$limit;

            if (!empty($params)) {
                $stmt = $db->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } else {
                $result = $db->query($sql);
                return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            }
        } catch (\Exception $e) {
            error_log("EvangelismReport: Error fetching leaderboard: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get Aggregated Executive Statistics for the Leaderboard
     */
    public function getLeaderboardStats($period = 'month', $churchId = null) {
        $defaultStats = [
            'total_souls' => 0,
            'total_soul_winners' => 0,
            'total_outreach_sessions' => 0,
            'avg_souls_per_outreach' => 0,
            'top_department' => 'General Outreach',
            'top_department_souls' => 0
        ];

        try {
            $db = Database::getInstance();
            $periodClause = $this->getPeriodWhereClause($period, 'e.report_date');

            $params = [];
            $types = "";
            $churchClause = "1=1";

            if (!empty($churchId)) {
                $churchClause = "(e.church_id = ? OR (e.church_id IS NULL AND u.church_id = ?))";
                $params[] = (int)$churchId;
                $params[] = (int)$churchId;
                $types .= "ii";
            }

            $sql = "SELECT 
                        COALESCE(SUM(e.souls_won), 0) as total_souls,
                        COUNT(DISTINCT e.user_id) as total_soul_winners,
                        COUNT(e.id) as total_outreach_sessions,
                        COALESCE(ROUND(AVG(e.souls_won), 1), 0) as avg_souls_per_outreach
                    FROM evangelism_reports e
                    JOIN users u ON u.id = e.user_id
                    WHERE $periodClause AND $churchClause";

            $row = null;
            if (!empty($params)) {
                $stmt = $db->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    $row = $stmt->get_result()->fetch_assoc();
                }
            } else {
                $res = $db->query($sql);
                $row = $res ? $res->fetch_assoc() : null;
            }

            $stats = array_merge($defaultStats, $row ?: []);

            // Top Department in Soul Winning
            $deptSql = "SELECT un.name as dept_name, SUM(e.souls_won) as dept_souls
                        FROM evangelism_reports e
                        JOIN users u ON u.id = e.user_id
                        JOIN unit_user uu ON uu.user_id = e.user_id
                        JOIN units un ON un.id = uu.unit_id
                        WHERE $periodClause AND $churchClause
                        GROUP BY un.id, un.name
                        ORDER BY dept_souls DESC
                        LIMIT 1";
            
            $topDept = null;
            if (!empty($params)) {
                $stmtD = $db->prepare($deptSql);
                if ($stmtD) {
                    $stmtD->bind_param($types, ...$params);
                    $stmtD->execute();
                    $topDept = $stmtD->get_result()->fetch_assoc();
                }
            } else {
                $resD = $db->query($deptSql);
                $topDept = $resD ? $resD->fetch_assoc() : null;
            }

            $stats['top_department'] = $topDept['dept_name'] ?? 'General Outreach';
            $stats['top_department_souls'] = (int)($topDept['dept_souls'] ?? 0);

            return $stats;
        } catch (\Exception $e) {
            error_log("EvangelismReport: Error getting stats: " . $e->getMessage());
            return $defaultStats;
        }
    }

    /**
     * Get Harvest Timeline Trend for Chart.js
     */
    public function getHarvestTrends($period = 'month', $churchId = null) {
        try {
            $db = Database::getInstance();
            $periodClause = $this->getPeriodWhereClause($period, 'e.report_date');

            $params = [];
            $types = "";
            $churchClause = "1=1";

            if (!empty($churchId)) {
                $churchClause = "(e.church_id = ? OR (e.church_id IS NULL AND u.church_id = ?))";
                $params[] = (int)$churchId;
                $params[] = (int)$churchId;
                $types .= "ii";
            }

            // Group format based on period
            $dateFormat = "%b %d";
            $groupBy = "e.report_date";
            if ($period === 'year' || $period === 'all') {
                $dateFormat = "%b %Y";
                $groupBy = "DATE_FORMAT(e.report_date, '%Y-%m')";
            }

            $sql = "SELECT 
                        DATE_FORMAT(e.report_date, '$dateFormat') as date_label,
                        SUM(e.souls_won) as count
                    FROM evangelism_reports e
                    JOIN users u ON u.id = e.user_id
                    WHERE $periodClause AND $churchClause
                    GROUP BY $groupBy, date_label
                    ORDER BY MIN(e.report_date) ASC";

            $rows = [];
            if (!empty($params)) {
                $stmt = $db->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } else {
                $res = $db->query($sql);
                $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            }

            $labels = [];
            $data = [];
            foreach ($rows as $r) {
                $labels[] = $r['date_label'];
                $data[] = (int)$r['count'];
            }

            return ['labels' => $labels, 'data' => $data];
        } catch (\Exception $e) {
            error_log("EvangelismReport: Error getting trends: " . $e->getMessage());
            return ['labels' => [], 'data' => []];
        }
    }

    /**
     * Get Department/Unit Breakdown for Donut Chart
     */
    public function getUnitBreakdown($period = 'month', $churchId = null) {
        try {
            $db = Database::getInstance();
            $periodClause = $this->getPeriodWhereClause($period, 'e.report_date');

            $params = [];
            $types = "";
            $churchClause = "1=1";

            if (!empty($churchId)) {
                $churchClause = "(e.church_id = ? OR (e.church_id IS NULL AND u.church_id = ?))";
                $params[] = (int)$churchId;
                $params[] = (int)$churchId;
                $types .= "ii";
            }

            $sql = "SELECT 
                        COALESCE(un.name, 'General Outreach') as unit_name,
                        SUM(e.souls_won) as total_souls
                    FROM evangelism_reports e
                    JOIN users u ON u.id = e.user_id
                    LEFT JOIN unit_user uu ON uu.user_id = e.user_id
                    LEFT JOIN units un ON un.id = uu.unit_id
                    WHERE $periodClause AND $churchClause
                    GROUP BY unit_name
                    ORDER BY total_souls DESC
                    LIMIT 6";

            $rows = [];
            if (!empty($params)) {
                $stmt = $db->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } else {
                $res = $db->query($sql);
                $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            }

            $labels = [];
            $data = [];
            foreach ($rows as $r) {
                $labels[] = $r['unit_name'];
                $data[] = (int)$r['total_souls'];
            }

            return ['labels' => $labels, 'data' => $data];
        } catch (\Exception $e) {
            error_log("EvangelismReport: Error getting unit breakdown: " . $e->getMessage());
            return ['labels' => [], 'data' => []];
        }
    }

    /**
     * Get Recent Itemized Outreach Logs for Superadmin Verification
     */
    public function getVerificationLogs($period = 'month', $churchId = null, $limit = 50) {
        try {
            $db = Database::getInstance();
            $periodClause = $this->getPeriodWhereClause($period, 'e.report_date');

            $params = [];
            $types = "";
            $churchClause = "1=1";

            if (!empty($churchId)) {
                $churchClause = "(e.church_id = ? OR (e.church_id IS NULL AND u.church_id = ?))";
                $params[] = (int)$churchId;
                $params[] = (int)$churchId;
                $types .= "ii";
            }

            $sql = "SELECT 
                        e.*,
                        u.name as user_name,
                        u.email as user_email,
                        c.name as church_name,
                        (
                            SELECT un.name 
                            FROM unit_user uu 
                            JOIN units un ON un.id = uu.unit_id 
                            WHERE uu.user_id = e.user_id 
                            LIMIT 1
                        ) as unit_name
                    FROM evangelism_reports e
                    JOIN users u ON u.id = e.user_id
                    LEFT JOIN churches c ON (c.id = e.church_id OR c.id = u.church_id)
                    WHERE $periodClause AND $churchClause
                    ORDER BY e.report_date DESC, e.id DESC
                    LIMIT " . (int)$limit;

            if (!empty($params)) {
                $stmt = $db->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } else {
                $res = $db->query($sql);
                return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            }
        } catch (\Exception $e) {
            error_log("EvangelismReport: Error getting logs: " . $e->getMessage());
            return [];
        }
    }
}
