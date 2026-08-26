<?php
use App\Utilities\AssetHelper;
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <a href="<?= AssetHelper::url('churches/' . $churchId . '/finance/records') ?>" class="btn btn-light btn-sm text-primary">
                            <i class="bx bx-arrow-back"></i> Back
                        </a>
                    </div>
                    <div>
                        <h4 class="text-white mb-1">Transaction #<?= $transaction['id'] ?></h4>
                        <p class="mb-0 text-white-50"><?= date('M d, Y', strtotime($transaction['transaction_date'])) ?> | <?= htmlspecialchars($transaction['category']) ?></p>
                    </div>
                    <div class="ms-auto">
                        <button onclick="window.print()" class="btn btn-soft-light btn-sm me-2">
                            <i class="bx bx-printer"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">General Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label class="text-muted mb-1">Transaction Type</label>
                        <div>
                            <span class="badge rounded-pill bg-soft-<?= $transaction['transaction_type'] === 'income' ? 'success text-success' : 'danger text-danger' ?> font-size-14 px-3">
                                <?= ucfirst($transaction['transaction_type']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <label class="text-muted mb-1">Amount</label>
                        <h2 class="fw-bold text-<?= $transaction['transaction_type'] === 'income' ? 'success' : 'danger' ?>">
                            ₦<?= number_format($transaction['amount'], 2) ?>
                        </h2>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="text-muted mb-1">Description</label>
                        <p class="lead font-size-16 text-dark bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($transaction['description'])) ?>
                        </p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label class="text-muted mb-1">Category</label>
                        <h6 class="fw-bold"><?= htmlspecialchars($transaction['category']) ?></h6>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted mb-1">Payment Method</label>
                        <h6 class="fw-bold"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $transaction['payment_method'] ?? 'N/A'))) ?></h6>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <label class="text-muted mb-1">Reference Number</label>
                        <h6 class="fw-bold"><?= htmlspecialchars($transaction['reference_number'] ?: 'None') ?></h6>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted mb-1">Transaction Date</label>
                        <h6 class="fw-bold"><?= date('l, F j, Y', strtotime($transaction['transaction_date'])) ?></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Metadata Sidebar -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">Record Metadata</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="text-muted mb-1 small uppercase fw-bold">Unit Assignment</label>
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                <i class="bx bx-building"></i>
                            </span>
                        </div>
                        <h6 class="mb-0"><?= htmlspecialchars($transaction['unit_name'] ?? 'Church-wide') ?></h6>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted mb-1 small uppercase fw-bold">Recorded By</label>
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title rounded-circle bg-soft-info text-info">
                                <i class="bx bx-user"></i>
                            </span>
                        </div>
                        <h6 class="mb-0"><?= htmlspecialchars(($transaction['first_name'] ?? '') . ' ' . ($transaction['last_name'] ?? '')) ?></h6>
                    </div>
                </div>

                <?php if (!empty($transaction['member_id'])): ?>
                <div class="mb-4">
                    <label class="text-muted mb-1 small uppercase fw-bold">Associated Member</label>
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title rounded-circle bg-soft-warning text-warning">
                                <i class="bx bx-user-circle"></i>
                            </span>
                        </div>
                        <h6 class="mb-0"><?= htmlspecialchars(($transaction['member_first_name'] ?? '') . ' ' . ($transaction['member_last_name'] ?? '')) ?></h6>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-4 pt-3 border-top">
                    <small class="text-muted d-block mb-1">System Audit</small>
                    <p class="small text-muted mb-0">
                        <i class="bx bx-time"></i> Created at: <?= date('M d, Y H:i', strtotime($transaction['created_at'])) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="card bg-info bg-opacity-10 border-info">
            <div class="card-body">
                <h6 class="text-info"><i class="bx bx-info-circle me-1"></i> Audit Note</h6>
                <p class="small text-info mb-0">This record is immutable once fully reconciled. For corrections, please contact the financial administrator.</p>
            </div>
        </div>
    </div>
</div>
