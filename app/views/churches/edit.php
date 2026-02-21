<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Church</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('churches') ?>">Churches</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url("churches/{$church['id']}") ?>"><?= htmlspecialchars($church['name']) ?></a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Edit Church: <?= htmlspecialchars($church['name']) ?></h4>
                <p class="card-title-desc mb-0">Update church information</p>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= AssetHelper::url("churches/{$church['id']}") ?>">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Church Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($church['name']) ?>" 
                                       placeholder="Enter church name" required>
                                <?php if (isset($errors['name'])): ?>
                                    <div class="text-danger mt-1"><?= htmlspecialchars($errors['name']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <?php foreach ($statuses as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= ($church['status'] === $key) ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="Enter church description..."><?= htmlspecialchars($church['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="<?= htmlspecialchars($church['address']) ?>" 
                                       placeholder="Street address" required>
                                <?php if (isset($errors['address'])): ?>
                                    <div class="text-danger mt-1"><?= htmlspecialchars($errors['address']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?= htmlspecialchars($church['city']) ?>" 
                                       placeholder="City" required>
                                <?php if (isset($errors['city'])): ?>
                                    <div class="text-danger mt-1"><?= htmlspecialchars($errors['city']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="state" name="state" 
                                       value="<?= htmlspecialchars($church['state']) ?>" 
                                       placeholder="State" required>
                                <?php if (isset($errors['state'])): ?>
                                    <div class="text-danger mt-1"><?= htmlspecialchars($errors['state']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                       value="<?= htmlspecialchars($church['postal_code']) ?>" 
                                       placeholder="Postal code" required>
                                <?php if (isset($errors['postal_code'])): ?>
                                    <div class="text-danger mt-1"><?= htmlspecialchars($errors['postal_code']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country" 
                                       value="<?= htmlspecialchars($church['country'] ?? 'USA') ?>" 
                                       placeholder="Country">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= htmlspecialchars($church['phone'] ?? '') ?>" 
                                       placeholder="Phone number">
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="text-danger mt-1"><?= htmlspecialchars($errors['phone']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($church['email'] ?? '') ?>" 
                                       placeholder="Email address">
                                <?php if (isset($errors['email'])): ?>
                                    <div class="text-danger mt-1"><?= htmlspecialchars($errors['email']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="website" name="website" 
                                       value="<?= htmlspecialchars($church['website'] ?? '') ?>" 
                                       placeholder="Website URL">
                                <?php if (isset($errors['website'])): ?>
                                    <div class="text-danger mt-1"><?= htmlspecialchars($errors['website']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="established_date" class="form-label">Established Date</label>
                                <input type="date" class="form-control" id="established_date" name="established_date" 
                                       value="<?= $church['established_date'] ? date('Y-m-d', strtotime($church['established_date'])) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pastor_user_id" class="form-label">Pastor</label>
                                <select class="form-select" id="pastor_user_id" name="pastor_user_id">
                                    <option value="">No pastor assigned</option>
                                    <?php foreach ($pastors ?? [] as $pastor): ?>
                                        <option value="<?= (int)$pastor['id'] ?>" <?= (($church['pastor_user_id'] ?? '') == $pastor['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars(trim($pastor['first_name'] . ' ' . $pastor['last_name']) . ' (' . $pastor['email'] . ')') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Select from registered pastors. Only users with the Pastor role appear here.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_headquarters" name="is_headquarters" 
                                   value="1" <?= $church['is_headquarters'] ? 'checked' : '' ?> 
                                   <?= $church['is_headquarters'] ? 'disabled' : '' ?>>
                            <label class="form-check-label" for="is_headquarters">
                                This is the headquarters/main church
                            </label>
                            <?php if ($church['is_headquarters']): ?>
                                <div class="form-text">
                                    This church is currently designated as headquarters and cannot be changed.
                                </div>
                            <?php else: ?>
                                <div class="form-text">
                                    Note: Only one church can be designated as headquarters.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= AssetHelper::url("churches/{$church['id']}") ?>" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Church
                        </a>
                        <div>
                            <?php if (!$church['is_headquarters']): ?>
                                <button type="button" class="btn btn-outline-danger me-2" 
                                        onclick="confirmDelete(<?= $church['id'] ?>)">
                                    <i class="bx bx-trash me-1"></i>Delete Church
                                </button>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Update Church
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format phone number
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 6) {
            value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
        } else if (value.length >= 3) {
            value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
        }
        e.target.value = value;
    });
    
});

function confirmDelete(churchId) {
    if (confirm('Are you sure you want to delete this church? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= AssetHelper::url("churches/") ?>' + churchId + '/delete';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '<?= $csrf_token ?>';
        form.appendChild(tokenInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.btn {
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.15s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
}
</style>