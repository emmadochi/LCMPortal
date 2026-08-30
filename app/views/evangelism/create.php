<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$myConverts = $myConverts ?? [];
$selectedConvertId = (int)($selectedConvertId ?? 0);
$mode = $mode ?? (!empty($myConverts) ? 'followup' : 'new_soul');
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
                                <i class="bx bx-heart me-1 align-middle"></i> Soul Care & Harvest Center
                            </span>
                        </div>
                        <h2 class="text-white fw-bold mb-1 font-size-22">Follow-Up Results & Soul Logging</h2>
                        <p class="text-white-50 font-size-13 mb-0">Record follow-up results on your assigned converts, book upcoming care dates, or log brand new souls.</p>
                    </div>
                    <div class="mt-3 mt-md-0 d-flex gap-2">
                        <a href="<?= AssetHelper::url('follow-ups') ?>" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 fw-semibold font-size-13">
                            <i class="bx bx-arrow-back me-1"></i> My Follow-up Pipeline
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="row justify-content-center mb-4">
    <div class="col-lg-10 col-md-12">
        <ul class="nav nav-pills nav-justified p-2 bg-white rounded-4 shadow-sm border" id="careTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill py-2.5 fw-semibold font-size-14 <?= $mode === 'followup' ? 'active' : '' ?>" id="followup-tab" data-bs-toggle="pill" data-bs-target="#followup-pane" type="button" role="tab" aria-controls="followup-pane" aria-selected="<?= $mode === 'followup' ? 'true' : 'false' ?>">
                    <i class="bx bx-phone-call me-1 font-size-16 align-middle"></i> Record Follow-up on Assigned Soul
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill py-2.5 fw-semibold font-size-14 <?= $mode === 'new_soul' ? 'active' : '' ?>" id="newsoul-tab" data-bs-toggle="pill" data-bs-target="#newsoul-pane" type="button" role="tab" aria-controls="newsoul-pane" aria-selected="<?= $mode === 'new_soul' ? 'true' : 'false' ?>">
                    <i class="bx bx-user-plus me-1 font-size-16 align-middle"></i> Log Brand New Soul Won
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill py-2.5 fw-semibold font-size-14 <?= $mode === 'bulk_outreach' ? 'active' : '' ?>" id="outreach-tab" data-bs-toggle="pill" data-bs-target="#outreach-pane" type="button" role="tab" aria-controls="outreach-pane" aria-selected="<?= $mode === 'bulk_outreach' ? 'true' : 'false' ?>">
                    <i class="bx bx-group me-1 font-size-16 align-middle"></i> Bulk Outreach Report
                </button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content" id="careTabsContent">
    <!-- Tab 1: Record Follow-up on Assigned / Won Soul -->
    <div class="tab-pane fade <?= $mode === 'followup' ? 'show active' : '' ?>" id="followup-pane" role="tabpanel" aria-labelledby="followup-tab">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-11">
                <?php if (empty($myConverts)): ?>
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
                        <i class="bx bx-user-x font-size-48 text-muted mb-3"></i>
                        <h5 class="fw-bold text-dark mb-1">No Souls in Your Care List Yet</h5>
                        <p class="text-muted font-size-14 mb-4">You haven't logged any souls or been assigned converts by your Pastor yet. Log your first soul below!</p>
                        <div>
                            <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" onclick="document.getElementById('newsoul-tab').click();">
                                <i class="bx bx-plus me-1"></i> Log a Soul Won Now
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <form action="<?= AssetHelper::url('evangelism/followup/record') ?>" method="POST">
                        <input type="hidden" name="_token" value="<?= $csrf_token ?>">

                        <!-- Step 1: Select the Soul -->
                        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                                    <i class="bx bx-user text-primary me-2 font-size-18"></i> 1. Select Soul from Your Care Pipeline
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label for="convert_id" class="form-label fw-semibold font-size-13 text-dark">
                                        Choose Convert / Soul to Follow Up <span class="text-danger">*</span>
                                    </label>
                                    <select name="convert_id" id="convert_id" class="form-select form-select-lg rounded-3" required onchange="updateConvertPreview(this.value)">
                                        <option value="">-- Choose a Soul from your care list (<?= count($myConverts) ?> available) --</option>
                                        <?php foreach ($myConverts as $c): ?>
                                            <?php 
                                            $isWon = (int)($c['soul_winner_id'] ?? 0) === (int)$this->session->get('user_id');
                                            $selected = ($selectedConvertId === (int)$c['id']) ? 'selected' : '';
                                            ?>
                                            <option value="<?= (int)$c['id'] ?>" 
                                                    data-phone="<?= htmlspecialchars($c['phone'] ?? '') ?>"
                                                    data-decision="<?= htmlspecialchars(ucfirst($c['decision_type'] ?? 'Salvation')) ?>"
                                                    data-source="<?= $isWon ? 'Won by You' : 'Assigned by Pastor' ?>"
                                                    data-first-contact="<?= !empty($c['first_contact_done']) ? '1' : '0' ?>"
                                                    data-service="<?= !empty($c['attended_service']) ? '1' : '0' ?>"
                                                    data-holy-ghost="<?= !empty($c['baptized_holy_ghost']) ? '1' : '0' ?>"
                                                    data-water="<?= !empty($c['baptized_water']) ? '1' : '0' ?>"
                                                    data-foundation="<?= !empty($c['foundation_class_enrolled']) ? '1' : '0' ?>"
                                                    data-next-date="<?= htmlspecialchars($c['next_followup_date'] ?? '') ?>"
                                                    data-prayer="<?= htmlspecialchars($c['prayer_requests'] ?? '') ?>"
                                                    <?= $selected ?>>
                                                <?= htmlspecialchars($c['full_name']) ?> <?= !empty($c['phone']) ? ' (' . htmlspecialchars($c['phone']) . ')' : '' ?> - [<?= $isWon ? 'Won by You' : 'Assigned by Pastor' ?>]
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Dynamic Convert Quick Info Card -->
                                <div id="convertPreviewCard" class="p-3 rounded-3 bg-light border mt-3" style="display: none;">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                        <div>
                                            <span class="badge bg-primary-subtle text-primary font-size-11" id="previewSource">Won by You</span>
                                            <span class="badge bg-secondary-subtle text-secondary font-size-11" id="previewDecision">Salvation</span>
                                        </div>
                                        <div class="d-flex gap-1" id="previewActionButtons">
                                            <!-- Dynamically injected WhatsApp / Call buttons -->
                                        </div>
                                    </div>
                                    <div class="font-size-12 text-muted mb-2">
                                        <strong>Phone:</strong> <span id="previewPhone" class="text-dark me-3">-</span>
                                        <strong>Next Planned Date:</strong> <span id="previewNextDate" class="text-dark">-</span>
                                    </div>
                                    <div id="previewPrayerSection" class="font-size-12 text-muted" style="display: none;">
                                        <strong>Prayer Points:</strong> <span id="previewPrayerText" class="fst-italic text-dark"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Follow-up Interaction Details & Result -->
                        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                                    <i class="bx bx-message-dots text-success me-2 font-size-18"></i> 2. Follow-Up Touchpoint & Outcome Result
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="contact_method" class="form-label fw-semibold font-size-13 text-dark">
                                            Contact Channel / Method <span class="text-danger">*</span>
                                        </label>
                                        <select name="contact_method" id="contact_method" class="form-select rounded-3" required>
                                            <option value="phone_call">📞 Phone Call</option>
                                            <option value="whatsapp_sms">💬 WhatsApp / SMS</option>
                                            <option value="home_visit">🏠 In-Person / Home Visit</option>
                                            <option value="church_meeting">⛪ Church Service Meeting</option>
                                            <option value="prayer_session">🙏 One-on-One Prayer Session</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="outcome" class="form-label fw-semibold font-size-13 text-dark">
                                            Follow-Up Result & Status <span class="text-danger">*</span>
                                        </label>
                                        <select name="outcome" id="outcome" class="form-select rounded-3" required>
                                            <option value="reached_receptive">✅ Receptive & Growing in Faith</option>
                                            <option value="attended_church">⛪ Attended Church Service</option>
                                            <option value="holy_ghost_baptized">🔥 Received Holy Ghost Baptism</option>
                                            <option value="water_baptized">💧 Baptized in Water</option>
                                            <option value="foundation_school_enrolled">📖 Enrolled in Foundation Class</option>
                                            <option value="busy_call_back">⏳ Reached but Busy (Call Back)</option>
                                            <option value="not_answering">📵 Not Answering / Voicemail</option>
                                            <option value="prayer_answered">🙌 Prayer Answered / Miracle Received</option>
                                            <option value="needs_pastoral_visit">⚠️ Needs Pastoral Visit / Counseling</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Spiritual Milestones Checkboxes -->
                                <div class="mb-3 p-3 rounded-3 bg-light border">
                                    <label class="form-label fw-bold font-size-13 text-dark mb-2">
                                        <i class="bx bx-check-double text-success me-1"></i> Spiritual Growth Milestones Achieved
                                    </label>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="milestones[attended_service]" value="1" id="m_attended_service">
                                                <label class="form-check-label font-size-13 text-dark" for="m_attended_service">
                                                    Attended Church Service
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="milestones[baptized_holy_ghost]" value="1" id="m_baptized_holy_ghost">
                                                <label class="form-check-label font-size-13 text-dark" for="m_baptized_holy_ghost">
                                                    Baptized in Holy Ghost 🔥
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="milestones[baptized_water]" value="1" id="m_baptized_water">
                                                <label class="form-check-label font-size-13 text-dark" for="m_baptized_water">
                                                    Water Baptized 💧
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="milestones[foundation_class_enrolled]" value="1" id="m_foundation_class_enrolled">
                                                <label class="form-check-label font-size-13 text-dark" for="m_foundation_class_enrolled">
                                                    Enrolled in Foundation Class 📖
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label fw-semibold font-size-13 text-dark">
                                        Follow-Up Notes, Discussion & Prayer Requests
                                    </label>
                                    <textarea name="notes" id="notes" rows="3" class="form-control rounded-3" placeholder="e.g. Spoke with convert, shared scriptures from John 15, prayed for family health, confirmed attendance for Sunday..."></textarea>
                                </div>

                                <!-- Book Next Follow-up Date -->
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="next_action_date" class="form-label fw-semibold font-size-13 text-dark">
                                            <i class="bx bx-calendar-plus text-primary me-1"></i> Book / Schedule Next Follow-Up Date
                                        </label>
                                        <input type="date" name="next_action_date" id="next_action_date" class="form-control rounded-3" value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
                                        <small class="text-muted font-size-11">When you plan to call or visit this soul again</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pb-5">
                            <a href="<?= AssetHelper::url('follow-ups') ?>" class="btn btn-light rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary fw-semibold rounded-pill px-5 shadow-sm">
                                <i class="bx bx-check me-1"></i> Save Follow-up & Book Next Date
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tab 2: Log Brand New Soul Won -->
    <div class="tab-pane fade <?= $mode === 'new_soul' ? 'show active' : '' ?>" id="newsoul-pane" role="tabpanel" aria-labelledby="newsoul-tab">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-11">
                <form action="<?= AssetHelper::url('evangelism/converts/store') ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">

                    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="bx bx-user-plus text-primary me-2 font-size-18"></i> New Convert Information
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="new_full_name" class="form-label fw-semibold font-size-13 text-dark">Convert Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control rounded-3" id="new_full_name" name="full_name" placeholder="e.g. Brother Emmanuel" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="new_phone" class="form-label fw-semibold font-size-13 text-dark">Phone / WhatsApp</label>
                                    <input type="tel" class="form-control rounded-3" id="new_phone" name="phone" placeholder="e.g. 08012345678">
                                </div>
                                <div class="col-md-6">
                                    <label for="new_email" class="form-label fw-semibold font-size-13 text-dark">Email Address</label>
                                    <input type="email" class="form-control rounded-3" id="new_email" name="email" placeholder="e.g. convert@example.com">
                                </div>
                                <div class="col-md-6">
                                    <label for="new_decision_type" class="form-label fw-semibold font-size-13 text-dark">Decision Made</label>
                                    <select class="form-select rounded-3" id="new_decision_type" name="decision_type">
                                        <option value="salvation">Accepting Christ (Salvation)</option>
                                        <option value="rededication">Rededication to God</option>
                                        <option value="healing_miracle">Received Healing / Miracle</option>
                                        <option value="inquiry">Spiritual Inquiry / Seeker</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="new_address" class="form-label fw-semibold font-size-13 text-dark">Residential Address / Location</label>
                                    <input type="text" class="form-control rounded-3" id="new_address" name="address" placeholder="e.g. 14 Market Road, City Centre">
                                </div>
                                <div class="col-12">
                                    <label for="new_prayer_requests" class="form-label fw-semibold font-size-13 text-dark">Prayer Requests & Needs</label>
                                    <textarea class="form-control rounded-3" id="new_prayer_requests" name="prayer_requests" rows="3" placeholder="e.g. Healing, job breakthrough, family peace..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="new_next_date" class="form-label fw-semibold font-size-13 text-dark">Initial Follow-up Date</label>
                                    <input type="date" class="form-control rounded-3" id="new_next_date" name="next_followup_date" value="<?= date('Y-m-d', strtotime('+2 days')) ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pb-5">
                        <a href="<?= AssetHelper::url('follow-ups') ?>" class="btn btn-light rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary fw-semibold rounded-pill px-5 shadow-sm">
                            <i class="bx bx-check me-1"></i> Add Soul to My Care Pipeline
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tab 3: Bulk Outreach Session Report -->
    <div class="tab-pane fade <?= $mode === 'bulk_outreach' ? 'show active' : '' ?>" id="outreach-pane" role="tabpanel" aria-labelledby="outreach-tab">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-11">
                <form action="<?= AssetHelper::url('evangelism/store') ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                    
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
                                </div>
                                <div class="col-md-6">
                                    <label for="souls_won" class="form-label fw-semibold font-size-13 text-dark">Total Souls Won <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control rounded-3" id="souls_won" name="souls_won" min="1" value="1" required>
                                </div>
                            </div>
                            <div class="mb-0">
                                <label for="notes_bulk" class="form-label fw-semibold font-size-13 text-dark">Outreach Location & Session Summary</label>
                                <textarea class="form-control rounded-3" id="notes_bulk" name="notes" rows="3" placeholder="e.g. Community outreach at Main Market, preached gospel to shopkeepers, prayed for 5 people..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                                    <i class="bx bx-user-check text-success me-2 font-size-18"></i> 2. Converts Captured
                                </h5>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="addConvertRowBtn">
                                <i class="bx bx-plus me-1"></i> Add Another Convert
                            </button>
                        </div>
                        <div class="card-body p-4" id="convertsContainer">
                            <div class="p-3 rounded-4 mb-3 convert-row" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold text-dark mb-0 font-size-13">Convert #1</h6>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold font-size-12 text-dark">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="convert_name[]" class="form-control form-control-sm rounded-3" placeholder="e.g. John Doe">
                                    </div>
                                    <div class="col-md-4">
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
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold font-size-12 text-dark">House / Residential Address</label>
                                        <input type="text" name="convert_address[]" class="form-control form-control-sm rounded-3" placeholder="e.g. 14 Market Road, City Centre">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold font-size-12 text-dark">Prayer Request / Notes</label>
                                        <input type="text" name="convert_prayer[]" class="form-control form-control-sm rounded-3" placeholder="e.g. Prayer for job, family peace...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pb-5">
                        <a href="<?= AssetHelper::url('follow-ups') ?>" class="btn btn-light rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary fw-semibold rounded-pill px-5 shadow-sm">
                            <i class="bx bx-check me-1"></i> Save Outreach & Capture Converts
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateConvertPreview(convertId) {
    const select = document.getElementById('convert_id');
    const preview = document.getElementById('convertPreviewCard');
    if (!select || !preview) return;

    if (!convertId) {
        preview.style.display = 'none';
        return;
    }

    const opt = select.options[select.selectedIndex];
    const phone = opt.getAttribute('data-phone') || '';
    const decision = opt.getAttribute('data-decision') || '';
    const source = opt.getAttribute('data-source') || '';
    const nextDate = opt.getAttribute('data-next-date') || 'None scheduled';
    const prayer = opt.getAttribute('data-prayer') || '';

    document.getElementById('previewSource').textContent = source;
    document.getElementById('previewDecision').textContent = decision;
    document.getElementById('previewPhone').textContent = phone || 'Not provided';
    document.getElementById('previewNextDate').textContent = nextDate;

    // Checkbox updates based on convert's existing milestones
    const cbService = document.getElementById('m_attended_service');
    const cbHolyGhost = document.getElementById('m_baptized_holy_ghost');
    const cbWater = document.getElementById('m_baptized_water');
    const cbFoundation = document.getElementById('m_foundation_class_enrolled');

    if (cbService) cbService.checked = opt.getAttribute('data-service') === '1';
    if (cbHolyGhost) cbHolyGhost.checked = opt.getAttribute('data-holy-ghost') === '1';
    if (cbWater) cbWater.checked = opt.getAttribute('data-water') === '1';
    if (cbFoundation) cbFoundation.checked = opt.getAttribute('data-foundation') === '1';

    // Build action buttons (WhatsApp, Call)
    const btnContainer = document.getElementById('previewActionButtons');
    btnContainer.innerHTML = '';
    if (phone) {
        const convertName = opt.text ? opt.text.split(' - ')[0].replace(/\(.*?\)/, '').trim() : 'Convert';
        btnContainer.innerHTML = `
            <button type="button" onclick="openWhatsAppTemplateModal('${convertName.replace(/'/g, "\\'")}', '${phone}')" class="btn btn-sm btn-success py-0 px-2 rounded-pill font-size-11">
                <i class="bx bxl-whatsapp"></i> Chat
            </button>
            <a href="tel:${phone}" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill font-size-11">
                <i class="bx bx-phone"></i> Call
            </a>
        `;
    }

    // Prayer points section
    const prayerSection = document.getElementById('previewPrayerSection');
    const prayerText = document.getElementById('previewPrayerText');
    if (prayer) {
        prayerText.textContent = prayer;
        prayerSection.style.display = 'block';
    } else {
        prayerSection.style.display = 'none';
    }

    preview.style.display = 'block';
}

document.addEventListener('DOMContentLoaded', function() {
    // If convert pre-selected, trigger preview update
    const select = document.getElementById('convert_id');
    if (select && select.value) {
        updateConvertPreview(select.value);
    }

    // Bulk outreach dynamic rows
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
                    <div class="col-md-4">
                        <label class="form-label fw-semibold font-size-12 text-dark">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="convert_name[]" class="form-control form-control-sm rounded-3" placeholder="e.g. Mary Okon">
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-6">
                        <label class="form-label fw-semibold font-size-12 text-dark">House / Residential Address</label>
                        <input type="text" name="convert_address[]" class="form-control form-control-sm rounded-3" placeholder="e.g. 14 Market Road, City Centre">
                    </div>
                    <div class="col-md-6">
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
