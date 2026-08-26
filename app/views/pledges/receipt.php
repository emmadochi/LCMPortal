<?php
use App\Utilities\AssetHelper;

$donorName = !empty($receipt['member_first_name']) ? ($receipt['member_first_name'] . ' ' . $receipt['member_last_name']) : ($receipt['donor_name'] ?? 'Donor');
$donorEmail = $receipt['member_email'] ?? $receipt['donor_email'] ?? 'N/A';
$donorPhone = $receipt['member_phone'] ?? $receipt['donor_phone'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt - <?= htmlspecialchars($receipt['receipt_number']) ?></title>
    <link href="<?= AssetHelper::css('bootstrap.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= AssetHelper::css('icons.min.css') ?>" rel="stylesheet" type="text/css" />
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        .receipt-container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }
        .receipt-header {
            border-bottom: 2px dashed #dee2e6;
            padding-bottom: 25px;
            margin-bottom: 25px;
        }
        .stamp {
            border: 2px solid #28a745;
            color: #28a745;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: inline-block;
            transform: rotate(-5deg);
        }
        @media print {
            body { background: #fff; }
            .receipt-container { box-shadow: none; border: none; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="no-print text-center mt-4">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow-sm px-4">
            <i class="bx bx-printer me-1"></i> Print Receipt
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary btn-lg ms-2">
            Close
        </button>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold text-primary mb-1"><?= htmlspecialchars($receipt['church_name'] ?? 'Church Administration') ?></h3>
                <p class="text-muted small mb-0"><?= htmlspecialchars($receipt['church_address'] ?? 'Official Financial Contribution Receipt') ?></p>
            </div>
            <div class="text-end">
                <span class="stamp">PAID & VERIFIED</span>
            </div>
        </div>

        <!-- Receipt Meta -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <span class="text-muted small text-uppercase">Receipt Number</span>
                <div class="fw-bold fs-5 text-dark"><?= htmlspecialchars($receipt['receipt_number']) ?></div>
            </div>
            <div class="col-6 text-end">
                <span class="text-muted small text-uppercase">Date of Payment</span>
                <div class="fw-bold fs-5 text-dark"><?= date('F d, Y', strtotime($receipt['payment_date'])) ?></div>
            </div>
        </div>

        <!-- Donor Information -->
        <div class="p-3 bg-light rounded-3 mb-4">
            <h6 class="fw-bold text-dark text-uppercase small mb-2"><i class="bx bx-user me-1 text-primary"></i> Received From</h6>
            <div class="fw-bold fs-6 text-dark"><?= htmlspecialchars($donorName) ?></div>
            <div class="small text-muted">Email: <?= htmlspecialchars($donorEmail) ?> | Phone: <?= htmlspecialchars($donorPhone) ?></div>
        </div>

        <!-- Payment Details Table -->
        <table class="table table-bordered mb-4">
            <thead class="table-light">
                <tr>
                    <th>Description / Purpose</th>
                    <th>Payment Method</th>
                    <th>Reference</th>
                    <th class="text-end">Amount Paid</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($receipt['campaign_name']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($receipt['notes'] ?: 'Pledge redemption installment') ?></small>
                    </td>
                    <td><?= ucfirst(str_replace('_', ' ', $receipt['payment_method'])) ?></td>
                    <td><?= htmlspecialchars($receipt['reference_number'] ?: '—') ?></td>
                    <td class="text-end fw-bold fs-5 text-success">$<?= number_format($receipt['amount'], 2) ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <th colspan="3" class="text-end">Total Received:</th>
                    <th class="text-end text-success fs-5">$<?= number_format($receipt['amount'], 2) ?></th>
                </tr>
            </tfoot>
        </table>

        <!-- Pledge Status Overview -->
        <div class="row g-3 mb-4 p-3 border rounded-3 bg-white">
            <div class="col-4 text-center border-end">
                <span class="text-muted small">Total Pledge Target</span>
                <div class="fw-bold text-dark">$<?= number_format($receipt['target_amount'], 2) ?></div>
            </div>
            <div class="col-4 text-center border-end">
                <span class="text-muted small">Cumulative Paid</span>
                <div class="fw-bold text-success">$<?= number_format($receipt['pledge_total_paid'], 2) ?></div>
            </div>
            <div class="col-4 text-center">
                <span class="text-muted small">Remaining Balance</span>
                <div class="fw-bold text-danger">$<?= number_format(max(0, $receipt['target_amount'] - $receipt['pledge_total_paid']), 2) ?></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="pt-3 border-top d-flex justify-content-between align-items-end text-muted small">
            <div>
                <div>Recorded By: <strong><?= htmlspecialchars(trim(($receipt['recorded_first_name'] ?? '') . ' ' . ($receipt['recorded_last_name'] ?? '')) ?: 'Finance Office') ?></strong></div>
                <div>Generated: <?= date('Y-m-d H:i:s') ?></div>
            </div>
            <div class="text-end">
                <div style="border-top: 1px solid #999; width: 160px; margin-top: 20px; padding-top: 4px;" class="text-center">Authorized Signature</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
