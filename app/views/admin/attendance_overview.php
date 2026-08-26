<?php use App\Utilities\AssetHelper; ?>

<style>
/* ═══════════════════════════════════════════════════════════
   ATTENDANCE OVERVIEW — Premium Redesign
═══════════════════════════════════════════════════════════ */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
    --ao-present:  #10b981;
    --ao-absent:   #f43f5e;
    --ao-rate:     #6366f1;
    --ao-ftimer:   #f59e0b;
    --ao-bg:       #f0f4f8;
    --ao-surface:  #ffffff;
    --ao-border:   #e2e8f0;
    --ao-text:     #1e293b;
    --ao-muted:    #64748b;
    --ao-radius:   18px;
    --ao-radius-sm:10px;
    --ao-shadow:   0 4px 24px rgba(0,0,0,.07);
    --ao-shadow-lg:0 12px 48px rgba(0,0,0,.12);
}

.ao-page { font-family: 'Inter', sans-serif; background: var(--ao-bg); min-height: 100vh; }

/* ── Hero Filter Bar ── */
.ao-filter-hero {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
    padding: 28px 32px 24px;
    border-radius: var(--ao-radius);
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
}
.ao-filter-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: rgba(255,255,255,.05);
    border-radius: 50%;
}
.ao-filter-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; left: 30%;
    width: 300px; height: 300px;
    background: rgba(255,255,255,.03);
    border-radius: 50%;
}
.ao-filter-hero .hero-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.5px;
    margin-bottom: 4px;
}
.ao-filter-hero .hero-subtitle {
    font-size: .82rem;
    color: rgba(255,255,255,.65);
    margin-bottom: 20px;
}
.ao-filter-row {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: flex-end;
    position: relative; z-index: 1;
}
.ao-filter-group { display: flex; flex-direction: column; gap: 5px; }
.ao-filter-group label {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: rgba(255,255,255,.6);
}
.ao-filter-group .form-control,
.ao-filter-group .form-select {
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
.ao-filter-group .form-control:focus,
.ao-filter-group .form-select:focus {
    background: rgba(255,255,255,.18);
    border-color: rgba(255,255,255,.5);
    box-shadow: 0 0 0 3px rgba(255,255,255,.1);
    color: #fff;
    outline: none;
}
.ao-filter-group .form-control::placeholder { color: rgba(255,255,255,.5); }
.ao-filter-group input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1) opacity(.7); }
.ao-filter-group select option { background: #312e81; color: #fff; }

.period-tabs {
    display: flex;
    background: rgba(255,255,255,.1);
    border-radius: 10px;
    padding: 3px;
    gap: 3px;
}
.period-tab {
    padding: 6px 14px;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: rgba(255,255,255,.7);
    font-size: .8rem;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: all .2s;
}
.period-tab.active {
    background: #fff;
    color: #312e81;
    box-shadow: 0 2px 8px rgba(0,0,0,.15);
}
.period-tab:hover:not(.active) { background: rgba(255,255,255,.15); color: #fff; }

.ao-btn-apply {
    background: linear-gradient(135deg, #10b981, #059669);
    border: none;
    color: #fff;
    border-radius: 10px;
    font-size: .85rem;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    padding: 0 22px;
    height: 38px;
    cursor: pointer;
    transition: all .2s;
    display: flex; align-items: center; gap: 6px;
    white-space: nowrap;
}
.ao-btn-apply:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(16,185,129,.4); }
.ao-btn-reset {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    color: rgba(255,255,255,.8);
    border-radius: 10px;
    font-size: .85rem;
    font-family: 'Inter', sans-serif;
    padding: 0 16px;
    height: 38px;
    cursor: pointer;
    transition: all .2s;
}
.ao-btn-reset:hover { background: rgba(255,255,255,.2); }

/* Select2 dark styling */
.ao-page .select2-container--default .select2-selection--multiple {
    background: rgba(255,255,255,.1) !important;
    border: 1px solid rgba(255,255,255,.2) !important;
    border-radius: 10px !important;
    min-height: 38px !important;
}
.ao-page .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background: rgba(255,255,255,.25) !important;
    border: none !important;
    color: #fff !important;
    border-radius: 6px !important;
    font-size: .75rem !important;
}
.ao-page .select2-container--default .select2-search--inline .select2-search__field {
    color: #fff !important;
}
.ao-page .select2-selection__placeholder { color: rgba(255,255,255,.5) !important; }

/* ── KPI Cards ── */
.ao-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media (max-width: 1100px) { .ao-kpi-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 580px)  { .ao-kpi-grid { grid-template-columns: 1fr; } }

.ao-kpi {
    background: var(--ao-surface);
    border-radius: var(--ao-radius);
    box-shadow: var(--ao-shadow);
    padding: 22px 22px 18px;
    position: relative;
    overflow: hidden;
    transition: transform .25s ease, box-shadow .25s ease;
    border: 1px solid var(--ao-border);
}
.ao-kpi:hover { transform: translateY(-4px); box-shadow: var(--ao-shadow-lg); }
.ao-kpi-accent {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    border-radius: var(--ao-radius) var(--ao-radius) 0 0;
}
.ao-kpi-bg-icon {
    position: absolute;
    right: -10px; bottom: -10px;
    font-size: 5rem;
    opacity: .06;
    line-height: 1;
}
.ao-kpi-label {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--ao-muted);
    margin-bottom: 8px;
}
.ao-kpi-value {
    font-size: 2.4rem;
    font-weight: 900;
    line-height: 1;
    letter-spacing: -1.5px;
    color: var(--ao-text);
    margin-bottom: 6px;
}
.ao-kpi-sub {
    font-size: .75rem;
    color: var(--ao-muted);
    font-weight: 500;
}
.ao-kpi-icon-wrap {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    margin-bottom: 14px;
}

.kpi-present .ao-kpi-accent { background: linear-gradient(90deg, #10b981, #34d399); }
.kpi-present .ao-kpi-icon-wrap { background: #ecfdf5; color: #10b981; }
.kpi-present .ao-kpi-value { color: #065f46; }

.kpi-absent .ao-kpi-accent { background: linear-gradient(90deg, #f43f5e, #fb7185); }
.kpi-absent .ao-kpi-icon-wrap { background: #fff1f2; color: #f43f5e; }
.kpi-absent .ao-kpi-value { color: #9f1239; }

.kpi-rate .ao-kpi-accent { background: linear-gradient(90deg, #6366f1, #818cf8); }
.kpi-rate .ao-kpi-icon-wrap { background: #eef2ff; color: #6366f1; }
.kpi-rate .ao-kpi-value { color: #3730a3; }

.kpi-ftimer .ao-kpi-accent { background: linear-gradient(90deg, #f59e0b, #fcd34d); }
.kpi-ftimer .ao-kpi-icon-wrap { background: #fffbeb; color: #f59e0b; }
.kpi-ftimer .ao-kpi-value { color: #78350f; }

/* ── Chart Panels ── */
.ao-panel {
    background: var(--ao-surface);
    border-radius: var(--ao-radius);
    box-shadow: var(--ao-shadow);
    border: 1px solid var(--ao-border);
    overflow: hidden;
    position: relative;
}
.ao-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px 14px;
    border-bottom: 1px solid var(--ao-border);
}
.ao-panel-title {
    font-size: .95rem;
    font-weight: 700;
    color: var(--ao-text);
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}
.ao-panel-title i {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.panel-icon-blue   { background: #eef2ff; color: #6366f1; }
.panel-icon-orange { background: #fff7ed; color: #f97316; }
.panel-icon-green  { background: #ecfdf5; color: #10b981; }
.panel-icon-amber  { background: #fffbeb; color: #f59e0b; }
.panel-icon-rose   { background: #fff1f2; color: #f43f5e; }

.ao-panel-badge {
    font-size: .72rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    background: #f1f5f9;
    color: var(--ao-muted);
}
.ao-panel-body { padding: 20px 22px; }
.ao-panel-body-flush { padding: 0; }

/* ── Section Loader ── */
.ao-loader {
    display: none;
    position: absolute; inset: 0;
    background: rgba(255,255,255,.8);
    z-index: 20;
    align-items: center; justify-content: center;
    backdrop-filter: blur(2px);
    border-radius: var(--ao-radius);
}
.ao-loader.show { display: flex; }
.ao-spinner {
    width: 40px; height: 40px;
    border: 3px solid #e2e8f0;
    border-top-color: #6366f1;
    border-radius: 50%;
    animation: ao-spin .7s linear infinite;
}
@keyframes ao-spin { to { transform: rotate(360deg); } }

/* ── Church Ranking Table ── */
.ao-table { width: 100%; border-collapse: collapse; }
.ao-table thead th {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--ao-muted);
    background: #f8fafc;
    padding: 11px 18px;
    border-bottom: 1px solid var(--ao-border);
    white-space: nowrap;
}
.ao-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background .15s;
}
.ao-table tbody tr:last-child { border-bottom: none; }
.ao-table tbody tr:hover { background: #f8fafc; }
.ao-table td {
    padding: 13px 18px;
    font-size: .875rem;
    color: var(--ao-text);
    vertical-align: middle;
}
.ao-rank-badge {
    width: 30px; height: 30px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .78rem;
    font-weight: 800;
}
.rank-gold   { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #fff; box-shadow: 0 3px 8px rgba(245,158,11,.4); }
.rank-silver { background: linear-gradient(135deg, #94a3b8, #64748b); color: #fff; box-shadow: 0 3px 8px rgba(100,116,139,.3); }
.rank-bronze { background: linear-gradient(135deg, #c97b3e, #a0522d); color: #fff; box-shadow: 0 3px 8px rgba(160,82,45,.3); }
.rank-other  { background: #f1f5f9; color: var(--ao-muted); }

.ao-rate-bar {
    width: 80px;
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
    display: inline-block;
    vertical-align: middle;
    margin-left: 8px;
}
.ao-rate-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #34d399);
    border-radius: 3px;
    transition: width .8s ease;
}

/* ── Service Type Pills ── */
.ao-service-item { padding: 14px 22px; border-bottom: 1px solid #f1f5f9; }
.ao-service-item:last-child { border-bottom: none; }
.ao-service-name { font-size: .88rem; font-weight: 600; color: var(--ao-text); }
.ao-service-meta { font-size: .75rem; color: var(--ao-muted); margin-top: 2px; }
.ao-service-track { height: 8px; background: #f1f5f9; border-radius: 4px; margin-top: 10px; overflow: hidden; }
.ao-service-fill { height: 100%; border-radius: 4px; transition: width .8s ease; }

/* ── Empty state ── */
.ao-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 20px; color: var(--ao-muted); }
.ao-empty i { font-size: 2.5rem; margin-bottom: 10px; opacity: .4; }
.ao-empty p { font-size: .85rem; margin: 0; }

/* ── Pulse dot ── */
.live-dot {
    width: 7px; height: 7px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
    animation: live-pulse 2s infinite;
}
@keyframes live-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .5; transform: scale(.7); }
}
</style>

<div class="ao-page">

    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Attendance Overview</h4>
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
    <div class="ao-filter-hero">
        <div class="d-flex align-items-center gap-2 mb-1">
            <div class="live-dot"></div>
            <span class="hero-title">Attendance Dashboard</span>
        </div>
        <p class="hero-subtitle">Live metrics across all churches — filtered by date, period, and congregation</p>

        <div class="ao-filter-row">
            <!-- Period -->
            <div class="ao-filter-group">
                <label>Trend Period</label>
                <div class="period-tabs">
                    <button class="period-tab active" data-period="monthly">Monthly</button>
                    <button class="period-tab" data-period="weekly">Weekly</button>
                    <button class="period-tab" data-period="yearly">Yearly</button>
                </div>
            </div>

            <!-- From Date -->
            <div class="ao-filter-group">
                <label>From Date</label>
                <input type="date" id="startDate" class="form-control" value="<?= htmlspecialchars($defaultStart) ?>">
            </div>

            <!-- To Date -->
            <div class="ao-filter-group">
                <label>To Date</label>
                <input type="date" id="endDate" class="form-control" value="<?= htmlspecialchars($defaultEnd) ?>">
            </div>

            <!-- Churches -->
            <div class="ao-filter-group" style="flex: 1; min-width: 200px;">
                <label>Filter Churches</label>
                <select id="churchSelect" class="form-select select2-multi" multiple style="min-width: 200px;">
                    <?php foreach ($churches as $church): ?>
                        <option value="<?= (int)$church['id'] ?>"><?= htmlspecialchars($church['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Actions -->
            <div class="ao-filter-group" style="margin-left: auto;">
                <label>&nbsp;</label>
                <div class="d-flex gap-2">
                    <button id="applyFilter" class="ao-btn-apply">
                        <i class="bx bx-search-alt-2"></i> Apply
                    </button>
                    <button id="resetFilter" class="ao-btn-reset">
                        <i class="bx bx-reset"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── KPI Cards ── -->
    <div class="ao-kpi-grid">

        <div class="ao-kpi kpi-present">
            <div class="ao-kpi-accent"></div>
            <div class="ao-loader" id="kpiLoader"><div class="ao-spinner"></div></div>
            <div class="ao-kpi-icon-wrap"><i class="bx bx-user-check"></i></div>
            <div class="ao-kpi-label">Total Present</div>
            <div class="ao-kpi-value" id="kpiPresent">—</div>
            <div class="ao-kpi-sub" id="kpiServices">Loading…</div>
            <div class="ao-kpi-bg-icon">✅</div>
        </div>

        <div class="ao-kpi kpi-absent">
            <div class="ao-kpi-accent"></div>
            <div class="ao-kpi-icon-wrap"><i class="bx bx-user-x"></i></div>
            <div class="ao-kpi-label">Total Absent</div>
            <div class="ao-kpi-value" id="kpiAbsent">—</div>
            <div class="ao-kpi-sub" id="kpiAbsentRate">Loading…</div>
            <div class="ao-kpi-bg-icon">❌</div>
        </div>

        <div class="ao-kpi kpi-rate">
            <div class="ao-kpi-accent"></div>
            <div class="ao-kpi-icon-wrap"><i class="bx bx-trending-up"></i></div>
            <div class="ao-kpi-label">Attendance Rate</div>
            <div class="ao-kpi-value" id="kpiRate">—</div>
            <div class="ao-kpi-sub">of all registered members</div>
            <div class="ao-kpi-bg-icon">📈</div>
        </div>

        <div class="ao-kpi kpi-ftimer">
            <div class="ao-kpi-accent"></div>
            <div class="ao-kpi-icon-wrap"><i class="bx bx-star"></i></div>
            <div class="ao-kpi-label">First-Timers</div>
            <div class="ao-kpi-value" id="kpiFirstTimers">—</div>
            <div class="ao-kpi-sub">new visitors in range</div>
            <div class="ao-kpi-bg-icon">⭐</div>
        </div>

    </div>

    <!-- ── Trend Chart (Full width) ── -->
    <div class="ao-panel mb-4" style="position: relative;">
        <div class="ao-loader" id="trendLoader"><div class="ao-spinner"></div></div>
        <div class="ao-panel-header">
            <h6 class="ao-panel-title">
                <span class="panel-icon-blue"><i class="bx bx-line-chart"></i></span>
                Attendance Trend
            </h6>
            <span class="ao-panel-badge" id="trendLabel"></span>
        </div>
        <div class="ao-panel-body" style="height: 260px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- ── Church Comparison + Service Breakdown ── -->
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="ao-panel h-100" style="position: relative;">
                <div class="ao-loader" id="churchLoader"><div class="ao-spinner"></div></div>
                <div class="ao-panel-header">
                    <h6 class="ao-panel-title">
                        <span class="panel-icon-orange"><i class="bx bx-buildings"></i></span>
                        Church Attendance Comparison
                    </h6>
                    <span class="ao-panel-badge" id="churchCountBadge"></span>
                </div>
                <div class="ao-panel-body" style="height: 280px;">
                    <canvas id="churchChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="ao-panel h-100" style="position: relative;">
                <div class="ao-loader" id="serviceLoader"><div class="ao-spinner"></div></div>
                <div class="ao-panel-header">
                    <h6 class="ao-panel-title">
                        <span class="panel-icon-green"><i class="bx bx-calendar-event"></i></span>
                        By Service Type
                    </h6>
                </div>
                <div class="ao-panel-body-flush" id="serviceBreakdownWrap" style="max-height: 320px; overflow-y: auto;">
                    <div class="ao-empty"><i class="bx bx-loader-circle"></i><p>Loading…</p></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Church Ranking Table ── -->
    <div class="ao-panel mb-4" style="position: relative;">
        <div class="ao-loader" id="tableLoader"><div class="ao-spinner"></div></div>
        <div class="ao-panel-header">
            <h6 class="ao-panel-title">
                <span class="panel-icon-amber"><i class="bx bx-trophy"></i></span>
                Church Attendance Rankings
            </h6>
            <span class="ao-panel-badge" id="rankBadge"></span>
        </div>
        <div class="ao-panel-body-flush">
            <div class="table-responsive">
                <table class="ao-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Rank</th>
                            <th>Church</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>First-Timers</th>
                            <th>Rate</th>
                            <th style="min-width:180px;">Progress</th>
                        </tr>
                    </thead>
                    <tbody id="churchTableBody">
                        <tr><td colspan="7" class="text-center py-5 text-muted" style="font-size:.85rem;">Loading data…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div><!-- /.ao-page -->

<?php ob_start(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.2/chart.umd.min.js"></script>
<script>
(function() {
    'use strict';

    let trendChart, churchChart;
    let activePeriod = 'monthly';
    const BASE = '<?= rtrim(AssetHelper::url(''), '/') ?>';
    const fmtN = v => Number(v || 0).toLocaleString();
    const fmtP = v => (Number(v || 0).toFixed(1)) + '%';

    // ── Select2 + Event Bindings ───────────────────────────
    $(document).ready(function() {
        $('#churchSelect').select2({
            placeholder: 'All churches',
            allowClear: true,
            width: '100%',
        });

        $(document).on('click', '.period-tab', function() {
            $('.period-tab').removeClass('active');
            $(this).addClass('active');
            activePeriod = $(this).data('period');
            fetchData();
        });

        $('#applyFilter').on('click', fetchData);
        $('#resetFilter').on('click', function() {
            const today = new Date();
            $('#startDate').val(today.getFullYear() + '-01-01');
            $('#endDate').val(today.toISOString().split('T')[0]);
            $('#churchSelect').val(null).trigger('change');
            activePeriod = 'monthly';
            $('.period-tab').removeClass('active');
            $('[data-period="monthly"]').addClass('active');
            fetchData();
        });

        fetchData();
    });

    // ── Fetch Data ────────────────────────────────────────
    function fetchData() {
        showLoaders();
        const params = new URLSearchParams();
        params.set('period',     activePeriod);
        params.set('start_date', $('#startDate').val());
        params.set('end_date',   $('#endDate').val());
        ($('#churchSelect').val() || []).forEach(id => params.append('church_ids[]', id));

        fetch(BASE + '/admin/attendance-overview/data?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(json => {
            if (!json.success) return;
            renderKPIs(json.summary);
            renderTrendChart(json.trend_chart);
            renderChurchChart(json.church_comparison);
            renderServiceBreakdown(json.service_breakdown || []);
            renderChurchTable(json.church_comparison.table || []);
            hideLoaders();
        })
        .catch(err => { console.error(err); hideLoaders(); });
    }

    // ── KPIs ──────────────────────────────────────────────
    function renderKPIs(s) {
        countUp('kpiPresent',     s.total_present);
        countUp('kpiAbsent',      s.total_absent);
        countUp('kpiFirstTimers', s.first_timers);
        document.getElementById('kpiRate').textContent       = fmtP(s.attendance_rate);
        document.getElementById('kpiServices').textContent   = fmtN(s.total_services) + ' session' + (s.total_services !== 1 ? 's' : '');
        const abRate = (s.total_present + s.total_absent) > 0
            ? ((s.total_absent / (s.total_present + s.total_absent)) * 100).toFixed(1) : 0;
        document.getElementById('kpiAbsentRate').textContent = abRate + '% absence rate';

        const start = $('#startDate').val(), end = $('#endDate').val();
        document.getElementById('trendLabel').textContent =
            activePeriod.charAt(0).toUpperCase() + activePeriod.slice(1) + ' | ' + start + ' → ' + end;
    }

    // ── Trend Chart ───────────────────────────────────────
    function renderTrendChart(data) {
        const ctx = document.getElementById('trendChart').getContext('2d');
        if (trendChart) trendChart.destroy();

        const gradPresent = ctx.createLinearGradient(0, 0, 0, 240);
        gradPresent.addColorStop(0, 'rgba(16,185,129,.25)');
        gradPresent.addColorStop(1, 'rgba(16,185,129,.01)');

        const gradAbsent = ctx.createLinearGradient(0, 0, 0, 240);
        gradAbsent.addColorStop(0, 'rgba(244,63,94,.2)');
        gradAbsent.addColorStop(1, 'rgba(244,63,94,.01)');

        const gradFT = ctx.createLinearGradient(0, 0, 0, 240);
        gradFT.addColorStop(0, 'rgba(99,102,241,.2)');
        gradFT.addColorStop(1, 'rgba(99,102,241,.01)');

        trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Present',
                        data: data.present,
                        borderColor: '#10b981',
                        backgroundColor: gradPresent,
                        fill: true,
                        tension: .4,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#10b981',
                        pointRadius: 4,
                        pointHoverRadius: 7,
                    },
                    {
                        label: 'Absent',
                        data: data.absent,
                        borderColor: '#f43f5e',
                        backgroundColor: gradAbsent,
                        fill: true,
                        tension: .4,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#f43f5e',
                        pointRadius: 4,
                        pointHoverRadius: 7,
                    },
                    {
                        label: 'First-Timers',
                        data: data.first_timers,
                        borderColor: '#6366f1',
                        backgroundColor: gradFT,
                        fill: true,
                        tension: .4,
                        borderWidth: 2.5,
                        borderDash: [6, 3],
                        pointBackgroundColor: '#6366f1',
                        pointRadius: 4,
                        pointHoverRadius: 7,
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
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1e293b',
                        titleFont: { family: 'Inter', size: 12, weight: '700' },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: { label: c => '  ' + c.dataset.label + ': ' + fmtN(c.raw) }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: { precision: 0, font: { family: 'Inter', size: 11 }, color: '#94a3b8' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#94a3b8' }
                    }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false }
            }
        });
    }

    // ── Church Comparison Chart ───────────────────────────
    function renderChurchChart(data) {
        document.getElementById('churchCountBadge').textContent = (data.labels?.length || 0) + ' churches';
        const ctx = document.getElementById('churchChart').getContext('2d');
        if (churchChart) churchChart.destroy();

        churchChart = new Chart(ctx, {
            type: 'bar',
            indexAxis: 'y',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Present',
                        data: data.present,
                        backgroundColor: 'rgba(16,185,129,.85)',
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'First-Timers',
                        data: data.first_timers ?? data.present.map(() => 0),
                        backgroundColor: 'rgba(99,102,241,.75)',
                        borderRadius: 6,
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
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: { label: c => '  ' + c.dataset.label + ': ' + fmtN(c.raw) }
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                        grid: { color: '#f1f5f9' },
                        ticks: { precision: 0, font: { family: 'Inter', size: 11 }, color: '#94a3b8' }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#1e293b', fontWeight: '600' }
                    }
                }
            }
        });
    }

    // ── Service Breakdown ─────────────────────────────────
    const SERVICE_COLORS = ['#6366f1','#10b981','#f59e0b','#f43f5e','#0ea5e9','#a855f7'];

    function renderServiceBreakdown(services) {
        const wrap = document.getElementById('serviceBreakdownWrap');
        if (!services.length) {
            wrap.innerHTML = '<div class="ao-empty"><i class="bx bx-calendar-x"></i><p>No service data in range.</p></div>';
            return;
        }
        const maxPresent = Math.max(...services.map(s => s.total_present), 1);
        wrap.innerHTML = services.map((s, i) => {
            const pct = Math.round((s.total_present / maxPresent) * 100);
            const color = SERVICE_COLORS[i % SERVICE_COLORS.length];
            const label = s.label || s.event_type;
            return `
            <div class="ao-service-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="ao-service-name">${label}</div>
                        <div class="ao-service-meta">${fmtN(s.sessions)} sessions · ${fmtN(s.total_absent)} absent</div>
                    </div>
                    <div class="text-end">
                        <div style="font-size:.92rem; font-weight:700; color:${color};">${fmtN(s.total_present)}</div>
                        <div style="font-size:.7rem; color:#94a3b8;">${fmtP(s.attendance_rate)} rate</div>
                    </div>
                </div>
                <div class="ao-service-track">
                    <div class="ao-service-fill" style="width:${pct}%; background:${color};"></div>
                </div>
            </div>`;
        }).join('');
    }

    // ── Church Ranking Table ──────────────────────────────
    function renderChurchTable(rows) {
        const tbody = document.getElementById('churchTableBody');
        document.getElementById('rankBadge').textContent = rows.length + ' churches';

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">No attendance data for selected filters.</td></tr>';
            return;
        }

        rows.sort((a, b) => b.total_present - a.total_present);
        const maxPresent = rows[0].total_present || 1;

        tbody.innerHTML = rows.map((r, i) => {
            let rankBadge;
            if (i === 0) rankBadge = `<span class="ao-rank-badge rank-gold"><i class="bx bxs-crown" style="font-size:.9rem;"></i></span>`;
            else if (i === 1) rankBadge = `<span class="ao-rank-badge rank-silver">${i+1}</span>`;
            else if (i === 2) rankBadge = `<span class="ao-rank-badge rank-bronze">${i+1}</span>`;
            else rankBadge = `<span class="ao-rank-badge rank-other">${i+1}</span>`;

            const ratePct = r.attendance_rate || 0;
            const barWidth = Math.round((r.total_present / maxPresent) * 100);

            return `<tr>
                <td>${rankBadge}</td>
                <td><span style="font-weight:600; color:#1e293b;">${r.church_name}</span></td>
                <td><span style="color:#10b981; font-weight:700;">${fmtN(r.total_present)}</span></td>
                <td><span style="color:#f43f5e; font-weight:600;">${fmtN(r.total_absent)}</span></td>
                <td><span style="color:#6366f1; font-weight:600;">${fmtN(r.first_timers)}</span></td>
                <td><span style="font-weight:700; color:#1e293b;">${fmtP(ratePct)}</span></td>
                <td>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <div class="ao-rate-bar" style="width:100px;">
                            <div class="ao-rate-bar-fill" style="width:${barWidth}%;"></div>
                        </div>
                        <span style="font-size:.7rem; color:#94a3b8; min-width:32px;">${barWidth}%</span>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    // ── Loaders ───────────────────────────────────────────
    function showLoaders() { document.querySelectorAll('.ao-loader').forEach(el => el.classList.add('show')); }
    function hideLoaders() { document.querySelectorAll('.ao-loader').forEach(el => el.classList.remove('show')); }

    // ── Count-Up Animation ────────────────────────────────
    function countUp(id, target) {
        const el = document.getElementById(id);
        if (!el) return;
        const val = parseInt(target) || 0;
        const dur = 900;
        const step = 16;
        const steps = dur / step;
        let cur = 0;
        const inc = val / steps;
        const t = setInterval(() => {
            cur = Math.min(cur + inc, val);
            el.textContent = Math.round(cur).toLocaleString();
            if (cur >= val) clearInterval(t);
        }, step);
    }

})();
</script>
<?php $pageJs = ob_get_clean(); ?>
