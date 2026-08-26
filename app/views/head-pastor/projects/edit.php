<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$project = $project ?? null;
$units = $units ?? [];
$currentUnitIds = $currentUnitIds ?? [];
$csrf_token = $csrf_token ?? '';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                <h4 class="card-title mb-0">Edit Project: <?= htmlspecialchars($project['title']) ?></h4>
                <a href="<?= AssetHelper::url("churches/{$churchId}/projects/{$project['id']}") ?>" class="btn btn-sm btn-light">
                    <i class="bx bx-arrow-back me-1"></i> Back to Details
                </a>
            </div>
            <div class="card-body p-4">
                <form action="<?= AssetHelper::url("churches/{$churchId}/projects/{$project['id']}") ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="_method" value="PUT">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <label for="title" class="form-label">Project Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg border-light bg-light" id="title" name="title" value="<?= htmlspecialchars($project['title']) ?>" required>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control border-light bg-light" id="description" name="description" rows="6" required><?= htmlspecialchars($project['description']) ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light border-0 mb-4 shadow-none">
                                <div class="card-body">
                                    <h5 class="font-size-14 mb-3">Project Logistics</h5>
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label text-muted font-size-12 mb-1">Current Status</label>
                                        <select class="form-select border-0 shadow-sm" id="status" name="status" required>
                                            <option value="planning" <?= $project['status'] === 'planning' ? 'selected' : '' ?>>Planning</option>
                                            <option value="in_progress" <?= $project['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="on_hold" <?= $project['status'] === 'on_hold' ? 'selected' : '' ?>>On Hold</option>
                                            <option value="completed" <?= $project['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="cancelled" <?= $project['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="priority" class="form-label text-muted font-size-12 mb-1">Priority Level</label>
                                        <select class="form-select border-0 shadow-sm" id="priority" name="priority">
                                            <option value="low" <?= $project['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                                            <option value="medium" <?= $project['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                            <option value="high" <?= $project['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                                            <option value="urgent" <?= $project['priority'] === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="budget" class="form-label text-muted font-size-12 mb-1">Budget ($)</label>
                                        <input type="number" step="0.01" class="form-control border-0 shadow-sm" id="budget" name="budget" value="<?= $project['budget'] ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="card bg-light border-0 mb-4 shadow-none">
                                <div class="card-body">
                                    <h5 class="font-size-14 mb-3">Timeline</h5>
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label text-muted font-size-12 mb-1">Start Date</label>
                                        <input type="date" class="form-control border-0 shadow-sm" id="start_date" name="start_date" value="<?= $project['start_date'] ?>" required>
                                    </div>
                                    <div class="mb-0">
                                        <label for="end_date" class="form-label text-muted font-size-12 mb-1">End Date (Target)</label>
                                        <input type="date" class="form-control border-0 shadow-sm" id="end_date" name="end_date" value="<?= $project['end_date'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card border border-light shadow-none mb-0">
                                <div class="card-header bg-transparent py-3">
                                    <h5 class="font-size-14 mb-0">Assigned Units (Scope)</h5>
                                    <p class="text-muted font-size-12 mb-0">Update the church units involved in this project.</p>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php if (empty($units)): ?>
                                            <div class="col-12 text-center py-3">
                                                <p class="text-muted mb-0">No units found for this church.</p>
                                            </div>
                                        <?php else: ?>
                                            <?php foreach ($units as $unit): ?>
                                                <div class="col-md-4 col-sm-6 mb-2">
                                                    <div class="form-check custom-checkbox">
                                                        <input type="checkbox" class="form-check-input" id="unit_<?= $unit['unit_id'] ?>" name="unit_ids[]" value="<?= $unit['unit_id'] ?>" <?= in_array((int)$unit['unit_id'], $currentUnitIds) ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="unit_<?= $unit['unit_id'] ?>"><?= htmlspecialchars($unit['unit_name']) ?></label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="<?= AssetHelper::url("churches/{$churchId}/projects/{$project['id']}") ?>" class="btn btn-light px-4 me-2">Cancel</a>
                        <button type="submit" class="btn btn-info px-5">Update Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
