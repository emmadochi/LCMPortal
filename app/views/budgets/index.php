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
.fin-accent-budget { background: linear-gradient(90deg, #4f46e5, #818cf8); }
.fin-accent-spent { background: linear-gradient(90deg, #f43f5e, #fb7185); }
.fin-accent-rem { background: linear-gradient(90deg, #10b981, #34d399); }
.fin-accent-util { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

.fin-icon-box {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
}
.fin-icon-budget { background: #eef2ff; color: #4f46e5; }
.fin-icon-spent { background: #fff1f2; color: #f43f5e; }
.fin-icon-rem { background: #ecfdf5; color: #10b981; }
.fin-icon-util { background: #fffbeb; color: #f59e0b; }

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
                        <li class="breadcrumb-item active text-primary fw-semibold">Budget Management</li>
                    </ol>
                </nav>
                <h3 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-pie-chart-alt-2 text-primary"></i> Budget Management & Performance
                </h3>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= AssetHelper::url('budgets/export?year=' . $selectedYear . ($churchId ? '&church_id=' . $churchId : '')) ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bx bx-download me-1"></i> Export CSV
                </a>
                <a href="<?= AssetHelper::url($churchId ? "churches/{$churchId}/budgets/create" : "budgets/create") ?>" class="btn btn-primary rounded-pill px-4">
                    <i class="bx bx-plus me-1"></i> New Budget Allocation
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Budgeted -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-budget"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Total Budgeted</div>
                    <div class="fin-icon-box fin-icon-budget">
                        <i class="bx bx-wallet"></i>
                    </div>
                </div>
                <div class="fin-value text-primary">
                    ₦<?= number_format($summary['total_budgeted'], 2) ?>
                </div>
                <div class="fin-subtext">
                    <i class="bx bx-calendar text-muted me-1"></i> Fiscal Year <?= $selectedYear ?>
                </div>
            </div>
        </div>

        <!-- Actual Expenditures -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-spent"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Actual Expenditures</div>
                    <div class="fin-icon-box fin-icon-spent">
                        <i class="bx bx-trending-down"></i>
                    </div>
                </div>
                <div class="fin-value text-danger">
                    ₦<?= number_format($summary['total_spent'], 2) ?>
                </div>
                <div class="fin-subtext">
                    <i class="bx bx-receipt text-muted me-1"></i> Spent across active allocations
                </div>
            </div>
        </div>

        <!-- Remaining Funds -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-rem"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Remaining Funds</div>
                    <div class="fin-icon-box fin-icon-rem">
                        <i class="bx bx-check-shield"></i>
                    </div>
                </div>
                <div class="fin-value text-success">
                    ₦<?= number_format($summary['remaining'], 2) ?>
                </div>
                <div class="fin-subtext">
                    <i class="bx bx-shield-quarter text-muted me-1"></i> Available for disbursement
                </div>
            </div>
        </div>

        <!-- Overall Utilization -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-util"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Overall Utilization</div>
                    <div class="fin-icon-box fin-icon-util">
                        <i class="bx bx-tachometer"></i>
                    </div>
                </div>
                <div class="fin-value text-dark">
                    <?= $summary['utilization_pct'] ?>%
                </div>
                <div class="progress mt-2" style="height: 6px; border-radius: 4px; background: #e2e8f0;">
                    <div class="progress-bar bg-<?= $summary['utilization_pct'] > 90 ? 'danger' : ($summary['utilization_pct'] > 75 ? 'warning' : 'success') ?>" role="progressbar" style="width: <?= min(100, $summary['utilization_pct']) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="fin-panel p-3 mb-4">
        <form method="GET" action="<?= AssetHelper::url($churchId ? "churches/{$churchId}/budgets" : "budgets") ?>" class="row g-3 align-items-end">
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

            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted text-uppercase">Fiscal Year</label>
                <select name="year" class="form-select">
                    <?php for ($y = date('Y') + 1; $y >= date('Y') - 3; $y--): ?>
                        <option value="<?= $y ?>" <?= ($selectedYear == $y) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <?php if (!empty($units)): ?>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Unit / Department</label>
                <select name="unit_id" class="form-select">
                    <option value="">All Units / Church-wide</option>
                    <?php foreach ($units as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($selectedUnit == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted text-uppercase">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" <?= ($selectedStatus === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="draft" <?= ($selectedStatus === 'draft') ? 'selected' : '' ?>>Draft</option>
                    <option value="closed" <?= ($selectedStatus === 'closed') ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill"><i class="bx bx-filter-alt me-1"></i> Filter</button>
                <a href="<?= AssetHelper::url($churchId ? "churches/{$churchId}/budgets" : "budgets") ?>" class="btn btn-light rounded-pill" title="Reset"><i class="bx bx-refresh"></i></a>
            </div>
        </form>
    </div>

    <!-- Budget Allocations Table -->
    <div class="fin-panel">
        <div class="fin-panel-header">
            <h5 class="fin-panel-title">
                <i class="bx bx-list-ul text-primary fs-5"></i> Budget Allocations (<?= count($budgets) ?>)
            </h5>
        </div>
        <div class="fin-panel-body p-0">
            <?php if (empty($budgets)): ?>
                <div class="text-center py-5">
                    <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                        <i class="bx bx-folder-open text-muted font-size-24"></i>
                    </div>
                    <h5 class="text-dark fw-bold">No budget allocations found</h5>
                    <p class="text-muted small mb-3">Get started by creating a budget allocation for the selected period.</p>
                    <a href="<?= AssetHelper::url($churchId ? "churches/{$churchId}/budgets/create" : "budgets/create") ?>" class="btn btn-primary rounded-pill px-4">
                        <i class="bx bx-plus me-1"></i> Add Budget Allocation
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="fin-table mb-0">
                        <thead>
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
                                            <span class="badge bg-light text-secondary border me-1"><?= htmlspecialchars($b['category'] ?: 'General') ?></span>
                                            <?= htmlspecialchars($b['church_name'] ?? 'All Churches') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary">
                                            <?= htmlspecialchars($b['unit_name'] ?: 'Church-wide') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark"><?= ucfirst($b['period_type']) ?> (<?= $b['fiscal_year'] ?>)</div>
                                        <div class="small text-muted"><?= date('M d', strtotime($b['start_date'])) ?> - <?= date('M d, Y', strtotime($b['end_date'])) ?></div>
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        ₦<?= number_format($b['total_budget_amount'], 2) ?>
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        ₦<?= number_format($b['actual_spent'], 2) ?>
                                        <div class="small text-muted">Rem: ₦<?= number_format($b['remaining_amount'], 2) ?></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between mb-1 small">
                                            <span class="badge bg-soft-<?= $b['health_badge'] ?> text-<?= $b['health_badge'] ?> fw-bold">
                                                <?= $b['utilization_pct'] ?>%
                                            </span>
                                            <span class="small">
                                                <?= $b['health_status'] === 'exceeded' ? '<span class="text-danger fw-semibold"><i class="bx bx-error"></i> Exceeded</span>' : ($b['health_status'] === 'caution' ? '<span class="text-warning fw-semibold"><i class="bx bx-info-circle"></i> Caution</span>' : '<span class="text-success fw-semibold"><i class="bx bx-check"></i> On Track</span>') ?>
                                            </span>
                                        </div>
                                        <div class="progress" style="height: 6px; border-radius: 4px; background: #f1f5f9;">
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
                                            <button class="btn btn-sm btn-light dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="<?= AssetHelper::url("budgets/{$b['id']}/edit") ?>">
                                                        <i class="bx bx-edit text-primary me-1"></i> Edit Allocation
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
