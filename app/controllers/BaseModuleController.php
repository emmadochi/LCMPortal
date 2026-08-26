<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Utilities\Security;
use App\Models\ActivityLog;
use App\Models\BaseModel;

/**
 * Base Module Controller
 * 
 * Provides common CRUD operations and Church ID-based routing
 * for all administrative modules. Extend this controller for
 * consistent functionality across the application.
 * 
 * @author Professional Development Team
 * @version 1.0
 */
abstract class BaseModuleController extends BaseController
{
    /**
     * @var BaseModel The model instance for this module
     */
    protected $model;
    
    /**
     * @var string The module name (e.g., 'finance', 'media', 'projects')
     */
    protected $moduleName;
    
    /**
     * @var string The view directory path
     */
    protected $viewPath;
    
    /**
     * @var array Default permissions required for various actions
     */
    protected $permissions = [
        'index' => 'view_all_reports',
        'create' => 'create_reports', 
        'store' => 'create_reports',
        'show' => 'view_all_reports',
        'edit' => 'manage_reports',
        'update' => 'manage_reports',
        'delete' => 'manage_reports'
    ];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initializeModule();
    }
    
    /**
     * Initialize module-specific properties
     * Must be implemented by child classes
     */
    abstract protected function initializeModule();
    
    /**
     * Get church ID from route parameters or session
     * Validates that user has authorization to access the specified church
     * 
     * @param int|null $churchId
     * @return int|null
     */
    protected function getChurchId($churchId = null)
    {
        // If church ID provided in route, validate authorization
        if ($churchId) {
            $churchId = (int)$churchId;
            $userId = $this->session->get('user_id');
            $userRole = $this->session->get('user_role');
            
            // Admins can access any church
            if ($userRole === 'admin') {
                return $churchId;
            }
            
            // Head pastors can only access their assigned church
            if ($this->session->isHeadPastor()) {
                $headPastorChurchId = $this->session->get('head_pastor_church_id');
                if ($churchId !== $headPastorChurchId) {
                    $this->session->setFlash('error', 'You are not authorized to access this church\'s data.');
                    $this->redirect('/');
                    return null;
                }
                return $churchId;
            }
            
            // Staff/Directors can only access their assigned church/unit
            if ($this->session->hasPermission('manage_units') || $this->session->hasPermission('view_all_reports')) {
                $userChurchId = $this->session->get('church_id');
                if ($churchId !== $userChurchId) {
                    $this->session->setFlash('error', 'Unauthorized access to church data.');
                    $this->redirect('/');
                    return null;
                }
                return $churchId;
            }
            
            // Regular members can only access their own church
            $userChurchId = $this->session->get('church_id');
            if ($churchId !== $userChurchId) {
                $this->session->setFlash('error', 'Unauthorized access to church data.');
                $this->redirect('/');
                return null;
            }
            return $churchId;
        }
        
        // Default to user's church from session
        return $this->session->get('church_id');
    }
    
    /**
     * Display list of module records for a specific church
     * 
     * @param int|null $churchId
     */
    public function index($churchId = null)
    {
        $this->requirePermission($this->permissions['index']);
        
        $churchId = $this->getChurchId($churchId);
        if (!$churchId) return;
        
        $filters = $this->request->get();
        $records = $this->model->getByChurch($churchId, $filters);
        
        $this->render($this->viewPath . '/index', [
            'title' => ucfirst($this->moduleName) . ' Management',
            'records' => $records,
            'churchId' => $churchId,
            'filters' => $filters,
            'breadcrumbs' => $this->getBreadcrumbs('index', $churchId)
        ]);
    }
    
    /**
     * Show create form for new record
     * 
     * @param int|null $churchId
     */
    public function create($churchId = null)
    {
        $this->requirePermission($this->permissions['create']);
        
        $churchId = $this->getChurchId($churchId);
        if (!$churchId) return;
        
        $csrfToken = Security::generateCSRFToken();
        
        $this->render($this->viewPath . '/create', [
            'title' => 'Create ' . ucfirst($this->moduleName),
            'churchId' => $churchId,
            'csrf_token' => $csrfToken,
            'breadcrumbs' => $this->getBreadcrumbs('create', $churchId)
        ]);
    }
    
    /**
     * Store new record
     * 
     * @param int|null $churchId
     */
    public function store($churchId = null)
    {
        $this->requirePermission($this->permissions['store']);
        
        $churchId = $this->getChurchId($churchId);
        if (!$churchId) return;
        
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/{$this->moduleName}/{$churchId}/create");
            return;
        }
        
        $data = $this->request->post();
        $data['church_id'] = $churchId;
        $data['created_by'] = $this->session->get('user_id');
        
        // Validate data
        $validation = $this->validateInput($data);
        if (!$validation['valid']) {
            $this->session->setFlash('error', $validation['errors']);
            $this->redirect("/{$this->moduleName}/{$churchId}/create");
            return;
        }
        
        $recordId = $this->model->create($data);
        
        if ($recordId) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'create',
                ucfirst($this->moduleName),
                $recordId,
                "Created new {$this->moduleName} record"
            );
            
            $this->session->setFlash('success', ucfirst($this->moduleName) . ' created successfully.');
            $this->redirect("/{$this->moduleName}/{$churchId}/{$recordId}");
        } else {
            $this->session->setFlash('error', 'Failed to create ' . $this->moduleName . '.');
            $this->redirect("/{$this->moduleName}/{$churchId}/create");
        }
    }
    
    /**
     * Show specific record details
     * 
     * @param int $id
     * @param int|null $churchId
     */
    public function show($id, $churchId = null)
    {
        $this->requirePermission($this->permissions['show']);
        
        $churchId = $this->getChurchId($churchId);
        if (!$churchId) return;
        
        $record = $this->model->find($id);
        
        if (!$record || $record['church_id'] != $churchId) {
            $this->session->setFlash('error', ucfirst($this->moduleName) . ' not found.');
            $this->redirect("/{$this->moduleName}/{$churchId}");
            return;
        }
        
        $this->render($this->viewPath . '/show', [
            'title' => ucfirst($this->moduleName) . ' Details',
            'record' => $record,
            'churchId' => $churchId,
            'breadcrumbs' => $this->getBreadcrumbs('show', $churchId, $record)
        ]);
    }
    
    /**
     * Show edit form
     * 
     * @param int $id
     * @param int|null $churchId
     */
    public function edit($id, $churchId = null)
    {
        $this->requirePermission($this->permissions['edit']);
        
        $churchId = $this->getChurchId($churchId);
        if (!$churchId) return;
        
        $record = $this->model->find($id);
        
        if (!$record || $record['church_id'] != $churchId) {
            $this->session->setFlash('error', ucfirst($this->moduleName) . ' not found.');
            $this->redirect("/{$this->moduleName}/{$churchId}");
            return;
        }
        
        $csrfToken = Security::generateCSRFToken();
        
        $this->render($this->viewPath . '/edit', [
            'title' => 'Edit ' . ucfirst($this->moduleName),
            'record' => $record,
            'churchId' => $churchId,
            'csrf_token' => $csrfToken,
            'breadcrumbs' => $this->getBreadcrumbs('edit', $churchId, $record)
        ]);
    }
    
    /**
     * Update existing record
     * 
     * @param int $id
     * @param int|null $churchId
     */
    public function update($id, $churchId = null)
    {
        $this->requirePermission($this->permissions['update']);
        
        $churchId = $this->getChurchId($churchId);
        if (!$churchId) return;
        
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/{$this->moduleName}/{$churchId}/{$id}/edit");
            return;
        }
        
        $record = $this->model->find($id);
        if (!$record || $record['church_id'] != $churchId) {
            $this->session->setFlash('error', ucfirst($this->moduleName) . ' not found.');
            $this->redirect("/{$this->moduleName}/{$churchId}");
            return;
        }
        
        $data = $this->request->post();
        $data['updated_by'] = $this->session->get('user_id');
        
        // Validate data
        $validation = $this->validateInput($data, $id);
        if (!$validation['valid']) {
            $this->session->setFlash('error', $validation['errors']);
            $this->redirect("/{$this->moduleName}/{$churchId}/{$id}/edit");
            return;
        }
        
        if ($this->model->update($id, $data)) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'update',
                ucfirst($this->moduleName),
                $id,
                "Updated {$this->moduleName} record"
            );
            
            $this->session->setFlash('success', ucfirst($this->moduleName) . ' updated successfully.');
            $this->redirect("/{$this->moduleName}/{$churchId}/{$id}");
        } else {
            $this->session->setFlash('error', 'Failed to update ' . $this->moduleName . '.');
            $this->redirect("/{$this->moduleName}/{$churchId}/{$id}/edit");
        }
    }
    
    /**
     * Delete record
     * 
     * @param int $id
     * @param int|null $churchId
     */
    public function delete($id, $churchId = null)
    {
        $this->requirePermission($this->permissions['delete']);
        
        $churchId = $this->getChurchId($churchId);
        if (!$churchId) return;
        
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/{$this->moduleName}/{$churchId}");
            return;
        }
        
        $record = $this->model->find($id);
        if (!$record || $record['church_id'] != $churchId) {
            $this->session->setFlash('error', ucfirst($this->moduleName) . ' not found.');
            $this->redirect("/{$this->moduleName}/{$churchId}");
            return;
        }
        
        if ($this->model->delete($id)) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'delete',
                ucfirst($this->moduleName),
                $id,
                "Deleted {$this->moduleName} record"
            );
            
            $this->session->setFlash('success', ucfirst($this->moduleName) . ' deleted successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to delete ' . $this->moduleName . '.');
        }
        
        $this->redirect("/{$this->moduleName}/{$churchId}");
    }
    
    /**
     * Export module data
     * 
     * @param int|null $churchId
     */
    public function export($churchId = null)
    {
        $this->requirePermission($this->permissions['index']);
        
        $churchId = $this->getChurchId($churchId);
        if (!$churchId) return;
        
        $filters = $this->request->get();
        $records = $this->model->getByChurch($churchId, $filters);
        
        $this->exportData($records, $this->moduleName . '_export_' . date('Y-m-d'));
    }
    
    /**
     * Validate input data
     * Override in child classes for module-specific validation
     * 
     * @param array $data
     * @param int|null $id
     * @return array
     */
    protected function validateInput($data, $id = null)
    {
        return ['valid' => true, 'errors' => []];
    }
    
    /**
     * Generate breadcrumbs for navigation
     * 
     * @param string $action
     * @param int $churchId
     * @param array|null $record
     * @return array
     */
    protected function getBreadcrumbs($action, $churchId, $record = null)
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => '/'],
            ['label' => ucfirst($this->moduleName), 'url' => "/{$this->moduleName}/{$churchId}"]
        ];
        
        switch ($action) {
            case 'create':
                $breadcrumbs[] = ['label' => 'Create', 'active' => true];
                break;
            case 'show':
                $breadcrumbs[] = ['label' => $this->getRecordTitle($record), 'active' => true];
                break;
            case 'edit':
                $breadcrumbs[] = ['label' => $this->getRecordTitle($record), 'url' => "/{$this->moduleName}/{$churchId}/{$record['id']}"];
                $breadcrumbs[] = ['label' => 'Edit', 'active' => true];
                break;
            case 'index':
            default:
                $breadcrumbs[count($breadcrumbs)-1]['active'] = true;
                break;
        }
        
        return $breadcrumbs;
    }
    
    /**
     * Get record title for breadcrumbs
     * Override in child classes for meaningful titles
     * 
     * @param array $record
     * @return string
     */
    protected function getRecordTitle($record)
    {
        return isset($record['title']) ? $record['title'] : 
               (isset($record['name']) ? $record['name'] : 'Record #' . $record['id']);
    }
    
    /**
     * Require specific permission
     * 
     * @param string $permission
     */
    protected function requirePermission($permission)
    {
        if ($this->session->hasPermission($permission)) {
            return;
        }

        // Mapping of global permissions to unit-specific permissions
        $permissionMap = [
            'create_reports' => 'manage_unit_reports',
            'manage_reports' => 'manage_unit_reports',
            'view_all_reports' => 'view_unit_reports',
            'manage_finance' => 'manage_unit_finance',
            'manage_attendance' => 'manage_unit_attendance',
            'manage_projects' => 'manage_unit_projects'
        ];

        if (isset($permissionMap[$permission]) && $this->session->hasPermission($permissionMap[$permission])) {
            return;
        }

        if (!$this->session->hasPermission($permission)) {
            $this->session->setFlash('error', 'Insufficient permissions.');
            $this->redirect('/');
        }
    }
}