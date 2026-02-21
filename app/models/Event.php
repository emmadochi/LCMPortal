<?php
namespace App\Models;

class Event extends BaseModel {
    protected $table = 'events';
    protected $fillable = [
        'title', 'description', 'event_type', 'start_date', 'end_date', 
        'location', 'organizer_id', 'status', 'capacity', 
        'registration_required', 'created_by'
    ];

    /**
     * Get events with organizer details
     */
    public function getEventsWithDetails($conditions = [], $orderBy = null) {
        $sql = "SELECT e.*, u.first_name, u.last_name, u.email as organizer_email 
                FROM events e 
                LEFT JOIN users u ON e.organizer_id = u.id";
        
        $params = [];
        $types = "";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "e.{$field} = ?";
                $params[] = $value;
                $types .= is_int($value) ? "i" : "s";
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        } else {
            $sql .= " ORDER BY e.start_date ASC";
        }
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get upcoming events
     */
    public function getUpcomingEvents($days = 30) {
        $sql = "SELECT e.*, u.first_name, u.last_name, u.email as organizer_email 
                FROM events e 
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE e.start_date >= NOW() 
                AND e.start_date <= DATE_ADD(NOW(), INTERVAL ? DAY)
                AND e.status = 'published'
                ORDER BY e.start_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $days);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get past events
     */
    public function getPastEvents($days = 30) {
        $sql = "SELECT e.*, u.first_name, u.last_name, u.email as organizer_email 
                FROM events e 
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE e.start_date < NOW()
                AND e.start_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
                AND e.status = 'completed'
                ORDER BY e.start_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $days);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get event by ID with organizer details
     */
    public function getEventWithOrganizer($id) {
        $sql = "SELECT e.*, u.first_name, u.last_name, u.email as organizer_email
                FROM events e 
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE e.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get events by type
     */
    public function getEventsByType($eventType) {
        $sql = "SELECT e.*, u.first_name, u.last_name 
                FROM events e 
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE e.event_type = ? 
                AND e.status = 'published'
                ORDER BY e.start_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $eventType);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get event types
     */
    public function getEventTypes() {
        return [
            'worship_service' => 'Worship Service',
            'bible_study' => 'Bible Study',
            'prayer_meeting' => 'Prayer Meeting',
            'youth_program' => 'Youth Program',
            'children_ministry' => 'Children Ministry',
            'outreach' => 'Outreach Event',
            'conference' => 'Conference',
            'seminar' => 'Seminar',
            'workshop' => 'Workshop',
            'fellowship' => 'Fellowship',
            'wedding' => 'Wedding',
            'funeral' => 'Funeral',
            'other' => 'Other'
        ];
    }

    /**
     * Get event statuses
     */
    public function getStatuses() {
        return [
            'draft' => 'Draft',
            'published' => 'Published',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed'
        ];
    }

    /**
     * Get comprehensive event statistics
     */
    public function getEventStatistics($period = 'all') {
        $whereClause = '';
        $params = [];
        $types = '';
        
        // Handle period filtering
        switch($period) {
            case 'today':
                $whereClause = "WHERE DATE(e.start_date) = CURDATE()";
                break;
            case 'week':
                $whereClause = "WHERE YEARWEEK(e.start_date, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $whereClause = "WHERE YEAR(e.start_date) = YEAR(CURDATE()) AND MONTH(e.start_date) = MONTH(CURDATE())";
                break;
            case 'year':
                $whereClause = "WHERE YEAR(e.start_date) = YEAR(CURDATE())";
                break;
            case 'quarter':
                $whereClause = "WHERE YEAR(e.start_date) = YEAR(CURDATE()) AND QUARTER(e.start_date) = QUARTER(CURDATE())";
                break;
        }
        
        // Overall statistics
        $stats = [];
        
        // Total events
        $sql = "SELECT COUNT(*) as total FROM events e {$whereClause}";
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $stats['total_events'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Events by status
        $sql = "SELECT status, COUNT(*) as count FROM events e {$whereClause} GROUP BY status";
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $statusResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stats['by_status'] = [];
        foreach ($statusResults as $row) {
            $stats['by_status'][$row['status']] = $row['count'];
        }
        
        // Events by type
        $sql = "SELECT event_type, COUNT(*) as count FROM events e {$whereClause} GROUP BY event_type ORDER BY count DESC";
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $stats['by_type'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Upcoming events (next 30 days)
        $sql = "SELECT COUNT(*) as upcoming FROM events e WHERE e.start_date > NOW() AND e.start_date <= DATE_ADD(NOW(), INTERVAL 30 DAY) AND e.status = 'published'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['upcoming_events'] = $stmt->get_result()->fetch_assoc()['upcoming'];
        
        // Ongoing events (currently happening)
        $sql = "SELECT COUNT(*) as ongoing FROM events e WHERE e.start_date <= NOW() AND e.end_date >= NOW() AND e.status IN ('published', 'completed')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['ongoing_events'] = $stmt->get_result()->fetch_assoc()['ongoing'];
        
        // Past events
        $sql = "SELECT COUNT(*) as past FROM events e WHERE e.end_date < NOW() AND e.status = 'completed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['past_events'] = $stmt->get_result()->fetch_assoc()['past'];
        
        // Average events per month (for the selected period)
        $sql = "SELECT COUNT(*) / COUNT(DISTINCT DATE_FORMAT(start_date, '%Y-%m')) as avg_per_month FROM events e {$whereClause} HAVING COUNT(DISTINCT DATE_FORMAT(start_date, '%Y-%m')) > 0";
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $avgResult = $stmt->get_result()->fetch_assoc();
        $stats['avg_events_per_month'] = $avgResult ? round($avgResult['avg_per_month'], 2) : 0;
        
        // Most popular event type
        $stats['most_popular_type'] = !empty($stats['by_type']) ? $stats['by_type'][0]['event_type'] : 'N/A';
        
        // Registration statistics
        $regSql = "SELECT 
                    COUNT(*) as total_registrations,
                    COUNT(CASE WHEN er.status = 'confirmed' THEN 1 END) as confirmed_registrations,
                    COUNT(CASE WHEN er.status = 'attended' THEN 1 END) as attended_registrations,
                    AVG(CASE WHEN er.status IN ('confirmed', 'attended') THEN 1 ELSE 0 END) * 100 as attendance_rate
                   FROM event_registrations er
                   JOIN events e ON er.event_id = e.id {$whereClause}";
        
        $stmt = $this->db->prepare($regSql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $regStats = $stmt->get_result()->fetch_assoc();
        
        $stats['total_registrations'] = $regStats['total_registrations'] ?? 0;
        $stats['confirmed_registrations'] = $regStats['confirmed_registrations'] ?? 0;
        $stats['attended_registrations'] = $regStats['attended_registrations'] ?? 0;
        $stats['attendance_rate'] = $regStats['attendance_rate'] ? round($regStats['attendance_rate'], 2) : 0;
        
        return $stats;
    }

    /**
     * Get monthly event trends
     */
    public function getMonthlyTrends($months = 12) {
        $sql = "SELECT 
                    DATE_FORMAT(start_date, '%Y-%m') as month,
                    COUNT(*) as event_count,
                    COUNT(CASE WHEN status = 'published' THEN 1 END) as published_count,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_count
                FROM events 
                WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(start_date, '%Y-%m')
                ORDER BY month ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $months);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get event participation statistics
     */
    public function getParticipationStats() {
        $sql = "SELECT 
                    e.id,
                    e.title,
                    e.start_date,
                    COUNT(er.id) as total_registered,
                    COUNT(CASE WHEN er.status = 'confirmed' THEN 1 END) as confirmed,
                    COUNT(CASE WHEN er.status = 'attended' THEN 1 END) as attended,
                    CASE 
                        WHEN COUNT(er.id) > 0 THEN ROUND(COUNT(CASE WHEN er.status = 'attended' THEN 1 END) * 100.0 / COUNT(er.id), 2)
                        ELSE 0 
                    END as attendance_percentage
                FROM events e
                LEFT JOIN event_registrations er ON e.id = er.event_id
                WHERE e.status IN ('completed', 'published')
                GROUP BY e.id, e.title, e.start_date
                ORDER BY e.start_date DESC
                LIMIT 20";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Export events to CSV
     */
    public function exportToCSV($conditions = []) {
        $events = $this->getEventsWithDetails($conditions);
        
        $csvData = [];
        // Headers
        $csvData[] = ['ID', 'Title', 'Description', 'Event Type', 'Start Date', 'End Date', 'Location', 'Organizer', 'Status', 'Capacity', 'Registration Required', 'Created By', 'Created At'];
        
        // Data rows
        foreach ($events as $event) {
            $csvData[] = [
                $event['id'],
                $event['title'],
                substr($event['description'], 0, 100) . (strlen($event['description']) > 100 ? '...' : ''),
                $event['event_type'],
                $event['start_date'],
                $event['end_date'],
                $event['location'],
                $event['first_name'] . ' ' . $event['last_name'],
                $event['status'],
                $event['capacity'] ?: 'N/A',
                $event['registration_required'] ? 'Yes' : 'No',
                $event['created_by'],
                $event['created_at']
            ];
        }
        
        return $csvData;
    }

    /**
     * Export event registrations to CSV
     */
    public function exportRegistrationsToCSV($eventId) {
        $registrationModel = new \App\Models\EventRegistration();
        $registrations = $registrationModel->getEventRegistrations($eventId);
        $event = $this->find($eventId);
        
        $csvData = [];
        // Headers
        $csvData[] = ['Event', 'Attendee Name', 'Email', 'Phone', 'Registration Date', 'Status', 'Notes'];
        
        // Data rows
        foreach ($registrations as $reg) {
            $csvData[] = [
                $event['title'],
                $reg['first_name'] . ' ' . $reg['last_name'],
                $reg['email'],
                $reg['phone'] ?: 'N/A',
                $reg['registration_date'],
                ucfirst($reg['status']),
                $reg['notes'] ?: 'N/A'
            ];
        }
        
        return $csvData;
    }

    /**
     * Generate iCal event for sharing
     */
    public function generateICalEvent($eventId) {
        $event = $this->find($eventId);
        if (!$event) {
            return false;
        }
        
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//ADMIN PORTAL//Event Management//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:" . uniqid() . "@adminportal.local\r\n";
        $ical .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
        $ical .= "DTSTART:" . gmdate('Ymd\THis\Z', strtotime($event['start_date'])) . "\r\n";
        $ical .= "DTEND:" . gmdate('Ymd\THis\Z', strtotime($event['end_date'])) . "\r\n";
        $ical .= "SUMMARY:" . $this->escapeICalText($event['title']) . "\r\n";
        $ical .= "DESCRIPTION:" . $this->escapeICalText($event['description']) . "\r\n";
        $ical .= "LOCATION:" . $this->escapeICalText($event['location']) . "\r\n";
        $ical .= "END:VEVENT\r\n";
        $ical .= "END:VCALENDAR\r\n";
        
        return $ical;
    }

    /**
     * Escape text for iCal format
     */
    private function escapeICalText($text) {
        return str_replace([',', ';', '\\', "\n"], ['\,', '\;', '\\\\', '\\n'], $text);
    }
}