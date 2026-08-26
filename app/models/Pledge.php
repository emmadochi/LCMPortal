<?php
namespace App\Models;

class Pledge extends BaseModel {
    protected $table = 'pledges';
    protected $fillable = [
        'church_id', 'member_id', 'donor_name', 'donor_email', 'donor_phone',
        'campaign_name', 'target_amount', 'amount_paid', 'start_date', 'due_date',
        'frequency', 'status', 'notes', 'recorded_by'
    ];

    /**
     * Get pledges with full details
     */
    public function getPledgesWithDetails($conditions = [], $churchId = null, $search = null) {
        $sql = "SELECT p.*,
                       c.name AS church_name,
                       m.first_name AS member_first_name,
                       m.last_name AS member_last_name,
                       m.email AS member_email,
                       m.phone AS member_phone,
                       rec.first_name AS recorded_first_name,
                       rec.last_name AS recorded_last_name
                FROM pledges p
                LEFT JOIN churches c ON p.church_id = c.id
                LEFT JOIN users m ON p.member_id = m.id
                LEFT JOIN users rec ON p.recorded_by = rec.id
                WHERE 1=1";
        
        $params = [];
        $types = "";

        if ($churchId) {
            $sql .= " AND p.church_id = ?";
            $params[] = (int)$churchId;
            $types .= "i";
        }

        if (!empty($conditions)) {
            foreach ($conditions as $field => $val) {
                $sql .= " AND p.{$field} = ?";
                $params[] = $val;
                $types .= is_int($val) ? "i" : "s";
            }
        }

        if (!empty($search)) {
            $sql .= " AND (p.campaign_name LIKE ? OR m.first_name LIKE ? OR m.last_name LIKE ? OR p.donor_name LIKE ?)";
            $term = "%" . $search . "%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $types .= "ssss";
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $pledges = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($pledges as &$pledge) {
            $target = (float)$pledge['target_amount'];
            $paid = (float)$pledge['amount_paid'];
            $pledge['remaining_balance'] = max(0, $target - $paid);
            $pledge['fulfillment_pct'] = $target > 0 ? min(100, round(($paid / $target) * 100, 1)) : 0;
            
            // Check overdue
            if ($pledge['status'] !== 'fulfilled' && $pledge['status'] !== 'cancelled' && !empty($pledge['due_date'])) {
                if (strtotime($pledge['due_date']) < strtotime(date('Y-m-d'))) {
                    $pledge['is_overdue'] = true;
                }
            }
        }
        unset($pledge);

        return $pledges;
    }

    /**
     * Get single pledge with its payment history
     */
    public function getPledgeWithPayments($pledgeId) {
        $pledge = $this->find($pledgeId);
        if (!$pledge) {
            return null;
        }

        $pledges = $this->getPledgesWithDetails(['id' => $pledgeId]);
        $pledgeData = !empty($pledges) ? $pledges[0] : $pledge;

        $paymentModel = new PledgePayment();
        $pledgeData['payments'] = $paymentModel->getPaymentsByPledge($pledgeId);

        return $pledgeData;
    }

    /**
     * Record a payment against a pledge and automatically create the matching FinanceRecord
     */
    public function recordPayment($pledgeId, $paymentData, $recordedBy) {
        $pledge = $this->find($pledgeId);
        if (!$pledge) {
            return ['success' => false, 'message' => 'Pledge not found'];
        }

        $amount = (float)$paymentData['amount'];
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Payment amount must be greater than 0'];
        }

        $paymentDate = $paymentData['payment_date'] ?? date('Y-m-d');
        $paymentMethod = $paymentData['payment_method'] ?? 'cash';
        $referenceNumber = $paymentData['reference_number'] ?? null;
        $notes = $paymentData['notes'] ?? ('Payment for pledge: ' . $pledge['campaign_name']);
        $receiptNumber = 'PLG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        // 1. Create finance_record entry
        $financeModel = new FinanceRecord();
        $financeRecordId = $financeModel->create([
            'church_id' => $pledge['church_id'],
            'member_id' => $pledge['member_id'],
            'transaction_type' => 'income',
            'amount' => $amount,
            'category' => 'Pledge Redemption',
            'description' => "Pledge Payment - {$pledge['campaign_name']}" . ($referenceNumber ? " (Ref: {$referenceNumber})" : ""),
            'transaction_date' => $paymentDate,
            'payment_method' => $paymentMethod,
            'reference_number' => $referenceNumber,
            'recorded_by' => $recordedBy,
            'user_id' => $recordedBy
        ]);

        // 2. Create pledge_payment entry
        $paymentModel = new PledgePayment();
        $paymentId = $paymentModel->create([
            'pledge_id' => $pledgeId,
            'finance_record_id' => $financeRecordId ?: null,
            'amount' => $amount,
            'payment_date' => $paymentDate,
            'payment_method' => $paymentMethod,
            'reference_number' => $referenceNumber,
            'receipt_number' => $receiptNumber,
            'notes' => $notes,
            'recorded_by' => $recordedBy
        ]);

        if (!$paymentId) {
            return ['success' => false, 'message' => 'Failed to record pledge payment'];
        }

        // 3. Update pledge amount_paid & status
        $newAmountPaid = (float)$pledge['amount_paid'] + $amount;
        $targetAmount = (float)$pledge['target_amount'];
        
        $newStatus = 'in_progress';
        if ($newAmountPaid >= $targetAmount) {
            $newStatus = 'fulfilled';
        }

        $this->update($pledgeId, [
            'amount_paid' => $newAmountPaid,
            'status' => $newStatus
        ]);

        return [
            'success' => true,
            'payment_id' => $paymentId,
            'receipt_number' => $receiptNumber,
            'new_amount_paid' => $newAmountPaid,
            'new_status' => $newStatus
        ];
    }

    /**
     * Get aggregate pledges summary
     */
    public function getPledgesSummary($churchId = null) {
        $sql = "SELECT 
                    COUNT(*) AS total_pledges,
                    COALESCE(SUM(target_amount), 0) AS total_target,
                    COALESCE(SUM(amount_paid), 0) AS total_redeemed,
                    SUM(CASE WHEN status = 'fulfilled' THEN 1 ELSE 0 END) AS fulfilled_count,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count
                FROM pledges
                WHERE 1=1";
        
        $params = [];
        $types = "";
        if ($churchId) {
            $sql .= " AND church_id = ?";
            $params[] = (int)$churchId;
            $types .= "i";
        }

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        $target = (float)$res['total_target'];
        $redeemed = (float)$res['total_redeemed'];
        $res['remaining'] = max(0, $target - $redeemed);
        $res['fulfillment_pct'] = $target > 0 ? round(($redeemed / $target) * 100, 1) : 0;

        return $res;
    }

    /**
     * Get member pledges
     */
    public function getPledgesByMember($memberId) {
        return $this->getPledgesWithDetails(['member_id' => $memberId]);
    }
}
