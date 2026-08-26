<?php
namespace App\Controllers;

use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Church;
use App\Models\PropertyLog;
use App\Models\PropertyAssignmentLog;
use App\Models\PropertyTransfer;
use App\Utilities\Security;
use App\Utilities\ExportHelper;

class HeadPastorPropertyController extends BaseHeadPastorController {
    
    private $propertyModel;
    private $categoryModel;
    private $churchModel;
    
    public function __construct() {
        parent::__construct();
        $this->propertyModel = new Property();
        $this->categoryModel = new PropertyCategory();
        $this->churchModel = new Church();
    }
    
    /**
     * Dashboard view for head pastor property management
     */
    public function index() {
        // Get properties for this church
        $properties = $this->propertyModel->getAllWithDetails(['church_id' => $this->churchId]);
        
        // Get property statistics
        $totalProperties = count($properties);
        $statusCounts = [
            'available' => 0,
            'in_use' => 0,
            'maintenance' => 0,
            'damaged' => 0,
            'lost' => 0,
            'disposed' => 0
        ];
        
        $categoryBreakdown = [];
        
        foreach ($properties as $property) {
            // Status counts
            $status = $property['status'] ?? 'unknown';
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            } else {
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
            }
            
            // Category breakdown
            $category = $property['category_name'] ?? 'Uncategorized';
            $categoryBreakdown[$category] = ($categoryBreakdown[$category] ?? 0) + 1;
        }
        
        // Get recent activity logs
        $recentLogs = $this->getRecentActivityLogs($this->churchId);
        
        // Get categories for quick statistics
        $categories = $this->categoryModel->getAllWithCounts();
        
        $this->render('head-pastor/property/index', [
            'title' => 'Property Management - ' . $this->church['name'],
            'pageTitle' => 'Property Dashboard',
            'church' => $this->church,
            'properties' => array_slice($properties, 0, 10), // Recent 10 for dashboard table
            'totalProperties' => $totalProperties,
            'statusCounts' => $statusCounts,
            'categoryBreakdown' => $categoryBreakdown,
            'recentLogs' => $recentLogs,
            'categories' => $categories
        ]);
    }
    
    /**
     * List all properties for the head pastor's church
     */
    public function records() {
        // Get filters
        $categoryId = $this->request->get('category_id');
        $status = $this->request->get('status');
        $search = trim($this->request->get('search', ''));
        
        $filters = ['church_id' => $this->churchId];
        
        if ($categoryId) {
            $filters['category_id'] = (int)$categoryId;
        }
        
        if ($status && in_array($status, array_keys(Property::getStatusOptions()))) {
            $filters['status'] = $status;
        }
        
        if ($search) {
            $filters['search'] = $search;
        }
        
        $properties = $this->propertyModel->getAllWithDetails($filters);
        
        // Get categories for filter dropdown
        $categories = $this->categoryModel->getAll();
        
        $this->render('head-pastor/property/records', [
            'title' => 'Property Records - ' . $this->church['name'],
            'pageTitle' => 'Property Inventory',
            'church' => $this->church,
            'properties' => $properties,
            'categories' => $categories,
            'filters' => [
                'category_id' => $categoryId,
                'status' => $status,
                'search' => $search
            ]
        ]);
    }
    
    /**
     * Show create form for new property
     */
    public function create() {
        // Get categories
        $categories = $this->categoryModel->getAll();
        
        // Get users for assignment (members of this church)
        $users = $this->churchModel->getChurchMemberUsers($this->churchId);
        
        $csrfToken = Security::generateCSRFToken();
        
        $this->render('head-pastor/property/create', [
            'title' => 'Add New Property - ' . $this->church['name'],
            'pageTitle' => 'Register New Property',
            'church' => $this->church,
            'categories' => $categories,
            'users' => $users,
            'csrf_token' => $csrfToken,
            'statusOptions' => Property::getStatusOptions()
        ]);
    }
    
    /**
     * Store new property
     */
    public function store() {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/churches/{$this->churchId}/property/create");
        }
        
        // Validate input
        $validation = $this->validate([
            'category_id' => 'required|numeric',
            'name' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required|in:' . implode(',', array_keys(Property::getStatusOptions())),
            'purchase_date' => 'date',
            'purchase_cost' => 'numeric|min:0'
        ]);
        
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/churches/{$this->churchId}/property/create");
        }
        
        $data = [
            'church_id' => $this->churchId,
            'category_id' => (int)$this->request->post('category_id'),
            'name' => trim($this->request->post('name')),
            'description' => trim($this->request->post('description', '')),
            'status' => $this->request->post('status'),
            'location' => trim($this->request->post('location', '')),
            'purchase_date' => $this->request->post('purchase_date') ?: null,
            'purchase_cost' => $this->request->post('purchase_cost') ? (float)$this->request->post('purchase_cost') : null,
            'serial_number' => trim($this->request->post('serial_number', '')),
            'notes' => trim($this->request->post('notes', '')),
            'created_by' => $this->session->get('user_id'),
            'assigned_to_user_id' => $this->request->post('assigned_to_user_id') ? (int)$this->request->post('assigned_to_user_id') : null
        ];
        
        $id = $this->propertyModel->create($data);
        if ($id) {
            // Log activity
            \App\Models\ActivityLog::log(
                $this->session->get('user_id'),
                'create',
                'Property',
                $id,
                "Created property: {$data['name']}"
            );
            
            $this->session->setFlash('success', 'Property created successfully.');
            $this->redirect("/churches/{$this->churchId}/property/records");
        } else {
            $this->session->setFlash('error', 'Failed to create property.');
            $this->redirect("/churches/{$this->churchId}/property/create");
        }
    }
    
    /**
     * Show property details
     */
    public function show($id, $propertyId) {
        $property = $this->propertyModel->getWithDetails($propertyId);
        
        // Security check: Verify property belongs to head pastor's church
        if (!$property || $property['church_id'] != $this->churchId) {
            $this->session->setFlash('error', 'Property not found or access denied.');
            $this->redirect("/churches/{$this->churchId}/property/records");
        }
        
        // Get activity logs for this property
        $logs = $this->getPropertyLogs($propertyId);
        
        // Get users for assignment
        $users = $this->churchModel->getChurchMemberUsers($this->churchId);
        
        $this->render('head-pastor/property/show', [
            'title' => 'Property Details - ' . $property['name'],
            'pageTitle' => 'Asset Profile',
            'church' => $this->church,
            'property' => $property,
            'logs' => $logs,
            'users' => $users,
            'statusOptions' => Property::getStatusOptions()
        ]);
    }
    
    /**
     * Show edit form for property
     */
    public function edit($id, $propertyId) {
        $property = $this->propertyModel->getWithDetails($propertyId);
        
        // Security check: Verify property belongs to head pastor's church
        if (!$property || $property['church_id'] != $this->churchId) {
            $this->session->setFlash('error', 'Property not found or access denied.');
            $this->redirect("/churches/{$this->churchId}/property/records");
        }
        
        // Get categories
        $categories = $this->categoryModel->getAll();
        
        // Get users for assignment
        $users = $this->churchModel->getChurchMemberUsers($this->churchId);
        
        $csrfToken = Security::generateCSRFToken();
        
        $this->render('head-pastor/property/edit', [
            'title' => 'Edit Property - ' . $property['name'],
            'pageTitle' => 'Update Asset Details',
            'church' => $this->church,
            'property' => $property,
            'categories' => $categories,
            'users' => $users,
            'csrf_token' => $csrfToken,
            'statusOptions' => Property::getStatusOptions()
        ]);
    }
    
    /**
     * Update property
     */
    public function update($id, $propertyId) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/churches/{$this->churchId}/property/{$propertyId}/edit");
        }
        
        $property = $this->propertyModel->find($propertyId);
        
        // Security check: Verify property belongs to head pastor's church
        if (!$property || $property['church_id'] != $this->churchId) {
            $this->session->setFlash('error', 'Property not found or access denied.');
            $this->redirect("/churches/{$this->churchId}/property/records");
        }
        
        // Validate input
        $validation = $this->validate([
            'category_id' => 'required|numeric',
            'name' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required|in:' . implode(',', array_keys(Property::getStatusOptions())),
            'purchase_date' => 'date',
            'purchase_cost' => 'numeric|min:0'
        ]);
        
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/churches/{$this->churchId}/property/{$propertyId}/edit");
        }
        
        $data = [
            'category_id' => (int)$this->request->post('category_id'),
            'name' => trim($this->request->post('name')),
            'description' => trim($this->request->post('description', '')),
            'status' => $this->request->post('status'),
            'location' => trim($this->request->post('location', '')),
            'purchase_date' => $this->request->post('purchase_date') ?: null,
            'purchase_cost' => $this->request->post('purchase_cost') ? (float)$this->request->post('purchase_cost') : null,
            'serial_number' => trim($this->request->post('serial_number', '')),
            'notes' => trim($this->request->post('notes', '')),
            'assigned_to_user_id' => $this->request->post('assigned_to_user_id') ? (int)$this->request->post('assigned_to_user_id') : null
        ];
        
        $updated = $this->propertyModel->update($propertyId, $data);
        if ($updated) {
            // Log activity
            \App\Models\ActivityLog::log(
                $this->session->get('user_id'),
                'update',
                'Property',
                $propertyId,
                "Updated property: {$data['name']}"
            );
            
            $this->session->setFlash('success', 'Property updated successfully.');
            $this->redirect("/churches/{$this->churchId}/property/{$propertyId}");
        } else {
            $this->session->setFlash('error', 'Failed to update property.');
            $this->redirect("/churches/{$this->churchId}/property/{$propertyId}/edit");
        }
    }
    
    /**
     * Update property status
     */
    public function updateStatus($id, $propertyId) {
        $property = $this->propertyModel->find($propertyId);
        
        // Security check: Verify property belongs to head pastor's church
        if (!$property || $property['church_id'] != $this->churchId) {
            $this->session->setFlash('error', 'Property not found or access denied.');
            $this->redirect("/churches/{$this->churchId}/property/records");
        }
        
        $newStatus = $this->request->post('status');
        $notes = trim($this->request->post('notes', ''));
        
        if (!in_array($newStatus, array_keys(Property::getStatusOptions()))) {
            $this->session->setFlash('error', 'Invalid status.');
            $this->redirect("/churches/{$this->churchId}/property/{$propertyId}");
        }
        
        $updated = $this->propertyModel->updateStatus($propertyId, $newStatus, $this->session->get('user_id'), $notes);
        
        if ($updated) {
            $this->session->setFlash('success', 'Property status updated successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to update property status.');
        }
        
        $this->redirect("/churches/{$this->churchId}/property/{$propertyId}");
    }
    
    /**
     * Assign property to a user
     */
    public function assign($id, $propertyId) {
        $property = $this->propertyModel->find($propertyId);
        
        // Security check: Verify property belongs to head pastor's church
        if (!$property || $property['church_id'] != $this->churchId) {
            $this->session->setFlash('error', 'Property not found or access denied.');
            $this->redirect("/churches/{$this->churchId}/property/records");
        }
        
        $toUserId = $this->request->post('user_id') ? (int)$this->request->post('user_id') : null;
        $notes = trim($this->request->post('notes', ''));
        
        $updated = $this->propertyModel->assignToUser($propertyId, $toUserId, $this->session->get('user_id'), $notes);
        
        if ($updated) {
            $this->session->setFlash('success', 'Property assignment updated successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to update property assignment.');
        }
        
        $this->redirect("/churches/{$this->churchId}/property/{$propertyId}");
    }
    
    /**
     * Export properties to CSV
     */
    public function export() {
        $categoryId = $this->request->get('category_id');
        $status = $this->request->get('status');
        
        $filters = ['church_id' => $this->churchId];
        
        if ($categoryId) {
            $filters['category_id'] = (int)$categoryId;
        }
        
        if ($status) {
            $filters['status'] = $status;
        }
        
        $properties = $this->propertyModel->getAllWithDetails($filters);
        
        // Prepare CSV data
        $headers = ['Name', 'Category', 'Status', 'Location', 'Assigned To', 'Purchase Date', 'Purchase Cost', 'Serial Number'];
        $rows = [];
        
        foreach ($properties as $property) {
            $rows[] = [
                $property['name'] ?? '',
                $property['category_name'] ?? '',
                ucfirst(str_replace('_', ' ', $property['status'] ?? '')),
                $property['location'] ?? '',
                trim(($property['assigned_first_name'] ?? '') . ' ' . ($property['assigned_last_name'] ?? '')),
                $property['purchase_date'] ?? '',
                $property['purchase_cost'] ? number_format($property['purchase_cost'], 2) : '',
                $property['serial_number'] ?? ''
            ];
        }
        
        $filename = 'properties_' . strtolower(str_replace(' ', '_', $this->church['name'])) . '_' . date('Y-m-d') . '.csv';
        ExportHelper::exportCSV($rows, $headers, $filename);
    }
    
    /**
     * Get recent activity logs for the church's properties
     */
    private function getRecentActivityLogs($churchId) {
        $logModel = new PropertyLog();
        return $logModel->getRecentActivityLogs($churchId, 10);
    }
    
    /**
     * Get logs for a specific property
     */
    private function getPropertyLogs($propertyId) {
        $logModel = new PropertyLog();
        return $logModel->getPropertyLogs($propertyId, 50);
    }
}