<?php
namespace App\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\ExportHelper;
use App\Utilities\SearchHelper;

class UnitController extends BaseController {
    private $unitModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->unitModel = new Unit();
        $this->userModel = new User();
        
        // Check permission
        $this->authorize('manage_units');
    }

    /**
     * List all units
     */
    public function index() {
        $search = $this->request->get('search', '');
        $status = $this->request->get('status', '');
        
        // Build conditions using SearchHelper
        $conditions = [];
        
        // Filter by status
        if ($status) {
            $conditions['status'] = $status;
        }
        
        // Get all units
        $units = $this->unitModel->findAll($conditions, 'created_at DESC');
        
        // Apply search filter if provided
        if ($search) {
            $searchTerm = SearchHelper::sanitize($search);
            $units = array_filter($units, function($unit) use ($searchTerm) {
                return stripos($unit['name'], $searchTerm) !== false ||
                       stripos($unit['description'] ?? '', $searchTerm) !== false;
            });
        }
        
        $this->render('units/index', [
            'title' => 'Units',
            'pageTitle' => 'Units',
            'units' => $units,
            'search' => $search,
            'status' => $status
        ]);
    }

    /**
     * Show single unit
     */
    public function show($id) {
        $unit = $this->unitModel->find($id);
        
        if (!$unit) {
            $this->session->setFlash('error', 'Unit not found.');
            $this->redirect('/units');
        }
        
        $members = $this->unitModel->getMembers($id);
        $directors = $this->unitModel->getDirectors($id);
        $statistics = $this->unitModel->getStatistics($id);
        
        // Get all users for assignment dropdowns
        $allUsers = $this->userModel->findAll(['status' => 'active'], 'first_name, last_name');
        
        $this->render('units/show', [
            'title' => $unit['name'],
            'pageTitle' => $unit['name'],
            'unit' => $unit,
            'members' => $members,
            'directors' => $directors,
            'statistics' => $statistics,
            'allUsers' => $allUsers,
            'breadcrumbs' => [
                ['label' => 'Units', 'url' => '/units'],
                ['label' => $unit['name'], 'active' => true]
            ]
        ]);
    }

    /**
     * Show create form
     */
    public function create() {
        $csrfToken = Security::generateCSRFToken();
        $this->render('units/create', [
            'title' => 'Create Unit',
            'pageTitle' => 'Create Unit',
            'csrf_token' => $csrfToken,
            'breadcrumbs' => [
                ['label' => 'Units', 'url' => '/units'],
                ['label' => 'Create', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new unit
     */
    public function store() {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/units/create');
        }

        // Validate input
        $validation = $this->validate([
            'name' => 'required|min:3|max:255',
            'description' => 'optional|max:1000'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/units/create');
        }

        $data = [
            'name' => $this->request->post('name'),
            'description' => $this->request->post('description', ''),
            'status' => 'active'
        ];

        $id = $this->unitModel->create($data);
        if ($id) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'create',
                'Unit',
                $id,
                "Created unit: {$data['name']}"
            );
            
            $this->session->setFlash('success', 'Unit created successfully.');
            $this->redirect("/units/{$id}");
        } else {
            $this->session->setFlash('error', 'Failed to create unit.');
            $this->redirect('/units/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit($id) {
        $unit = $this->unitModel->find($id);
        
        if (!$unit) {
            $this->session->setFlash('error', 'Unit not found.');
            $this->redirect('/units');
        }
        
        $csrfToken = Security::generateCSRFToken();
        $this->render('units/edit', [
            'title' => 'Edit Unit',
            'pageTitle' => 'Edit Unit',
            'unit' => $unit,
            'csrf_token' => $csrfToken,
            'breadcrumbs' => [
                ['label' => 'Units', 'url' => '/units'],
                ['label' => $unit['name'], 'url' => '/units/' . $id],
                ['label' => 'Edit', 'active' => true]
            ]
        ]);
    }

    /**
     * Update unit
     */
    public function update($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/units/{$id}/edit");
        }

        // Validate input
        $validation = $this->validate([
            'name' => 'required|min:3|max:255',
            'description' => 'optional|max:1000',
            'status' => 'required'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/units/{$id}/edit");
        }

        $data = [
            'name' => $this->request->post('name'),
            'description' => $this->request->post('description', ''),
            'status' => $this->request->post('status')
        ];

        if ($this->unitModel->update($id, $data)) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'update',
                'Unit',
                $id,
                "Updated unit: {$data['name']}"
            );
            
            $this->session->setFlash('success', 'Unit updated successfully.');
            $this->redirect("/units/{$id}");
        } else {
            $this->session->setFlash('error', 'Failed to update unit.');
            $this->redirect("/units/{$id}/edit");
        }
    }

    /**
     * Delete unit
     */
    public function delete($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/units');
        }

        $unit = $this->unitModel->find($id);
        $unitName = $unit ? $unit['name'] : 'Unknown';
        
        if ($this->unitModel->delete($id)) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'delete',
                'Unit',
                $id,
                "Deleted unit: {$unitName}"
            );
            
            $this->session->setFlash('success', 'Unit deleted successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to delete unit.');
        }
        
        $this->redirect('/units');
    }

    /**
     * Assign member to unit (AJAX)
     */
    public function assignMember() {
        $unitId = (int)$this->request->post('unit_id');
        $userId = (int)$this->request->post('user_id');
        $role = $this->request->post('role', 'member');

        if (!$unitId || !$userId) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        if ($this->unitModel->assignMember($unitId, $userId, $role)) {
            // Log activity
            $user = $this->userModel->find($userId);
            $unit = $this->unitModel->find($unitId);
            ActivityLog::log(
                $this->session->get('user_id'),
                'assign',
                'Unit',
                $unitId,
                "Assigned member {$user['first_name']} {$user['last_name']} to unit {$unit['name']}"
            );
            
            $this->json(['success' => true, 'message' => 'Member assigned successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to assign member. User may already be assigned.'], 400);
        }
    }

    /**
     * Remove member from unit (AJAX)
     */
    public function removeMember() {
        $unitId = (int)$this->request->post('unit_id');
        $userId = (int)$this->request->post('user_id');

        if (!$unitId || !$userId) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        if ($this->unitModel->removeMember($unitId, $userId)) {
            // Log activity
            $user = $this->userModel->find($userId);
            $unit = $this->unitModel->find($unitId);
            ActivityLog::log(
                $this->session->get('user_id'),
                'remove',
                'Unit',
                $unitId,
                "Removed member {$user['first_name']} {$user['last_name']} from unit {$unit['name']}"
            );
            
            $this->json(['success' => true, 'message' => 'Member removed successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to remove member'], 400);
        }
    }

    /**
     * Assign director to unit (AJAX)
     */
    public function assignDirector() {
        $unitId = (int)$this->request->post('unit_id');
        $userId = (int)$this->request->post('user_id');

        if (!$unitId || !$userId) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        if ($this->unitModel->assignDirector($unitId, $userId)) {
            // Log activity
            $user = $this->userModel->find($userId);
            $unit = $this->unitModel->find($unitId);
            ActivityLog::log(
                $this->session->get('user_id'),
                'assign',
                'Unit',
                $unitId,
                "Assigned director {$user['first_name']} {$user['last_name']} to unit {$unit['name']}"
            );
            
            $this->json(['success' => true, 'message' => 'Director assigned successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to assign director. User may already be assigned.'], 400);
        }
    }

    /**
     * Remove director from unit (AJAX)
     */
    public function removeDirector() {
        $unitId = (int)$this->request->post('unit_id');
        $userId = (int)$this->request->post('user_id');

        if (!$unitId || !$userId) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        if ($this->unitModel->removeDirector($unitId, $userId)) {
            // Log activity
            $user = $this->userModel->find($userId);
            $unit = $this->unitModel->find($unitId);
            ActivityLog::log(
                $this->session->get('user_id'),
                'remove',
                'Unit',
                $unitId,
                "Removed director {$user['first_name']} {$user['last_name']} from unit {$unit['name']}"
            );
            
            $this->json(['success' => true, 'message' => 'Director removed successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to remove director'], 400);
        }
    }

    /**
     * Export units
     */
    public function export() {
        $format = $this->request->get('format', 'csv');
        $units = $this->unitModel->findAll([], 'created_at DESC');
        
        $headers = ['ID', 'Name', 'Description', 'Status', 'Created At'];
        $data = [];
        
        foreach ($units as $unit) {
            $data[] = [
                'id' => $unit['id'],
                'name' => $unit['name'],
                'description' => $unit['description'] ?? '',
                'status' => $unit['status'],
                'created_at' => $unit['created_at']
            ];
        }
        
        $filename = 'units_' . date('Y-m-d_His') . '.' . $format;
        
        if ($format === 'json') {
            ExportHelper::exportJSON($data, $filename);
        } elseif ($format === 'pdf') {
            ExportHelper::exportPDF($data, $headers, 'Units Export', $filename);
        } else {
            ExportHelper::exportCSV($data, $headers, $filename);
        }
    }
}

