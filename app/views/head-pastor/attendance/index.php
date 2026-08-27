<?php
use App\Utilities\AssetHelper;
use App\Models\Attendance;

$church = $church ?? null;
$services = $services ?? [];
$eventTypeLabels = Attendance::getEventTypes();
$churchId = $church['id'] ?? 0;
$totalCongregation = $totalCongregation ?? 0;
$avgAttendance = $avgAttendance ?? 0;
$firstTimersAll = $firstTimersAll ?? 0;
$allServicesCount = $allServicesCount ?? count($services);

// Calculate recent service stats
$latestService = !empty($services) ? $services[0] : null;
$latestPresent = $latestService ? (int)$latestService['present_count'] : 0;
$latestAbsent = $latestService ? (int)$latestService['absent_count'] : 0;
$latestTotal = $latestPresent + $latestAbsent;
$latestRate = $latestTotal > 0 ? round(($latestPresent / $latestTotal) * 100, 1) : 0;

// Prepare Event Type Breakdown data for donut chart
$eventLabels = [];
$eventCounts = [];
$eventColors = ['#556ee6', '#34c38f', '#f1b44c', '#50a5f1', '#f46a6a', '#74788d', '#e83e8c', '#6f42c1'];
$i = 0;
foreach ($eventTypeBreakdown ?? [] as $eb) {
    $typeName = $eventTypeLabels[$eb['event_type']] ?? ucwords(str_replace('_', ' ', $eb['event_type']));
    $eventLabels[] = $typeName;
    $eventCounts[] = (int)$eb['total_present'];
}
?>

<!-- Header & Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 12px;">
            <div class="card-body p-4 text-white">
                <div class="row align-items-center">
                    <div class="col-lg-7 col-md-12 mb-3 mb-lg-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md me-3 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bx bx-chart font-size-28 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-white mb-1 fw-bold">Attendance & Growth Hub</h3>
                                <p class="mb-0 text-white-50 font-size-14">Real-time attendance intelligence, trends, and congregational growth for <strong><?= htmlspecialchars($church['name'] ?? 'Your Church') ?></strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-12 text-lg-end">
                        <div class="d-inline-flex flex-wrap gap-2">
                            <a href="<?= AssetHelper::url("churches/{$churchId}/attendance/mark") ?>" class="btn btn-success shadow-sm px-3 fw-semibold">
                                <i class="bx bx-check-double me-1 font-size-16 align-middle"></i> Mark Attendance
                            </a>
                            <a href="<?= AssetHelper::url("churches/{$churchId}/attendance/records") ?>" class="btn btn-light shadow-sm px-3 fw-semibold">
                                <i class="bx bx-list-ul me-1 font-size-16 align-middle"></i> Full Ledger
                            </a>
                            <a href="<?= AssetHelper::url("churches/{$churchId}/attendance/report") ?>" class="btn btn-outline-light px-3 fw-semibold">
                                <i class="bx bx-file me-1 font-size-16 align-middle"></i> Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern KPI Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm rounded-3 stat-card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 bg-success bg-opacity-10 d-flex align-items-center justify-content-center text-success">
                            <i class="bx bx-user-check font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-12 mb-1">Latest Turnout</p>
                        <h4 class="mb-0 fw-bold text-dark"><?= number_format($latestPresent) ?></h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <?php if ($latestService): ?>
                            <span class="badge bg-soft-success text-success font-size-11 mb-1 d-block"><?= $latestRate ?>% present</span>
                            <small class="text-muted font-size-11"><?= date('M j', strtotime($latestService['event_date'])) ?></small>
                        <?php else: ?>
                            <span class="badge bg-soft-secondary text-muted font-size-11">No records</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm rounded-3 stat-card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary">
                            <i class="bx bx-trending-up font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-12 mb-1">Avg. Attendance</p>
                        <h4 class="mb-0 fw-bold text-dark"><?= number_format($avgAttendance) ?></h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge bg-soft-primary text-primary font-size-11 mb-1 d-block"><?= $allServicesCount ?> services</span>
                        <small class="text-muted font-size-11">Historical</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm rounded-3 stat-card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 bg-warning bg-opacity-10 d-flex align-items-center justify-content-center text-warning">
                            <i class="bx bx-user-plus font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-12 mb-1">First Timers</p>
                        <h4 class="mb-0 fw-bold text-dark"><?= number_format($firstTimersAll) ?></h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge bg-soft-warning text-warning font-size-11 mb-1 d-block">Welcomed</span>
                        <small class="text-muted font-size-11">Total Guests</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm rounded-3 stat-card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 bg-info bg-opacity-10 d-flex align-items-center justify-content-center text-info">
                            <i class="bx bx-group font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-12 mb-1">Congregation Size</p>
                        <h4 class="mb-0 fw-bold text-dark"><?= number_format($totalCongregation) ?></h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge bg-soft-info text-info font-size-11 mb-1 d-block"><?= count($unitIds) ?> Active Units</span>
                        <small class="text-muted font-size-11">Registered</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Chart Section -->
<div class="row g-4 mb-4">
    <!-- Main Interactive Attendance Chart -->
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-line-chart text-primary me-2 font-size-18"></i> Attendance Growth Trends
                    </h5>
                    <small class="text-muted">Interactive attendance and turnout analytics</small>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <!-- Chart Type Switcher -->
                    <div class="btn-group btn-group-sm" role="group" id="chartTypeGroup">
                        <button type="button" class="btn btn-outline-secondary active" data-chart-type="bar" title="Bar Chart">
                            <i class="bx bx-bar-chart-alt-2"></i> Bar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-chart-type="line" title="Smooth Area Trend">
                            <i class="bx bx-trending-up"></i> Trend Line
                        </button>
                    </div>
                    <!-- Period Filter -->
                    <div class="btn-group btn-group-sm" role="group" id="attendance-chart-filter">
                        <input type="radio" class="btn-check" name="chartPeriod" id="chartPeriodWeekly" value="weekly" autocomplete="off">
                        <label class="btn btn-outline-primary" for="chartPeriodWeekly">Weekly</label>
                        
                        <input type="radio" class="btn-check" name="chartPeriod" id="chartPeriodMonthly" value="monthly" autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="chartPeriodMonthly">Monthly</label>
                        
                        <input type="radio" class="btn-check" name="chartPeriod" id="chartPeriodYearly" value="yearly" autocomplete="off">
                        <label class="btn btn-outline-primary" for="chartPeriodYearly">Yearly</label>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div id="chartLoadingOverlay" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading chart...</span>
                    </div>
                    <p class="text-muted mt-2 font-size-13">Loading attendance metrics...</p>
                </div>
                <div class="chart-container" id="chartWrapper" style="position: relative; height: 350px;">
                    <canvas id="attendanceOverviewChart"></canvas>
                </div>
                <div id="chartEmptyState" class="text-center py-5 d-none">
                    <i class="bx bx-bar-chart-alt text-muted opacity-50" style="font-size: 3.5rem;"></i>
                    <h6 class="mt-2 text-dark">No Attendance Data in Selected Period</h6>
                    <p class="text-muted font-size-13 mb-3">Record Sunday or mid-week service attendance to populate visual trends.</p>
                    <a href="<?= AssetHelper::url("churches/{$churchId}/attendance/mark") ?>" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i> Record Attendance Now
                    </a>
                </div>
            </div>
            <div class="card-footer bg-light bg-opacity-50 border-top py-2 px-4">
                <div class="row text-center font-size-13 text-muted">
                    <div class="col-4">
                        <span class="d-inline-block rounded-circle me-1" style="width: 10px; height: 10px; background-color: #34c38f;"></span>
                        <strong>Present</strong>
                    </div>
                    <div class="col-4">
                        <span class="d-inline-block rounded-circle me-1" style="width: 10px; height: 10px; background-color: #f1b44c;"></span>
                        <strong>First Timers</strong>
                    </div>
                    <div class="col-4">
                        <span class="d-inline-block rounded-circle me-1" style="width: 10px; height: 10px; background-color: #74788d;"></span>
                        <strong>Absent</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Type Breakdown Donut Chart -->
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-pie-chart-alt-2 text-info me-2 font-size-18"></i> Attendance by Service
                </h5>
                <small class="text-muted">Turnout share across service categories</small>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-center">
                <?php if (!empty($eventCounts) && array_sum($eventCounts) > 0): ?>
                    <div style="position: relative; height: 260px;">
                        <canvas id="serviceTypeDonutChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless align-middle mb-0 font-size-12">
                                <tbody>
                                    <?php foreach ($eventTypeBreakdown as $idx => $eb): ?>
                                        <?php 
                                            $totalP = (int)$eb['total_present'];
                                            $grandTotal = array_sum($eventCounts);
                                            $pct = $grandTotal > 0 ? round(($totalP / $grandTotal) * 100, 1) : 0;
                                            $color = $eventColors[$idx % count($eventColors)];
                                            $label = $eventTypeLabels[$eb['event_type']] ?? ucwords(str_replace('_', ' ', $eb['event_type']));
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background-color: <?= $color ?>;"></span>
                                                <?= htmlspecialchars($label) ?>
                                            </td>
                                            <td class="text-end fw-bold text-dark"><?= number_format($totalP) ?></td>
                                            <td class="text-end text-muted"><?= $pct ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bx bx-pie-chart text-muted opacity-50 font-size-40 mb-2"></i>
                        <p class="text-muted mb-0">No service distribution data available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Departmental & Unit Attendance Breakdown -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-buildings text-primary me-2 font-size-18"></i> Unit Attendance Summary
                    </h5>
                    <small class="text-muted">Attendance engagement across branch departments and units</small>
                </div>
                <a href="<?= AssetHelper::url('units') ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bx bx-cog me-1"></i> Manage Units
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Unit / Department</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center" style="width: 25%;">Turnout Health</th>
                                <th class="text-center">Avg / Session</th>
                                <th class="text-center pe-4">Sessions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unitSummaries ?? [] as $unit): ?>
                                <?php 
                                    $present = (int)$unit['total_present'];
                                    $absent = (int)$unit['total_absent'];
                                    $totalMarks = $present + $absent;
                                    $rate = $totalMarks > 0 ? round(($present / $totalMarks) * 100) : 0;
                                    $meetings = (int)$unit['services_counted'];
                                    $avg = $meetings > 0 ? round($present / $meetings, 1) : 0;
                                    $rateColor = $rate >= 75 ? 'success' : ($rate >= 50 ? 'primary' : 'warning');
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs rounded-circle bg-soft-primary text-primary me-2 d-flex align-items-center justify-content-center font-size-13 fw-bold">
                                                <?= strtoupper(substr($unit['unit_name'], 0, 2)) ?>
                                            </div>
                                            <a href="<?= AssetHelper::url("units/{$unit['unit_id']}") ?>" class="text-dark fw-bold hover-primary">
                                                <?= htmlspecialchars($unit['unit_name']) ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-success"><?= number_format($present) ?></td>
                                    <td class="text-center text-muted"><?= number_format($absent) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-<?= $rateColor ?>" role="progressbar" style="width: <?= $rate ?>%" aria-valuenow="<?= $rate ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="font-size-12 fw-semibold text-<?= $rateColor ?>"><?= $rate ?>%</span>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-dark"><?= $avg ?></td>
                                    <td class="text-center pe-4">
                                        <span class="badge rounded-pill bg-soft-info text-info px-2 py-1 font-size-11"><?= $meetings ?> meetings</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($unitSummaries)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bx bx-info-circle me-1 font-size-16"></i> No unit-level attendance recorded yet.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Services Ledger -->
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-history text-primary me-2 font-size-18"></i> Recent Service Records
                    </h5>
                    <small class="text-muted">Latest roll-call sessions and services logged</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= AssetHelper::url("churches/{$churchId}/attendance/records") ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-list-check me-1"></i> View All Records
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Service / Event</th>
                                <th>Scope</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center" style="width: 20%;">Turnout</th>
                                <th class="text-center pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $svc): ?>
                                <?php
                                $present = (int)($svc['present_count'] ?? 0);
                                $absent = (int)($svc['absent_count'] ?? 0);
                                $total = $present + $absent;
                                $rate = $total > 0 ? round(($present / $total) * 100) : 0;
                                $unitId = $svc['unit_id'] ?? null;
                                $scopeLabel = ($unitId === null) ? '🌟 All Church Members' : ($svc['unit_name'] ?? 'Unit #' . (int)$unitId);
                                $serviceUrl = AssetHelper::url("churches/{$churchId}/attendance/service") . '?event_date=' . rawurlencode($svc['event_date']) . '&event_type=' . rawurlencode($svc['event_type']);
                                if ($unitId !== null) {
                                    $serviceUrl .= '&unit_id=' . (int)$unitId;
                                }
                                ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-dark">
                                        <?= date('M d, Y', strtotime($svc['event_date'])) ?>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-soft-primary text-primary px-2 py-1 font-size-12">
                                            <?= htmlspecialchars($eventTypeLabels[$svc['event_type']] ?? ucwords(str_replace('_', ' ', $svc['event_type'] ?? ''))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark font-size-12 border">
                                            <?= htmlspecialchars($scopeLabel) ?>
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold text-success font-size-14"><?= number_format($present) ?></td>
                                    <td class="text-center text-muted font-size-14"><?= number_format($absent) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $rate ?>%" aria-valuenow="<?= $rate ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="font-size-12 fw-semibold text-muted"><?= $rate ?>%</span>
                                        </div>
                                    </td>
                                    <td class="text-center pe-4">
                                        <a href="<?= $serviceUrl ?>" class="btn btn-sm btn-soft-primary px-2 py-1" title="View Service Ledger">
                                            <i class="bx bx-show align-middle me-1"></i> Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($services)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bx bx-calendar-x display-4 text-muted mb-2 opacity-50 d-block"></i>
                                        <h6 class="text-dark">No Attendance Records Found</h6>
                                        <p class="text-muted font-size-13 mb-3">Begin marking roll-calls to establish congregation tracking.</p>
                                        <a href="<?= AssetHelper::url("churches/{$churchId}/attendance/mark") ?>" class="btn btn-primary btn-sm">
                                            <i class="bx bx-plus me-1"></i> Record First Service
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Engine & Interactive Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var chartDataUrl = "<?= AssetHelper::url("churches/{$churchId}/attendance/chart-data") ?>";
    var currentChartType = 'bar';
    var currentPeriod = 'monthly';
    var attendanceChart = null;

    // Cache initial data from controller if available
    var initialChartData = <?= json_encode($chartData ?? []) ?>;

    function renderChart(data, chartType) {
        var canvas = document.getElementById('attendanceOverviewChart');
        if (!canvas) return;

        var emptyState = document.getElementById('chartEmptyState');
        var chartWrapper = document.getElementById('chartWrapper');

        if (!data || !data.length) {
            chartWrapper.classList.add('d-none');
            emptyState.classList.remove('d-none');
            if (attendanceChart) {
                attendanceChart.destroy();
                attendanceChart = null;
            }
            return;
        }

        chartWrapper.classList.remove('d-none');
        emptyState.classList.add('d-none');

        var labels = data.map(function(d) { return d.label; });
        var present = data.map(function(d) { return parseInt(d.present) || 0; });
        var absent = data.map(function(d) { return parseInt(d.absent) || 0; });
        var firstTimer = data.map(function(d) { return parseInt(d.first_timer) || 0; });

        if (attendanceChart) {
            attendanceChart.destroy();
        }

        var datasets = [];

        if (chartType === 'line') {
            datasets = [
                {
                    label: 'Present',
                    data: present,
                    borderColor: '#34c38f',
                    backgroundColor: 'rgba(52, 195, 143, 0.15)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#34c38f'
                },
                {
                    label: 'First Timers',
                    data: firstTimer,
                    borderColor: '#f1b44c',
                    backgroundColor: 'rgba(241, 180, 76, 0.15)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointBackgroundColor: '#f1b44c'
                },
                {
                    label: 'Absent',
                    data: absent,
                    borderColor: '#74788d',
                    backgroundColor: 'rgba(116, 120, 141, 0.05)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.35,
                    pointRadius: 3,
                    pointBackgroundColor: '#74788d'
                }
            ];
        } else {
            // Bar chart
            datasets = [
                {
                    label: 'Present',
                    data: present,
                    backgroundColor: '#34c38f',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 32
                },
                {
                    label: 'First Timers',
                    data: firstTimer,
                    backgroundColor: '#f1b44c',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 32
                },
                {
                    label: 'Absent',
                    data: absent,
                    backgroundColor: '#e2e5e8',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 32
                }
            ];
        }

        attendanceChart = new Chart(canvas, {
            type: chartType,
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#2a3042',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 8,
                        boxPadding: 6,
                        usePointStyle: true
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#74788d', font: { size: 12 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                        ticks: { color: '#74788d', font: { size: 12 }, precision: 0 }
                    }
                }
            }
        });
    }

    function loadAttendanceData(period) {
        var loadingOverlay = document.getElementById('chartLoadingOverlay');
        var chartWrapper = document.getElementById('chartWrapper');

        if (loadingOverlay) loadingOverlay.classList.remove('d-none');
        if (chartWrapper) chartWrapper.classList.add('opacity-50');

        $.ajax({
            url: chartDataUrl + "?period=" + encodeURIComponent(period),
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (loadingOverlay) loadingOverlay.classList.add('d-none');
                if (chartWrapper) chartWrapper.classList.remove('opacity-50');

                if (res.success && res.data) {
                    renderChart(res.data, currentChartType);
                } else {
                    renderChart([], currentChartType);
                }
            },
            error: function() {
                if (loadingOverlay) loadingOverlay.classList.add('d-none');
                if (chartWrapper) chartWrapper.classList.remove('opacity-50');
                renderChart([], currentChartType);
            }
        });
    }

    // Period switcher (Weekly, Monthly, Yearly)
    $("#attendance-chart-filter input[name='chartPeriod']").on("change", function() {
        currentPeriod = $(this).val();
        loadAttendanceData(currentPeriod);
    });

    // Chart Type switcher (Bar vs Line)
    $("#chartTypeGroup button").on("click", function() {
        $("#chartTypeGroup button").removeClass("active");
        $(this).addClass("active");
        currentChartType = $(this).data("chart-type");
        loadAttendanceData(currentPeriod);
    });

    // Initial render
    if (initialChartData && initialChartData.length > 0) {
        renderChart(initialChartData, currentChartType);
    } else {
        loadAttendanceData(currentPeriod);
    }

    // Render Donut Chart for Service Breakdown
    <?php if (!empty($eventCounts) && array_sum($eventCounts) > 0): ?>
    var donutCanvas = document.getElementById('serviceTypeDonutChart');
    if (donutCanvas) {
        new Chart(donutCanvas, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($eventLabels) ?>,
                datasets: [{
                    data: <?= json_encode($eventCounts) ?>,
                    backgroundColor: <?= json_encode(array_slice($eventColors, 0, count($eventLabels))) ?>,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#2a3042',
                        padding: 10,
                        cornerRadius: 6
                    }
                }
            }
        });
    }
    <?php endif; ?>
});
</script>

<style>
.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
}
.hover-primary:hover {
    color: #556ee6 !important;
}
</style>
