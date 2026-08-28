<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
use App\Core\Session;

$session = Session::getInstance();
$targetUser = $targetUser ?? [];
$careStats = $careStats ?? [];
$converts = $converts ?? [];
$commendations = $commendations ?? [];
$reports = $reports ?? [];

$totalSouls = array_sum(array_column($reports, 'souls_won'));
?>

<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm text-white overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); border-radius: 18px; border: 1px solid rgba(245, 158, 11, 0.2) !important;">
            <div class="card-body p-4 p-md-5 position-relative">
                <div class="d-flex flex-wrap justify-content-between align-items-center position-relative">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-lg rounded-circle d-flex align-items-center justify-content-center fw-bold font-size-26 shadow" style="background: #fef3c7; color: #b45309; width: 72px; height: 72px; border: 3px solid #f59e0b;">
                            <?= strtoupper(substr($targetUser['name'] ?? 'U', 0, 2)) ?>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge px-3 py-1 rounded-pill font-size-12 fw-bold" style="background: rgba(245, 158, 11, 0.2); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.4);">
                                    🏆 Soul Winner Portfolio
                                </span>
                            </div>
                            <h2 class="text-white fw-bold mb-0 font-size-24"><?= htmlspecialchars($targetUser['name'] ?? '') ?></h2>
                            <small class="text-white-50 font-size-13"><?= htmlspecialchars($targetUser['email'] ?? '') ?></small>
                        </div>
                    </div>

                    <div class="mt-3 mt-md-0 d-flex gap-2">
                        <a href="<?= AssetHelper::url('evangelism/leaderboard') ?>" class="btn btn-outline-light rounded-pill px-3 py-2 fw-semibold font-size-13">
                            <i class="bx bx-arrow-back me-1"></i> Back to Leaderboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Four Pastoral Care Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3">
            <small class="text-muted text-uppercase fw-semibold font-size-11 mb-1 d-block">Lifetime Souls Won</small>
            <h3 class="mb-0 fw-bold text-dark font-size-22"><?= number_format($totalSouls) ?></h3>
            <small class="text-success font-size-11 fw-semibold"><i class="bx bx-heart me-1"></i> Harvest</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3">
            <small class="text-muted text-uppercase fw-semibold font-size-11 mb-1 d-block">Converts in Care</small>
            <h3 class="mb-0 fw-bold text-dark font-size-22"><?= (int)($careStats['total_converts'] ?? 0) ?></h3>
            <small class="text-primary font-size-11 fw-semibold"><i class="bx bx-user-check me-1"></i> Tracked</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3">
            <small class="text-muted text-uppercase fw-semibold font-size-11 mb-1 d-block">Attended Church</small>
            <h3 class="mb-0 fw-bold text-dark font-size-22"><?= (int)($careStats['attended_church_count'] ?? 0) ?></h3>
            <small class="text-info font-size-11 fw-semibold"><i class="bx bx-church me-1"></i> Integrated</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3">
            <small class="text-muted text-uppercase fw-semibold font-size-11 mb-1 d-block">Holy Ghost Baptized</small>
            <h3 class="mb-0 fw-bold text-dark font-size-22"><?= (int)($careStats['holy_ghost_baptized_count'] ?? 0) ?></h3>
            <small class="text-warning font-size-11 fw-semibold"><i class="bx bx-flame me-1"></i> Empowered</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Converts List -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-group text-primary me-2 font-size-18"></i> Harvest Converts & Milestones
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Convert Name</th>
                                <th>Phone</th>
                                <th>Decision</th>
                                <th class="text-center">Church</th>
                                <th class="text-center">Holy Ghost</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($converts)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No converts entered by this member yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($converts as $c): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark font-size-13"><?= htmlspecialchars($c['full_name']) ?></td>
                                        <td class="text-muted font-size-13"><?= htmlspecialchars($c['phone'] ?? 'N/A') ?></td>
                                        <td><span class="badge bg-soft-info text-info font-size-11"><?= ucfirst($c['decision_type']) ?></span></td>
                                        <td class="text-center">
                                            <i class="bx <?= !empty($c['attended_service']) ? 'bx-check-circle text-success' : 'bx-circle text-muted' ?> font-size-16"></i>
                                        </td>
                                        <td class="text-center">
                                            <i class="bx <?= !empty($c['baptized_holy_ghost']) ? 'bx-flame text-warning' : 'bx-circle text-muted' ?> font-size-16"></i>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="<?= AssetHelper::url('evangelism/converts/' . (int)$c['id']) ?>" class="btn btn-sm btn-light border rounded-pill px-2.5">
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
        </div>
    </div>

    <!-- Pastoral Commendation Board -->
    <div class="col-lg-5">
        <!-- Post Commendation Form (Admins / Head Pastors) -->
        <?php if ($session->isAdmin() || $session->isHeadPastor()): ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-crown text-warning me-2 font-size-18"></i> Leave Pastoral Commendation
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= AssetHelper::url('evangelism/leaderboard/commend') ?>">
                    <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="user_id" value="<?= (int)$targetUser['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13 text-dark">Commendation Type</label>
                        <select name="badge_type" class="form-select rounded-3">
                            <option value="champion">🏆 Kingdom Harvest Champion</option>
                            <option value="faithful">🌿 Faithful Laborer Commendation</option>
                            <option value="fire">🔥 Evangelism Firebearer</option>
                            <option value="pastoral_blessing">🙏 Pastoral Blessing & Prayer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold font-size-13 text-dark">Pastoral Message of Encouragement</label>
                        <textarea name="message" class="form-control rounded-3" rows="3" placeholder="e.g. Well done! Your passion for souls is inspiring our entire assembly. Keep shining the light of Christ!" required></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning fw-bold text-dark rounded-pill px-4 shadow-sm">
                            <i class="bx bx-send me-1"></i> Post Commendation
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Existing Commendations -->
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-medal text-warning me-2 font-size-18"></i> Pastoral Honors & Feedback
                </h5>
            </div>
            <div class="card-body p-4">
                <?php if (empty($commendations)): ?>
                    <p class="text-muted text-center font-size-13 py-3 mb-0">No pastoral commendations posted yet.</p>
                <?php else: ?>
                    <?php foreach ($commendations as $com): ?>
                        <div class="p-3 rounded-3 mb-3" style="background: #fffbeb; border: 1px solid #fde68a;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge font-size-11" style="background: #fef3c7; color: #92400e;">
                                    🎖️ <?= ucwords(str_replace('_', ' ', $com['badge_type'])) ?>
                                </span>
                                <small class="text-muted font-size-11"><?= date('M d, Y', strtotime($com['created_at'])) ?></small>
                            </div>
                            <p class="text-dark font-size-13 mb-1" style="line-height: 1.5;">
                                "<?= nl2br(htmlspecialchars($com['message'])) ?>"
                            </p>
                            <small class="text-muted font-size-11 fw-semibold d-block">
                                &mdash; Pastor <?= htmlspecialchars($com['pastor_name'] ?? 'Leadership') ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
