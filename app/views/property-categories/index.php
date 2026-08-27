<?php
use App\Utilities\AssetHelper;
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
.fin-panel-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--fin-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.fin-table {
    width: 100%;
    border-collapse: collapse;
}
.fin-table thead th {
    background: #f8fafc;
    color: #64748b;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 12px 20px;
    border-bottom: 1px solid var(--fin-border);
}
.fin-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}
.fin-table tbody tr:hover {
    background: #f8fafc;
}
.fin-table td {
    padding: 14px 20px;
    font-size: 0.88rem;
    color: var(--fin-dark);
    vertical-align: middle;
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
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('properties') ?>" class="text-decoration-none text-muted">Properties</a></li>
                        <li class="breadcrumb-item active text-primary fw-semibold">Asset Categories</li>
                    </ol>
                </nav>
                <h3 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-folder text-primary"></i> Property & Asset Categories
                </h3>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= AssetHelper::url('properties') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bx bx-cube me-1"></i> View All Properties
                </a>
                <a href="<?= AssetHelper::url('property-categories/create') ?>" class="btn btn-primary rounded-pill px-4">
                    <i class="bx bx-plus me-1"></i> Create Category
                </a>
            </div>
        </div>
    </div>

    <!-- Categories Panel -->
    <div class="fin-panel">
        <div class="fin-panel-header">
            <h5 class="fin-panel-title">
                <i class="bx bx-category text-primary fs-5"></i> Registered Asset Categories (<?= count($categories ?? []) ?>)
            </h5>
        </div>
        <div class="fin-panel-body p-0">
            <?php if (empty($categories)): ?>
                <div class="text-center py-5">
                    <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                        <i class="bx bx-folder-open text-muted font-size-24"></i>
                    </div>
                    <h5 class="text-dark fw-bold">No categories registered yet</h5>
                    <p class="text-muted small mb-3">Organize your property and inventory (e.g. Sound Systems, Musical Instruments, Vehicles, Office Equipment).</p>
                    <a href="<?= AssetHelper::url('property-categories/create') ?>" class="btn btn-primary rounded-pill px-4">
                        <i class="bx bx-plus me-1"></i> Create First Category
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="fin-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Category Name</th>
                                <th>Description</th>
                                <th class="text-center">Total Assets</th>
                                <th>Created By</th>
                                <th>Created Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-xs bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem; background: #eef2ff;">
                                                <i class="bx bx-folder"></i>
                                            </div>
                                            <span class="fw-bold text-dark"><?= htmlspecialchars($category['name']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted small"><?= htmlspecialchars($category['description'] ?: 'No description provided') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 fw-bold">
                                            <?= (int)$category['property_count'] ?> Assets
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($category['creator_first_name']): ?>
                                            <span class="small fw-medium text-dark"><?= htmlspecialchars($category['creator_first_name'] . ' ' . $category['creator_last_name']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">Admin</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?= date('M d, Y', strtotime($category['created_at'])) ?></td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="<?= AssetHelper::url('property-categories/' . $category['id'] . '/edit') ?>" class="btn btn-sm btn-light rounded-pill px-3" title="Edit">
                                                <i class="bx bx-edit text-primary me-1"></i> Edit
                                            </a>
                                            <form method="POST" action="<?= AssetHelper::url('property-categories/' . $category['id'] . '/delete') ?>" 
                                                  onsubmit="return confirm('Are you sure you want to delete this category?');" class="d-inline">
                                                <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Utilities\Security::generateCSRFToken()) ?>">
                                                <button type="submit" class="btn btn-sm btn-light rounded-pill px-3 text-danger" title="Delete">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
