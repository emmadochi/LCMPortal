<?php
use App\Utilities\AssetHelper;

$submissions = $submissions ?? [];
$directorUnits = $directorUnits ?? [];
$churches = $churches ?? [];
$selectedUnitId = $selectedUnitId ?? 0;
$selectedChurchId = $selectedChurchId ?? null;
$selectedStatus = $selectedStatus ?? '';
$isGeneralDirector = $isGeneralDirector ?? false;
?>

<div class="container-fluid p-0">
    <!-- Header Card with Cross-Branch & Filters -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-check-shield text-success me-2 font-size-24"></i> Department Submissions & Reviews
                    </h4>
                    <p class="text-muted font-size-13 mb-0">Review incoming reports from unit members, approve workflows, and request revisions.</p>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <a href="<?= AssetHelper::url('unit-reports/submissions/export?unit_id=' . $selectedUnitId . '&church_id=' . ($selectedChurchId ?? '')) ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">
                        <i class="bx bx-download me-1"></i> Export to CSV
                    </a>
                    <a href="<?= AssetHelper::url('unit-reports/templates?unit_id=' . $selectedUnitId) ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="bx bx-cog me-1"></i> Manage Form Templates
                    </a>
                </div>
            </div>

            <!-- Filter Controls -->
            <form method="GET" class="row g-2 mt-3 pt-3 border-top">
                <!-- Unit Selector -->
                <div class="col-md-4 col-sm-6">
                    <label class="form-label font-size-11 text-muted fw-bold mb-1">Ministry Department</label>
                    <select name="unit_id" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                        <?php foreach ($directorUnits as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= ($selectedUnitId == $u['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Church Branch Selector (Cross-Branch for General Directors) -->
                <?php if ($isGeneralDirector && !empty($churches)): ?>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label font-size-11 text-muted fw-bold mb-1">Church Branch (Cross-Branch Hub)</label>
                        <select name="church_id" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                            <option value="">All Church Branches (Global)</option>
                            <?php foreach ($churches as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ((string)$selectedChurchId === (string)$c['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Status Filter -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label font-size-11 text-muted fw-bold mb-1">Review Status</label>
                    <select name="status" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="submitted" <?= ($selectedStatus === 'submitted') ? 'selected' : '' ?>>Submitted (Pending Review)</option>
                        <option value="approved" <?= ($selectedStatus === 'approved') ? 'selected' : '' ?>>Approved</option>
                        <option value="needs_revision" <?= ($selectedStatus === 'needs_revision') ? 'selected' : '' ?>>Revision Requested</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted font-size-11 text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4">Report Form</th>
                            <th>Church Branch</th>
                            <th>Submitted By</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($submissions)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bx bx-inbox font-size-36 opacity-50 mb-2 d-block"></i>
                                    <h6 class="text-dark fw-semibold">No Submissions Found</h6>
                                    <p class="font-size-12 mb-0">Submissions from unit members will appear here as soon as they fill out assigned forms.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($submissions as $s): ?>
                                <?php
                                $statusBadge = 'bg-soft-warning text-warning';
                                $statusLabel = 'Pending Review';
                                if ($s['status'] === 'approved') {
                                    $statusBadge = 'bg-soft-success text-success';
                                    $statusLabel = 'Approved';
                                } elseif ($s['status'] === 'needs_revision') {
                                    $statusBadge = 'bg-soft-danger text-danger';
                                    $statusLabel = 'Needs Revision';
                                }
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark font-size-14">
                                            <?= htmlspecialchars($s['template_title']) ?>
                                        </div>
                                        <small class="text-muted font-size-11">
                                            <i class="bx bx-group me-1"></i> <?= htmlspecialchars($s['unit_name']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-size-11">
                                            <i class="bx bx-church me-1"></i> <?= htmlspecialchars($s['church_name'] ?? 'General HQ') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark font-size-13">
                                            <?= htmlspecialchars($s['submitter_name']) ?>
                                        </div>
                                        <small class="text-muted font-size-11"><?= htmlspecialchars($s['submitter_email']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-info text-info font-size-11">
                                            <?= htmlspecialchars($s['report_period']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $statusBadge ?> px-2.5 py-1 rounded-pill font-size-11 fw-semibold">
                                            <?= $statusLabel ?>
                                        </span>
                                    </td>
                                    <td class="font-size-12 text-muted">
                                        <?= date('M d, Y h:i A', strtotime($s['submitted_at'])) ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= AssetHelper::url('unit-reports/submissions/' . $s['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                                            <i class="bx bx-show me-1"></i> Review & Inspect
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
