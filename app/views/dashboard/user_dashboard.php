<?php
use App\Core\Session;
use App\Utilities\AssetHelper;

$session = Session::getInstance();
$userName = $session->get('user_name', 'User');
$evangelismReports = $evangelismReports ?? [];
$notifications = $notifications ?? [];
$assignedUnits = $assignedUnits ?? [];
$attendanceSummary = $attendanceSummary ?? [];
$givingSummary = $givingSummary ?? ['total' => 0, 'this_year' => 0, 'last_transaction' => null];
$engagementScore = $engagementScore ?? 0;
$aiInsights = $aiInsights ?? ['insights' => [], 'recommendations' => [], 'score' => 0];
$isUnitHead = $isUnitHead ?? false;
$managedUnits = $managedUnits ?? [];
?>

<!-- Premium Header -->
<div class="dashboard-header animate__animated animate__fadeInDown">
    <div class="row align-items-center">
        <div class="col-md-8 text-center text-md-start">
            <div class="welcome-text">
                <h1>Shalom, <?= htmlspecialchars(explode(' ', $userName)[0]) ?>!</h1>
                <p class="lead mb-0">"Let your light so shine before men, that they may see your good works..."</p>
            </div>
        </div>
        <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
            <div class="d-inline-block text-start p-3 glass-metric rounded-pill pulse-primary">
                <div class="d-flex align-items-center gap-3">
                    <div class="engagement-circle" style="--percentage: <?= $engagementScore ?>%" data-score="<?= round($engagementScore) ?>"></div>
                    <div>
                        <div class="stat-label">Church Pulse</div>
                        <div class="stat-value text-white"><?= round($engagementScore) ?>%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($isUnitHead): ?>
<!-- Unit Management Section (for Directors/Heads) -->
<div class="unit-head-section animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 text-primary"><i class='bx bx-crown'></i> Unit Stewardship</h4>
            <p class="text-muted mb-0">Managing your assigned units and reporting performance.</p>
        </div>
        <div class="badge bg-primary-soft text-primary p-2 px-3">Unit Head</div>
    </div>
    
    <div class="row">
        <?php foreach ($managedUnits as $managed): ?>
            <div class="col-md-6 mb-3">
                <div class="card metric-card border shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="fw-bold"><?= htmlspecialchars($managed['unit']['name']) ?></h5>
                            <span class="badge bg-success-soft text-success">Active</span>
                        </div>
                        <div class="row text-center mb-4">
                            <div class="col-4">
                                <div class="stat-value h4 mb-0"><?= $managed['stats']['total_members'] ?></div>
                                <div class="stat-label small">Members</div>
                            </div>
                            <div class="col-4 border-start border-end">
                                <div class="stat-value h4 mb-0"><?= $managed['stats']['avg_attendance'] ?></div>
                                <div class="stat-label small">Avg Att.</div>
                            </div>
                            <div class="col-4">
                                <div class="stat-value h4 mb-0"><?= number_format($managed['stats']['net_balance'] / 1000, 1) ?>k</div>
                                <div class="stat-label small">Finance</div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= AssetHelper::url('attendance/mark') ?>?unit_id=<?= $managed['unit']['id'] ?>" class="btn btn-primary btn-sm quick-action-btn">
                                <i class='bx bx-check-square'></i> Mark Attendance
                            </a>
                            <a href="<?= AssetHelper::url('finance/create') ?>?unit_id=<?= $managed['unit']['id'] ?>" class="btn btn-outline-primary btn-sm quick-action-btn">
                                <i class='bx bx-dollar-circle'></i> Record Finance
                            </a>
                            <a href="<?= AssetHelper::url('reports/create') ?>?unit_id=<?= $managed['unit']['id'] ?>" class="btn btn-outline-primary btn-sm quick-action-btn">
                                <i class='bx bx-file'></i> Unit Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <!-- Main Content Col -->
    <div class="col-lg-8">
        <!-- Personal Units -->
        <div class="card metric-card mb-4 shadow-sm animate__animated animate__fadeInLeft">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">My Active Units & Teams</h5>
                <a href="<?= AssetHelper::url('my-units') ?>" class="btn btn-link btn-sm p-0 text-primary">View Details</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if (empty($assignedUnits)): ?>
                        <div class="col-12 text-center py-4">
                            <p class="text-muted">You are not currently assigned to any units.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($assignedUnits as $unit): ?>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded d-flex align-items-center gap-3 hover-slide">
                                    <div class="avatar-sm bg-primary-soft rounded-circle d-flex align-items-center justify-content-center text-primary">
                                        <i class='bx bxs-group h4 mb-0'></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($unit['name']) ?></h6>
                                        <small class="text-muted"><?= ucfirst($unit['role']) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Attendance History -->
        <div class="card metric-card mb-4 shadow-sm animate__animated animate__fadeInLeft" style="animation-delay: 0.1s">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">My Attendance History</h5>
                <a href="<?= AssetHelper::url('attendance/my-history') ?>" class="btn btn-link btn-sm">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Service/Event</th>
                                <th>Unit</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($attendanceSummary)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No attendance data found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($attendanceSummary as $att): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($att['event_date'])) ?></td>
                                        <td><?= ucfirst($att['event_type']) ?></td>
                                        <td><?= htmlspecialchars($att['unit_name'] ?? 'General') ?></td>
                                        <td><span class="badge bg-success">Present</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Evangelism Reports -->
        <div class="card metric-card shadow-sm animate__animated animate__fadeInLeft" style="animation-delay: 0.2s">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">My Evangelism Impact</h5>
                <div>
                    <a href="<?= AssetHelper::url('evangelism/create') ?>" class="btn btn-success btn-sm"><i class='bx bx-plus'></i> New Report</a>
                    <a href="<?= AssetHelper::url('evangelism') ?>" class="btn btn-link btn-sm">See Archive</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Souls Won</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($evangelismReports)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Bring someone to Christ today!</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach (array_slice($evangelismReports, 0, 5) as $report): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($report['report_date'])) ?></td>
                                        <td class="fw-bold text-primary"><?= $report['souls_won'] ?></td>
                                        <td><?= htmlspecialchars(mb_strimwidth($report['notes'], 0, 50, "...")) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Col -->
    <div class="col-lg-4">
        <!-- Giving Summary -->
        <div class="card metric-card mb-4 shadow-sm animate__animated animate__fadeInRight">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Giving Summary</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="stat-label">Total Contribution</div>
                    <div class="stat-value">₦<?= number_format($givingSummary['total'], 2) ?></div>
                </div>
                <div class="d-flex justify-content-between border-top pt-3">
                    <div>
                        <div class="stat-label x-small">This Year</div>
                        <div class="fw-bold">₦<?= number_format($givingSummary['this_year'], 2) ?></div>
                    </div>
                    <?php if ($givingSummary['last_transaction']): ?>
                        <div class="text-end">
                            <div class="stat-label x-small">Last Giving</div>
                            <div class="fw-bold"><?= date('M d', strtotime($givingSummary['last_transaction']['transaction_date'])) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mt-4">
                    <a href="<?= AssetHelper::url('giving/my-records') ?>" class="btn btn-primary w-100">View History</a>
                </div>
            </div>
        </div>

        <!-- AI Insights -->
        <div class="card metric-card mb-4 shadow-sm animate__animated animate__fadeInRight" style="animation-delay: 0.1s">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Church Pulse Insights</h5>
            </div>
            <div class="card-body">
                <?php if (empty($aiInsights['recommendations'])): ?>
                    <p class="text-muted small">Insights will appear as you engage with church activities.</p>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($aiInsights['recommendations'] as $rec): ?>
                            <li class="mb-3 d-flex gap-2">
                                <i class='bx bx-info-circle text-primary h5 mt-1'></i>
                                <span class="small"><?= htmlspecialchars($rec) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Notifications -->
        <div class="card metric-card shadow-sm animate__animated animate__fadeInRight" style="animation-delay: 0.2s">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Notifications</h5>
            </div>
            <div class="card-body">
                <?php if (empty($notifications)): ?>
                    <div class="text-center py-3">
                        <i class='bx bx-bell-off h2 text-muted'></i>
                        <p class="text-muted small">No new notifications.</p>
                    </div>
                <?php else: ?>
                    <div class="notification-list">
                        <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>
                            <div class="p-2 mb-2 border-bottom">
                                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($notification['title']) ?></h6>
                                <p class="small text-muted mb-1"><?= htmlspecialchars($notification['message']) ?></p>
                                <small class="text-muted x-small"><?= date('M d, H:i', strtotime($notification['created_at'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?= AssetHelper::url('notifications') ?>" class="btn btn-link w-100 btn-sm mt-2">View All</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
