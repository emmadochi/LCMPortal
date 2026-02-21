<?php
use App\Utilities\AssetHelper;
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create Project</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= AssetHelper::url('projects') ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" 
                               required minlength="3" maxlength="255" placeholder="Project title">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="6" required minlength="10" placeholder="Project description..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>" 
                                       required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="budget" class="form-label">Budget</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="budget" name="budget" 
                                           value="<?= htmlspecialchars($_POST['budget'] ?? '') ?>" 
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <?php foreach ($statuses as $status): ?>
                                        <option value="<?= $status ?>" 
                                            <?= (isset($_POST['status']) && $_POST['status'] === $status) ? 'selected' : ($status === 'planning' ? 'selected' : '') ?>>
                                            <?= ucfirst(str_replace('_', ' ', $status)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <?php foreach ($priorities as $priority): ?>
                                        <option value="<?= $priority ?>" 
                                            <?= (isset($_POST['priority']) && $_POST['priority'] === $priority) ? 'selected' : ($priority === 'medium' ? 'selected' : '') ?>>
                                            <?= ucfirst($priority) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="unit_ids" class="form-label">Assigned Units</label>
                        <select class="form-select" id="unit_ids" name="unit_ids[]" multiple size="5">
                            <?php foreach ($units as $unit): ?>
                                <option value="<?= $unit['id'] ?>">
                                    <?= htmlspecialchars($unit['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Hold Ctrl/Cmd to select multiple units</div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?= AssetHelper::url('projects') ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="check-circle" class="me-1"></i> Create Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

