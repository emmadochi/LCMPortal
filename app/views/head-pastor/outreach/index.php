<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$summary = $summary ?? ['total' => 0, 'total_attendance' => 0, 'total_first_timers' => 0, 'total_budget' => 0, 'total_actual' => 0, 'efficiency' => 0];
?>

<div class="row">
    <div class="col-12">
        <div class="card bg-primary text-white mb-4 shadow-sm border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h3 class="text-white mb-2">Outreach & Events Dashboard</h3>
                        <p class="mb-0 text-white-50">Impact tracking and program oversight for <?= htmlspecialchars($church['name'] ?? 'My Church') ?></p>
                    </div>
                    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                        <div class="d-flex gap-2 justify-content-sm-end">
                            <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/create") ?>" class="btn btn-success">
                                <i class="bx bx-plus me-1"></i> New Report
                            </a>
                            <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/records") ?>" class="btn btn-light shadow-sm">
                                <i class="bx bx-list-ul me-1"></i> Full Records
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-24">
                        <i class="bx bx-map-pin"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2 font-size-13 text-uppercase">Total Programs</h5>
                <h3 class="mb-0"><?= number_format($summary['total']) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-info text-info font-size-24">
                        <i class="bx bx-group"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2 font-size-13 text-uppercase">Total Attendance</h5>
                <h3 class="mb-0 text-info"><?= number_format($summary['total_attendance']) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-success text-success font-size-24">
                        <i class="bx bx-user-plus"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2 font-size-13 text-uppercase">First Timers</h5>
                <h3 class="mb-0 text-success"><?= number_format($summary['total_first_timers']) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-warning text-warning font-size-24">
                        <i class="bx bx-trending-up"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2 font-size-13 text-uppercase">Budget Efficiency</h5>
                <h3 class="mb-0 text-warning"><?= $summary['efficiency'] ?>%</h3>
                <small class="text-muted">Actual vs Budgeted</small>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Cost Analysis Card -->
    <div class="col-lg-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom">
                <h4 class="card-title mb-0">Financial Summary</h4>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-center mb-4">
                    <div class="avatar-md mx-auto mb-3">
                        <span class="avatar-title rounded-circle bg-light text-primary font-size-24">
                            <i class="bx bx-money"></i>
                        </span>
                    </div>
                </div>
                <div class="row text-center mt-auto">
                    <div class="col-6 border-end">
                        <h5 class="font-size-15 text-muted mb-1">Budgeted</h5>
                        <p class="text-dark fw-bold mb-0">₦<?= number_format($summary['total_budget'], 2) ?></p>
                    </div>
                    <div class="col-6">
                        <h5 class="font-size-15 text-muted mb-1">Spent</h5>
                        <p class="text-primary fw-bold mb-0">₦<?= number_format($summary['total_actual'], 2) ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="progress animated-progess custom-progress mb-1">
                        <div class="progress-bar bg-primary" role="progressbar" 
                             style="width: <?= min($summary['efficiency'], 100) ?>%" 
                             aria-valuenow="<?= $summary['efficiency'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Outreach Programs -->
    <div class="col-lg-8">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                <h4 class="card-title mb-0">Recent Programs</h4>
                <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/records") ?>" class="btn btn-sm btn-link">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>Program Title</th>
                                <th>Date</th>
                                <th class="text-center">Attendance</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports ?? [] as $r): ?>
                                <tr>
                                    <td>
                                        <h5 class="text-truncate font-size-14 mb-1">
                                            <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/{$r['id']}") ?>" class="text-dark"><?= htmlspecialchars($r['title']) ?></a>
                                        </h5>
                                        <p class="text-muted mb-0 font-size-12">Unit: <?= htmlspecialchars($r['unit_name'] ?? 'General') ?></p>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($r['program_date'])) ?></td>
                                    <td class="text-center">
                                        <span class="fw-bold"><?= number_format($r['total_attendance'] ?? 0) ?></span>
                                        <br>
                                        <small class="text-muted"><?= number_format($r['first_timers_count'] ?? 0) ?> FT</small>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $statusClass = 'bg-soft-secondary text-secondary';
                                            if ($r['status'] === 'submitted') $statusClass = 'bg-soft-primary text-primary';
                                            elseif ($r['status'] === 'approved') $statusClass = 'bg-soft-success text-success';
                                        ?>
                                        <span class="badge rounded-pill <?= $statusClass ?> font-size-11">
                                            <?= ucfirst($r['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/{$r['id']}") ?>" class="btn btn-sm btn-light btn-rounded">
                                            <i class="bx bx-show-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($reports)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No outreach records found for this period.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
