<?php
use App\Utilities\AssetHelper;

$templates = $templates ?? [];
$directorUnits = $directorUnits ?? [];
$selectedUnitId = $selectedUnitId ?? 0;
?>

<div class="container-fluid p-0">
    <!-- Header Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-layout text-primary me-2 font-size-24"></i> Department Report Forms & Templates
                    </h4>
                    <p class="text-muted font-size-13 mb-0">Configure dynamic questionnaires, submission schedules, and member reporting assignments.</p>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <!-- Unit Selector -->
                    <?php if (!empty($directorUnits)): ?>
                        <form method="GET" class="d-flex gap-2 align-items-center">
                            <select name="unit_id" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                                <?php foreach ($directorUnits as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= ($selectedUnitId == $u['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($u['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    <?php endif; ?>

                    <a href="<?= AssetHelper::url('unit-reports/templates/builder?unit_id=' . $selectedUnitId) ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                        <i class="bx bx-plus me-1"></i> Design New Report Form
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="row g-4">
        <?php if (empty($templates)): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                    <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                        <i class="bx bx-file-blank text-muted font-size-36"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No Report Templates Configured Yet</h5>
                    <p class="text-muted font-size-13 mb-4">Create your department's first custom report form or choose a 1-click ministry preset.</p>
                    <div>
                        <a href="<?= AssetHelper::url('unit-reports/templates/builder?unit_id=' . $selectedUnitId) ?>" class="btn btn-primary rounded-pill px-4">
                            <i class="bx bx-plus me-1"></i> Open Form Designer
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($templates as $t): ?>
                <div class="col-xl-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-soft-primary text-primary font-size-11 px-2.5 py-1 rounded-pill">
                                    <?= htmlspecialchars($t['category'] ?? 'General') ?>
                                </span>
                                <span class="badge bg-soft-success text-success font-size-11 px-2 py-0.5 rounded-pill">
                                    <?= ucfirst($t['frequency']) ?>
                                </span>
                            </div>

                            <h5 class="fw-bold text-dark mb-1 font-size-16">
                                <?= htmlspecialchars($t['title']) ?>
                            </h5>
                            <p class="text-muted font-size-12 mb-3 flex-grow-1" style="line-height: 1.5;">
                                <?= htmlspecialchars(mb_strimwidth($t['description'] ?? 'No description provided.', 0, 90, '...')) ?>
                            </p>

                            <div class="p-3 bg-light rounded-3 mb-3 font-size-12">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted"><i class="bx bx-list-check me-1"></i> Questions:</span>
                                    <strong class="text-dark"><?= (int)$t['field_count'] ?> Fields</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted"><i class="bx bx-time-five me-1"></i> Deadline:</span>
                                    <strong class="text-dark"><?= htmlspecialchars($t['deadline_day']) ?> at <?= date('h:i A', strtotime($t['deadline_time'])) ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"><i class="bx bx-folder me-1"></i> Submissions:</span>
                                    <strong class="text-primary"><?= (int)$t['submission_count'] ?> Received</strong>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="<?= AssetHelper::url('unit-reports/submit/' . $t['id']) ?>" class="btn btn-sm btn-outline-success rounded-pill px-3 flex-grow-1 fw-semibold" target="_blank">
                                    <i class="bx bx-play-circle me-1"></i> Preview Form
                                </a>
                                <a href="<?= AssetHelper::url('unit-reports/templates/builder/' . $t['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                                    <i class="bx bx-edit-alt me-1"></i> Edit Schema
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
