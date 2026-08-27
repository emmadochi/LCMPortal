<?php
use App\Utilities\AssetHelper;
?>

<style>
:root {
    --fin-emerald: #10b981;
    --fin-rose: #f43f5e;
    --fin-indigo: #4f46e5;
    --fin-amber: #f59e0b;
    --fin-dark: #0f172a;
    --fin-surface: #ffffff;
    --fin-border: #e2e8f0;
    --fin-sub: #64748b;
    --fin-radius: 16px;
}

.fin-dashboard {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: var(--fin-dark);
}

.fin-header-card {
    background: #ffffff;
    border-radius: var(--fin-radius);
    padding: 22px 28px;
    border: 1px solid var(--fin-border);
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    margin-bottom: 24px;
}

.fin-metric-card {
    background: #ffffff;
    border-radius: var(--fin-radius);
    border: 1px solid var(--fin-border);
    box-shadow: 0 4px 16px rgba(0,0,0,0.04);
    padding: 22px 24px;
    position: relative;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    height: 100%;
}
.fin-metric-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.08);
}
.fin-metric-accent {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
}
.fin-accent-target { background: linear-gradient(90deg, #4f46e5, #818cf8); }
.fin-accent-redeemed { background: linear-gradient(90deg, #10b981, #34d399); }
.fin-accent-balance { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.fin-accent-rate { background: linear-gradient(90deg, #0ea5e9, #38bdf8); }

.fin-icon-box {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
}
.fin-icon-target { background: #eef2ff; color: #4f46e5; }
.fin-icon-redeemed { background: #ecfdf5; color: #10b981; }
.fin-icon-balance { background: #fffbeb; color: #f59e0b; }
.fin-icon-rate { background: #f0f9ff; color: #0284c7; }

.fin-label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--fin-sub);
    margin-bottom: 6px;
}
.fin-value {
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--fin-dark);
    letter-spacing: -0.5px;
    line-height: 1.2;
    margin-bottom: 6px;
}
.fin-subtext {
    font-size: 0.78rem;
    color: var(--fin-sub);
    display: flex;
    align-items: center;
    gap: 6px;
}

.fin-panel {
    background: #ffffff;
    border-radius: var(--fin-radius);
    border: 1px solid var(--fin-border);
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    overflow: hidden;
    margin-bottom: 24px;
}
.fin-panel-header {
    padding: 18px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
}
.fin-panel-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--fin-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.fin-table {
    width: 100%;
    border-collapse: collapse;
}
.fin-table thead th {
    background: #f8fafc;
    color: #64748b;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 12px 20px;
    border-bottom: 1px solid var(--fin-border);
}
.fin-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}
.fin-table tbody tr:hover {
    background: #f8fafc;
}
.fin-table td {
    padding: 14px 20px;
    font-size: 0.88rem;
    color: var(--fin-dark);
    vertical-align: middle;
}
</style>

<div class="container-fluid p-0 fin-dashboard">
    <!-- Header Section -->
    <div class="fin-header-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('finance') ?>" class="text-decoration-none text-muted">Finances</a></li>
                        <li class="breadcrumb-item active text-success fw-semibold">Pledges & Campaigns</li>
                    </ol>
                </nav>
                <h3 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-gift text-success"></i> Pledge Commitments & Redemption
                </h3>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= AssetHelper::url('pledges/export' . ($churchId ? '?church_id=' . $churchId : '')) ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bx bx-download me-1"></i> Export CSV
                </a>
                <a href="<?= AssetHelper::url($churchId ? "churches/{$churchId}/pledges/create" : "pledges/create") ?>" class="btn btn-success rounded-pill px-4">
                    <i class="bx bx-plus me-1"></i> Record New Pledge
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Target Pledges -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-target"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Total Target Pledges</div>
                    <div class="fin-icon-box fin-icon-target">
                        <i class="bx bx-target-lock"></i>
                    </div>
                </div>
                <div class="fin-value text-primary">
                    ₦<?= number_format($summary['total_target'], 2) ?>
                </div>
                <div class="fin-subtext">
                    <i class="bx bx-list-check text-muted me-1"></i> <?= $summary['total_pledges'] ?> Total Commitments
                </div>
            </div>
        </div>

        <!-- Total Redeemed (Paid) -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-redeemed"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Total Redeemed (Paid)</div>
                    <div class="fin-icon-box fin-icon-redeemed">
                        <i class="bx bx-check-circle"></i>
                    </div>
                </div>
                <div class="fin-value text-success">
                    ₦<?= number_format($summary['total_redeemed'], 2) ?>
                </div>
                <div class="fin-subtext">
                    <span class="badge bg-soft-success text-success fw-bold"><?= $summary['fulfilled_count'] ?> Fulfilled</span>
                </div>
            </div>
        </div>

        <!-- Outstanding Balance -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-balance"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Outstanding Balance</div>
                    <div class="fin-icon-box fin-icon-balance">
                        <i class="bx bx-hourglass"></i>
                    </div>
                </div>
                <div class="fin-value text-warning">
                    ₦<?= number_format($summary['remaining'], 2) ?>
                </div>
                <div class="fin-subtext">
                    <?= $summary['in_progress_count'] + $summary['pending_count'] ?> Pending Redemptions
                </div>
            </div>
        </div>

        <!-- Fulfillment Rate -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-rate"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Fulfillment Rate</div>
                    <div class="fin-icon-box fin-icon-rate">
                        <i class="bx bx-trending-up"></i>
                    </div>
                </div>
                <div class="fin-value text-dark">
                    <?= $summary['fulfillment_pct'] ?>%
                </div>
                <div class="progress mt-2" style="height: 6px; border-radius: 4px; background: #e2e8f0;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= min(100, $summary['fulfillment_pct']) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="fin-panel p-3 mb-4">
        <form method="GET" action="<?= AssetHelper::url($churchId ? "churches/{$churchId}/pledges" : "pledges") ?>" class="row g-3 align-items-end">
            <?php if ($this->session->hasPermission('manage_users') && !empty($churches)): ?>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Church / Branch</label>
                <select name="church_id" class="form-select">
                    <option value="">All Churches (Global)</option>
                    <?php foreach ($churches as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($churchId == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted text-uppercase">Search Donor / Campaign</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bx bx-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Donor name, email or campaign..." value="<?= htmlspecialchars($searchTerm ?? '') ?>">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Fulfillment Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= ($selectedStatus === 'pending') ? 'selected' : '' ?>>Pending (0% paid)</option>
                    <option value="in_progress" <?= ($selectedStatus === 'in_progress') ? 'selected' : '' ?>>In Progress (Partial)</option>
                    <option value="fulfilled" <?= ($selectedStatus === 'fulfilled') ? 'selected' : '' ?>>Fulfilled (100%)</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-success w-100 rounded-pill"><i class="bx bx-filter-alt me-1"></i> Filter</button>
                <a href="<?= AssetHelper::url($churchId ? "churches/{$churchId}/pledges" : "pledges") ?>" class="btn btn-light rounded-pill" title="Reset"><i class="bx bx-refresh"></i></a>
            </div>
        </form>
    </div>

    <!-- Pledges Table -->
    <div class="fin-panel">
        <div class="fin-panel-header">
            <h5 class="fin-panel-title">
                <i class="bx bx-list-check text-success fs-5"></i> Registered Pledges (<?= count($pledges) ?>)
            </h5>
        </div>
        <div class="fin-panel-body p-0">
            <?php if (empty($pledges)): ?>
                <div class="text-center py-5">
                    <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                        <i class="bx bx-gift text-muted font-size-24"></i>
                    </div>
                    <h5 class="text-dark fw-bold">No pledge commitments found</h5>
                    <p class="text-muted small mb-3">Log member and partner commitments for building campaigns, missions, or special projects.</p>
                    <a href="<?= AssetHelper::url($churchId ? "churches/{$churchId}/pledges/create" : "pledges/create") ?>" class="btn btn-success rounded-pill px-4">
                        <i class="bx bx-plus me-1"></i> Record First Pledge
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="fin-table mb-0">
                        <thead>
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
                                            <div class="avatar-xs bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem; background: #eef2ff;">
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
                                        ₦<?= number_format($p['target_amount'], 2) ?>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        ₦<?= number_format($p['amount_paid'], 2) ?>
                                        <div class="small text-muted">Bal: ₦<?= number_format($p['remaining_balance'], 2) ?></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between mb-1 small">
                                            <span class="fw-bold text-<?= $statusBadge ?>">
                                                <?= $p['fulfillment_pct'] ?>%
                                            </span>
                                        </div>
                                        <div class="progress" style="height: 6px; border-radius: 4px; background: #f1f5f9;">
                                            <div class="progress-bar bg-<?= $statusBadge ?>" role="progressbar" style="width: <?= min(100, $p['fulfillment_pct']) ?>%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark"><?= ucwords(str_replace('_', ' ', $p['frequency'])) ?></div>
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
                                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#paymentModal<?= $p['id'] ?>" title="Record Payment">
                                                    <i class="bx bx-wallet me-1"></i> Pay
                                                </button>
                                            <?php endif; ?>
                                            <a href="<?= AssetHelper::url("pledges/{$p['id']}") ?>" class="btn btn-sm btn-light rounded-pill" title="View Details">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        </div>

                                        <!-- Payment Modal -->
                                        <?php if ($p['status'] !== 'fulfilled'): ?>
                                        <div class="modal fade text-start" id="paymentModal<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title text-white"><i class="bx bx-wallet me-1"></i> Record Payment for Pledge</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form method="POST" action="<?= AssetHelper::url("pledges/{$p['id']}/payment") ?>">
                                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                        <div class="modal-body p-4">
                                                            <div class="alert alert-light border mb-3">
                                                                <div class="small text-muted">Donor: <strong class="text-dark"><?= htmlspecialchars($donorDisplayName) ?></strong></div>
                                                                <div class="small text-muted">Campaign: <strong class="text-dark"><?= htmlspecialchars($p['campaign_name']) ?></strong></div>
                                                                <div class="small text-muted">Outstanding Balance: <strong class="text-danger">₦<?= number_format($p['remaining_balance'], 2) ?></strong></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold small text-muted text-uppercase">Payment Amount (₦) <span class="text-danger">*</span></label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text bg-light">₦</span>
                                                                    <input type="number" step="0.01" name="amount" class="form-control" value="<?= $p['remaining_balance'] ?>" max="<?= $p['remaining_balance'] ?>" min="0.01" required>
                                                                </div>
                                                            </div>

                                                            <div class="row g-3 mb-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-bold small text-muted text-uppercase">Payment Date</label>
                                                                    <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-bold small text-muted text-uppercase">Payment Method</label>
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
                                                                <label class="form-label fw-bold small text-muted text-uppercase">Reference Number / Transaction ID</label>
                                                                <input type="text" name="reference_number" class="form-control" placeholder="e.g. TRF-8392193">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold small text-muted text-uppercase">Notes</label>
                                                                <input type="text" name="notes" class="form-control" placeholder="Optional notes for this installment">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success rounded-pill px-4"><i class="bx bx-check me-1"></i> Save Payment & Generate Receipt</button>
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
