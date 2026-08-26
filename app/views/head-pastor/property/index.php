<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$statusCounts = $statusCounts ?? [];
$totalProperties = $totalProperties ?? 0;
?>

<div class="row">
    <div class="col-12">
        <div class="card bg-primary text-white mb-4 shadow-sm border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h3 class="text-white mb-2">Property Management</h3>
                        <p class="mb-0 text-white-50">Overseeing the physical assets and stewardship of <?= htmlspecialchars($church['name'] ?? 'My Church') ?></p>
                    </div>
                    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                        <div class="d-flex gap-2 justify-content-sm-end">
                            <a href="<?= AssetHelper::url("churches/{$churchId}/property/create") ?>" class="btn btn-success">
                                <i class="bx bx-plus me-1"></i> Register Asset
                            </a>
                            <a href="<?= AssetHelper::url("churches/{$churchId}/property/records") ?>" class="btn btn-light shadow-sm">
                                <i class="bx bx-list-ul me-1"></i> Inventory
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
                        <i class="bx bx-home-alt"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">Total Assets</h5>
                <h3 class="mb-0"><?= number_format($totalProperties) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-success text-success font-size-24">
                        <i class="bx bx-check-shield"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">In Use / Good</h5>
                <h3 class="mb-0 text-success"><?= number_format(($statusCounts['in_use'] ?? 0) + ($statusCounts['available'] ?? 0)) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-warning text-warning font-size-24">
                        <i class="bx bx-wrench"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">Maintenance</h5>
                <h3 class="mb-0 text-warning"><?= number_format($statusCounts['maintenance'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid shadow-sm border-0">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-danger text-danger font-size-24">
                        <i class="bx bx-error-alt"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">Damaged / Lost</h5>
                <h3 class="mb-0 text-danger"><?= number_format(($statusCounts['damaged'] ?? 0) + ($statusCounts['lost'] ?? 0)) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Category Breakdown Chart -->
    <div class="col-lg-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom">
                <h4 class="card-title mb-0">Asset Categories</h4>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <?php if (empty($categoryBreakdown)): ?>
                    <p class="text-muted text-center py-5">No category data found.</p>
                <?php else: ?>
                    <div class="chart-container w-100" style="position: relative; height: 250px;">
                        <canvas id="categoryDistributionChart"></canvas>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Assets -->
    <div class="col-lg-8">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                <h4 class="card-title mb-0">Property Inventory (Recent)</h4>
                <a href="<?= AssetHelper::url("churches/{$churchId}/property/records") ?>" class="btn btn-sm btn-link">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>Asset Name</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Location</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($properties ?? [] as $p): ?>
                                <tr>
                                    <td>
                                        <h5 class="text-truncate font-size-14 mb-1">
                                            <a href="<?= AssetHelper::url("churches/{$churchId}/property/{$p['id']}") ?>" class="text-dark"><?= htmlspecialchars($p['name']) ?></a>
                                        </h5>
                                        <p class="text-muted mb-0 font-size-12">Serial: <?= htmlspecialchars($p['serial_number'] ?: 'N/A') ?></p>
                                    </td>
                                    <td><?= htmlspecialchars($p['category_name']) ?></td>
                                    <td>
                                        <?php 
                                            $statusClass = 'bg-soft-secondary text-secondary';
                                            if ($p['status'] === 'available' || $p['status'] === 'in_use') $statusClass = 'bg-soft-success text-success';
                                            elseif ($p['status'] === 'maintenance') $statusClass = 'bg-soft-warning text-warning';
                                            elseif ($p['status'] === 'damaged' || $p['status'] === 'lost') $statusClass = 'bg-soft-danger text-danger';
                                        ?>
                                        <span class="badge rounded-pill <?= $statusClass ?> font-size-11">
                                            <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($p['location'] ?: 'Not Specified') ?></td>
                                    <td class="text-center">
                                        <a href="<?= AssetHelper::url("churches/{$churchId}/property/{$p['id']}") ?>" class="btn btn-sm btn-light btn-rounded">
                                            <i class="bx bx-show-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($properties)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No assets found in inventory.</td>
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
    <!-- Recent Activity Logs -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom">
                <h4 class="card-title mb-0">Recent Property Activity</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Asset</th>
                                <th>Action</th>
                                <th>Status Change</th>
                                <th>User</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentLogs ?? [] as $log): ?>
                                <tr>
                                    <td class="text-muted"><?= date('M d, H:i', strtotime($log['created_at'])) ?></td>
                                    <td class="fw-medium"><?= htmlspecialchars($log['property_name']) ?></td>
                                    <td>
                                        <span class="badge border border-dark text-dark"><?= ucfirst(str_replace('_', ' ', $log['action'])) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($log['old_status'] || $log['new_status']): ?>
                                            <small class="text-muted"><?= ucfirst($log['old_status']) ?></small>
                                            <i class="bx bx-right-arrow-alt mx-1"></i>
                                            <small class="fw-bold"><?= ucfirst($log['new_status']) ?></small>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?></td>
                                    <td class="text-truncate" style="max-width: 200px;"><?= htmlspecialchars($log['notes'] ?: '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentLogs)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">No recent activity logs found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctxCategory = document.getElementById('categoryDistributionChart');
    if (ctxCategory) {
        const categoryData = <?= json_encode($categoryBreakdown ?? []) ?>;
        const labels = Object.keys(categoryData);
        const data = Object.values(categoryData);

        new Chart(ctxCategory, {
            type: 'polarArea',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        'rgba(85, 110, 230, 0.7)',
                        'rgba(52, 195, 143, 0.7)',
                        'rgba(241, 180, 76, 0.7)',
                        'rgba(244, 106, 106, 0.7)',
                        'rgba(80, 165, 241, 0.7)'
                    ],
                    borderWidth: 1
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
                }
            }
        });
    }
});
</script>
