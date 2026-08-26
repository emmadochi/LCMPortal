<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$project = $project ?? null;
$projectUnits = $projectUnits ?? [];
?>

<div class="row">
    <div class="col-lg-8">
        <!-- Project Main Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-start mb-4">
                    <div class="flex-grow-1">
                        <h3 class="mb-1"><?= htmlspecialchars($project['title']) ?></h3>
                        <p class="text-muted mb-0">Project ID: #<?= $project['id'] ?> | Created: <?= date('M d, Y', strtotime($project['created_at'])) ?></p>
                    </div>
                    <div class="ms-3">
                        <?php 
                            $statusClass = 'bg-soft-secondary text-secondary';
                            if ($project['status'] === 'in_progress') $statusClass = 'bg-soft-primary text-primary';
                            elseif ($project['status'] === 'completed') $statusClass = 'bg-soft-success text-success';
                            elseif ($project['status'] === 'on_hold') $statusClass = 'bg-soft-warning text-warning';
                            elseif ($project['status'] === 'cancelled') $statusClass = 'bg-soft-danger text-danger';
                        ?>
                        <span class="badge rounded-pill <?= $statusClass ?> font-size-12 px-4 py-2">
                            <?= ucfirst(str_replace('_', ' ', $project['status'])) ?>
                        </span>
                    </div>
                </div>

                <div class="project-details mb-4">
                    <h5 class="font-size-16 mb-3">Project Description</h5>
                    <div class="text-muted font-size-14" style="line-height: 1.8;">
                        <?= nl2br(htmlspecialchars($project['description'])) ?>
                    </div>
                </div>

                <hr class="my-4 border-light">

                <div class="row text-center border-bottom border-light pb-4 mb-4">
                    <div class="col-4 border-end border-light">
                        <p class="text-muted mb-2 font-size-13">Start Date</p>
                        <h5 class="mb-0"><?= date('M d, Y', strtotime($project['start_date'])) ?></h5>
                    </div>
                    <div class="col-4 border-end border-light">
                        <p class="text-muted mb-2 font-size-13">End Date (Target)</p>
                        <h5 class="mb-0"><?= $project['end_date'] ? date('M d, Y', strtotime($project['end_date'])) : '--' ?></h5>
                    </div>
                    <div class="col-4">
                        <p class="text-muted mb-2 font-size-13">Budget</p>
                        <h5 class="mb-0 text-primary">₦<?= number_format($project['budget'] ?? 0, 2) ?></h5>
                    </div>
                </div>

                <div class="assigned-units">
                    <h5 class="font-size-16 mb-3">Participating Units</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if (empty($projectUnits)): ?>
                            <span class="text-muted italic">No units assigned to this project.</span>
                        <?php else: ?>
                            <?php foreach ($projectUnits as $pu): ?>
                                <span class="badge bg-light text-dark border p-2 px-3 fw-medium">
                                    <i class="bx bx-buildings me-1 text-primary"></i> <?= htmlspecialchars($pu['unit_name']) ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Actions Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Management</h5>
                <div class="d-grid gap-2">
                    <a href="<?= AssetHelper::url("churches/{$churchId}/projects/{$project['id']}/edit") ?>" class="btn btn-primary d-flex align-items-center justify-content-center">
                        <i class="bx bx-edit-alt me-2"></i> Edit Project
                    </a>
                    <a href="<?= AssetHelper::url("churches/{$churchId}/projects/records") ?>" class="btn btn-light d-flex align-items-center justify-content-center">
                        <i class="bx bx-list-ul me-2"></i> All Projects
                    </a>
                </div>
                
                <hr class="my-3">
                
                <form action="<?= AssetHelper::url("churches/{$churchId}/projects/{$project['id']}/delete") ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this project? This action is permanent.');" class="d-grid">
                    <button type="submit" class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                        <i class="bx bx-trash me-2"></i> Delete Project
                    </button>
                </form>
            </div>
        </div>

        <!-- Meta Information Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title mb-3">Project Metadata</h5>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-xs me-3">
                        <span class="avatar-title rounded-circle bg-soft-info text-info">
                            <i class="bx bx-purchase-tag-alt"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-muted mb-0 font-size-12">Priority</p>
                        <h6 class="mb-0"><?= ucfirst($project['priority']) ?></h6>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-0">
                    <div class="avatar-xs me-3">
                        <span class="avatar-title rounded-circle bg-soft-warning text-warning">
                            <i class="bx bx-user"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-muted mb-0 font-size-12">Owner / Assigned</p>
                        <h6 class="mb-0">Head Pastor</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
