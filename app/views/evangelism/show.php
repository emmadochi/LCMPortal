<?php
use App\Utilities\AssetHelper;
?>

<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm text-white overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); border-radius: 18px; border: 1px solid rgba(245, 158, 11, 0.2) !important;">
            <div class="card-body p-4 position-relative">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge px-3 py-1 rounded-pill font-size-12 fw-bold" style="background: rgba(245, 158, 11, 0.2); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.4);">
                                <i class="bx bx-check-circle me-1 align-middle"></i> Verified Entry
                            </span>
                        </div>
                        <h2 class="text-white fw-bold mb-1 font-size-22">Evangelism Report Details</h2>
                        <p class="text-white-50 font-size-13 mb-0">Record #<?= (int)$record['id'] ?> filed on <?= date('M d, Y', strtotime($record['report_date'])) ?></p>
                    </div>
                    <div class="mt-3 mt-md-0 d-flex gap-2">
                        <a href="<?= AssetHelper::url('evangelism/' . (int)$record['id'] . '/edit') ?>" class="btn btn-sm btn-warning fw-semibold rounded-pill px-3 py-2 font-size-13 shadow-sm">
                            <i class="bx bx-edit me-1"></i> Edit
                        </a>
                        <a href="<?= AssetHelper::url('evangelism') ?>" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 fw-semibold font-size-13">
                            <i class="bx bx-arrow-back me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-file text-primary me-2 font-size-18"></i> Harvest Record Summary
                </h5>
                <span class="badge bg-soft-success text-success font-size-12 px-3 py-1 rounded-pill">
                    <i class="bx bx-check-circle me-1"></i> Verified Active
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <small class="text-muted text-uppercase fw-semibold font-size-11 d-block mb-1">Outreach Date</small>
                            <h4 class="fw-bold text-dark mb-0 font-size-18">
                                <i class="bx bx-calendar text-primary me-1"></i>
                                <?= date('F d, Y', strtotime($record['report_date'])) ?>
                            </h4>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-4" style="background: #fff3e0; border: 1px solid #fed7aa;">
                            <small class="text-muted text-uppercase fw-semibold font-size-11 d-block mb-1">Souls Won</small>
                            <h4 class="fw-bold mb-0 font-size-22" style="color: #e65100;">
                                <i class="bx bx-heart me-1"></i>
                                +<?= (int)$record['souls_won'] ?> Souls
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold font-size-13 text-dark mb-2">Location & Outreach Notes</label>
                    <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0; line-height: 1.6;">
                        <?= !empty($record['notes']) ? nl2br(htmlspecialchars($record['notes'])) : '<span class="text-muted fst-italic">No additional notes provided.</span>' ?>
                    </div>
                </div>

                <div class="row g-2 text-muted font-size-12 pt-3 border-top">
                    <div class="col-sm-6">
                        <i class="bx bx-time me-1"></i> Submitted: <strong><?= date('M d, Y H:i', strtotime($record['created_at'] ?? 'now')) ?></strong>
                    </div>
                    <?php if (!empty($record['updated_at']) && $record['updated_at'] !== $record['created_at']): ?>
                    <div class="col-sm-6 text-sm-end">
                        <i class="bx bx-pencil me-1"></i> Last Updated: <strong><?= date('M d, Y H:i', strtotime($record['updated_at'])) ?></strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer bg-light py-3 border-top rounded-bottom-4 d-flex justify-content-between align-items-center">
                <a href="<?= AssetHelper::url('evangelism') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bx bx-arrow-back me-1"></i> Back to Ledger
                </a>
                <form method="POST" action="<?= AssetHelper::url('evangelism/' . (int)$record['id'] . '/delete') ?>" onsubmit="return confirm('Are you sure you want to permanently delete this report?');">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Utilities\Security::generateCSRFToken()) ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                        <i class="bx bx-trash me-1"></i> Delete Report
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
