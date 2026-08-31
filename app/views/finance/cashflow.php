<?php
use App\Utilities\AssetHelper;

$months = $cashflow['months'] ?? [];
$monthsLabels = array_map(function($m) { return $m['month_short']; }, $months);
$inflowsData = array_map(function($m) { return round($m['operating_inflows'], 2); }, $months);
$outflowsData = array_map(function($m) { return round($m['operating_outflows'], 2); }, $months);
$netData = array_map(function($m) { return round($m['net_cashflow'], 2); }, $months);
$selectedMonth = $selectedMonth ?? 0;
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
    padding: 20px 24px;
    border: 1px solid var(--fin-border);
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    margin-bottom: 24px;
}

.fin-filter-toolbar {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 10px 14px;
}

.fin-select-group .input-group-text {
    border-color: #e2e8f0;
    font-size: 0.85rem;
}
.fin-select-group .form-select {
    border-color: #e2e8f0;
    height: 36px;
    font-size: 0.82rem;
    color: #1e293b;
}
.fin-select-group .form-select:focus {
    border-color: #4f46e5;
    box-shadow: none;
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
.col-highlight {
    background: rgba(79, 70, 229, 0.07) !important;
    font-weight: 700 !important;
    border-left: 2px solid #4f46e5 !important;
    border-right: 2px solid #4f46e5 !important;
}
</style>

<div class="container-fluid p-0 fin-dashboard">
    <!-- Header Card -->
    <div class="fin-header-card">
        <!-- Top Row: Breadcrumbs, Title & Quick Actions -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('finance') ?>" class="text-decoration-none text-muted">Finances</a></li>
                        <li class="breadcrumb-item active text-primary fw-semibold">Cashflow & Trends</li>
                    </ol>
                </nav>
                <h3 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-line-chart text-primary"></i> Cashflow Statement & Analytics
                </h3>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary rounded-pill px-3 font-size-12 fw-semibold">
                    <i class="bx bx-printer me-1"></i> Print Statement
                </button>
            </div>
        </div>

        <!-- Bottom Row: Sleek Unified Inline Filter Toolbar -->
        <div class="fin-filter-toolbar d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center flex-wrap gap-2 flex-grow-1">
                <!-- Active Branch / Consolidated Scope Badge -->
                <div id="churchContextBadge" class="d-inline-flex align-items-center gap-1.5 px-3 py-1.5 rounded-pill <?= !empty($currentChurch) ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-info-subtle text-info border border-info-subtle' ?> font-size-12 fw-semibold">
                    <i class="bx <?= !empty($currentChurch) ? 'bx-church' : 'bx-globe' ?> font-size-14"></i>
                    <span id="churchBadgeText"><?= !empty($currentChurch) ? 'Branch: ' . htmlspecialchars($currentChurch['name']) : 'Consolidated (All Branches)' ?></span>
                </div>

                <!-- Church Selector Dropdown -->
                <?php if ($this->session->hasPermission('manage_users') && !empty($churches)): ?>
                    <div class="input-group input-group-sm fin-select-group" style="width: auto; min-width: 175px;">
                        <span class="input-group-text bg-white border-end-0 text-muted rounded-start-pill ps-2.5 pe-1"><i class="bx bx-church"></i></span>
                        <select id="filterChurch" name="church_id" class="form-select form-select-sm border-start-0 rounded-end-pill font-size-12 fw-medium bg-white shadow-none">
                            <option value="" <?= empty($churchId) ? 'selected' : '' ?>>All Churches (Global)</option>
                            <?php foreach ($churches as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ((string)$churchId === (string)$c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Fiscal Year Selector Dropdown -->
                <div class="input-group input-group-sm fin-select-group" style="width: auto; min-width: 135px;">
                    <span class="input-group-text bg-white border-end-0 text-muted rounded-start-pill ps-2.5 pe-1"><i class="bx bx-calendar"></i></span>
                    <select id="filterYear" name="year" class="form-select form-select-sm border-start-0 rounded-end-pill font-size-12 fw-medium bg-white shadow-none">
                        <?php for ($y = date('Y'); $y >= date('Y') - 4; $y--): ?>
                            <option value="<?= $y ?>" <?= ($selectedYear == $y) ? 'selected' : '' ?>><?= $y ?> Fiscal Year</option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Month Selector Dropdown -->
                <div class="input-group input-group-sm fin-select-group" style="width: auto; min-width: 155px;">
                    <span class="input-group-text bg-white border-end-0 text-muted rounded-start-pill ps-2.5 pe-1"><i class="bx bx-calendar-event"></i></span>
                    <select id="filterMonth" name="month" class="form-select form-select-sm border-start-0 rounded-end-pill font-size-12 fw-medium bg-white shadow-none">
                        <option value="0" <?= empty($selectedMonth) ? 'selected' : '' ?>>All Months (Full Year)</option>
                        <?php
                        $monthNames = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
                        foreach ($monthNames as $mNum => $mName):
                        ?>
                            <option value="<?= $mNum ?>" <?= ((int)$selectedMonth === $mNum) ? 'selected' : '' ?>><?= $mName ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Loading Spinner -->
                <div id="filterLoadingSpinner" class="spinner-border spinner-border-sm text-primary ms-1" style="display: none;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <!-- Active Period Badge -->
            <div class="d-flex align-items-center">
                <span class="badge bg-white text-dark border px-3 py-1.5 rounded-pill font-size-12 shadow-sm" id="periodContextBadge">
                    <i class="bx bx-time-five text-primary me-1"></i> <span id="periodBadgeText"><?= htmlspecialchars($kpi['period_label'] ?? 'Full Year ' . $selectedYear) ?></span>
                </span>
            </div>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Inflows -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-inflow"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label" id="kpiInflowLabel"><?= !empty($selectedMonth) ? 'Monthly Total Inflows' : 'Annual Total Inflows' ?></div>
                    <div class="fin-icon-box fin-icon-inflow">
                        <i class="bx bx-trending-up"></i>
                    </div>
                </div>
                <div class="fin-value text-success" id="kpiInflowValue">
                    ₦<?= number_format(round($kpi['inflow'] ?? $cashflow['total_inflow'])) ?>
                </div>
                <div class="fin-subtext" id="kpiInflowSubtext">
                    YoY Growth: <strong class="<?= ($kpi['income_growth_pct'] ?? $yoy['income_growth_pct']) >= 0 ? 'text-success' : 'text-danger' ?>" id="kpiInflowGrowth"><?= (($kpi['income_growth_pct'] ?? $yoy['income_growth_pct']) >= 0 ? '+' : '') . ($kpi['income_growth_pct'] ?? $yoy['income_growth_pct']) ?>%</strong> vs <span id="kpiInflowPrevPeriod"><?= $kpi['comparison_label'] ?? $yoy['previous_year'] ?></span>
                </div>
            </div>
        </div>

        <!-- Total Outflows -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-outflow"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label" id="kpiOutflowLabel"><?= !empty($selectedMonth) ? 'Monthly Total Outflows' : 'Annual Total Outflows' ?></div>
                    <div class="fin-icon-box fin-icon-outflow">
                        <i class="bx bx-trending-down"></i>
                    </div>
                </div>
                <div class="fin-value text-danger" id="kpiOutflowValue">
                    ₦<?= number_format(round($kpi['outflow'] ?? $cashflow['total_outflow'])) ?>
                </div>
                <div class="fin-subtext" id="kpiOutflowSubtext">
                    Expense Change: <strong class="<?= ($kpi['expense_growth_pct'] ?? $yoy['expense_growth_pct']) <= 0 ? 'text-success' : 'text-danger' ?>" id="kpiOutflowGrowth"><?= (($kpi['expense_growth_pct'] ?? $yoy['expense_growth_pct']) >= 0 ? '+' : '') . ($kpi['expense_growth_pct'] ?? $yoy['expense_growth_pct']) ?>%</strong> vs <span id="kpiOutflowPrevPeriod"><?= $kpi['comparison_label'] ?? $yoy['previous_year'] ?></span>
                </div>
            </div>
        </div>

        <!-- Net Cashflow -->
        <div class="col-lg-3 col-md-6">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-net"></div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fin-label" id="kpiNetLabel"><?= !empty($selectedMonth) ? 'Net Monthly Cashflow' : 'Net Annual Cashflow' ?></div>
                    <div class="fin-icon-box fin-icon-net">
                        <i class="bx bx-wallet"></i>
                    </div>
                </div>
                <div class="fin-value <?= ($kpi['net'] ?? $cashflow['net_annual_cashflow']) >= 0 ? 'text-primary' : 'text-danger' ?>" id="kpiNetValue">
                    ₦<?= number_format(round($kpi['net'] ?? $cashflow['net_annual_cashflow'])) ?>
                </div>
                <div class="fin-subtext" id="kpiNetSubtext">
                    Net Growth: <strong class="<?= ($kpi['net_growth_pct'] ?? $yoy['net_growth_pct']) >= 0 ? 'text-success' : 'text-danger' ?>" id="kpiNetGrowth"><?= (($kpi['net_growth_pct'] ?? $yoy['net_growth_pct']) >= 0 ? '+' : '') . ($kpi['net_growth_pct'] ?? $yoy['net_growth_pct']) ?>%</strong>
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
                <div class="fin-value text-dark" id="kpiMarginValue">
                    <?= $kpi['margin'] ?? 0 ?>%
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
                <i class="bx bx-bar-chart-alt-2 text-primary fs-5"></i> Monthly Cash Inflow vs Outflow (<span id="chartYearText">FY <?= $selectedYear ?></span>)
            </h5>
            <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">Interactive Live Analytics</span>
        </div>
        <div class="fin-panel-body">
            <div id="cashflowApexChart" style="min-height: 350px;"></div>
        </div>
    </div>

    <!-- 12-Month Structured Cashflow Statement -->
    <div class="fin-panel mb-4">
        <div class="fin-panel-header">
            <h5 class="fin-panel-title">
                <i class="bx bx-table text-primary fs-5"></i> 12-Month Structured Cashflow Statement (<span id="tableYearText">FY <?= $selectedYear ?></span>)
            </h5>
        </div>
        <div class="fin-panel-body p-0">
            <div class="table-responsive">
                <table class="fin-table table-bordered mb-0 text-nowrap" id="cashflowStatementTable">
                    <thead>
                        <tr class="text-center">
                            <th class="text-start ps-3">Line Item</th>
                            <?php foreach ($months as $idx => $m): ?>
                                <th class="month-col month-col-<?= $idx + 1 ?> <?= ($selectedMonth == ($idx + 1)) ? 'col-highlight' : '' ?>"><?= $m['month_short'] ?></th>
                            <?php endforeach; ?>
                            <th class="bg-light fw-bold">Full Year</th>
                        </tr>
                    </thead>
                    <tbody id="cashflowTableBody">
                        <!-- Inflows Row -->
                        <tr style="background: #f0fdf4;">
                            <td class="ps-3 fw-bold text-success"><i class="bx bx-plus-circle me-1"></i> Operating Inflows (Income)</td>
                            <?php foreach ($months as $idx => $m): ?>
                                <td class="text-end fw-semibold text-success month-col month-col-<?= $idx + 1 ?> <?= ($selectedMonth == ($idx + 1)) ? 'col-highlight' : '' ?>" id="inflow-cell-<?= $idx + 1 ?>">₦<?= number_format(round($m['operating_inflows'])) ?></td>
                            <?php endforeach; ?>
                            <td class="text-end fw-bold bg-light text-success" id="inflow-total-cell">₦<?= number_format(round($cashflow['total_inflow'])) ?></td>
                        </tr>

                        <!-- Outflows Row -->
                        <tr style="background: #fff1f2;">
                            <td class="ps-3 fw-bold text-danger"><i class="bx bx-minus-circle me-1"></i> Operating Outflows (Expenses)</td>
                            <?php foreach ($months as $idx => $m): ?>
                                <td class="text-end fw-semibold text-danger month-col month-col-<?= $idx + 1 ?> <?= ($selectedMonth == ($idx + 1)) ? 'col-highlight' : '' ?>" id="outflow-cell-<?= $idx + 1 ?>">₦<?= number_format(round($m['operating_outflows'])) ?></td>
                            <?php endforeach; ?>
                            <td class="text-end fw-bold bg-light text-danger" id="outflow-total-cell">₦<?= number_format(round($cashflow['total_outflow'])) ?></td>
                        </tr>

                        <!-- Net Monthly Cashflow -->
                        <tr class="fw-bold">
                            <td class="ps-3 text-primary"><i class="bx bx-wallet me-1"></i> Net Monthly Cashflow</td>
                            <?php foreach ($months as $idx => $m): ?>
                                <td class="text-end <?= $m['net_cashflow'] >= 0 ? 'text-success' : 'text-danger' ?> month-col month-col-<?= $idx + 1 ?> <?= ($selectedMonth == ($idx + 1)) ? 'col-highlight' : '' ?>" id="net-cell-<?= $idx + 1 ?>">
                                    ₦<?= number_format(round($m['net_cashflow'])) ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="text-end bg-light text-primary fs-6" id="net-total-cell">₦<?= number_format(round($cashflow['net_annual_cashflow'])) ?></td>
                        </tr>

                        <!-- Cumulative Cash Balance -->
                        <tr class="bg-light">
                            <td class="ps-3 fw-bold text-dark"><i class="bx bx-wallet-alt me-1"></i> Cumulative Year-to-Date Balance</td>
                            <?php foreach ($months as $idx => $m): ?>
                                <td class="text-end fw-bold <?= $m['closing_balance'] >= 0 ? 'text-primary' : 'text-danger' ?> month-col month-col-<?= $idx + 1 ?> <?= ($selectedMonth == ($idx + 1)) ? 'col-highlight' : '' ?>" id="closing-cell-<?= $idx + 1 ?>">
                                    ₦<?= number_format(round($m['closing_balance'])) ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="text-end fw-bold bg-light text-primary" id="closing-total-cell">₦<?= number_format(round($cashflow['net_annual_cashflow'])) ?></td>
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
                <i class="bx bx-git-compare text-primary fs-5"></i> Year-over-Year (YoY) Performance (<span id="yoyTitleCurrent"><?= $yoy['current_year'] ?></span> vs <span id="yoyTitlePrevious"><?= $yoy['previous_year'] ?></span>)
            </h5>
        </div>
        <div class="fin-panel-body">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 bg-light">
                        <span class="text-muted small text-uppercase fw-bold">Inflows Growth</span>
                        <h3 class="fw-bold mt-1 <?= $yoy['income_growth_pct'] >= 0 ? 'text-success' : 'text-danger' ?>" id="yoyInflowGrowth">
                            <?= ($yoy['income_growth_pct'] >= 0 ? '+' : '') . $yoy['income_growth_pct'] ?>%
                        </h3>
                        <div class="small text-muted" id="yoyInflowAmounts">
                            ₦<?= number_format(round($yoy['current']['total_inflow'])) ?> vs ₦<?= number_format(round($yoy['previous']['total_inflow'])) ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 bg-light">
                        <span class="text-muted small text-uppercase fw-bold">Outflows Growth</span>
                        <h3 class="fw-bold mt-1 <?= $yoy['expense_growth_pct'] <= 0 ? 'text-success' : 'text-danger' ?>" id="yoyOutflowGrowth">
                            <?= ($yoy['expense_growth_pct'] >= 0 ? '+' : '') . $yoy['expense_growth_pct'] ?>%
                        </h3>
                        <div class="small text-muted" id="yoyOutflowAmounts">
                            ₦<?= number_format(round($yoy['current']['total_outflow'])) ?> vs ₦<?= number_format(round($yoy['previous']['total_outflow'])) ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 bg-light">
                        <span class="text-muted small text-uppercase fw-bold">Net Balance Improvement</span>
                        <h3 class="fw-bold mt-1 <?= $yoy['net_growth_pct'] >= 0 ? 'text-success' : 'text-danger' ?>" id="yoyNetGrowth">
                            <?= ($yoy['net_growth_pct'] >= 0 ? '+' : '') . $yoy['net_growth_pct'] ?>%
                        </h3>
                        <div class="small text-muted" id="yoyNetAmounts">
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
var cashflowChartInstance = null;

document.addEventListener('DOMContentLoaded', function() {
    initCashflowChart();
    setupAjaxFilters();
});

function initCashflowChart() {
    if (typeof ApexCharts === 'undefined') return;

    var chartContainer = document.querySelector("#cashflowApexChart");
    if (!chartContainer) return;

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

    cashflowChartInstance = new ApexCharts(chartContainer, options);
    cashflowChartInstance.render();
}

function setupAjaxFilters() {
    var churchSelect = document.getElementById('filterChurch');
    var yearSelect = document.getElementById('filterYear');
    var monthSelect = document.getElementById('filterMonth');

    if (churchSelect) churchSelect.addEventListener('change', performCashflowAjax);
    if (yearSelect) yearSelect.addEventListener('change', performCashflowAjax);
    if (monthSelect) monthSelect.addEventListener('change', performCashflowAjax);
}

function performCashflowAjax() {
    var churchSelect = document.getElementById('filterChurch');
    var yearSelect = document.getElementById('filterYear');
    var monthSelect = document.getElementById('filterMonth');
    var spinner = document.getElementById('filterLoadingSpinner');

    var churchId = churchSelect ? churchSelect.value : '<?= $churchId ?? '' ?>';
    var year = yearSelect ? yearSelect.value : '<?= $selectedYear ?>';
    var month = monthSelect ? monthSelect.value : '0';

    if (spinner) spinner.style.display = 'inline-block';

    var params = new URLSearchParams();
    if (churchId) params.append('church_id', churchId);
    if (year) params.append('year', year);
    if (month && month !== '0') params.append('month', month);

    var requestUrl = window.location.pathname + '?' + params.toString();

    // Update browser URL without reloading
    history.pushState(null, '', requestUrl);

    fetch(requestUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            updateCashflowUI(data);
        }
    })
    .catch(function(err) {
        console.error('Error fetching cashflow data:', err);
    })
    .finally(function() {
        if (spinner) spinner.style.display = 'none';
    });
}

function updateCashflowUI(data) {
    var kpi = data.kpi || {};
    var cashflow = data.cashflow || {};
    var yoy = data.yoy || {};
    var months = cashflow.months || [];
    var selectedMonth = parseInt(data.selectedMonth || 0);

    // 1. Update Active Branch / Global Context Badge
    var badgeText = document.getElementById('churchBadgeText');
    var badgeContainer = document.getElementById('churchContextBadge');
    if (badgeText && badgeContainer) {
        if (data.churchId && data.churchName && data.churchName !== 'All Churches (Global)') {
            badgeText.textContent = 'Active Branch: ' + data.churchName;
            badgeContainer.className = 'badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill font-size-12 fw-semibold';
            badgeContainer.querySelector('i').className = 'bx bx-church me-1 align-middle font-size-14';
        } else {
            badgeText.textContent = 'Consolidated: All Churches (Global)';
            badgeContainer.className = 'badge bg-info-subtle text-info border border-info-subtle px-3 py-1.5 rounded-pill font-size-12 fw-semibold';
            badgeContainer.querySelector('i').className = 'bx bx-globe me-1 align-middle font-size-14';
        }
    }

    // 2. Update Period Badge
    var periodBadge = document.getElementById('periodBadgeText');
    if (periodBadge) {
        periodBadge.textContent = kpi.period_label || ('Full Year ' + data.selectedYear);
    }

    // 3. Update KPI Cards
    var isMonth = selectedMonth > 0;
    document.getElementById('kpiInflowLabel').textContent = isMonth ? 'Monthly Total Inflows' : 'Annual Total Inflows';
    document.getElementById('kpiOutflowLabel').textContent = isMonth ? 'Monthly Total Outflows' : 'Annual Total Outflows';
    document.getElementById('kpiNetLabel').textContent = isMonth ? 'Net Monthly Cashflow' : 'Net Annual Cashflow';

    document.getElementById('kpiInflowValue').textContent = '₦' + Math.round(kpi.inflow || 0).toLocaleString();
    document.getElementById('kpiOutflowValue').textContent = '₦' + Math.round(kpi.outflow || 0).toLocaleString();

    var netEl = document.getElementById('kpiNetValue');
    netEl.textContent = '₦' + Math.round(kpi.net || 0).toLocaleString();
    netEl.className = 'fin-value ' + ((kpi.net || 0) >= 0 ? 'text-primary' : 'text-danger');

    document.getElementById('kpiMarginValue').textContent = (kpi.margin || 0) + '%';

    // Update KPI Growth Subtexts
    var inGrowthEl = document.getElementById('kpiInflowGrowth');
    inGrowthEl.textContent = ((kpi.income_growth_pct >= 0 ? '+' : '') + kpi.income_growth_pct) + '%';
    inGrowthEl.className = kpi.income_growth_pct >= 0 ? 'text-success' : 'text-danger';
    document.getElementById('kpiInflowPrevPeriod').textContent = kpi.comparison_label || (data.selectedYear - 1);

    var outGrowthEl = document.getElementById('kpiOutflowGrowth');
    outGrowthEl.textContent = ((kpi.expense_growth_pct >= 0 ? '+' : '') + kpi.expense_growth_pct) + '%';
    outGrowthEl.className = kpi.expense_growth_pct <= 0 ? 'text-success' : 'text-danger';
    document.getElementById('kpiOutflowPrevPeriod').textContent = kpi.comparison_label || (data.selectedYear - 1);

    var netGrowthEl = document.getElementById('kpiNetGrowth');
    netGrowthEl.textContent = ((kpi.net_growth_pct >= 0 ? '+' : '') + kpi.net_growth_pct) + '%';
    netGrowthEl.className = kpi.net_growth_pct >= 0 ? 'text-success' : 'text-danger';

    // 4. Update Year titles
    document.getElementById('chartYearText').textContent = 'FY ' + data.selectedYear;
    document.getElementById('tableYearText').textContent = 'FY ' + data.selectedYear;

    // 5. Update Table Values & Column Highlight
    document.querySelectorAll('.month-col').forEach(function(el) {
        el.classList.remove('col-highlight');
    });

    if (selectedMonth > 0) {
        document.querySelectorAll('.month-col-' + selectedMonth).forEach(function(el) {
            el.classList.add('col-highlight');
        });
    }

    months.forEach(function(m, idx) {
        var mNum = idx + 1;
        var inCell = document.getElementById('inflow-cell-' + mNum);
        if (inCell) inCell.textContent = '₦' + Math.round(m.operating_inflows).toLocaleString();

        var outCell = document.getElementById('outflow-cell-' + mNum);
        if (outCell) outCell.textContent = '₦' + Math.round(m.operating_outflows).toLocaleString();

        var netCell = document.getElementById('net-cell-' + mNum);
        if (netCell) {
            netCell.textContent = '₦' + Math.round(m.net_cashflow).toLocaleString();
            netCell.className = 'text-end ' + (m.net_cashflow >= 0 ? 'text-success' : 'text-danger') + ' month-col month-col-' + mNum + (selectedMonth === mNum ? ' col-highlight' : '');
        }

        var closingCell = document.getElementById('closing-cell-' + mNum);
        if (closingCell) {
            closingCell.textContent = '₦' + Math.round(m.closing_balance).toLocaleString();
            closingCell.className = 'text-end fw-bold ' + (m.closing_balance >= 0 ? 'text-primary' : 'text-danger') + ' month-col month-col-' + mNum + (selectedMonth === mNum ? ' col-highlight' : '');
        }
    });

    document.getElementById('inflow-total-cell').textContent = '₦' + Math.round(cashflow.total_inflow || 0).toLocaleString();
    document.getElementById('outflow-total-cell').textContent = '₦' + Math.round(cashflow.total_outflow || 0).toLocaleString();
    document.getElementById('net-total-cell').textContent = '₦' + Math.round(cashflow.net_annual_cashflow || 0).toLocaleString();
    document.getElementById('closing-total-cell').textContent = '₦' + Math.round(cashflow.net_annual_cashflow || 0).toLocaleString();

    // 6. Update YoY Section
    document.getElementById('yoyTitleCurrent').textContent = yoy.current_year || data.selectedYear;
    document.getElementById('yoyTitlePrevious').textContent = yoy.previous_year || (data.selectedYear - 1);

    var yoyInEl = document.getElementById('yoyInflowGrowth');
    yoyInEl.textContent = ((yoy.income_growth_pct >= 0 ? '+' : '') + yoy.income_growth_pct) + '%';
    yoyInEl.className = 'fw-bold mt-1 ' + (yoy.income_growth_pct >= 0 ? 'text-success' : 'text-danger');
    document.getElementById('yoyInflowAmounts').textContent = '₦' + Math.round(yoy.current ? yoy.current.total_inflow : 0).toLocaleString() + ' vs ₦' + Math.round(yoy.previous ? yoy.previous.total_inflow : 0).toLocaleString();

    var yoyOutEl = document.getElementById('yoyOutflowGrowth');
    yoyOutEl.textContent = ((yoy.expense_growth_pct >= 0 ? '+' : '') + yoy.expense_growth_pct) + '%';
    yoyOutEl.className = 'fw-bold mt-1 ' + (yoy.expense_growth_pct <= 0 ? 'text-success' : 'text-danger');
    document.getElementById('yoyOutflowAmounts').textContent = '₦' + Math.round(yoy.current ? yoy.current.total_outflow : 0).toLocaleString() + ' vs ₦' + Math.round(yoy.previous ? yoy.previous.total_outflow : 0).toLocaleString();

    var yoyNetEl = document.getElementById('yoyNetGrowth');
    yoyNetEl.textContent = ((yoy.net_growth_pct >= 0 ? '+' : '') + yoy.net_growth_pct) + '%';
    yoyNetEl.className = 'fw-bold mt-1 ' + (yoy.net_growth_pct >= 0 ? 'text-success' : 'text-danger');
    document.getElementById('yoyNetAmounts').textContent = '₦' + Math.round(yoy.current ? yoy.current.net_annual_cashflow : 0).toLocaleString() + ' vs ₦' + Math.round(yoy.previous ? yoy.previous.net_annual_cashflow : 0).toLocaleString();

    // 7. Update ApexCharts Series & Labels dynamically
    if (cashflowChartInstance) {
        var newInflows = months.map(function(m) { return Math.round(m.operating_inflows); });
        var newOutflows = months.map(function(m) { return Math.round(m.operating_outflows); });
        var newNet = months.map(function(m) { return Math.round(m.net_cashflow); });
        var newLabels = months.map(function(m) { return m.month_short; });

        cashflowChartInstance.updateOptions({
            labels: newLabels
        });

        cashflowChartInstance.updateSeries([{
            name: 'Inflow (Income)',
            type: 'column',
            data: newInflows
        }, {
            name: 'Outflow (Expenses)',
            type: 'column',
            data: newOutflows
        }, {
            name: 'Net Cashflow',
            type: 'line',
            data: newNet
        }]);
    }
}
</script>
