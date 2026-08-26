<?php
use App\Utilities\AssetHelper;
?>
<div class="db-page premium-form">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="db-panel">
                <div class="db-panel-header">
                    <h4 class="db-panel-title">
                        <span class="pi-blue"><i class="bx bx-edit"></i></span>
                        Edit User
                    </h4>
                </div>
                <div class="db-panel-body">
                    <form method="POST" action="<?= AssetHelper::url('users/' . $user['id']) ?>">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="_method" value="PUT">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?= htmlspecialchars($user['first_name']) ?>" 
                                           required minlength="2" maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?= htmlspecialchars($user['last_name']) ?>" 
                                           required minlength="2" maxlength="100">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="age_group" class="form-label">Age group</label>
                            <select class="form-select" id="age_group" name="age_group">
                                <option value="">Not specified</option>
                                <?php foreach (($ageGroups ?? []) as $value => $label): ?>
                                    <option value="<?= htmlspecialchars($value) ?>" <?= ($user['age_group'] ?? '') === $value ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Used for attendance reporting (returning vs first-timer by adult/child/teen). No exact age required.</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($user['email']) ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group auth-pass-inputgroup">
                                <input type="password" class="form-control" id="password" name="password" 
                                       minlength="6" placeholder="Leave blank to keep current password">
                                <button class="btn btn-light shadow-none ms-0 border" style="border-radius: 0 var(--db-radius-sm) var(--db-radius-sm) 0; background: #f8fafc;" type="button" id="password-addon">
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>
                            </div>
                            <div class="form-text">Leave blank to keep current password. Minimum 6 characters if changing.</div>
                        </div>

                        <div class="mb-3">
                            <label for="church_id" class="form-label">Church Branch <span class="text-danger">*</span></label>
                            <select class="form-select" id="church_id" name="church_id" required>
                                <option value="">Select Church...</option>
                                <?php foreach (($churches ?? []) as $church): ?>
                                    <option value="<?= $church['id'] ?>" <?= (int)($user['church_id'] ?? 0) === (int)$church['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($church['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select" id="role" name="role" required>
                                        <?php foreach ($roles as $roleOption): ?>
                                            <option value="<?= $roleOption ?>" 
                                                <?= $user['role'] === $roleOption ? 'selected' : '' ?>>
                                                <?= ucfirst($roleOption) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        <option value="suspended" <?= $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="<?= AssetHelper::url('users/' . $user['id']) ?>" class="btn-premium btn-secondary">Cancel</a>
                            <button type="submit" class="btn-premium btn-primary">
                                <i class="bx bx-check-circle"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const churchSelect = document.getElementById('church_id');
    const churchLabel = document.querySelector('label[for="church_id"]');
    
    if (roleSelect && churchSelect) {
        function toggleChurchRequired() {
            if (roleSelect.value === 'admin') {
                churchSelect.removeAttribute('required');
                if (churchLabel) {
                    churchLabel.innerHTML = 'Church Branch';
                }
            } else {
                churchSelect.setAttribute('required', 'required');
                if (churchLabel) {
                    churchLabel.innerHTML = 'Church Branch <span class="text-danger">*</span>';
                }
            }
        }
        
        roleSelect.addEventListener('change', toggleChurchRequired);
        toggleChurchRequired(); // Run initially
    }
});
</script>
