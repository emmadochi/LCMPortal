<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$reports = $reports ?? [];
$converts = $converts ?? [];
$careStats = $careStats ?? [];
$commendations = $commendations ?? [];
$userStats = $userStats ?? [];

$totalSouls = (int)($userStats['total_souls'] ?? 0);
$totalLogs = (int)($userStats['total_logs'] ?? 0);
$convertsCount = count($converts);
$attendedCount = (int)($careStats['attended_church_count'] ?? 0);
$holyGhostCount = (int)($careStats['holy_ghost_baptized_count'] ?? 0);
?>

<!-- Executive Royal Hero Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm text-white overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); border-radius: 18px; border: 1px solid rgba(245, 158, 11, 0.2) !important;">
            <div class="card-body p-4 p-md-5 position-relative">
                <!-- Ambient Glow Elements -->
                <div style="position: absolute; right: -30px; top: -30px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(245, 158, 11, 0.2) 0%, rgba(245, 158, 11, 0) 70%); border-radius: 50%;"></div>
                <div style="position: absolute; left: 15%; bottom: -40px; width: 180px; height: 180px; background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(99, 102, 241, 0) 70%); border-radius: 50%;"></div>

                <div class="row align-items-center position-relative">
                    <div class="col-lg-7 col-md-12 mb-4 mb-lg-0">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge px-3 py-1.5 rounded-pill font-size-12 fw-bold" style="background: rgba(245, 158, 11, 0.2); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.4);">
                                <i class="bx bx-flame me-1 align-middle"></i> Personal Harvest & Care Journal
                            </span>
                            <span class="badge px-3 py-1.5 rounded-pill font-size-12 fw-semibold" style="background: rgba(255, 255, 255, 0.1); color: #e2e8f0; border: 1px solid rgba(255, 255, 255, 0.15);">
                                <?= $convertsCount ?> Convert<?= $convertsCount === 1 ? '' : 's' ?> in Follow-up
                            </span>
                        </div>
                        <h1 class="text-white fw-bold mb-2 font-size-28">
                            My Evangelism & Soul Winning Logs ✨
                        </h1>
                        <p class="text-white-50 font-size-14 mb-0" style="max-width: 600px; line-height: 1.6;">
                            "Go ye into all the world, and preach the gospel to every creature." &mdash; <span class="text-white">Mark 16:15</span>
                        </p>
                    </div>

                    <!-- Action Bar -->
                    <div class="col-lg-5 col-md-12 text-lg-end">
                        <div class="d-flex flex-wrap justify-content-lg-end align-items-center gap-2">
                            <a href="<?= AssetHelper::url('evangelism/leaderboard') ?>" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 fw-semibold font-size-13 shadow-sm">
                                <i class="bx bx-trophy text-warning me-1"></i> Soul Leaderboard
                            </a>

                            <button type="button" class="btn btn-warning fw-bold text-dark rounded-pill px-3 py-2 shadow-sm font-size-13" data-bs-toggle="modal" data-bs-target="#newConvertModal">
                                <i class="bx bx-user-plus me-1"></i> Add Convert
                            </button>

                            <button type="button" class="btn btn-outline-light rounded-pill px-3 py-2 fw-semibold font-size-13" data-bs-toggle="modal" data-bs-target="#newReportModal">
                                <i class="bx bx-plus me-1"></i> Log Session
                            </button>

                            <a href="<?= AssetHelper::url('evangelism/export?format=csv') ?>" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 fw-semibold font-size-13">
                                <i class="bx bx-download me-1"></i> CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pastoral Commendation Banner (If any) -->
<?php if (!empty($commendations)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 5px solid #f59e0b !important;">
            <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center me-3" style="background: #fef3c7; color: #b45309;">
                        <i class="bx bx-crown font-size-22"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0 font-size-14">
                            🎖️ Pastoral Commendation: "<?= htmlspecialchars($commendations[0]['message']) ?>"
                        </h6>
                        <small class="text-muted font-size-12">&mdash; Pastor <?= htmlspecialchars($commendations[0]['pastor_name'] ?? 'Leadership') ?> &bull; <?= date('M d, Y', strtotime($commendations[0]['created_at'])) ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Four Personal Metric KPI Cards -->
<div class="row g-3 mb-4">
    <!-- Lifetime Souls Won -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white stat-card-hover">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #fff3e0; color: #e65100;">
                            <i class="bx bx-heart font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Lifetime Souls Won</p>
                        <h3 class="mb-0 fw-bold text-dark font-size-22"><?= number_format($totalSouls) ?></h3>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge font-size-11 mb-1 d-block" style="background: #fff3e0; color: #e65100;">Harvest</span>
                        <small class="text-muted font-size-11">Total Fruit</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= min(100, $totalSouls * 10) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Converts in Care -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white stat-card-hover">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #e3f2fd; color: #1976d2;">
                            <i class="bx bx-user-check font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Converts In Follow-up</p>
                        <h3 class="mb-0 fw-bold text-dark font-size-22"><?= number_format($convertsCount) ?></h3>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge font-size-11 mb-1 d-block" style="background: #e3f2fd; color: #1976d2;">Discipleship</span>
                        <small class="text-muted font-size-11">Care Roster</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= min(100, $convertsCount * 15) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attended Church Service -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white stat-card-hover">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #e8f5e9; color: #2e7d32;">
                            <i class="bx bx-church font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Attended Church</p>
                        <h3 class="mb-0 fw-bold text-dark font-size-22"><?= $attendedCount ?> <span class="font-size-13 fw-normal text-muted">souls</span></h3>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge font-size-11 mb-1 d-block" style="background: #e8f5e9; color: #2e7d32;">Integrated</span>
                        <small class="text-muted font-size-11">Worshiping</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= min(100, $attendedCount * 25) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Holy Ghost Baptized -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white stat-card-hover">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #f3e5f5; color: #7b1fa2;">
                            <i class="bx bx-flame font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Holy Ghost Baptized</p>
                        <h4 class="mb-0 fw-bold text-dark font-size-22"><?= $holyGhostCount ?> <span class="font-size-13 fw-normal text-muted">souls</span></h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge font-size-11 mb-1 d-block" style="background: #f3e5f5; color: #7b1fa2;">Fire</span>
                        <small class="text-muted font-size-11">Empowered</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar" role="progressbar" style="width: 100%; background: #7b1fa2;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab Navigation: Converts Roster vs Outreach Logs -->
<div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
    <div class="card-header bg-white border-bottom py-2">
        <ul class="nav nav-pills card-header-pills" id="evangelismTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active fw-semibold rounded-pill px-4" id="converts-tab" data-bs-toggle="tab" href="#convertsPane" role="tab">
                    <i class="bx bx-user-check me-1"></i> Converts & Discipleship Care (<?= $convertsCount ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-semibold rounded-pill px-4" id="outreach-tab" data-bs-toggle="tab" href="#outreachPane" role="tab">
                    <i class="bx bx-calendar me-1"></i> Outreach Sessions (<?= count($reports) ?>)
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content" id="evangelismTabsContent">
            <!-- Tab 1: Converts Roster & Milestones -->
            <div class="tab-pane fade show active" id="convertsPane" role="tabpanel">
                <div class="p-3 bg-light border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span class="text-muted font-size-13">Track personal contact follow-ups, service attendance, and spiritual progress.</span>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#newConvertModal">
                        <i class="bx bx-plus me-1"></i> Add New Convert
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Convert Name</th>
                                <th>Phone / Contact</th>
                                <th>Decision</th>
                                <th class="text-center">Church</th>
                                <th class="text-center">Holy Ghost</th>
                                <th class="text-center">Water Bapt.</th>
                                <th>Next Follow-up</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($converts)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="avatar-lg rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3 shadow-sm" style="background: #f8fafc; color: #64748b; width: 72px; height: 72px;">
                                            <i class="bx bx-user-plus font-size-36"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Converts Added to Care Pipeline</h5>
                                        <p class="font-size-13 text-muted mx-auto mb-3" style="max-width: 480px;">
                                            Capture the names and contact details of people you lead to Christ so you can follow up with them, pray with them, and nurture them to church maturity.
                                        </p>
                                        <button type="button" class="btn btn-primary fw-semibold rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#newConvertModal">
                                            <i class="bx bx-plus me-1"></i> Add First Convert
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($converts as $c): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs rounded-circle me-2 d-flex align-items-center justify-content-center font-size-12 fw-bold" style="background: #e0e7ff; color: #4338ca;">
                                                    <?= strtoupper(substr($c['full_name'], 0, 2)) ?>
                                                </div>
                                                <div>
                                                    <a href="<?= AssetHelper::url('evangelism/converts/' . (int)$c['id']) ?>" class="fw-bold text-dark font-size-13 text-decoration-none">
                                                        <?= htmlspecialchars($c['full_name']) ?>
                                                    </a>
                                                    <small class="text-muted d-block font-size-11">Won: <?= date('M d, Y', strtotime($c['created_at'])) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($c['phone'])): ?>
                                                <div class="d-flex align-items-center gap-1">
                                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $c['phone']) ?>" target="_blank" class="badge bg-soft-success text-success p-1" title="WhatsApp Chat">
                                                        <i class="bx bxl-whatsapp font-size-14"></i>
                                                    </a>
                                                    <span class="text-dark font-size-13"><?= htmlspecialchars($c['phone']) ?></span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted font-size-12">No Phone</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-info text-info font-size-11">
                                                <?= ucfirst($c['decision_type']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill <?= !empty($c['attended_service']) ? 'bg-soft-success text-success' : 'bg-light text-muted' ?> font-size-11 px-2 py-0.5">
                                                <?= !empty($c['attended_service']) ? 'Attended' : 'Pending' ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill <?= !empty($c['baptized_holy_ghost']) ? 'bg-soft-warning text-warning' : 'bg-light text-muted' ?> font-size-11 px-2 py-0.5">
                                                <?= !empty($c['baptized_holy_ghost']) ? '🔥 Yes' : 'No' ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill <?= !empty($c['baptized_water']) ? 'bg-soft-info text-info' : 'bg-light text-muted' ?> font-size-11 px-2 py-0.5">
                                                <?= !empty($c['baptized_water']) ? '💧 Yes' : 'No' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($c['next_followup_date'])): ?>
                                                <small class="text-primary fw-semibold font-size-12">
                                                    <i class="bx bx-calendar-event me-1"></i> <?= date('M d', strtotime($c['next_followup_date'])) ?>
                                                </small>
                                            <?php else: ?>
                                                <small class="text-muted font-size-12">Not scheduled</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 font-size-12 fw-semibold open-convert-care-btn"
                                                data-id="<?= (int)$c['id'] ?>"
                                                data-name="<?= htmlspecialchars($c['full_name']) ?>"
                                                data-phone="<?= htmlspecialchars($c['phone'] ?? '') ?>"
                                                data-decision="<?= htmlspecialchars($c['decision_type'] ?? 'salvation') ?>"
                                                data-prayer="<?= htmlspecialchars($c['prayer_requests'] ?? '') ?>"
                                                data-contacted="<?= !empty($c['first_contact_done']) ? '1' : '0' ?>"
                                                data-attended="<?= !empty($c['attended_service']) ? '1' : '0' ?>"
                                                data-holyghost="<?= !empty($c['baptized_holy_ghost']) ? '1' : '0' ?>"
                                                data-water="<?= !empty($c['baptized_water']) ? '1' : '0' ?>"
                                                data-foundation="<?= !empty($c['foundation_class_enrolled']) ? '1' : '0' ?>"
                                                data-department="<?= htmlspecialchars($c['department_joined'] ?? '') ?>"
                                                data-profile-url="<?= AssetHelper::url('evangelism/converts/' . (int)$c['id']) ?>">
                                                <i class="bx bx-heart me-1"></i> Care & Journey
                                            </button>
                                            <a href="<?= AssetHelper::url('evangelism/converts/' . (int)$c['id']) ?>" class="btn btn-sm btn-light border rounded-pill px-2 ms-1" title="Open Full Profile Page">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Outreach Sessions Ledger -->
            <div class="tab-pane fade" id="outreachPane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Outreach Date</th>
                                <th class="text-center">Souls Won</th>
                                <th>Location & Field Notes</th>
                                <th>Submitted On</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reports)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No outreach sessions recorded yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reports as $report): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark font-size-13">
                                            <i class="bx bx-calendar me-1 text-primary"></i>
                                            <?= date('M d, Y', strtotime($report['report_date'])) ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill px-3 py-1.5 font-size-13 fw-bold" style="background: #e8f5e9; color: #2e7d32;">
                                                +<?= (int)$report['souls_won'] ?> Souls
                                            </span>
                                        </td>
                                        <td class="text-muted font-size-13" style="max-width: 350px;">
                                            <?= !empty($report['notes']) ? htmlspecialchars(mb_strimwidth($report['notes'], 0, 85, "...")) : '<span class="text-muted fst-italic">Outreach session</span>' ?>
                                        </td>
                                        <td class="text-muted font-size-12">
                                            <?= date('M d, Y H:i', strtotime($report['created_at'])) ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-soft-success text-success font-size-11 px-2.5 py-1">
                                                <i class="bx bx-check-circle me-1 align-middle"></i> Recorded
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-1">
                                                <a href="<?= AssetHelper::url('evangelism/' . (int)$report['id']) ?>" class="btn btn-sm btn-light border rounded-pill px-2" title="View Details">
                                                    <i class="bx bx-show text-primary"></i>
                                                </a>
                                                <a href="<?= AssetHelper::url('evangelism/' . (int)$report['id'] . '/edit') ?>" class="btn btn-sm btn-light border rounded-pill px-2" title="Edit Report">
                                                    <i class="bx bx-edit text-secondary"></i>
                                                </a>
                                                <form method="POST" action="<?= AssetHelper::url('evangelism/' . (int)$report['id'] . '/delete') ?>" style="display:inline;">
                                                    <input type="hidden" name="_token" value="<?= htmlspecialchars(Security::generateCSRFToken()) ?>">
                                                    <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2" title="Delete" onclick="return confirm('Are you sure you want to delete this report?');">
                                                        <i class="bx bx-trash text-danger"></i>
                                                    </button>
                                                </form>
                                            </div>
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
</div>

<!-- Modal 1: Add New Convert & Follow-Up -->
<div class="modal fade" id="newConvertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header bg-light py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-user-plus text-primary me-2 font-size-20"></i> Add New Convert to Care
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= AssetHelper::url('evangelism/converts/store') ?>">
                <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13 text-dark">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control rounded-3" placeholder="e.g. Samuel Adebayo" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold font-size-13 text-dark">Phone / WhatsApp</label>
                            <input type="tel" name="phone" class="form-control rounded-3" placeholder="e.g. 08012345678">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold font-size-13 text-dark">Email (Optional)</label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="e.g. samuel@example.com">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold font-size-13 text-dark">Decision Type</label>
                            <select name="decision_type" class="form-select rounded-3">
                                <option value="salvation">Accepting Christ (Salvation)</option>
                                <option value="rededication">Rededication to Christ</option>
                                <option value="healing_miracle">Received Miracle / Healing</option>
                                <option value="inquiry">Spiritual Inquiry / Seeker</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold font-size-13 text-dark">First Follow-up Date</label>
                            <input type="date" name="next_followup_date" class="form-control rounded-3" value="<?= date('Y-m-d', strtotime('+2 days')) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13 text-dark">Prayer Requests & Needs</label>
                        <textarea name="prayer_requests" class="form-control rounded-3" rows="2" placeholder="e.g. Deliverance, breakthrough, salvation of family members..."></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold font-size-13 text-dark">Address / Location</label>
                        <input type="text" name="address" class="form-control rounded-3" placeholder="e.g. Ikeja, Lagos">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                        <i class="bx bx-check me-1"></i> Add Convert & Open Journey
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Log Session -->
<div class="modal fade" id="newReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header bg-light py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-heart text-danger me-2 font-size-20"></i> Log Outreach Session Count
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= AssetHelper::url('evangelism/store') ?>">
                <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13 text-dark">Date of Outreach</label>
                        <input type="date" name="report_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13 text-dark">Number of Souls Won</label>
                        <input type="number" name="souls_won" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13 text-dark">Location / Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="e.g. Outreach at University Campus, led 3 people to accept Christ..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm fw-semibold">
                        <i class="bx bx-check me-1"></i> Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 3: Instant Convert Care & Journey Modal -->
<div class="modal fade" id="convertCareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header text-white py-3 border-0" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); border-radius: 18px 18px 0 0;">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center fw-bold" style="background: #fef3c7; color: #b45309;">
                            <i class="bx bx-user-check font-size-20"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0" id="careModalConvertName">Convert Name</h5>
                            <small class="text-white-50 font-size-12" id="careModalConvertDecision">Salvation &bull; Discipleship Journey</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="#" id="careModalWhatsappBtn" target="_blank" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold">
                            <i class="bx bxl-whatsapp me-1"></i> WhatsApp
                        </a>
                        <a href="#" id="careModalCallBtn" class="btn btn-warning btn-sm text-dark rounded-pill px-3 fw-bold">
                            <i class="bx bx-phone-call me-1"></i> Call
                        </a>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            
            <div class="modal-body p-4 bg-light">
                <div class="row g-3">
                    <!-- Left: 6 Spiritual Milestones -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 p-3">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                                <i class="bx bx-flame text-danger me-2 font-size-18"></i> Spiritual Milestones
                            </h6>
                            
                            <div class="list-group list-group-flush font-size-13">
                                <!-- 1. Initial Contact -->
                                <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span>📞 1. Initial Contact Done</span>
                                    <form method="POST" class="milestone-toggle-form">
                                        <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                                        <input type="hidden" name="milestone" value="first_contact_done">
                                        <input type="hidden" name="value" class="milestone-val" value="1">
                                        <button type="submit" class="btn btn-xs rounded-pill milestone-btn btn-outline-secondary">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- 2. Attended Church -->
                                <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span>⛪ 2. Attended Church Service</span>
                                    <form method="POST" class="milestone-toggle-form">
                                        <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                                        <input type="hidden" name="milestone" value="attended_service">
                                        <input type="hidden" name="value" class="milestone-val" value="1">
                                        <button type="submit" class="btn btn-xs rounded-pill milestone-btn btn-outline-secondary">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- 3. Holy Ghost Baptized -->
                                <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span>🔥 3. Holy Ghost Baptized</span>
                                    <form method="POST" class="milestone-toggle-form">
                                        <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                                        <input type="hidden" name="milestone" value="baptized_holy_ghost">
                                        <input type="hidden" name="value" class="milestone-val" value="1">
                                        <button type="submit" class="btn btn-xs rounded-pill milestone-btn btn-outline-secondary">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- 4. Water Baptism -->
                                <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span>💧 4. Water Baptism</span>
                                    <form method="POST" class="milestone-toggle-form">
                                        <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                                        <input type="hidden" name="milestone" value="baptized_water">
                                        <input type="hidden" name="value" class="milestone-val" value="1">
                                        <button type="submit" class="btn btn-xs rounded-pill milestone-btn btn-outline-secondary">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- 5. Foundation Class -->
                                <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span>📖 5. Believers Class Enrolled</span>
                                    <form method="POST" class="milestone-toggle-form">
                                        <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                                        <input type="hidden" name="milestone" value="foundation_class_enrolled">
                                        <input type="hidden" name="value" class="milestone-val" value="1">
                                        <button type="submit" class="btn btn-xs rounded-pill milestone-btn btn-outline-secondary">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- 6. Department Joined -->
                                <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span>🕊️ 6. Ministry Unit Integrated</span>
                                    <form method="POST" class="milestone-toggle-form">
                                        <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                                        <input type="hidden" name="milestone" value="department_joined">
                                        <input type="hidden" name="value" class="milestone-val" value="Evangelism">
                                        <button type="submit" class="btn btn-xs rounded-pill milestone-btn btn-outline-secondary">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-3 pt-2 border-top">
                                <small class="text-muted d-block mb-1">Prayer Points / Needs:</small>
                                <p class="text-dark font-size-12 fst-italic mb-0 p-2 rounded bg-light" id="careModalPrayerPoints">None</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Quick Touchpoint Logger -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 p-3">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                                <i class="bx bx-message-square-add text-primary me-2 font-size-18"></i> Log Follow-up Touchpoint
                            </h6>

                            <form method="POST" id="careModalFollowupForm">
                                <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                                
                                <div class="mb-2">
                                    <label class="form-label font-size-12 fw-semibold mb-1">Contact Method</label>
                                    <select name="contact_method" class="form-select form-select-sm rounded-3">
                                        <option value="phone_call">📞 Phone Call</option>
                                        <option value="whatsapp_sms">💬 WhatsApp Message</option>
                                        <option value="home_visit">🏡 Home Visit</option>
                                        <option value="church_meeting">⛪ Met at Church</option>
                                        <option value="prayer_session">🙏 Prayer Session</option>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label font-size-12 fw-semibold mb-1">Outcome</label>
                                    <select name="outcome" class="form-select form-select-sm rounded-3">
                                        <option value="reached_receptive">✅ Receptive / Welcomed Prayer</option>
                                        <option value="attended_service">⛪ Attended Service</option>
                                        <option value="reached_busy">⏳ Reached but Busy</option>
                                        <option value="not_answering">📴 Not Answering</option>
                                        <option value="prayer_answered">🙌 Prayer Answered / Testimony</option>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label font-size-12 fw-semibold mb-1">Conversation Notes</label>
                                    <textarea name="notes" class="form-control form-control-sm rounded-3" rows="2" placeholder="Discussion summary, scriptures shared..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label font-size-12 fw-semibold mb-1">Next Follow-up Date</label>
                                    <input type="date" name="next_action_date" class="form-control form-control-sm rounded-3" value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm">
                                        <i class="bx bx-check me-1"></i> Save Touchpoint
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light py-2 border-top d-flex justify-content-between">
                <a href="#" id="careModalFullProfileLink" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                    <i class="bx bx-arrow-to-right me-1"></i> Open Full Discipleship Profile Page
                </a>
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const careModalEl = document.getElementById('convertCareModal');
    let careModal = null;
    if (careModalEl && typeof bootstrap !== 'undefined') {
        careModal = new bootstrap.Modal(careModalEl);
    }

    document.querySelectorAll('.open-convert-care-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const name = this.dataset.name;
            const phone = this.dataset.phone;
            const decision = this.dataset.decision;
            const prayer = this.dataset.prayer;
            const contacted = this.dataset.contacted === '1';
            const attended = this.dataset.attended === '1';
            const holyghost = this.dataset.holyghost === '1';
            const water = this.dataset.water === '1';
            const foundation = this.dataset.foundation === '1';
            const department = this.dataset.department;
            const profileUrl = this.dataset.profileUrl;

            // Populate Modal
            document.getElementById('careModalConvertName').textContent = name;
            document.getElementById('careModalConvertDecision').textContent = (decision.charAt(0).toUpperCase() + decision.slice(1)) + ' • Discipleship Journey';
            document.getElementById('careModalPrayerPoints').textContent = prayer || 'No specific prayer request recorded.';
            document.getElementById('careModalFullProfileLink').href = profileUrl;

            // Action Buttons
            const waBtn = document.getElementById('careModalWhatsappBtn');
            const callBtn = document.getElementById('careModalCallBtn');
            const cleanPhone = phone ? phone.replace(/[^0-9]/g, '') : '';

            if (cleanPhone) {
                waBtn.style.display = 'inline-flex';
                waBtn.href = 'https://wa.me/' + cleanPhone;
                callBtn.style.display = 'inline-flex';
                callBtn.href = 'tel:' + phone;
            } else {
                waBtn.style.display = 'none';
                callBtn.style.display = 'none';
            }

            // Set Form Action
            const followupForm = document.getElementById('careModalFollowupForm');
            followupForm.action = '<?= AssetHelper::url('evangelism/converts/') ?>' + id + '/followup';

            // Milestone forms action & buttons state
            const milestoneForms = careModalEl.querySelectorAll('.milestone-toggle-form');
            const milestonesState = [contacted, attended, holyghost, water, foundation, (department && department.length > 0)];

            milestoneForms.forEach(function(mForm, idx) {
                mForm.action = '<?= AssetHelper::url('evangelism/converts/') ?>' + id + '/milestone';
                const isChecked = milestonesState[idx];
                const mBtn = mForm.querySelector('.milestone-btn');
                const mVal = mForm.querySelector('.milestone-val');

                if (isChecked) {
                    mBtn.className = 'btn btn-xs rounded-pill milestone-btn btn-success';
                    mBtn.innerHTML = '<i class="bx bx-check-double"></i> Done';
                    mVal.value = '0';
                } else {
                    mBtn.className = 'btn btn-xs rounded-pill milestone-btn btn-outline-secondary';
                    mBtn.innerHTML = '<i class="bx bx-check"></i> Mark';
                    mVal.value = (idx === 5) ? 'Evangelism' : '1';
                }
            });

            if (careModal) {
                careModal.show();
            } else {
                window.location.href = profileUrl;
            }
        });
    });
});
</script>

<style>
.stat-card-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.08) !important;
}
.btn-xs {
    padding: 0.2rem 0.6rem;
    font-size: 0.75rem;
}
</style>

