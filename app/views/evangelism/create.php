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
                                <i class="bx bx-plus-circle me-1 align-middle"></i> New Harvest Entry
                            </span>
                        </div>
                        <h2 class="text-white fw-bold mb-1 font-size-22">Log Evangelism & Soul Winning Report</h2>
                        <p class="text-white-50 font-size-13 mb-0">Record personal or department soul winning outreach results into the portal ledger.</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <a href="<?= AssetHelper::url('evangelism') ?>" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 fw-semibold font-size-13">
                            <i class="bx bx-arrow-back me-1"></i> Back to Reports
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
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-edit-alt text-primary me-2 font-size-18"></i> Outreach Details
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= AssetHelper::url('evangelism/store') ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="report_date" class="form-label fw-semibold font-size-13 text-dark">Date of Outreach <span class="text-danger">*</span></label>
                            <input type="date" class="form-control rounded-3" id="report_date" name="report_date" value="<?= date('Y-m-d') ?>" required>
                            <small class="text-muted font-size-11">The day this outreach took place</small>
                        </div>
                        <div class="col-md-6">
                            <label for="souls_won" class="form-label fw-semibold font-size-13 text-dark">Souls Won <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-3" id="souls_won" name="souls_won" min="1" value="1" required>
                            <small class="text-muted font-size-11">Number of individuals led to Christ</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold font-size-13 text-dark">Outreach Location & Follow-Up Notes</label>
                        <textarea class="form-control rounded-3" id="notes" name="notes" rows="4" placeholder="Mention outreach venue (e.g., Campus, Street, Hospital), names or contact points for follow-up..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="<?= AssetHelper::url('evangelism') ?>" class="btn btn-light rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary fw-semibold rounded-pill px-4 shadow-sm">
                            <i class="bx bx-check me-1"></i> Save Evangelism Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
