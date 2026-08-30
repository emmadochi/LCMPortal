<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$convert = $convert ?? [];
$followupLogs = $followupLogs ?? [];

// Calculate milestone progress percentage
$milestonesTotal = 6;
$milestonesCompleted = 0;
if (!empty($convert['first_contact_done'])) $milestonesCompleted++;
if (!empty($convert['attended_service'])) $milestonesCompleted++;
if (!empty($convert['baptized_holy_ghost'])) $milestonesCompleted++;
if (!empty($convert['baptized_water'])) $milestonesCompleted++;
if (!empty($convert['foundation_class_enrolled'])) $milestonesCompleted++;
if (!empty($convert['department_joined'])) $milestonesCompleted++;

$milestonePercent = round(($milestonesCompleted / $milestonesTotal) * 100);
?>

<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm text-white overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); border-radius: 18px; border: 1px solid rgba(245, 158, 11, 0.2) !important;">
            <div class="card-body p-4 p-md-5 position-relative">
                <div class="d-flex flex-wrap justify-content-between align-items-center position-relative">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge px-3 py-1.5 rounded-pill font-size-12 fw-bold" style="background: rgba(245, 158, 11, 0.2); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.4);">
                                <i class="bx bx-user-check me-1 align-middle"></i> Convert Care & Discipleship
                            </span>
                            <span class="badge px-3 py-1.5 rounded-pill font-size-12 fw-semibold" style="background: rgba(255, 255, 255, 0.1); color: #e2e8f0;">
                                <?= ucfirst($convert['decision_type'] ?? 'Salvation') ?> &bull; Added <?= date('M d, Y', strtotime($convert['created_at'])) ?>
                            </span>
                        </div>
                        <h1 class="text-white fw-bold mb-1 font-size-28">
                            <?= htmlspecialchars($convert['full_name']) ?>
                        </h1>
                        <p class="text-white-50 font-size-14 mb-0">
                            Won to Christ by <strong class="text-white"><?= htmlspecialchars($convert['soul_winner_name'] ?? 'You') ?></strong> &bull; <?= htmlspecialchars($convert['church_name'] ?? 'Life Changers') ?>
                        </p>
                    </div>

                    <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2">
                        <?php if (!empty($convert['phone'])): ?>
                            <button type="button" onclick="openWhatsAppTemplateModal('<?= htmlspecialchars(addslashes($convert['full_name'])) ?>', '<?= htmlspecialchars(addslashes($convert['phone'])) ?>')" class="btn btn-success fw-semibold rounded-pill px-3 py-2 font-size-13 shadow-sm">
                                <i class="bx bxl-whatsapp me-1"></i> WhatsApp Message
                            </button>
                            <a href="tel:<?= htmlspecialchars($convert['phone']) ?>" class="btn btn-warning text-dark fw-bold rounded-pill px-3 py-2 font-size-13 shadow-sm">
                                <i class="bx bx-phone-call me-1"></i> Call Now
                            </a>
                        <?php endif; ?>
                        <a href="<?= AssetHelper::url('evangelism') ?>" class="btn btn-outline-light rounded-pill px-3 py-2 fw-semibold font-size-13">
                            <i class="bx bx-arrow-back me-1"></i> Back to Journal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Spiritual Journey Milestones & Quick Info -->
    <div class="col-lg-5">
        <!-- Spiritual Growth Journey Checklist -->
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-flame text-danger me-2 font-size-18"></i> Spiritual Growth Milestones
                </h5>
                <span class="badge bg-soft-primary text-primary font-size-12 fw-bold px-2.5 py-1"><?= $milestonePercent ?>% Growth</span>
            </div>
            <div class="card-body p-4">
                <p class="text-muted font-size-13 mb-3">Track <?= htmlspecialchars(explode(' ', $convert['full_name'])[0]) ?>'s spiritual integration into the body of Christ.</p>

                <!-- Milestone Progress Bar -->
                <div class="progress mb-4" style="height: 8px; border-radius: 6px;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $milestonePercent ?>%"></div>
                </div>

                <div class="list-group list-group-flush">
                    <!-- 1. First Follow-up Contact -->
                    <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs rounded-circle me-3 d-flex align-items-center justify-content-center <?= !empty($convert['first_contact_done']) ? 'bg-success text-white' : 'bg-light text-muted' ?>">
                                <i class="bx bx-phone font-size-16"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark font-size-13">1. Initial Follow-up Contact</h6>
                                <small class="text-muted font-size-11">Phone call, SMS, or in-person visit</small>
                            </div>
                        </div>
                        <form method="POST" action="<?= AssetHelper::url('evangelism/converts/' . (int)$convert['id'] . '/milestone') ?>">
                            <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                            <input type="hidden" name="milestone" value="first_contact_done">
                            <input type="hidden" name="value" value="<?= !empty($convert['first_contact_done']) ? '0' : '1' ?>">
                            <button type="submit" class="btn btn-sm rounded-pill <?= !empty($convert['first_contact_done']) ? 'btn-success' : 'btn-outline-secondary' ?>">
                                <i class="bx <?= !empty($convert['first_contact_done']) ? 'bx-check-double' : 'bx-check' ?>"></i>
                            </button>
                        </form>
                    </div>

                    <!-- 2. Attended Church Service -->
                    <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs rounded-circle me-3 d-flex align-items-center justify-content-center <?= !empty($convert['attended_service']) ? 'bg-success text-white' : 'bg-light text-muted' ?>">
                                <i class="bx bx-church font-size-16"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark font-size-13">2. Attended Church Service</h6>
                                <small class="text-muted font-size-11">Worshiped in Sunday / Midweek service</small>
                            </div>
                        </div>
                        <form method="POST" action="<?= AssetHelper::url('evangelism/converts/' . (int)$convert['id'] . '/milestone') ?>">
                            <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                            <input type="hidden" name="milestone" value="attended_service">
                            <input type="hidden" name="value" value="<?= !empty($convert['attended_service']) ? '0' : '1' ?>">
                            <button type="submit" class="btn btn-sm rounded-pill <?= !empty($convert['attended_service']) ? 'btn-success' : 'btn-outline-secondary' ?>">
                                <i class="bx <?= !empty($convert['attended_service']) ? 'bx-check-double' : 'bx-check' ?>"></i>
                            </button>
                        </form>
                    </div>

                    <!-- 3. Holy Ghost Baptism -->
                    <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs rounded-circle me-3 d-flex align-items-center justify-content-center <?= !empty($convert['baptized_holy_ghost']) ? 'bg-warning text-dark' : 'bg-light text-muted' ?>">
                                <i class="bx bx-flame font-size-16"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark font-size-13">3. Holy Ghost Baptism 🔥</h6>
                                <small class="text-muted font-size-11">Empowered with evidence of speaking in tongues</small>
                            </div>
                        </div>
                        <form method="POST" action="<?= AssetHelper::url('evangelism/converts/' . (int)$convert['id'] . '/milestone') ?>">
                            <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                            <input type="hidden" name="milestone" value="baptized_holy_ghost">
                            <input type="hidden" name="value" value="<?= !empty($convert['baptized_holy_ghost']) ? '0' : '1' ?>">
                            <button type="submit" class="btn btn-sm rounded-pill <?= !empty($convert['baptized_holy_ghost']) ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' ?>">
                                <i class="bx <?= !empty($convert['baptized_holy_ghost']) ? 'bx-check-double' : 'bx-check' ?>"></i>
                            </button>
                        </form>
                    </div>

                    <!-- 4. Water Baptism -->
                    <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs rounded-circle me-3 d-flex align-items-center justify-content-center <?= !empty($convert['baptized_water']) ? 'bg-info text-white' : 'bg-light text-muted' ?>">
                                <i class="bx bx-water font-size-16"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark font-size-13">4. Water Baptism 💧</h6>
                                <small class="text-muted font-size-11">Baptized by immersion in Jesus' name</small>
                            </div>
                        </div>
                        <form method="POST" action="<?= AssetHelper::url('evangelism/converts/' . (int)$convert['id'] . '/milestone') ?>">
                            <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                            <input type="hidden" name="milestone" value="baptized_water">
                            <input type="hidden" name="value" value="<?= !empty($convert['baptized_water']) ? '0' : '1' ?>">
                            <button type="submit" class="btn btn-sm rounded-pill <?= !empty($convert['baptized_water']) ? 'btn-info text-white' : 'btn-outline-secondary' ?>">
                                <i class="bx <?= !empty($convert['baptized_water']) ? 'bx-check-double' : 'bx-check' ?>"></i>
                            </button>
                        </form>
                    </div>

                    <!-- 5. Believers / Foundation Class -->
                    <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs rounded-circle me-3 d-flex align-items-center justify-content-center <?= !empty($convert['foundation_class_enrolled']) ? 'bg-primary text-white' : 'bg-light text-muted' ?>">
                                <i class="bx bx-book-open font-size-16"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark font-size-13">5. Foundation / Believers Class</h6>
                                <small class="text-muted font-size-11">Enrolled in new convert discipleship</small>
                            </div>
                        </div>
                        <form method="POST" action="<?= AssetHelper::url('evangelism/converts/' . (int)$convert['id'] . '/milestone') ?>">
                            <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                            <input type="hidden" name="milestone" value="foundation_class_enrolled">
                            <input type="hidden" name="value" value="<?= !empty($convert['foundation_class_enrolled']) ? '0' : '1' ?>">
                            <button type="submit" class="btn btn-sm rounded-pill <?= !empty($convert['foundation_class_enrolled']) ? 'btn-primary' : 'btn-outline-secondary' ?>">
                                <i class="bx <?= !empty($convert['foundation_class_enrolled']) ? 'bx-check-double' : 'bx-check' ?>"></i>
                            </button>
                        </form>
                    </div>

                    <!-- 6. Integrated into Ministry Department -->
                    <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs rounded-circle me-3 d-flex align-items-center justify-content-center <?= !empty($convert['department_joined']) ? 'text-white' : 'bg-light text-muted' ?>" style="<?= !empty($convert['department_joined']) ? 'background:#7b1fa2;' : '' ?>">
                                <i class="bx bx-group font-size-16"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark font-size-13">6. Ministry Unit / Discipled 🕊️</h6>
                                <small class="text-muted font-size-11"><?= !empty($convert['department_joined']) ? 'Joined ' . htmlspecialchars($convert['department_joined']) : 'Joined a service unit/department' ?></small>
                            </div>
                        </div>
                        <form method="POST" action="<?= AssetHelper::url('evangelism/converts/' . (int)$convert['id'] . '/milestone') ?>">
                            <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                            <input type="hidden" name="milestone" value="department_joined">
                            <input type="hidden" name="value" value="<?= !empty($convert['department_joined']) ? '' : 'Evangelism & Outreach' ?>">
                            <button type="submit" class="btn btn-sm rounded-pill <?= !empty($convert['department_joined']) ? 'btn-dark' : 'btn-outline-secondary' ?>">
                                <i class="bx <?= !empty($convert['department_joined']) ? 'bx-check-double' : 'bx-check' ?>"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Discipleship & Assigned Carer Card -->
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-user-pin text-primary me-2 font-size-18"></i> Follow-Up Carer / Mentor
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center me-3 font-size-18 fw-bold">
                        <?= !empty($convert['assigned_mentor_name']) ? strtoupper(substr($convert['assigned_mentor_name'], 0, 1)) : strtoupper(substr($convert['soul_winner_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">
                            <?= htmlspecialchars($convert['assigned_mentor_name'] ?? ($convert['soul_winner_name'] ?? 'Soul Winner')) ?>
                        </h6>
                        <small class="text-muted">
                            <?= !empty($convert['assigned_mentor_name']) ? 'Assigned Discipleship Mentor' : 'Original Soul Winner' ?>
                        </small>
                    </div>
                </div>

                <?php if (!empty($isAdminOrPastor)): ?>
                    <form method="POST" action="<?= AssetHelper::url('evangelism/converts/' . (int)$convert['id'] . '/assign') ?>" class="mt-3 pt-3 border-top">
                        <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                        <label class="form-label font-size-12 fw-semibold text-muted mb-1">Reassign Follow-Up To Church Member:</label>
                        <div class="input-group">
                            <select name="assigned_mentor_id" class="form-select form-select-sm">
                                <option value="">-- Revert to Soul Winner --</option>
                                <?php foreach ($churchMembers as $m): ?>
                                    <option value="<?= $m['id'] ?>" <?= ((int)($convert['assigned_mentor_id'] ?? 0) === (int)$m['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($m['first_name'] . ' ' . $m['last_name'] . ' (' . ($m['email'] ?? '') . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bx bx-check"></i> Assign
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contact & Profile Details -->
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-id-card text-muted me-2 font-size-18"></i> Contact & Prayer Details
                </h5>
            </div>
            <div class="card-body p-4">
                <ul class="list-unstyled mb-0 font-size-13">
                    <li class="mb-3 d-flex align-items-center">
                        <i class="bx bx-phone text-primary me-2 font-size-18"></i>
                        <span class="text-muted me-2">Phone:</span>
                        <strong class="text-dark"><?= htmlspecialchars($convert['phone'] ?? 'Not Provided') ?></strong>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="bx bx-envelope text-primary me-2 font-size-18"></i>
                        <span class="text-muted me-2">Email:</span>
                        <strong class="text-dark"><?= htmlspecialchars($convert['email'] ?? 'Not Provided') ?></strong>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bx bx-map-pin text-primary me-2 font-size-18 mt-1"></i>
                        <div>
                            <span class="text-muted me-2">Address:</span>
                            <strong class="text-dark"><?= htmlspecialchars($convert['address'] ?? 'Not Provided') ?></strong>
                        </div>
                    </li>
                    <li class="mb-0">
                        <span class="text-muted d-block mb-1">Prayer Requests & Needs:</span>
                        <div class="p-3 rounded-3 bg-light text-dark" style="line-height: 1.5;">
                            <?= !empty($convert['prayer_requests']) ? nl2br(htmlspecialchars($convert['prayer_requests'])) : '<span class="text-muted fst-italic">No specific prayer points recorded.</span>' ?>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Column: Follow-up Timeline & New Touchpoint Logger -->
    <div class="col-lg-7">
        <!-- New Follow-up Touchpoint Logger -->
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-plus-circle text-primary me-2 font-size-18"></i> Log Follow-up Touchpoint
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= AssetHelper::url('evangelism/converts/' . (int)$convert['id'] . '/followup') ?>">
                    <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold font-size-13 text-dark">Contact Method</label>
                            <select name="contact_method" class="form-select rounded-3" required>
                                <option value="phone_call">📞 Phone Call</option>
                                <option value="whatsapp_sms">💬 WhatsApp / SMS Message</option>
                                <option value="home_visit">🏡 In-Person / Home Visitation</option>
                                <option value="church_meeting">⛪ Church Service Meeting</option>
                                <option value="prayer_session">🙏 One-on-One Prayer Session</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold font-size-13 text-dark">Conversation Outcome</label>
                            <select name="outcome" class="form-select rounded-3" required>
                                <option value="reached_receptive">✅ Receptive & Welcomed Prayer</option>
                                <option value="attended_service">⛪ Attended Church Service</option>
                                <option value="reached_busy">⏳ Reached but Busy (Reschedule)</option>
                                <option value="not_answering">📴 Not Answering / Line Busy</option>
                                <option value="prayer_answered">🙌 Testimony / Prayer Answered</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13 text-dark">Conversation Summary & Prayer Notes</label>
                        <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="Notes on how the convert is doing spiritually, scriptures shared, prayer answered..."></textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold font-size-13 text-dark">Next Scheduled Follow-up Date</label>
                            <input type="date" name="next_action_date" class="form-control rounded-3" value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold font-size-13 text-dark">Update Milestone (Optional)</label>
                            <select name="milestone_updated" class="form-select rounded-3">
                                <option value="">-- No Milestone Update --</option>
                                <option value="attended_service">⛪ Attended Church Service</option>
                                <option value="baptized_holy_ghost">🔥 Baptized with Holy Ghost</option>
                                <option value="baptized_water">💧 Water Baptism</option>
                                <option value="foundation_class_enrolled">📖 Enrolled in Believers Class</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary fw-semibold rounded-pill px-4 shadow-sm">
                            <i class="bx bx-check me-1"></i> Save Touchpoint
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Follow-up Activity History Timeline -->
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-history text-muted me-2 font-size-18"></i> Follow-up Touchpoint History
                </h5>
                <span class="badge bg-soft-secondary text-secondary font-size-12"><?= count($followupLogs) ?> Contact<?= count($followupLogs) === 1 ? '' : 's' ?></span>
            </div>
            <div class="card-body p-4">
                <?php if (empty($followupLogs)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bx bx-conversation font-size-36 opacity-50 mb-2 d-block"></i>
                        <h6 class="fw-semibold text-dark">No follow-up touchpoints logged yet</h6>
                        <p class="font-size-13 text-muted">Use the form above to record your first phone call, visit, or prayer conversation with <?= htmlspecialchars(explode(' ', $convert['full_name'])[0]) ?>.</p>
                    </div>
                <?php else: ?>
                    <div class="timeline-wrapper">
                        <?php foreach ($followupLogs as $log): ?>
                            <div class="d-flex mb-4 position-relative">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-xs rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center font-size-16">
                                        <i class="bx bx-check"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 p-3 rounded-3 bg-light border">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 fw-bold text-dark font-size-13">
                                            <?= ucwords(str_replace('_', ' ', $log['contact_method'])) ?> &bull; 
                                            <span class="badge bg-soft-success text-success font-size-11"><?= ucwords(str_replace('_', ' ', $log['outcome'])) ?></span>
                                        </h6>
                                        <small class="text-muted font-size-11"><?= date('M d, Y H:i', strtotime($log['created_at'])) ?></small>
                                    </div>
                                    <p class="mb-1 text-muted font-size-13" style="line-height: 1.5;">
                                        <?= !empty($log['notes']) ? nl2br(htmlspecialchars($log['notes'])) : '<span class="fst-italic">Follow-up recorded</span>' ?>
                                    </p>
                                    <?php if (!empty($log['next_action_date'])): ?>
                                        <small class="text-primary font-size-11 fw-semibold d-block mt-1">
                                            <i class="bx bx-calendar-event me-1"></i> Next Follow-up Scheduled: <?= date('M d, Y', strtotime($log['next_action_date'])) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
