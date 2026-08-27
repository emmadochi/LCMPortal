<?php
use App\Utilities\AssetHelper;

$filters = $filters ?? [];
$statusOptions = $statusOptions ?? [];
$churches = $churches ?? [];
$isAdmin = $isAdmin ?? false;
$isHeadPastor = $isHeadPastor ?? false;
?>

<style>
:root {
    --fin-emerald: #10b981;
    --fin-indigo: #4f46e5;
    --fin-dark: #0f172a;
    --fin-surface: #ffffff;
    --fin-border: #e2e8f0;
    --fin-sub: #64748b;
    --fin-radius: 16px;
}

.fin-header-card {
    background: #ffffff;
    border-radius: var(--fin-radius);
    padding: 22px 28px;
    border: 1px solid var(--fin-border);
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    margin-bottom: 24px;
}

.fin-panel {
    background: #ffffff;
    border-radius: var(--fin-radius);
    border: 1px solid var(--fin-border);
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    overflow: hidden;
    margin-bottom: 24px;
}
.fin-panel-header {
    padding: 18px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
}

.property-card {
    border: 1px solid var(--fin-border);
    border-radius: 14px;
    background: #ffffff;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    overflow: hidden;
    height: 100%;
}
.property-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.08);
}
</style>

<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="fin-header-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary fw-semibold">Properties & Assets</li>
                    </ol>
                </nav>
                <h3 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-cube-alt text-primary"></i> Church Properties & Assets
                </h3>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= AssetHelper::url('property-categories') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bx bx-folder me-1"></i> Asset Categories
                </a>
                <?php if ($isAdmin || $isHeadPastor): ?>
                    <a href="<?= AssetHelper::url('properties/create') ?>" class="btn btn-primary rounded-pill px-4">
                        <i class="bx bx-plus me-1"></i> Add Property Asset
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="fin-panel p-3 mb-4">
        <form method="GET" action="<?= AssetHelper::url('properties') ?>" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Search Property</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bx bx-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Name, serial number..." 
                           value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Category</label>
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
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted text-uppercase">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <?php foreach ($statusOptions as $key => $label): ?>
                        <option value="<?= $key ?>" 
                                <?= ($filters['status'] ?? '') === $key ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($isAdmin): ?>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted text-uppercase">Church Branch</label>
                <select name="church_id" class="form-select">
                    <option value="">All Branches</option>
                    <?php foreach ($churches as $church): ?>
                        <option value="<?= $church['id'] ?>"
                            <?= ($filters['church_id'] ?? '') == $church['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($church['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill"><i class="bx bx-filter-alt me-1"></i> Filter</button>
                <a href="<?= AssetHelper::url('properties') ?>" class="btn btn-light rounded-pill" title="Reset"><i class="bx bx-refresh"></i></a>
            </div>
        </form>
    </div>

    <!-- Properties Grid -->
    <div class="row g-4">
        <?php if (empty($properties)): ?>
            <div class="col-12">
                <div class="fin-panel text-center py-5">
                    <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                        <i class="bx bx-cube text-muted font-size-24"></i>
                    </div>
                    <h5 class="text-dark fw-bold">No property assets registered</h5>
                    <p class="text-muted small mb-3">Keep track of musical equipment, vehicles, real estate, electronics, and church furnishings.</p>
                    <?php if ($isAdmin || $isHeadPastor): ?>
                        <a href="<?= AssetHelper::url('properties/create') ?>" class="btn btn-primary rounded-pill px-4">
                            <i class="bx bx-plus me-1"></i> Add Your First Property Asset
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($properties as $property): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="property-card">
                        <?php if (!empty($property['image_path'])): ?>
                            <img src="<?= AssetHelper::baseUrl($property['image_path']) ?>" 
                                 class="img-fluid w-100" 
                                 alt="<?= htmlspecialchars($property['name']) ?>"
                                 style="height: 170px; object-fit: cover;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-light" 
                                 style="height: 170px;">
                                <i class="bx bx-cube-alt font-size-24 text-muted opacity-50"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-soft-primary text-primary"><?= htmlspecialchars($property['category_name'] ?? 'Asset') ?></span>
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
                                <span class="badge rounded-pill <?= $class ?>"><?= htmlspecialchars($statusLabel) ?></span>
                            </div>

                            <h6 class="fw-bold text-dark text-truncate mb-1 mt-2" title="<?= htmlspecialchars($property['name']) ?>">
                                <?= htmlspecialchars($property['name']) ?>
                            </h6>

                            <?php if (!empty($property['church_name'])): ?>
                                <p class="small text-muted mb-1"><i class="bx bx-church me-1"></i><?= htmlspecialchars($property['church_name']) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($property['location'])): ?>
                                <p class="small text-muted mb-1"><i class="bx bx-map-pin me-1"></i><?= htmlspecialchars($property['location']) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($property['assigned_first_name'])): ?>
                                <p class="small text-muted mb-3"><i class="bx bx-user me-1"></i>Assigned: <?= htmlspecialchars($property['assigned_first_name'] . ' ' . $property['assigned_last_name']) ?></p>
                            <?php else: ?>
                                <p class="small text-muted mb-3"><i class="bx bx-user-x me-1"></i>Unassigned</p>
                            <?php endif; ?>

                            <div class="d-flex gap-2">
                                <a href="<?= AssetHelper::url('properties/' . $property['id']) ?>" 
                                   class="btn btn-sm btn-outline-primary flex-fill rounded-pill">
                                    <i class="bx bx-show me-1"></i> View Asset
                                </a>
                                <a href="<?= AssetHelper::url('properties/' . $property['id'] . '/edit') ?>" 
                                   class="btn btn-sm btn-light rounded-pill px-3" title="Edit Asset">
                                    <i class="bx bx-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
