<?php
use App\Utilities\AssetHelper;

$months = $cashflow['months'] ?? [];
$monthsLabels = array_map(function($m) { return $m['month_short']; }, $months);
$inflowsData = array_map(function($m) { return round($m['operating_inflows'], 2); }, $months);
$outflowsData = array_map(function($m) { return round($m['operating_outflows'], 2); }, $months);
$netData = array_map(function($m) { return round($m['net_cashflow'], 2); }, $months);
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
.fin-accent-inflow { background: linear-gradient(90deg, #10b981, #34d399); }
.fin-accent-outflow { background: linear-gradient(90deg, #f43f5e, #fb7185); }
.fin-accent-net { background: linear-gradient(90deg, #4f46e5, #818cf8); }
.fin-accent-margin { background: linear-gradient(90deg, #0ea5e9, #38bdf8); }

.fin-icon-box {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
}
.fin-icon-inflow { background: #ecfdf5; color: #10b981; }
.fin-icon-outflow { background: #fff1f2; color: #f43f5e; }
.fin-icon-net { background: #eef2ff; color: #4f46e5; }
.fin-icon-margin { background: #f0f9ff; color: #0284c7; }

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
.fin-panel-body {
    padding: 20px 24px;
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
    padding: 12px 16px;
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
    padding: 12px 16px;
    font-size: 0.86rem;
    color: var(--fin-dark);
    vertical-align: middle;
}
</style>

<div class="container-fluid p-0 fin-dashboard">
    <!-- Header -->
    <div class="fin-header-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('finance') ?>" class="text-decoration-none text-muted">Finances</a></li>
                        <li class="breadcrumb-item active text-info fw-semibold">Cashflow & Trends</li>
                    </ol>
                </nav>
                <h3 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-line-chart text-info"></i> Cashflow Statement & Year-over-Year Analytics
                </h3>
            </div>
            
            <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                <?php if ($this->session->hasPermission('manage_users') && !empty($churches)): ?>
                    <select name="church_id" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                        <option value="">All Churches (Global)</option>
                        <?php foreach ($churches as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($churchId == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

                <select name="year" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                    <?php for ($y = date('Y'); $y >= date('Y') - 4; $y--): ?>
                        <option value="<?= $y ?>" <?= ($selectedYear == $y) ? 'selected' : '' ?>><?= $y ?> Fiscal Year</option>
                    <?php endfor; ?>
                </select>
                
                <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bx bx-printer me-1"></i> Print Statement
                </button>
            </form>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <!-- Annual Total Inflows -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-inflow"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Annual Total Inflows</div>
                    <div class="fin-icon-box fin-icon-inflow">
                        <i class="bx bx-trending-up"></i>
                    </div>
                </div>
                <div class="fin-value text-success">
                    ₦<?= number_format(round($cashflow['total_inflow'])) ?>
                </div>
                <div class="fin-subtext">
                    YoY Growth: <strong class="<?= $yoy['income_growth_pct'] >= 0 ? 'text-success' : 'text-danger' ?>"><?= ($yoy['income_growth_pct'] >= 0 ? '+' : '') . $yoy['income_growth_pct'] ?>%</strong> vs <?= $yoy['previous_year'] ?>
                </div>
            </div>
        </div>

        <!-- Annual Total Outflows -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-outflow"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Annual Total Outflows</div>
                    <div class="fin-icon-box fin-icon-outflow">
                        <i class="bx bx-trending-down"></i>
                    </div>
                </div>
                <div class="fin-value text-danger">
                    ₦<?= number_format(round($cashflow['total_outflow'])) ?>
                </div>
                <div class="fin-subtext">
                    Expense Change: <strong class="<?= $yoy['expense_growth_pct'] <= 0 ? 'text-success' : 'text-danger' ?>"><?= ($yoy['expense_growth_pct'] >= 0 ? '+' : '') . $yoy['expense_growth_pct'] ?>%</strong> vs <?= $yoy['previous_year'] ?>
                </div>
            </div>
        </div>

        <!-- Net Annual Cashflow -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-net"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Net Annual Cashflow</div>
                    <div class="fin-icon-box fin-icon-net">
                        <i class="bx bx-wallet"></i>
                    </div>
                </div>
                <div class="fin-value <?= $cashflow['net_annual_cashflow'] >= 0 ? 'text-primary' : 'text-danger' ?>">
                    ₦<?= number_format(round($cashflow['net_annual_cashflow'])) ?>
                </div>
                <div class="fin-subtext">
                    Net Growth: <strong class="<?= $yoy['net_growth_pct'] >= 0 ? 'text-success' : 'text-danger' ?>"><?= ($yoy['net_growth_pct'] >= 0 ? '+' : '') . $yoy['net_growth_pct'] ?>%</strong>
                </div>
            </div>
        </div>

        <!-- Operating Margin -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-margin"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Operating Inflow Margin</div>
                    <div class="fin-icon-box fin-icon-margin">
                        <i class="bx bx-pie-chart-alt"></i>
                    </div>
                </div>
                <?php 
                    $margin = $cashflow['total_inflow'] > 0 
                        ? round(($cashflow['net_annual_cashflow'] / $cashflow['total_inflow']) * 100, 1) 
                        : 0; 
                ?>
                <div class="fin-value text-dark">
                    <?= $margin ?>%
                </div>
                <div class="fin-subtext">
                    Retained Cash Percentage
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Cash Inflow vs Outflow Chart -->
    <div class="fin-panel mb-4">
        <div class="fin-panel-header">
            <h5 class="fin-panel-title">
                <i class="bx bx-bar-chart-alt-2 text-primary fs-5"></i> Monthly Cash Inflow vs Outflow (FY <?= $selectedYear ?>)
            </h5>
            <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">Interactive Analytics</span>
        </div>
        <div class="fin-panel-body">
            <div id="cashflowApexChart" style="min-height: 350px;"></div>
        </div>
    </div>

    <!-- 12-Month Structured Cashflow Statement -->
    <div class="fin-panel mb-4">
        <div class="fin-panel-header">
            <h5 class="fin-panel-title">
                <i class="bx bx-table text-primary fs-5"></i> 12-Month Structured Cashflow Statement (FY <?= $selectedYear ?>)
            </h5>
        </div>
        <div class="fin-panel-body p-0">
            <div class="table-responsive">
                <table class="fin-table table-bordered mb-0 text-nowrap">
                    <thead>
                        <tr class="text-center">
                            <th class="text-start ps-3">Line Item</th>
                            <?php foreach ($months as $m): ?>
                                <th><?= $m['month_short'] ?></th>
                            <?php endforeach; ?>
                            <th class="bg-light fw-bold">Full Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Inflows Row -->
                        <tr style="background: #f0fdf4;">
                            <td class="ps-3 fw-bold text-success"><i class="bx bx-plus-circle me-1"></i> Operating Inflows (Income)</td>
                            <?php foreach ($months as $m): ?>
                                <td class="text-end fw-semibold text-success">₦<?= number_format(round($m['operating_inflows'])) ?></td>
                            <?php endforeach; ?>
                            <td class="text-end fw-bold bg-light text-success">₦<?= number_format(round($cashflow['total_inflow'])) ?></td>
                        </tr>

                        <!-- Outflows Row -->
                        <tr style="background: #fff1f2;">
                            <td class="ps-3 fw-bold text-danger"><i class="bx bx-minus-circle me-1"></i> Operating Outflows (Expenses)</td>
                            <?php foreach ($months as $m): ?>
                                <td class="text-end fw-semibold text-danger">₦<?= number_format(round($m['operating_outflows'])) ?></td>
                            <?php endforeach; ?>
                            <td class="text-end fw-bold bg-light text-danger">₦<?= number_format(round($cashflow['total_outflow'])) ?></td>
                        </tr>

                        <!-- Net Monthly Cashflow -->
                        <tr class="fw-bold">
                            <td class="ps-3 text-primary"><i class="bx bx-wallet me-1"></i> Net Monthly Cashflow</td>
                            <?php foreach ($months as $m): ?>
                                <td class="text-end <?= $m['net_cashflow'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    ₦<?= number_format(round($m['net_cashflow'])) ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="text-end bg-light text-primary fs-6">₦<?= number_format(round($cashflow['net_annual_cashflow'])) ?></td>
                        </tr>

                        <!-- Cumulative Cash Balance -->
                        <tr class="bg-light">
                            <td class="ps-3 fw-bold text-dark"><i class="bx bx-wallet-alt me-1"></i> Cumulative Year-to-Date Balance</td>
                            <?php foreach ($months as $m): ?>
                                <td class="text-end fw-bold <?= $m['closing_balance'] >= 0 ? 'text-primary' : 'text-danger' ?>">
                                    ₦<?= number_format(round($m['closing_balance'])) ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="text-end fw-bold bg-light text-primary">₦<?= number_format(round($cashflow['net_annual_cashflow'])) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Year-over-Year Comparison -->
    <div class="fin-panel">
        <div class="fin-panel-header">
            <h5 class="fin-panel-title">
                <i class="bx bx-git-compare text-primary fs-5"></i> Year-over-Year (YoY) Performance (<?= $yoy['current_year'] ?> vs <?= $yoy['previous_year'] ?>)
            </h5>
        </div>
        <div class="fin-panel-body">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 bg-light">
                        <span class="text-muted small text-uppercase fw-bold">Inflows Growth</span>
                        <h3 class="fw-bold mt-1 <?= $yoy['income_growth_pct'] >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= ($yoy['income_growth_pct'] >= 0 ? '+' : '') . $yoy['income_growth_pct'] ?>%
                        </h3>
                        <div class="small text-muted">
                            ₦<?= number_format(round($yoy['current']['total_inflow'])) ?> vs ₦<?= number_format(round($yoy['previous']['total_inflow'])) ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 bg-light">
                        <span class="text-muted small text-uppercase fw-bold">Outflows Growth</span>
                        <h3 class="fw-bold mt-1 <?= $yoy['expense_growth_pct'] <= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= ($yoy['expense_growth_pct'] >= 0 ? '+' : '') . $yoy['expense_growth_pct'] ?>%
                        </h3>
                        <div class="small text-muted">
                            ₦<?= number_format(round($yoy['current']['total_outflow'])) ?> vs ₦<?= number_format(round($yoy['previous']['total_outflow'])) ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 bg-light">
                        <span class="text-muted small text-uppercase fw-bold">Net Balance Improvement</span>
                        <h3 class="fw-bold mt-1 <?= $yoy['net_growth_pct'] >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= ($yoy['net_growth_pct'] >= 0 ? '+' : '') . $yoy['net_growth_pct'] ?>%
                        </h3>
                        <div class="small text-muted">
                            ₦<?= number_format(round($yoy['current']['net_annual_cashflow'])) ?> vs ₦<?= number_format(round($yoy['previous']['net_annual_cashflow'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof ApexCharts === 'undefined') return;

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
            fontFamily: 'Inter, sans-serif',
            stacked: false,
            toolbar: { show: false }
        },
        stroke: {
            width: [0, 0, 3],
            curve: 'smooth'
        },
        plotOptions: {
            bar: {
                columnWidth: '45%',
                borderRadius: 5
            }
        },
        colors: ['#10b981', '#f43f5e', '#4f46e5'],
        labels: <?= json_encode(!empty($monthsLabels) ? $monthsLabels : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']) ?>,
        markers: { size: 4 },
        xaxis: {
            type: 'category',
            labels: { style: { colors: '#64748b' } }
        },
        yaxis: {
            labels: {
                style: { colors: '#64748b' },
                formatter: function(val) {
                    return '₦' + (val / 1000).toFixed(0) + 'k';
                }
            }
        },
        tooltip: {
            theme: 'dark',
            shared: true,
            intersect: false,
            y: {
                formatter: function (y) {
                    if (typeof y !== "undefined") {
                        return "₦" + Number(y).toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2});
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

    var chartContainer = document.querySelector("#cashflowApexChart");
    if (chartContainer) {
        var chart = new ApexCharts(chartContainer, options);
        chart.render();
    }
});
</script>
