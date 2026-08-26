<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= htmlspecialchars($unit['name']) ?> Performance Profile</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url("churches/{$church['id']}/performance") ?>">Unit Performance</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($unit['name']) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Total Members</p>
                        <h4 class="mb-0 text-primary"><?= number_format($metrics['total_members']) ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-soft-primary mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-primary">
                                <i class="bx bx-group font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Avg. Attendance</p>
                        <h4 class="mb-0 text-success"><?= $metrics['avg_attendance'] ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-soft-success mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-success">
                                <i class="bx bx-calendar-check font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Active Projects</p>
                        <h4 class="mb-0 text-warning"><?= $metrics['active_projects'] ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-soft-warning mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-warning">
                                <i class="bx bx-task font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Net Balance</p>
                        <h4 class="mb-0 <?= $metrics['net_balance'] >= 0 ? 'text-success' : 'text-danger' ?>">
                            ₦<?= number_format($metrics['net_balance'], 2) ?>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-soft-info mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-info">
                                <i class="bx bx-wallet font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h4 class="card-title mb-0">Attendance Trend (Last 10 Records)</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Event Type</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Growth</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendanceHistory as $i => $att): ?>
                                <tr>
                                    <td><?= date('M j, Y', strtotime($att['event_date'])) ?></td>
                                    <td>
                                        <span class="badge rounded-pill bg-soft-info text-info font-size-11">
                                            <?= htmlspecialchars($att['event_type']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold"><?= (int)$att['present_count'] ?></td>
                                    <td class="text-center text-muted">-</td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($attendanceHistory)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No attendance data found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h4 class="card-title mb-0">Active Projects</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($activeProjects)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Project Name</th>
                                    <th>Start Date</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activeProjects as $project): ?>
                                    <tr>
                                        <td>
                                            <h5 class="font-size-13 mb-0"><?= htmlspecialchars($project['name']) ?></h5>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($project['created_at'])) ?></td>
                                        <td>
                                            <span class="badge bg-soft-warning text-warning">Medium</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Active</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bx bx-task text-muted display-4 mb-3"></i>
                        <p class="text-muted">No active projects for this unit.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h4 class="card-title mb-0">Recent Financial Transactions</h4>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($financeHistory as $tx): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <p class="mb-0 fw-bold"><?= htmlspecialchars($tx['title'] ?: $tx['transaction_type']) ?></p>
                                <span class="text-muted small"><?= date('M j, Y', strtotime($tx['transaction_date'])) ?></span>
                            </div>
                            <span class="<?= $tx['transaction_type'] === 'income' ? 'text-success' : 'text-danger' ?> fw-bold">
                                <?= $tx['transaction_type'] === 'income' ? '+' : '-' ?>₦<?= number_format($tx['amount'], 2) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($financeHistory)): ?>
                        <li class="list-group-item text-center py-4 text-muted">No recent transactions.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h4 class="card-title mb-0">Latest Narrative Reports</h4>
            </div>
            <div class="card-body">
                <?php foreach ($recentReports as $report): ?>
                    <div class="mb-3 border-bottom pb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="badge bg-soft-primary text-primary"><?= htmlspecialchars($report['type'] ?? 'General') ?></span>
                            <span class="text-muted small"><?= date('M j, Y', strtotime($report['created_at'])) ?></span>
                        </div>
                        <h6 class="mb-1"><?= htmlspecialchars($report['title']) ?></h6>
                        <a href="<?= AssetHelper::url("churches/{$church['id']}/unit-reports/{$report['id']}") ?>" class="text-primary small">Read more <i class="bx bx-right-arrow-alt"></i></a>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($recentReports)): ?>
                    <div class="text-center py-4 text-muted">No reports submitted.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm { height: 3rem; width: 3rem; }
.mini-stat-icon { position: absolute; right: 20px; top: 18px; }
.bg-soft-primary { background-color: rgba(85, 110, 230, 0.25); }
.bg-soft-success { background-color: rgba(52, 195, 143, 0.25); }
.bg-soft-warning { background-color: rgba(241, 180, 76, 0.25); }
.bg-soft-info { background-color: rgba(80, 165, 241, 0.25); }
.card { border-radius: 0.75rem; }
.list-group-item { border-bottom-style: dashed; }
</style>
