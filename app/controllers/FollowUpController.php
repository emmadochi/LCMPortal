<?php
namespace App\Controllers;

use App\Models\FollowUp;
use App\Models\User;
use App\Models\MembershipType;
use App\Utilities\Security;

class FollowUpController extends BaseController {
    
    private $followUpModel;
    private $userModel;
    private $membershipTypeModel;
    private $convertModel;
    
    public function __construct() {
        parent::__construct();
        $this->followUpModel = new FollowUp();
        $this->userModel = new User();
        $this->membershipTypeModel = new MembershipType();
        $this->convertModel = new \App\Models\EvangelismConvert();
    }
    
    /**
     * List all follow-ups (Souls won/assigned + general member follow-ups)
     */
    public function index() {
        $userId = (int)$this->session->get('user_id');
        $isAdminOrPastor = $this->session->isAdmin() || $this->session->isHeadPastor() || $this->session->hasPermission('manage_users');

        $search = $this->request->get('search', '');
        $status = $this->request->get('status', '');
        $priority = $this->request->get('priority', '');
        $type = $this->request->get('type', '');
        $assignedTo = $this->request->get('assigned_to', '');
        
        // Non-admin/pastor members see follow-ups assigned to them
        if (!$isAdminOrPastor) {
            $assignedTo = $userId;
        }

        $followUps = $this->followUpModel->getFollowUpsWithDetails([
            'search' => $search,
            'status' => $status,
            'priority' => $priority,
            'type' => $type,
            'assigned_to' => $assignedTo ?: null
        ]);
        
        // Enrich display status: pending + past due => overdue
        $today = date('Y-m-d');
        foreach ($followUps as &$f) {
            if (($f['status'] ?? '') === 'pending' && !empty($f['due_date']) && $f['due_date'] < $today) {
                $f['status'] = 'overdue';
            }
        }
        unset($f);
        
        // Fetch souls won & assigned to this user for discipleship follow-up
        $myConverts = $this->convertModel->getConvertsBySoulWinner($userId);
        $careStats = $this->convertModel->getSoulWinnerCareStats($userId);

        // Get filter options
        $types = $this->followUpModel->getFollowUpTypes();
        $priorities = ['urgent', 'high', 'medium', 'low'];
        $statuses = ['pending', 'completed', 'overdue'];
        $members = $isAdminOrPastor ? $this->userModel->getActiveMembers() : [];
        
        $this->render('follow-ups/index', [
            'title' => 'Follow-up & Soul Care Pipeline',
            'pageTitle' => $isAdminOrPastor ? 'Follow-up & Pastoral Care' : 'My Follow-up Pipeline',
            'followUps' => $followUps,
            'myConverts' => $myConverts,
            'careStats' => $careStats,
            'types' => $types,
            'priorities' => $priorities,
            'statuses' => $statuses,
            'members' => $members,
            'isAdminOrPastor' => $isAdminOrPastor,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'priority' => $priority,
                'type' => $type,
                'assigned_to' => $assignedTo
            ]
        ]);
    }
    
    /**
     * Return table body rows and stats as JSON for AJAX filter (same filters as index).
     */
    public function tableFragment() {
        $search = $this->request->get('search', '');
        $status = $this->request->get('status', '');
        $priority = $this->request->get('priority', '');
        $type = $this->request->get('type', '');
        $assignedTo = $this->request->get('assigned_to', '');
        
        $followUps = $this->followUpModel->getFollowUpsWithDetails([
            'search' => $search,
            'status' => $status,
            'priority' => $priority,
            'type' => $type,
            'assigned_to' => $assignedTo
        ]);
        
        $today = date('Y-m-d');
        foreach ($followUps as &$f) {
            if (($f['status'] ?? '') === 'pending' && !empty($f['due_date']) && $f['due_date'] < $today) {
                $f['status'] = 'overdue';
            }
        }
        unset($f);
        
        $stats = [
            'pending' => count(array_filter($followUps, function ($x) { return ($x['status'] ?? '') === 'pending'; })),
            'overdue' => count(array_filter($followUps, function ($x) { return ($x['status'] ?? '') === 'overdue'; })),
            'completed' => count(array_filter($followUps, function ($x) { return ($x['status'] ?? '') === 'completed'; })),
            'total' => count($followUps),
        ];
        
        ob_start();
        include __DIR__ . '/../views/follow-ups/_table_rows.php';
        $rowsHtml = ob_get_clean();
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['rowsHtml' => $rowsHtml, 'stats' => $stats]);
        exit;
    }
    
    /**
     * Show create follow-up form
     */
    public function create() {
        $memberId = $this->request->get('member_id', '');
        $csrfToken = Security::generateCSRFToken();
        
        // Get all active members
        $members = $this->userModel->getActiveMembers();
        
        // Get follow-up types
        $types = $this->followUpModel->getFollowUpTypes();
        
        $this->render('follow-ups/create', [
            'title' => 'Create Follow-up',
            'pageTitle' => 'Create New Follow-up',
            'csrf_token' => $csrfToken,
            'members' => $members,
            'types' => $types,
            'selectedMember' => $memberId
        ]);
    }
    
    /**
     * Store new follow-up
     */
    public function store() {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/follow-ups/create');
        }
        
        // Validate input
        $validation = $this->validate([
            'member_id' => 'required|integer',
            'type' => 'required',
            'priority' => 'required',
            'due_date' => 'required|date'
        ]);
        
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/follow-ups/create');
        }
        
        $data = [
            'member_id' => $this->request->post('member_id'),
            'type' => $this->request->post('type'),
            'priority' => $this->request->post('priority'),
            'due_date' => $this->request->post('due_date'),
            'notes' => $this->request->post('notes', ''),
            'assigned_to' => $this->request->post('assigned_to', null),
            'status' => 'pending'
        ];
        
        $id = $this->followUpModel->create($data);
        if ($id) {
            $this->session->setFlash('success', 'Follow-up created successfully.');
            $this->redirect('/follow-ups');
        } else {
            $this->session->setFlash('error', 'Failed to create follow-up.');
            $this->redirect('/follow-ups/create');
        }
    }
    
    /**
     * Show follow-up details
     */
    public function show($id) {
        $followUp = $this->followUpModel->getFollowUpWithDetails($id);
        
        if (!$followUp) {
            $this->session->setFlash('error', 'Follow-up not found.');
            $this->redirect('/follow-ups');
        }
        
        $this->render('follow-ups/show', [
            'title' => 'Follow-up Details',
            'pageTitle' => 'Follow-up Details',
            'followUp' => $followUp
        ]);
    }
    
    /**
     * Show edit form
     */
    public function edit($id) {
        $followUp = $this->followUpModel->find($id);
        
        if (!$followUp) {
            $this->session->setFlash('error', 'Follow-up not found.');
            $this->redirect('/follow-ups');
        }
        
        $csrfToken = Security::generateCSRFToken();
        $members = $this->userModel->getActiveMembers();
        $types = $this->followUpModel->getFollowUpTypes();
        
        $this->render('follow-ups/edit', [
            'title' => 'Edit Follow-up',
            'pageTitle' => 'Edit Follow-up',
            'followUp' => $followUp,
            'csrf_token' => $csrfToken,
            'members' => $members,
            'types' => $types
        ]);
    }
    
    /**
     * Update follow-up
     */
    public function update($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/follow-ups/{$id}/edit");
        }
        
        $followUp = $this->followUpModel->find($id);
        if (!$followUp) {
            $this->session->setFlash('error', 'Follow-up not found.');
            $this->redirect('/follow-ups');
        }
        
        // Validate input
        $validation = $this->validate([
            'member_id' => 'required|integer',
            'type' => 'required',
            'priority' => 'required',
            'due_date' => 'required|date'
        ]);
        
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/follow-ups/{$id}/edit");
        }
        
        $data = [
            'member_id' => $this->request->post('member_id'),
            'type' => $this->request->post('type'),
            'priority' => $this->request->post('priority'),
            'due_date' => $this->request->post('due_date'),
            'notes' => $this->request->post('notes', ''),
            'assigned_to' => $this->request->post('assigned_to', null)
        ];
        
        if ($this->followUpModel->update($id, $data)) {
            $this->session->setFlash('success', 'Follow-up updated successfully.');
            $this->redirect('/follow-ups');
        } else {
            $this->session->setFlash('error', 'Failed to update follow-up.');
            $this->redirect("/follow-ups/{$id}/edit");
        }
    }
    
    /**
     * Complete a follow-up
     */
    public function complete($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/follow-ups');
        }
        
        $followUp = $this->followUpModel->find($id);
        if (!$followUp) {
            $this->session->setFlash('error', 'Follow-up not found.');
            $this->redirect('/follow-ups');
        }
        
        $notes = $this->request->post('completion_notes', '');
        if ($this->followUpModel->markCompleted($id, $notes)) {
            $this->session->setFlash('success', 'Follow-up marked as completed.');
        } else {
            $this->session->setFlash('error', 'Failed to complete follow-up.');
        }
        
        $this->redirect('/follow-ups');
    }
    
    /**
     * Delete follow-up
     */
    public function delete($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/follow-ups');
        }
        
        $followUp = $this->followUpModel->find($id);
        if (!$followUp) {
            $this->session->setFlash('error', 'Follow-up not found.');
            $this->redirect('/follow-ups');
        }
        
        if ($this->followUpModel->delete($id)) {
            $this->session->setFlash('success', 'Follow-up deleted successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to delete follow-up.');
        }
        
        $this->redirect('/follow-ups');
    }
    
    /**
     * Get follow-up statistics
     */
    public function statistics() {
        $stats = $this->followUpModel->getFollowUpStats();
        
        $this->render('follow-ups/statistics', [
            'title' => 'Follow-up Statistics',
            'pageTitle' => 'Follow-up Statistics',
            'stats' => $stats
        ]);
    }
    
    /**
     * API endpoint for creating follow-up from AJAX
     */
    public function apiCreate() {
        header('Content-Type: application/json');
        
        // Validate CSRF for AJAX requests
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->json(['success' => false, 'message' => 'Invalid security token.'], 400);
        }
        
        // Validate required fields
        $requiredFields = ['member_id', 'type', 'priority', 'due_date'];
        foreach ($requiredFields as $field) {
            if (!$this->request->post($field)) {
                $this->json(['success' => false, 'message' => "Missing required field: {$field}"], 400);
            }
        }
        
        $data = [
            'member_id' => $this->request->post('member_id'),
            'type' => $this->request->post('type'),
            'priority' => $this->request->post('priority'),
            'due_date' => $this->request->post('due_date'),
            'notes' => $this->request->post('notes', ''),
            'status' => 'pending'
        ];
        
        $id = $this->followUpModel->create($data);
        if ($id) {
            $this->json([
                'success' => true,
                'message' => 'Follow-up created successfully.',
                'follow_up_id' => $id
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to create follow-up.'], 500);
        }
    }
}