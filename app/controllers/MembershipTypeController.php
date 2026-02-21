<?php
namespace App\Controllers;

use App\Models\MembershipType;
use App\Utilities\Security;

class MembershipTypeController extends BaseController {
    
    private $membershipTypeModel;
    
    public function __construct() {
        parent::__construct();
        $this->membershipTypeModel = new MembershipType();
        
        // Only admins can manage membership types
        $this->authorize('manage_users');
    }
    
    /**
     * List all membership types
     */
    public function index() {
        $membershipTypes = $this->membershipTypeModel->findAll([], 'name ASC');
        
        $this->render('membership-types/index', [
            'title' => 'Membership Types',
            'pageTitle' => 'Membership Types Management',
            'membershipTypes' => $membershipTypes
        ]);
    }
    
    /**
     * Show create form
     */
    public function create() {
        $csrfToken = Security::generateCSRFToken();
        
        $this->render('membership-types/create', [
            'title' => 'Create Membership Type',
            'pageTitle' => 'Create New Membership Type',
            'csrf_token' => $csrfToken
        ]);
    }
    
    /**
     * Store new membership type
     */
    public function store() {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/membership-types/create');
        }
        
        // Validate input
        $validation = $this->validate([
            'name' => 'required|min:2|max:50',
            'description' => 'max:255'
        ]);
        
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/membership-types/create');
        }
        
        // Check if name already exists
        $existingType = $this->membershipTypeModel->findByName($this->request->post('name'));
        if ($existingType) {
            $this->session->setFlash('error', 'Membership type name already exists.');
            $this->redirect('/membership-types/create');
        }
        
        $data = [
            'name' => trim($this->request->post('name')),
            'description' => trim($this->request->post('description', '')),
            'is_active' => (bool)$this->request->post('is_active', true)
        ];
        
        $id = $this->membershipTypeModel->create($data);
        if ($id) {
            $this->session->setFlash('success', 'Membership type created successfully.');
            $this->redirect('/membership-types');
        } else {
            $this->session->setFlash('error', 'Failed to create membership type.');
            $this->redirect('/membership-types/create');
        }
    }
    
    /**
     * Show edit form
     */
    public function edit($id) {
        $membershipType = $this->membershipTypeModel->find($id);
        
        if (!$membershipType) {
            $this->session->setFlash('error', 'Membership type not found.');
            $this->redirect('/membership-types');
        }
        
        $csrfToken = Security::generateCSRFToken();
        
        $this->render('membership-types/edit', [
            'title' => 'Edit Membership Type',
            'pageTitle' => 'Edit Membership Type',
            'membershipType' => $membershipType,
            'csrf_token' => $csrfToken
        ]);
    }
    
    /**
     * Update membership type
     */
    public function update($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/membership-types/{$id}/edit");
        }
        
        // Validate input
        $validation = $this->validate([
            'name' => 'required|min:2|max:50',
            'description' => 'max:255'
        ]);
        
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/membership-types/{$id}/edit");
        }
        
        $membershipType = $this->membershipTypeModel->find($id);
        if (!$membershipType) {
            $this->session->setFlash('error', 'Membership type not found.');
            $this->redirect('/membership-types');
        }
        
        // Check if name is being changed and if it already exists
        $newName = trim($this->request->post('name'));
        if (strtolower($membershipType['name']) !== strtolower($newName)) {
            $existingType = $this->membershipTypeModel->findByName($newName);
            if ($existingType) {
                $this->session->setFlash('error', 'Membership type name already exists.');
                $this->redirect("/membership-types/{$id}/edit");
            }
        }
        
        $data = [
            'name' => $newName,
            'description' => trim($this->request->post('description', '')),
            'is_active' => (bool)$this->request->post('is_active', true)
        ];
        
        if ($this->membershipTypeModel->update($id, $data)) {
            $this->session->setFlash('success', 'Membership type updated successfully.');
            $this->redirect('/membership-types');
        } else {
            $this->session->setFlash('error', 'Failed to update membership type.');
            $this->redirect("/membership-types/{$id}/edit");
        }
    }
    
    /**
     * Delete membership type
     */
    public function delete($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/membership-types');
        }
        
        $membershipType = $this->membershipTypeModel->find($id);
        if (!$membershipType) {
            $this->session->setFlash('error', 'Membership type not found.');
            $this->redirect('/membership-types');
        }
        
        // Check if membership type is in use
        if ($this->membershipTypeModel->isInUse($id)) {
            $this->session->setFlash('error', 'Cannot delete membership type - it is currently assigned to members.');
            $this->redirect('/membership-types');
        }
        
        if ($this->membershipTypeModel->delete($id)) {
            $this->session->setFlash('success', 'Membership type deleted successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to delete membership type.');
        }
        
        $this->redirect('/membership-types');
    }
    
    /**
     * Toggle active status
     */
    public function toggleStatus($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->json(['success' => false, 'message' => 'Invalid security token.'], 400);
        }
        
        $membershipType = $this->membershipTypeModel->find($id);
        if (!$membershipType) {
            $this->json(['success' => false, 'message' => 'Membership type not found.'], 404);
        }
        
        $newStatus = !$membershipType['is_active'];
        if ($this->membershipTypeModel->update($id, ['is_active' => $newStatus])) {
            $this->json([
                'success' => true, 
                'message' => 'Status updated successfully.',
                'new_status' => $newStatus
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update status.'], 500);
        }
    }
}