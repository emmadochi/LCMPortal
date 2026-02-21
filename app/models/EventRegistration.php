<?php
namespace App\Models;

class EventRegistration extends BaseModel {
    protected $table = 'event_registrations';
    protected $fillable = [
        'event_id', 'user_id', 'status', 'notes'
    ];

    /**
     * Register a user for an event
     */
    public function registerForEvent($eventId, $userId, $notes = null) {
        // Check if already registered
        if ($this->isRegistered($eventId, $userId)) {
            return false;
        }

        // Check capacity if applicable
        $event = (new Event())->find($eventId);
        if ($event && $event['capacity']) {
            $currentRegistrations = $this->countRegistrations($eventId);
            if ($currentRegistrations >= $event['capacity']) {
                return false; // Event is full
            }
        }

        $data = [
            'event_id' => $eventId,
            'user_id' => $userId,
            'status' => 'registered',
            'notes' => $notes
        ];

        return $this->create($data);
    }

    /**
     * Check if user is already registered for event
     */
    public function isRegistered($eventId, $userId) {
        $sql = "SELECT COUNT(*) as count FROM event_registrations 
                WHERE event_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $eventId, $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }

    /**
     * Get all registrations for an event
     */
    public function getEventRegistrations($eventId) {
        $sql = "SELECT er.*, u.first_name, u.last_name, u.email, u.phone
                FROM event_registrations er
                JOIN users u ON er.user_id = u.id
                WHERE er.event_id = ?
                ORDER BY er.registration_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get user's registrations
     */
    public function getUserRegistrations($userId) {
        $sql = "SELECT er.*, e.title, e.start_date, e.end_date, e.location, e.status as event_status
                FROM event_registrations er
                JOIN events e ON er.event_id = e.id
                WHERE er.user_id = ?
                ORDER BY e.start_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Count total registrations for an event
     */
    public function countRegistrations($eventId) {
        $sql = "SELECT COUNT(*) as count FROM event_registrations 
                WHERE event_id = ? AND status != 'cancelled'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    /**
     * Get registration counts by status for an event
     */
    public function getRegistrationStats($eventId) {
        $sql = "SELECT status, COUNT(*) as count 
                FROM event_registrations 
                WHERE event_id = ? 
                GROUP BY status";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $stats = [
            'registered' => 0,
            'confirmed' => 0,
            'attended' => 0,
            'cancelled' => 0
        ];
        
        foreach ($results as $row) {
            $stats[$row['status']] = $row['count'];
        }
        
        return $stats;
    }

    /**
     * Update registration status
     */
    public function updateStatus($id, $status) {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Cancel registration
     */
    public function cancelRegistration($id) {
        return $this->updateStatus($id, 'cancelled');
    }

    /**
     * Mark as attended
     */
    public function markAttended($id) {
        return $this->updateStatus($id, 'attended');
    }

    /**
     * Get upcoming events user is registered for
     */
    public function getUpcomingRegistrations($userId, $days = 30) {
        $sql = "SELECT er.*, e.title, e.description, e.start_date, e.end_date, e.location
                FROM event_registrations er
                JOIN events e ON er.event_id = e.id
                WHERE er.user_id = ? 
                AND e.start_date >= NOW()
                AND e.start_date <= DATE_ADD(NOW(), INTERVAL ? DAY)
                AND er.status != 'cancelled'
                ORDER BY e.start_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $userId, $days);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get registrants who attended an event (integrated with attendance system)
     */
    public function getEventAttendees($eventId) {
        $sql = "SELECT er.*, u.first_name, u.last_name, u.email, u.phone, a.status as attendance_status
                FROM event_registrations er
                JOIN users u ON er.user_id = u.id
                LEFT JOIN attendance a ON er.user_id = a.user_id AND a.event_id = er.event_id
                WHERE er.event_id = ? AND er.status IN ('confirmed', 'attended')
                ORDER BY u.first_name, u.last_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Import registrations to attendance system
     */
    public function importToAttendance($eventId) {
        // Get confirmed/attended registrants
        $registrations = $this->getEventRegistrations($eventId);
        $attendanceModel = new \App\Models\Attendance();
        
        $imported = 0;
        foreach ($registrations as $reg) {
            if (in_array($reg['status'], ['confirmed', 'attended'])) {
                // Check if attendance record already exists
                $existing = $attendanceModel->db->prepare(
                    "SELECT id FROM attendance WHERE user_id = ? AND event_id = ?"
                );
                $existing->bind_param("ii", $reg['user_id'], $eventId);
                $existing->execute();
                
                if (!$existing->get_result()->fetch_assoc()) {
                    // Create attendance record
                    $data = [
                        'user_id' => $reg['user_id'],
                        'event_id' => $eventId,
                        'date' => date('Y-m-d'),
                        'status' => ($reg['status'] === 'attended') ? 'present' : 'absent',
                        'notes' => 'Imported from event registration'
                    ];
                    $attendanceModel->create($data);
                    $imported++;
                }
            }
        }
        
        return $imported;
    }

    /**
     * Send event reminders to registered users
     */
    public function sendEventReminders($daysBefore = 1) {
        $notificationModel = new \App\Models\Notification();
        
        // Get events happening in X days
        $sql = "SELECT e.*, 
                       COUNT(er.id) as registration_count,
                       GROUP_CONCAT(er.user_id) as registered_user_ids
                FROM events e
                LEFT JOIN event_registrations er ON e.id = er.event_id 
                    AND er.status IN ('registered', 'confirmed')
                WHERE DATE(e.start_date) = DATE(DATE_ADD(NOW(), INTERVAL ? DAY))
                AND e.status = 'published'
                GROUP BY e.id
                HAVING registration_count > 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $daysBefore);
        $stmt->execute();
        $events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $sent = 0;
        foreach ($events as $event) {
            $userIds = explode(',', $event['registered_user_ids']);
            
            foreach ($userIds as $userId) {
                if (!empty($userId)) {
                    $message = "Reminder: {$event['title']} is happening " . 
                              ($daysBefore == 1 ? "tomorrow" : "in {$daysBefore} days") . 
                              " on " . date('M j, Y \a\t g:i A', strtotime($event['start_date']));
                    
                    $data = [
                        'user_id' => $userId,
                        'type' => 'event_reminder',
                        'title' => 'Event Reminder',
                        'message' => $message,
                        'related_id' => $event['id'],
                        'related_type' => 'event'
                    ];
                    
                    if ($notificationModel->create($data)) {
                        $sent++;
                    }
                }
            }
        }
        
        return $sent;
    }

    /**
     * Get user's upcoming event reminders
     */
    public function getUserEventReminders($userId, $days = 7) {
        $sql = "SELECT e.*, er.registration_date, DATEDIFF(e.start_date, NOW()) as days_until
                FROM event_registrations er
                JOIN events e ON er.event_id = e.id
                WHERE er.user_id = ? 
                AND e.start_date >= NOW()
                AND e.start_date <= DATE_ADD(NOW(), INTERVAL ? DAY)
                AND er.status IN ('registered', 'confirmed')
                AND e.status = 'published'
                ORDER BY e.start_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $userId, $days);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}