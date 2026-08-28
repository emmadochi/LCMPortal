<?php
namespace App\Controllers;

use App\Models\EvangelismReport;
use App\Models\EvangelismConvert;
use App\Models\Church;
use App\Models\User;
use App\Utilities\Security;
use App\Utilities\AssetHelper;

class EvangelismController extends BaseController {
    private $evangelismReportModel;
    private $convertModel;

    public function __construct() {
        parent::__construct();
        $this->evangelismReportModel = new EvangelismReport();
        $this->convertModel = new EvangelismConvert();
        if (!$this->session->has('user_id')) {
            $this->redirect('/login');
        }
    }

    public function index() {
        $userId = (int)$this->session->get('user_id');
        $reports = $this->evangelismReportModel->getReportsByUserId($userId);
        $converts = $this->convertModel->getConvertsBySoulWinner($userId);
        $careStats = $this->convertModel->getSoulWinnerCareStats($userId);
        $commendations = $this->convertModel->getPastoralNotes($userId);

        $totalSouls = 0;
        $highestOutreach = 0;
        foreach ($reports as $r) {
            $s = (int)($r['souls_won'] ?? 0);
            $totalSouls += $s;
            if ($s > $highestOutreach) {
                $highestOutreach = $s;
            }
        }
        $totalLogs = count($reports);
        $latestReport = !empty($reports) ? $reports[0]['report_date'] : null;

        $this->render('evangelism/index', [
            'title' => 'Evangelism & Soul Care Journal',
            'pageTitle' => 'Evangelism & Soul Care Journal',
            'reports' => $reports,
            'converts' => $converts,
            'careStats' => $careStats,
            'commendations' => $commendations,
            'userStats' => [
                'total_souls' => max($totalSouls, $careStats['total_converts']),
                'total_logs' => $totalLogs,
                'highest_outreach' => $highestOutreach,
                'latest_report' => $latestReport
            ]
        ]);
    }

    public function create() {
        $csrfToken = Security::generateCSRFToken();

        $this->render('evangelism/create', [
            'title' => 'Log Outreach & Converts',
            'pageTitle' => 'Log Outreach & Converts',
            'csrf_token' => $csrfToken
        ]);
    }

    public function store() {
        $token = $this->request->post('_token');
        if (!Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/evangelism');
        }

        $validation = $this->validate([
            'report_date' => 'required|date',
            'souls_won' => 'required|numeric'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/evangelism/create');
        }

        $userId = (int)$this->session->get('user_id');
        $churchId = $this->session->get('church_id') ?? null;
        $soulsWon = (int)$this->request->post('souls_won');

        $data = [
            'user_id' => $userId,
            'church_id' => $churchId,
            'report_date' => $this->request->post('report_date'),
            'souls_won' => $soulsWon,
            'notes' => $this->request->post('notes')
        ];

        $reportId = $this->evangelismReportModel->create($data);

        if ($reportId) {
            // Check if structured convert details were also submitted
            $convertNames = $this->request->post('convert_name');
            $convertPhones = $this->request->post('convert_phone');
            $convertEmails = $this->request->post('convert_email');
            $convertDecisions = $this->request->post('convert_decision');
            $convertPrayers = $this->request->post('convert_prayer');

            if (is_array($convertNames)) {
                foreach ($convertNames as $idx => $cName) {
                    $cName = trim($cName);
                    if (!empty($cName)) {
                        $this->convertModel->createConvert([
                            'report_id' => $reportId,
                            'soul_winner_id' => $userId,
                            'church_id' => $churchId,
                            'full_name' => $cName,
                            'phone' => $convertPhones[$idx] ?? '',
                            'email' => $convertEmails[$idx] ?? '',
                            'decision_type' => $convertDecisions[$idx] ?? 'salvation',
                            'prayer_requests' => $convertPrayers[$idx] ?? '',
                            'status' => 'new'
                        ]);
                    }
                }
            }

            $this->session->setFlash('success', 'Evangelism outreach and convert details recorded successfully!');
            $this->redirect('/evangelism');
        } else {
            $this->session->setFlash('error', 'Failed to submit outreach report.');
            $this->redirect('/evangelism/create');
        }
    }

    public function convertStore() {
        $token = $this->request->post('_token');
        if (!Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/evangelism');
        }

        $fullName = trim($this->request->post('full_name'));
        if (empty($fullName)) {
            $this->session->setFlash('error', 'Convert full name is required.');
            $this->redirect('/evangelism');
        }

        $userId = (int)$this->session->get('user_id');
        $churchId = $this->session->get('church_id') ?? null;

        $createdId = $this->convertModel->createConvert([
            'soul_winner_id' => $userId,
            'church_id' => $churchId,
            'full_name' => $fullName,
            'phone' => $this->request->post('phone'),
            'email' => $this->request->post('email'),
            'address' => $this->request->post('address'),
            'gender' => $this->request->post('gender'),
            'decision_type' => $this->request->post('decision_type', 'salvation'),
            'prayer_requests' => $this->request->post('prayer_requests'),
            'next_followup_date' => $this->request->post('next_followup_date'),
            'status' => 'new'
        ]);

        if ($createdId) {
            // Also log an automatic evangelism count record if requested
            $this->evangelismReportModel->create([
                'user_id' => $userId,
                'church_id' => $churchId,
                'report_date' => date('Y-m-d'),
                'souls_won' => 1,
                'notes' => 'Soul won: ' . $fullName . ' (' . ($this->request->post('decision_type', 'salvation')) . ')'
            ]);

            $this->session->setFlash('success', "Convert '{$fullName}' added to your Follow-up Care pipeline!");
        } else {
            $this->session->setFlash('error', 'Failed to add convert.');
        }

        $this->redirect('/evangelism');
    }

    public function convertShow($id) {
        $convert = $this->convertModel->getConvertById((int)$id);
        $userId = (int)$this->session->get('user_id');

        if (!$convert) {
            $this->session->setFlash('error', 'Convert record not found.');
            $this->redirect('/evangelism');
        }

        // Only allow soul winner or pastors/admins
        $isAdminOrPastor = $this->session->isAdmin() || $this->session->isHeadPastor();
        if ((int)$convert['soul_winner_id'] !== $userId && !$isAdminOrPastor) {
            $this->session->setFlash('error', 'Access denied.');
            $this->redirect('/evangelism');
        }

        $followupLogs = $this->convertModel->getFollowupLogs((int)$id);

        $this->render('evangelism/convert_show', [
            'title' => 'Convert Care: ' . $convert['full_name'],
            'pageTitle' => 'Convert Care Profile',
            'convert' => $convert,
            'followupLogs' => $followupLogs
        ]);
    }

    public function updateMilestone($id) {
        $token = $this->request->post('_token');
        if (!Security::validateCSRFToken($token)) {
            if ($this->request->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'CSRF error']);
                exit;
            }
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/evangelism');
        }

        $milestone = $this->request->post('milestone');
        $value = $this->request->post('value');

        $updated = $this->convertModel->updateMilestone((int)$id, $milestone, $value);

        if ($this->request->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$updated]);
            exit;
        }

        $this->session->setFlash('success', 'Spiritual milestone updated!');
        $this->redirect('/evangelism/converts/' . (int)$id);
    }

    public function addFollowupLog($id) {
        $token = $this->request->post('_token');
        if (!Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/evangelism/converts/' . (int)$id);
        }

        $userId = (int)$this->session->get('user_id');
        $data = [
            'contact_method' => $this->request->post('contact_method'),
            'outcome' => $this->request->post('outcome'),
            'notes' => $this->request->post('notes'),
            'next_action_date' => $this->request->post('next_action_date'),
            'milestone_updated' => $this->request->post('milestone_updated')
        ];

        if ($this->convertModel->addFollowupLog((int)$id, $userId, $data)) {
            // If milestone was checked in form, update it too
            if (!empty($data['milestone_updated'])) {
                $this->convertModel->updateMilestone((int)$id, $data['milestone_updated'], 1);
            }
            $this->session->setFlash('success', 'Follow-up touchpoint recorded successfully!');
        } else {
            $this->session->setFlash('error', 'Failed to record follow-up log.');
        }

        $this->redirect('/evangelism/converts/' . (int)$id);
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
        $record = $this->evangelismReportModel->find((int)$id);
        if (!$record || (int)$record['user_id'] !== (int)$this->session->get('user_id')) {
            $this->session->setFlash('error', 'Report not found or access denied.');
            $this->redirect('/evangelism');
        }

        $csrfToken = Security::generateCSRFToken();

        $this->render('evangelism/edit', [
            'title' => 'Edit Evangelism Report',
            'pageTitle' => 'Edit Evangelism Report',
            'record' => $record,
            'csrf_token' => $csrfToken
        ]);
    }

    public function update($id) {
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
            $this->redirect('/evangelism');
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

    public function leaderboard() {
        $period = $this->request->get('period', 'month');
        $validPeriods = ['week', 'month', 'quarter', 'year', 'all'];
        if (!in_array($period, $validPeriods)) {
            $period = 'month';
        }

        $churchId = $this->request->get('church_id');
        if ($this->session->isHeadPastor() && !$this->session->isSuperAdmin()) {
            $churchId = $this->session->getHeadPastorChurchId();
        }

        $churchModel = new Church();
        $churches = $churchModel->findAll(['status' => 'active'], 'name ASC');

        $leaderboard = $this->evangelismReportModel->getLeaderboard($period, $churchId, 50);
        $stats = $this->evangelismReportModel->getLeaderboardStats($period, $churchId);
        $harvestTrends = $this->evangelismReportModel->getHarvestTrends($period, $churchId);
        $unitBreakdown = $this->evangelismReportModel->getUnitBreakdown($period, $churchId);
        $verificationLogs = $this->evangelismReportModel->getVerificationLogs($period, $churchId, 50);

        $periodLabels = [
            'week' => 'This Week',
            'month' => 'This Month',
            'quarter' => 'This Quarter',
            'year' => 'This Year (' . date('Y') . ')',
            'all' => 'All Time'
        ];

        // Check for AJAX JSON request
        $isAjax = ($this->request->get('ajax') === '1') || 
                  (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'period' => $period,
                'period_label' => $periodLabels[$period] ?? 'This Month',
                'church_id' => $churchId,
                'stats' => $stats,
                'leaderboard' => $leaderboard,
                'harvestTrends' => $harvestTrends,
                'unitBreakdown' => $unitBreakdown,
                'verificationLogs' => $verificationLogs,
                'exportUrl' => AssetHelper::url('evangelism/leaderboard/export?period=' . $period . ($churchId ? '&church_id=' . $churchId : ''))
            ]);
            exit;
        }

        $this->render('evangelism/leaderboard', [
            'title' => 'Soul Winner Leaderboard & Analytics',
            'pageTitle' => 'Soul Winning Leaderboard',
            'period' => $period,
            'churchId' => $churchId,
            'churches' => $churches,
            'leaderboard' => $leaderboard,
            'stats' => $stats,
            'harvestTrends' => $harvestTrends,
            'unitBreakdown' => $unitBreakdown,
            'verificationLogs' => $verificationLogs
        ]);
    }

    public function memberDetail($id) {
        $userId = (int)$id;
        $userModel = new User();
        $targetUser = $userModel->find($userId);

        if (!$targetUser) {
            if ($this->request->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'User not found']);
                exit;
            }
            $this->redirect('/evangelism/leaderboard');
        }

        $converts = $this->convertModel->getConvertsBySoulWinner($userId, 50);
        $careStats = $this->convertModel->getSoulWinnerCareStats($userId);
        $commendations = $this->convertModel->getPastoralNotes($userId);
        $reports = $this->evangelismReportModel->getReportsByUserId($userId);

        if ($this->request->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $targetUser['id'],
                    'name' => $targetUser['name'],
                    'email' => $targetUser['email']
                ],
                'careStats' => $careStats,
                'converts' => $converts,
                'commendations' => $commendations,
                'reportCount' => count($reports)
            ]);
            exit;
        }

        $this->render('evangelism/member_detail', [
            'title' => 'Harvest Portfolio: ' . $targetUser['name'],
            'pageTitle' => 'Soul Winner Portfolio',
            'targetUser' => $targetUser,
            'careStats' => $careStats,
            'converts' => $converts,
            'commendations' => $commendations,
            'reports' => $reports
        ]);
    }

    public function addCommendation() {
        $token = $this->request->post('_token');
        if (!Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/evangelism/leaderboard');
        }

        // Only allow admins or pastors to leave official commendations
        if (!$this->session->isAdmin() && !$this->session->isHeadPastor()) {
            $this->session->setFlash('error', 'Only Pastors and Administrators can leave official pastoral commendations.');
            $this->redirect('/evangelism/leaderboard');
        }

        $targetUserId = (int)$this->request->post('user_id');
        $pastorId = (int)$this->session->get('user_id');
        $churchId = $this->session->get('church_id') ?? null;
        $message = trim($this->request->post('message'));
        $badgeType = $this->request->post('badge_type', 'commendation');

        if (empty($message)) {
            $this->session->setFlash('error', 'Message cannot be empty.');
            $this->redirect('/evangelism/leaderboard');
        }

        if ($this->convertModel->addPastoralNote($targetUserId, $pastorId, $churchId, $message, $badgeType)) {
            $this->session->setFlash('success', 'Pastoral commendation posted to the soul winner!');
        } else {
            $this->session->setFlash('error', 'Failed to post commendation.');
        }

        $this->redirect('/evangelism/leaderboard');
    }

    public function exportLeaderboard() {
        $period = $this->request->get('period', 'month');
        $churchId = $this->request->get('church_id');
        if ($this->session->isHeadPastor() && !$this->session->isSuperAdmin()) {
            $churchId = $this->session->getHeadPastorChurchId();
        }

        $leaderboard = $this->evangelismReportModel->getLeaderboard($period, $churchId, 500);

        $headers = ['Rank', 'Member Name', 'Email', 'Church Branch', 'Department', 'Total Souls Won', 'Outreach Reports', 'Latest Outreach'];
        $rows = [];

        $rank = 1;
        foreach ($leaderboard as $row) {
            $rows[] = [
                $rank++,
                $row['user_name'],
                $row['user_email'],
                $row['church_name'] ?? 'General',
                $row['unit_name'] ?? 'General',
                (int)$row['total_souls'],
                (int)$row['report_count'],
                $row['latest_outreach'] ? date('Y-m-d', strtotime($row['latest_outreach'])) : 'N/A'
            ];
        }

        $filename = 'soul_winner_leaderboard_' . $period . '_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    public function export() {
        $reports = $this->evangelismReportModel->getReportsByUserId($this->session->get('user_id'));

        $headers = ['Report Date', 'Souls Won', 'Notes', 'Submitted On'];
        $rows = [];

        foreach ($reports as $report) {
            $rows[] = [
                $report['report_date'],
                $report['souls_won'],
                $report['notes'] ?? '',
                $report['created_at']
            ];
        }

        $filename = 'my_evangelism_reports_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }
}
