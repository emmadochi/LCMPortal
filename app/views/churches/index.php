<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Church Management</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Churches</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Statistics Cards -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-white-50 mb-1">Total Churches</p>
                        <h3 class="text-white mb-0"><?= $stats['total_churches'] ?></h3>
                    </div>
                    <div class="avatar-sm rounded-circle bg-soft-light align-self-center">
                        <span class="avatar-title rounded-circle bg-transparent">
                            <i class="bx bx-church font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-white-50 mb-1">Active Churches</p>
                        <h3 class="text-white mb-0"><?= $stats['by_status']['active'] ?? 0 ?></h3>
                    </div>
                    <div class="avatar-sm rounded-circle bg-soft-light align-self-center">
                        <span class="avatar-title rounded-circle bg-transparent">
                            <i class="bx bx-check-circle font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-white-50 mb-1">Headquarters</p>
                        <h3 class="text-white mb-0"><?= $stats['headquarters'] ?></h3>
                    </div>
                    <div class="avatar-sm rounded-circle bg-soft-light align-self-center">
                        <span class="avatar-title rounded-circle bg-transparent">
                            <i class="bx bx-home font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-white-50 mb-1">Total Units</p>
                        <h3 class="text-white mb-0"><?= array_sum(array_column($churches ?? [], 'unit_count')) ?></h3>
                    </div>
                    <div class="avatar-sm rounded-circle bg-soft-light align-self-center">
                        <span class="avatar-title rounded-circle bg-transparent">
                            <i class="bx bx-group font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">All Churches</h4>
                    <div>
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bx bx-export me-1"></i>Export
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?= AssetHelper::url('churches/export') ?>">
                                    <i class="bx bx-spreadsheet me-2"></i>Export All Churches (CSV)
                                </a>
                                <a class="dropdown-item" href="<?= AssetHelper::url('churches/export') ?>?<?= http_build_query(array_filter([
                                    'search' => $filters['search'] ?? '',
                                    'status' => $filters['status'] ?? '',
                                    'state' => $filters['state'] ?? ''
                                ])) ?>">
                                    <i class="bx bx-filter me-2"></i>Export Filtered Results (CSV)
                                </a>
                            </div>
                        </div>
                        <a href="<?= AssetHelper::url('churches/create') ?>" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Create Church
                        </a>
                    </div>
                </div>
                <p class="card-title-desc mb-0">Manage all church locations and branches</p>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="filter-section mb-4">
                    <h5 class="mb-3"><i class="bx bx-filter me-2"></i>Filter Churches</h5>
                    <form method="GET" action="<?= AssetHelper::url('churches') ?>" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Search Churches</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search by name, city, or state..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Statuses</option>
                                <?php foreach ($statuses as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= ($filters['status'] ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">State</label>
                            <input type="text" class="form-control" name="state" 
                                   placeholder="Filter by state..." value="<?= htmlspecialchars($filters['state'] ?? '') ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-filter me-1"></i>Filter
                                </button>
                                <?php if (!empty(array_filter($filters))): ?>
                                    <a href="<?= AssetHelper::url('churches') ?>" class="btn btn-outline-secondary">
                                        <i class="bx bx-x"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Churches Table -->
                <?php if (!empty($churches)): ?>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Church Name</th>
                                    <th>Location</th>
                                    <th>Members</th>
                                    <th>Details</th>
                                    <th>Status</th>
                                    <th>Units</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($churches as $church): ?>
                                    <tr>
                                        <td>
                                            <h5 class="font-size-14 mb-1">
                                                <a href="<?= AssetHelper::url("churches/{$church['id']}") ?>" class="text-dark">
                                                    <?= htmlspecialchars($church['name']) ?>
                                                </a>
                                                <?php if ($church['is_headquarters']): ?>
                                                    <span class="badge bg-primary ms-2">HQ</span>
                                                <?php endif; ?>
                                            </h5>
                                            <?php if ($church['description']): ?>
                                                <p class="text-muted mb-0"><?= htmlspecialchars(substr($church['description'], 0, 60)) ?><?= strlen($church['description']) > 60 ? '...' : '' ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <p class="mb-1">
                                                <i class="bx bx-map me-1 text-muted"></i>
                                                <?= htmlspecialchars($church['city']) ?>, <?= htmlspecialchars($church['state']) ?>
                                            </p>
                                            <?php if ($church['postal_code']): ?>
                                                <p class="mb-0 text-muted"><?= htmlspecialchars($church['postal_code']) ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-semibold text-primary">
                                                <i class="bx bx-group me-1"></i>
                                                <?= $church['member_count'] ?? 0 ?> <?= ($church['member_count'] ?? 0) === 1 ? 'member' : 'members' ?>
                                            </p>
                                        </td>
                                        <td>
                                            <?php if (!empty($church['pastor_name'])): ?>
                                                <p class="mb-0">
                                                    <i class="bx bx-user me-1 text-muted"></i>
                                                    <?= htmlspecialchars($church['pastor_name']) ?>
                                                </p>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClasses = [
                                                'active' => 'success',
                                                'inactive' => 'secondary',
                                                'suspended' => 'danger'
                                            ];
                                            $statusLabels = [
                                                'active' => 'Active',
                                                'inactive' => 'Inactive',
                                                'suspended' => 'Suspended'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $statusClasses[$church['status']] ?>">
                                                <?= $statusLabels[$church['status']] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= $church['unit_count'] ?? 0 ?> units</span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="<?= AssetHelper::url("churches/{$church['id']}") ?>">
                                                            <i class="bx bx-show me-2"></i>View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="<?= AssetHelper::url("churches/{$church['id']}/membership") ?>">
                                                            <i class="bx bx-pie-chart-alt me-2"></i>Membership Dashboard
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="<?= AssetHelper::url("churches/{$church['id']}/edit") ?>">
                                                            <i class="bx bx-edit me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    <?php if (!$church['is_headquarters'] && $church['unit_count'] == 0): ?>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="<?= AssetHelper::url("churches/{$church['id']}/delete") ?>" 
                                                                  class="d-inline" onsubmit="return confirm('Are you sure you want to delete this church?')">
                                                                <input type="hidden" name="_token" value="<?= \App\Utilities\Security::generateCSRFToken() ?>">
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="bx bx-trash me-2"></i>Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bx bx-church text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">No Churches Found</h5>
                        <p class="text-muted">There are no churches matching your current filters.<br>Create your first church to get started!</p>
                        <a href="<?= AssetHelper::url('churches/create') ?>" class="btn btn-primary btn-lg">
                            <i class="bx bx-plus me-2"></i>Create Your First Church
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.5px;
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.75em;
    border-radius: 0.375rem;
}

.btn {
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.15s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
}

.dropdown-menu {
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
}
</style>