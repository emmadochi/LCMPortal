<?php
use App\Utilities\AssetHelper;
use App\Models\Attendance;

$church = $church ?? null;
$services = $services ?? [];
$eventTypeLabels = Attendance::getEventTypes();
$churchId = $church['id'] ?? 0;
?>

<div class="row">
    <div class="col-12">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h3 class="text-white mb-2">Attendance Dashboard</h3>
                        <p class="mb-0 text-white-50">Monitoring the growth and engagement of <?= htmlspecialchars($church['name']) ?></p>
                    </div>
                    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                        <a href="<?= AssetHelper::url("churches/{$churchId}/attendance/mark") ?>" class="btn btn-light">
                            <i class="bx bx-list-check me-1"></i> Record Attendance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card mini-stats-wid">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-success text-success font-size-24">
                        <i class="bx bx-trending-up"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">Latest Attendance</h5>
                <h3 class="mb-0"><?= !empty($services) ? (int)$services[0]['present_count'] : 0 ?></h3>
                <small class="text-muted"><?= !empty($services) ? date('M j, Y', strtotime($services[0]['event_date'])) : 'No data' ?></small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-info text-info font-size-24">
                        <i class="bx bx-user-plus"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">First Timers (Recent)</h5>
                <h3 class="mb-0"><?= isset($chartData) && !empty($chartData) ? end($chartData)['first_timer'] : 0 ?></h3>
                <small class="text-muted">Current month</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-24">
                        <i class="bx bx-building"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">Units Covered</h5>
                <h3 class="mb-0"><?= count($unitIds) ?></h3>
                <small class="text-muted">Active units</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-warning text-warning font-size-24">
                        <i class="bx bx-calendar"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">Total Services</h5>
                <h3 class="mb-0"><?= count($services) ?></h3>
                <small class="text-muted">This recorded history</small>
            </div>
        </div>
    </div>
</div>

<!-- Attendance chart -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-transparent border-bottom">
                <h4 class="card-title mb-0">Attendance Overview</h4>
                <div class="btn-group btn-group-sm" role="group" id="attendance-chart-filter">
                    <input type="radio" class="btn-check" name="chartPeriod" id="chartPeriodWeekly" value="weekly" autocomplete="off">
                    <label class="btn btn-outline-primary" for="chartPeriodWeekly">Weekly</label>
                    <input type="radio" class="btn-check" name="chartPeriod" id="chartPeriodMonthly" value="monthly" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="chartPeriodMonthly">Monthly</label>
                    <input type="radio" class="btn-check" name="chartPeriod" id="chartPeriodYearly" value="yearly" autocomplete="off">
                    <label class="btn btn-outline-primary" for="chartPeriodYearly">Yearly</label>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 350px;">
                    <canvas id="attendanceOverviewChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance by Unit -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h4 class="card-title mb-0"><i class="bx bx-group me-2 text-primary"></i>Attendance Summary by Unit</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Unit Name</th>
                                <th class="text-center">Total Present</th>
                                <th class="text-center">Total Absent</th>
                                <th class="text-center">Avg. Attendance</th>
                                <th class="text-center">Meetings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unitSummaries ?? [] as $unit): ?>
                                <?php 
                                    $meetings = (int)$unit['services_counted'];
                                    $avg = $meetings > 0 ? round($unit['total_present'] / $meetings, 1) : 0;
                                ?>
                                <tr>
                                    <td>
                                        <a href="<?= AssetHelper::url("churches/{$churchId}/performance/{$unit['unit_id']}") ?>" class="text-primary fw-bold">
                                            <?= htmlspecialchars($unit['unit_name']) ?>
                                        </a>
                                    </td>
                                    <td class="text-center text-success fw-bold"><?= number_format($unit['total_present']) ?></td>
                                    <td class="text-center text-muted"><?= number_format($unit['total_absent']) ?></td>
                                    <td class="text-center text-primary fw-bold"><?= $avg ?></td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-soft-info text-info"><?= $meetings ?> services</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($unitSummaries)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No unit attendance data found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                <h4 class="card-title mb-0">Recent Services</h4>
                <a href="<?= AssetHelper::url("churches/{$churchId}/attendance/records") ?>" class="btn btn-sm btn-link">View all records</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-nowrap align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Event Type</th>
                                <th>Scope</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $svc): ?>
                                <?php
                                $present = (int)($svc['present_count'] ?? 0);
                                $absent = (int)($svc['absent_count'] ?? 0);
                                $unitId = $svc['unit_id'] ?? null;
                                $scopeLabel = ($unitId === null) ? 'All church' : ($svc['unit_name'] ?? 'Unit #' . (int)$unitId);
                                $serviceUrl = AssetHelper::url("churches/{$churchId}/attendance/service") . '?event_date=' . rawurlencode($svc['event_date']) . '&event_type=' . rawurlencode($svc['event_type']);
                                if ($unitId !== null) {
                                    $serviceUrl .= '&unit_id=' . (int)$unitId;
                                }
                                ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($svc['event_date'])) ?></td>
                                    <td>
                                        <span class="badge rounded-pill bg-soft-info text-info font-size-12">
                                            <?= htmlspecialchars($eventTypeLabels[$svc['event_type']] ?? str_replace('_', ' ', $svc['event_type'] ?? '')) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($scopeLabel) ?></td>
                                    <td class="text-center">
                                        <span class="text-success fw-bold"><?= $present ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted"><?= $absent ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= $serviceUrl ?>" class="btn btn-sm btn-light" title="View Detail">
                                            <i class="bx bx-show align-middle"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($services)): ?>
                    <div class="text-center py-4">
                        <i class="bx bx-calendar-x display-4 text-muted mb-2"></i>
                        <p class="text-muted">No attendance records found for this church yet.</p>
                        <a href="<?= AssetHelper::url("churches/{$churchId}/attendance/mark") ?>" class="btn btn-primary btn-sm">Start Recording</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Attendance overview chart
    var chartDataUrl = "<?= AssetHelper::url("churches/{$churchId}/attendance/chart-data") ?>";
    var attendanceChart = null;

    function loadAttendanceChart(period) {
        $.ajax({
            url: chartDataUrl + "?period=" + encodeURIComponent(period),
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (res.success && res.data && res.data.length) {
                    var labels = res.data.map(function(d) { return d.label; });
                    var present = res.data.map(function(d) { return d.present; });
                    var absent = res.data.map(function(d) { return d.absent; });
                    var firstTimer = res.data.map(function(d) { return d.first_timer || 0; });
                    updateAttendanceChart(labels, present, absent, firstTimer);
                } else {
                    updateAttendanceChart([], [], [], []);
                }
            },
            error: function() {
                updateAttendanceChart([], [], [], []);
            }
        });
    }

    function updateAttendanceChart(labels, present, absent, firstTimer) {
        var ctx = document.getElementById('attendanceOverviewChart');
        if (!ctx) return;
        if (attendanceChart) attendanceChart.destroy();
        attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Present',
                        data: present,
                        backgroundColor: '#34c38f',
                        borderColor: '#34c38f',
                        borderWidth: 0,
                        borderRadius: 4
                    },
                    {
                        label: 'First Timers',
                        data: firstTimer,
                        backgroundColor: '#f1b44c',
                        borderColor: '#f1b44c',
                        borderWidth: 0,
                        borderRadius: 4
                    },
                    {
                        label: 'Absent',
                        data: absent,
                        backgroundColor: '#74788d',
                        borderColor: '#74788d',
                        borderWidth: 0,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, padding: 20 } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    $("#attendance-chart-filter input[name='chartPeriod']").on("change", function() {
        loadAttendanceChart($(this).val());
    });
    
    loadAttendanceChart("monthly");
});
</script>
