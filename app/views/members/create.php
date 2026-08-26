<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$csrfToken = Security::generateCSRFToken();
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Create Member</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('members') ?>">Member Directory</a></li>
                    <li class="breadcrumb-item active">Create Member</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create Member</h4>
            </div>
            <div class="card-body">
                <form action="<?= AssetHelper::url('members') ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
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
                                        <option value="<?= $church['id'] ?>"><?= htmlspecialchars($church['name']) ?></option>
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
                                        <option value="<?= $role ?>" <?= $role === 'user' ? 'selected' : '' ?>><?= ucfirst($role) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="unit_id" class="form-label">Assign to Unit</label>
                                <select class="form-select" id="unit_id" name="unit_id" required>
                                    <option value="">Select Unit</option>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?= $unit['id'] ?>"><?= htmlspecialchars($unit['name']) ?></option>
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
                                        <option value="<?= $type ?>" <?= $type === 'member' ? 'selected' : '' ?>><?= ucfirst($type) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="join_date" class="form-label">Join Date</label>
                                <input type="date" class="form-control" id="join_date" name="join_date" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Member</button>
                </form>
            </div>
        </div>
    </div>
</div>
