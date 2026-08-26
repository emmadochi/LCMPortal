<?php
namespace App\Controllers;

use App\Models\Pledge;
use App\Models\PledgePayment;
use App\Models\Church;
use App\Models\User;
use App\Models\ActivityLog;
use App\Utilities\Validator;
use App\Utilities\Security;

class PledgeController extends BaseController {
    protected Pledge $pledgeModel;
    protected PledgePayment $paymentModel;
    protected Church $churchModel;
    protected User $userModel;

    public function __construct() {
        parent::__construct();
        $this->pledgeModel = new Pledge();
        $this->paymentModel = new PledgePayment();
        $this->churchModel = new Church();
        $this->userModel = new User();
    }

    /**
     * Resolve effective church_id
     */
    protected function resolveChurchId($churchId = null) {
        if ($this->session->isHeadPastor()) {
            return (int)$this->session->getHeadPastorChurchId();
        }
        if ($churchId) {
            return (int)$churchId;
        }
        if ($this->session->get('church_id')) {
            return (int)$this->session->get('church_id');
        }
        return null;
    }

    /**
     * Pledge Dashboard & List
     */
    public function index($churchId = null) {
        $effectiveChurchId = $this->resolveChurchId($churchId);
        $status = $this->request->get('status') ?: null;
        $search = $this->request->get('search') ?: null;

        $conditions = [];
        if ($status) {
            $conditions['status'] = $status;
        }

        $pledges = $this->pledgeModel->getPledgesWithDetails($conditions, $effectiveChurchId, $search);
        $summary = $this->pledgeModel->getPledgesSummary($effectiveChurchId);

        $churches = $this->churchModel->getChurches([]);
        $currentChurch = $effectiveChurchId ? $this->churchModel->find($effectiveChurchId) : null;

        $this->render('pledges/index', [
            'title' => 'Pledges & Campaigns',
            'pageTitle' => 'Pledge Commitments & Redemption',
            'pledges' => $pledges,
            'summary' => $summary,
            'churches' => $churches,
            'currentChurch' => $currentChurch,
            'churchId' => $effectiveChurchId,
            'selectedStatus' => $status,
            'searchTerm' => $search,
            'csrfToken' => Security::generateCSRFToken(),
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => ''],
                ['label' => 'Finances', 'url' => 'finance'],
                ['label' => 'Pledges', 'active' => true]
            ]
        ]);
    }

    /**
     * Create Pledge Form
     */
    public function create($churchId = null) {
        $effectiveChurchId = $this->resolveChurchId($churchId);
        $churches = $this->churchModel->getChurches([]);
        
        // Members list for dropdown
        $memberConditions = [];
        if ($effectiveChurchId) {
            $memberConditions['church_id'] = $effectiveChurchId;
        }
        $members = $this->userModel->findAll($memberConditions, 'first_name ASC');

        $this->render('pledges/create', [
            'title' => 'Record New Pledge',
            'pageTitle' => 'Record Member / Donor Pledge',
            'churches' => $churches,
            'members' => $members,
            'churchId' => $effectiveChurchId,
            'csrfToken' => Security::generateCSRFToken(),
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => ''],
                ['label' => 'Pledges', 'url' => 'pledges'],
                ['label' => 'New Pledge', 'active' => true]
            ]
        ]);
    }

    /**
     * Store Pledge
     */
    public function store($churchId = null) {
        $data = $this->request->post();
        
        $validator = new Validator();
        $rules = [
            'campaign_name' => 'required|max:255',
            'target_amount' => 'required|numeric|min:1',
            'start_date' => 'required|date'
        ];

        if (!$validator->validate($data, $rules)) {
            $this->session->setFlash('errors', $validator->getErrors());
            $redirectUrl = $churchId ? "churches/{$churchId}/pledges/create" : "pledges/create";
            $this->response->redirect($redirectUrl);
            return;
        }

        $targetChurchId = $this->resolveChurchId($data['church_id'] ?? $churchId);
        if (!$targetChurchId) {
            $this->session->setFlash('error', 'Please select a church.');
            $this->response->redirect('pledges/create');
            return;
        }

        $memberId = !empty($data['member_id']) ? (int)$data['member_id'] : null;
        $donorName = trim($data['donor_name'] ?? '');
        
        if (!$memberId && empty($donorName)) {
            $this->session->setFlash('error', 'Please select a registered member or enter a donor name.');
            $this->response->redirect('pledges/create');
            return;
        }

        $pledgeData = [
            'church_id' => $targetChurchId,
            'member_id' => $memberId,
            'donor_name' => $donorName ?: null,
            'donor_email' => trim($data['donor_email'] ?? '') ?: null,
            'donor_phone' => trim($data['donor_phone'] ?? '') ?: null,
            'campaign_name' => trim($data['campaign_name']),
            'target_amount' => (float)$data['target_amount'],
            'amount_paid' => 0.00,
            'start_date' => $data['start_date'],
            'due_date' => !empty($data['due_date']) ? $data['due_date'] : null,
            'frequency' => $data['frequency'] ?? 'one_time',
            'status' => 'pending',
            'notes' => trim($data['notes'] ?? ''),
            'recorded_by' => (int)$this->session->get('user_id')
        ];

        $pledgeId = $this->pledgeModel->create($pledgeData);

        if ($pledgeId) {
            ActivityLog::log(
                $this->session->get('user_id'),
                'pledge_created',
                'Pledge',
                $pledgeId,
                "Created pledge: {$pledgeData['campaign_name']} of \${$pledgeData['target_amount']}"
            );

            // If initial payment was made simultaneously
            if (!empty($data['initial_payment']) && (float)$data['initial_payment'] > 0) {
                $this->pledgeModel->recordPayment($pledgeId, [
                    'amount' => (float)$data['initial_payment'],
                    'payment_date' => $data['start_date'],
                    'payment_method' => $data['initial_payment_method'] ?? 'cash',
                    'reference_number' => $data['initial_payment_ref'] ?? null,
                    'notes' => 'Initial payment upon pledge creation'
                ], (int)$this->session->get('user_id'));
            }

            $this->session->setFlash('success', 'Pledge commitment recorded successfully.');
            $this->response->redirect("pledges/{$pledgeId}");
        } else {
            $this->session->setFlash('error', 'Failed to create pledge.');
            $this->response->redirect('pledges/create');
        }
    }

    /**
     * Show Pledge Details & Payment History
     */
    public function show($id) {
        $pledge = $this->pledgeModel->getPledgeWithPayments($id);
        if (!$pledge) {
            $this->session->setFlash('error', 'Pledge not found.');
            $this->response->redirect('pledges');
            return;
        }

        $this->render('pledges/show', [
            'title' => 'Pledge Details',
            'pageTitle' => 'Pledge: ' . htmlspecialchars($pledge['campaign_name']),
            'pledge' => $pledge,
            'csrfToken' => Security::generateCSRFToken(),
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => ''],
                ['label' => 'Pledges', 'url' => 'pledges'],
                ['label' => 'Details', 'active' => true]
            ]
        ]);
    }

    /**
     * Record Payment against a pledge
     */
    public function recordPayment($id) {
        $pledge = $this->pledgeModel->find($id);
        if (!$pledge) {
            if ($this->request->isAjax()) {
                $this->response->json(['success' => false, 'message' => 'Pledge not found']);
                return;
            }
            $this->session->setFlash('error', 'Pledge not found.');
            $this->response->redirect('pledges');
            return;
        }

        $data = $this->request->post();
        $amount = (float)($data['amount'] ?? 0);

        if ($amount <= 0) {
            if ($this->request->isAjax()) {
                $this->response->json(['success' => false, 'message' => 'Payment amount must be greater than 0']);
                return;
            }
            $this->session->setFlash('error', 'Payment amount must be greater than 0.');
            $this->response->redirect("pledges/{$id}");
            return;
        }

        $result = $this->pledgeModel->recordPayment($id, [
            'amount' => $amount,
            'payment_date' => $data['payment_date'] ?? date('Y-m-d'),
            'payment_method' => $data['payment_method'] ?? 'cash',
            'reference_number' => $data['reference_number'] ?? null,
            'notes' => $data['notes'] ?? null
        ], (int)$this->session->get('user_id'));

        if ($result['success']) {
            ActivityLog::log(
                $this->session->get('user_id'),
                'pledge_payment_recorded',
                'Pledge',
                $id,
                "Recorded payment of \${$amount} for pledge #{$id} (Receipt: {$result['receipt_number']})"
            );

            if ($this->request->isAjax()) {
                $this->response->json($result);
                return;
            }

            $this->session->setFlash('success', "Payment of \${$amount} recorded successfully. Receipt #: {$result['receipt_number']}");
            $this->response->redirect("pledges/{$id}");
        } else {
            if ($this->request->isAjax()) {
                $this->response->json($result);
                return;
            }
            $this->session->setFlash('error', $result['message'] ?? 'Failed to record payment.');
            $this->response->redirect("pledges/{$id}");
        }
    }

    /**
     * Printable Payment Receipt
     */
    public function receipt($paymentId) {
        $receipt = $this->paymentModel->getPaymentReceiptDetails($paymentId);
        if (!$receipt) {
            $this->session->setFlash('error', 'Receipt record not found.');
            $this->response->redirect('pledges');
            return;
        }

        $this->render('pledges/receipt', [
            'title' => 'Pledge Payment Receipt - #' . $receipt['receipt_number'],
            'pageTitle' => 'Payment Receipt',
            'receipt' => $receipt,
            'layout' => 'blank' // blank or clean print layout
        ]);
    }

    /**
     * Member Personal Pledges View
     */
    public function myPledges() {
        $userId = (int)$this->session->get('user_id');
        $pledges = $this->pledgeModel->getPledgesByMember($userId);

        $totalPledged = 0;
        $totalPaid = 0;
        foreach ($pledges as $p) {
            $totalPledged += (float)$p['target_amount'];
            $totalPaid += (float)$p['amount_paid'];
        }

        $this->render('pledges/my_pledges', [
            'title' => 'My Pledges & Commitments',
            'pageTitle' => 'My Pledges & Campaigns',
            'pledges' => $pledges,
            'totalPledged' => $totalPledged,
            'totalPaid' => $totalPaid,
            'remaining' => max(0, $totalPledged - $totalPaid),
            'breadcrumbs' => [
                ['label' => 'Personal Space', 'url' => 'profile'],
                ['label' => 'My Pledges', 'active' => true]
            ]
        ]);
    }

    /**
     * Export Pledges to CSV
     */
    public function export($churchId = null) {
        $effectiveChurchId = $this->resolveChurchId($churchId);
        $pledges = $this->pledgeModel->getPledgesWithDetails([], $effectiveChurchId);

        $filename = "pledges_report_" . date('Ymd') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Church', 'Donor / Member', 'Email', 'Phone', 'Campaign', 'Target Amount', 'Amount Paid', 'Remaining', 'Fulfillment %', 'Start Date', 'Due Date', 'Status']);

        foreach ($pledges as $p) {
            $donorName = !empty($p['member_first_name']) ? ($p['member_first_name'] . ' ' . $p['member_last_name']) : ($p['donor_name'] ?? 'Anonymous');
            $donorEmail = $p['member_email'] ?? $p['donor_email'] ?? '';
            $donorPhone = $p['member_phone'] ?? $p['donor_phone'] ?? '';

            fputcsv($output, [
                $p['id'],
                $p['church_name'] ?? 'Global',
                $donorName,
                $donorEmail,
                $donorPhone,
                $p['campaign_name'],
                number_format($p['target_amount'], 2, '.', ''),
                number_format($p['amount_paid'], 2, '.', ''),
                number_format($p['remaining_balance'], 2, '.', ''),
                $p['fulfillment_pct'] . '%',
                $p['start_date'],
                $p['due_date'] ?? 'N/A',
                $p['status']
            ]);
        }

        fclose($output);
        exit;
    }
}
