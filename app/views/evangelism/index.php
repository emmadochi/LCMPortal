<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$reports = $reports ?? [];
$userStats = $userStats ?? [
    'total_souls' => 0,
    'total_logs' => 0,
    'highest_outreach' => 0,
    'latest_report' => null
];

$totalSouls = (int)($userStats['total_souls'] ?? 0);
$totalLogs = (int)($userStats['total_logs'] ?? 0);
$highestOutreach = (int)($userStats['highest_outreach'] ?? 0);
$latestReport = $userStats['latest_report'] ?? null;
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
                                <i class="bx bx-flame me-1 align-middle"></i> Personal Harvest Journal
                            </span>
                            <span class="badge px-3 py-1.5 rounded-pill font-size-12 fw-semibold" style="background: rgba(255, 255, 255, 0.1); color: #e2e8f0; border: 1px solid rgba(255, 255, 255, 0.15);">
                                <?= $totalLogs ?> Outreach Log<?= $totalLogs === 1 ? '' : 's' ?> Filed
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

                            <button type="button" class="btn btn-warning fw-bold rounded-pill px-3 py-2 shadow-sm font-size-13" data-bs-toggle="modal" data-bs-target="#newReportModal">
                                <i class="bx bx-plus me-1"></i> Log Soul Won
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
                        <span class="badge font-size-11 mb-1 d-block" style="background: #fff3e0; color: #e65100;">Kingdom</span>
                        <small class="text-muted font-size-11">Total Harvest</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= min(100, $totalSouls * 10) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Outreach Logs Filed -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white stat-card-hover">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #e3f2fd; color: #1976d2;">
                            <i class="bx bx-calendar-check font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Outreach Logs</p>
                        <h3 class="mb-0 fw-bold text-dark font-size-22"><?= number_format($totalLogs) ?></h3>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge font-size-11 mb-1 d-block" style="background: #e3f2fd; color: #1976d2;">Sessions</span>
                        <small class="text-muted font-size-11">Recorded</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= min(100, $totalLogs * 15) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Single Highest Harvest -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white stat-card-hover">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #e8f5e9; color: #2e7d32;">
                            <i class="bx bx-trending-up font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Highest Session</p>
                        <h3 class="mb-0 fw-bold text-dark font-size-22"><?= $highestOutreach ?> <span class="font-size-13 fw-normal text-muted">souls</span></h3>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge font-size-11 mb-1 d-block" style="background: #e8f5e9; color: #2e7d32;">Highmark</span>
                        <small class="text-muted font-size-11">Single Outing</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= min(100, $highestOutreach * 20) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Activity Date -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white stat-card-hover">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #f3e5f5; color: #7b1fa2;">
                            <i class="bx bx-time-five font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Last Outreach</p>
                        <h4 class="mb-0 fw-bold text-dark font-size-16">
                            <?= $latestReport ? date('M d, Y', strtotime($latestReport)) : 'None Yet' ?>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge font-size-11 mb-1 d-block" style="background: #f3e5f5; color: #7b1fa2;">Activity</span>
                        <small class="text-muted font-size-11">Recency</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar" role="progressbar" style="width: 100%; background: #7b1fa2;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reports Ledger Table -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-list-check text-primary me-2 font-size-18"></i> Outreach Submissions Ledger
                    </h5>
                    <small class="text-muted">Chronological history of all evangelism reports you have submitted</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="position-relative" style="width: 240px;">
                        <input type="text" id="reportSearch" class="form-control form-control-sm rounded-pill ps-4" placeholder="Search notes/location...">
                        <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="reportsTable">
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
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <div class="avatar-lg rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3 shadow-sm" style="background: #f8fafc; color: #64748b; width: 72px; height: 72px;">
                                            <i class="bx bx-heart font-size-36"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Evangelism Reports Logged Yet</h5>
                                        <p class="font-size-13 text-muted mx-auto mb-3" style="max-width: 460px;">
                                            Start documenting your soul-winning activities and community outreach outings to track kingdom growth.
                                        </p>
                                        <button type="button" class="btn btn-primary fw-semibold rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#newReportModal">
                                            <i class="bx bx-plus me-1"></i> Log Your First Soul Won
                                        </button>
                                    </td>
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
                                                    <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2" title="Delete" onclick="return confirm('Are you sure you want to delete this outreach report?');">
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

<!-- Modal: Log Outreach / Soul Won Directly from Index -->
<div class="modal fade" id="newReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header bg-light py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-heart text-danger me-2 font-size-20"></i> Log Kingdom Outreach / Soul Won
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
                        <label class="form-label fw-semibold font-size-13 text-dark">Location / Notes / Convert Details</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="e.g. Outreach at Market Square, led 2 people to accept Christ..."></textarea>
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

<style>
.stat-card-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.08) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('reportSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            var query = this.value.toLowerCase();
            var rows = document.querySelectorAll('#reportsTable tbody tr');
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(query) > -1 ? '' : 'none';
            });
        });
    }
});
</script>
