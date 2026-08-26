<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$summary = $summary ?? ['total' => 0, 'active' => 0, 'completed' => 0, 'planning' => 0, 'total_budget' => 0];
?>

<div class="row">
    <div class="col-12">
        <div class="card bg-primary text-white mb-4 shadow-sm border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h3 class="text-white mb-2">Projects & Events Dashboard</h3>
                        <p class="mb-0 text-white-50">Managing mission-critical initiatives and church events for <?= htmlspecialchars($church['name'] ?? 'My Church') ?></p>
                    </div>
                    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                        <div class="d-flex gap-2 justify-content-sm-end">
                            <a href="<?= AssetHelper::url("churches/{$churchId}/projects/create") ?>" class="btn btn-success">
                                <i class="bx bx-plus me-1"></i> New Project
                            </a>
                            <a href="<?= AssetHelper::url("churches/{$churchId}/projects/records") ?>" class="btn btn-light shadow-sm">
                                <i class="bx bx-list-ul me-1"></i> Full List
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
        <div class="card mini-stats-wid shadow-sm border-0">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-24">
                        <i class="bx bx-briefcase-alt-2"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">Total Projects</h5>
                <h3 class="mb-0"><?= number_format($summary['total']) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-info text-info font-size-24">
                        <i class="bx bx-loader-circle bx-spin-hover"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">Active</h5>
                <h3 class="mb-0 text-info"><?= number_format($summary['active']) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-success text-success font-size-24">
                        <i class="bx bx-check-double"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">Completed</h5>
                <h3 class="mb-0 text-success"><?= number_format($summary['completed']) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-warning text-warning font-size-24">
                        <i class="bx bx-dollar-circle"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">Total Budget</h5>
                <h3 class="mb-0 text-warning">₦<?= number_format($summary['total_budget'], 2) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Status Distribution Chart -->
    <div class="col-lg-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom">
                <h4 class="card-title mb-0">Status Distribution</h4>
            </div>
            <div class="card-body d-flex align-items-center">
                <div class="chart-container w-100" style="position: relative; height: 250px;">
                    <canvas id="statusDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Projects -->
    <div class="col-lg-8">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                <h4 class="card-title mb-0">Recent Projects</h4>
                <a href="<?= AssetHelper::url("churches/{$churchId}/projects/records") ?>" class="btn btn-sm btn-link">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>Project Name</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th class="text-end">Budget</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects ?? [] as $p): ?>
                                <tr>
                                    <td>
                                        <h5 class="text-truncate font-size-14 mb-1">
                                            <a href="<?= AssetHelper::url("churches/{$churchId}/projects/{$p['id']}") ?>" class="text-dark"><?= htmlspecialchars($p['title']) ?></a>
                                        </h5>
                                        <p class="text-muted mb-0 font-size-12">Started: <?= date('M d, Y', strtotime($p['start_date'])) ?></p>
                                    </td>
                                    <td>
                                        <?php 
                                            $priorityClass = 'bg-secondary';
                                            if ($p['priority'] === 'urgent') $priorityClass = 'bg-danger';
                                            elseif ($p['priority'] === 'high') $priorityClass = 'bg-warning text-dark';
                                            elseif ($p['priority'] === 'medium') $priorityClass = 'bg-info';
                                        ?>
                                        <span class="badge <?= $priorityClass ?> font-size-11"><?= ucfirst($p['priority']) ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                            $statusClass = 'bg-soft-secondary text-secondary';
                                            if ($p['status'] === 'in_progress') $statusClass = 'bg-soft-primary text-primary';
                                            elseif ($p['status'] === 'completed') $statusClass = 'bg-soft-success text-success';
                                            elseif ($p['status'] === 'on_hold') $statusClass = 'bg-soft-warning text-warning';
                                        ?>
                                        <span class="badge rounded-pill <?= $statusClass ?> font-size-11">
                                            <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">
                                        ₦<?= number_format($p['budget'] ?? 0, 2) ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= AssetHelper::url("churches/{$churchId}/projects/{$p['id']}") ?>" class="btn btn-sm btn-light btn-rounded">
                                            <i class="bx bx-show-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($projects)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No recent projects found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Projects by Unit -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h4 class="card-title mb-0"><i class="bx bx-pie-chart-alt-2 me-2 text-primary"></i>Projects by Unit</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($unitSummaries ?? [] as $unit): ?>
                        <div class="col-md-3 mb-3">
                            <div class="card border shadow-none mb-0 h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-xs me-3">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-18">
                                                <i class="bx bx-collection"></i>
                                            </span>
                                        </div>
                                        <h5 class="font-size-14 mb-0 text-truncate">
                                            <a href="<?= AssetHelper::url("churches/{$churchId}/performance/{$unit['unit_id']}") ?>" class="text-primary">
                                                <?= htmlspecialchars($unit['unit_name']) ?>
                                            </a>
                                        </h5>
                                    </div>
                                    <div class="text-muted">
                                        <h4><?= number_format($unit['project_count']) ?> <span class="font-size-13">Projects</span></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctxDistribution = document.getElementById('statusDistributionChart');
    if (ctxDistribution) {
        new Chart(ctxDistribution, {
            type: 'doughnut',
            data: {
                labels: ['Planning', 'In Progress', 'On Hold', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [
                        <?= $summary['planning'] ?>,
                        <?= $summary['active'] ?>, // simplifying for chart
                        0, // need more granular summary if we want exact match
                        <?= $summary['completed'] ?>,
                        0
                    ],
                    backgroundColor: ['#f8b425', '#556ee6', '#f1b44c', '#34c38f', '#f46a6a'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 15, font: { size: 11 } }
                    }
                },
                cutout: '70%'
            }
        });
    }
});
</script>
