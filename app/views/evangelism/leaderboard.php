<?php
use App\Utilities\AssetHelper;
use App\Core\Session;

$session = Session::getInstance();
$period = $period ?? 'month';
$churchId = $churchId ?? null;
$churches = $churches ?? [];
$leaderboard = $leaderboard ?? [];
$stats = $stats ?? [];

$totalSouls = (int)($stats['total_souls'] ?? 0);
$totalSoulWinners = (int)($stats['total_soul_winners'] ?? 0);
$totalOutreachSessions = (int)($stats['total_outreach_sessions'] ?? 0);
$avgSoulsPerOutreach = (float)($stats['avg_souls_per_outreach'] ?? 0);
$topDepartment = $stats['top_department'] ?? 'General Outreach';
$topDepartmentSouls = (int)($stats['top_department_souls'] ?? 0);

$harvestTrends = $harvestTrends ?? ['labels' => [], 'data' => []];
$unitBreakdown = $unitBreakdown ?? ['labels' => [], 'data' => []];
$verificationLogs = $verificationLogs ?? [];

$top1 = $leaderboard[0] ?? null;
$top2 = $leaderboard[1] ?? null;
$top3 = $leaderboard[2] ?? null;

$periodLabels = [
    'week' => 'This Week',
    'month' => 'This Month',
    'quarter' => 'This Quarter',
    'year' => 'This Year (' . date('Y') . ')',
    'all' => 'All Time'
];
?>

<!-- Executive Royal Hero Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm text-white overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); border-radius: 18px; border: 1px solid rgba(245, 158, 11, 0.2) !important;">
            <div class="card-body p-4 p-md-5 position-relative">
                <!-- Ambient Gold & Purple Glow -->
                <div style="position: absolute; right: -30px; top: -30px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(245, 158, 11, 0.2) 0%, rgba(245, 158, 11, 0) 70%); border-radius: 50%;"></div>
                <div style="position: absolute; left: 20%; bottom: -40px; width: 180px; height: 180px; background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(99, 102, 241, 0) 70%); border-radius: 50%;"></div>

                <div class="row align-items-center position-relative">
                    <div class="col-lg-7 col-md-12 mb-4 mb-lg-0">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge px-3 py-1.5 rounded-pill font-size-12 fw-bold" style="background: rgba(245, 158, 11, 0.2); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.4);">
                                <i class="bx bx-trophy me-1 align-middle"></i> Kingdom Harvest Awards
                            </span>
                            <span class="badge px-3 py-1.5 rounded-pill font-size-12 fw-semibold" style="background: rgba(255, 255, 255, 0.1); color: #e2e8f0; border: 1px solid rgba(255, 255, 255, 0.15);">
                                <i class="bx bx-calendar me-1 align-middle"></i> <?= $periodLabels[$period] ?? 'This Month' ?>
                            </span>
                        </div>
                        <h1 class="text-white fw-bold mb-2 font-size-28">
                            Soul Winning Leaderboard & Analytics ✨
                        </h1>
                        <p class="text-white-50 font-size-14 mb-3" style="max-width: 620px; line-height: 1.6;">
                            "The fruit of the righteous is a tree of life; and he that winneth souls is wise." &mdash; <span class="text-white">Proverbs 11:30</span>
                        </p>

                        <!-- Quick Period Switcher Pills -->
                        <div class="d-inline-flex flex-wrap gap-1 p-1 rounded-pill" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15);">
                            <a href="<?= AssetHelper::url('evangelism/leaderboard?period=week' . ($churchId ? '&church_id=' . $churchId : '')) ?>" class="btn btn-sm rounded-pill px-3 fw-semibold font-size-12 <?= $period === 'week' ? 'btn-warning text-dark shadow-sm' : 'text-white' ?>">Week</a>
                            <a href="<?= AssetHelper::url('evangelism/leaderboard?period=month' . ($churchId ? '&church_id=' . $churchId : '')) ?>" class="btn btn-sm rounded-pill px-3 fw-semibold font-size-12 <?= $period === 'month' ? 'btn-warning text-dark shadow-sm' : 'text-white' ?>">Month</a>
                            <a href="<?= AssetHelper::url('evangelism/leaderboard?period=quarter' . ($churchId ? '&church_id=' . $churchId : '')) ?>" class="btn btn-sm rounded-pill px-3 fw-semibold font-size-12 <?= $period === 'quarter' ? 'btn-warning text-dark shadow-sm' : 'text-white' ?>">Quarter</a>
                            <a href="<?= AssetHelper::url('evangelism/leaderboard?period=year' . ($churchId ? '&church_id=' . $churchId : '')) ?>" class="btn btn-sm rounded-pill px-3 fw-semibold font-size-12 <?= $period === 'year' ? 'btn-warning text-dark shadow-sm' : 'text-white' ?>">Year</a>
                            <a href="<?= AssetHelper::url('evangelism/leaderboard?period=all' . ($churchId ? '&church_id=' . $churchId : '')) ?>" class="btn btn-sm rounded-pill px-3 fw-semibold font-size-12 <?= $period === 'all' ? 'btn-warning text-dark shadow-sm' : 'text-white' ?>">All Time</a>
                        </div>
                    </div>

                    <!-- Right Action Bar -->
                    <div class="col-lg-5 col-md-12 text-lg-end">
                        <div class="d-flex flex-wrap justify-content-lg-end align-items-center gap-2">
                            <!-- Church Selector (Superadmin) -->
                            <?php if ($session->isSuperAdmin() && !empty($churches)): ?>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light bg-white border dropdown-toggle fw-semibold rounded-pill px-3 shadow-sm font-size-13 py-2" type="button" data-bs-toggle="dropdown">
                                    <i class="bx bx-church me-1 text-primary"></i>
                                    <?php 
                                    $selectedChurchName = 'All Assemblies';
                                    if ($churchId) {
                                        foreach ($churches as $c) {
                                            if ($c['id'] == $churchId) { $selectedChurchName = $c['name']; break; }
                                        }
                                    }
                                    echo htmlspecialchars($selectedChurchName);
                                    ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><a class="dropdown-item <?= empty($churchId) ? 'active' : '' ?>" href="<?= AssetHelper::url('evangelism/leaderboard?period=' . $period) ?>">All Assemblies</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <?php foreach ($churches as $c): ?>
                                        <li><a class="dropdown-item <?= $churchId == $c['id'] ? 'active' : '' ?>" href="<?= AssetHelper::url('evangelism/leaderboard?period=' . $period . '&church_id=' . $c['id']) ?>"><?= htmlspecialchars($c['name']) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <button type="button" class="btn btn-warning fw-bold rounded-pill px-3 py-2 shadow-sm font-size-13" data-bs-toggle="modal" data-bs-target="#logEvangelismModal">
                                <i class="bx bx-plus me-1"></i> Log Soul Won
                            </button>

                            <a href="<?= AssetHelper::url('evangelism/leaderboard/export?period=' . $period . ($churchId ? '&church_id=' . $churchId : '')) ?>" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 fw-semibold font-size-13">
                                <i class="bx bx-download me-1"></i> CSV
                            </a>

                            <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 fw-semibold font-size-13">
                                <i class="bx bx-printer me-1"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Four Executive Modern KPI Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Total Souls Won -->
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
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Total Souls Won</p>
                        <h3 class="mb-0 fw-bold text-dark font-size-22"><?= number_format($totalSouls) ?></h3>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge font-size-11 mb-1 d-block" style="background: #fff3e0; color: #e65100;">Harvest</span>
                        <small class="text-muted font-size-11"><?= $periodLabels[$period] ?? '' ?></small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= min(100, $totalSouls * 10) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Soul Winners -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white stat-card-hover">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #e3f2fd; color: #1976d2;">
                            <i class="bx bx-group font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Active Soul Winners</p>
                        <h3 class="mb-0 fw-bold text-dark font-size-22"><?= number_format($totalSoulWinners) ?></h3>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge font-size-11 mb-1 d-block" style="background: #e3f2fd; color: #1976d2;">Laborers</span>
                        <small class="text-muted font-size-11">Mobilized</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= min(100, $totalSoulWinners * 20) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Souls per Session -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white stat-card-hover">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #e8f5e9; color: #2e7d32;">
                            <i class="bx bx-line-chart font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Outreach Velocity</p>
                        <h3 class="mb-0 fw-bold text-dark font-size-22"><?= $avgSoulsPerOutreach ?></h3>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge font-size-11 mb-1 d-block" style="background: #e8f5e9; color: #2e7d32;">Souls/Session</span>
                        <small class="text-muted font-size-11"><?= $totalOutreachSessions ?> Sessions</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= min(100, $avgSoulsPerOutreach * 25) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Mobilized Department -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white stat-card-hover">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 d-flex align-items-center justify-content-center" style="background: #f3e5f5; color: #7b1fa2;">
                            <i class="bx bx-crown font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Top Department</p>
                        <h4 class="mb-0 fw-bold text-dark font-size-16 text-truncate" style="max-width: 140px;" title="<?= htmlspecialchars($topDepartment) ?>">
                            <?= htmlspecialchars($topDepartment) ?>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge font-size-11 mb-1 d-block" style="background: #f3e5f5; color: #7b1fa2;"><?= $topDepartmentSouls ?> Souls</span>
                        <small class="text-muted font-size-11">Leading Unit</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar" role="progressbar" style="width: 100%; background: #7b1fa2;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hall of Faith: Top 3 Champions Podium OR Inspiring Empty-State -->
<div class="row g-3 mb-4 align-items-end">
    <?php if (!empty($leaderboard)): ?>
        <!-- 2nd Place (Silver) -->
        <div class="col-md-4 order-2 order-md-1">
            <?php if ($top2): ?>
            <div class="card border-0 shadow-sm rounded-4 bg-white text-center p-3 position-relative stat-card-hover" style="border-top: 4px solid #94a3b8 !important;">
                <div class="position-absolute top-0 start-50 translate-middle">
                    <span class="badge rounded-circle p-2 shadow-sm font-size-16" style="background: #94a3b8; color: #ffffff; width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;">
                        🥈
                    </span>
                </div>
                <div class="mt-3 mb-2">
                    <div class="avatar-md rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold font-size-20" style="background: #f1f5f9; color: #475569; width: 64px; height: 64px; border: 2px solid #cbd5e1;">
                        <?= strtoupper(substr($top2['user_name'], 0, 2)) ?>
                    </div>
                </div>
                <h5 class="fw-bold text-dark mb-0 font-size-16"><?= htmlspecialchars($top2['user_name']) ?></h5>
                <small class="text-muted font-size-12 d-block mb-2"><?= htmlspecialchars($top2['unit_name'] ?? 'General') ?> &bull; <?= htmlspecialchars($top2['church_name'] ?? 'Life Changers') ?></small>
                <div class="py-2 px-3 rounded-3 my-2" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <h3 class="fw-bold text-dark mb-0">+<?= number_format($top2['total_souls']) ?></h3>
                    <small class="text-muted font-size-11 text-uppercase fw-semibold">Souls Won</small>
                </div>
                <small class="text-muted font-size-11"><i class="bx bx-calendar-check me-1"></i> <?= $top2['report_count'] ?> Outreach Sessions</small>
            </div>
            <?php endif; ?>
        </div>

        <!-- 1st Place (Gold Champion) -->
        <div class="col-md-4 order-1 order-md-2">
            <?php if ($top1): ?>
            <div class="card border-0 shadow rounded-4 text-center p-4 position-relative text-white overflow-hidden stat-card-hover" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); border: 2px solid #f59e0b !important;">
                <!-- Crown ambient glow -->
                <div style="position: absolute; top: -30px; left: 50%; transform: translateX(-50%); width: 140px; height: 140px; background: radial-gradient(circle, rgba(245, 158, 11, 0.4) 0%, rgba(245, 158, 11, 0) 70%); border-radius: 50%;"></div>
                
                <div class="position-absolute top-0 start-50 translate-middle">
                    <span class="badge rounded-circle p-2 shadow font-size-20" style="background: #f59e0b; color: #ffffff; width: 46px; height: 46px; display: inline-flex; align-items: center; justify-content: center;">
                        👑
                    </span>
                </div>
                
                <div class="mt-3 mb-2 position-relative">
                    <div class="avatar-lg rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold font-size-26 shadow" style="background: #fef3c7; color: #b45309; width: 80px; height: 80px; border: 3px solid #f59e0b;">
                        <?= strtoupper(substr($top1['user_name'], 0, 2)) ?>
                    </div>
                </div>
                
                <span class="badge font-size-11 px-3 py-1 rounded-pill mb-1 d-inline-block fw-bold" style="background: rgba(245, 158, 11, 0.25); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.45);">
                    🏆 1st Place Champion
                </span>
                <h4 class="fw-bold text-white mb-0 font-size-18"><?= htmlspecialchars($top1['user_name']) ?></h4>
                <small class="text-white-50 font-size-12 d-block mb-3"><?= htmlspecialchars($top1['unit_name'] ?? 'General') ?> &bull; <?= htmlspecialchars($top1['church_name'] ?? 'Life Changers') ?></small>
                
                <div class="py-2.5 px-3 rounded-3 my-2" style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.2);">
                    <h2 class="fw-bold text-warning mb-0 font-size-30">+<?= number_format($top1['total_souls']) ?></h2>
                    <small class="text-white text-uppercase font-size-11 fw-semibold tracking-wider">Kingdom Souls Won</small>
                </div>
                
                <small class="text-white-50 font-size-11"><i class="bx bx-calendar-check me-1"></i> <?= $top1['report_count'] ?> Outreach Sessions &bull; Last: <?= date('M d', strtotime($top1['latest_outreach'])) ?></small>
            </div>
            <?php endif; ?>
        </div>

        <!-- 3rd Place (Bronze) -->
        <div class="col-md-4 order-3">
            <?php if ($top3): ?>
            <div class="card border-0 shadow-sm rounded-4 bg-white text-center p-3 position-relative stat-card-hover" style="border-top: 4px solid #d97706 !important;">
                <div class="position-absolute top-0 start-50 translate-middle">
                    <span class="badge rounded-circle p-2 shadow-sm font-size-16" style="background: #d97706; color: #ffffff; width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;">
                        🥉
                    </span>
                </div>
                <div class="mt-3 mb-2">
                    <div class="avatar-md rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold font-size-20" style="background: #fef3c7; color: #92400e; width: 64px; height: 64px; border: 2px solid #fde68a;">
                        <?= strtoupper(substr($top3['user_name'], 0, 2)) ?>
                    </div>
                </div>
                <h5 class="fw-bold text-dark mb-0 font-size-16"><?= htmlspecialchars($top3['user_name']) ?></h5>
                <small class="text-muted font-size-12 d-block mb-2"><?= htmlspecialchars($top3['unit_name'] ?? 'General') ?> &bull; <?= htmlspecialchars($top3['church_name'] ?? 'Life Changers') ?></small>
                <div class="py-2 px-3 rounded-3 my-2" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <h3 class="fw-bold text-dark mb-0">+<?= number_format($top3['total_souls']) ?></h3>
                    <small class="text-muted font-size-11 text-uppercase fw-semibold">Souls Won</small>
                </div>
                <small class="text-muted font-size-11"><i class="bx bx-calendar-check me-1"></i> <?= $top3['report_count'] ?> Outreach Sessions</small>
            </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Inspiring Empty State Encouragement Card -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 text-center p-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #faf5ff 0%, #f0fdf4 100%); border: 2px dashed #c084fc !important;">
                <div class="avatar-lg rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3 shadow-sm" style="background: #ffffff; color: #9333ea; width: 80px; height: 80px;">
                    <i class="bx bx-trophy font-size-36"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1 font-size-20">Be the First Soul Winner in <?= $periodLabels[$period] ?? 'This Period' ?>! 🌟</h4>
                <p class="text-muted font-size-14 mx-auto mb-4" style="max-width: 540px;">
                    The harvest is plentiful! Document your personal or department outreach activity now and take your place on the Kingdom Hall of Champions.
                </p>
                <div>
                    <button type="button" class="btn btn-primary fw-bold rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#logEvangelismModal">
                        <i class="bx bx-plus me-1"></i> Log Soul Won Now
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Analytics Charts (Timeline Trend & Department Breakdown) -->
<div class="row g-4 mb-4">
    <!-- Harvest Timeline Trend Line -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-trending-up text-primary me-2 font-size-18"></i> Evangelism Harvest Timeline
                    </h5>
                    <small class="text-muted">Tracking souls won over the selected timeframe</small>
                </div>
                <span class="badge bg-soft-primary text-primary font-size-12 px-2.5 py-1"><?= $periodLabels[$period] ?? '' ?></span>
            </div>
            <div class="card-body p-4">
                <div style="height: 280px; position: relative;">
                    <canvas id="harvestTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Contribution Donut -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-pie-chart-alt-2 text-warning me-2 font-size-18"></i> Outreach by Department
                </h5>
                <small class="text-muted">Soul winning breakdown by unit</small>
            </div>
            <div class="card-body p-4">
                <div style="height: 220px; position: relative;">
                    <canvas id="unitBreakdownChart"></canvas>
                </div>
                <div class="mt-3 text-center">
                    <small class="text-muted font-size-12">Top mobilizing ministry departments</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full Ranked Soul Winners Table -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-list-ol text-primary me-2 font-size-18"></i> Complete Soul Winner Rankings
                    </h5>
                    <small class="text-muted">Ranked list of all soul winners in the selected timeframe</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="position-relative" style="width: 240px;">
                        <input type="text" id="leaderboardSearch" class="form-control form-control-sm rounded-pill ps-4" placeholder="Search soul winner...">
                        <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="leaderboardTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 80px;">Rank</th>
                                <th>Soul Winner / Member</th>
                                <th>Assembly / Campus</th>
                                <th>Department</th>
                                <th class="text-center">Outreach Logs</th>
                                <th class="text-center">Last Active</th>
                                <th class="text-end pe-4">Total Souls Won</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($leaderboard)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bx bx-trophy font-size-36 opacity-50 mb-2 d-block"></i>
                                        <h6 class="fw-semibold text-dark">No soul winning records in this timeframe</h6>
                                        <p class="font-size-13 mb-3">Click below to record the first soul won!</p>
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#logEvangelismModal">
                                            <i class="bx bx-plus me-1"></i> Log Soul Won
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $rank = 1; foreach ($leaderboard as $row): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <?php if ($rank === 1): ?>
                                                <span class="badge rounded-circle p-2 font-size-14" style="background: #fef3c7; color: #b45309; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">🥇 1</span>
                                            <?php elseif ($rank === 2): ?>
                                                <span class="badge rounded-circle p-2 font-size-14" style="background: #f1f5f9; color: #475569; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">🥈 2</span>
                                            <?php elseif ($rank === 3): ?>
                                                <span class="badge rounded-circle p-2 font-size-14" style="background: #fffbeb; color: #d97706; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">🥉 3</span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill bg-light text-dark font-size-12 px-2 py-1">#<?= $rank ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs rounded-circle me-2 d-flex align-items-center justify-content-center font-size-12 fw-bold" style="background: #e0e7ff; color: #4338ca;">
                                                    <?= strtoupper(substr($row['user_name'], 0, 2)) ?>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark font-size-13"><?= htmlspecialchars($row['user_name']) ?></h6>
                                                    <small class="text-muted font-size-11"><?= htmlspecialchars($row['user_email']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted font-size-13">
                                            <i class="bx bx-church me-1 text-primary"></i> <?= htmlspecialchars($row['church_name'] ?? 'Life Changers') ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-info text-info font-size-11 px-2 py-0.5">
                                                <?= htmlspecialchars($row['unit_name'] ?? 'General') ?>
                                            </span>
                                        </td>
                                        <td class="text-center font-size-13 text-muted">
                                            <?= $row['report_count'] ?> reports
                                        </td>
                                        <td class="text-center font-size-13 text-muted">
                                            <?= $row['latest_outreach'] ? date('M d, Y', strtotime($row['latest_outreach'])) : 'N/A' ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="badge rounded-pill px-3 py-1.5 font-size-13 fw-bold" style="background: #e8f5e9; color: #2e7d32;">
                                                +<?= number_format($row['total_souls']) ?> Souls
                                            </span>
                                        </td>
                                    </tr>
                                <?php $rank++; endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Itemized Verification & Outreach Logs Feed -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-history text-muted me-2 font-size-18"></i> Recent Outreach Log Audits
                    </h5>
                    <small class="text-muted">Itemized individual outreach submissions and soul-winning decision logs</small>
                </div>
                <a href="<?= AssetHelper::url('evangelism') ?>" class="btn btn-sm btn-outline-secondary fw-semibold">
                    Manage All Reports
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Soul Winner</th>
                                <th>Assembly</th>
                                <th>Department</th>
                                <th class="text-center">Souls Logged</th>
                                <th>Outreach Notes / Location</th>
                                <th class="text-center pe-4">Verification</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($verificationLogs)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No recent outreach logs recorded.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach (array_slice($verificationLogs, 0, 15) as $log): ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-dark font-size-13">
                                            <?= date('M d, Y', strtotime($log['report_date'])) ?>
                                        </td>
                                        <td class="fw-semibold text-dark font-size-13">
                                            <?= htmlspecialchars($log['user_name']) ?>
                                        </td>
                                        <td class="text-muted font-size-13">
                                            <?= htmlspecialchars($log['church_name'] ?? 'General') ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-secondary text-secondary font-size-11">
                                                <?= htmlspecialchars($log['unit_name'] ?? 'General') ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-soft-success text-success font-size-12 fw-bold px-2 py-1">
                                                +<?= (int)$log['souls_won'] ?> Souls
                                            </span>
                                        </td>
                                        <td class="text-muted font-size-13" style="max-width: 320px;">
                                            <?= htmlspecialchars(mb_strimwidth($log['notes'] ?? 'Outreach session', 0, 75, "...")) ?>
                                        </td>
                                        <td class="text-center pe-4">
                                            <span class="badge rounded-pill bg-soft-success text-success font-size-11 px-2 py-0.5">
                                                <i class="bx bx-check-circle me-1 align-middle"></i> Verified
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
    </div>
</div>

<!-- Modal: Log Outreach / Soul Won Directly from Leaderboard -->
<div class="modal fade" id="logEvangelismModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header bg-light py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-heart text-danger me-2 font-size-20"></i> Log Kingdom Outreach / Soul Won
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
                        <label class="form-label fw-semibold font-size-13 text-dark">Location / Notes / Convert Details</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="e.g. Outreach at University Campus, led 3 people to accept Christ..."></textarea>
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
</style>

<!-- Chart.js Scripts -->
<script>
(function() {
    function initLeaderboardCharts() {
        if (typeof Chart === 'undefined') {
            setTimeout(initLeaderboardCharts, 100);
            return;
        }

        // 1. Harvest Trend Line Chart
        var trendCtx = document.getElementById('harvestTrendChart');
        if (trendCtx) {
            if (window._harvestTrendInstance) {
                window._harvestTrendInstance.destroy();
            }

            var trendLabels = <?= json_encode(!empty($harvestTrends['labels']) ? $harvestTrends['labels'] : ['Week 1', 'Week 2', 'Week 3', 'Week 4']) ?>;
            var trendData = <?= json_encode(!empty($harvestTrends['data']) ? $harvestTrends['data'] : [0, 0, 0, 0]) ?>;

            window._harvestTrendInstance = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Souls Won',
                        data: trendData,
                        borderColor: '#4338ca',
                        backgroundColor: 'rgba(67, 56, 202, 0.08)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#4338ca',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(c) {
                                    return c.parsed.y + ' souls won';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { size: 11 }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: { font: { size: 11 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // 2. Unit Breakdown Donut Chart
        var donutCtx = document.getElementById('unitBreakdownChart');
        if (donutCtx) {
            if (window._unitBreakdownInstance) {
                window._unitBreakdownInstance.destroy();
            }

            var donutLabels = <?= json_encode(!empty($unitBreakdown['labels']) ? $unitBreakdown['labels'] : ['General Outreach']) ?>;
            var donutData = <?= json_encode(!empty($unitBreakdown['data']) ? $unitBreakdown['data'] : [1]) ?>;
            var colors = ['#4338ca', '#f59e0b', '#10b981', '#06b6d4', '#ec4899', '#8b5cf6'];

            window._unitBreakdownInstance = new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: donutLabels,
                    datasets: [{
                        data: donutData,
                        backgroundColor: colors.slice(0, donutLabels.length),
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11 },
                                padding: 12
                            }
                        }
                    }
                }
            });
        }
    }

    // Search filter for table
    document.addEventListener('DOMContentLoaded', function() {
        initLeaderboardCharts();

        var searchInput = document.getElementById('leaderboardSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                var query = this.value.toLowerCase();
                var rows = document.querySelectorAll('#leaderboardTable tbody tr');
                rows.forEach(function(row) {
                    var text = row.textContent.toLowerCase();
                    row.style.display = text.indexOf(query) > -1 ? '' : 'none';
                });
            });
        }
    });

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        initLeaderboardCharts();
    }
})();
</script>
