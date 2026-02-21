<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Create Membership Type</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('membership-types') ?>">Membership Types</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">New Membership Type</h4>
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
                
                <form method="POST" action="<?= AssetHelper::url('membership-types') ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Membership Type Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                                   required minlength="2" maxlength="50"
                                   placeholder="e.g., Visitor, Member, Leader, Pastor">
                            <div class="form-text">Enter a unique name for this membership type</div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3" maxlength="255"
                                      placeholder="Brief description of this membership type..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            <div class="form-text">Optional: Describe the purpose or characteristics of this membership type</div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
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
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Create Membership Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
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
                        <strong>Common Examples:</strong> Visitor, Member, Leader, Staff, Elder, Deacon, Pastor
                    </li>
                </ul>
                
                <hr>
                
                <h6>Best Practices:</h6>
                <ul class="ps-3">
                    <li>Use consistent naming conventions</li>
                    <li>Keep descriptions brief but descriptive</li>
                    <li>Consider hierarchy (Visitor → Member → Leader)</li>
                    <li>Plan for growth and flexibility</li>
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Default Types</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">These are commonly used membership types that will be automatically created:</p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <strong>Visitor</strong> - Guest or new visitor
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Member</strong> - Regular church member
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Leader</strong> - Team or group leader
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Staff</strong> - Church staff member
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Elder</strong> - Church elder or board member
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Deacon</strong> - Church deacon or servant
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Pastor</strong> - Lead pastor or minister
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>