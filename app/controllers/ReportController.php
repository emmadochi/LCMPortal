<?php
namespace App\Controllers;

use App\Models\Report;
use App\Models\ReportFile;
use App\Models\Unit;
use App\Models\Church;
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\FileUpload;
use App\Utilities\ExportHelper;
use App\Utilities\SearchHelper;

class ReportController extends BaseController {
    private $reportModel;
    private $reportFileModel;
    private $unitModel;
    private $churchModel;

    public function __construct() {
        parent::__construct();
        $this->reportModel = new Report();
        $this->reportFileModel = new ReportFile();
        $this->unitModel = new Unit();
        $this->churchModel = new Church();
        
        // Check permission
        $this->authorize('manage_reports');
    }

    /**
     * List all reports (optionally scoped to a church for super admin)
     */
    public function index() {
        $search = $this->request->get('search', '');
        $reportType = $this->request->get('report_type', '');
        $status = $this->request->get('status', '');
        $unitId = $this->request->get('unit_id', '');
        $churchId = (int) $this->request->get('church_id', 0);
        $churchFilter = null;
        $reports = [];

        if ($churchId && $this->session->get('user_role') === 'admin') {
            $church = $this->churchModel->find($churchId);
            if ($church) {
                $unitIds = $this->churchModel->getChurchUnitIds($churchId);
                $reports = $this->reportModel->getReportsWithDetailsByUnitIds($unitIds, 'created_at DESC');
                $churchFilter = ['id' => $churchId, 'name' => $church['name']];
            }
        }

        if ($churchFilter === null) {
            $conditions = [];
            if ($reportType) {
                $conditions['report_type'] = $reportType;
            }
            if ($status) {
                $conditions['status'] = $status;
            }
            if ($unitId) {
                $conditions['unit_id'] = (int)$unitId;
            }
            $reports = $this->reportModel->getReportsWithDetails($conditions, 'created_at DESC');
        }
        
        // Apply search filter if provided
        if ($search) {
            $searchTerm = SearchHelper::sanitize($search);
            $reports = array_filter($reports, function($report) use ($searchTerm) {
                return stripos($report['title'], $searchTerm) !== false ||
                       stripos($report['content'] ?? '', $searchTerm) !== false ||
                       stripos($report['unit_name'] ?? '', $searchTerm) !== false;
            });
        }
        
        $units = $this->unitModel->getActiveUnits();
        $reportTypes = ['weekly', 'event', 'departmental', 'outreach', 'media', 'technical', 'other'];
        
        $this->render('reports/index', [
            'title' => 'Reports',
            'pageTitle' => $churchFilter ? 'Reports — ' . $churchFilter['name'] : 'Reports',
            'reports' => $reports,
            'units' => $units,
            'reportTypes' => $reportTypes,
            'search' => $search,
            'report_type' => $reportType,
            'status' => $status,
            'unit_id' => $unitId,
            'churchFilter' => $churchFilter
        ]);
    }

    /**
     * Show single report
     */
    public function show($id) {
        $report = $this->reportModel->find($id);
        
        if (!$report) {
            $this->session->setFlash('error', 'Report not found.');
            $this->redirect('/reports');
        }
        
        // Get report files
        $files = $this->reportModel->getFiles($id);
        
        // Get unit info
        $unit = $this->unitModel->find($report['unit_id']);
        
        $this->render('reports/show', [
            'title' => $report['title'],
            'pageTitle' => $report['title'],
            'report' => $report,
            'files' => $files,
            'unit' => $unit,
            'breadcrumbs' => [
                ['label' => 'Reports', 'url' => '/reports'],
                ['label' => $report['title'], 'active' => true]
            ]
        ]);
    }

    /**
     * Show create form
     */
    public function create() {
        $csrfToken = Security::generateCSRFToken();
        
        // Get user's units
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if ($userRole === 'admin') {
            $units = $this->unitModel->getActiveUnits();
        } else {
            // Get units where user is member or director
            $units = $this->unitModel->getActiveUnits(); // Simplified for now
        }
        
        $reportTypes = ['weekly', 'event', 'departmental', 'outreach', 'media', 'technical', 'other'];
        
        $this->render('reports/create', [
            'title' => 'Create Report',
            'pageTitle' => 'Create Report',
            'csrf_token' => $csrfToken,
            'units' => $units,
            'reportTypes' => $reportTypes,
            'breadcrumbs' => [
                ['label' => 'Reports', 'url' => '/reports'],
                ['label' => 'Create', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new report
     */
    public function store() {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/reports/create');
        }

        // Validate input
        $validation = $this->validate([
            'unit_id' => 'required|numeric',
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:10',
            'report_type' => 'required'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/reports/create');
        }

        $data = [
            'unit_id' => (int)$this->request->post('unit_id'),
            'user_id' => $this->session->get('user_id'),
            'title' => $this->request->post('title'),
            'content' => $this->request->post('content'),
            'report_type' => $this->request->post('report_type'),
            'status' => $this->request->post('status', 'draft')
        ];

        // If status is submitted, set submitted_at
        if ($data['status'] === 'submitted') {
            $data['submitted_at'] = date('Y-m-d H:i:s');
        }

        $reportId = $this->reportModel->create($data);
        
        if ($reportId) {
            // Handle file uploads
            if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
                $this->handleFileUploads($reportId);
            }
            
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'create',
                'Report',
                $reportId,
                "Created report: {$data['title']}"
            );
            
            $this->session->setFlash('success', 'Report created successfully.');
            $this->redirect("/reports/{$reportId}");
        } else {
            $this->session->setFlash('error', 'Failed to create report.');
            $this->redirect('/reports/create');
        }
    }

    /**
     * Handle file uploads for report
     */
    private function handleFileUploads($reportId) {
        try {
            $config = require __DIR__ . '/../../config/config.php';
            $uploadPath = $config['upload']['path'] . '/reports';
            $allowedTypes = $config['upload']['allowed_types'];
            
            $fileUpload = new FileUpload($uploadPath, $allowedTypes);
            $fileUpload->setMaxSize($config['upload']['max_size']);
            
            if (!isset($_FILES['files']) || empty($_FILES['files']['name'][0])) {
                return;
            }
            
            $files = $_FILES['files'];
            $fileCount = is_array($files['name']) ? count($files['name']) : 1;
            
            for ($i = 0; $i < $fileCount; $i++) {
                if (is_array($files['name'])) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $file = [
                            'name' => $files['name'][$i],
                            'type' => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error' => $files['error'][$i],
                            'size' => $files['size'][$i]
                        ];
                        
                        $result = $fileUpload->upload($file, 'report_' . $reportId);
                        
                        if ($result['success']) {
                            $this->reportFileModel->create([
                                'report_id' => $reportId,
                                'file_name' => $result['filename'],
                                'file_path' => str_replace('\\', '/', $result['filepath']), // Normalize path
                                'file_type' => $result['extension'],
                                'file_size' => $result['size']
                            ]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail report creation
            error_log('File upload error: ' . $e->getMessage());
        }
    }

    /**
     * Get reports with details
     */
    public function getReportsWithDetails($conditions = []) {
        return $this->reportModel->getReportsWithDetails($conditions);
    }

    /**
     * Export reports
     */
    public function export() {
        $format = strtolower($this->request->get('format', 'csv'));
        $reports = $this->reportModel->getReportsWithDetails([], 'created_at DESC');
        
        $headers = ['ID', 'Title', 'Unit', 'Type', 'Status', 'Submitted At', 'Created At'];
        $data = [];
        
        foreach ($reports as $report) {
            $data[] = [
                'id' => $report['id'],
                'title' => $report['title'],
                'unit' => $report['unit_name'] ?? '',
                'type' => $report['report_type'],
                'status' => $report['status'],
                'submitted_at' => $report['submitted_at'] ?? '',
                'created_at' => $report['created_at']
            ];
        }
        
        // Normalise filename/extension based on format
        switch ($format) {
            case 'json':
                $filename = 'reports_' . date('Y-m-d_His') . '.json';
                ExportHelper::exportJSON($data, $filename);
                break;
            case 'pdf':
                $filename = 'reports_' . date('Y-m-d_His') . '.pdf';
                ExportHelper::exportPDF($data, $headers, 'Reports Export', $filename);
                break;
            case 'excel':
            case 'xls':
            case 'xlsx':
                $filename = 'reports_' . date('Y-m-d_His') . '.xls';
                ExportHelper::exportExcel($data, $headers, $filename);
                break;
            case 'csv':
            default:
                $filename = 'reports_' . date('Y-m-d_His') . '.csv';
                ExportHelper::exportCSV($data, $headers, $filename);
                break;
        }
    }
}

