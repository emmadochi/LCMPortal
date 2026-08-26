<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Unit Performance Dashboard</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Unit Performance</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white shadow-lg border-0 overflow-hidden">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h2 class="text-white mb-2">Church Health Overview</h2>
                        <p class="mb-0 text-white-50">Monitoring the operational performance and growth metrics of all units within <?= htmlspecialchars($church['name']) ?></p>
                    </div>
                    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                        <i class="bx bx-line-chart display-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Unit performance Matrix</h4>
                    <span class="badge bg-soft-info text-info rounded-pill px-3 py-2"><?= count($units) ?> Assigned Units</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Unit Name</th>
                                <th>Unit Head</th>
                                <th class="text-center">Membership</th>
                                <th class="text-center">Avg. Attendance</th>
                                <th class="text-center">Active Projects</th>
                                <th class="text-center">Financial Position</th>
                                <th class="text-center">Latest Report</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($units as $unit): ?>
                                <?php 
                                    $metrics = $unit['metrics']; 
                                    $balanceClass = $metrics['net_balance'] >= 0 ? 'text-success' : 'text-danger';
                                    $lastReport = $metrics['last_report_date'] ? date('M j, Y', strtotime($metrics['last_report_date'])) : '<span class="text-muted">No report</span>';
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <h5 class="font-size-14 mb-1 text-primary"><?= htmlspecialchars($unit['unit_name']) ?></h5>
                                        <span class="text-muted small">ID: #<?= (int)$unit['unit_id'] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    <?= substr($unit['unit_head_name'] ?? '?', 0, 1) ?>
                                                </span>
                                            </div>
                                            <span><?= htmlspecialchars($unit['unit_head_name'] ?: 'Not Assigned') ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold"><?= number_format($metrics['total_members']) ?></span>
                                        <div class="text-muted small">Members</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-success text-success font-size-12 px-2 py-1">
                                            <?= $metrics['avg_attendance'] ?> <i class="bx bx-group ms-1"></i>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-warning text-warning font-size-12 px-2 py-1">
                                            <?= $metrics['active_projects'] ?> <i class="bx bx-task ms-1"></i>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold <?= $balanceClass ?>">
                                            <?= $metrics['net_balance'] >= 0 ? '+' : '' ?>₦<?= number_format($metrics['net_balance'], 2) ?>
                                        </span>
                                    </td>
                                    <td class="text-center text-muted small">
                                        <?= $lastReport ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= AssetHelper::url("churches/{$church['id']}/performance/{$unit['unit_id']}") ?>" class="btn btn-sm btn-soft-primary btn-rounded waves-effect waves-light">
                                            <i class="bx bx-show-alt me-1"></i> Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: rgba(69, 101, 246, 0.03);
}
.card {
    border-radius: 0.75rem;
}
.badge-soft-success {
    background-color: rgba(52, 195, 143, 0.15);
    color: #34c38f;
}
.badge-soft-warning {
    background-color: rgba(241, 180, 76, 0.15);
    color: #f1b44c;
}
.badge-soft-info {
    background-color: rgba(80, 165, 241, 0.15);
    color: #50a5f1;
}
.avatar-xs {
    height: 1.5rem;
    width: 1.5rem;
}
</style>
