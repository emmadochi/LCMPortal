<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Unit</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= AssetHelper::url('units/' . $unit['id']) ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Unit Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($unit['name']) ?>" 
                               required minlength="3" maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="4" maxlength="1000"><?= htmlspecialchars($unit['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?= $unit['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $unit['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?= AssetHelper::url('units/' . $unit['id']) ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="check-circle" class="me-1"></i> Update Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
