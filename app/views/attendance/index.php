<?php
use App\Utilities\AssetHelper;
use App\Models\Attendance;

$churchFilter = $churchFilter ?? null;
$services = $services ?? [];
$eventTypeLabels = Attendance::getEventTypes();
?>
<?php if (!empty($churchFilter)): ?>
<div class="alert alert-info d-flex align-items-center justify-content-between mb-3" role="alert">
    <span><i class="bx bx-church me-2"></i>Viewing church: <strong><?= htmlspecialchars($churchFilter['name']) ?></strong></span>
    <a href="<?= AssetHelper::url('attendance') ?>" class="btn btn-sm btn-outline-primary">View all</a>
</div>
<?php endif; ?>

<!-- Attendance chart: weekly / monthly / yearly (AJAX) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
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
                <div class="chart-container" style="position: relative; height: 320px;">
                    <canvas id="attendanceOverviewChart"></canvas>
                </div>
                <p class="text-muted small mb-0 mt-2">Present, first timers (first time at church), and absent by period. Change filter to load data via AJAX.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Attendance by Service</h4>
                    <p class="card-title-desc mb-0">View attendance grouped by event or service; open a row to see who was present or absent.</p>
                </div>
                <div class="d-flex gap-2">
                    <?php
                    $exportBaseUrl = AssetHelper::url('attendance/export');
                    $exportQuery = $_GET ?? [];
                    unset($exportQuery['page']);
                    $queryString = http_build_query($exportQuery);
                    $queryPrefix = $queryString ? ($queryString . '&') : '';
                    ?>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="download" class="me-1"></i> Export
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="<?= $exportBaseUrl . '?' . $queryPrefix . 'format=csv' ?>">
                                CSV
                            </a>
                            <a class="dropdown-item" href="<?= $exportBaseUrl . '?' . $queryPrefix . 'format=excel' ?>">
                                Excel (.xls)
                            </a>
                            <a class="dropdown-item" href="<?= $exportBaseUrl . '?' . $queryPrefix . 'format=json' ?>">
                                JSON
                            </a>
                            <a class="dropdown-item" href="<?= $exportBaseUrl . '?' . $queryPrefix . 'format=pdf' ?>">
                                PDF
                            </a>
                        </div>
                    </div>
                    <a href="<?= AssetHelper::url('attendance/mark' . (!empty($churchFilter) ? '?church_id=' . (int)$churchFilter['id'] : '')) ?>" class="btn btn-success">
                        <i data-feather="list-check" class="me-1"></i> Mark (roll-call)
                    </a>
                    <a href="<?= AssetHelper::url('attendance/create') ?>" class="btn btn-primary">
                        <i data-feather="calendar-plus" class="me-1"></i> Record single
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="attendance-datatable" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Event type</th>
                            <th>Unit / scope</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Absent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $svc): ?>
                            <?php
                            $present = (int)($svc['present_count'] ?? 0);
                            $absent = (int)($svc['absent_count'] ?? 0);
                            $unitId = $svc['unit_id'] ?? null;
                            $churchId = $svc['church_id'] ?? null;
                            $scopeLabel = '';
                            $serviceUrl = AssetHelper::url('attendance/service') . '?event_date=' . rawurlencode($svc['event_date']) . '&event_type=' . rawurlencode($svc['event_type']);
                            if ($churchId && ($unitId === null || $unitId === '')) {
                                $scopeLabel = ($svc['church_name'] ?? 'Church') . ' (All church)';
                                $serviceUrl .= '&church_id=' . (int)$churchId;
                            } else {
                                $scopeLabel = $svc['unit_name'] ?? 'Unit #' . (int)$unitId;
                                $serviceUrl .= '&unit_id=' . (int)$unitId;
                            }
                            ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($svc['event_date'])) ?></td>
                                <td>
                                    <span class="badge bg-info"><?= htmlspecialchars($eventTypeLabels[$svc['event_type']] ?? str_replace('_', ' ', $svc['event_type'] ?? '')) ?></span>
                                    <?php if (!empty($svc['service_description'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($svc['service_description']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($scopeLabel) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?= $present ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $absent ?></span>
                                </td>
                                <td>
                                    <a href="<?= $serviceUrl ?>" class="btn btn-sm btn-outline-primary" title="View service detail">
                                        <i data-feather="eye" class="icon-sm"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (empty($services)): ?>
                    <p class="text-muted mb-0">No attendance records yet. Use <strong>Mark (roll-call)</strong> or <strong>Record single</strong> to add records.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$extraCss = [
    'libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
    'libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'
];

$extraJs = [
    'libs/datatables.net/js/jquery.dataTables.min.js',
    'libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
    'libs/datatables.net-responsive/js/dataTables.responsive.min.js',
    'libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js',
    'pages/datatables.init.js'
];

$chartDataUrl = AssetHelper::url('attendance/chart-data');
$chartChurchId = !empty($churchFilter['id']) ? (int)$churchFilter['id'] : 0;
$pageJs = <<<JS
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    $(document).ready(function() {
        $('#attendance-datatable').DataTable({
            responsive: true,
            order: [[0, 'desc']],
            pageLength: 25,
            language: {
                search: "",
                searchPlaceholder: "Search services..."
            }
        });

        // Attendance overview chart (AJAX: weekly / monthly / yearly)
        var chartDataUrl = "{$chartDataUrl}";
        var chartChurchId = {$chartChurchId};
        var attendanceChart = null;

        function loadAttendanceChart(period) {
            var url = chartDataUrl + "?period=" + encodeURIComponent(period);
            if (chartChurchId > 0) url += "&church_id=" + chartChurchId;
            $.ajax({
                url: url,
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
            firstTimer = firstTimer || [];
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
                            backgroundColor: 'rgba(40, 199, 111, 0.7)',
                            borderColor: 'rgb(40, 199, 111)',
                            borderWidth: 1
                        },
                        {
                            label: 'First timers',
                            data: firstTimer,
                            backgroundColor: 'rgba(255, 193, 7, 0.8)',
                            borderColor: 'rgb(220, 165, 0)',
                            borderWidth: 1
                        },
                        {
                            label: 'Absent',
                            data: absent,
                            backgroundColor: 'rgba(108, 117, 125, 0.7)',
                            borderColor: 'rgb(108, 117, 125)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true },
                        x: { stacked: false }
                    }
                }
            });
        }

        $("#attendance-chart-filter input[name='chartPeriod']").on("change", function() {
            var period = $(this).val();
            loadAttendanceChart(period);
        });
        loadAttendanceChart("monthly");
    });
</script>
JS;
?>
