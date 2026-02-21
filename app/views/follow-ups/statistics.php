<?php
use App\Utilities\AssetHelper;

$stats = $stats ?? [];
$totalPending = (int)($stats['total_pending'] ?? 0);
$totalOverdue = (int)($stats['total_overdue'] ?? 0);
$totalCompleted = (int)($stats['total_completed'] ?? 0);
$total = $totalPending + $totalOverdue + $totalCompleted;
$byPriority = $stats['by_priority'] ?? [];
$byType = $stats['by_type'] ?? [];
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Follow-up Statistics</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('follow-ups') ?>">Follow-ups</a></li>
                    <li class="breadcrumb-item active">Statistics</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0">Overview</h4>
                    <a href="<?= AssetHelper::url('follow-ups') ?>" class="btn btn-outline-primary btn-sm">
                        <i data-feather="list" class="me-1"></i> Back to Follow-ups
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-primary mb-0">
                            <div class="card-body text-center">
                                <h3 class="text-primary mb-1"><?= $totalPending ?></h3>
                                <p class="text-muted mb-0">Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning mb-0">
                            <div class="card-body text-center">
                                <h3 class="text-warning mb-1"><?= $totalOverdue ?></h3>
                                <p class="text-muted mb-0">Overdue</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success mb-0">
                            <div class="card-body text-center">
                                <h3 class="text-success mb-1"><?= $totalCompleted ?></h3>
                                <p class="text-muted mb-0">Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info mb-0">
                            <div class="card-body text-center">
                                <h3 class="text-info mb-1"><?= $total ?></h3>
                                <p class="text-muted mb-0">Total</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card border mb-0">
                            <div class="card-header">
                                <h5 class="card-title mb-0">By Priority</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $priorityLabels = ['urgent' => 'Urgent', 'high' => 'High', 'medium' => 'Medium', 'low' => 'Low'];
                                $priorityColors = ['urgent' => 'danger', 'high' => 'warning', 'medium' => 'primary', 'low' => 'secondary'];
                                foreach ($priorityLabels as $key => $label):
                                    $count = (int)($byPriority[$key] ?? 0);
                                    $pct = $total > 0 ? round($count / $total * 100) : 0;
                                ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-<?= $priorityColors[$key] ?>-subtle text-<?= $priorityColors[$key] ?>"><?= $label ?></span>
                                    <span><?= $count ?> <small class="text-muted">(<?= $pct ?>%)</small></span>
                                </div>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-<?= $priorityColors[$key] ?>" role="progressbar" style="width: <?= $pct ?>%"></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border mb-0">
                            <div class="card-header">
                                <h5 class="card-title mb-0">By Type</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $typeLabels = [
                                    'new_convert' => 'New Convert',
                                    'prayer_request' => 'Prayer Request',
                                    'counseling' => 'Counseling',
                                    'visitation' => 'Visitation',
                                    'general' => 'General'
                                ];
                                foreach ($typeLabels as $key => $label):
                                    $count = (int)($byType[$key] ?? 0);
                                    $pct = $total > 0 ? round($count / $total * 100) : 0;
                                ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><?= $label ?></span>
                                    <span><?= $count ?> <small class="text-muted">(<?= $pct ?>%)</small></span>
                                </div>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?= $pct ?>%"></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
