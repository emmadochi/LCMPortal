<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$units = $units ?? [];
$csrf_token = $csrf_token ?? '';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                <h4 class="card-title mb-0">Create New Project</h4>
                <a href="<?= AssetHelper::url("churches/{$churchId}/projects") ?>" class="btn btn-sm btn-light">
                    <i class="bx bx-arrow-back me-1"></i> Back to Dashboard
                </a>
            </div>
            <div class="card-body p-4">
                <form action="<?= AssetHelper::url("churches/{$churchId}/projects") ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <label for="title" class="form-label">Project Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg border-light bg-light" id="title" name="title" placeholder="Enter project name..." required>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control border-light bg-light" id="description" name="description" rows="6" placeholder="Describe the project goal, scope and impact..." required></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <h5 class="font-size-14 mb-3">Project Logistics</h5>
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label text-muted font-size-12 mb-1">Current Status</label>
                                        <select class="form-select border-0 shadow-sm" id="status" name="status" required>
                                            <option value="planning">Planning</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="on_hold">On Hold</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="priority" class="form-label text-muted font-size-12 mb-1">Priority Level</label>
                                        <select class="form-select border-0 shadow-sm" id="priority" name="priority">
                                            <option value="low">Low</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="high">High</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="budget" class="form-label text-muted font-size-12 mb-1">Budget ($)</label>
                                        <input type="number" step="0.01" class="form-control border-0 shadow-sm" id="budget" name="budget" placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <h5 class="font-size-14 mb-3">Timeline</h5>
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label text-muted font-size-12 mb-1">Start Date</label>
                                        <input type="date" class="form-control border-0 shadow-sm" id="start_date" name="start_date" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="mb-0">
                                        <label for="end_date" class="form-label text-muted font-size-12 mb-1">End Date (Target)</label>
                                        <input type="date" class="form-control border-0 shadow-sm" id="end_date" name="end_date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card border border-light">
                                <div class="card-header bg-transparent">
                                    <h5 class="font-size-14 mb-0">Assigned Units (Scope)</h5>
                                    <p class="text-muted font-size-12 mb-0">Select the church units involved in this project.</p>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php if (empty($units)): ?>
                                            <div class="col-12 text-center py-3">
                                                <p class="text-muted mb-0">No units found for this church. <a href="<?= AssetHelper::url('units/create') ?>" target="_blank">Create one?</a></p>
                                            </div>
                                        <?php else: ?>
                                            <?php foreach ($units as $unit): ?>
                                                <div class="col-md-4 col-sm-6 mb-2">
                                                    <div class="form-check custom-checkbox">
                                                        <input type="checkbox" class="form-check-input" id="unit_<?= $unit['unit_id'] ?>" name="unit_ids[]" value="<?= $unit['unit_id'] ?>">
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
                        <button type="reset" class="btn btn-light px-4 me-2">Reset</button>
                        <button type="submit" class="btn btn-primary px-5">Save Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
