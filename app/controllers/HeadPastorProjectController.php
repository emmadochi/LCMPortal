<?php
namespace App\Controllers;

use App\Models\Project;
use App\Models\Unit;
use App\Models\Church;
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\ExportHelper;

class HeadPastorProjectController extends BaseHeadPastorController {
    private $projectModel;
    private $unitModel;
    private $churchModel;

    public function __construct() {
        parent::__construct();
        $this->projectModel = new Project();
        $this->unitModel = new Unit();
        $this->churchModel = new Church();
    }

    /**
     * Dashboard view for head pastor project management
     */
    public function index() {
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        $projects = $this->projectModel->getProjectsWithDetailsByUnitIds($unitIds, 'p.created_at DESC');
        
        // Calculate summaries
        $summary = [
            'total' => count($projects),
            'active' => 0,
            'completed' => 0,
            'planning' => 0,
            'total_budget' => 0
        ];

        foreach ($projects as $p) {
            if ($p['status'] === 'in_progress' || $p['status'] === 'on_hold') $summary['active']++;
            if ($p['status'] === 'completed') $summary['completed']++;
            if ($p['status'] === 'planning') $summary['planning']++;
            $summary['total_budget'] += (float)($p['budget'] ?? 0);
        }

        // Get unit breakdown summary
        $unitSummaries = $this->projectModel->getProjectCountsByUnit($this->churchId);
        
        $this->render('head-pastor/projects/index', [
            'title' => 'Project Management - ' . $this->church['name'],
            'pageTitle' => 'Projects Dashboard',
            'church' => $this->church,
            'summary' => $summary,
            'projects' => array_slice($projects, 0, 5), // Recent 5
            'unitIds' => $unitIds,
            'unitSummaries' => $unitSummaries
        ]);
    }

    /**
     * List all projects for the church
     */
    public function records() {
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        $projects = $this->projectModel->getProjectsWithDetailsByUnitIds($unitIds, 'p.created_at DESC');
        
        $this->render('head-pastor/projects/records', [
            'title' => 'Project Records - ' . $this->church['name'],
            'pageTitle' => 'All Projects',
            'church' => $this->church,
            'projects' => $projects
        ]);
    }

    /**
     * Show create project form
     */
    public function create() {
        $units = $this->churchModel->getChurchUnits($this->churchId);
        $csrfToken = Security::generateCSRFToken();
        
        $this->render('head-pastor/projects/create', [
            'title' => 'New Project - ' . $this->church['name'],
            'pageTitle' => 'Create New Project',
            'church' => $this->church,
            'units' => $units,
            'csrf_token' => $csrfToken,
            'statuses' => ['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'],
            'priorities' => ['low', 'medium', 'high', 'urgent']
        ]);
    }

    /**
     * Store new project
     */
    public function store() {
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/churches/{$this->churchId}/projects/create");
        }

        $validation = $this->validate([
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:10',
            'start_date' => 'required|date',
            'status' => 'required'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/churches/{$this->churchId}/projects/create");
        }

        $data = [
            'title' => $this->request->post('title'),
            'description' => $this->request->post('description'),
            'start_date' => $this->request->post('start_date'),
            'end_date' => $this->request->post('end_date') ?: null,
            'status' => $this->request->post('status'),
            'priority' => $this->request->post('priority', 'medium'),
            'budget' => $this->request->post('budget') ? (float)$this->request->post('budget') : null,
            'created_by' => $this->session->get('user_id')
        ];

        $projectId = $this->projectModel->create($data);
        
        if ($projectId) {
            // Assign units to project (ensure units belong to this church)
            $unitIds = $this->request->post('unit_ids');
            if ($unitIds && is_array($unitIds)) {
                $churchUnits = $this->churchModel->getChurchUnitIds($this->churchId);
                foreach ($unitIds as $unitId) {
                    if (in_array((int)$unitId, $churchUnits)) {
                        $this->assignUnitToProject($projectId, (int)$unitId);
                    }
                }
            }
            
            ActivityLog::log($this->session->get('user_id'), 'create', 'Project', $projectId, "Created project: {$data['title']}");
            
            $this->session->setFlash('success', 'Project created successfully.');
            $this->redirect("/churches/{$this->churchId}/projects/{$projectId}");
        } else {
            $this->session->setFlash('error', 'Failed to create project.');
            $this->redirect("/churches/{$this->churchId}/projects/create");
        }
    }

    /**
     * Show single project detail
     */
    public function show($id, $projectId) {
        $project = $this->projectModel->find($projectId);
        
        if (!$project) {
            $this->session->setFlash('error', 'Project not found.');
            $this->redirect("/churches/{$this->churchId}/projects");
        }

        // Security check: Ensure at least one unit of this project belongs to this church
        // OR the project was created by someone in this church (though projects are unit-linked)
        $projectUnits = $this->projectModel->getProjectUnits($projectId);
        $churchUnitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        
        $isAuthorized = false;
        foreach ($projectUnits as $pu) {
            if (in_array((int)$pu['unit_id'], $churchUnitIds)) {
                $isAuthorized = true;
                break;
            }
        }

        if (!$isAuthorized && $this->session->get('user_role') !== 'admin') {
            $this->session->setFlash('error', 'Access denied to this project.');
            $this->redirect("/churches/{$this->churchId}/projects");
        }

        $this->render('head-pastor/projects/show', [
            'title' => $project['title'] . ' - ' . $this->church['name'],
            'pageTitle' => 'Project Details',
            'church' => $this->church,
            'project' => $project,
            'projectUnits' => $projectUnits
        ]);
    }

    /**
     * Show edit form
     */
    public function edit($id, $projectId) {
        $project = $this->projectModel->find($projectId);
        if (!$project) {
            $this->session->setFlash('error', 'Project not found.');
            $this->redirect("/churches/{$this->churchId}/projects");
        }

        // Security check
        $projectUnits = $this->projectModel->getProjectUnits($projectId);
        $churchUnitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        $isAuthorized = false;
        $currentUnitIds = [];
        foreach ($projectUnits as $pu) {
            $unitId = (int)$pu['unit_id'];
            $currentUnitIds[] = $unitId;
            if (in_array($unitId, $churchUnitIds)) {
                $isAuthorized = true;
            }
        }

        if (!$isAuthorized && $this->session->get('user_role') !== 'admin') {
            $this->session->setFlash('error', 'Access denied.');
            $this->redirect("/churches/{$this->churchId}/projects");
        }

        $units = $this->churchModel->getChurchUnits($this->churchId);
        $csrfToken = Security::generateCSRFToken();

        $this->render('head-pastor/projects/edit', [
            'title' => 'Edit Project - ' . $this->church['name'],
            'pageTitle' => 'Edit Project',
            'church' => $this->church,
            'project' => $project,
            'units' => $units,
            'currentUnitIds' => $currentUnitIds,
            'csrf_token' => $csrfToken,
            'statuses' => ['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'],
            'priorities' => ['low', 'medium', 'high', 'urgent']
        ]);
    }

    /**
     * Update project
     */
    public function update($id, $projectId) {
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/churches/{$this->churchId}/projects/{$projectId}/edit");
        }

        // Security check (already verified in edit, but let's re-verify)
        $project = $this->projectModel->find($projectId);
        if (!$project) { $this->redirect("/churches/{$this->churchId}/projects"); }
        
        $churchUnitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        $projectUnits = $this->projectModel->getProjectUnits($projectId);
        $isAuthorized = false;
        foreach ($projectUnits as $pu) {
            if (in_array((int)$pu['unit_id'], $churchUnitIds)) { $isAuthorized = true; break; }
        }
        if (!$isAuthorized && $this->session->get('user_role') !== 'admin') {
            $this->session->setFlash('error', 'Access denied.');
            $this->redirect("/churches/{$this->churchId}/projects");
        }

        $validation = $this->validate([
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:10',
            'start_date' => 'required|date',
            'status' => 'required'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/churches/{$this->churchId}/projects/{$projectId}/edit");
        }

        $data = [
            'title' => $this->request->post('title'),
            'description' => $this->request->post('description'),
            'start_date' => $this->request->post('start_date'),
            'end_date' => $this->request->post('end_date') ?: null,
            'status' => $this->request->post('status'),
            'priority' => $this->request->post('priority', 'medium'),
            'budget' => $this->request->post('budget') ? (float)$this->request->post('budget') : null
        ];

        if ($this->projectModel->update($projectId, $data)) {
            $unitIds = $this->request->post('unit_ids');
            if (is_array($unitIds)) {
                // Remove old assignments for THIS church's units only
                // Actually, standard behavior is usually to replace all, 
                // but since this is church-scoped, we only manage this church's units.
                // For simplicity, let's sync all provided unit IDs if they belong to this church.
                
                // Clear existing assignments for this church
                $this->clearChurchUnitsFromProject($projectId, $churchUnitIds);
                
                foreach ($unitIds as $unitId) {
                    if (in_array((int)$unitId, $churchUnitIds)) {
                        $this->assignUnitToProject($projectId, (int)$unitId);
                    }
                }
            }
            
            ActivityLog::log($this->session->get('user_id'), 'update', 'Project', $projectId, "Updated project: {$data['title']}");
            $this->session->setFlash('success', 'Project updated successfully.');
            $this->redirect("/churches/{$this->churchId}/projects/{$projectId}");
        } else {
            $this->session->setFlash('error', 'Failed to update project.');
            $this->redirect("/churches/{$this->churchId}/projects/{$projectId}/edit");
        }
    }

    /**
     * Delete project
     */
    public function delete($id, $projectId) {
        // Security check
        $churchUnitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        $projectUnits = $this->projectModel->getProjectUnits($projectId);
        $isAuthorized = false;
        foreach ($projectUnits as $pu) {
            if (in_array((int)$pu['unit_id'], $churchUnitIds)) { $isAuthorized = true; break; }
        }
        if (!$isAuthorized && $this->session->get('user_role') !== 'admin') {
            $this->session->setFlash('error', 'Access denied.');
            $this->redirect("/churches/{$this->churchId}/projects");
        }

        if ($this->projectModel->delete($projectId)) {
            ActivityLog::log($this->session->get('user_id'), 'delete', 'Project', $projectId, "Deleted project #{$projectId}");
            $this->session->setFlash('success', 'Project deleted successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to delete project.');
        }
        $this->redirect("/churches/{$this->churchId}/projects");
    }

    /**
     * Export projects
     */
    public function export() {
        $unitIds = $this->churchModel->getChurchUnitIds($this->churchId);
        $projects = $this->projectModel->getProjectsWithDetailsByUnitIds($unitIds, 'p.created_at DESC');

        $headers = ['Title', 'Status', 'Priority', 'Start Date', 'End Date', 'Budget', 'Created By'];
        $rows = [];

        foreach ($projects as $p) {
            $rows[] = [
                $p['title'],
                ucfirst(str_replace('_', ' ', $p['status'])),
                ucfirst($p['priority']),
                $p['start_date'],
                $p['end_date'] ?? 'N/A',
                $p['budget'] ? number_format($p['budget'], 2) : '0.00',
                trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''))
            ];
        }

        $filename = 'projects_' . strtolower(str_replace(' ', '_', $this->church['name'])) . '_' . date('Y-m-d') . '.csv';
        ExportHelper::exportCSV($rows, $headers, $filename);
    }

    /**
     * Internal helper to assign unit to project
     */
    private function assignUnitToProject($projectId, $unitId) {
        $sql = "INSERT IGNORE INTO project_units (project_id, unit_id, created_at) VALUES (?, ?, NOW())";
        $stmt = $this->projectModel->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $projectId, $unitId);
            return $stmt->execute();
        }
        return false;
    }

    /**
     * Internal helper to clear specific church units from a project
     */
    private function clearChurchUnitsFromProject($projectId, array $churchUnitIds) {
        if (empty($churchUnitIds)) return;
        $placeholders = implode(',', array_fill(0, count($churchUnitIds), '?'));
        $sql = "DELETE FROM project_units WHERE project_id = ? AND unit_id IN ({$placeholders})";
        $stmt = $this->projectModel->db->prepare($sql);
        if ($stmt) {
            $params = array_merge([$projectId], $churchUnitIds);
            $types = 'i' . str_repeat('i', count($churchUnitIds));
            $stmt->bind_param($types, ...$params);
            return $stmt->execute();
        }
        return false;
    }
}
