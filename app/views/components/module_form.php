<?php
use App\Utilities\AssetHelper;
use App\Utilities\Helper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= htmlspecialchars($title ?? 'Create Record') ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <?php foreach ($breadcrumbs ?? [] as $breadcrumb): ?>
                        <?php if (isset($breadcrumb['active']) && $breadcrumb['active']): ?>
                            <li class="breadcrumb-item active"><?= htmlspecialchars($breadcrumb['label']) ?></li>
                        <?php elseif (isset($breadcrumb['url'])): ?>
                            <li class="breadcrumb-item">
                                <a href="<?= AssetHelper::url($breadcrumb['url']) ?>"><?= htmlspecialchars($breadcrumb['label']) ?></a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item"><?= htmlspecialchars($breadcrumb['label']) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="bx bx-<?= $moduleIcon ?? 'file' ?> me-2"></i>
                    <?= $formTitle ?? 'Record Details' ?>
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= AssetHelper::url($formAction ?? "#") ?>" 
                      id="recordForm" enctype="<?= $enctype ?? 'application/x-www-form-urlencoded' ?>">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?? '' ?>">
                    
                    <?php if (isset($formFields)): ?>
                        <?php foreach ($formFields as $field): ?>
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    <?= htmlspecialchars($field['label']) ?>
                                    <?php if (isset($field['required']) && $field['required']): ?>
                                        <span class="text-danger">*</span>
                                    <?php endif; ?>
                                </label>
                                
                                <?php if ($field['type'] === 'text'): ?>
                                    <input type="text" 
                                           class="form-control <?= isset($field['error']) ? 'is-invalid' : '' ?>" 
                                           name="<?= $field['name'] ?>" 
                                           value="<?= htmlspecialchars($field['value'] ?? '') ?>"
                                           placeholder="<?= $field['placeholder'] ?? '' ?>"
                                           <?= isset($field['required']) && $field['required'] ? 'required' : '' ?>
                                           <?= isset($field['readonly']) && $field['readonly'] ? 'readonly' : '' ?>>
                                
                                <?php elseif ($field['type'] === 'email'): ?>
                                    <input type="email" 
                                           class="form-control <?= isset($field['error']) ? 'is-invalid' : '' ?>" 
                                           name="<?= $field['name'] ?>" 
                                           value="<?= htmlspecialchars($field['value'] ?? '') ?>"
                                           placeholder="<?= $field['placeholder'] ?? '' ?>"
                                           <?= isset($field['required']) && $field['required'] ? 'required' : '' ?>>
                                
                                <?php elseif ($field['type'] === 'number'): ?>
                                    <input type="number" 
                                           class="form-control <?= isset($field['error']) ? 'is-invalid' : '' ?>" 
                                           name="<?= $field['name'] ?>" 
                                           value="<?= htmlspecialchars($field['value'] ?? '') ?>"
                                           placeholder="<?= $field['placeholder'] ?? '' ?>"
                                           min="<?= $field['min'] ?? '' ?>"
                                           max="<?= $field['max'] ?? '' ?>"
                                           step="<?= $field['step'] ?? '1' ?>"
                                           <?= isset($field['required']) && $field['required'] ? 'required' : '' ?>>
                                
                                <?php elseif ($field['type'] === 'date'): ?>
                                    <input type="date" 
                                           class="form-control <?= isset($field['error']) ? 'is-invalid' : '' ?>" 
                                           name="<?= $field['name'] ?>" 
                                           value="<?= htmlspecialchars($field['value'] ?? '') ?>"
                                           <?= isset($field['required']) && $field['required'] ? 'required' : '' ?>>
                                
                                <?php elseif ($field['type'] === 'datetime'): ?>
                                    <input type="datetime-local" 
                                           class="form-control <?= isset($field['error']) ? 'is-invalid' : '' ?>" 
                                           name="<?= $field['name'] ?>" 
                                           value="<?= htmlspecialchars($field['value'] ?? '') ?>"
                                           <?= isset($field['required']) && $field['required'] ? 'required' : '' ?>>
                                
                                <?php elseif ($field['type'] === 'textarea'): ?>
                                    <textarea class="form-control <?= isset($field['error']) ? 'is-invalid' : '' ?>" 
                                              name="<?= $field['name'] ?>" 
                                              rows="<?= $field['rows'] ?? 3 ?>"
                                              placeholder="<?= $field['placeholder'] ?? '' ?>"
                                              <?= isset($field['required']) && $field['required'] ? 'required' : '' ?>><?= htmlspecialchars($field['value'] ?? '') ?></textarea>
                                
                                <?php elseif ($field['type'] === 'select'): ?>
                                    <select class="form-select <?= isset($field['error']) ? 'is-invalid' : '' ?>" 
                                            name="<?= $field['name'] ?>"
                                            <?= isset($field['required']) && $field['required'] ? 'required' : '' ?>>
                                        <?php if (isset($field['placeholder'])): ?>
                                            <option value=""><?= htmlspecialchars($field['placeholder']) ?></option>
                                        <?php endif; ?>
                                        <?php foreach ($field['options'] ?? [] as $option): ?>
                                            <option value="<?= $option['value'] ?>" 
                                                    <?= (isset($field['value']) && $field['value'] == $option['value']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($option['label']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                
                                <?php elseif ($field['type'] === 'checkbox'): ?>
                                    <div class="form-check">
                                        <input class="form-check-input <?= isset($field['error']) ? 'is-invalid' : '' ?>" 
                                               type="checkbox" 
                                               name="<?= $field['name'] ?>" 
                                               value="1"
                                               id="<?= $field['name'] ?>"
                                               <?= (isset($field['value']) && $field['value']) ? 'checked' : '' ?>
                                               <?= isset($field['required']) && $field['required'] ? 'required' : '' ?>>
                                        <label class="form-check-label" for="<?= $field['name'] ?>">
                                            <?= htmlspecialchars($field['label']) ?>
                                        </label>
                                    </div>
                                
                                <?php elseif ($field['type'] === 'radio'): ?>
                                    <?php foreach ($field['options'] ?? [] as $option): ?>
                                        <div class="form-check">
                                            <input class="form-check-input <?= isset($field['error']) ? 'is-invalid' : '' ?>" 
                                                   type="radio" 
                                                   name="<?= $field['name'] ?>" 
                                                   value="<?= $option['value'] ?>"
                                                   id="<?= $field['name'] . '_' . $option['value'] ?>"
                                                   <?= (isset($field['value']) && $field['value'] == $option['value']) ? 'checked' : '' ?>
                                                   <?= isset($field['required']) && $field['required'] ? 'required' : '' ?>>
                                            <label class="form-check-label" for="<?= $field['name'] . '_' . $option['value'] ?>">
                                                <?= htmlspecialchars($option['label']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                
                                <?php elseif ($field['type'] === 'file'): ?>
                                    <input type="file" 
                                           class="form-control <?= isset($field['error']) ? 'is-invalid' : '' ?>" 
                                           name="<?= $field['name'] ?>"
                                           accept="<?= $field['accept'] ?? '*' ?>"
                                           <?= isset($field['required']) && $field['required'] ? 'required' : '' ?>>
                                    <?php if (isset($field['current']) && $field['current']): ?>
                                        <div class="mt-2">
                                            <small class="text-muted">Current file: 
                                                <a href="<?= AssetHelper::url($field['current']) ?>" target="_blank">
                                                    <?= htmlspecialchars(basename($field['current'])) ?>
                                                </a>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                
                                <?php endif; ?>
                                
                                <?php if (isset($field['help'])): ?>
                                    <div class="form-text"><?= htmlspecialchars($field['help']) ?></div>
                                <?php endif; ?>
                                
                                <?php if (isset($field['error'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($field['error']) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= AssetHelper::url($cancelUrl ?? "#") ?>" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Cancel
                        </a>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i><?= $submitText ?? 'Save Record' ?>
                            </button>
                            <?php if (isset($showSaveAndNew) && $showSaveAndNew): ?>
                                <button type="submit" name="save_and_new" value="1" class="btn btn-outline-primary">
                                    Save & New
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <?php if (isset($sidebarContent)): ?>
            <?= $sidebarContent ?>
        <?php else: ?>
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bx bx-info-circle me-2"></i>Information</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        <?= $sidebarText ?? 'Fill in all required fields marked with *' ?>
                    </p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bx bx-check-circle text-success me-2"></i>All fields are required unless marked optional</li>
                        <li class="mb-2"><i class="bx bx-check-circle text-success me-2"></i>Data will be saved to your church's records</li>
                        <li class="mb-0"><i class="bx bx-check-circle text-success me-2"></i>Changes are logged for audit purposes</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('recordForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Scroll to first invalid field
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
            }
        });
        
        // Remove invalid class on input
        form.addEventListener('input', function(e) {
            if (e.target.classList.contains('is-invalid')) {
                e.target.classList.remove('is-invalid');
            }
        });
    }
    
    // Auto-save draft (optional)
    <?php if (isset($enableAutoSave) && $enableAutoSave): ?>
    let autoSaveTimer;
    const autoSave = function() {
        // Implement auto-save logic here
        console.log('Auto-saving...');
    };
    
    form.addEventListener('input', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(autoSave, 30000); // Auto-save every 30 seconds
    });
    <?php endif; ?>
});
</script>