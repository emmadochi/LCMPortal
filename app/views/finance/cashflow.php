<?php
use App\Utilities\AssetHelper;

$months = $cashflow['months'] ?? [];
$monthsLabels = array_map(function($m) { return $m['month_short']; }, $months);
$inflowsData = array_map(function($m) { return round($m['operating_inflows'], 2); }, $months);
$outflowsData = array_map(function($m) { return round($m['operating_outflows'], 2); }, $months);
$netData = array_map(function($m) { return round($m['net_cashflow'], 2); }, $months);
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="bg-white border-bottom px-4 py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('finance') ?>">Finances</a></li>
                        <li class="breadcrumb-item active">Cashflow & Trends</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-line-chart text-primary me-1"></i> Cashflow Statement & Year-over-Year Analytics</h4>
            </div>
            
            <form method="GET" class="d-flex gap-2 align-items-center">
                <?php if ($this->session->hasPermission('manage_users') && !empty($churches)): ?>
                    <select name="church_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Churches (Global)</option>
                        <?php foreach ($churches as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($churchId == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                    <?php for ($y = date('Y'); $y >= date('Y') - 4; $y--): ?>
                        <option value="<?= $y ?>" <?= ($selectedYear == $y) ? 'selected' : '' ?>><?= $y ?> Fiscal Year</option>
                    <?php endfor; ?>
                </select>
                
                <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-printer"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="p-4">
        <!-- KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Annual Total Inflows</span>
                            <i class="bx bx-trending-up font-size-22"></i>
                        </div>
                        <h3 class="mb-1 text-white fw-bold">$<?= number_format($cashflow['total_inflow'], 2) ?></h3>
                        <small class="text-white-50">
                            YoY Growth: <span class="fw-bold text-white"><?= ($yoy['income_growth_pct'] >= 0 ? '+' : '') . $yoy['income_growth_pct'] ?>%</span> vs <?= $yoy['previous_year'] ?>
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Annual Total Outflows</span>
                            <i class="bx bx-trending-down font-size-22"></i>
                        </div>
                        <h3 class="mb-1 text-white fw-bold">$<?= number_format($cashflow['total_outflow'], 2) ?></h3>
                        <small class="text-white-50">
                            Expense Change: <span class="fw-bold text-white"><?= ($yoy['expense_growth_pct'] >= 0 ? '+' : '') . $yoy['expense_growth_pct'] ?>%</span> vs <?= $yoy['previous_year'] ?>
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Net Annual Cashflow</span>
                            <i class="bx bx-wallet font-size-22"></i>
                        </div>
                        <h3 class="mb-1 text-white fw-bold">$<?= number_format($cashflow['net_annual_cashflow'], 2) ?></h3>
                        <small class="text-white-50">
                            Net Growth: <span class="fw-bold text-white"><?= ($yoy['net_growth_pct'] >= 0 ? '+' : '') . $yoy['net_growth_pct'] ?>%</span>
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-gradient" style="background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-white-50 small text-uppercase fw-semibold">Operating Inflow Margin</span>
                            <i class="bx bx-pie-chart-alt font-size-22"></i>
                        </div>
                        <?php 
                            $margin = $cashflow['total_inflow'] > 0 
                                ? round(($cashflow['net_annual_cashflow'] / $cashflow['total_inflow']) * 100, 1) 
                                : 0; 
                        ?>
                        <h3 class="mb-1 text-white fw-bold"><?= $margin ?>%</h3>
                        <small class="text-white-50">Retained Cash Percentage</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ApexChart Section -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-bar-chart-alt-2 me-1 text-primary"></i> Monthly Cash Inflow vs Outflow (FY <?= $selectedYear ?>)</h5>
                <span class="badge bg-soft-primary text-primary">Interactive Analytics</span>
            </div>
            <div class="card-body">
                <div id="cashflowApexChart" style="min-height: 350px;"></div>
            </div>
        </div>

        <!-- Cashflow Statement Table -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-table me-1 text-primary"></i> 12-Month Structured Cashflow Statement (FY <?= $selectedYear ?>)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0 text-nowrap">
                        <thead class="table-light text-center">
                            <tr>
                                <th class="text-start ps-3">Line Item</th>
                                <?php foreach ($months as $m): ?>
                                    <th><?= $m['month_short'] ?></th>
                                <?php endforeach; ?>
                                <th class="bg-light fw-bold">Full Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Inflows Row -->
                            <tr class="table-success table-opacity-25">
                                <td class="ps-3 fw-bold text-success"><i class="bx bx-plus-circle me-1"></i> Operating Inflows (Income)</td>
                                <?php foreach ($months as $m): ?>
                                    <td class="text-end fw-semibold">$<?= number_format($m['operating_inflows'], 2) ?></td>
                                <?php endforeach; ?>
                                <td class="text-end fw-bold bg-light text-success">$<?= number_format($cashflow['total_inflow'], 2) ?></td>
                            </tr>

                            <!-- Outflows Row -->
                            <tr class="table-danger table-opacity-25">
                                <td class="ps-3 fw-bold text-danger"><i class="bx bx-minus-circle me-1"></i> Operating Outflows (Expenses)</td>
                                <?php foreach ($months as $m): ?>
                                    <td class="text-end fw-semibold text-danger">$<?= number_format($m['operating_outflows'], 2) ?></td>
                                <?php endforeach; ?>
                                <td class="text-end fw-bold bg-light text-danger">$<?= number_format($cashflow['total_outflow'], 2) ?></td>
                            </tr>

                            <!-- Net Monthly Cashflow -->
                            <tr class="fw-bold">
                                <td class="ps-3 text-primary"><i class="bx bx-dollar-circle me-1"></i> Net Monthly Cashflow</td>
                                <?php foreach ($months as $m): ?>
                                    <td class="text-end <?= $m['net_cashflow'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                        $<?= number_format($m['net_cashflow'], 2) ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="text-end bg-light text-primary fs-6">$<?= number_format($cashflow['net_annual_cashflow'], 2) ?></td>
                            </tr>

                            <!-- Cumulative Cash Balance -->
                            <tr class="table-light">
                                <td class="ps-3 fw-bold text-dark"><i class="bx bx-wallet-alt me-1"></i> Cumulative Year-to-Date Balance</td>
                                <?php foreach ($months as $m): ?>
                                    <td class="text-end fw-bold <?= $m['closing_balance'] >= 0 ? 'text-primary' : 'text-danger' ?>">
                                        $<?= number_format($m['closing_balance'], 2) ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="text-end fw-bold bg-light text-primary">$<?= number_format($cashflow['net_annual_cashflow'], 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Year-over-Year Comparison -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-transparent py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-git-compare me-1 text-primary"></i> Year-over-Year (YoY) Performance (<?= $yoy['current_year'] ?> vs <?= $yoy['previous_year'] ?>)</h5>
            </div>
            <div class="card-body">
                <div class="row g-4 text-center">
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light">
                            <span class="text-muted small text-uppercase">Inflows Growth</span>
                            <h3 class="fw-bold mt-1 <?= $yoy['income_growth_pct'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= ($yoy['income_growth_pct'] >= 0 ? '+' : '') . $yoy['income_growth_pct'] ?>%
                            </h3>
                            <div class="small text-muted">
                                $<?= number_format($yoy['current']['total_inflow'], 2) ?> vs $<?= number_format($yoy['previous']['total_inflow'], 2) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light">
                            <span class="text-muted small text-uppercase">Outflows Growth</span>
                            <h3 class="fw-bold mt-1 <?= $yoy['expense_growth_pct'] <= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= ($yoy['expense_growth_pct'] >= 0 ? '+' : '') . $yoy['expense_growth_pct'] ?>%
                            </h3>
                            <div class="small text-muted">
                                $<?= number_format($yoy['current']['total_outflow'], 2) ?> vs $<?= number_format($yoy['previous']['total_outflow'], 2) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light">
                            <span class="text-muted small text-uppercase">Net Balance Improvement</span>
                            <h3 class="fw-bold mt-1 <?= $yoy['net_growth_pct'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= ($yoy['net_growth_pct'] >= 0 ? '+' : '') . $yoy['net_growth_pct'] ?>%
                            </h3>
                            <div class="small text-muted">
                                $<?= number_format($yoy['current']['net_annual_cashflow'], 2) ?> vs $<?= number_format($yoy['previous']['net_annual_cashflow'], 2) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ApexCharts Script -->
<script src="<?= AssetHelper::lib('apexcharts/apexcharts.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var options = {
        series: [{
            name: 'Inflow (Income)',
            type: 'column',
            data: <?= json_encode($inflowsData) ?>
        }, {
            name: 'Outflow (Expenses)',
            type: 'column',
            data: <?= json_encode($outflowsData) ?>
        }, {
            name: 'Net Cashflow',
            type: 'line',
            data: <?= json_encode($netData) ?>
        }],
        chart: {
            height: 350,
            type: 'line',
            stacked: false,
            toolbar: { show: true }
        },
        stroke: {
            width: [0, 0, 3],
            curve: 'smooth'
        },
        plotOptions: {
            bar: {
                columnWidth: '50%',
                borderRadius: 4
            }
        },
        colors: ['#28a745', '#dc3545', '#1e3c72'],
        labels: <?= json_encode($monthsLabels) ?>,
        markers: { size: 5 },
        xaxis: {
            type: 'category'
        },
        yaxis: {
            title: { text: 'Amount ($)' },
            labels: {
                formatter: function(val) {
                    return '$' + val.toLocaleString();
                }
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (y) {
                    if (typeof y !== "undefined") {
                        return "$" + y.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    }
                    return y;
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right'
        }
    };

    var chart = new ApexCharts(document.querySelector("#cashflowApexChart"), options);
    chart.render();
});
</script>
