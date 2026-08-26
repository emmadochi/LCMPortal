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
                        <li class="breadcrumb-item active">Budgets</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-pie-chart-alt-2 text-primary me-1"></i> Budget Management & Performance</h4>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= AssetHelper::url('budgets/export?year=' . $selectedYear . ($churchId ? '&church_id=' . $churchId : '')) ?>" class="btn btn-outline-secondary waves-effect">
                    <i class="bx bx-download me-1"></i> Export CSV
                </a>
                <a href="<?= AssetHelper::url($churchId ? "churches/{$churchId}/budgets/create" : "budgets/create") ?>" class="btn btn-primary waves-effect waves-light">
                    <i class="bx bx-plus me-1"></i> New Budget Allocation
                </a>
            </div>
        </div>
    </div>

    <div class="p-4">
        <!-- KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Total Budgeted</span>
                            <div class="avatar-xs bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white">
                                <i class="bx bx-wallet font-size-18"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 text-white fw-bold">$<?= number_format($summary['total_budgeted'], 2) ?></h3>
                        <small class="text-white-50"><i class="bx bx-calendar me-1"></i> Fiscal Year <?= $selectedYear ?></small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #e65c00 0%, #f9d423 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Actual Expenditures</span>
                            <div class="avatar-xs bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white">
                                <i class="bx bx-trending-down font-size-18"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 text-white fw-bold">$<?= number_format($summary['total_spent'], 2) ?></h3>
                        <small class="text-white-50">Spent across active allocations</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Remaining Funds</span>
                            <div class="avatar-xs bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white">
                                <i class="bx bx-check-shield font-size-18"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 text-white fw-bold">$<?= number_format($summary['remaining'], 2) ?></h3>
                        <small class="text-white-50">Available for disbursement</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Overall Utilization</span>
                            <div class="avatar-xs bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white">
                                <i class="bx bx-tachometer font-size-18"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 text-white fw-bold"><?= $summary['utilization_pct'] ?>%</h3>
                        <div class="progress mt-2" style="height: 6px; background-color: rgba(255,255,255,0.3);">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?= min(100, $summary['utilization_pct']) ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-3">
                <form method="GET" action="<?= AssetHelper::url($churchId ? "churches/{$churchId}/budgets" : "budgets") ?>" class="row g-3 align-items-end">
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

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">Fiscal Year</label>
                        <select name="year" class="form-select">
                            <?php for ($y = date('Y') + 1; $y >= date('Y') - 3; $y--): ?>
                                <option value="<?= $y ?>" <?= ($selectedYear == $y) ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <?php if (!empty($units)): ?>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">Unit / Department</label>
                        <select name="unit_id" class="form-select">
                            <option value="">All Units / Church-wide</option>
                            <?php foreach ($units as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= ($selectedUnit == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="active" <?= ($selectedStatus === 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="draft" <?= ($selectedStatus === 'draft') ? 'selected' : '' ?>>Draft</option>
                            <option value="closed" <?= ($selectedStatus === 'closed') ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter-alt me-1"></i> Filter</button>
                        <a href="<?= AssetHelper::url($churchId ? "churches/{$churchId}/budgets" : "budgets") ?>" class="btn btn-light" title="Reset"><i class="bx bx-refresh"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Budget Allocations Table -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-list-ul me-1 text-primary"></i> Budget Allocations (<?= count($budgets) ?>)</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($budgets)): ?>
                    <div class="text-center py-5">
                        <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                            <i class="bx bx-folder-open text-muted font-size-24"></i>
                        </div>
                        <h5 class="text-muted">No budget allocations found</h5>
                        <p class="text-muted small mb-3">Get started by creating a budget allocation for the selected period.</p>
                        <a href="<?= AssetHelper::url($churchId ? "churches/{$churchId}/budgets/create" : "budgets/create") ?>" class="btn btn-sm btn-primary">
                            <i class="bx bx-plus me-1"></i> Add Budget Allocation
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Budget Item</th>
                                    <th>Scope / Unit</th>
                                    <th>Period</th>
                                    <th class="text-end">Budget Target</th>
                                    <th class="text-end">Actual Spent</th>
                                    <th style="min-width: 150px;">Utilization</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($budgets as $b): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($b['title']) ?></div>
                                            <div class="small text-muted">
                                                <span class="badge bg-light text-secondary me-1"><?= htmlspecialchars($b['category'] ?: 'General') ?></span>
                                                <?= htmlspecialchars($b['church_name'] ?? 'All Churches') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-primary text-primary">
                                                <?= htmlspecialchars($b['unit_name'] ?: 'Church-wide') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold"><?= ucfirst($b['period_type']) ?> (<?= $b['fiscal_year'] ?>)</div>
                                            <div class="small text-muted"><?= date('M d', strtotime($b['start_date'])) ?> - <?= date('M d, Y', strtotime($b['end_date'])) ?></div>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            $<?= number_format($b['total_budget_amount'], 2) ?>
                                        </td>
                                        <td class="text-end fw-bold text-danger">
                                            $<?= number_format($b['actual_spent'], 2) ?>
                                            <div class="small text-muted">Rem: $<?= number_format($b['remaining_amount'], 2) ?></div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-between mb-1 small">
                                                <span class="badge bg-soft-<?= $b['health_badge'] ?> text-<?= $b['health_badge'] ?> fw-bold">
                                                    <?= $b['utilization_pct'] ?>%
                                                </span>
                                                <span class="text-muted small">
                                                    <?= $b['health_status'] === 'exceeded' ? '<span class="text-danger"><i class="bx bx-error"></i> Exceeded</span>' : ($b['health_status'] === 'caution' ? '<span class="text-warning"><i class="bx bx-info-circle"></i> Caution</span>' : '<span class="text-success"><i class="bx bx-check"></i> On Track</span>') ?>
                                                </span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-<?= $b['health_badge'] ?>" role="progressbar" style="width: <?= min(100, $b['utilization_pct']) ?>%"></div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-<?= $b['status'] === 'active' ? 'success' : ($b['status'] === 'draft' ? 'warning' : 'secondary') ?>">
                                                <?= ucfirst($b['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-horizontal-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="<?= AssetHelper::url("budgets/{$b['id']}/edit") ?>">
                                                            <i class="bx bx-edit text-primary me-1"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form method="POST" action="<?= AssetHelper::url("budgets/{$b['id']}/delete") ?>" onsubmit="return confirm('Are you sure you want to delete this budget allocation?');">
                                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
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
