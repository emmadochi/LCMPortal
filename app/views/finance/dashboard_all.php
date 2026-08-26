<?php
use App\Utilities\AssetHelper;
?>

<div class="container-fluid p-0">
    <!-- Breadcrumbs & Actions Header -->
    <div class="bg-white border-bottom px-4 py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Global Finances</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-buildings text-primary me-1"></i> Global Financial Administration</h4>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= AssetHelper::url('budgets') ?>" class="btn btn-outline-primary">
                    <i class="bx bx-pie-chart-alt-2 me-1"></i> Budgets
                </a>
                <a href="<?= AssetHelper::url('pledges') ?>" class="btn btn-outline-success">
                    <i class="bx bx-gift me-1"></i> Pledges & Campaigns
                </a>
                <a href="<?= AssetHelper::url('finance/cashflow') ?>" class="btn btn-outline-info">
                    <i class="bx bx-line-chart me-1"></i> Cashflow & YoY
                </a>
                <a href="<?= AssetHelper::url('finance/audit-trail') ?>" class="btn btn-outline-secondary">
                    <i class="bx bx-shield-quarter me-1"></i> Audit Trail
                </a>
            </div>
        </div>
    </div>

    <div class="p-4">
        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Global Total Income</span>
                            <div class="avatar-xs bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white">
                                <i class="bx bx-trending-up font-size-18"></i>
                            </div>
                        </div>
                        <h2 class="mb-1 text-white fw-bold">$<?= number_format($summary['total_income'], 2) ?></h2>
                        <small class="text-white-50"><i class="bx bx-check-circle me-1"></i> <?= $summary['record_count'] ?> Transactions Recorded</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Global Total Expenses</span>
                            <div class="avatar-xs bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white">
                                <i class="bx bx-trending-down font-size-18"></i>
                            </div>
                        </div>
                        <h2 class="mb-1 text-white fw-bold">$<?= number_format($summary['total_expense'], 2) ?></h2>
                        <small class="text-white-50"><i class="bx bx-building-house me-1"></i> Across <?= count($church_breakdown ?? []) ?> Active Churches</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Net Treasury Balance</span>
                            <div class="avatar-xs bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white">
                                <i class="bx bx-wallet font-size-18"></i>
                            </div>
                        </div>
                        <h2 class="mb-1 text-white fw-bold">$<?= number_format($summary['net_balance'], 2) ?></h2>
                        <small class="text-white-50">
                            Status: <span class="badge bg-white text-<?= $summary['net_balance'] >= 0 ? 'success' : 'danger' ?> fw-bold"><?= $summary['net_balance'] >= 0 ? 'Positive Surplus' : 'Deficit' ?></span>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-line-chart me-1 text-primary"></i> 6-Month Inflow & Outflow Trend</h5>
                        <a href="<?= AssetHelper::url('finance/cashflow') ?>" class="btn btn-sm btn-link text-primary text-decoration-none">Full Cashflow &rarr;</a>
                    </div>
                    <div class="card-body">
                        <div id="financeTrendChart" style="min-height: 280px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-buildings me-1 text-success"></i> Church Distribution</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th class="ps-3">Church</th>
                                        <th class="text-end">Income</th>
                                        <th class="text-end pe-3">Net</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($church_breakdown as $church): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold small text-dark">
                                            <a href="<?= AssetHelper::url('churches/' . $church['id'] . '/finance') ?>" class="text-dark text-decoration-none">
                                                <?= htmlspecialchars($church['name']) ?>
                                            </a>
                                        </td>
                                        <td class="text-end text-success small fw-semibold">
                                            $<?= number_format($church['income'], 0) ?>
                                        </td>
                                        <td class="text-end pe-3 small fw-bold <?= $church['balance'] >= 0 ? 'text-primary' : 'text-danger' ?>">
                                            $<?= number_format($church['balance'], 0) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-3">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">Transaction Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="income" <?= ($filters['type'] ?? '') === 'income' ? 'selected' : '' ?>>Income</option>
                            <option value="expense" <?= ($filters['type'] ?? '') === 'expense' ? 'selected' : '' ?>>Expense</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter-alt me-1"></i> Apply Filters</button>
                        <a href="<?= AssetHelper::url('finance') ?>" class="btn btn-light" title="Reset"><i class="bx bx-refresh"></i></a>
                        <a href="<?= AssetHelper::url('finance/export/csv') ?>" class="btn btn-outline-secondary" title="Export CSV"><i class="bx bx-download"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-receipt me-1 text-primary"></i> Recent Transactions (<?= count($records) ?>)</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($records)): ?>
                    <div class="text-center py-5">
                        <i class="bx bx-receipt text-muted font-size-36 mb-2"></i>
                        <h6 class="text-muted">No financial records match the selected filters.</h6>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Date</th>
                                    <th>Church / Unit</th>
                                    <th>Category</th>
                                    <th>Description / Member</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-end pe-4">Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($records as $r): ?>
                                    <tr>
                                        <td class="ps-4 small fw-semibold text-dark">
                                            <?= date('M d, Y', strtotime($r['transaction_date'])) ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark small"><?= htmlspecialchars($r['church_name'] ?? 'Church-wide') ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($r['unit_name'] ?? 'General') ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?= htmlspecialchars($r['category'] ?: 'Uncategorized') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-dark"><?= htmlspecialchars($r['description'] ?? 'Transaction') ?></div>
                                            <?php if (!empty($r['member_first_name'])): ?>
                                                <div class="small text-muted"><i class="bx bx-user me-1"></i><?= htmlspecialchars($r['member_first_name'] . ' ' . $r['member_last_name']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-bold fs-6 <?= $r['transaction_type'] === 'income' ? 'text-success' : 'text-danger' ?>">
                                            <?= $r['transaction_type'] === 'income' ? '+' : '-' ?>$<?= number_format($r['amount'], 2) ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-soft-<?= $r['transaction_type'] === 'income' ? 'success' : 'danger' ?> text-<?= $r['transaction_type'] === 'income' ? 'success' : 'danger' ?> px-2 py-1">
                                                <?= ucfirst($r['transaction_type']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4 small text-muted">
                                            <?= htmlspecialchars(trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?: 'Admin') ?>
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

<!-- ApexCharts Script -->
<script src="<?= AssetHelper::lib('apexcharts/apexcharts.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var chartData = <?= json_encode($chart_data) ?>;
    
    var options = {
        series: [{
            name: 'Income',
            data: chartData.income || []
        }, {
            name: 'Expense',
            data: chartData.expense || []
        }],
        chart: {
            height: 280,
            type: 'area',
            toolbar: { show: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        colors: ['#28a745', '#dc3545'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100, 100]
            }
        },
        xaxis: {
            categories: chartData.labels || []
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return "$" + val.toLocaleString();
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "$" + val.toLocaleString(undefined, {minimumFractionDigits: 2});
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#financeTrendChart"), options);
    chart.render();
});
</script>
