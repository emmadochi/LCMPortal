<?php
use App\Core\Session;
use App\Models\Unit;
use App\Models\User;
use App\Utilities\AssetHelper;

$session = Session::getInstance();
$userRole = $session->get('user_role', 'user');

// Use data passed from controller
$totalUnits = $totalUnits ?? 0;
$totalUsers = $totalUsers ?? 0;
$totalReports = $totalReports ?? 0;
$totalAttendance = $totalAttendance ?? 0;
$recentUnits = $recentUnits ?? [];
$reportsByMonth = $reportsByMonth ?? [];
$attendanceByMonth = $attendanceByMonth ?? [];
$financeSummary = $financeSummary ?? ['income' => 0, 'expense' => 0];
$financeByMonth = $financeByMonth ?? [];
$recentActivityLogs = $recentActivityLogs ?? [];
$myFollowUps = $myFollowUps ?? [];
?>

<div class="row">
    <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-h-100">
            <!-- card body -->
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-6">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Units</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $totalUnits ?>">0</span>
                        </h4>
                    </div>
                    <div class="col-6">
                        <div class="text-end">
                            <i data-feather="users" class="icon-lg text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="text-nowrap">
                    <span class="badge bg-success-subtle text-success">Active</span>
                    <span class="ms-1 text-muted font-size-13">Units</span>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-h-100">
            <!-- card body -->
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-6">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Users</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $totalUsers ?>">0</span>
                        </h4>
                    </div>
                    <div class="col-6">
                        <div class="text-end">
                            <i data-feather="user" class="icon-lg text-info"></i>
                        </div>
                    </div>
                </div>
                <div class="text-nowrap">
                    <span class="badge bg-info-subtle text-info">Active</span>
                    <span class="ms-1 text-muted font-size-13">Users</span>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col-->

    <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-h-100">
            <!-- card body -->
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-6">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Reports</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $totalReports ?>">0</span>
                        </h4>
                    </div>
                    <div class="col-6">
                        <div class="text-end">
                            <i data-feather="file-text" class="icon-lg text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="text-nowrap">
                    <span class="badge bg-success-subtle text-success">Submitted</span>
                    <span class="ms-1 text-muted font-size-13">Reports</span>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-h-100">
            <!-- card body -->
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-6">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Attendance Records</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $totalAttendance ?>">0</span>
                        </h4>
                    </div>
                    <div class="col-6">
                        <div class="text-end">
                            <i data-feather="calendar" class="icon-lg text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="text-nowrap">
                    <span class="badge bg-warning-subtle text-warning">Records</span>
                    <span class="ms-1 text-muted font-size-13">This Month</span>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->    
</div><!-- end row-->

<div class="row">
    <!-- Charts Row -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Reports Over Time</h4>
            </div>
            <div class="card-body">
                <canvas id="reportsChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Finance Summary</h4>
            </div>
            <div class="card-body">
                <canvas id="financeChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Attendance Trends</h4>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Recent Units</h4>
            </div>
            <div class="card-body">
                <?php if (empty($recentUnits)): ?>
                    <p class="text-muted mb-0">No units created yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle mb-0">
                            <tbody>
                                <?php foreach ($recentUnits as $unit): ?>
                                    <tr>
                                        <td>
                                            <h5 class="font-size-14 mb-1">
                                                <a href="<?= AssetHelper::url('units/' . $unit['id']) ?>" class="text-dark">
                                                    <?= htmlspecialchars($unit['name']) ?>
                                                </a>
                                            </h5>
                                            <p class="text-muted mb-0 font-size-12">
                                                <?= date('M d, Y', strtotime($unit['created_at'])) ?>
                                            </p>
                                        </td>
                                        <td>
                                            <?php if ($unit['status'] === 'active'): ?>
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                            <?php endif; ?>
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

<!-- Follow-ups assigned to you -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Follow-ups assigned to you</h4>
                <?php if (!empty($myFollowUps)): ?>
                <a href="<?= AssetHelper::url('follow-ups') ?>" class="btn btn-sm btn-outline-primary">View all</a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php $myFollowUps = $myFollowUps ?? []; ?>
                <?php if (empty($myFollowUps)): ?>
                    <p class="text-muted mb-0">No follow-ups assigned to you.</p>
                    <a href="<?= AssetHelper::url('follow-ups') ?>" class="btn btn-sm btn-primary mt-2">Go to Follow-ups</a>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-nowrap align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Type</th>
                                    <th>Due date</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myFollowUps as $f): ?>
                                <tr class="<?= ($f['status'] ?? '') === 'overdue' ? 'table-danger' : '' ?>">
                                    <td>
                                        <strong><?= htmlspecialchars(($f['first_name'] ?? '') . ' ' . ($f['last_name'] ?? '')) ?></strong>
                                        <br><small class="text-muted"><?= htmlspecialchars($f['email'] ?? '') ?></small>
                                    </td>
                                    <td><?= ucfirst(str_replace('_', ' ', $f['type'] ?? '')) ?></td>
                                    <td><?= !empty($f['due_date']) ? date('M j, Y', strtotime($f['due_date'])) : '—' ?></td>
                                    <td>
                                        <?php
                                        $p = $f['priority'] ?? 'medium';
                                        $pClass = $p === 'urgent' ? 'danger' : ($p === 'high' ? 'warning' : ($p === 'medium' ? 'primary' : 'secondary'));
                                        ?>
                                        <span class="badge bg-<?= $pClass ?>-subtle text-<?= $pClass ?>"><?= ucfirst($p) ?></span>
                                    </td>
                                    <td>
                                        <?php $st = $f['status'] ?? 'pending'; ?>
                                        <span class="badge bg-<?= $st === 'overdue' ? 'danger' : 'warning' ?>-subtle text-<?= $st === 'overdue' ? 'danger' : 'warning' ?>"><?= ucfirst($st) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= AssetHelper::url('follow-ups/' . (int)($f['id'] ?? 0)) ?>" class="btn btn-sm btn-primary">View details</a>
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

<!-- Recent Activity Log -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Recent Activity</h4>
                <?php if ($userRole === 'admin'): ?>
                <a href="<?= AssetHelper::url('activity-logs') ?>" class="btn btn-sm btn-outline-primary">
                    <i data-feather="activity" class="icon-sm me-1"></i> View all logs
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php
                $recentActivityLogs = $recentActivityLogs ?? [];
                $actionBadges = ['create' => 'success', 'update' => 'primary', 'delete' => 'danger', 'login' => 'info', 'logout' => 'secondary', 'assign' => 'warning', 'remove' => 'dark'];
                ?>
                <?php if (empty($recentActivityLogs)): ?>
                    <p class="text-muted mb-0">No recent activity.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle mb-0 table-sm">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivityLogs as $log): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars(trim(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? ''))) ?: htmlspecialchars($log['email'] ?? '—') ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $actionBadges[$log['action']] ?? 'secondary' ?>">
                                                <?= ucfirst(htmlspecialchars($log['action'])) ?>
                                            </span>
                                            <?php if (!empty($log['model_type'])): ?>
                                                <span class="badge bg-info-subtle text-info ms-1"><?= htmlspecialchars($log['model_type']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($log['description'] ?? '') ?></td>
                                        <td><small class="text-muted"><?= date('M d, H:i', strtotime($log['created_at'])) ?></small></td>
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

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Quick Actions</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="<?= AssetHelper::url('units') ?>" class="btn btn-primary w-100 waves-effect waves-light">
                            <i data-feather="users" class="me-2"></i> Manage Units
                        </a>
                    </div>
                    <?php if ($userRole === 'admin'): ?>
                    <div class="col-md-4 mb-3">
                        <a href="<?= AssetHelper::url('users') ?>" class="btn btn-info w-100 waves-effect waves-light">
                            <i data-feather="user" class="me-2"></i> Manage Users
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-4 mb-3">
                        <a href="<?= AssetHelper::url('reports/create') ?>" class="btn btn-success w-100 waves-effect waves-light">
                            <i data-feather="file-text" class="me-2"></i> Create Report
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="<?= AssetHelper::url('attendance/create') ?>" class="btn btn-warning w-100 waves-effect waves-light">
                            <i data-feather="calendar" class="me-2"></i> Record Attendance
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="<?= AssetHelper::url('media') ?>" class="btn btn-secondary w-100 waves-effect waves-light">
                            <i data-feather="image" class="me-2"></i> Media Library
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="<?= AssetHelper::url('projects') ?>" class="btn btn-dark w-100 waves-effect waves-light">
                            <i data-feather="briefcase" class="me-2"></i> Projects
                        </a>
                    </div>
                    <?php if ($userRole === 'admin' || $userRole === 'director' || (isset($session) && $session->isHeadPastor())): ?>
                    <div class="col-md-4 mb-3">
                        <a href="<?= AssetHelper::url('follow-ups') ?>" class="btn btn-outline-primary w-100 waves-effect waves-light">
                            <i data-feather="clipboard" class="me-2"></i> Follow-ups
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="<?= AssetHelper::url('members') ?>" class="btn btn-outline-info w-100 waves-effect waves-light">
                            <i data-feather="user-check" class="me-2"></i> Member Directory
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Prepare chart data
$reportsLabels = json_encode(array_column($reportsByMonth, 'month'));
$reportsData = json_encode(array_column($reportsByMonth, 'count'));

$attendanceLabels = json_encode(array_column($attendanceByMonth, 'month'));
$attendanceData = json_encode(array_column($attendanceByMonth, 'count'));

$financeIncome = json_encode(array_column(array_map(function($m) { return ['month' => $m, 'value' => ($financeByMonth[$m]['income'] ?? 0)]; }, array_keys($financeByMonth)), 'value'));
$financeExpense = json_encode(array_column(array_map(function($m) { return ['month' => $m, 'value' => ($financeByMonth[$m]['expense'] ?? 0)]; }, array_keys($financeByMonth)), 'value'));
$financeLabels = json_encode(array_keys($financeByMonth));

$financeSummaryIncome = $financeSummary['income'] ?? 0;
$financeSummaryExpense = $financeSummary['expense'] ?? 0;

// Add counter animation and charts script
$pageJs = <<<JS
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Counter animation
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.counter-value');
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current);
                }
            }, 30);
        });
        
        // Reports Chart
        const reportsCtx = document.getElementById('reportsChart');
        if (reportsCtx) {
            new Chart(reportsCtx, {
                type: 'line',
                data: {
                    labels: {$reportsLabels},
                    datasets: [{
                        label: 'Reports',
                        data: {$reportsData},
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart');
        if (attendanceCtx) {
            new Chart(attendanceCtx, {
                type: 'bar',
                data: {
                    labels: {$attendanceLabels},
                    datasets: [{
                        label: 'Attendance Records',
                        data: {$attendanceData},
                        backgroundColor: 'rgba(255, 159, 64, 0.6)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Finance Chart (Pie)
        const financeCtx = document.getElementById('financeChart');
        if (financeCtx) {
            new Chart(financeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Income', 'Expense'],
                    datasets: [{
                        data: [{$financeSummaryIncome}, {$financeSummaryExpense}],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(255, 99, 132, 0.6)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });
</script>
JS;
?>
