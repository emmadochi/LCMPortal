<?php
namespace App\Models;

class PledgePayment extends BaseModel {
    protected $table = 'pledge_payments';
    protected $fillable = [
        'pledge_id', 'finance_record_id', 'amount', 'payment_date', 
        'payment_method', 'reference_number', 'receipt_number', 
        'notes', 'recorded_by'
    ];

    /**
     * Get payments for a specific pledge
     */
    public function getPaymentsByPledge($pledgeId) {
        $sql = "SELECT pp.*, 
                       u.first_name AS recorded_first_name, 
                       u.last_name AS recorded_last_name 
                FROM pledge_payments pp
                LEFT JOIN users u ON pp.recorded_by = u.id
                WHERE pp.pledge_id = ?
                ORDER BY pp.payment_date DESC, pp.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $pledgeId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Find payment with full pledge & donor details for receipts
     */
    public function getPaymentReceiptDetails($paymentId) {
        $sql = "SELECT pp.*, 
                       p.campaign_name, p.target_amount, p.amount_paid AS pledge_total_paid, p.church_id,
                       c.name AS church_name, c.address AS church_address,
                       m.first_name AS member_first_name, m.last_name AS member_last_name, m.email AS member_email, m.phone AS member_phone,
                       p.donor_name, p.donor_email, p.donor_phone,
                       rec.first_name AS recorded_first_name, rec.last_name AS recorded_last_name
                FROM pledge_payments pp
                JOIN pledges p ON pp.pledge_id = p.id
                LEFT JOIN churches c ON p.church_id = c.id
                LEFT JOIN users m ON p.member_id = m.id
                LEFT JOIN users rec ON pp.recorded_by = rec.id
                WHERE pp.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $paymentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
