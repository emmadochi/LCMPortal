<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$properties = $properties ?? [];
$categories = $categories ?? [];
$filters = $filters ?? [];
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="card-title mb-1">Asset Inventory</h4>
                        <p class="text-muted mb-0">Complete list of church properties and their current status.</p>
                    </div>
                    <div>
                        <a href="<?= AssetHelper::url("churches/{$churchId}/property/create") ?>" class="btn btn-primary d-flex align-items-center">
                            <i class="bx bx-plus me-1"></i> Register New Asset
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <form action="<?= AssetHelper::url("churches/{$churchId}/property/records") ?>" method="GET" class="bg-light p-3 rounded mb-4 shadow-none">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label font-size-12 text-muted mb-1">Search</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-0"><i class="bx bx-search"></i></span>
                                <input type="text" name="search" class="form-control border-0 shadow-sm" placeholder="Name, description or serial..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-size-12 text-muted mb-1">Category</label>
                            <select name="category_id" class="form-select border-0 shadow-sm">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (isset($filters['category_id']) && $filters['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-size-12 text-muted mb-1">Status</label>
                            <select name="status" class="form-select border-0 shadow-sm">
                                <option value="">All Statuses</option>
                                <option value="available" <?= (isset($filters['status']) && $filters['status'] === 'available') ? 'selected' : '' ?>>Available</option>
                                <option value="in_use" <?= (isset($filters['status']) && $filters['status'] === 'in_use') ? 'selected' : '' ?>>In Use</option>
                                <option value="maintenance" <?= (isset($filters['status']) && $filters['status'] === 'maintenance') ? 'selected' : '' ?>>Maintenance</option>
                                <option value="damaged" <?= (isset($filters['status']) && $filters['status'] === 'damaged') ? 'selected' : '' ?>>Damaged</option>
                                <option value="lost" <?= (isset($filters['status']) && $filters['status'] === 'lost') ? 'selected' : '' ?>>Lost</option>
                                <option value="disposed" <?= (isset($filters['status']) && $filters['status'] === 'disposed') ? 'selected' : '' ?>>Disposed</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-info w-100 shadow-sm">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="propertyTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Asset Details</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th class="text-end">Cost</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($properties)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="avatar-md mx-auto mb-3">
                                            <span class="avatar-title rounded-circle bg-light text-primary font-size-24">
                                                <i class="bx bx-package"></i>
                                            </span>
                                        </div>
                                        <h5>No properties found</h5>
                                        <p class="text-muted">Adjust your filters or register a new asset.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($properties as $index => $p): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <div class="fw-bold text-dark mb-0"><?= htmlspecialchars($p['name']) ?></div>
                                            <small class="text-muted">SN: <?= htmlspecialchars($p['serial_number'] ?: 'None') ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-info text-info rounded-pill font-size-11">
                                                <?= htmlspecialchars($p['category_name']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($p['location'] ?: '-') ?></td>
                                        <td>
                                            <?php if ($p['assigned_to_user_id']): ?>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-10">
                                                            <?= strtoupper(substr($p['assigned_first_name'], 0, 1) . substr($p['assigned_last_name'], 0, 1)) ?>
                                                        </span>
                                                    </div>
                                                    <span class="font-size-13"><?= htmlspecialchars($p['assigned_first_name'] . ' ' . $p['assigned_last_name']) ?></span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted italic">- Unassigned -</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $statusClass = 'bg-soft-secondary text-secondary';
                                                switch($p['status']) {
                                                    case 'available':
                                                    case 'in_use': $statusClass = 'bg-soft-success text-success'; break;
                                                    case 'maintenance': $statusClass = 'bg-soft-warning text-warning'; break;
                                                    case 'damaged':
                                                    case 'lost':
                                                    case 'disposed': $statusClass = 'bg-soft-danger text-danger'; break;
                                                }
                                            ?>
                                            <span class="badge <?= $statusClass ?> font-size-11">
                                                <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">₦<?= number_format($p['purchase_cost'] ?? 0, 2) ?></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light btn-rounded dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li><a class="dropdown-item" href="<?= AssetHelper::url("churches/{$churchId}/property/{$p['id']}") ?>"><i class="bx bx-show-alt me-2 text-primary"></i> Asset Details</a></li>
                                                    <li><a class="dropdown-item" href="<?= AssetHelper::url("churches/{$churchId}/property/{$p['id']}/edit") ?>"><i class="bx bx-edit-alt me-2 text-info"></i> Edit Asset</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#statusModal<?= $p['id'] ?>"><i class="bx bx-refresh me-2 text-warning"></i> Update Status</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
