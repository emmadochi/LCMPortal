<?php
namespace App\Controllers;

use App\Models\Church;
use App\Models\ChurchUnitTarget;
use App\Models\Unit;
use App\Models\User;
use App\Utilities\Security;

class ChurchController extends BaseController {
    private $churchModel;
    private $unitModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->churchModel = new Church();
        $this->unitModel = new Unit();
        $this->userModel = new User();
        
        // Check permission - churches management requires admin role
        $this->authorize('manage_churches');
    }

    /**
     * List all churches
     */
    public function index() {
        $search = $this->request->get('search', '');
        $status = $this->request->get('status', '');
        $state = $this->request->get('state', '');
        
        $conditions = [];
        if ($search) {
            $conditions['search'] = $search;
        }
        if ($status) {
            $conditions['status'] = $status;
        }
        if ($state) {
            $conditions['state'] = $state;
        }
        
        $churches = $this->churchModel->getChurches($conditions);
        $stats = $this->churchModel->getStatistics();
        
        $this->render('churches/index', [
            'title' => 'Church Management',
            'pageTitle' => 'Church Management',
            'churches' => $churches,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'state' => $state
            ],
            'statuses' => $this->churchModel->getStatuses()
        ]);
    }

    /**
     * Show create church form
     */
    public function create() {
        $csrfToken = Security::generateCSRFToken();
        $statuses = $this->churchModel->getStatuses();
        $pastors = $this->userModel->getPastors();
        
        $this->render('churches/create', [
            'title' => 'Create Church',
            'pageTitle' => 'Create New Church',
            'csrf_token' => $csrfToken,
            'statuses' => $statuses,
            'pastors' => $pastors
        ]);
    }

    /**
     * Store new church
     */
    public function store() {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/churches/create');
        }
        
        // Validate input
        $validation = $this->validate([
            'name' => 'required|min:3|max:255',
            'address' => 'required',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'postal_code' => 'required|max:20',
            'country' => 'required|max:100',
            'email' => 'email|nullable',
            'website' => 'url|nullable',
            'phone' => 'max:20|nullable'
        ]);
        
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/churches/create');
        }
        
        // Check if church name already exists
        $name = trim($this->request->post('name'));
        if ($this->churchModel->nameExists($name)) {
            $this->session->setFlash('error', 'A church with this name already exists.');
            $this->redirect('/churches/create');
        }
        
        $data = [
            'name' => $name,
            'description' => trim($this->request->post('description', '')),
            'address' => trim($this->request->post('address')),
            'city' => trim($this->request->post('city')),
            'state' => trim($this->request->post('state')),
            'postal_code' => trim($this->request->post('postal_code')),
            'country' => trim($this->request->post('country', 'USA')),
            'phone' => trim($this->request->post('phone', '')),
            'email' => trim($this->request->post('email', '')),
            'website' => trim($this->request->post('website', '')),
            'established_date' => $this->request->post('established_date') ?: null,
            'pastor_user_id' => $this->request->post('pastor_user_id') ? (int)$this->request->post('pastor_user_id') : null,
            'status' => $this->request->post('status', 'active'),
            'is_headquarters' => (bool)$this->request->post('is_headquarters', false),
            'created_by' => $this->session->get('user_id')
        ];
        
        $id = $this->churchModel->create($data);
        if ($id) {
            $this->session->setFlash('success', 'Church created successfully.');
            $this->redirect('/churches');
        } else {
            $this->session->setFlash('error', 'Failed to create church.');
            $this->redirect('/churches/create');
        }
    }

    /**
     * Church membership dashboard: stats, engagement bands, leaders, unit coordinators, filterable member list.
     */
    public function membershipDashboard($id) {
        $church = $this->churchModel->getChurchWithUnits($id);
        if (!$church) {
            $this->session->setFlash('error', 'Church not found.');
            $this->redirect('/churches');
        }
        $stats = $this->churchModel->getMembershipStats($id);
        $units = $this->churchModel->getChurchUnits($id);
        $filters = [
            'unit_id' => $this->request->get('unit_id', ''),
            'engagement' => $this->request->get('engagement', ''),
            'role' => $this->request->get('role', ''),
            'search' => trim($this->request->get('search', '')),
        ];
        $page = max(1, (int)$this->request->get('page', 1));
        $perPage = 20;
        $result = $this->churchModel->getMembersForDashboard($id, $filters, $page, $perPage);
        $systemRoles = ['admin' => 'Admin', 'director' => 'Director', 'officer' => 'Officer', 'pastor' => 'Pastor', 'user' => 'User'];
        $this->render('churches/membership-dashboard', [
            'title' => 'Membership Dashboard - ' . $church['name'],
            'pageTitle' => 'Membership Dashboard',
            'church' => $church,
            'stats' => $stats,
            'members' => $result['data'],
            'total' => $result['total'],
            'current_page' => $result['current_page'],
            'per_page' => $result['per_page'],
            'total_pages' => $result['total_pages'],
            'filters' => $filters,
            'units' => $units,
            'systemRoles' => $systemRoles,
        ]);
    }

    /**
     * Show church details
     */
    public function show($id) {
        $church = $this->churchModel->getChurchWithDetails($id);
        
        if (!$church) {
            $this->session->setFlash('error', 'Church not found.');
            $this->redirect('/churches');
        }
        
        $church = $this->churchModel->getChurchWithUnits($id); // Updated to get head pastor info
        $units = $this->churchModel->getChurchUnits($id);
        $allUnits = $this->unitModel->getAllUnits();
        $possible_head_pastors = $this->churchModel->getPossibleHeadPastors();
        
        // Get units not yet assigned to this church
        $assignedUnitIds = array_column($units, 'unit_id');
        $availableUnits = array_filter($allUnits, function($unit) use ($assignedUnitIds) {
            return !in_array($unit['id'], $assignedUnitIds);
        });

        $targetModel = new ChurchUnitTarget();
        $churchTargets = $targetModel->getTargetsByChurch($id);
        $targetTypes = ChurchUnitTarget::getTargetTypes();
        
        $this->render('churches/show', [
            'title' => $church['name'],
            'pageTitle' => $church['name'],
            'church' => $church,
            'units' => $units,
            'availableUnits' => $availableUnits,
            'possible_head_pastors' => $possible_head_pastors,
            'churchTargets' => $churchTargets,
            'targetTypes' => $targetTypes,
            'csrf_token' => Security::generateCSRFToken(),
            'is_admin' => $this->session->get('user_role') === 'admin'
        ]);
    }

    /**
     * Show edit form
     */
    public function edit($id) {
        $church = $this->churchModel->find($id);
        
        if (!$church) {
            $this->session->setFlash('error', 'Church not found.');
            $this->redirect('/churches');
        }
        
        $csrfToken = Security::generateCSRFToken();
        $statuses = $this->churchModel->getStatuses();
        $pastors = $this->userModel->getPastors();
        
        $this->render('churches/edit', [
            'title' => 'Edit Church',
            'pageTitle' => 'Edit Church',
            'church' => $church,
            'csrf_token' => $csrfToken,
            'statuses' => $statuses,
            'pastors' => $pastors
        ]);
    }

    /**
     * Update church
     */
    public function update($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/churches/{$id}/edit");
        }
        
        $church = $this->churchModel->find($id);
        if (!$church) {
            $this->session->setFlash('error', 'Church not found.');
            $this->redirect('/churches');
        }
        
        // Validate input
        $validation = $this->validate([
            'name' => 'required|min:3|max:255',
            'address' => 'required',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'postal_code' => 'required|max:20',
            'country' => 'required|max:100',
            'email' => 'email|nullable',
            'website' => 'url|nullable',
            'phone' => 'max:20|nullable'
        ]);
        
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/churches/{$id}/edit");
        }
        
        // Check if church name already exists (excluding current church)
        $name = trim($this->request->post('name'));
        if ($this->churchModel->nameExists($name, $id)) {
            $this->session->setFlash('error', 'A church with this name already exists.');
            $this->redirect("/churches/{$id}/edit");
        }
        
        $data = [
            'name' => $name,
            'description' => trim($this->request->post('description', '')),
            'address' => trim($this->request->post('address')),
            'city' => trim($this->request->post('city')),
            'state' => trim($this->request->post('state')),
            'postal_code' => trim($this->request->post('postal_code')),
            'country' => trim($this->request->post('country', 'USA')),
            'phone' => trim($this->request->post('phone', '')),
            'email' => trim($this->request->post('email', '')),
            'website' => trim($this->request->post('website', '')),
            'established_date' => $this->request->post('established_date') ?: null,
            'pastor_user_id' => $this->request->post('pastor_user_id') ? (int)$this->request->post('pastor_user_id') : null,
            'status' => $this->request->post('status', 'active'),
            'is_headquarters' => (bool)$this->request->post('is_headquarters', false)
        ];
        
        if ($this->churchModel->update($id, $data)) {
            $this->session->setFlash('success', 'Church updated successfully.');
            $this->redirect('/churches');
        } else {
            $this->session->setFlash('error', 'Failed to update church.');
            $this->redirect("/churches/{$id}/edit");
        }
    }

    /**
     * Delete church
     */
    public function delete($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/churches');
        }
        
        $church = $this->churchModel->find($id);
        if (!$church) {
            $this->session->setFlash('error', 'Church not found.');
            $this->redirect('/churches');
        }
        
        // Check if this is the headquarters church
        if ($church['is_headquarters']) {
            $this->session->setFlash('error', 'Cannot delete the headquarters church.');
            $this->redirect('/churches');
        }
        
        if ($this->churchModel->delete($id)) {
            $this->session->setFlash('success', 'Church deleted successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to delete church.');
        }
        
        $this->redirect('/churches');
    }

    /**
     * Assign unit to church
     */
    public function assignUnit($churchId) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/churches/{$churchId}");
        }
        
        $unitId = $this->request->post('unit_id');
        $isPrimary = (bool)$this->request->post('is_primary', false);
        $notes = trim($this->request->post('notes', ''));
        
        if (!$unitId) {
            $this->session->setFlash('error', 'Please select a unit to assign.');
            $this->redirect("/churches/{$churchId}");
        }
        
        $result = $this->churchModel->assignUnit(
            $churchId, 
            $unitId, 
            $this->session->get('user_id'),
            $isPrimary,
            $notes
        );
        
        if ($result) {
            $this->session->setFlash('success', 'Unit assigned to church successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to assign unit. It may already be assigned.');
        }
        
        $this->redirect("/churches/{$churchId}");
    }

    /**
     * Remove unit from church
     */
    public function removeUnit($churchId) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/churches/{$churchId}");
        }
        
        $unitId = $this->request->post('unit_id');
        
        if (!$unitId) {
            $this->session->setFlash('error', 'Unit ID is required.');
            $this->redirect("/churches/{$churchId}");
        }
        
        if ($this->churchModel->removeUnit($churchId, $unitId)) {
            $this->session->setFlash('success', 'Unit removed from church successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to remove unit from church.');
        }
        
        $this->redirect("/churches/{$churchId}");
    }

    /**
     * Get statistics for dashboard
     */
    public function statistics() {
        $stats = $this->churchModel->getStatistics();
        
        header('Content-Type: application/json');
        echo json_encode($stats);
        exit;
    }

    /**
     * Export churches to CSV
     */
    public function export() {
        $search = $this->request->get('search', '');
        $status = $this->request->get('status', '');
        $state = $this->request->get('state', '');
        
        $conditions = [];
        if ($search) {
            $conditions['search'] = $search;
        }
        if ($status) {
            $conditions['status'] = $status;
        }
        if ($state) {
            $conditions['state'] = $state;
        }
        
        $csvData = $this->churchModel->exportToCSV($conditions);
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="churches_' . date('Y-m-d_H-i-s') . '.csv"');
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
     * Generate detailed church report
     */
    public function generateReport($id) {
        $startDate = $this->request->get('start_date');
        $endDate = $this->request->get('end_date');
        
        $report = $this->churchModel->getChurchReport($id, $startDate, $endDate);
        
        if (!$report) {
            $this->session->setFlash('error', 'Church not found.');
            $this->redirect('/churches');
        }
        
        $this->render('churches/report', [
            'title' => 'Church Report: ' . $report['church']['name'],
            'pageTitle' => 'Detailed Report for ' . $report['church']['name'],
            'report' => $report,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    /**
     * Assign head pastor to a church
     */
    public function assignHeadPastor($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/churches/{$id}");
        }

        $userId = $this->request->post('user_id');

        if (!$userId) {
            $this->session->setFlash('error', 'Please select a user to assign as head pastor.');
            $this->redirect("/churches/{$id}");
        }

        // Verify the user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            $this->session->setFlash('error', 'Selected user does not exist.');
            $this->redirect("/churches/{$id}");
        }

        if ($this->churchModel->assignHeadPastor($id, $userId)) {
            $this->session->setFlash('success', 'Head pastor assigned successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to assign head pastor.');
        }

        $this->redirect("/churches/{$id}");
    }

    /**
     * Remove head pastor assignment from a church
     */
    public function removeHeadPastor($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/churches/{$id}");
        }

        if ($this->churchModel->removeHeadPastor($id)) {
            $this->session->setFlash('success', 'Head pastor assignment removed successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to remove head pastor assignment.');
        }

        $this->redirect("/churches/{$id}");
    }
}