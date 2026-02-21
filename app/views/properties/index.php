<?php
use App\Utilities\AssetHelper;

$filters = $filters ?? [];
$statusOptions = $statusOptions ?? [];
$churches = $churches ?? [];
$isAdmin = $isAdmin ?? false;
$isHeadPastor = $isHeadPastor ?? false;
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Properties</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Properties</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Church Properties</h4>
                    <p class="card-title-desc mb-0">Manage church assets and equipment</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= AssetHelper::url('property-categories') ?>" class="btn btn-outline-info">
                        <i data-feather="folder" class="me-1"></i> Categories
                    </a>
                    <?php if ($isAdmin || $isHeadPastor): ?>
                        <a href="<?= AssetHelper::url('properties/create') ?>" class="btn btn-primary">
                            <i data-feather="plus-circle" class="me-1"></i> Add Property
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="<?= AssetHelper::url('properties') ?>" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search properties..." 
                                   value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" 
                                            <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <?php foreach ($statusOptions as $key => $label): ?>
                                    <option value="<?= $key ?>" 
                                            <?= ($filters['status'] ?? '') === $key ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if ($isAdmin): ?>
                        <div class="col-md-3">
                            <select name="church_id" class="form-select">
                                <option value="">All Churches</option>
                                <?php foreach ($churches as $church): ?>
                                    <option value="<?= $church['id'] ?>"
                                        <?= ($filters['church_id'] ?? '') == $church['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($church['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                                <a href="<?= AssetHelper::url('properties') ?>" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Properties Grid -->
                <div class="row">
                    <?php if (empty($properties)): ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i data-feather="package" class="icon-lg text-muted mb-3"></i>
                                <p class="text-muted">No properties found.</p>
                                <a href="<?= AssetHelper::url('properties/create') ?>" class="btn btn-primary">Add Your First Property</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($properties as $property): ?>
                            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body p-2">
                                        <?php if (!empty($property['image_path'])): ?>
                                            <img src="<?= AssetHelper::baseUrl($property['image_path']) ?>" 
                                                 class="img-fluid rounded mb-2" 
                                                 alt="<?= htmlspecialchars($property['name']) ?>"
                                                 style="height: 180px; width: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center bg-light rounded mb-2" 
                                                 style="height: 180px;">
                                                <i data-feather="package" class="icon-lg text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-1 text-truncate" title="<?= htmlspecialchars($property['name']) ?>">
                                                <?= htmlspecialchars($property['name']) ?>
                                            </h6>
                                            <p class="text-muted mb-1 font-size-12">
                                                <span class="badge bg-info"><?= htmlspecialchars($property['category_name']) ?></span>
                                                <?php if (!empty($property['church_name'])): ?>
                                                    <span class="badge bg-secondary ms-1"><?= htmlspecialchars($property['church_name']) ?></span>
                                                <?php endif; ?>
                                            </p>
                                            <p class="mb-2">
                                                <?php
                                                $statusClass = [
                                                    'available' => 'bg-success',
                                                    'in_use' => 'bg-primary',
                                                    'maintenance' => 'bg-warning',
                                                    'damaged' => 'bg-danger',
                                                    'disposed' => 'bg-secondary',
                                                    'lost' => 'bg-dark'
                                                ];
                                                $statusLabel = $statusOptions[$property['status']] ?? $property['status'];
                                                $class = $statusClass[$property['status']] ?? 'bg-secondary';
                                                ?>
                                                <span class="badge <?= $class ?>"><?= htmlspecialchars($statusLabel) ?></span>
                                            </p>
                                            <?php if ($property['location']): ?>
                                                <p class="text-muted mb-1 font-size-12">
                                                    <i data-feather="map-pin" class="icon-xs me-1"></i>
                                                    <?= htmlspecialchars($property['location']) ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($property['assigned_first_name'])): ?>
                                                <p class="text-muted mb-2 font-size-12">
                                                    <i data-feather="user" class="icon-xs me-1"></i>
                                                    Assigned: <?= htmlspecialchars($property['assigned_first_name'] . ' ' . $property['assigned_last_name']) ?>
                                                </p>
                                            <?php endif; ?>
                                            <div class="d-flex gap-1">
                                                <a href="<?= AssetHelper::url('properties/' . $property['id']) ?>" 
                                                   class="btn btn-sm btn-outline-primary flex-fill">
                                                    <i data-feather="eye" class="icon-sm"></i> View
                                                </a>
                                                <a href="<?= AssetHelper::url('properties/' . $property['id'] . '/edit') ?>" 
                                                   class="btn btn-sm btn-outline-secondary">
                                                    <i data-feather="edit" class="icon-sm"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
