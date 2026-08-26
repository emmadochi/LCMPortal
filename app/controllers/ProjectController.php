<?php
namespace App\Controllers;

use App\Models\Project;
use App\Models\Unit;
use App\Models\Church;
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\ExportHelper;

class ProjectController extends BaseController {
    private $projectModel;
    private $unitModel;
    private $churchModel;

    public function __construct() {
        parent::__construct();
        $this->projectModel = new Project();
        $this->unitModel = new Unit();
        $this->churchModel = new Church();
        
        // Check permission
        $this->authorize('manage_projects');
    }

    /**
     * List all projects (optionally scoped to a church for super admin)
     */
    public function index() {
        $churchId = (int) $this->request->get('church_id', 0);
        $churchFilter = null;
        $projects = [];

        if ($churchId && $this->session->get('user_role') === 'admin') {
            $church = $this->churchModel->find($churchId);
            if ($church) {
                $unitIds = $this->churchModel->getChurchUnitIds($churchId);
                $projects = $this->projectModel->getProjectsWithDetailsByUnitIds($unitIds, 'p.created_at DESC');
                $churchFilter = ['id' => $churchId, 'name' => $church['name']];
            }
        } elseif ($this->session->isHeadPastor()) {
            $headId = $this->session->getHeadPastorChurchId();
            $church = $this->churchModel->find($headId);
            if ($church) {
                $unitIds = $this->churchModel->getChurchUnitIds($headId);
                $projects = $this->projectModel->getProjectsWithDetailsByUnitIds($unitIds, 'p.created_at DESC');
                $churchFilter = ['id' => $headId, 'name' => $church['name']];
            }
        }

        if ($churchFilter === null) {
            $projects = $this->projectModel->getProjectsWithDetails([], 'p.created_at DESC');
        }
        
        $this->render('projects/index', [
            'title' => 'Projects',
            'pageTitle' => $churchFilter ? 'Projects — ' . $churchFilter['name'] : 'Projects & Events',
            'projects' => $projects,
            'churchFilter' => $churchFilter
        ]);
    }

    /**
     * Show create form
     */
    public function create() {
        $csrfToken = Security::generateCSRFToken();
        $units = $this->unitModel->getActiveUnits();
        $statuses = ['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        
        $this->render('projects/create', [
            'title' => 'Create Project',
            'pageTitle' => 'Create Project',
            'csrf_token' => $csrfToken,
            'units' => $units,
            'statuses' => $statuses,
            'priorities' => $priorities,
            'breadcrumbs' => [
                ['label' => 'Projects', 'url' => '/projects'],
                ['label' => 'Create', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new project
     */
    public function store() {
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/projects/create');
        }

        $validation = $this->validate([
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:10',
            'start_date' => 'required|date',
            'status' => 'required'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/projects/create');
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
            // Assign units to project
            if ($this->request->post('unit_ids')) {
                $unitIds = $this->request->post('unit_ids');
                if (is_array($unitIds)) {
                    foreach ($unitIds as $unitId) {
                        $this->assignUnitToProject($projectId, (int)$unitId);
                    }
                }
            }
            
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'create',
                'Project',
                $projectId,
                "Created project: {$data['title']}"
            );
            
            $this->session->setFlash('success', 'Project created successfully.');
            $this->redirect("/projects/{$projectId}");
        } else {
            $this->session->setFlash('error', 'Failed to create project.');
            $this->redirect('/projects/create');
        }
    }

    /**
     * Show single project
     */
    public function show($id) {
        $project = $this->projectModel->find($id);
        
        if (!$project) {
            $this->session->setFlash('error', 'Project not found.');
            $this->redirect('/projects');
        }
        
        $projectUnits = $this->projectModel->getProjectUnits($id);
        
        $this->render('projects/show', [
            'title' => $project['title'],
            'pageTitle' => $project['title'],
            'project' => $project,
            'projectUnits' => $projectUnits,
            'breadcrumbs' => [
                ['label' => 'Projects', 'url' => '/projects'],
                ['label' => $project['title'], 'active' => true]
            ]
        ]);
    }

    /**
     * Export projects list (optionally scoped by church).
     * Supports: csv, excel, json, pdf
     */
    public function export() {
        $churchId = (int) $this->request->get('church_id', 0);
        $churchFilter = null;
        $projects = [];

        if ($churchId && $this->session->get('user_role') === 'admin') {
            $church = $this->churchModel->find($churchId);
            if ($church) {
                $unitIds = $this->churchModel->getChurchUnitIds($churchId);
                $projects = $this->projectModel->getProjectsWithDetailsByUnitIds($unitIds, 'p.created_at DESC');
                $churchFilter = ['id' => $churchId, 'name' => $church['name']];
            }
        } elseif ($this->session->isHeadPastor()) {
            $headId = $this->session->getHeadPastorChurchId();
            $church = $this->churchModel->find($headId);
            if ($church) {
                $unitIds = $this->churchModel->getChurchUnitIds($headId);
                $projects = $this->projectModel->getProjectsWithDetailsByUnitIds($unitIds, 'p.created_at DESC');
                $churchFilter = ['id' => $headId, 'name' => $church['name']];
            }
        }

        if ($churchFilter === null) {
            $projects = $this->projectModel->getProjectsWithDetails([], 'p.created_at DESC');
        }

        $rows = [];
        foreach ($projects as $project) {
            $rows[] = [
                'title' => $project['title'],
                'status' => $project['status'],
                'priority' => $project['priority'],
                'start_date' => $project['start_date'],
                'end_date' => $project['end_date'] ?? '',
                'budget' => $project['budget'] ?? '',
                'created_by' => trim(($project['first_name'] ?? '') . ' ' . ($project['last_name'] ?? '')),
            ];
        }

        $headers = ['Title', 'Status', 'Priority', 'Start Date', 'End Date', 'Budget', 'Created By'];
        $format = strtolower($this->request->get('format', 'csv'));
        $suffix = $churchId ? '_church_' . $churchId : '_all';
        $baseName = 'projects' . $suffix . '_' . date('Y-m-d_His');

        switch ($format) {
            case 'json':
                ExportHelper::exportJSON($rows, $baseName . '.json');
                break;
            case 'pdf':
                ExportHelper::exportPDF($rows, $headers, 'Projects Export', $baseName . '.pdf');
                break;
            case 'excel':
            case 'xls':
            case 'xlsx':
                ExportHelper::exportExcel($rows, $headers, $baseName . '.xls');
                break;
            case 'csv':
            default:
                ExportHelper::exportCSV($rows, $headers, $baseName . '.csv');
                break;
        }
    }

    /**
     * Assign unit to project
     */
    private function assignUnitToProject($projectId, $unitId) {
        try {
            $sql = "INSERT INTO project_units (project_id, unit_id, created_at) 
                    VALUES (?, ?, NOW())
                    ON DUPLICATE KEY UPDATE updated_at = NOW()";
            
            $stmt = $this->projectModel->db->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ii", $projectId, $unitId);
                return $stmt->execute();
            }
        } catch (\Exception $e) {
            // Table might not exist yet
            error_log('Project unit assignment error: ' . $e->getMessage());
        }
        return false;
    }
}

