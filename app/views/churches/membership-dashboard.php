<?php
use App\Utilities\AssetHelper;

$church = $church ?? [];
$stats = $stats ?? [];
$members = $members ?? [];
$units = $units ?? [];
$filters = $filters ?? [];
$systemRoles = $systemRoles ?? [];
$current_page = (int)($current_page ?? 1);
$total_pages = (int)($total_pages ?? 0);
$total = (int)($total ?? 0);
$per_page = (int)($per_page ?? 20);

$engagementLabels = [
    'active' => 'Active',
    'partially_active' => 'Partially Active',
    'inactive' => 'Inactive',
];
$engagementBadgeClass = [
    'active' => 'bg-success',
    'partially_active' => 'bg-warning',
    'inactive' => 'bg-secondary',
];
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= htmlspecialchars($pageTitle ?? 'Membership Dashboard') ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('churches') ?>">Churches</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('churches/' . ($church['id'] ?? 0)) ?>"><?= htmlspecialchars($church['name'] ?? '') ?></a></li>
                    <li class="breadcrumb-item active">Membership</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <a href="<?= AssetHelper::url('churches/' . ($church['id'] ?? 0)) ?>" class="btn btn-soft-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i>Back to Church
        </a>
        <a href="<?= AssetHelper::url('users?church_id=' . (int)($church['id'] ?? 0)) ?>" class="btn btn-soft-primary btn-sm ms-2">
            <i class="bx bx-group me-1"></i>View All Members (Users)
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row">
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted font-size-13">Total Members</span>
                        <h4 class="mb-0"><?= (int)($stats['total_members'] ?? 0) ?></h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-primary-subtle rounded-circle">
                            <i class="bx bx-group font-size-20 text-primary"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card card-h-100 border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted font-size-13">Active</span>
                        <h4 class="mb-0 text-success"><?= (int)($stats['active_count'] ?? 0) ?></h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-success-subtle rounded-circle">
                            <i class="bx bx-check-circle font-size-20 text-success"></i>
                        </span>
                    </div>
                </div>
                <small class="text-muted">5+ attendances (90 days)</small>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card card-h-100 border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted font-size-13">Partially Active</span>
                        <h4 class="mb-0 text-warning"><?= (int)($stats['partially_active_count'] ?? 0) ?></h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-warning-subtle rounded-circle">
                            <i class="bx bx-time-five font-size-20 text-warning"></i>
                        </span>
                    </div>
                </div>
                <small class="text-muted">1–4 attendances</small>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card card-h-100 border-secondary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted font-size-13">Inactive</span>
                        <h4 class="mb-0 text-secondary"><?= (int)($stats['inactive_count'] ?? 0) ?></h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-secondary-subtle rounded-circle">
                            <i class="bx bx-user-x font-size-20 text-secondary"></i>
                        </span>
                    </div>
                </div>
                <small class="text-muted">0 attendances</small>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted font-size-13">Leaders</span>
                        <h4 class="mb-0"><?= (int)($stats['leaders_count'] ?? 0) ?></h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-info-subtle rounded-circle">
                            <i class="bx bx-star font-size-20 text-info"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted font-size-13">Unit Coordinators</span>
                        <h4 class="mb-0"><?= (int)($stats['unit_coordinators_count'] ?? 0) ?></h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-dark-subtle rounded-circle">
                            <i class="bx bx-user-pin font-size-20 text-dark"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Engagement Overview</h5>
            </div>
            <div class="card-body">
                <canvas id="engagementChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= AssetHelper::url('churches/' . ($church['id'] ?? 0) . '/membership') ?>" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Unit</label>
                        <select name="unit_id" class="form-select form-select-sm">
                            <option value="">All Units</option>
                            <?php foreach ($units as $u): ?>
                                <option value="<?= (int)($u['unit_id'] ?? 0) ?>" <?= (string)($filters['unit_id'] ?? '') === (string)($u['unit_id'] ?? '') ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['unit_name'] ?? $u['unit_id'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Engagement</label>
                        <select name="engagement" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="active" <?= ($filters['engagement'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="partially_active" <?= ($filters['engagement'] ?? '') === 'partially_active' ? 'selected' : '' ?>>Partially Active</option>
                            <option value="inactive" <?= ($filters['engagement'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">System Role</label>
                        <select name="role" class="form-select form-select-sm">
                            <option value="">All Roles</option>
                            <?php foreach ($systemRoles as $key => $label): ?>
                                <option value="<?= htmlspecialchars($key) ?>" <?= ($filters['role'] ?? '') === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Name or email" value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-1">
                        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                        <a href="<?= AssetHelper::url('churches/' . ($church['id'] ?? 0) . '/membership') ?>" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Members</h5>
                <span class="text-muted font-size-13"><?= $total ?> total</span>
            </div>
            <div class="card-body">
                <?php if (empty($members)): ?>
                    <p class="text-muted mb-0">No members match the current filters.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Units</th>
                                    <th>Engagement</th>
                                    <th>System Role</th>
                                    <th style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $m): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars(trim(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? ''))) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($m['email'] ?? '') ?></td>
                                        <td class="font-size-13"><?= htmlspecialchars($m['units_display'] ?? '—') ?></td>
                                        <td>
                                            <?php
                                            $band = $m['engagement_band'] ?? 'inactive';
                                            $class = $engagementBadgeClass[$band] ?? 'bg-secondary';
                                            $label = $engagementLabels[$band] ?? ucfirst($band);
                                            ?>
                                            <span class="badge <?= $class ?>"><?= htmlspecialchars($label) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info"><?= htmlspecialchars($systemRoles[$m['system_role'] ?? 'user'] ?? ucfirst($m['system_role'] ?? 'User')) ?></span>
                                        </td>
                                        <td>
                                            <a href="<?= AssetHelper::url('users/' . (int)($m['id'] ?? 0)) ?>" class="btn btn-sm btn-soft-primary" title="View profile">
                                                <i class="bx bx-user"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($total_pages > 1): ?>
                        <nav class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="text-muted font-size-13">
                                Page <?= $current_page ?> of <?= $total_pages ?>
                            </div>
                            <ul class="pagination pagination-rounded mb-0">
                                <?php if ($current_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= AssetHelper::url('churches/' . ($church['id'] ?? 0) . '/membership?' . http_build_query(array_merge($filters, ['page' => $current_page - 1]))) ?>">Prev</a>
                                    </li>
                                <?php endif; ?>
                                <?php for ($p = max(1, $current_page - 2); $p <= min($total_pages, $current_page + 2); $p++): ?>
                                    <li class="page-item <?= $p === $current_page ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= AssetHelper::url('churches/' . ($church['id'] ?? 0) . '/membership?' . http_build_query(array_merge($filters, ['page' => $p]))) ?>"><?= $p ?></a>
                                    </li>
                                <?php endfor; ?>
                                <?php if ($current_page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= AssetHelper::url('churches/' . ($church['id'] ?? 0) . '/membership?' . http_build_query(array_merge($filters, ['page' => $current_page + 1]))) ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$chartActive = (int)($stats['active_count'] ?? 0);
$chartPartial = (int)($stats['partially_active_count'] ?? 0);
$chartInactive = (int)($stats['inactive_count'] ?? 0);
$pageJs = <<<JS
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('engagementChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Partially Active', 'Inactive'],
                datasets: [{
                    data: [{$chartActive}, {$chartPartial}, {$chartInactive}],
                    backgroundColor: ['rgba(40, 199, 111, 0.8)', 'rgba(255, 193, 7, 0.8)', 'rgba(108, 117, 125, 0.8)'],
                    borderColor: ['rgb(40, 199, 111)', 'rgb(255, 193, 7)', 'rgb(108, 117, 125)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>
JS;
?>
