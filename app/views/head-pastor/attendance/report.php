<?php
use App\Utilities\AssetHelper;
use App\Models\Attendance;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$startDate = $startDate ?? date('Y-m-01');
$endDate = $endDate ?? date('Y-m-t');
$selectedUnitId = $selectedUnitId ?? '';
$selectedEventType = $selectedEventType ?? '';
$churchUnits = $churchUnits ?? [];
$eventTypes = $eventTypes ?? Attendance::getEventTypes();

$summary = $summary ?? [
    'total_present' => 0, 'total_absent' => 0, 'first_timers' => 0, 'returning_members' => 0,
    'adults' => 0, 'teens' => 0, 'children' => 0, 'total_sessions' => 0
];
$services = $services ?? [];
$eventBreakdown = $eventBreakdown ?? [];
$trend = $trend ?? [];

$totalPresent = (int)($summary['total_present'] ?? 0);
$totalAbsent = (int)($summary['total_absent'] ?? 0);
$totalRegisteredAttendance = $totalPresent + $totalAbsent;
$overallRate = $totalRegisteredAttendance > 0 ? round(($totalPresent / $totalRegisteredAttendance) * 100, 1) : 0;
$firstTimers = (int)($summary['first_timers'] ?? 0);
$returningMembers = (int)($summary['returning_members'] ?? 0);
$firstTimerRate = $totalPresent > 0 ? round(($firstTimers / $totalPresent) * 100, 1) : 0;

$adults = (int)($summary['adults'] ?? 0);
$teens = (int)($summary['teens'] ?? 0);
$children = (int)($summary['children'] ?? 0);
$demographicTotal = $adults + $teens + $children;

$totalSessions = (int)($summary['total_sessions'] ?? count($services));
$avgPerSession = $totalSessions > 0 ? round($totalPresent / $totalSessions, 1) : 0;

// Prepare trend chart data
$trendLabels = array_column($trend, 'date_label');
$trendPresent = array_map('intval', array_column($trend, 'present'));
$trendAbsent = array_map('intval', array_column($trend, 'absent'));
$trendFirstTimers = array_map('intval', array_column($trend, 'first_timers'));
?>

<!-- Print-only Title Header -->
<div class="d-none d-print-block text-center mb-4 pb-2 border-bottom">
    <h2 class="mb-1 text-dark fw-bold"><?= htmlspecialchars($church['name'] ?? 'Life Changers Church') ?></h2>
    <h4 class="text-uppercase text-muted font-size-14 mb-1">Official Attendance & Demographics Report</h4>
    <p class="text-muted font-size-12 mb-0">Period: <?= date('F j, Y', strtotime($startDate)) ?> &mdash; <?= date('F j, Y', strtotime($endDate)) ?> | Generated on <?= date('M d, Y h:i A') ?></p>
</div>

<!-- Screen-only Header & Breadcrumbs -->
<div class="row d-print-none mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18 fw-bold text-dark">
                <i class="bx bx-file-blank text-primary me-2"></i>Attendance Intelligence Report
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url("churches/{$churchId}/attendance") ?>">Attendance</a></li>
                    <li class="breadcrumb-item active">Period Report</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Report Control & Filter Card -->
<div class="row d-print-none mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4">
                <form method="GET" action="<?= AssetHelper::url("churches/{$churchId}/attendance/report") ?>" id="reportFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold font-size-13 text-dark">Start Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bx bx-calendar text-muted"></i></span>
                                <input type="date" name="start_date" id="startDateInput" class="form-control border-start-0 ps-0" value="<?= htmlspecialchars($startDate) ?>" required>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold font-size-13 text-dark">End Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bx bx-calendar text-muted"></i></span>
                                <input type="date" name="end_date" id="endDateInput" class="form-control border-start-0 ps-0" value="<?= htmlspecialchars($endDate) ?>" required>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-6">
                            <label class="form-label fw-semibold font-size-13 text-dark">Unit / Scope</label>
                            <select name="unit_id" class="form-select">
                                <option value="">All Services & Units</option>
                                <?php foreach ($churchUnits as $cu): ?>
                                    <option value="<?= $cu['unit_id'] ?>" <?= (string)$selectedUnitId === (string)$cu['unit_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cu['unit_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-xl-2 col-md-6">
                            <label class="form-label fw-semibold font-size-13 text-dark">Service Type</label>
                            <select name="event_type" class="form-select">
                                <option value="">All Service Types</option>
                                <?php foreach ($eventTypes as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $selectedEventType === $key ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-xl-2 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 fw-semibold">
                                <i class="bx bx-filter-alt me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="window.print()" title="Print / Export PDF">
                                <i class="bx bx-printer"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Quick Preset Pills -->
                    <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top align-items-center">
                        <small class="text-muted fw-semibold me-1">Quick Presets:</small>
                        <button type="button" class="btn btn-sm btn-soft-secondary py-1 px-2 font-size-12 rounded-pill preset-btn" data-preset="this_month">This Month</button>
                        <button type="button" class="btn btn-sm btn-soft-secondary py-1 px-2 font-size-12 rounded-pill preset-btn" data-preset="last_month">Last Month</button>
                        <button type="button" class="btn btn-sm btn-soft-secondary py-1 px-2 font-size-12 rounded-pill preset-btn" data-preset="last_30">Last 30 Days</button>
                        <button type="button" class="btn btn-sm btn-soft-secondary py-1 px-2 font-size-12 rounded-pill preset-btn" data-preset="this_quarter">This Quarter</button>
                        <button type="button" class="btn btn-sm btn-soft-secondary py-1 px-2 font-size-12 rounded-pill preset-btn" data-preset="ytd">Year to Date</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Report Summary Header Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%); border-radius: 12px;">
            <div class="card-body p-4 text-white">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <span class="badge bg-white bg-opacity-20 text-white font-size-12 px-3 py-1 mb-2 rounded-pill">
                            <i class="bx bx-calendar-check me-1 align-middle"></i> Report Scope: <?= date('M j, Y', strtotime($startDate)) ?> &mdash; <?= date('M j, Y', strtotime($endDate)) ?>
                        </span>
                        <h3 class="text-white mb-1 fw-bold"><?= htmlspecialchars($church['name'] ?? 'Your Church') ?></h3>
                        <p class="mb-0 text-white-50 font-size-13">
                            Aggregated metrics for <?= $totalSessions ?> service sessions recorded in this timeframe
                        </p>
                    </div>
                    <div class="text-sm-end">
                        <div class="d-inline-block bg-white bg-opacity-10 rounded-3 p-3 text-center">
                            <span class="font-size-12 text-white-50 text-uppercase d-block mb-1">Total Attendance</span>
                            <h2 class="text-white fw-bold mb-0"><?= number_format($totalPresent) ?></h2>
                            <small class="text-success fw-bold"><i class="bx bx-check"></i> <?= $overallRate ?>% Turnout Rate</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Four Executive Metric KPI Cards -->
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
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Total Present</p>
                        <h4 class="mb-0 fw-bold text-dark"><?= number_format($totalPresent) ?></h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge bg-soft-success text-success font-size-11 mb-1 d-block"><?= $overallRate ?>%</span>
                        <small class="text-muted font-size-11"><?= number_format($totalAbsent) ?> absent</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $overallRate ?>%"></div>
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
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">First Timers</p>
                        <h4 class="mb-0 fw-bold text-dark"><?= number_format($firstTimers) ?></h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge bg-soft-warning text-warning font-size-11 mb-1 d-block"><?= $firstTimerRate ?>% share</span>
                        <small class="text-muted font-size-11"><?= number_format($returningMembers) ?> returning</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $firstTimerRate ?>%"></div>
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
                            <i class="bx bx-calendar-event font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Avg Turnout / Service</p>
                        <h4 class="mb-0 fw-bold text-dark"><?= number_format($avgPerSession) ?></h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge bg-soft-primary text-primary font-size-11 mb-1 d-block"><?= $totalSessions ?> sessions</span>
                        <small class="text-muted font-size-11">Recorded</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
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
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Demographics Breakdown</p>
                        <h5 class="mb-0 fw-bold text-dark font-size-14">
                            <?= $adults ?> <small class="text-muted font-size-11">A</small> &bull; 
                            <?= $teens ?> <small class="text-muted font-size-11">T</small> &bull; 
                            <?= $children ?> <small class="text-muted font-size-11">C</small>
                        </h5>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge bg-soft-info text-info font-size-11 mb-1 d-block">Age Split</span>
                        <small class="text-muted font-size-11">Present</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-primary" style="width: <?= $demographicTotal > 0 ? round(($adults/$demographicTotal)*100) : 100 ?>%"></div>
                    <div class="progress-bar bg-info" style="width: <?= $demographicTotal > 0 ? round(($teens/$demographicTotal)*100) : 0 ?>%"></div>
                    <div class="progress-bar bg-warning" style="width: <?= $demographicTotal > 0 ? round(($children/$demographicTotal)*100) : 0 ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visual Analytics (Charts Section) -->
<div class="row g-4 mb-4">
    <!-- Period Attendance Trend Chart -->
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-line-chart text-primary me-2 font-size-18"></i> Attendance Timeline Trend
                </h5>
                <small class="text-muted">Turnout per service date during this period</small>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($trend)): ?>
                    <div style="position: relative; height: 320px;">
                        <canvas id="periodTrendChart"></canvas>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bx bx-bar-chart-alt display-4 opacity-50 mb-2 d-block"></i>
                        <p class="mb-0">No service records found in this selected date range.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Demographics & Retention Visualizer -->
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-pie-chart-alt text-info me-2 font-size-18"></i> Retention & Demographics
                </h5>
                <small class="text-muted">Visitor ratio & age group share</small>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-around">
                <?php if ($totalPresent > 0): ?>
                    <div style="position: relative; height: 180px;">
                        <canvas id="retentionDonutChart"></canvas>
                    </div>
                    <div class="row text-center mt-3 pt-3 border-top g-2">
                        <div class="col-4">
                            <span class="font-size-11 text-muted text-uppercase d-block">Adults</span>
                            <h5 class="fw-bold text-primary mb-0"><?= number_format($adults) ?></h5>
                            <small class="text-muted font-size-11"><?= $demographicTotal > 0 ? round(($adults/$demographicTotal)*100) : 0 ?>%</small>
                        </div>
                        <div class="col-4 border-start border-end">
                            <span class="font-size-11 text-muted text-uppercase d-block">Teens</span>
                            <h5 class="fw-bold text-info mb-0"><?= number_format($teens) ?></h5>
                            <small class="text-muted font-size-11"><?= $demographicTotal > 0 ? round(($teens/$demographicTotal)*100) : 0 ?>%</small>
                        </div>
                        <div class="col-4">
                            <span class="font-size-11 text-muted text-uppercase d-block">Children</span>
                            <h5 class="fw-bold text-warning mb-0"><?= number_format($children) ?></h5>
                            <small class="text-muted font-size-11"><?= $demographicTotal > 0 ? round(($children/$demographicTotal)*100) : 0 ?>%</small>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bx bx-pie-chart-alt text-muted opacity-50 font-size-40 mb-2"></i>
                        <p class="mb-0">No attendance data to generate demographic charts.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Services Log Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-list-check text-primary me-2 font-size-18"></i> Services & Events Log
                    </h5>
                    <small class="text-muted">Itemized breakdown for each service in the selected period</small>
                </div>
                <div class="d-flex gap-2 d-print-none">
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="exportTableToCSV('attendance_report.csv')">
                        <i class="bx bx-download me-1"></i> Export CSV
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="servicesReportTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Service / Event</th>
                                <th>Scope</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">First Timers</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center" style="width: 18%;">Turnout Rate</th>
                                <th class="text-center pe-4 d-print-none">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $svc): ?>
                                <?php
                                    $p = (int)$svc['present_count'];
                                    $a = (int)$svc['absent_count'];
                                    $ft = (int)($svc['first_timers_count'] ?? 0);
                                    $tot = $p + $a;
                                    $rate = $tot > 0 ? round(($p / $tot) * 100) : 0;
                                    $unitId = $svc['unit_id'] ?? null;
                                    $scopeLabel = ($unitId === null) ? '🌟 All Church Members' : ($svc['unit_name'] ?? 'Unit #' . (int)$unitId);
                                    $serviceUrl = AssetHelper::url("churches/{$churchId}/attendance/service") . '?event_date=' . rawurlencode($svc['event_date']) . '&event_type=' . rawurlencode($svc['event_type']);
                                    if ($unitId !== null) {
                                        $serviceUrl .= '&unit_id=' . (int)$unitId;
                                    }
                                    $rateColor = $rate >= 75 ? 'success' : ($rate >= 50 ? 'primary' : 'warning');
                                ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-dark">
                                        <?= date('M d, Y', strtotime($svc['event_date'])) ?>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-soft-primary text-primary px-2 py-1 font-size-12">
                                            <?= htmlspecialchars($eventTypes[$svc['event_type']] ?? ucwords(str_replace('_', ' ', $svc['event_type'] ?? ''))) ?>
                                        </span>
                                        <?php if (!empty($svc['service_description'])): ?>
                                            <small class="text-muted d-block font-size-11"><?= htmlspecialchars(substr($svc['service_description'], 0, 45)) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-size-12">
                                            <?= htmlspecialchars($scopeLabel) ?>
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold text-success font-size-14"><?= number_format($p) ?></td>
                                    <td class="text-center text-warning fw-semibold font-size-14"><?= number_format($ft) ?></td>
                                    <td class="text-center text-muted font-size-14"><?= number_format($a) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-<?= $rateColor ?>" role="progressbar" style="width: <?= $rate ?>%"></div>
                                            </div>
                                            <span class="font-size-12 fw-semibold text-<?= $rateColor ?>"><?= $rate ?>%</span>
                                        </div>
                                    </td>
                                    <td class="text-center pe-4 d-print-none">
                                        <a href="<?= $serviceUrl ?>" class="btn btn-sm btn-soft-primary px-2 py-1" title="View Roll-Call Breakdown">
                                            <i class="bx bx-show align-middle me-1"></i> Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($services)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bx bx-calendar-x display-4 opacity-50 mb-2 d-block"></i>
                                        <h6 class="text-dark">No Service Records Found in Selected Range</h6>
                                        <p class="text-muted font-size-13 mb-0">Try expanding the date range or removing unit/event filters.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($services)): ?>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="3" class="ps-4 text-uppercase">Summary Totals</td>
                                    <td class="text-center text-success"><?= number_format($totalPresent) ?></td>
                                    <td class="text-center text-warning"><?= number_format($firstTimers) ?></td>
                                    <td class="text-center text-muted"><?= number_format($totalAbsent) ?></td>
                                    <td class="text-center"><?= $overallRate ?>% Avg</td>
                                    <td class="d-print-none"></td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js & Preset Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Preset Date Buttons Handler
    var now = new Date();
    function formatDate(d) {
        var month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();
        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;
        return [year, month, day].join('-');
    }

    $(".preset-btn").on("click", function() {
        var preset = $(this).data("preset");
        var s = new Date(), e = new Date();

        if (preset === 'this_month') {
            s = new Date(now.getFullYear(), now.getMonth(), 1);
            e = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        } else if (preset === 'last_month') {
            s = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            e = new Date(now.getFullYear(), now.getMonth(), 0);
        } else if (preset === 'last_30') {
            s = new Date();
            s.setDate(now.getDate() - 30);
            e = new Date();
        } else if (preset === 'this_quarter') {
            var q = Math.floor(now.getMonth() / 3);
            s = new Date(now.getFullYear(), q * 3, 1);
            e = new Date(now.getFullYear(), (q + 1) * 3, 0);
        } else if (preset === 'ytd') {
            s = new Date(now.getFullYear(), 0, 1);
            e = new Date();
        }

        $("#startDateInput").val(formatDate(s));
        $("#endDateInput").val(formatDate(e));
        $("#reportFilterForm").submit();
    });

    // 2. Render Period Trend Line Chart
    <?php if (!empty($trend)): ?>
    var trendCanvas = document.getElementById('periodTrendChart');
    if (trendCanvas) {
        new Chart(trendCanvas, {
            type: 'line',
            data: {
                labels: <?= json_encode($trendLabels) ?>,
                datasets: [
                    {
                        label: 'Present Members',
                        data: <?= json_encode($trendPresent) ?>,
                        borderColor: '#34c38f',
                        backgroundColor: 'rgba(52, 195, 143, 0.15)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#34c38f'
                    },
                    {
                        label: 'First Timers',
                        data: <?= json_encode($trendFirstTimers) ?>,
                        borderColor: '#f1b44c',
                        backgroundColor: 'rgba(241, 180, 76, 0.15)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3,
                        pointBackgroundColor: '#f1b44c'
                    },
                    {
                        label: 'Absent',
                        data: <?= json_encode($trendAbsent) ?>,
                        borderColor: '#74788d',
                        backgroundColor: 'rgba(116, 120, 141, 0.05)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.3,
                        pointRadius: 3,
                        pointBackgroundColor: '#74788d'
                    }
                ]
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
                        position: 'top',
                        labels: { usePointStyle: true, boxWidth: 8, font: { size: 12 } }
                    },
                    tooltip: {
                        backgroundColor: '#2a3042',
                        padding: 10,
                        cornerRadius: 6
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#74788d', font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { color: '#74788d', font: { size: 11 }, precision: 0 }
                    }
                }
            }
        });
    }
    <?php endif; ?>

    // 3. Render Retention / Demographics Donut Chart
    <?php if ($totalPresent > 0): ?>
    var donutCanvas = document.getElementById('retentionDonutChart');
    if (donutCanvas) {
        new Chart(donutCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Returning Members', 'First Timers'],
                datasets: [{
                    data: [<?= $returningMembers ?>, <?= $firstTimers ?>],
                    backgroundColor: ['#556ee6', '#f1b44c'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, boxWidth: 8, padding: 12, font: { size: 12 } }
                    },
                    tooltip: {
                        backgroundColor: '#2a3042',
                        padding: 8,
                        cornerRadius: 6
                    }
                }
            }
        });
    }
    <?php endif; ?>
});

// CSV Export Helper
function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("#servicesReportTable tr");
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        for (var j = 0; j < cols.length - 1; j++) { // Exclude action column
            var text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
            row.push('"' + text.replace(/"/g, '""') + '"');
        }
        csv.push(row.join(","));
    }
    var csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>

<style>
.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
}
@media print {
    body {
        background: #fff !important;
        font-size: 12pt;
    }
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .navbar-header, .vertical-menu, .footer, .d-print-none {
        display: none !important;
    }
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>
