<?php
use App\Utilities\AssetHelper;
?>

<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="bg-white border-bottom px-4 py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('finance') ?>">Finances</a></li>
                        <li class="breadcrumb-item active">Pledges & Campaigns</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-gift text-success me-1"></i> Pledge Commitments & Redemption</h4>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= AssetHelper::url('pledges/export' . ($churchId ? '?church_id=' . $churchId : '')) ?>" class="btn btn-outline-secondary waves-effect">
                    <i class="bx bx-download me-1"></i> Export CSV
                </a>
                <a href="<?= AssetHelper::url($churchId ? "churches/{$churchId}/pledges/create" : "pledges/create") ?>" class="btn btn-success waves-effect waves-light">
                    <i class="bx bx-plus me-1"></i> Record New Pledge
                </a>
            </div>
        </div>
    </div>

    <div class="p-4">
        <!-- KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Total Target Pledges</span>
                            <div class="avatar-xs bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white">
                                <i class="bx bx-target-lock font-size-18"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 text-white fw-bold">$<?= number_format($summary['total_target'], 2) ?></h3>
                        <small class="text-white-50"><?= $summary['total_pledges'] ?> Total Commitments</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Total Redeemed (Paid)</span>
                            <div class="avatar-xs bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white">
                                <i class="bx bx-check-circle font-size-18"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 text-white fw-bold">$<?= number_format($summary['total_redeemed'], 2) ?></h3>
                        <small class="text-white-50"><?= $summary['fulfilled_count'] ?> Fully Fulfilled</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Outstanding Balance</span>
                            <div class="avatar-xs bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white">
                                <i class="bx bx-hourglass font-size-18"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 text-white fw-bold">$<?= number_format($summary['remaining'], 2) ?></h3>
                        <small class="text-white-50"><?= $summary['in_progress_count'] + $summary['pending_count'] ?> Pending Redemptions</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #302b63 0%, #24243e 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Fulfillment Rate</span>
                            <div class="avatar-xs bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white">
                                <i class="bx bx-trending-up font-size-18"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 text-white fw-bold"><?= $summary['fulfillment_pct'] ?>%</h3>
                        <div class="progress mt-2" style="height: 6px; background-color: rgba(255,255,255,0.3);">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= min(100, $summary['fulfillment_pct']) ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-3">
                <form method="GET" action="<?= AssetHelper::url($churchId ? "churches/{$churchId}/pledges" : "pledges") ?>" class="row g-3 align-items-end">
                    <?php if ($this->session->hasPermission('manage_users') && !empty($churches)): ?>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">Church / Branch</label>
                        <select name="church_id" class="form-select select2">
                            <option value="">All Churches (Global)</option>
                            <?php foreach ($churches as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($churchId == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted">Search Donor / Campaign</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Donor name, email or campaign..." value="<?= htmlspecialchars($searchTerm ?? '') ?>">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">Fulfillment Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" <?= ($selectedStatus === 'pending') ? 'selected' : '' ?>>Pending (0% paid)</option>
                            <option value="in_progress" <?= ($selectedStatus === 'in_progress') ? 'selected' : '' ?>>In Progress (Partial)</option>
                            <option value="fulfilled" <?= ($selectedStatus === 'fulfilled') ? 'selected' : '' ?>>Fulfilled (100%)</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-success w-100"><i class="bx bx-filter-alt me-1"></i> Filter</button>
                        <a href="<?= AssetHelper::url($churchId ? "churches/{$churchId}/pledges" : "pledges") ?>" class="btn btn-light" title="Reset"><i class="bx bx-refresh"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pledges Table -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-list-check me-1 text-success"></i> Registered Pledges (<?= count($pledges) ?>)</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pledges)): ?>
                    <div class="text-center py-5">
                        <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                            <i class="bx bx-gift text-muted font-size-24"></i>
                        </div>
                        <h5 class="text-muted">No pledge commitments found</h5>
                        <p class="text-muted small mb-3">Log member and partner commitments for building campaigns, missions, or special projects.</p>
                        <a href="<?= AssetHelper::url($churchId ? "churches/{$churchId}/pledges/create" : "pledges/create") ?>" class="btn btn-sm btn-success">
                            <i class="bx bx-plus me-1"></i> Record First Pledge
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Donor / Member</th>
                                    <th>Campaign</th>
                                    <th class="text-end">Target Pledge</th>
                                    <th class="text-end">Amount Paid</th>
                                    <th style="min-width: 150px;">Progress</th>
                                    <th>Frequency / Due</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pledges as $p): ?>
                                    <?php 
                                        $donorDisplayName = !empty($p['member_first_name']) ? ($p['member_first_name'] . ' ' . $p['member_last_name']) : ($p['donor_name'] ?? 'Guest Donor');
                                        $statusBadge = $p['status'] === 'fulfilled' ? 'success' : ($p['status'] === 'in_progress' ? 'info' : 'warning');
                                    ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold">
                                                    <?= strtoupper(substr($donorDisplayName, 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <a href="<?= AssetHelper::url("pledges/{$p['id']}") ?>" class="fw-bold text-dark text-decoration-none">
                                                        <?= htmlspecialchars($donorDisplayName) ?>
                                                    </a>
                                                    <div class="small text-muted">
                                                        <?= htmlspecialchars($p['member_phone'] ?? $p['donor_phone'] ?? $p['church_name'] ?? '') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <i class="bx bx-flag text-primary me-1"></i><?= htmlspecialchars($p['campaign_name']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            $<?= number_format($p['target_amount'], 2) ?>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            $<?= number_format($p['amount_paid'], 2) ?>
                                            <div class="small text-muted">Bal: $<?= number_format($p['remaining_balance'], 2) ?></div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-between mb-1 small">
                                                <span class="fw-bold text-<?= $statusBadge ?>">
                                                    <?= $p['fulfillment_pct'] ?>%
                                                </span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-<?= $statusBadge ?>" role="progressbar" style="width: <?= min(100, $p['fulfillment_pct']) ?>%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold"><?= ucwords(str_replace('_', ' ', $p['frequency'])) ?></div>
                                            <div class="small <?= !empty($p['is_overdue']) ? 'text-danger fw-bold' : 'text-muted' ?>">
                                                <?= !empty($p['due_date']) ? date('M d, Y', strtotime($p['due_date'])) : 'No deadline' ?>
                                                <?= !empty($p['is_overdue']) ? ' <span class="badge bg-danger">Overdue</span>' : '' ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-<?= $statusBadge ?>">
                                                <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-1">
                                                <?php if ($p['status'] !== 'fulfilled'): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#paymentModal<?= $p['id'] ?>" title="Record Payment">
                                                        <i class="bx bx-dollar"></i> Pay
                                                    </button>
                                                <?php endif; ?>
                                                <a href="<?= AssetHelper::url("pledges/{$p['id']}") ?>" class="btn btn-sm btn-light" title="View Details">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            </div>

                                            <!-- Payment Modal -->
                                            <?php if ($p['status'] !== 'fulfilled'): ?>
                                            <div class="modal fade text-start" id="paymentModal<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-header bg-success text-white">
                                                            <h5 class="modal-title text-white"><i class="bx bx-dollar-circle me-1"></i> Record Payment for Pledge</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form method="POST" action="<?= AssetHelper::url("pledges/{$p['id']}/payment") ?>">
                                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                            <div class="modal-body p-4">
                                                                <div class="alert alert-light border mb-3">
                                                                    <div class="small text-muted">Donor: <strong><?= htmlspecialchars($donorDisplayName) ?></strong></div>
                                                                    <div class="small text-muted">Campaign: <strong><?= htmlspecialchars($p['campaign_name']) ?></strong></div>
                                                                    <div class="small text-muted">Outstanding Balance: <strong class="text-danger">$<?= number_format($p['remaining_balance'], 2) ?></strong></div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-semibold">Payment Amount ($) <span class="text-danger">*</span></label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">$</span>
                                                                        <input type="number" step="0.01" name="amount" class="form-control" value="<?= $p['remaining_balance'] ?>" max="<?= $p['remaining_balance'] ?>" min="0.01" required>
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
                                                                    <input type="text" name="reference_number" class="form-control" placeholder="e.g. TRF-8392193">
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-semibold">Notes</label>
                                                                    <input type="text" name="notes" class="form-control" placeholder="Optional notes for this installment">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-success px-4"><i class="bx bx-check me-1"></i> Save Payment & Generate Receipt</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
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
