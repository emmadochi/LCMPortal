<?php
use App\Core\Session;
use App\Models\Church;
use App\Utilities\AssetHelper;

$session  = Session::getInstance();
$userRole = $session->get('user_role', 'user');
$userName = $session->get('user_name', 'User');

$roleNames       = ['admin' => 'Administrator', 'director' => 'Director', 'pastor' => 'Pastor', 'officer' => 'Officer', 'user' => 'Member'];
$roleDisplayName = $roleNames[$userRole] ?? ucfirst($userRole);

$isHeadPastor      = $session->isHeadPastor();
$headPastorChurchId = $session->getHeadPastorChurchId();
$headPastorChurchName = null;
if ($isHeadPastor && $headPastorChurchId) {
    $churchModel = new Church();
    $church = $churchModel->find($headPastorChurchId);
    $headPastorChurchName = $church ? $church['name'] : null;
}

$isDirector        = $session->isDirector();
$directorUnits     = $session->getDirectorUnits();
$isPastor          = $session->get('is_pastor', false);
$isPastorDirector  = $session->get('is_pastor_director', false);

$totalUnits        = $totalUnits ?? 0;
$totalUsers        = $totalUsers ?? 0;
$totalReports      = $totalReports ?? 0;
$totalAttendance   = $totalAttendance ?? 0;
$recentUnits       = $recentUnits ?? [];
$reportsByMonth    = $reportsByMonth ?? [];
$attendanceByMonth = $attendanceByMonth ?? [];
$financeSummary    = $financeSummary ?? ['income' => 0, 'expense' => 0];
$financeByMonth    = $financeByMonth ?? [];
$recentActivityLogs = $recentActivityLogs ?? [];
$myFollowUps       = $myFollowUps ?? [];

$headPastorData    = $headPastorData ?? null;
$directorData      = $directorData ?? null;
$pastorData        = $pastorData ?? null;
$pastorDirectorData = $pastorDirectorData ?? null;

// Prepare chart JSON
$reportsLabels  = json_encode(array_column($reportsByMonth, 'month'));
$reportsData    = json_encode(array_column($reportsByMonth, 'count'));
$attendanceLabels = json_encode(array_column($attendanceByMonth, 'month'));
$attendanceData = json_encode(array_column($attendanceByMonth, 'count'));
$financeLabels  = json_encode(array_keys($financeByMonth));
$financeIncome  = json_encode(array_values(array_map(fn($m) => $m['income'] ?? 0, $financeByMonth)));
$financeExpense = json_encode(array_values(array_map(fn($m) => $m['expense'] ?? 0, $financeByMonth)));
$financeSummaryIncome  = (float)($financeSummary['income'] ?? 0);
$financeSummaryExpense = (float)($financeSummary['expense'] ?? 0);
$netBalance     = $financeSummaryIncome - $financeSummaryExpense;

$today     = date('l, F j, Y');
$greeting  = (int)date('H') < 12 ? 'Good Morning' : ((int)date('H') < 17 ? 'Good Afternoon' : 'Good Evening');
?>


<div class="db-page">

    <!-- ── Hero Welcome Banner ── -->
    <div class="db-hero mb-4" style="position:relative; z-index:1;">
        <div class="row align-items-center" style="position:relative; z-index:1;">
            <div class="col-md-8">
                <div class="db-hero-greeting">
                    <span class="live-dot me-1"></span>
                    <?= $greeting ?>
                </div>
                <div class="db-hero-name"><?= htmlspecialchars($userName) ?> 👋</div>
                <p class="db-hero-sub">
                    Here's what's happening across the portal today.
                </p>
                <p class="db-hero-date"><?= $today ?></p>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="db-role-pill">
                        <i class="bx bx-shield-quarter"></i>
                        <?= htmlspecialchars($roleDisplayName) ?>
                    </span>
                    <?php if ($isHeadPastor && $headPastorChurchName): ?>
                    <span class="db-role-pill">
                        <i class="bx bx-church"></i>
                        <?= htmlspecialchars($headPastorChurchName) ?>
                    </span>
                    <?php endif; ?>
                    <?php if ($isPastorDirector): ?>
                    <span class="db-role-pill">
                        <i class="bx bx-user-pin"></i>
                        Pastor-Director
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-4 mt-4 mt-md-0">
                <div class="db-hero-stats">
                    <div class="db-hero-stat">
                        <div class="db-hero-stat-val" id="heroIncome">
                            ₦<?= number_format($financeSummaryIncome / 1000, 0) ?>k
                        </div>
                        <div class="db-hero-stat-lbl">Total Income</div>
                    </div>
                    <div class="db-hero-divider"></div>
                    <div class="db-hero-stat">
                        <div class="db-hero-stat-val" id="heroExpense">
                            ₦<?= number_format($financeSummaryExpense / 1000, 0) ?>k
                        </div>
                        <div class="db-hero-stat-lbl">Total Expenses</div>
                    </div>
                    <div class="db-hero-divider"></div>
                    <div class="db-hero-stat">
                        <div class="db-hero-stat-val <?= $netBalance >= 0 ? 'net-positive' : 'net-negative' ?>">
                            <?= $netBalance >= 0 ? '+' : '-' ?>₦<?= number_format(abs($netBalance) / 1000, 0) ?>k
                        </div>
                        <div class="db-hero-stat-lbl">Net Balance</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── KPI Cards ── -->
    <div class="db-kpi-grid">

        <div class="db-kpi kpi-units">
            <div class="db-kpi-accent"></div>
            <div class="db-kpi-icon-wrap"><i class="bx bx-buildings"></i></div>
            <div class="db-kpi-label"><?= $isHeadPastor ? 'My Units' : 'Total Units' ?></div>
            <div class="db-kpi-value db-counter" data-target="<?= $totalUnits ?>">0</div>
            <div class="db-kpi-sub">active units registered</div>
            <div class="db-kpi-bg-icon">🏛️</div>
        </div>

        <div class="db-kpi kpi-users">
            <div class="db-kpi-accent"></div>
            <div class="db-kpi-icon-wrap"><i class="bx bx-group"></i></div>
            <div class="db-kpi-label"><?= $isHeadPastor ? 'Members' : 'Total Users' ?></div>
            <div class="db-kpi-value db-counter" data-target="<?= $totalUsers ?>">0</div>
            <div class="db-kpi-sub">active portal accounts</div>
            <div class="db-kpi-bg-icon">👥</div>
        </div>

        <div class="db-kpi kpi-reports">
            <div class="db-kpi-accent"></div>
            <div class="db-kpi-icon-wrap"><i class="bx bx-file-blank"></i></div>
            <div class="db-kpi-label">Total Reports</div>
            <div class="db-kpi-value db-counter" data-target="<?= $totalReports ?>">0</div>
            <div class="db-kpi-sub">reports submitted overall</div>
            <div class="db-kpi-bg-icon">📋</div>
        </div>

        <div class="db-kpi kpi-attendance">
            <div class="db-kpi-accent"></div>
            <div class="db-kpi-icon-wrap"><i class="bx bx-calendar-check"></i></div>
            <div class="db-kpi-label">Attendance Records</div>
            <div class="db-kpi-value db-counter" data-target="<?= $totalAttendance ?>">0</div>
            <div class="db-kpi-sub">total attendance logged</div>
            <div class="db-kpi-bg-icon">📅</div>
        </div>

    </div>

    <!-- ── Charts Row ── -->
    <div class="row g-4 mb-4">

        <!-- Reports + Attendance Line Charts -->
        <div class="col-lg-8">
            <div class="db-panel h-100">
                <div class="db-panel-header">
                    <h6 class="db-panel-title">
                        <span class="pi-blue"><i class="bx bx-line-chart"></i></span>
                        Reports &amp; Attendance — 6 Month Trend
                    </h6>
                </div>
                <div class="db-panel-body" style="height:260px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Finance Doughnut -->
        <div class="col-lg-4">
            <div class="db-panel h-100">
                <div class="db-panel-header">
                    <h6 class="db-panel-title">
                        <span class="pi-green"><i class="bx bx-pie-chart-alt"></i></span>
                        Financial Split
                    </h6>
                </div>
                <div class="db-panel-body d-flex flex-column align-items-center" style="gap:16px;">
                    <div style="width:170px; height:170px; position:relative;">
                        <canvas id="financeDonut"></canvas>
                    </div>
                    <div style="width:100%;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <span style="font-size:.82rem; display:flex; align-items:center; gap:7px;">
                                <span style="width:9px;height:9px;border-radius:50%;background:#10b981;display:inline-block;"></span>Income
                            </span>
                            <strong style="font-size:.85rem; color:#10b981;">₦<?= number_format($financeSummaryIncome, 2) ?></strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:.82rem; display:flex; align-items:center; gap:7px;">
                                <span style="width:9px;height:9px;border-radius:50%;background:#f43f5e;display:inline-block;"></span>Expenses
                            </span>
                            <strong style="font-size:.85rem; color:#f43f5e;">₦<?= number_format($financeSummaryExpense, 2) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ── Income/Expense Monthly Bar ── -->
    <div class="db-panel mb-4">
        <div class="db-panel-header">
            <h6 class="db-panel-title">
                <span class="pi-amber"><i class="bx bx-bar-chart-alt-2"></i></span>
                Income vs Expenses — Monthly
            </h6>
        </div>
        <div class="db-panel-body" style="height:240px;">
            <canvas id="financeBarChart"></canvas>
        </div>
    </div>

    <!-- ── Quick Actions ── -->
    <div class="db-panel mb-4">
        <div class="db-panel-header">
            <h6 class="db-panel-title">
                <span class="pi-violet"><i class="bx bx-grid-alt"></i></span>
                Quick Actions
            </h6>
        </div>
        <div class="db-panel-body">
            <div class="db-quick-grid">
                <a href="<?= AssetHelper::url('units') ?>" class="db-quick-btn qb-indigo">
                    <i class="bx bx-buildings"></i>
                    Manage Units
                </a>
                <?php if ($userRole === 'admin'): ?>
                <a href="<?= AssetHelper::url('users') ?>" class="db-quick-btn qb-green">
                    <i class="bx bx-group"></i>
                    Manage Users
                </a>
                <?php endif; ?>
                <a href="<?= AssetHelper::url('reports/create') ?>" class="db-quick-btn qb-amber">
                    <i class="bx bx-file-blank"></i>
                    Create Report
                </a>
                <a href="<?= AssetHelper::url('attendance/create') ?>" class="db-quick-btn qb-rose">
                    <i class="bx bx-calendar-plus"></i>
                    Record Attendance
                </a>
                <?php if ($userRole === 'admin' || $userRole === 'director' || $session->isHeadPastor()): ?>
                <a href="<?= AssetHelper::url('follow-ups') ?>" class="db-quick-btn qb-cyan">
                    <i class="bx bx-clipboard"></i>
                    Follow-Ups
                </a>
                <a href="<?= AssetHelper::url('members') ?>" class="db-quick-btn qb-violet">
                    <i class="bx bx-user-check"></i>
                    Member Directory
                </a>
                <?php if ($userRole === 'admin'): ?>
                <a href="<?= AssetHelper::url('admin/finance-report') ?>" class="db-quick-btn qb-orange">
                    <i class="bx bx-dollar-circle"></i>
                    Financial Report
                </a>
                <a href="<?= AssetHelper::url('admin/attendance-overview') ?>" class="db-quick-btn qb-slate">
                    <i class="bx bx-stats"></i>
                    Attendance Report
                </a>
                <?php endif; ?>
                <?php endif; ?>
                <a href="<?= AssetHelper::url('media') ?>" class="db-quick-btn qb-slate">
                    <i class="bx bx-image"></i>
                    Media Library
                </a>
                <a href="<?= AssetHelper::url('projects') ?>" class="db-quick-btn qb-indigo">
                    <i class="bx bx-briefcase"></i>
                    Projects
                </a>
            </div>
        </div>
    </div>

    <!-- ── Follow-ups + Recent Units ── -->
    <div class="row g-4 mb-4">

        <!-- Follow-ups assigned to user -->
        <div class="col-lg-7">
            <div class="db-panel h-100">
                <div class="db-panel-header">
                    <h6 class="db-panel-title">
                        <span class="pi-rose"><i class="bx bx-clipboard"></i></span>
                        My Pending Follow-Ups
                    </h6>
                    <?php if (!empty($myFollowUps)): ?>
                    <a href="<?= AssetHelper::url('follow-ups') ?>" class="db-panel-badge" style="text-decoration:none;">
                        View all →
                    </a>
                    <?php endif; ?>
                </div>
                <div class="db-panel-flush">
                    <?php if (empty($myFollowUps)): ?>
                    <div class="db-empty">
                        <i class="bx bx-check-circle"></i>
                        <p>No pending follow-ups assigned to you.</p>
                    </div>
                    <?php else: ?>
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Type</th>
                                <th>Due Date</th>
                                <th>Priority</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myFollowUps as $f): ?>
                            <?php
                                $p = $f['priority'] ?? 'medium';
                                $st = $f['status'] ?? 'pending';
                                $pCls = match($p) { 'urgent'=>'fu-urgent','high'=>'fu-high','medium'=>'fu-medium',default=>'fu-low' };
                                $sCls = $st === 'overdue' ? 'fu-overdue' : 'fu-pending';
                            ?>
                            <tr>
                                <td>
                                    <div style="font-weight:600;"><?= htmlspecialchars(trim(($f['first_name'] ?? '') . ' ' . ($f['last_name'] ?? ''))) ?></div>
                                    <div style="font-size:.72rem; color:#94a3b8;"><?= htmlspecialchars($f['email'] ?? '') ?></div>
                                </td>
                                <td style="color:#64748b; font-size:.82rem;"><?= ucfirst(str_replace('_', ' ', $f['type'] ?? '')) ?></td>
                                <td style="font-size:.82rem;"><?= !empty($f['due_date']) ? date('M j, Y', strtotime($f['due_date'])) : '—' ?></td>
                                <td><span class="db-panel-badge <?= $pCls ?>"><?= ucfirst($p) ?></span></td>
                                <td><span class="db-panel-badge <?= $sCls ?>"><?= ucfirst($st) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Units -->
        <div class="col-lg-5">
            <div class="db-panel h-100">
                <div class="db-panel-header">
                    <h6 class="db-panel-title">
                        <span class="pi-blue"><i class="bx bx-buildings"></i></span>
                        Recent Units
                    </h6>
                    <a href="<?= AssetHelper::url('units') ?>" class="db-panel-badge" style="text-decoration:none;">See all →</a>
                </div>
                <div class="db-panel-flush">
                    <?php if (empty($recentUnits)): ?>
                    <div class="db-empty">
                        <i class="bx bx-building"></i>
                        <p>No units registered yet.</p>
                    </div>
                    <?php else: ?>
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>Unit Name</th>
                                <th>Created</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUnits as $unit): ?>
                            <tr>
                                <td>
                                    <a href="<?= AssetHelper::url('units/' . $unit['id']) ?>" style="font-weight:600; color:#1e293b; text-decoration:none;">
                                        <?= htmlspecialchars($unit['name']) ?>
                                    </a>
                                </td>
                                <td style="font-size:.78rem; color:#94a3b8;"><?= date('M d, Y', strtotime($unit['created_at'])) ?></td>
                                <td>
                                    <?php if ($unit['status'] === 'active'): ?>
                                    <span style="background:#dcfce7; color:#15803d; font-size:.7rem; font-weight:700; padding:3px 9px; border-radius:20px;">Active</span>
                                    <?php else: ?>
                                    <span style="background:#f1f5f9; color:#64748b; font-size:.7rem; font-weight:700; padding:3px 9px; border-radius:20px;">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- ── Role-Based Widgets ── -->
    <?php if ($headPastorData || $directorData || $pastorData || $pastorDirectorData): ?>
    <div class="row g-4 mb-4">

        <?php if ($headPastorData): ?>
        <div class="col-lg-6">
            <div class="db-panel h-100">
                <div class="db-panel-header">
                    <h6 class="db-panel-title">
                        <span class="pi-green"><i class="bx bx-church"></i></span>
                        My Church Overview
                    </h6>
                </div>
                <div class="db-panel-body">
                    <div style="font-size:1rem; font-weight:700; color:#1e293b; margin-bottom:14px;">
                        <?= htmlspecialchars($headPastorData['church']['name']) ?>
                    </div>
                    <div class="d-flex gap-4 mb-4">
                        <div>
                            <div style="font-size:1.6rem; font-weight:900; color:#10b981;"><?= $headPastorData['members_count'] ?></div>
                            <div style="font-size:.72rem; text-transform:uppercase; letter-spacing:.07em; color:#94a3b8; font-weight:700;">Members</div>
                        </div>
                        <div style="width:1px; background:#e2e8f0;"></div>
                        <div>
                            <div style="font-size:1.6rem; font-weight:900; color:#6366f1;"><?= $headPastorData['units_count'] ?></div>
                            <div style="font-size:.72rem; text-transform:uppercase; letter-spacing:.07em; color:#94a3b8; font-weight:700;">Units</div>
                        </div>
                    </div>
                    <?php if (!empty($headPastorData['recent_finance'])): ?>
                    <div style="font-size:.75rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:#94a3b8; margin-bottom:8px;">Recent Transactions</div>
                    <?php foreach (array_slice($headPastorData['recent_finance'], 0, 3) as $fin): ?>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:7px 0; border-bottom:1px solid #f1f5f9; font-size:.85rem;">
                        <span style="color:#475569;"><?= htmlspecialchars($fin['description'] ?? 'Transaction') ?></span>
                        <span style="font-weight:700; color:<?= $fin['transaction_type'] === 'income' ? '#10b981' : '#f43f5e' ?>;">
                            <?= $fin['transaction_type'] === 'income' ? '+' : '-' ?>₦<?= number_format($fin['amount'], 2) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <a href="<?= AssetHelper::url('head-pastor/finance') ?>" class="db-quick-btn qb-green mt-3" style="flex-direction:row; gap:6px; padding:10px 16px; font-size:.82rem; width:fit-content;">
                        <i class="bx bx-line-chart" style="font-size:1rem;"></i> View Financial Dashboard
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($directorData): ?>
        <div class="col-lg-6">
            <div class="db-panel h-100">
                <div class="db-panel-header">
                    <h6 class="db-panel-title">
                        <span class="pi-amber"><i class="bx bx-user-pin"></i></span>
                        Units I Direct
                    </h6>
                    <span class="db-panel-badge"><?= $directorData['total_units'] ?> units</span>
                </div>
                <div class="db-panel-flush">
                    <?php foreach ($directorData['units'] as $uData): ?>
                    <div style="padding:14px 22px; border-bottom:1px solid #f1f5f9;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                            <span style="font-weight:600; color:#1e293b;"><?= htmlspecialchars($uData['unit']['name']) ?></span>
                            <span style="background:#eef2ff; color:#6366f1; font-size:.72rem; font-weight:700; padding:3px 9px; border-radius:20px;">
                                <?= $uData['members_count'] ?> members
                            </span>
                        </div>
                        <?php if (!empty($uData['recent_reports'])): ?>
                        <div style="font-size:.75rem; color:#94a3b8;">
                            <?php foreach (array_slice($uData['recent_reports'], 0, 2) as $rep): ?>
                            <div style="padding:2px 0;">• <?= htmlspecialchars($rep['title'] ?? 'Untitled') ?></div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div style="font-size:.75rem; color:#cbd5e1;">No recent reports</div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    <!-- ── Recent Activity Log ── -->
    <div class="db-panel mb-4">
        <div class="db-panel-header">
            <h6 class="db-panel-title">
                <span class="pi-cyan"><i class="bx bx-pulse"></i></span>
                Recent Activity
            </h6>
            <?php if ($userRole === 'admin'): ?>
            <a href="<?= AssetHelper::url('activity-logs') ?>" class="db-panel-badge" style="text-decoration:none;">View all →</a>
            <?php endif; ?>
        </div>
        <?php
        $actionColors = ['create'=>'#10b981','update'=>'#6366f1','delete'=>'#f43f5e','login'=>'#0ea5e9','logout'=>'#94a3b8','assign'=>'#f59e0b','remove'=>'#f43f5e'];
        $actionBgColors = ['create'=>'#ecfdf5','update'=>'#eef2ff','delete'=>'#fff1f2','login'=>'#ecfeff','logout'=>'#f8fafc','assign'=>'#fffbeb','remove'=>'#fff1f2'];
        ?>
        <div class="db-panel-flush">
            <?php if (empty($recentActivityLogs)): ?>
            <div class="db-empty"><i class="bx bx-pulse"></i><p>No recent activity to display.</p></div>
            <?php else: ?>
            <?php foreach (array_slice($recentActivityLogs, 0, 12) as $log):
                $action = $log['action'] ?? 'create';
                $dotColor = $actionColors[$action] ?? '#94a3b8';
                $dotBg = $actionBgColors[$action] ?? '#f8fafc';
            ?>
            <div class="db-activity-item">
                <div class="db-activity-dot" style="background:<?= $dotBg ?>; color:<?= $dotColor ?>;">
                    <i class="bx <?= match($action) { 'create'=>'bx-plus','update'=>'bx-edit','delete'=>'bx-trash','login'=>'bx-log-in','logout'=>'bx-log-out','assign'=>'bx-link',default=>'bx-bell' } ?>"></i>
                </div>
                <div style="flex:1;">
                    <div class="db-activity-text">
                        <strong><?= htmlspecialchars(trim(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? ''))) ?: htmlspecialchars($log['email'] ?? '—') ?></strong>
                        — <?= htmlspecialchars($log['description'] ?? '') ?>
                    </div>
                    <div class="db-activity-time"><?= date('M d, H:i', strtotime($log['created_at'])) ?></div>
                </div>
                <span style="background:<?= $dotBg ?>; color:<?= $dotColor ?>; font-size:.68rem; font-weight:700; padding:3px 9px; border-radius:20px; white-space:nowrap;">
                    <?= ucfirst($action) ?>
                </span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /.db-page -->

<?php ob_start(); ?>
<script src="<?= AssetHelper::lib('chart.js/chart.umd.js') ?>"></script>
<script>
(function() {
    'use strict';

    // ── Count-up ──────────────────────────────────────
    document.querySelectorAll('.db-counter').forEach(el => {
        const target = parseInt(el.dataset.target) || 0;
        const dur = 900, step = 16, steps = dur / step;
        let cur = 0;
        const inc = target / steps;
        const t = setInterval(() => {
            cur = Math.min(cur + inc, target);
            el.textContent = Math.round(cur).toLocaleString();
            if (cur >= target) clearInterval(t);
        }, step);
    });

    const fontFamily = "'Inter', sans-serif";
    const tooltipDefaults = {
        backgroundColor: '#1e293b',
        titleFont: { family: fontFamily, size: 12, weight: '700' },
        bodyFont:  { family: fontFamily, size: 12 },
        padding: 12, cornerRadius: 10,
    };
    const tickStyle = { font: { family: fontFamily, size: 11 }, color: '#94a3b8' };

    // ── Trend Chart (Reports + Attendance) ───────────
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        const rLabels = <?= $reportsLabels ?>;
        const aLabels = <?= $attendanceLabels ?>;
        const labels  = rLabels.length >= aLabels.length ? rLabels : aLabels;

        const ctx = trendCtx.getContext('2d');
        const gR = ctx.createLinearGradient(0,0,0,220);
        gR.addColorStop(0, 'rgba(99,102,241,.22)');
        gR.addColorStop(1, 'rgba(99,102,241,.01)');
        const gA = ctx.createLinearGradient(0,0,0,220);
        gA.addColorStop(0, 'rgba(245,158,11,.2)');
        gA.addColorStop(1, 'rgba(245,158,11,.01)');

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label:'Reports', data:<?= $reportsData ?>, borderColor:'#6366f1', backgroundColor:gR, fill:true, tension:.4, borderWidth:2.5, pointRadius:4, pointHoverRadius:7, pointBackgroundColor:'#6366f1' },
                    { label:'Attendance', data:<?= $attendanceData ?>, borderColor:'#f59e0b', backgroundColor:gA, fill:true, tension:.4, borderWidth:2.5, pointRadius:4, pointHoverRadius:7, pointBackgroundColor:'#f59e0b' },
                ]
            },
            options: {
                responsive:true, maintainAspectRatio:false,
                plugins: {
                    legend: { position:'top', labels:{ usePointStyle:true, pointStyleWidth:10, font:{ family:fontFamily, size:12, weight:'600' } } },
                    tooltip: { ...tooltipDefaults, mode:'index', intersect:false }
                },
                scales: {
                    y: { beginAtZero:true, grid:{ color:'#f1f5f9', drawBorder:false }, ticks:{ ...tickStyle, precision:0 } },
                    x: { grid:{ display:false }, ticks: tickStyle }
                }
            }
        });
    }

    // ── Finance Doughnut ──────────────────────────────
    const donutCtx = document.getElementById('financeDonut');
    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Income','Expenses'],
                datasets: [{ data:[<?= $financeSummaryIncome ?>,<?= $financeSummaryExpense ?>], backgroundColor:['#10b981','#f43f5e'], borderWidth:3, borderColor:'#fff' }]
            },
            options: {
                cutout:'70%',
                plugins: {
                    legend: { display:false },
                    tooltip: { ...tooltipDefaults, callbacks:{ label: c => '  ' + c.label + ': ₦' + Number(c.raw).toLocaleString('en-NG',{minimumFractionDigits:2,maximumFractionDigits:2}) } }
                }
            }
        });
    }

    // ── Finance Bar Chart ─────────────────────────────
    const barCtx = document.getElementById('financeBarChart');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?= $financeLabels ?>,
                datasets: [
                    { label:'Income',   data:<?= $financeIncome ?>,  backgroundColor:'rgba(16,185,129,.8)',  borderRadius:7, borderSkipped:false },
                    { label:'Expenses', data:<?= $financeExpense ?>, backgroundColor:'rgba(244,63,94,.75)', borderRadius:7, borderSkipped:false }
                ]
            },
            options: {
                responsive:true, maintainAspectRatio:false,
                plugins: {
                    legend: { position:'top', labels:{ usePointStyle:true, pointStyleWidth:10, font:{ family:fontFamily, size:12, weight:'600' } } },
                    tooltip: { ...tooltipDefaults, callbacks:{ label: c => '  ' + c.dataset.label + ': ₦' + Number(c.raw).toLocaleString('en-NG',{minimumFractionDigits:2}) } }
                },
                scales: {
                    y: { grid:{ color:'#f1f5f9', drawBorder:false }, ticks:{ ...tickStyle, callback: v => '₦' + Number(v/1000).toFixed(0)+'k' } },
                    x: { grid:{ display:false }, ticks: tickStyle }
                }
            }
        });
    }

})();
</script>
<?php $pageJs = ob_get_clean(); ?>
