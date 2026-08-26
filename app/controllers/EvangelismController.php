<?php
namespace App\Controllers;

use App\Models\EvangelismReport;
use App\Utilities\Security;

class EvangelismController extends BaseController {
    private $evangelismReportModel;

    public function __construct() {
        parent::__construct();
        $this->evangelismReportModel = new EvangelismReport();
        $this->authorize('user'); // Ensure user is logged in
    }

    public function index() {
        $reports = $this->evangelismReportModel->getReportsByUserId($this->session->get('user_id'));

        $this->render('evangelism/index', [
            'title' => 'Evangelism Reports',
            'pageTitle' => 'My Evangelism Reports',
            'reports' => $reports
        ]);
    }

    public function create() {
        $csrfToken = Security::generateCSRFToken();

        $this->render('evangelism/create', [
            'title' => 'New Evangelism Report',
            'pageTitle' => 'New Evangelism Report',
            'csrf_token' => $csrfToken
        ]);
    }

    public function store() {
        $token = $this->request->post('_token');
        if (!Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/evangelism/create');
        }

        $validation = $this->validate([
            'report_date' => 'required|date',
            'souls_won' => 'required|numeric'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/evangelism/create');
        }

        $data = [
            'user_id' => $this->session->get('user_id'),
            'report_date' => $this->request->post('report_date'),
            'souls_won' => $this->request->post('souls_won'),
            'notes' => $this->request->post('notes')
        ];

        if ($this->evangelismReportModel->create($data)) {
            $this->session->setFlash('success', 'Evangelism report submitted successfully.');
            $this->redirect('/evangelism');
        } else {
            $this->session->setFlash('error', 'Failed to submit report.');
            $this->redirect('/evangelism/create');
        }
    }

    public function show($id) {
        $record = $this->evangelismReportModel->find((int)$id);
        if (!$record || (int)$record['user_id'] !== (int)$this->session->get('user_id')) {
            $this->session->setFlash('error', 'Report not found or access denied.');
            $this->redirect('/evangelism');
        }
        $this->render('evangelism/show', [
            'title' => 'Evangelism Report',
            'pageTitle' => 'Evangelism Report',
            'record' => $record
        ]);
    }

    public function edit($id) {
        $csrfToken = Security::generateCSRFToken();
        $record = $this->evangelismReportModel->find((int)$id);
        if (!$record || (int)$record['user_id'] !== (int)$this->session->get('user_id')) {
            $this->session->setFlash('error', 'Report not found or access denied.');
            $this->redirect('/evangelism');
        }
        $this->render('evangelism/edit', [
            'title' => 'Edit Evangelism Report',
            'pageTitle' => 'Edit Evangelism Report',
            'csrf_token' => $csrfToken,
            'record' => $record
        ]);
    }

    public function update($id) {
        $token = $this->request->post('_token');
        if (!Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/evangelism/{$id}/edit");
        }

        $record = $this->evangelismReportModel->find((int)$id);
        if (!$record || (int)$record['user_id'] !== (int)$this->session->get('user_id')) {
            $this->session->setFlash('error', 'Report not found or access denied.');
            $this->redirect('/evangelism');
        }

        $validation = $this->validate([
            'report_date' => 'required|date',
            'souls_won' => 'required|numeric'
        ]);
        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/evangelism/{$id}/edit");
        }

        $data = [
            'report_date' => $this->request->post('report_date'),
            'souls_won' => $this->request->post('souls_won'),
            'notes' => $this->request->post('notes')
        ];

        if ($this->evangelismReportModel->update((int)$id, $data)) {
            $this->session->setFlash('success', 'Report updated successfully.');
            $this->redirect("/evangelism/{$id}");
        } else {
            $this->session->setFlash('error', 'Failed to update report.');
            $this->redirect("/evangelism/{$id}/edit");
        }
    }

    public function delete($id) {
        $token = $this->request->post('_token');
        if (!Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/evangelism');
        }

        $record = $this->evangelismReportModel->find((int)$id);
        if (!$record || (int)$record['user_id'] !== (int)$this->session->get('user_id')) {
            $this->session->setFlash('error', 'Report not found or access denied.');
            $this->redirect('/evangelism');
        }

        if ($this->evangelismReportModel->delete((int)$id)) {
            $this->session->setFlash('success', 'Report deleted.');
        } else {
            $this->session->setFlash('error', 'Failed to delete report.');
        }
        $this->redirect('/evangelism');
    }

    public function export() {
        $userId = (int)$this->session->get('user_id');
        $dateFrom = $this->request->get('date_from');
        $dateTo = $this->request->get('date_to');

        $conditions = ['user_id' => $userId];
        $orderBy = 'report_date DESC';

        // Basic filter in memory if date range provided
        $reports = $this->evangelismReportModel->findAll($conditions, $orderBy);
        if ($dateFrom || $dateTo) {
            $df = $dateFrom ? strtotime($dateFrom) : null;
            $dt = $dateTo ? strtotime($dateTo) : null;
            $reports = array_filter($reports, function($r) use ($df, $dt) {
                $rd = strtotime($r['report_date']);
                if ($df && $rd < $df) return false;
                if ($dt && $rd > $dt) return false;
                return true;
            });
        }

        $headers = ['Report Date', 'Souls Won', 'Notes', 'Submitted On'];
        $rows = [];
        foreach ($reports as $r) {
            $rows[] = [
                $r['report_date'],
                $r['souls_won'],
                $r['notes'] ?? '',
                $r['created_at'] ?? ''
            ];
        }

        $format = strtolower($this->request->get('format', 'csv'));
        $baseName = 'evangelism_reports_user_' . $userId . '_' . date('Y-m-d_His');
        switch ($format) {
            case 'json':
                \App\Utilities\ExportHelper::exportJSON(array_values($rows), $baseName . '.json');
                break;
            case 'pdf':
                \App\Utilities\ExportHelper::exportPDF($rows, $headers, 'Evangelism Reports', $baseName . '.pdf');
                break;
            case 'excel':
            case 'xls':
            case 'xlsx':
                \App\Utilities\ExportHelper::exportExcel($rows, $headers, $baseName . '.xls');
                break;
            default:
                \App\Utilities\ExportHelper::exportCSV($rows, $headers, $baseName . '.csv');
                break;
        }
    }
}
