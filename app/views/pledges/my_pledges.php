<?php
use App\Utilities\AssetHelper;
?>

<div class="container-fluid p-0">
    <div class="bg-white border-bottom px-4 py-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('profile') ?>">Personal Space</a></li>
                        <li class="breadcrumb-item active">My Pledges</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-gift text-success me-1"></i> My Pledges & Commitments</h4>
            </div>
            <a href="<?= AssetHelper::url('giving/my-records') ?>" class="btn btn-outline-primary">
                <i class="bx bx-history me-1"></i> View Giving History
            </a>
        </div>
    </div>

    <div class="p-4">
        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="card-body p-3">
                        <span class="text-white-50 small text-uppercase fw-semibold">Total Pledged</span>
                        <h3 class="mb-0 mt-1 text-white fw-bold">$<?= number_format($totalPledged, 2) ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 bg-gradient" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                    <div class="card-body p-3">
                        <span class="text-white-50 small text-uppercase fw-semibold">Total Redeemed</span>
                        <h3 class="mb-0 mt-1 text-white fw-bold">$<?= number_format($totalPaid, 2) ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 bg-gradient" style="background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%); color: white;">
                    <div class="card-body p-3">
                        <span class="text-white-50 small text-uppercase fw-semibold">Remaining Balance</span>
                        <h3 class="mb-0 mt-1 text-white fw-bold">$<?= number_format($remaining, 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pledges List -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-transparent py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-list-check me-1 text-success"></i> My Active & Past Commitments</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pledges)): ?>
                    <div class="text-center py-5">
                        <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                            <i class="bx bx-gift text-muted font-size-24"></i>
                        </div>
                        <h6 class="text-muted">No pledges recorded</h6>
                        <p class="text-muted small">You do not currently have any active pledges registered.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Campaign Name</th>
                                    <th class="text-end">Target Pledge</th>
                                    <th class="text-end">Amount Paid</th>
                                    <th style="min-width: 160px;">Progress</th>
                                    <th>Frequency / Due</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-4">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pledges as $p): ?>
                                    <?php $statusBadge = $p['status'] === 'fulfilled' ? 'success' : ($p['status'] === 'in_progress' ? 'info' : 'warning'); ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($p['campaign_name']) ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($p['church_name'] ?? '') ?></div>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            $<?= number_format($p['target_amount'], 2) ?>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            $<?= number_format($p['amount_paid'], 2) ?>
                                            <div class="small text-muted">Bal: $<?= number_format($p['remaining_balance'], 2) ?></div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between small mb-1">
                                                <span class="fw-bold text-<?= $statusBadge ?>"><?= $p['fulfillment_pct'] ?>%</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-<?= $statusBadge ?>" role="progressbar" style="width: <?= min(100, $p['fulfillment_pct']) ?>%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold"><?= ucwords(str_replace('_', ' ', $p['frequency'])) ?></div>
                                            <div class="small text-muted"><?= !empty($p['due_date']) ? date('M d, Y', strtotime($p['due_date'])) : 'Ongoing' ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-<?= $statusBadge ?>">
                                                <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="<?= AssetHelper::url("pledges/{$p['id']}") ?>" class="btn btn-sm btn-light">
                                                <i class="bx bx-show me-1"></i> View Payments
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
