<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$csrfToken = Security::generateCSRFToken();
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Member</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('members') ?>">Member Directory</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('members/' . $member['id']) ?>">View Profile</a></li>
                    <li class="breadcrumb-item active">Edit Member</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Member: <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></h4>
            </div>
            <div class="card-body">
                <form action="<?= AssetHelper::url('members/' . $member['id']) ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?= htmlspecialchars($member['first_name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?= htmlspecialchars($member['last_name']) ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($member['email']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password (leave blank to keep current)</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($churches)): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="church_id" class="form-label">Church Branch <span class="text-danger">*</span></label>
                                <select class="form-select" id="church_id" name="church_id" required>
                                    <option value="">Select Church...</option>
                                    <?php foreach ($churches as $church): ?>
                                        <option value="<?= $church['id'] ?>" <?= (int)$member['church_id'] === (int)$church['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($church['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role">
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role ?>" <?= $member['role'] === $role ? 'selected' : '' ?>>
                                            <?= ucfirst($role) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <?php foreach ($statuses as $statusOption): ?>
                                        <option value="<?= $statusOption ?>" <?= $member['status'] === $statusOption ? 'selected' : '' ?>>
                                            <?= ucfirst($statusOption) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="unit_id" class="form-label">Assign to Unit</label>
                                <select class="form-select" id="unit_id" name="unit_id" required>
                                    <option value="">Select Unit</option>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?= $unit['id'] ?>" 
                                                <?= ($primaryMembership && $primaryMembership['unit_id'] == $unit['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($unit['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="membership_type" class="form-label">Membership Type</label>
                                <select class="form-select" id="membership_type" name="membership_type">
                                    <?php foreach ($membershipTypes as $type): ?>
                                        <option value="<?= $type ?>" 
                                                <?= ($primaryMembership && $primaryMembership['membership_type'] === $type) ? 'selected' : '' ?>>
                                            <?= ucfirst($type) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="join_date" class="form-label">Join Date</label>
                                <input type="date" class="form-control" id="join_date" name="join_date" 
                                       value="<?= $primaryMembership ? $primaryMembership['join_date'] : date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Member</button>
                        <a href="<?= AssetHelper::url('members/' . $member['id']) ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
