<?php
namespace App\Models;

class OutreachReport extends BaseModel {
    protected $table = 'outreach_reports';
    protected $fillable = [
        'event_id', 'church_id', 'unit_id', 'title', 'program_date', 'end_date',
        'description', 'status', 'total_attendance', 'first_timers_count',
        'budget_total', 'actual_total', 'created_by'
    ];

    /**
     * Get reports with creator and optional church/unit/event details
     */
    public function getReportsWithDetails($conditions = [], $orderBy = null) {
        $sql = "SELECT r.*,
                u.first_name, u.last_name, u.email as creator_email,
                c.name as church_name,
                un.name as unit_name,
                e.title as event_title
                FROM outreach_reports r
                LEFT JOIN users u ON r.created_by = u.id
                LEFT JOIN churches c ON r.church_id = c.id
                LEFT JOIN units un ON r.unit_id = un.id
                LEFT JOIN events e ON r.event_id = e.id";
        $params = [];
        $types = "";
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "r.{$field} = ?";
                $params[] = $value;
                $types .= is_int($value) ? "i" : "s";
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        } else {
            $sql .= " ORDER BY r.program_date DESC, r.created_at DESC";
        }
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get reports scoped by church unit IDs (for admin church filter)
     */
    public function getReportsWithDetailsByUnitIds(array $unitIds, $orderBy = null) {
        if (empty($unitIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
        $sql = "SELECT r.*,
                u.first_name, u.last_name, u.email as creator_email,
                c.name as church_name,
                un.name as unit_name,
                e.title as event_title
                FROM outreach_reports r
                LEFT JOIN users u ON r.created_by = u.id
                LEFT JOIN churches c ON r.church_id = c.id
                LEFT JOIN units un ON r.unit_id = un.id
                LEFT JOIN events e ON r.event_id = e.id
                WHERE r.unit_id IN ({$placeholders})";
        if ($orderBy) {
            $sql .= " ORDER BY " . (strpos($orderBy, 'r.') === 0 ? $orderBy : 'r.' . $orderBy);
        } else {
            $sql .= " ORDER BY r.program_date DESC, r.created_at DESC";
        }
        $stmt = $this->db->prepare($sql);
        $types = str_repeat('i', count($unitIds));
        $stmt->bind_param($types, ...$unitIds);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPublicity($reportId) {
        $m = new OutreachPublicity();
        return $m->findAll(['outreach_report_id' => $reportId], 'sort_order ASC, id ASC');
    }

    public function getLogistics($reportId) {
        $m = new OutreachLogistic();
        return $m->findAll(['outreach_report_id' => $reportId], 'sort_order ASC, id ASC');
    }

    public function getCosts($reportId) {
        $m = new OutreachCost();
        return $m->findAll(['outreach_report_id' => $reportId], 'sort_order ASC, id ASC');
    }

    public function getChallenges($reportId) {
        $m = new OutreachChallenge();
        return $m->findAll(['outreach_report_id' => $reportId], 'sort_order ASC, id ASC');
    }

    public function getTargets($reportId) {
        $m = new OutreachTarget();
        return $m->findAll(['outreach_report_id' => $reportId], 'sort_order ASC, id ASC');
    }

    public function getImages($reportId) {
        $m = new OutreachImage();
        return $m->getByReportId($reportId);
    }

    public static function getStatuses() {
        return [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved'
        ];
    }
}
