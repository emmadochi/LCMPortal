<?php
use App\Utilities\AssetHelper;
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Property Categories</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Property Categories</li>
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
                    <h4 class="card-title mb-0">Property Categories</h4>
                    <p class="card-title-desc mb-0">Organize properties by categories</p>
                </div>
                <a href="<?= AssetHelper::url('property-categories/create') ?>" class="btn btn-primary">
                    <i data-feather="plus-circle" class="me-1"></i> Create Category
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Properties Count</th>
                                <th>Created By</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No categories found. <a href="<?= AssetHelper::url('property-categories/create') ?>">Create one</a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($category['name']) ?></strong></td>
                                        <td><?= htmlspecialchars($category['description'] ?: '-') ?></td>
                                        <td>
                                            <span class="badge bg-info"><?= (int)$category['property_count'] ?></span>
                                        </td>
                                        <td>
                                            <?php if ($category['creator_first_name']): ?>
                                                <?= htmlspecialchars($category['creator_first_name'] . ' ' . $category['creator_last_name']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($category['created_at'])) ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="<?= AssetHelper::url('property-categories/' . $category['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">
                                                    <i data-feather="edit" class="icon-sm"></i>
                                                </a>
                                                <form method="POST" action="<?= AssetHelper::url('property-categories/' . $category['id'] . '/delete') ?>" 
                                                      onsubmit="return confirm('Are you sure you want to delete this category? Properties must be removed or reassigned first.');">
                                                    <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Utilities\Security::generateCSRFToken()) ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i data-feather="trash-2" class="icon-sm"></i>
                                                    </button>
                                                </form>
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
