<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Membership Type</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('membership-types') ?>">Membership Types</a></li>
                    <li class="breadcrumb-item active">Edit: <?= htmlspecialchars($membershipType['name']) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Edit Membership Type</h4>
            </div>
            
            <div class="card-body">
                <?php if ($this->session->hasFlash('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($this->session->getFlash('errors') as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?= AssetHelper::url('membership-types/' . $membershipType['id']) ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Membership Type Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($membershipType['name'] ?? $_POST['name'] ?? '') ?>" 
                                   required minlength="2" maxlength="50"
                                   placeholder="e.g., Visitor, Member, Leader, Pastor">
                            <div class="form-text">Enter a unique name for this membership type</div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3" maxlength="255"
                                      placeholder="Brief description of this membership type..."><?= htmlspecialchars($membershipType['description'] ?? $_POST['description'] ?? '') ?></textarea>
                            <div class="form-text">Optional: Describe the purpose or characteristics of this membership type</div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?= ($membershipType['is_active'] ?? ($_POST['is_active'] ?? true)) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                            <div class="form-text">Inactive membership types won't be available for new assignments</div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= AssetHelper::url('membership-types') ?>" class="btn btn-secondary">
                            <i data-feather="arrow-left" class="me-1"></i> Cancel
                        </a>
                        <div>
                            <button type="button" class="btn btn-danger me-2" onclick="deleteType()">
                                <i data-feather="trash" class="me-1"></i> Delete
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="me-1"></i> Update Membership Type
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Current Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td><?= date('F j, Y', strtotime($membershipType['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Last Updated:</strong></td>
                        <td><?= date('F j, Y g:i A', strtotime($membershipType['updated_at'])) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Current Status:</strong></td>
                        <td>
                            <?php if ($membershipType['is_active']): ?>
                                <span class="badge bg-success-subtle text-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Guidelines</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i data-feather="check-circle" class="text-success me-2"></i>
                        <strong>Unique Names:</strong> Each membership type must have a unique name
                    </li>
                    <li class="mb-2">
                        <i data-feather="check-circle" class="text-success me-2"></i>
                        <strong>Clear Descriptions:</strong> Help others understand the purpose of each type
                    </li>
                    <li class="mb-2">
                        <i data-feather="check-circle" class="text-success me-2"></i>
                        <strong>Status Control:</strong> Deactivate instead of deleting when no longer needed
                    </li>
                    <li class="mb-2">
                        <i data-feather="check-circle" class="text-success me-2"></i>
                        <strong>Careful Changes:</strong> Changing names affects all members with this type
                    </li>
                </ul>
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
                <p>Are you sure you want to delete "<strong><?= htmlspecialchars($membershipType['name']) ?></strong>"?</p>
                <p class="text-danger"><strong>Note:</strong> You can only delete membership types that are not currently assigned to any members.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="<?= AssetHelper::url('membership-types/' . $membershipType['id'] . '/delete') ?>" style="display: inline;">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars(Security::generateCSRFToken()) ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteType() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>