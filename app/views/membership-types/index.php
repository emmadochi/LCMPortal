<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Membership Types</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Membership Types</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="card-title mb-0">Manage Membership Types</h4>
                        <p class="card-title-desc mb-0">Create, edit, and organize membership categories</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="<?= AssetHelper::url('membership-types/create') ?>" class="btn btn-primary">
                            <i data-feather="plus-circle" class="me-1"></i> Add New Type
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Statistics Overview -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <h3 class="text-primary"><?= count($membershipTypes) ?></h3>
                                <p class="mb-0">Total Membership Types</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <h3 class="text-success">
                                    <?= count(array_filter($membershipTypes, function($type) { return $type['is_active']; })) ?>
                                </h3>
                                <p class="mb-0">Active Types</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <h3 class="text-warning">
                                    <?= count(array_filter($membershipTypes, function($type) { return !$type['is_active']; })) ?>
                                </h3>
                                <p class="mb-0">Inactive Types</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Membership Types Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="membership-types-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Members</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($membershipTypes)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i data-feather="users" class="icon-lg text-muted mb-3"></i>
                                        <h5>No membership types found</h5>
                                        <p class="text-muted">Create your first membership type to get started</p>
                                        <a href="<?= AssetHelper::url('membership-types/create') ?>" class="btn btn-primary">
                                            <i data-feather="plus" class="me-1"></i> Create First Type
                                        </a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($membershipTypes as $type): ?>
                                    <tr>
                                        <td>
                                            <h5 class="font-size-14 mb-1">
                                                <?= htmlspecialchars($type['name']) ?>
                                            </h5>
                                        </td>
                                        
                                        <td>
                                            <?= htmlspecialchars($type['description'] ?? 'No description') ?>
                                        </td>
                                        
                                        <td>
                                            <?php if ($type['is_active']): ?>
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td>
                                            <!-- This would show actual member count -->
                                            <span class="text-muted">N/A</span>
                                        </td>
                                        
                                        <td>
                                            <?= date('M j, Y', strtotime($type['created_at'])) ?>
                                        </td>
                                        
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= AssetHelper::url('membership-types/' . $type['id'] . '/edit') ?>" 
                                                   class="btn btn-sm btn-primary" title="Edit">
                                                    <i data-feather="edit" class="me-1"></i>
                                                </a>
                                                
                                                <?php if ($type['is_active']): ?>
                                                    <button class="btn btn-sm btn-outline-warning" 
                                                            onclick="toggleStatus(<?= $type['id'] ?>, false)" 
                                                            title="Deactivate">
                                                        <i data-feather="pause" class="me-1"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-success" 
                                                            onclick="toggleStatus(<?= $type['id'] ?>, true)" 
                                                            title="Activate">
                                                        <i data-feather="play" class="me-1"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteType(<?= $type['id'] ?>)" 
                                                        title="Delete">
                                                    <i data-feather="trash" class="me-1"></i>
                                                </button>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Membership Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this membership type?</p>
                <p class="text-danger"><strong>Note:</strong> You can only delete membership types that are not currently assigned to any members.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="_token" value="<?= htmlspecialchars(Security::generateCSRFToken()) ?>">
</form>

<script>
let typeIdToDelete = null;

function toggleStatus(typeId, newStatus) {
    if (confirm(`Are you sure you want to ${newStatus ? 'activate' : 'deactivate'} this membership type?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= AssetHelper::url('membership-types') ?>/' + typeId + '/toggle-status';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '<?= htmlspecialchars(Security::generateCSRFToken()) ?>';
        form.appendChild(tokenInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteType(typeId) {
    typeIdToDelete = typeId;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (typeIdToDelete) {
        const form = document.getElementById('deleteForm');
        form.action = '<?= AssetHelper::url('membership-types') ?>/' + typeIdToDelete + '/delete';
        form.submit();
    }
});

// Initialize DataTable
document.addEventListener('DOMContentLoaded', function() {
    if (typeof DataTable !== 'undefined') {
        new DataTable('#membership-types-table', {
            paging: true,
            searching: true,
            ordering: true,
            info: true
        });
    }
});
</script>