<?php use App\Utilities\AssetHelper; ?>

<style>
/* ═══════════════════════════════════════════════════════════
   FINANCIAL REPORT — Premium Design (matches Attendance Overview)
═══════════════════════════════════════════════════════════ */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
    --fr-income:   #10b981;
    --fr-expense:  #f43f5e;
    --fr-balance:  #6366f1;
    --fr-txn:      #f59e0b;
    --fr-bg:       #f0f4f8;
    --fr-surface:  #ffffff;
    --fr-border:   #e2e8f0;
    --fr-text:     #1e293b;
    --fr-muted:    #64748b;
    --fr-radius:   18px;
    --fr-shadow:   0 4px 24px rgba(0,0,0,.07);
    --fr-shadow-lg:0 12px 48px rgba(0,0,0,.12);
}

.fr-page { font-family: 'Inter', sans-serif; background: var(--fr-bg); min-height: 100vh; }

/* ── Hero Filter Bar ── */
.fr-filter-hero {
    background: linear-gradient(135deg, #14532d 0%, #166534 50%, #15803d 100%);
    padding: 28px 32px 24px;
    border-radius: var(--fr-radius);
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
}
.fr-filter-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: rgba(255,255,255,.05);
    border-radius: 50%;
}
.fr-filter-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; left: 30%;
    width: 300px; height: 300px;
    background: rgba(255,255,255,.03);
    border-radius: 50%;
}
.fr-filter-hero .hero-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.5px;
    margin-bottom: 4px;
}
.fr-filter-hero .hero-subtitle {
    font-size: .82rem;
    color: rgba(255,255,255,.65);
    margin-bottom: 20px;
}
.fr-filter-row {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: flex-end;
    position: relative; z-index: 1;
}
.fr-filter-group { display: flex; flex-direction: column; gap: 5px; }
.fr-filter-group label {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: rgba(255,255,255,.6);
}
.fr-filter-group .form-control,
.fr-filter-group .form-select {
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.2);
    color: #fff;
    border-radius: 10px;
    font-size: .85rem;
    font-family: 'Inter', sans-serif;
    height: 38px;
    padding: 0 12px;
    backdrop-filter: blur(8px);
    transition: all .2s;
    min-width: 140px;
}
.fr-filter-group .form-control:focus,
.fr-filter-group .form-select:focus {
    background: rgba(255,255,255,.18);
    border-color: rgba(255,255,255,.5);
    box-shadow: 0 0 0 3px rgba(255,255,255,.1);
    color: #fff;
    outline: none;
}
.fr-filter-group .form-control::placeholder { color: rgba(255,255,255,.5); }
.fr-filter-group input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1) opacity(.7); }
.fr-filter-group select option { background: #14532d; color: #fff; }

/* Select2 green-dark styling */
.fr-page .select2-container--default .select2-selection--multiple {
    background: rgba(255,255,255,.1) !important;
    border: 1px solid rgba(255,255,255,.2) !important;
    border-radius: 10px !important;
    min-height: 38px !important;
}
.fr-page .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background: rgba(255,255,255,.25) !important;
    border: none !important;
    color: #fff !important;
    border-radius: 6px !important;
    font-size: .75rem !important;
}
.fr-page .select2-container--default .select2-search--inline .select2-search__field { color: #fff !important; }
.fr-page .select2-selection__placeholder { color: rgba(255,255,255,.5) !important; }

.fr-btn-apply {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    border: none; color: #fff;
    border-radius: 10px;
    font-size: .85rem; font-weight: 700;
    font-family: 'Inter', sans-serif;
    padding: 0 22px; height: 38px;
    cursor: pointer; transition: all .2s;
    display: flex; align-items: center; gap: 6px;
    white-space: nowrap;
}
.fr-btn-apply:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(99,102,241,.4); }
.fr-btn-reset {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    color: rgba(255,255,255,.8);
    border-radius: 10px;
    font-size: .85rem; font-family: 'Inter', sans-serif;
    padding: 0 16px; height: 38px;
    cursor: pointer; transition: all .2s;
}
.fr-btn-reset:hover { background: rgba(255,255,255,.2); }

/* ── Live Dot ── */
.live-dot-green {
    width: 7px; height: 7px;
    background: #4ade80;
    border-radius: 50%;
    display: inline-block;
    animation: live-pulse-green 2s infinite;
}
@keyframes live-pulse-green {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .5; transform: scale(.7); }
}

/* ── KPI Cards ── */
.fr-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media (max-width: 1100px) { .fr-kpi-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 580px)  { .fr-kpi-grid { grid-template-columns: 1fr; } }

.fr-kpi {
    background: var(--fr-surface);
    border-radius: var(--fr-radius);
    box-shadow: var(--fr-shadow);
    padding: 22px 22px 18px;
    position: relative;
    overflow: hidden;
    transition: transform .25s ease, box-shadow .25s ease;
    border: 1px solid var(--fr-border);
}
.fr-kpi:hover { transform: translateY(-4px); box-shadow: var(--fr-shadow-lg); }
.fr-kpi-accent {
    position: absolute; top: 0; left: 0; right: 0;
    height: 4px;
    border-radius: var(--fr-radius) var(--fr-radius) 0 0;
}
.fr-kpi-bg-icon {
    position: absolute; right: -10px; bottom: -10px;
    font-size: 5rem; opacity: .06; line-height: 1;
}
.fr-kpi-label {
    font-size: .7rem; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    color: var(--fr-muted); margin-bottom: 8px;
}
.fr-kpi-value {
    font-size: 1.9rem; font-weight: 900;
    line-height: 1; letter-spacing: -1px;
    color: var(--fr-text); margin-bottom: 6px;
}
.fr-kpi-sub { font-size: .75rem; color: var(--fr-muted); font-weight: 500; }
.fr-kpi-icon-wrap {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; margin-bottom: 14px;
}

.kpi-income  .fr-kpi-accent { background: linear-gradient(90deg, #10b981, #34d399); }
.kpi-income  .fr-kpi-icon-wrap { background: #ecfdf5; color: #10b981; }
.kpi-income  .fr-kpi-value { color: #065f46; }

.kpi-expense .fr-kpi-accent { background: linear-gradient(90deg, #f43f5e, #fb7185); }
.kpi-expense .fr-kpi-icon-wrap { background: #fff1f2; color: #f43f5e; }
.kpi-expense .fr-kpi-value { color: #9f1239; }

.kpi-balance .fr-kpi-accent { background: linear-gradient(90deg, #6366f1, #818cf8); }
.kpi-balance .fr-kpi-icon-wrap { background: #eef2ff; color: #6366f1; }
.kpi-balance .fr-kpi-value { color: #3730a3; }

.kpi-balance-neg .fr-kpi-accent { background: linear-gradient(90deg, #f97316, #fb923c); }
.kpi-balance-neg .fr-kpi-icon-wrap { background: #fff7ed; color: #f97316; }
.kpi-balance-neg .fr-kpi-value { color: #7c2d12; }

.kpi-txn  .fr-kpi-accent { background: linear-gradient(90deg, #f59e0b, #fcd34d); }
.kpi-txn  .fr-kpi-icon-wrap { background: #fffbeb; color: #f59e0b; }
.kpi-txn  .fr-kpi-value { color: #78350f; }

/* ── Panel ── */
.fr-panel {
    background: var(--fr-surface);
    border-radius: var(--fr-radius);
    box-shadow: var(--fr-shadow);
    border: 1px solid var(--fr-border);
    overflow: hidden;
    position: relative;
}
.fr-panel-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 22px 14px;
    border-bottom: 1px solid var(--fr-border);
}
.fr-panel-title {
    font-size: .95rem; font-weight: 700;
    color: var(--fr-text);
    display: flex; align-items: center; gap: 8px; margin: 0;
}
.fr-panel-title i {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.icon-blue   { background: #eef2ff; color: #6366f1; }
.icon-green  { background: #ecfdf5; color: #10b981; }
.icon-rose   { background: #fff1f2; color: #f43f5e; }
.icon-orange { background: #fff7ed; color: #f97316; }
.icon-amber  { background: #fffbeb; color: #f59e0b; }
.icon-violet { background: #f5f3ff; color: #7c3aed; }
.icon-cyan   { background: #ecfeff; color: #0891b2; }

.fr-panel-badge {
    font-size: .72rem; font-weight: 700;
    padding: 4px 10px; border-radius: 20px;
    background: #f1f5f9; color: var(--fr-muted);
}
.fr-panel-body { padding: 20px 22px; }
.fr-panel-body-flush { padding: 0; }

/* ── Loader ── */
.fr-loader {
    display: none;
    position: absolute; inset: 0;
    background: rgba(255,255,255,.8);
    z-index: 20; align-items: center; justify-content: center;
    backdrop-filter: blur(2px);
    border-radius: var(--fr-radius);
}
.fr-loader.show { display: flex; }
.fr-spinner {
    width: 40px; height: 40px;
    border: 3px solid #e2e8f0;
    border-top-color: #10b981;
    border-radius: 50%;
    animation: fr-spin .7s linear infinite;
}
@keyframes fr-spin { to { transform: rotate(360deg); } }

/* ── Tables ── */
.fr-table { width: 100%; border-collapse: collapse; }
.fr-table thead th {
    font-size: .7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .07em;
    color: var(--fr-muted); background: #f8fafc;
    padding: 11px 18px;
    border-bottom: 1px solid var(--fr-border);
    white-space: nowrap;
}
.fr-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .15s; }
.fr-table tbody tr:last-child { border-bottom: none; }
.fr-table tbody tr:hover { background: #f8fafc; }
.fr-table td { padding: 13px 18px; font-size: .875rem; color: var(--fr-text); vertical-align: middle; }

/* ── Type Badge ── */
.type-badge {
    display: inline-block; padding: 3px 10px; border-radius: 20px;
    font-size: .7rem; font-weight: 700; letter-spacing: .04em;
}
.type-income  { background: #dcfce7; color: #15803d; }
.type-expense { background: #fee2e2; color: #b91c1c; }

/* ── Donut Legend ── */
.fr-legend-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 7px 0; border-bottom: 1px solid #f8fafc;
    font-size: .82rem;
}
.fr-legend-row:last-child { border-bottom: none; }
.fr-legend-dot {
    width: 9px; height: 9px; border-radius: 50%;
    display: inline-block; margin-right: 7px; flex-shrink: 0;
}

/* ── Progress bar in church table ── */
.fr-bar { height: 6px; background: #f1f5f9; border-radius: 3px; overflow: hidden; margin-top: 4px; }
.fr-bar-fill { height: 100%; border-radius: 3px; transition: width .8s ease; }

/* ── Empty state ── */
.fr-empty {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 48px 20px; color: var(--fr-muted);
}
.fr-empty i { font-size: 2.5rem; margin-bottom: 10px; opacity: .4; }
.fr-empty p  { font-size: .85rem; margin: 0; }
</style>

<div class="fr-page">

    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Financial Report</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <?php foreach ($breadcrumbs as $crumb): ?>
                            <li class="breadcrumb-item <?= !isset($crumb['url']) ? 'active' : '' ?>">
                                <?= isset($crumb['url']) ? '<a href="'.AssetHelper::url($crumb['url']).'">'.$crumb['label'].'</a>' : $crumb['label'] ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Hero Filter Bar ── -->
    <div class="fr-filter-hero">
        <div class="d-flex align-items-center gap-2 mb-1">
            <div class="live-dot-green"></div>
            <span class="hero-title">Financial Dashboard</span>
        </div>
        <p class="hero-subtitle">Live income, expense &amp; net balance across all churches — filtered by date &amp; congregation</p>

        <div class="fr-filter-row">
            <div class="fr-filter-group">
                <label>From Date</label>
                <input type="date" id="startDate" class="form-control" value="<?= htmlspecialchars($defaultStart) ?>">
            </div>
            <div class="fr-filter-group">
                <label>To Date</label>
                <input type="date" id="endDate" class="form-control" value="<?= htmlspecialchars($defaultEnd) ?>">
            </div>
            <div class="fr-filter-group" style="flex: 1; min-width: 200px;">
                <label>Filter Churches</label>
                <select id="churchSelect" class="form-select select2-multi" multiple style="min-width: 200px;">
                    <?php foreach ($churches as $church): ?>
                        <option value="<?= (int)$church['id'] ?>"><?= htmlspecialchars($church['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="fr-filter-group" style="margin-left: auto;">
                <label>&nbsp;</label>
                <div class="d-flex gap-2">
                    <button id="applyFilter" class="fr-btn-apply">
                        <i class="bx bx-search-alt-2"></i> Apply
                    </button>
                    <button id="resetFilter" class="fr-btn-reset">
                        <i class="bx bx-reset"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── KPI Cards ── -->
    <div class="fr-kpi-grid">

        <div class="fr-kpi kpi-income">
            <div class="fr-kpi-accent"></div>
            <div class="fr-loader" id="kpiLoader1"><div class="fr-spinner"></div></div>
            <div class="fr-kpi-icon-wrap"><i class="bx bx-trending-up"></i></div>
            <div class="fr-kpi-label">Total Income</div>
            <div class="fr-kpi-value" id="kpiIncome">—</div>
            <div class="fr-kpi-sub" id="kpiIncomeChurches">across all churches</div>
            <div class="fr-kpi-bg-icon">💰</div>
        </div>

        <div class="fr-kpi kpi-expense">
            <div class="fr-kpi-accent"></div>
            <div class="fr-kpi-icon-wrap"><i class="bx bx-trending-down"></i></div>
            <div class="fr-kpi-label">Total Expenses</div>
            <div class="fr-kpi-value" id="kpiExpense">—</div>
            <div class="fr-kpi-sub">total expenditure</div>
            <div class="fr-kpi-bg-icon">💸</div>
        </div>

        <div class="fr-kpi kpi-balance" id="kpiBalanceCard">
            <div class="fr-kpi-accent" id="kpiBalanceAccent"></div>
            <div class="fr-kpi-icon-wrap" id="kpiBalanceIcon"><i class="bx bx-wallet"></i></div>
            <div class="fr-kpi-label">Net Balance</div>
            <div class="fr-kpi-value" id="kpiBalance">—</div>
            <div class="fr-kpi-sub" id="kpiBalanceSub">—</div>
            <div class="fr-kpi-bg-icon" id="kpiBalanceBg">⚖️</div>
        </div>

        <div class="fr-kpi kpi-txn">
            <div class="fr-kpi-accent"></div>
            <div class="fr-kpi-icon-wrap"><i class="bx bx-receipt"></i></div>
            <div class="fr-kpi-label">Transactions</div>
            <div class="fr-kpi-value" id="kpiTxn">—</div>
            <div class="fr-kpi-sub" id="kpiChurchCount">Loading…</div>
            <div class="fr-kpi-bg-icon">🧾</div>
        </div>

    </div>

    <!-- ── Monthly Trend Chart ── -->
    <div class="fr-panel mb-4" style="position:relative;">
        <div class="fr-loader" id="trendLoader"><div class="fr-spinner"></div></div>
        <div class="fr-panel-header">
            <h6 class="fr-panel-title">
                <span class="icon-blue"><i class="bx bx-bar-chart-alt-2"></i></span>
                Income vs Expenses — Monthly Trend
            </h6>
            <span class="fr-panel-badge" id="trendRangeLabel"></span>
        </div>
        <div class="fr-panel-body" style="height: 270px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- ── Income Donut + Expense Donut ── -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="fr-panel h-100" style="position:relative;">
                <div class="fr-loader" id="incomeCatLoader"><div class="fr-spinner"></div></div>
                <div class="fr-panel-header">
                    <h6 class="fr-panel-title">
                        <span class="icon-green"><i class="bx bx-pie-chart-alt"></i></span>
                        Income by Category
                    </h6>
                </div>
                <div class="fr-panel-body d-flex gap-4 align-items-center">
                    <div style="width: 180px; flex-shrink:0;">
                        <canvas id="incomeCatChart"></canvas>
                    </div>
                    <div id="incomeCatLegend" style="flex:1;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="fr-panel h-100" style="position:relative;">
                <div class="fr-loader" id="expenseCatLoader"><div class="fr-spinner"></div></div>
                <div class="fr-panel-header">
                    <h6 class="fr-panel-title">
                        <span class="icon-rose"><i class="bx bx-pie-chart-alt"></i></span>
                        Expenses by Category
                    </h6>
                </div>
                <div class="fr-panel-body d-flex gap-4 align-items-center">
                    <div style="width: 180px; flex-shrink:0;">
                        <canvas id="expenseCatChart"></canvas>
                    </div>
                    <div id="expenseCatLegend" style="flex:1;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Church Comparison Chart ── -->
    <div class="fr-panel mb-4" style="position:relative;">
        <div class="fr-loader" id="churchLoader"><div class="fr-spinner"></div></div>
        <div class="fr-panel-header">
            <h6 class="fr-panel-title">
                <span class="icon-orange"><i class="bx bx-buildings"></i></span>
                Church Financial Comparison
            </h6>
            <span class="fr-panel-badge" id="churchCountBadge"></span>
        </div>
        <div class="fr-panel-body" style="height: 270px;">
            <canvas id="churchChart"></canvas>
        </div>
    </div>

    <!-- ── Church Breakdown Table ── -->
    <div class="fr-panel mb-4" style="position:relative;">
        <div class="fr-loader" id="tableLoader"><div class="fr-spinner"></div></div>
        <div class="fr-panel-header">
            <h6 class="fr-panel-title">
                <span class="icon-violet"><i class="bx bx-table"></i></span>
                Church-by-Church Breakdown
            </h6>
        </div>
        <div class="fr-panel-body-flush">
            <div class="table-responsive">
                <table class="fr-table">
                    <thead>
                        <tr>
                            <th>Church</th>
                            <th>Total Income</th>
                            <th>Total Expenses</th>
                            <th>Net Balance</th>
                            <th>Transactions</th>
                            <th style="min-width:160px;">Balance Ratio</th>
                        </tr>
                    </thead>
                    <tbody id="churchTableBody">
                        <tr><td colspan="6" class="text-center py-5 text-muted" style="font-size:.85rem;">Loading data…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ── Recent Transactions ── -->
    <div class="fr-panel mb-4" style="position:relative;">
        <div class="fr-loader" id="txnLoader"><div class="fr-spinner"></div></div>
        <div class="fr-panel-header">
            <h6 class="fr-panel-title">
                <span class="icon-cyan"><i class="bx bx-list-ul"></i></span>
                Recent Transactions
            </h6>
            <span class="fr-panel-badge" id="txnCountBadge"></span>
        </div>
        <div class="fr-panel-body-flush">
            <div class="table-responsive">
                <table class="fr-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Church</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody id="txnTableBody">
                        <tr><td colspan="6" class="text-center py-5 text-muted" style="font-size:.85rem;">Loading data…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div><!-- /.fr-page -->

<?php ob_start(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.2/chart.umd.min.js"></script>
<script>
(function() {
    'use strict';

    let trendChart, churchChart, incomeCatChart, expenseCatChart;

    const BASE    = '<?= rtrim(\App\Utilities\AssetHelper::url(''), '/') ?>';
    const PALETTE = ['#10b981','#6366f1','#f43f5e','#f59e0b','#0ea5e9','#a855f7','#f97316','#14b8a6','#ec4899','#84cc16'];

    const fmtCur = v => '₦' + Number(v || 0).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const fmtN   = v => Number(v || 0).toLocaleString();

    // ── Select2 + Bindings ────────────────────────────────
    $(document).ready(function() {
        $('#churchSelect').select2({ placeholder: 'All churches', allowClear: true, width: '100%' });
        $('#applyFilter').on('click', fetchData);
        $('#resetFilter').on('click', function() {
            const today = new Date();
            $('#startDate').val(today.getFullYear() + '-01-01');
            $('#endDate').val(today.toISOString().split('T')[0]);
            $('#churchSelect').val(null).trigger('change');
            fetchData();
        });
        fetchData();
    });

    // ── Fetch ─────────────────────────────────────────────
    function fetchData() {
        showLoaders();
        const params = new URLSearchParams();
        params.set('start_date', $('#startDate').val());
        params.set('end_date',   $('#endDate').val());
        ($('#churchSelect').val() || []).forEach(id => params.append('church_ids[]', id));

        fetch(BASE + '/admin/finance-report/data?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(json => {
            if (!json.success) return;
            renderKPIs(json.summary);
            renderTrendChart(json.monthly_chart);
            renderCatChart('income', json.income_categories);
            renderCatChart('expense', json.expense_categories);
            renderChurchChart(json.church_comparison);
            renderChurchTable(json.church_comparison.table || []);
            renderTxnTable(json.recent_transactions || []);
            hideLoaders();
        })
        .catch(err => { console.error(err); hideLoaders(); });
    }

    // ── KPIs ──────────────────────────────────────────────
    function renderKPIs(s) {
        countUpCur('kpiIncome',  s.total_income);
        countUpCur('kpiExpense', s.total_expense);
        countUpCur('kpiBalance', Math.abs(s.net_balance));
        countUpInt('kpiTxn',     s.transaction_count);

        document.getElementById('kpiChurchCount').textContent   = fmtN(s.church_count) + ' church' + (s.church_count !== 1 ? 'es' : '');
        document.getElementById('kpiIncomeChurches').textContent = 'from ' + fmtN(s.church_count) + ' church' + (s.church_count !== 1 ? 'es' : '');

        const balCard   = document.getElementById('kpiBalanceCard');
        const balSub    = document.getElementById('kpiBalanceSub');
        const balBg     = document.getElementById('kpiBalanceBg');
        const surplus   = s.net_balance >= 0;

        balCard.classList.toggle('kpi-balance',     surplus);
        balCard.classList.toggle('kpi-balance-neg', !surplus);
        balSub.textContent   = surplus ? '✓ Surplus' : '⚠ Deficit';
        balSub.style.color   = surplus ? '#10b981' : '#f97316';
        balBg.textContent    = surplus ? '📈' : '📉';

        const start = $('#startDate').val(), end = $('#endDate').val();
        document.getElementById('trendRangeLabel').textContent = start + ' → ' + end;
    }

    // ── Trend Chart ───────────────────────────────────────
    function renderTrendChart(data) {
        const ctx = document.getElementById('trendChart').getContext('2d');
        if (trendChart) trendChart.destroy();

        trendChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Income',
                        data: data.income,
                        backgroundColor: 'rgba(16,185,129,.8)',
                        borderRadius: 7,
                        borderSkipped: false,
                    },
                    {
                        label: 'Expenses',
                        data: data.expense,
                        backgroundColor: 'rgba(244,63,94,.75)',
                        borderRadius: 7,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, pointStyleWidth: 10, font: { family: 'Inter', size: 12, weight: '600' } }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { family: 'Inter', size: 12, weight: '700' },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 12, cornerRadius: 10,
                        callbacks: { label: c => '  ' + c.dataset.label + ': ' + fmtCur(c.raw) }
                    }
                },
                scales: {
                    y: {
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: { callback: v => '₦' + Number(v / 1000).toFixed(0) + 'k', font: { family: 'Inter', size: 11 }, color: '#94a3b8' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#94a3b8' }
                    }
                }
            }
        });
    }

    // ── Category Donut Charts ─────────────────────────────
    function renderCatChart(type, data) {
        const canvasId = type === 'income' ? 'incomeCatChart'  : 'expenseCatChart';
        const legendId = type === 'income' ? 'incomeCatLegend' : 'expenseCatLegend';
        const ctx      = document.getElementById(canvasId).getContext('2d');

        if (type === 'income'  && incomeCatChart)  incomeCatChart.destroy();
        if (type === 'expense' && expenseCatChart) expenseCatChart.destroy();

        const legendEl = document.getElementById(legendId);

        if (!data.labels?.length) {
            legendEl.innerHTML = '<div class="fr-empty" style="padding:20px 0;"><i class="bx bx-pie-chart-alt"></i><p>No data for range.</p></div>';
            return;
        }

        const palette = type === 'income'
            ? ['#10b981','#34d399','#6ee7b7','#a7f3d0','#d1fae5','#059669','#047857','#065f46','#0d9488','#0891b2']
            : ['#f43f5e','#fb7185','#fda4af','#fecdd3','#f43f5e','#e11d48','#be123c','#9f1239','#f97316','#ef4444'];

        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{ data: data.values, backgroundColor: palette, borderWidth: 2, borderColor: '#fff' }]
            },
            options: {
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10, cornerRadius: 8,
                        callbacks: { label: c => '  ' + c.label + ': ' + fmtCur(c.raw) }
                    }
                }
            }
        });

        if (type === 'income')  incomeCatChart  = chart;
        else                    expenseCatChart = chart;

        const total = data.values.reduce((a, b) => a + b, 0);
        legendEl.innerHTML = data.labels.map((l, i) => `
            <div class="fr-legend-row">
                <span><span class="fr-legend-dot" style="background:${palette[i]};"></span>${l}</span>
                <div class="text-end">
                    <div style="font-size:.82rem; font-weight:700; color:#1e293b;">${fmtCur(data.values[i])}</div>
                    <div style="font-size:.7rem; color:#94a3b8;">${total > 0 ? ((data.values[i]/total)*100).toFixed(1) : 0}%</div>
                </div>
            </div>
        `).join('');
    }

    // ── Church Comparison Chart ───────────────────────────
    function renderChurchChart(data) {
        document.getElementById('churchCountBadge').textContent = (data.labels?.length || 0) + ' churches';
        const ctx = document.getElementById('churchChart').getContext('2d');
        if (churchChart) churchChart.destroy();

        churchChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Income',
                        data: data.income,
                        backgroundColor: 'rgba(16,185,129,.8)',
                        borderRadius: 7, borderSkipped: false,
                    },
                    {
                        label: 'Expenses',
                        data: data.expense,
                        backgroundColor: 'rgba(244,63,94,.75)',
                        borderRadius: 7, borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, pointStyleWidth: 10, font: { family: 'Inter', size: 12, weight: '600' } }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b', padding: 12, cornerRadius: 10,
                        callbacks: { label: c => '  ' + c.dataset.label + ': ' + fmtCur(c.raw) }
                    }
                },
                scales: {
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { callback: v => '₦' + Number(v / 1000).toFixed(0) + 'k', font: { family: 'Inter', size: 11 }, color: '#94a3b8' }
                    },
                    x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 11 }, color: '#1e293b' } }
                }
            }
        });
    }

    // ── Church Table ──────────────────────────────────────
    function renderChurchTable(rows) {
        const tbody = document.getElementById('churchTableBody');
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">No data for the selected filters.</td></tr>';
            return;
        }
        const maxIncome = Math.max(...rows.map(r => r.total_income), 1);
        tbody.innerHTML = rows.map(r => {
            const surplus = r.net_balance >= 0;
            const balCls  = surplus ? '#10b981' : '#f43f5e';
            const barPct  = Math.round((r.total_income / maxIncome) * 100);
            const barColor = surplus ? '#10b981' : '#f43f5e';
            return `<tr>
                <td style="font-weight:600; color:#1e293b;">${r.church_name}</td>
                <td style="color:#10b981; font-weight:700;">${fmtCur(r.total_income)}</td>
                <td style="color:#f43f5e; font-weight:600;">${fmtCur(r.total_expense)}</td>
                <td style="color:${balCls}; font-weight:700;">${surplus ? '+' : '-'}${fmtCur(Math.abs(r.net_balance))}</td>
                <td style="font-weight:600;">${fmtN(r.transaction_count)}</td>
                <td>
                    <div class="fr-bar" style="width:140px;">
                        <div class="fr-bar-fill" style="width:${barPct}%; background:${barColor};"></div>
                    </div>
                    <div style="font-size:.7rem; color:#94a3b8; margin-top:2px;">${barPct}% of top</div>
                </td>
            </tr>`;
        }).join('');
    }

    // ── Transaction Table ─────────────────────────────────
    function renderTxnTable(rows) {
        document.getElementById('txnCountBadge').textContent = rows.length + ' shown';
        const tbody = document.getElementById('txnTableBody');
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">No transactions for selected range.</td></tr>';
            return;
        }
        tbody.innerHTML = rows.map(r => {
            const isIncome = r.transaction_type === 'income';
            const amtColor = isIncome ? '#10b981' : '#f43f5e';
            const badgeCls = isIncome ? 'type-income' : 'type-expense';
            return `<tr>
                <td style="color:#64748b; font-size:.82rem;">${r.transaction_date}</td>
                <td style="font-weight:500;">${r.church_name ?? '—'}</td>
                <td style="color:#475569;">${r.description ?? '—'}</td>
                <td><span style="background:#f1f5f9; color:#475569; border-radius:6px; padding:2px 9px; font-size:.75rem; font-weight:600;">${r.category ?? 'Other'}</span></td>
                <td><span class="type-badge ${badgeCls}">${isIncome ? 'Income' : 'Expense'}</span></td>
                <td style="font-weight:700; color:${amtColor};">${fmtCur(r.amount)}</td>
            </tr>`;
        }).join('');
    }

    // ── Loaders ───────────────────────────────────────────
    function showLoaders() { document.querySelectorAll('.fr-loader').forEach(el => el.classList.add('show')); }
    function hideLoaders() { document.querySelectorAll('.fr-loader').forEach(el => el.classList.remove('show')); }

    // ── Animations ────────────────────────────────────────
    function countUpCur(id, target) {
        const el  = document.getElementById(id);
        const val = parseFloat(target) || 0;
        const dur = 900; const step = 16; const steps = dur / step;
        let cur = 0; const inc = val / steps;
        const t = setInterval(() => {
            cur = Math.min(cur + inc, val);
            el.textContent = fmtCur(cur);
            if (cur >= val) clearInterval(t);
        }, step);
    }
    function countUpInt(id, target) {
        const el  = document.getElementById(id);
        const val = parseInt(target) || 0;
        const dur = 900; const step = 16; const steps = dur / step;
        let cur = 0; const inc = val / steps;
        const t = setInterval(() => {
            cur = Math.min(cur + inc, val);
            el.textContent = Math.round(cur).toLocaleString();
            if (cur >= val) clearInterval(t);
        }, step);
    }

})();
</script>
<?php $pageJs = ob_get_clean(); ?>
