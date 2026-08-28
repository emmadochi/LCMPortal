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
                        <h2 class="text-white fw-bold mb-1 font-size-22">Log Evangelism Outreach & Converts</h2>
                        <p class="text-white-50 font-size-13 mb-0">Record outreach statistics and capture converts directly into your Follow-up Care pipeline.</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <a href="<?= AssetHelper::url('evangelism') ?>" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 fw-semibold font-size-13">
                            <i class="bx bx-arrow-back me-1"></i> Back to Journal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9 col-md-11">
        <form action="<?= AssetHelper::url('evangelism/store') ?>" method="POST">
            <input type="hidden" name="_token" value="<?= $csrf_token ?>">
            
            <!-- Section 1: Outreach Session Summary -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-map-pin text-primary me-2 font-size-18"></i> 1. Outreach Session Overview
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="report_date" class="form-label fw-semibold font-size-13 text-dark">Date of Outreach <span class="text-danger">*</span></label>
                            <input type="date" class="form-control rounded-3" id="report_date" name="report_date" value="<?= date('Y-m-d') ?>" required>
                            <small class="text-muted font-size-11">The day this outreach took place</small>
                        </div>
                        <div class="col-md-6">
                            <label for="souls_won" class="form-label fw-semibold font-size-13 text-dark">Total Souls Won <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-3" id="souls_won" name="souls_won" min="1" value="1" required>
                            <small class="text-muted font-size-11">Total number of decisions recorded</small>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="notes" class="form-label fw-semibold font-size-13 text-dark">Outreach Location & Session Summary</label>
                        <textarea class="form-control rounded-3" id="notes" name="notes" rows="3" placeholder="e.g. Community outreach at Main Market, preached gospel to shopkeepers, prayed for 5 people..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Convert Care Roster Details -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="bx bx-user-check text-success me-2 font-size-18"></i> 2. Converts Captured for Follow-Up Care
                        </h5>
                        <small class="text-muted font-size-12">Input names and contact points to automatically track their spiritual growth journey</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="addConvertRowBtn">
                        <i class="bx bx-plus me-1"></i> Add Another Convert
                    </button>
                </div>
                <div class="card-body p-4" id="convertsContainer">
                    <!-- Convert Row 1 -->
                    <div class="p-3 rounded-4 mb-3 convert-row" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark mb-0 font-size-13">Convert #1</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold font-size-12 text-dark">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="convert_name[]" class="form-control form-control-sm rounded-3" placeholder="e.g. John Doe">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold font-size-12 text-dark">Phone / WhatsApp</label>
                                <input type="tel" name="convert_phone[]" class="form-control form-control-sm rounded-3" placeholder="e.g. 08012345678">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold font-size-12 text-dark">Decision Type</label>
                                <select name="convert_decision[]" class="form-select form-select-sm rounded-3">
                                    <option value="salvation">Accepting Christ (Salvation)</option>
                                    <option value="rededication">Rededication</option>
                                    <option value="healing_miracle">Received Healing</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold font-size-12 text-dark">Prayer Request / Notes</label>
                                <input type="text" name="convert_prayer[]" class="form-control form-control-sm rounded-3" placeholder="e.g. Prayer for job, family peace...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pb-5">
                <a href="<?= AssetHelper::url('evangelism') ?>" class="btn btn-light rounded-pill px-4">Cancel</a>
                <button type="submit" class="btn btn-primary fw-semibold rounded-pill px-5 shadow-sm">
                    <i class="bx bx-check me-1"></i> Save Outreach & Open Follow-ups
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let convertIndex = 1;
    const container = document.getElementById('convertsContainer');
    const addBtn = document.getElementById('addConvertRowBtn');

    if (addBtn && container) {
        addBtn.addEventListener('click', function() {
            convertIndex++;
            const row = document.createElement('div');
            row.className = 'p-3 rounded-4 mb-3 convert-row';
            row.style.background = '#f8fafc';
            row.style.border = '1px solid #e2e8f0';
            row.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-dark mb-0 font-size-13">Convert #${convertIndex}</h6>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="this.closest('.convert-row').remove();">
                        <i class="bx bx-trash font-size-16"></i> Remove
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold font-size-12 text-dark">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="convert_name[]" class="form-control form-control-sm rounded-3" placeholder="e.g. Mary Okon">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold font-size-12 text-dark">Phone / WhatsApp</label>
                        <input type="tel" name="convert_phone[]" class="form-control form-control-sm rounded-3" placeholder="e.g. 08087654321">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold font-size-12 text-dark">Decision Type</label>
                        <select name="convert_decision[]" class="form-select form-select-sm rounded-3">
                            <option value="salvation">Accepting Christ (Salvation)</option>
                            <option value="rededication">Rededication</option>
                            <option value="healing_miracle">Received Healing</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold font-size-12 text-dark">Prayer Request / Notes</label>
                        <input type="text" name="convert_prayer[]" class="form-control form-control-sm rounded-3" placeholder="e.g. Growth in faith, healing...">
                    </div>
                </div>
            `;
            container.appendChild(row);
        });
    }
});
</script>
