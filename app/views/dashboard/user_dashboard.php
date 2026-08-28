<?php
use App\Core\Session;
use App\Utilities\AssetHelper;

$session = Session::getInstance();
$userName = $session->get('user_name', 'User');
$user = $user ?? null;
$church = $church ?? null;
$evangelismReports = $evangelismReports ?? [];
$totalSoulsWon = (int)($totalSoulsWon ?? 0);
$notifications = $notifications ?? [];
$assignedUnits = $assignedUnits ?? [];
$attendanceSummary = $attendanceSummary ?? [];
$attendanceTrend = $attendanceTrend ?? [];
$givingSummary = $givingSummary ?? ['total' => 0, 'this_year' => 0, 'last_transaction' => null];
$engagementScore = (float)($engagementScore ?? 0);
$aiInsights = $aiInsights ?? ['insights' => [], 'recommendations' => [], 'score' => 0];
$isUnitHead = (bool)($isUnitHead ?? false);
$managedUnits = $managedUnits ?? [];
$activePledgesCount = (int)($activePledgesCount ?? 0);
$totalPledged = (float)($totalPledged ?? 0);
$totalPaid = (float)($totalPaid ?? 0);

// Determine engagement tier
$tier = 'Growing Disciple';
$tierColor = 'info';
if ($engagementScore >= 80) {
    $tier = 'Pillar of Faith';
    $tierColor = 'warning';
} elseif ($engagementScore >= 50) {
    $tier = 'Active Servant';
    $tierColor = 'success';
}

$totalGiving = (float)($givingSummary['total'] ?? 0);
$thisYearGiving = (float)($givingSummary['this_year'] ?? 0);
$attendanceCount = count($attendanceSummary);
?>

<!-- Executive Hero Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm text-white overflow-hidden" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 16px;">
            <div class="card-body p-4 p-md-5 position-relative">
                <!-- Decorative background elements -->
                <div style="position: absolute; right: -40px; top: -40px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(99, 102, 241, 0) 70%); border-radius: 50%;"></div>
                
                <div class="row align-items-center position-relative">
                    <div class="col-lg-8 col-md-12 mb-4 mb-lg-0">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge px-3 py-1.5 rounded-pill font-size-12 fw-semibold" style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.25);">
                                <i class="bx bx-church me-1 align-middle"></i> <?= htmlspecialchars($church['name'] ?? 'Life Changers Church') ?>
                            </span>
                            <span class="badge px-3 py-1.5 rounded-pill font-size-12 fw-semibold" style="background: rgba(56, 239, 125, 0.2); color: #38ef7d; border: 1px solid rgba(56, 239, 125, 0.4);">
                                <i class="bx bx-award me-1 align-middle"></i> <?= $tier ?>
                            </span>
                        </div>
                        <h1 class="text-white fw-bold mb-2 font-size-28">
                            Shalom, <?= htmlspecialchars(explode(' ', $userName)[0]) ?>! ✨
                        </h1>
                        <p class="text-white-50 font-size-14 mb-0" style="max-width: 600px; line-height: 1.6;">
                            "Let your light so shine before men, that they may see your good works, and glorify your Father which is in heaven." &mdash; <span class="text-white">Matthew 5:16</span>
                        </p>
                    </div>
                    <div class="col-lg-4 col-md-12 text-lg-end">
                        <div class="d-inline-flex align-items-center rounded-4 p-3 text-start gap-3" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15);">
                            <div class="position-relative d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                                <svg viewBox="0 0 36 36" class="circular-chart" style="width: 58px; height: 58px;">
                                    <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255, 255, 255, 0.15)" stroke-width="3.5" />
                                    <path class="circle" stroke-dasharray="<?= round($engagementScore) ?>, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#38ef7d" stroke-width="3.5" stroke-linecap="round" />
                                </svg>
                                <span class="position-absolute font-size-13 fw-bold text-white"><?= round($engagementScore) ?>%</span>
                            </div>
                            <div>
                                <span class="font-size-11 text-white-50 text-uppercase d-block fw-semibold tracking-wider">Church Pulse</span>
                                <h5 class="text-white fw-bold mb-0"><?= $tier ?></h5>
                                <small class="text-success font-size-11"><i class="bx bx-trending-up me-1"></i> Active Member</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Four High-Contrast Modern KPI Cards -->
<div class="row g-3 mb-4">
    <!-- Service Attendance -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= AssetHelper::url('attendance/my-history') ?>" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm rounded-4 stat-card-hover bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #e8f5e9; color: #2e7d32;">
                                <i class="bx bx-calendar-check font-size-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Attendance Record</p>
                            <h4 class="mb-0 fw-bold text-dark font-size-18"><?= $attendanceCount ?> Services</h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <span class="badge bg-soft-success text-success font-size-11 mb-1 d-block">Active</span>
                            <small class="text-muted font-size-11">Verified</small>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= min(100, $attendanceCount * 20) ?>%"></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Personal Giving -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= AssetHelper::url('giving/my-records') ?>" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm rounded-4 stat-card-hover bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #e3f2fd; color: #1976d2;">
                                <i class="bx bx-wallet font-size-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Lifetime Giving</p>
                            <h4 class="mb-0 fw-bold text-dark font-size-18">₦<?= number_format($totalGiving, 2) ?></h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <span class="badge bg-soft-primary text-primary font-size-11 mb-1 d-block">₦<?= number_format($thisYearGiving, 0) ?> YTD</span>
                            <small class="text-muted font-size-11">Receipts</small>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Active Ministry Teams -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= AssetHelper::url('my-units') ?>" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm rounded-4 stat-card-hover bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #e0f7fa; color: #00838f;">
                                <i class="bx bx-group font-size-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Ministry Teams</p>
                            <h4 class="mb-0 fw-bold text-dark font-size-18"><?= count($assignedUnits) ?> Assigned</h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <span class="badge bg-soft-info text-info font-size-11 mb-1 d-block"><?= $isUnitHead ? 'Unit Leader' : 'Serving' ?></span>
                            <small class="text-muted font-size-11">Units</small>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Evangelism & Souls Won -->
    <div class="col-xl-3 col-md-6">
        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#logEvangelismModal" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm rounded-4 stat-card-hover bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #fff3e0; color: #e65100;">
                                <i class="bx bx-heart font-size-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Evangelism Impact</p>
                            <h4 class="mb-0 fw-bold text-dark font-size-18"><?= $totalSoulsWon ?> Souls Won</h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <span class="badge bg-soft-warning text-warning font-size-11 mb-1 d-block">+ Log New</span>
                            <small class="text-muted font-size-11">Kingdom</small>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Interactive Quick Command Action Bar -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-body p-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <span class="text-muted font-size-12 fw-bold text-uppercase d-flex align-items-center me-2">
                        <i class="bx bx-bolt-circle text-primary me-1 font-size-16"></i> Quick Access:
                    </span>
                    <div class="d-flex flex-wrap gap-2 flex-grow-1">
                        <a href="<?= AssetHelper::url('giving/my-records') ?>" class="btn btn-sm btn-light border fw-semibold rounded-pill px-3">
                            <i class="bx bx-receipt text-success me-1"></i> Giving Records & Receipts
                        </a>
                        <a href="<?= AssetHelper::url('giving/my-pledges') ?>" class="btn btn-sm btn-light border fw-semibold rounded-pill px-3">
                            <i class="bx bx-gift text-warning me-1"></i> My Pledges
                            <?php if ($activePledgesCount > 0): ?>
                                <span class="badge bg-warning text-dark ms-1"><?= $activePledgesCount ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?= AssetHelper::url('attendance/my-history') ?>" class="btn btn-sm btn-light border fw-semibold rounded-pill px-3">
                            <i class="bx bx-calendar-check text-primary me-1"></i> Attendance History
                        </a>
                        <a href="<?= AssetHelper::url('my-units') ?>" class="btn btn-sm btn-light border fw-semibold rounded-pill px-3">
                            <i class="bx bx-group text-info me-1"></i> Department Teams
                        </a>
                        <button type="button" class="btn btn-sm btn-primary fw-semibold rounded-pill px-3 ms-auto" data-bs-toggle="modal" data-bs-target="#logEvangelismModal">
                            <i class="bx bx-plus me-1"></i> Log Soul Won
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($isUnitHead && !empty($managedUnits)): ?>
<!-- Unit Leadership Hub (For Unit Directors / Department Heads) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4" style="background: #f8faff; border: 1px solid #e2e8f0 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1 d-flex align-items-center">
                            <i class="bx bx-crown text-warning me-2 font-size-20"></i> Unit Leadership & Stewardship
                        </h5>
                        <p class="text-muted font-size-13 mb-0">You are appointed as Director/Head over the following departments:</p>
                    </div>
                    <span class="badge bg-primary text-white font-size-12 px-3 py-1 rounded-pill">Unit Director</span>
                </div>

                <div class="row g-3">
                    <?php foreach ($managedUnits as $managed): ?>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($managed['unit']['name']) ?></h5>
                                        <span class="badge bg-soft-success text-success font-size-11">Active Unit</span>
                                    </div>
                                    <div class="row text-center py-2 my-2 bg-light rounded-3 g-0">
                                        <div class="col-4">
                                            <h5 class="fw-bold text-primary mb-0"><?= $managed['stats']['total_members'] ?></h5>
                                            <small class="text-muted font-size-11">Members</small>
                                        </div>
                                        <div class="col-4 border-start border-end">
                                            <h5 class="fw-bold text-success mb-0"><?= $managed['stats']['avg_attendance'] ?></h5>
                                            <small class="text-muted font-size-11">Avg Headcount</small>
                                        </div>
                                        <div class="col-4">
                                            <h5 class="fw-bold text-dark mb-0">₦<?= number_format(($managed['stats']['net_balance'] ?? 0) / 1000, 1) ?>k</h5>
                                            <small class="text-muted font-size-11">Balance</small>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                        <a href="<?= AssetHelper::url('attendance/mark') ?>?unit_id=<?= $managed['unit']['id'] ?>" class="btn btn-sm btn-primary fw-semibold flex-grow-1">
                                            <i class="bx bx-check-square me-1"></i> Mark Attendance
                                        </a>
                                        <a href="<?= AssetHelper::url('reports/create') ?>?unit_id=<?= $managed['unit']['id'] ?>" class="btn btn-sm btn-outline-primary fw-semibold">
                                            <i class="bx bx-file me-1"></i> Unit Report
                                        </a>
                                        <a href="<?= AssetHelper::url('finance/create') ?>?unit_id=<?= $managed['unit']['id'] ?>" class="btn btn-sm btn-outline-secondary fw-semibold">
                                            <i class="bx bx-wallet me-1"></i> Record Finance
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Left Column: Teams, Attendance History, Evangelism -->
    <div class="col-xl-8">
        <!-- My Active Units & Teams -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-group text-primary me-2 font-size-18"></i> My Active Units & Ministry Teams
                </h5>
                <a href="<?= AssetHelper::url('my-units') ?>" class="btn btn-sm btn-link p-0 text-primary fw-semibold">
                    View Details &rarr;
                </a>
            </div>
            <div class="card-body p-4">
                <?php if (empty($assignedUnits)): ?>
                    <div class="text-center py-4 text-muted">
                        <div class="avatar-md bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-2">
                            <i class="bx bx-group text-muted font-size-24"></i>
                        </div>
                        <h6 class="text-dark fw-semibold">Not assigned to any units yet</h6>
                        <p class="text-muted font-size-13 mb-0">Join a church department or service unit to start ministering!</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($assignedUnits as $unit): ?>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 d-flex align-items-center gap-3 bg-light bg-opacity-50 hover-elevate transition-all">
                                    <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
                                        <i class="bx bxs-group font-size-22"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold text-dark font-size-14"><?= htmlspecialchars($unit['name']) ?></h6>
                                        <span class="badge bg-soft-primary text-primary font-size-11 px-2 py-0.5">
                                            <?= ucfirst($unit['role'] ?? 'Member') ?>
                                        </span>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="<?= AssetHelper::url('units/' . ($unit['id'] ?? '')) ?>" class="btn btn-sm btn-soft-secondary py-1 px-2 font-size-12" title="View Unit Space">
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Attendance History Ledger -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-calendar-check text-success me-2 font-size-18"></i> Recent Service Attendance
                    </h5>
                    <small class="text-muted">Your verified roll-call records across church gatherings</small>
                </div>
                <a href="<?= AssetHelper::url('attendance/my-history') ?>" class="btn btn-sm btn-outline-primary fw-semibold">
                    Full History
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Service / Event</th>
                                <th>Scope</th>
                                <th class="text-center pe-4">Verification</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($attendanceSummary)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bx bx-calendar-x font-size-28 opacity-50 mb-1 d-block"></i>
                                        <p class="mb-0 font-size-13">No attendance records found yet.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($attendanceSummary as $att): ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-dark font-size-13">
                                            <?= date('M d, Y', strtotime($att['event_date'])) ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-primary text-primary text-uppercase px-2 py-1 font-size-11">
                                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $att['event_type'] ?? 'Service'))) ?>
                                            </span>
                                        </td>
                                        <td class="text-muted font-size-13">
                                            <?= htmlspecialchars($att['unit_name'] ?? 'General Congregation') ?>
                                        </td>
                                        <td class="text-center pe-4">
                                            <span class="badge rounded-pill bg-soft-success text-success px-2 py-1 font-size-11">
                                                <i class="bx bx-check-circle me-1 align-middle"></i> Present
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Evangelism & Soul Winning Tracker -->
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-heart text-danger me-2 font-size-18"></i> Kingdom Outreach & Soul Winning
                    </h5>
                    <small class="text-muted">Documenting the harvest of souls won to Christ</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-success fw-semibold" data-bs-toggle="modal" data-bs-target="#logEvangelismModal">
                        <i class="bx bx-plus me-1"></i> New Outreach Log
                    </button>
                    <a href="<?= AssetHelper::url('evangelism') ?>" class="btn btn-sm btn-outline-secondary fw-semibold">
                        Archive
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th class="text-center">Souls Won</th>
                                <th>Location / Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($evangelismReports)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="bx bx-user-plus font-size-28 opacity-50 mb-1 d-block"></i>
                                        <h6 class="text-dark fw-semibold font-size-13 mb-1">Bring someone to Christ today!</h6>
                                        <p class="text-muted font-size-12 mb-0">Click "+ New Outreach Log" to document people you've reached with the Gospel.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach (array_slice($evangelismReports, 0, 5) as $report): ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-dark font-size-13">
                                            <?= date('M d, Y', strtotime($report['report_date'])) ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-soft-success text-success px-2 py-1 font-size-12 fw-bold">
                                                +<?= (int)$report['souls_won'] ?> Souls
                                            </span>
                                        </td>
                                        <td class="text-muted font-size-13">
                                            <?= htmlspecialchars(mb_strimwidth($report['notes'] ?? 'Outreach session', 0, 65, "...")) ?>
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

    <!-- Right Sidebar Column: Giving Summary, AI Spiritual Pulse, Notifications -->
    <div class="col-xl-4">
        <!-- Giving & Stewardship Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #0d5c46 0%, #15803d 100%);">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge font-size-11 px-2.5 py-1 rounded-pill" style="background: rgba(255, 255, 255, 0.2); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.35);">
                        <i class="bx bx-lock-alt me-1"></i> Private & Audited
                    </span>
                    <i class="bx bx-wallet-alt font-size-24 text-white" style="opacity: 0.9;"></i>
                </div>
                <span class="font-size-11 text-uppercase d-block mb-1 fw-bold tracking-wider" style="color: rgba(255, 255, 255, 0.85);">Total Lifetime Giving</span>
                <h2 class="text-white fw-bold mb-3 font-size-26">₦<?= number_format($totalGiving, 2) ?></h2>

                <div class="rounded-3 p-3 mb-3" style="background: rgba(0, 0, 0, 0.22); border: 1px solid rgba(255, 255, 255, 0.18);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="font-size-11 text-uppercase d-block fw-bold" style="color: rgba(255, 255, 255, 0.8);"><?= date('Y') ?> Giving</span>
                            <h5 class="text-white fw-bold mb-0">₦<?= number_format($thisYearGiving, 2) ?></h5>
                        </div>
                        <?php if (!empty($givingSummary['last_transaction'])): ?>
                            <div class="text-end border-start ps-3" style="border-color: rgba(255, 255, 255, 0.25) !important;">
                                <span class="font-size-11 text-uppercase d-block fw-bold" style="color: rgba(255, 255, 255, 0.8);">Last Seed</span>
                                <h6 class="text-white fw-bold mb-0"><?= date('M d', strtotime($givingSummary['last_transaction']['transaction_date'])) ?></h6>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="<?= AssetHelper::url('giving/my-records') ?>" class="btn fw-bold flex-grow-1 shadow-sm font-size-13 py-2" style="background: #ffffff; color: #0d5c46;">
                        View Records &rarr;
                    </a>
                    <a href="<?= AssetHelper::url('giving/my-pledges') ?>" class="btn fw-semibold font-size-13 py-2 text-white" style="background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.35);">
                        Pledges
                    </a>
                </div>
            </div>
        </div>

        <!-- Church Pulse & Pastoral Insights -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-bulb text-warning me-2 font-size-18"></i> Growth & Pastoral Insights
                </h5>
            </div>
            <div class="card-body p-4">
                <?php if (empty($aiInsights['recommendations'])): ?>
                    <div class="text-center py-3 text-muted">
                        <i class="bx bx-check-double font-size-28 text-success mb-1"></i>
                        <p class="font-size-13 mb-0">You're doing great! Keep growing in faith and fellowship.</p>
                    </div>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($aiInsights['recommendations'] as $rec): ?>
                            <li class="mb-3 d-flex gap-3 align-items-start pb-3 border-bottom last-no-border">
                                <div class="avatar-xs bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5">
                                    <i class="bx bx-check font-size-16"></i>
                                </div>
                                <span class="font-size-13 text-dark leading-relaxed"><?= htmlspecialchars($rec) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Unread Notifications Feed -->
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-bell text-info me-2 font-size-18"></i> Church Announcements
                </h5>
                <?php if (!empty($notifications)): ?>
                    <span class="badge bg-soft-danger text-danger font-size-11"><?= count($notifications) ?> New</span>
                <?php endif; ?>
            </div>
            <div class="card-body p-4">
                <?php if (empty($notifications)): ?>
                    <div class="text-center py-3 text-muted">
                        <i class="bx bx-bell-off font-size-28 opacity-50 mb-1 d-block"></i>
                        <p class="font-size-13 mb-0">No new announcements at this time.</p>
                    </div>
                <?php else: ?>
                    <div class="notification-feed">
                        <?php foreach (array_slice($notifications, 0, 4) as $notification): ?>
                            <div class="p-2 mb-2 rounded-3 bg-light bg-opacity-50">
                                <h6 class="mb-1 fw-bold text-dark font-size-13"><?= htmlspecialchars($notification['title']) ?></h6>
                                <p class="font-size-12 text-muted mb-1"><?= htmlspecialchars($notification['message']) ?></p>
                                <small class="text-muted font-size-11"><i class="bx bx-time me-1"></i><?= date('M d, H:i', strtotime($notification['created_at'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?= AssetHelper::url('notifications') ?>" class="btn btn-sm btn-link w-100 text-primary fw-semibold mt-2">
                        View All Announcements &rarr;
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Log Outreach / Soul Won -->
<div class="modal fade" id="logEvangelismModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 14px;">
            <div class="modal-header bg-light py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-heart text-danger me-2"></i> Log Kingdom Outreach / Soul Won
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= AssetHelper::url('evangelism') ?>">
                <input type="hidden" name="csrf_token" value="<?= \App\Utilities\Security::generateCSRFToken() ?>">
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
                        <label class="form-label fw-semibold font-size-13 text-dark">Location / Notes / Decision Details</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="e.g. Led 2 people to Christ during market outreach..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm fw-semibold">
                        <i class="bx bx-check me-1"></i> Save Outreach Log
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
.hover-elevate:hover {
    transform: translateX(4px);
    background-color: #f1f5f9 !important;
}
.last-no-border:last-child {
    border-bottom: none !important;
    padding-bottom: 0 !important;
    margin-bottom: 0 !important;
}
</style>
