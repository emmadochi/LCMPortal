<?php
use App\Utilities\AssetHelper;

$totalIncome  = (float)($summary['total_income'] ?? 0);
$totalExpense = (float)($summary['total_expense'] ?? 0);
$netBalance   = (float)($summary['net_balance'] ?? ($totalIncome - $totalExpense));
$txCount      = (int)($summary['record_count'] ?? 0);
$churchCount  = count($church_breakdown ?? []);
?>

<!-- Embedded Custom Styling for Finance Dashboard -->
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

/* Header Banner */
.fin-header-card {
    background: #ffffff;
    border-radius: var(--fin-radius);
    padding: 22px 28px;
    border: 1px solid var(--fin-border);
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    margin-bottom: 24px;
}

/* Metric Cards */
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
.fin-accent-income { background: linear-gradient(90deg, #10b981, #34d399); }
.fin-accent-expense { background: linear-gradient(90deg, #f43f5e, #fb7185); }
.fin-accent-net { background: linear-gradient(90deg, #4f46e5, #818cf8); }
.fin-accent-pledge { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

.fin-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.fin-icon-income { background: #ecfdf5; color: #10b981; }
.fin-icon-expense { background: #fff1f2; color: #f43f5e; }
.fin-icon-net { background: #eef2ff; color: #4f46e5; }
.fin-icon-pledge { background: #fffbeb; color: #f59e0b; }

.fin-label {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--fin-sub);
    margin-bottom: 6px;
}
.fin-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--fin-dark);
    letter-spacing: -0.5px;
    line-height: 1.2;
    margin-bottom: 6px;
}
.fin-subtext {
    font-size: 0.8rem;
    color: var(--fin-sub);
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Status Badges */
.fin-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
}
.fin-badge-success { background: #dcfce7; color: #15803d; }
.fin-badge-danger { background: #fee2e2; color: #b91c1c; }
.fin-badge-info { background: #e0e7ff; color: #4338ca; }
.fin-badge-warning { background: #fef3c7; color: #b45309; }

/* Panels */
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

/* Quick Action Buttons */
.fin-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 16px;
    font-size: 0.84rem;
    font-weight: 600;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}
.fin-btn-primary {
    background: #4f46e5;
    color: #ffffff !important;
}
.fin-btn-primary:hover {
    background: #4338ca;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}
.fin-btn-outline {
    background: #ffffff;
    color: #334155 !important;
    border-color: #cbd5e1;
}
.fin-btn-outline:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a !important;
}

/* Tables */
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

/* Empty States */
.fin-empty {
    text-align: center;
    padding: 40px 20px;
    color: #94a3b8;
}
.fin-empty i {
    font-size: 2.5rem;
    margin-bottom: 10px;
    opacity: 0.4;
}
</style>

<div class="container-fluid p-0 fin-dashboard">
    <!-- ── Top Navigation & Header ── -->
    <div class="fin-header-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary fw-semibold">Global Financial Administration</li>
                    </ol>
                </nav>
                <h3 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-buildings text-primary"></i> Global Financial Administration
                </h3>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= AssetHelper::url('budgets') ?>" class="fin-action-btn fin-btn-outline">
                    <i class="bx bx-pie-chart-alt-2 text-primary"></i> Budgets
                </a>
                <a href="<?= AssetHelper::url('pledges') ?>" class="fin-action-btn fin-btn-outline">
                    <i class="bx bx-gift text-success"></i> Pledges & Campaigns
                </a>
                <a href="<?= AssetHelper::url('finance/cashflow') ?>" class="fin-action-btn fin-btn-outline">
                    <i class="bx bx-line-chart text-info"></i> Cashflow & YoY
                </a>
                <a href="<?= AssetHelper::url('finance/audit-trail') ?>" class="fin-action-btn fin-btn-outline">
                    <i class="bx bx-shield-quarter text-secondary"></i> Audit Trail
                </a>
                <a href="<?= AssetHelper::url('finance/create') ?>" class="fin-action-btn fin-btn-primary">
                    <i class="bx bx-plus"></i> Record Transaction
                </a>
            </div>
        </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    <div class="row g-4 mb-4">
        <!-- 1. Total Income -->
        <div class="col-lg-4 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-income"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Global Total Income</div>
                    <div class="fin-icon-box fin-icon-income">
                        <i class="bx bx-trending-up"></i>
                    </div>
                </div>
                <div class="fin-value text-success">
                    ₦<?= number_format(round($totalIncome)) ?>
                </div>
                <div class="fin-subtext">
                    <span class="fin-badge fin-badge-success">
                        <i class="bx bx-check-circle"></i> Inflow
                    </span>
                    <span><?= $txCount ?> Total Transactions Recorded</span>
                </div>
            </div>
        </div>

        <!-- 2. Total Expenses -->
        <div class="col-lg-4 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-expense"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Global Total Expenses</div>
                    <div class="fin-icon-box fin-icon-expense">
                        <i class="bx bx-trending-down"></i>
                    </div>
                </div>
                <div class="fin-value text-danger">
                    ₦<?= number_format(round($totalExpense)) ?>
                </div>
                <div class="fin-subtext">
                    <span class="fin-badge fin-badge-danger">
                        <i class="bx bx-arrow-from-left"></i> Outflow
                    </span>
                    <span>Across <?= $churchCount ?> Active Branch Churches</span>
                </div>
            </div>
        </div>

        <!-- 3. Net Treasury Balance -->
        <div class="col-lg-4 col-md-12">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-net"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label">Net Treasury Balance</div>
                    <div class="fin-icon-box fin-icon-net">
                        <i class="bx bx-wallet"></i>
                    </div>
                </div>
                <div class="fin-value <?= $netBalance >= 0 ? 'text-primary' : 'text-danger' ?>">
                    ₦<?= number_format(round($netBalance)) ?>
                </div>
                <div class="fin-subtext">
                    <?php if ($netBalance >= 0): ?>
                    <span class="fin-badge fin-badge-success">
                        <i class="bx bx-shield-check"></i> Positive Surplus
                    </span>
                    <?php else: ?>
                    <span class="fin-badge fin-badge-danger">
                        <i class="bx bx-error-circle"></i> Deficit Alert
                    </span>
                    <?php endif; ?>
                    <span>Net Operating Liquid Reserves</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Charts & Visual Analytics ── -->
    <div class="row g-4 mb-4">
        <!-- 6-Month Inflow & Outflow Trend -->
        <div class="col-lg-8">
            <div class="fin-panel h-100 mb-0">
                <div class="fin-panel-header">
                    <h5 class="fin-panel-title">
                        <i class="bx bx-line-chart text-primary fs-5"></i> 6-Month Cash Inflow vs Outflow Trend
                    </h5>
                    <a href="<?= AssetHelper::url('finance/cashflow') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        Full Cashflow & YoY &rarr;
                    </a>
                </div>
                <div class="fin-panel-body">
                    <div id="financeTrendChart" style="min-height: 290px;"></div>
                </div>
            </div>
        </div>

        <!-- Church Branch Distribution -->
        <div class="col-lg-4">
            <div class="fin-panel h-100 mb-0">
                <div class="fin-panel-header">
                    <h5 class="fin-panel-title">
                        <i class="bx bx-pie-chart-alt text-success fs-5"></i> Branch Contribution
                    </h5>
                    <span class="badge bg-light text-muted border"><?= count($church_breakdown ?? []) ?> Branches</span>
                </div>
                <div class="fin-panel-body p-0">
                    <?php if (empty($church_breakdown)): ?>
                        <div class="fin-empty">
                            <i class="bx bx-church"></i>
                            <p class="mb-0">No branch revenue records yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="fin-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Church</th>
                                        <th class="text-end">Income</th>
                                        <th class="text-end">Net</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($church_breakdown as $c): 
                                        $cInc = (float)($c['total_income'] ?? 0);
                                        $cExp = (float)($c['total_expense'] ?? 0);
                                        $cNet = $cInc - $cExp;
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?= AssetHelper::url('churches/' . $c['id'] . '/finance') ?>" class="text-decoration-none fw-semibold text-dark">
                                                <?= htmlspecialchars($c['name']) ?>
                                            </a>
                                            <div class="small text-muted"><?= htmlspecialchars($c['city'] ?? '') ?></div>
                                        </td>
                                        <td class="text-end text-success fw-semibold">
                                            ₦<?= number_format(round($cInc)) ?>
                                        </td>
                                        <td class="text-end fw-bold <?= $cNet >= 0 ? 'text-primary' : 'text-danger' ?>">
                                            ₦<?= number_format(round($cNet)) ?>
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

    <!-- ── Recent Financial Transactions Ledger ── -->
    <div class="fin-panel">
        <div class="fin-panel-header">
            <h5 class="fin-panel-title">
                <i class="bx bx-receipt text-primary fs-5"></i> Recent Financial Transactions
            </h5>
            <div class="d-flex gap-2">
                <a href="<?= AssetHelper::url('finance/all') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    View Master Ledger &rarr;
                </a>
            </div>
        </div>
        <div class="fin-panel-body p-0">
            <?php if (empty($recent_records)): ?>
                <div class="fin-empty">
                    <i class="bx bx-receipt"></i>
                    <p class="mb-0">No financial transactions recorded yet.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="fin-table mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Church & Unit</th>
                                <th>Category</th>
                                <th>Description / Member</th>
                                <th>Payment Method</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Type</th>
                                <th class="text-end">Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_records as $r): 
                                $isInc = ($r['transaction_type'] ?? '') === 'income';
                            ?>
                            <tr>
                                <td class="text-muted small">
                                    <?= date('M d, Y', strtotime($r['transaction_date'] ?? $r['created_at'])) ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($r['church_name'] ?? 'Church-wide') ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($r['unit_name'] ?? 'General') ?></div>
                                </td>
                                <td>
                                    <span class="fin-badge fin-badge-info">
                                        <?= htmlspecialchars($r['category'] ?: 'General') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($r['description'] ?? 'Transaction') ?></div>
                                    <?php if (!empty($r['member_first_name'])): ?>
                                        <div class="small text-muted"><i class="bx bx-user me-1"></i><?= htmlspecialchars($r['member_first_name'] . ' ' . $r['member_last_name']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border">
                                        <i class="bx bx-credit-card me-1"></i><?= htmlspecialchars(ucfirst($r['payment_method'] ?? 'Transfer')) ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold fs-6 <?= $isInc ? 'text-success' : 'text-danger' ?>">
                                    <?= $isInc ? '+' : '-' ?>₦<?= number_format(round($r['amount'])) ?>
                                </td>
                                <td class="text-center">
                                    <span class="fin-badge <?= $isInc ? 'fin-badge-success' : 'fin-badge-danger' ?>">
                                        <?= ucfirst($r['transaction_type'] ?? 'income') ?>
                                    </span>
                                </td>
                                <td class="text-end text-muted small">
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

<!-- ── ApexCharts Script with CDN Fallback ── -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof ApexCharts === 'undefined') return;

    var chartData = <?= json_encode($chart_data ?? ['income' => [], 'expense' => [], 'labels' => []]) ?>;
    var labels = (chartData.labels && chartData.labels.length) ? chartData.labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    var incomeData = (chartData.income && chartData.income.length) ? chartData.income : [0, 0, 0, 0, 0, 0];
    var expenseData = (chartData.expense && chartData.expense.length) ? chartData.expense : [0, 0, 0, 0, 0, 0];

    var options = {
        series: [{
            name: 'Inflow (Income)',
            data: incomeData
        }, {
            name: 'Outflow (Expenses)',
            data: expenseData
        }],
        chart: {
            height: 290,
            type: 'area',
            fontFamily: 'Inter, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2.5 },
        colors: ['#10b981', '#f43f5e'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.35,
                opacityTo: 0.05,
                stops: [0, 95, 100]
            }
        },
        xaxis: {
            categories: labels,
            labels: {
                style: { colors: '#64748b', fontSize: '12px' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { colors: '#64748b', fontSize: '12px' },
                formatter: function (val) {
                    return "₦" + (val / 1000).toFixed(0) + "k";
                }
            }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            labels: { colors: '#334155' }
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function (val) {
                    return "₦" + Number(val).toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            }
        }
    };

    var chartContainer = document.querySelector("#financeTrendChart");
    if (chartContainer) {
        var chart = new ApexCharts(chartContainer, options);
        chart.render();
    }
});
</script>
