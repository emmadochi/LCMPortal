<?php
use App\Utilities\AssetHelper;

$donorDisplayName = !empty($pledge['member_first_name']) ? ($pledge['member_first_name'] . ' ' . $pledge['member_last_name']) : ($pledge['donor_name'] ?? 'Guest Donor');
$statusBadge = $pledge['status'] === 'fulfilled' ? 'success' : ($pledge['status'] === 'in_progress' ? 'info' : 'warning');
?>

<div class="container-fluid p-0">
    <div class="bg-white border-bottom px-4 py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('pledges') ?>">Pledges</a></li>
                        <li class="breadcrumb-item active">Pledge #<?= $pledge['id'] ?></li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-gift text-success me-1"></i> Pledge: <?= htmlspecialchars($pledge['campaign_name']) ?></h4>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= AssetHelper::url('pledges') ?>" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Pledges
                </a>
                <?php if ($pledge['status'] !== 'fulfilled'): ?>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#quickPaymentModal">
                        <i class="bx bx-dollar-circle me-1"></i> Record Payment
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="p-4">
        <div class="row g-4">
            <!-- Left Column: Pledge Summary Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="avatar-md bg-soft-success text-success rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                            <i class="bx bx-gift font-size-28"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($donorDisplayName) ?></h5>
                        <p class="text-muted small mb-3"><?= htmlspecialchars($pledge['campaign_name']) ?></p>
                        
                        <span class="badge rounded-pill bg-<?= $statusBadge ?> px-3 py-2 fs-6 mb-4">
                            <?= ucfirst(str_replace('_', ' ', $pledge['status'])) ?>
                        </span>

                        <div class="p-3 bg-light rounded-3 text-start mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Target Commitment:</span>
                                <span class="fw-bold text-dark">$<?= number_format($pledge['target_amount'], 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Paid to Date:</span>
                                <span class="fw-bold text-success">$<?= number_format($pledge['amount_paid'], 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Remaining Balance:</span>
                                <span class="fw-bold text-danger">$<?= number_format($pledge['remaining_balance'], 2) ?></span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Fulfillment:</span>
                                <span class="fw-bold text-<?= $statusBadge ?>"><?= $pledge['fulfillment_pct'] ?>%</span>
                            </div>
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar bg-<?= $statusBadge ?>" role="progressbar" style="width: <?= min(100, $pledge['fulfillment_pct']) ?>%"></div>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush text-start small">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Start Date:</span>
                                <span class="fw-semibold"><?= date('M d, Y', strtotime($pledge['start_date'])) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Due Date:</span>
                                <span class="fw-semibold <?= !empty($pledge['is_overdue']) ? 'text-danger' : '' ?>">
                                    <?= !empty($pledge['due_date']) ? date('M d, Y', strtotime($pledge['due_date'])) : 'No deadline' ?>
                                    <?= !empty($pledge['is_overdue']) ? ' (Overdue)' : '' ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Frequency:</span>
                                <span class="fw-semibold"><?= ucwords(str_replace('_', ' ', $pledge['frequency'])) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Church:</span>
                                <span class="fw-semibold"><?= htmlspecialchars($pledge['church_name'] ?? 'Global') ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Column: Payments History -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-receipt me-1 text-primary"></i> Installment Payments & Receipts (<?= count($pledge['payments'] ?? []) ?>)</h5>
                        <?php if ($pledge['status'] !== 'fulfilled'): ?>
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#quickPaymentModal">
                                <i class="bx bx-plus me-1"></i> Add Payment
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($pledge['payments'])): ?>
                            <div class="text-center py-5">
                                <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                                    <i class="bx bx-credit-card text-muted font-size-24"></i>
                                </div>
                                <h6 class="text-muted">No payments recorded yet</h6>
                                <p class="text-muted small">Record installments as the member redeems their pledge commitment.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Receipt #</th>
                                            <th>Date</th>
                                            <th class="text-end">Amount</th>
                                            <th>Method</th>
                                            <th>Reference</th>
                                            <th>Recorded By</th>
                                            <th class="text-end pe-4">Receipt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pledge['payments'] as $payment): ?>
                                            <tr>
                                                <td class="ps-4 fw-bold text-dark">
                                                    <?= htmlspecialchars($payment['receipt_number'] ?: '#' . $payment['id']) ?>
                                                </td>
                                                <td>
                                                    <?= date('M d, Y', strtotime($payment['payment_date'])) ?>
                                                </td>
                                                <td class="text-end fw-bold text-success">
                                                    $<?= number_format($payment['amount'], 2) ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">
                                                        <?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?>
                                                    </span>
                                                </td>
                                                <td class="small text-muted">
                                                    <?= htmlspecialchars($payment['reference_number'] ?: '—') ?>
                                                </td>
                                                <td class="small text-muted">
                                                    <?= htmlspecialchars(trim(($payment['recorded_first_name'] ?? '') . ' ' . ($payment['recorded_last_name'] ?? '')) ?: 'Admin') ?>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="<?= AssetHelper::url("pledges/receipt/{$payment['id']}") ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Print Official Receipt">
                                                        <i class="bx bx-printer me-1"></i> Receipt
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Payment Modal -->
<?php if ($pledge['status'] !== 'fulfilled'): ?>
<div class="modal fade" id="quickPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white"><i class="bx bx-dollar-circle me-1"></i> Record Pledge Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= AssetHelper::url("pledges/{$pledge['id']}/payment") ?>">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <div class="modal-body p-4">
                    <div class="alert alert-light border mb-3">
                        <div class="small text-muted">Outstanding Balance: <strong class="text-danger">$<?= number_format($pledge['remaining_balance'], 2) ?></strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Amount ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="amount" class="form-control" value="<?= $pledge['remaining_balance'] ?>" max="<?= $pledge['remaining_balance'] ?>" min="0.01" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash</option>
                                <option value="pos">POS / Card</option>
                                <option value="online">Online / Gateway</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reference Number / Transaction ID</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="e.g. TRF-938210">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4"><i class="bx bx-check me-1"></i> Save & Generate Receipt</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
