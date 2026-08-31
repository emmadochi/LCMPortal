<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$submission = $submission ?? null;
$values = $values ?? [];
$files = $files ?? [];
$csrfToken = Security::generateCSRFToken();

$status = $submission['status'] ?? 'submitted';
$statusBadge = 'bg-soft-warning text-warning';
$statusLabel = 'Pending Review';
if ($status === 'approved') {
    $statusBadge = 'bg-soft-success text-success';
    $statusLabel = 'Approved';
} elseif ($status === 'needs_revision') {
    $statusBadge = 'bg-soft-danger text-danger';
    $statusLabel = 'Revision Requested';
}
?>

<div class="container-fluid p-0">
    <!-- Header Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-soft-primary text-primary px-3 py-1 rounded-pill font-size-12 fw-semibold">
                            <i class="bx bx-group me-1"></i> <?= htmlspecialchars($submission['unit_name']) ?>
                        </span>
                        <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill font-size-12">
                            <i class="bx bx-church me-1"></i> <?= htmlspecialchars($submission['church_name'] ?? 'General HQ') ?>
                        </span>
                        <span class="badge <?= $statusBadge ?> px-3 py-1 rounded-pill font-size-12 fw-bold">
                            <?= $statusLabel ?>
                        </span>
                    </div>

                    <h3 class="fw-bold text-dark mb-1 font-size-22">
                        <?= htmlspecialchars($submission['template_title']) ?>
                    </h3>
                    <p class="text-muted font-size-13 mb-0">
                        Submitted by <strong><?= htmlspecialchars($submission['submitter_name']) ?></strong> (<?= htmlspecialchars($submission['submitter_email']) ?>) for Period <strong><?= htmlspecialchars($submission['report_period']) ?></strong>
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bx bx-printer me-1"></i> Print
                    </button>
                    <a href="<?= AssetHelper::url('unit-reports/submissions') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bx bx-arrow-back me-1"></i> Back to Submissions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: Dynamic Field Responses -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bx bx-list-check text-primary me-2 font-size-18"></i> Submitted Questionnaire & Responses
                    </h5>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($values)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bx bx-info-circle font-size-36 opacity-50 mb-2 d-block"></i>
                            <p class="font-size-13 mb-0">No dynamic responses captured for this record.</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($values as $idx => $val): ?>
                                <?php
                                $valText = $val['field_value'];
                                $isLong = strlen($valText) > 60 || $val['field_type'] === 'textarea';
                                $colClass = $isLong ? 'col-12' : 'col-md-6';
                                ?>
                                <div class="<?= $colClass ?>">
                                    <div class="p-3 bg-light rounded-3 border h-100">
                                        <label class="font-size-11 text-muted fw-bold text-uppercase d-block mb-1">
                                            <?= ($idx + 1) ?>. <?= htmlspecialchars($val['field_label']) ?>
                                        </label>
                                        <div class="fw-semibold text-dark font-size-14">
                                            <?php if ($val['field_type'] === 'file'): ?>
                                                <a href="<?= AssetHelper::url($valText) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-1">
                                                    <i class="bx bx-cloud-download me-1"></i> View / Download Attachment
                                                </a>
                                            <?php elseif ($val['field_type'] === 'checkbox'): ?>
                                                <span class="badge <?= $valText ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' ?> rounded-pill font-size-12">
                                                    <?= $valText ? 'Yes / Verified' : 'No / Skipped' ?>
                                                </span>
                                            <?php else: ?>
                                                <?= nl2br(htmlspecialchars($valText ?: 'None')) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Attached Media / Files -->
            <?php if (!empty($files)): ?>
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="bx bx-paperclip text-success me-2 font-size-18"></i> Attached Files & Photos
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <?php foreach ($files as $file): ?>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2 text-truncate">
                                            <i class="bx bx-file font-size-24 text-primary"></i>
                                            <div class="text-truncate">
                                                <h6 class="mb-0 font-size-13 text-dark text-truncate"><?= htmlspecialchars($file['file_name']) ?></h6>
                                                <small class="text-muted font-size-11"><?= round(($file['file_size'] ?? 0) / 1024) ?> KB</small>
                                            </div>
                                        </div>
                                        <a href="<?= AssetHelper::url($file['file_path']) ?>" target="_blank" class="btn btn-sm btn-light rounded-pill px-2.5">
                                            <i class="bx bx-download"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Review Action & Feedback Thread -->
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bx bx-check-double text-primary me-2 font-size-18"></i> Director Review Action
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?= AssetHelper::url('unit-reports/submissions/' . $submission['id'] . '/status') ?>">
                        <input type="hidden" name="_token" value="<?= $csrfToken ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark font-size-13">Update Status</label>
                            <select name="status" class="form-select rounded-pill">
                                <option value="approved" <?= ($status === 'approved') ? 'selected' : '' ?>>Approve Report</option>
                                <option value="needs_revision" <?= ($status === 'needs_revision') ? 'selected' : '' ?>>Request Revision (Needs Correction)</option>
                                <option value="under_review" <?= ($status === 'under_review') ? 'selected' : '' ?>>Mark Under Review</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark font-size-13">Director Feedback / Notes</label>
                            <textarea name="director_feedback" class="form-control rounded-3 font-size-13" rows="4" placeholder="Feedback, commendations, or revision instructions for the submitter..."><?= htmlspecialchars($submission['director_feedback'] ?? '') ?></textarea>
                            <small class="text-muted font-size-11 d-block mt-1">The member will receive an instant notification with this feedback.</small>
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold shadow-sm font-size-13 py-2">
                            <i class="bx bx-save me-1"></i> Save Review & Notify Member
                        </button>
                    </form>

                    <?php if (!empty($submission['reviewed_at'])): ?>
                        <div class="mt-4 pt-3 border-top font-size-12 text-muted">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Reviewed By:</span>
                                <strong class="text-dark"><?= htmlspecialchars($submission['reviewer_name'] ?? 'Director') ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Reviewed At:</span>
                                <strong class="text-dark"><?= date('M d, Y h:i A', strtotime($submission['reviewed_at'])) ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
