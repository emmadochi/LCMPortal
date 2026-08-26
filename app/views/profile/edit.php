<?php
use App\Utilities\AssetHelper;
?>

<div class="row justify-content-center">
    <div class="col-xl-6 col-lg-8">
        <div class="card metric-card shadow-sm animate__animated animate__fadeInUp">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Edit My Profile</h5>
            </div>
            <div class="card-body">
                <form action="<?= AssetHelper::url('profile/update-details') ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($member['first_name']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($member['last_name']) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($member['phone'] ?? '') ?>" placeholder="e.g. +2348012345678">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Age Group</label>
                            <select name="age_group" class="form-select">
                                <option value="" <?= empty($member['age_group']) ? 'selected' : '' ?>>Select Age Group</option>
                                <option value="adult" <?= ($member['age_group'] ?? '') === 'adult' ? 'selected' : '' ?>>Adult</option>
                                <option value="teen" <?= ($member['age_group'] ?? '') === 'teen' ? 'selected' : '' ?>>Teen</option>
                                <option value="child" <?= ($member['age_group'] ?? '') === 'child' ? 'selected' : '' ?>>Child</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Home Address</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($member['address'] ?? '') ?>" placeholder="Enter your residential address">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address (Read-only)</label>
                        <input type="email" name="email" class="form-control bg-light" value="<?= htmlspecialchars($member['email']) ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" name="profile_picture" class="form-control" accept="image/*">
                        <small class="text-muted">Allowed types: JPG, PNG, GIF, WEBP. Max size: 2MB.</small>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3 text-primary">Security Settings</h6>
                    <div class="mb-3">
                        <label class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="<?= AssetHelper::url('profile') ?>" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="alert alert-info mt-3 shadow-sm border-0">
            <div class="d-flex align-items-center gap-2">
                <i class='bx bx-info-circle h4 mb-0'></i>
                <div>
                    <strong>Note:</strong> Sensitive details like baptism date or unit assignments can only be changed by an administrator.
                </div>
            </div>
        </div>
    </div>
</div>
