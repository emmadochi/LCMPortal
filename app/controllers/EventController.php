<?php
namespace App\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\EventRegistration;
use App\Utilities\Security;

class EventController extends BaseController {
    private $eventModel;
    private $userModel;
    private $registrationModel;

    public function __construct() {
        parent::__construct();
        $this->eventModel = new Event();
        $this->userModel = new User();
        $this->registrationModel = new EventRegistration();
        
        // Check permission - this should be accessible to directors and admins
        // Allow access if user is admin or head pastor
        $userRole = $this->session->get('user_role');
        $isHeadPastor = $this->session->isHeadPastor();
        
        if ($userRole !== 'admin' && !$isHeadPastor) {
            $this->authorize('manage_events');
        }
    }

    /**
     * List all events
     */
    public function index() {
        $search = $this->request->get('search', '');
        $eventType = $this->request->get('event_type', '');
        $status = $this->request->get('status', '');
        $dateRange = $this->request->get('date_range', '');
        
        $conditions = [];
        if ($search) {
            $conditions['title'] = "%{$search}%";
        }
        if ($eventType) {
            $conditions['event_type'] = $eventType;
        }
        if ($status) {
            $conditions['status'] = $status;
        }
        
        $events = $this->eventModel->getEventsWithDetails($conditions);
        
        // Get filter options
        $eventTypes = $this->eventModel->getEventTypes();
        $statuses = $this->eventModel->getStatuses();
        
        $this->render('events/index', [
            'title' => 'Events Management',
            'pageTitle' => 'Events Management',
            'events' => $events,
            'eventTypes' => $eventTypes,
            'statuses' => $statuses,
            'filters' => [
                'search' => $search,
                'event_type' => $eventType,
                'status' => $status,
                'date_range' => $dateRange
            ]
        ]);
    }

    /**
     * Show create event form
     */
    public function create() {
        $csrfToken = Security::generateCSRFToken();
        $eventTypes = $this->eventModel->getEventTypes();
        $statuses = $this->eventModel->getStatuses();
        $organizers = $this->userModel->getAllUsers();
        
        $this->render('events/create', [
            'title' => 'Create Event',
            'pageTitle' => 'Create New Event',
            'csrf_token' => $csrfToken,
            'eventTypes' => $eventTypes,
            'statuses' => $statuses,
            'organizers' => $organizers
        ]);
    }

    /**
     * Store new event
     */
    public function store() {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/events/create');
        }
        
        // Validate input
        $validation = $this->validate([
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:10',
            'event_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|max:255'
        ]);
        
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/events/create');
        }
        
        $data = [
            'title' => trim($this->request->post('title')),
            'description' => trim($this->request->post('description')),
            'event_type' => $this->request->post('event_type'),
            'start_date' => $this->request->post('start_date'),
            'end_date' => $this->request->post('end_date'),
            'location' => trim($this->request->post('location')),
            'organizer_id' => $this->request->post('organizer_id') ?: null,
            'status' => $this->request->post('status', 'draft'),
            'capacity' => $this->request->post('capacity') ? (int)$this->request->post('capacity') : null,
            'registration_required' => (bool)$this->request->post('registration_required', false),
            'created_by' => $this->session->get('user_id')
        ];
        
        $id = $this->eventModel->create($data);
        if ($id) {
            $this->session->setFlash('success', 'Event created successfully.');
            $this->redirect('/events');
        } else {
            $this->session->setFlash('error', 'Failed to create event.');
            $this->redirect('/events/create');
        }
    }

    /**
     * Show event details
     */
    public function show($id) {
        $event = $this->eventModel->getEventWithOrganizer($id);
        
        if (!$event) {
            $this->session->setFlash('error', 'Event not found.');
            $this->redirect('/events');
        }
        
        $this->render('events/show', [
            'title' => $event['title'],
            'pageTitle' => $event['title'],
            'event' => $event
        ]);
    }

    /**
     * Show edit form
     */
    public function edit($id) {
        $event = $this->eventModel->find($id);
        
        if (!$event) {
            $this->session->setFlash('error', 'Event not found.');
            $this->redirect('/events');
        }
        
        $csrfToken = Security::generateCSRFToken();
        $eventTypes = $this->eventModel->getEventTypes();
        $statuses = $this->eventModel->getStatuses();
        $organizers = $this->userModel->getAllUsers();
        
        $this->render('events/edit', [
            'title' => 'Edit Event',
            'pageTitle' => 'Edit Event',
            'event' => $event,
            'csrf_token' => $csrfToken,
            'eventTypes' => $eventTypes,
            'statuses' => $statuses,
            'organizers' => $organizers
        ]);
    }

    /**
     * Update event
     */
    public function update($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/events/{$id}/edit");
        }
        
        $event = $this->eventModel->find($id);
        if (!$event) {
            $this->session->setFlash('error', 'Event not found.');
            $this->redirect('/events');
        }
        
        // Validate input
        $validation = $this->validate([
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:10',
            'event_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|max:255'
        ]);
        
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/events/{$id}/edit");
        }
        
        $data = [
            'title' => trim($this->request->post('title')),
            'description' => trim($this->request->post('description')),
            'event_type' => $this->request->post('event_type'),
            'start_date' => $this->request->post('start_date'),
            'end_date' => $this->request->post('end_date'),
            'location' => trim($this->request->post('location')),
            'organizer_id' => $this->request->post('organizer_id') ?: null,
            'status' => $this->request->post('status', 'draft'),
            'capacity' => $this->request->post('capacity') ? (int)$this->request->post('capacity') : null,
            'registration_required' => (bool)$this->request->post('registration_required', false)
        ];
        
        if ($this->eventModel->update($id, $data)) {
            $this->session->setFlash('success', 'Event updated successfully.');
            $this->redirect('/events');
        } else {
            $this->session->setFlash('error', 'Failed to update event.');
            $this->redirect("/events/{$id}/edit");
        }
    }

    /**
     * Delete event
     */
    public function delete($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/events');
        }
        
        $event = $this->eventModel->find($id);
        if (!$event) {
            $this->session->setFlash('error', 'Event not found.');
            $this->redirect('/events');
        }
        
        if ($this->eventModel->delete($id)) {
            $this->session->setFlash('success', 'Event deleted successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to delete event.');
        }
        
        $this->redirect('/events');
    }

    /**
     * Show calendar view
     */
    public function calendar() {
        $events = $this->eventModel->getEventsWithDetails(['status' => 'published']);
        
        $this->render('events/calendar', [
            'title' => 'Event Calendar',
            'pageTitle' => 'Event Calendar',
            'events' => $events
        ]);
    }

    /**
     * Register for an event
     */
    public function register($eventId) {
        $event = $this->eventModel->find($eventId);
        
        if (!$event) {
            $this->session->setFlash('error', 'Event not found.');
            $this->redirect('/events');
        }

        // Check if registration is required/open
        if (!$event['registration_required'] && $event['status'] !== 'published') {
            $this->session->setFlash('error', 'Registration is not available for this event.');
            $this->redirect("/events/{$eventId}");
        }

        $userId = $this->session->get('user_id');
        
        // Check if already registered
        if ($this->registrationModel->isRegistered($eventId, $userId)) {
            $this->session->setFlash('info', 'You are already registered for this event.');
            $this->redirect("/events/{$eventId}");
        }

        // Check capacity
        if ($event['capacity']) {
            $currentCount = $this->registrationModel->countRegistrations($eventId);
            if ($currentCount >= $event['capacity']) {
                $this->session->setFlash('error', 'This event has reached its capacity limit.');
                $this->redirect("/events/{$eventId}");
            }
        }

        $notes = $this->request->post('notes');
        $result = $this->registrationModel->registerForEvent($eventId, $userId, $notes);
        
        if ($result) {
            $this->session->setFlash('success', 'Successfully registered for the event!');
        } else {
            $this->session->setFlash('error', 'Failed to register for the event.');
        }
        
        $this->redirect("/events/{$eventId}");
    }

    /**
     * Cancel event registration
     */
    public function cancelRegistration($eventId) {
        $userId = $this->session->get('user_id');
        
        // Find the registration
        $sql = "SELECT id FROM event_registrations WHERE event_id = ? AND user_id = ?";
        $stmt = $this->registrationModel->db->prepare($sql);
        $stmt->bind_param("ii", $eventId, $userId);
        $stmt->execute();
        $registration = $stmt->get_result()->fetch_assoc();
        
        if (!$registration) {
            $this->session->setFlash('error', 'Registration not found.');
            $this->redirect('/events');
        }

        if ($this->registrationModel->cancelRegistration($registration['id'])) {
            $this->session->setFlash('success', 'Registration cancelled successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to cancel registration.');
        }
        
        $this->redirect("/events/{$eventId}");
    }

    /**
     * Show event registrations (for organizers/admins)
     */
    public function registrations($eventId) {
        $event = $this->eventModel->find($eventId);
        
        if (!$event) {
            $this->session->setFlash('error', 'Event not found.');
            $this->redirect('/events');
        }

        // Check if user can view registrations (organizer, admin, or creator)
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');
        
        if ($userRole !== 'admin' && $event['organizer_id'] != $userId && $event['created_by'] != $userId) {
            $this->session->setFlash('error', 'Access denied.');
            $this->redirect('/events');
        }

        $registrations = $this->registrationModel->getEventRegistrations($eventId);
        $stats = $this->registrationModel->getRegistrationStats($eventId);
        
        $this->render('events/registrations', [
            'title' => 'Event Registrations',
            'pageTitle' => "Registrations: {$event['title']}",
            'event' => $event,
            'registrations' => $registrations,
            'stats' => $stats
        ]);
    }

    /**
     * Update registration status (organizer/admin only)
     */
    public function updateRegistrationStatus($registrationId) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/events');
        }

        $status = $this->request->post('status');
        $allowedStatuses = ['registered', 'confirmed', 'attended', 'cancelled'];
        
        if (!in_array($status, $allowedStatuses)) {
            $this->session->setFlash('error', 'Invalid status.');
            $this->redirect('/events');
        }

        // Get registration to check permissions
        $registration = $this->registrationModel->find($registrationId);
        if (!$registration) {
            $this->session->setFlash('error', 'Registration not found.');
            $this->redirect('/events');
        }

        $event = $this->eventModel->find($registration['event_id']);
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');
        
        if ($userRole !== 'admin' && $event['organizer_id'] != $userId && $event['created_by'] != $userId) {
            $this->session->setFlash('error', 'Access denied.');
            $this->redirect('/events');
        }

        if ($this->registrationModel->updateStatus($registrationId, $status)) {
            $this->session->setFlash('success', 'Registration status updated successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to update registration status.');
        }
        
        $this->redirect("/events/{$registration['event_id']}/registrations");
    }

    /**
     * Import event registrations to attendance system
     */
    public function importAttendance($eventId) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/events');
        }

        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->session->setFlash('error', 'Event not found.');
            $this->redirect('/events');
        }

        // Check permissions
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');
        
        if ($userRole !== 'admin' && $event['organizer_id'] != $userId && $event['created_by'] != $userId) {
            $this->session->setFlash('error', 'Access denied.');
            $this->redirect('/events');
        }

        $imported = $this->registrationModel->importToAttendance($eventId);
        
        if ($imported > 0) {
            $this->session->setFlash('success', "Successfully imported {$imported} registrations to attendance records.");
        } else {
            $this->session->setFlash('info', 'No new registrations to import or all registrations already exist in attendance records.');
        }
        
        $this->redirect("/events/{$eventId}/registrations");
    }

    /**
     * Mark registrant as attended
     */
    public function markAttended($registrationId) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/events');
        }

        $registration = $this->registrationModel->find($registrationId);
        if (!$registration) {
            $this->session->setFlash('error', 'Registration not found.');
            $this->redirect('/events');
        }

        $event = $this->eventModel->find($registration['event_id']);
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');
        
        if ($userRole !== 'admin' && $event['organizer_id'] != $userId && $event['created_by'] != $userId) {
            $this->session->setFlash('error', 'Access denied.');
            $this->redirect('/events');
        }

        // Update registration status
        if ($this->registrationModel->markAttended($registrationId)) {
            // Also create/update attendance record
            $attendanceModel = new \App\Models\Attendance();
            $existing = $attendanceModel->db->prepare(
                "SELECT id FROM attendance WHERE user_id = ? AND event_id = ?"
            );
            $existing->bind_param("ii", $registration['user_id'], $registration['event_id']);
            $existing->execute();
            $attendanceRecord = $existing->get_result()->fetch_assoc();
            
            $attendanceData = [
                'user_id' => $registration['user_id'],
                'event_id' => $registration['event_id'],
                'date' => date('Y-m-d'),
                'status' => 'present',
                'notes' => 'Marked as attended from event registration'
            ];
            
            if ($attendanceRecord) {
                $attendanceModel->update($attendanceRecord['id'], $attendanceData);
            } else {
                $attendanceModel->create($attendanceData);
            }
            
            $this->session->setFlash('success', 'Attendee marked as attended successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to mark attendee as attended.');
        }
        
        $this->redirect("/events/{$registration['event_id']}/registrations");
    }

    /**
     * Send event reminders (admin/director only)
     */
    public function sendReminders($days = 1) {
        // Check permissions
        $userRole = $this->session->get('role');
        if ($userRole !== 'admin' && $userRole !== 'director') {
            $this->session->setFlash('error', 'Access denied.');
            $this->redirect('/events');
        }

        $sent = $this->registrationModel->sendEventReminders($days);
        
        if ($sent > 0) {
            $this->session->setFlash('success', "Successfully sent {$sent} event reminders.");
        } else {
            $this->session->setFlash('info', 'No event reminders were sent. No events scheduled for the specified timeframe.');
        }
        
        $this->redirect('/events');
    }

    /**
     * Show event statistics and reports
     */
    public function statistics() {
        $period = $this->request->get('period', 'all');
        $validPeriods = ['all', 'today', 'week', 'month', 'quarter', 'year'];
        
        if (!in_array($period, $validPeriods)) {
            $period = 'all';
        }
        
        $stats = $this->eventModel->getEventStatistics($period);
        $monthlyTrends = $this->eventModel->getMonthlyTrends(12);
        $participationStats = $this->eventModel->getParticipationStats();
        
        $this->render('events/statistics', [
            'title' => 'Event Statistics',
            'pageTitle' => 'Event Statistics & Analytics',
            'stats' => $stats,
            'monthlyTrends' => $monthlyTrends,
            'participationStats' => $participationStats,
            'currentPeriod' => $period,
            'periods' => [
                'all' => 'All Time',
                'today' => 'Today',
                'week' => 'This Week',
                'month' => 'This Month',
                'quarter' => 'This Quarter',
                'year' => 'This Year'
            ]
        ]);
    }

    /**
     * Export events to CSV
     */
    public function export() {
        $search = $this->request->get('search', '');
        $eventType = $this->request->get('event_type', '');
        $status = $this->request->get('status', '');
        
        $conditions = [];
        if ($search) {
            $conditions['title'] = "%{$search}%";
        }
        if ($eventType) {
            $conditions['event_type'] = $eventType;
        }
        if ($status) {
            $conditions['status'] = $status;
        }
        
        $csvData = $this->eventModel->exportToCSV($conditions);
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="events_' . date('Y-m-d_H-i-s') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Export event registrations to CSV
     */
    public function exportRegistrations($eventId) {
        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->session->setFlash('error', 'Event not found.');
            $this->redirect('/events');
        }

        // Check permissions
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');
        
        if ($userRole !== 'admin' && $event['organizer_id'] != $userId && $event['created_by'] != $userId) {
            $this->session->setFlash('error', 'Access denied.');
            $this->redirect('/events');
        }

        $csvData = $this->eventModel->exportRegistrationsToCSV($eventId);
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="registrations_' . $event['title'] . '_' . date('Y-m-d_H-i-s') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Download iCal event
     */
    public function downloadICal($eventId) {
        $icalData = $this->eventModel->generateICalEvent($eventId);
        
        if (!$icalData) {
            $this->session->setFlash('error', 'Event not found.');
            $this->redirect('/events');
        }
        
        // Set headers for iCal download
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="event_' . $eventId . '_' . date('Y-m-d_H-i-s') . '.ics"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo $icalData;
        exit;
    }
}