<?php
use App\Utilities\AssetHelper;
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create New Unit</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= AssetHelper::url('units') ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Unit Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                               required minlength="3" maxlength="255" placeholder="Enter unit name">
                        <div class="form-text">Enter the name of the unit or department</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="4" maxlength="1000" placeholder="Enter unit description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        <div class="form-text">Optional description of the unit's purpose</div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?= AssetHelper::url('units') ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="check-circle" class="me-1"></i> Create Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
