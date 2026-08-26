<!-- Sub-header with Assignment Selector -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #5b73e8 0%, #4430e7 100%);">
            <div class="card-body p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-white mb-1 fw-bold">Unit Workspace: <?= htmlspecialchars($unitName) ?></h3>
                        <p class="text-white-50 mb-0 font-size-14"><i class="bx bx-church me-1"></i> Branch: <?= htmlspecialchars($churchName) ?></p>
                    </div>
                    <?php if (count($assignments) > 1): ?>
                    <div class="col-md-4 mt-3 mt-md-0">
                        <div class="d-flex justify-content-md-end align-items-center">
                            <label class="text-white-50 me-2 text-nowrap font-size-13 mb-0">Switch Workspace:</label>
                            <select name="switch_assignment" class="form-select form-select-sm bg-white text-dark border-0 shadow-sm" style="width: auto; min-width: 180px;" onchange="window.location.href = window.location.pathname + '?church_id=' + this.value.split('-')[0] + '&unit_id=' + this.value.split('-')[1];">
                                <?php foreach ($assignments as $assign): ?>
                                    <option value="<?= $assign['church_id'] ?>-<?= $assign['unit_id'] ?>" <?= ((int)$assign['church_id'] === (int)$churchId && (int)$assign['unit_id'] === (int)$unitId) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($assign['unit_name'] . ' (' . $assign['church_name'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Widgets -->
<div class="row">
    <!-- Members Count Widget -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm position-relative overflow-hidden mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="text-muted mb-0">Active Members</h5>
                    <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center font-size-20">
                        <i class="bx bx-group"></i>
                    </div>
                </div>
                <h2 class="mb-1 fw-bold"><?= $stats['members_count'] ?></h2>
                <a href="<?= \App\Utilities\AssetHelper::url('my-unit/members') ?>" class="text-primary font-size-13 fw-semibold">
                    Manage Roster <i class="bx bx-right-arrow-alt align-middle ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Attendance Widget -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm position-relative overflow-hidden mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="text-muted mb-0">Avg. Attendance (Last 5)</h5>
                    <div class="avatar-sm bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center font-size-20">
                        <i class="bx bx-calendar-check"></i>
                    </div>
                </div>
                <h2 class="mb-1 fw-bold"><?= $stats['avg_attendance'] ?></h2>
                <a href="<?= \App\Utilities\AssetHelper::url('my-unit/attendance') ?>" class="text-success font-size-13 fw-semibold">
                    Mark Roll Call <i class="bx bx-right-arrow-alt align-middle ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Finances Widget -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm position-relative overflow-hidden mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="text-muted mb-0">Net Balance</h5>
                    <div class="avatar-sm bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center font-size-20">
                        <i class="bx bx-wallet"></i>
                    </div>
                </div>
                <h2 class="mb-1 fw-bold">$<?= number_format($stats['net_balance'], 2) ?></h2>
                <a href="<?= \App\Utilities\AssetHelper::url('my-unit/finance') ?>" class="text-warning font-size-13 fw-semibold">
                    View Ledger <i class="bx bx-right-arrow-alt align-middle ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Reports Table -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                <h5 class="mb-0 fw-bold"><i class="bx bx-file-find me-2 text-primary"></i>Recent Reports</h5>
                <a href="<?= \App\Utilities\AssetHelper::url('my-unit/reports') ?>" class="btn btn-sm btn-light font-size-12 fw-semibold">Submit New</a>
            </div>
            <div class="card-body px-4 pb-4">
                <?php if (empty($recentReports)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bx bx-file-blank display-4"></i>
                        <p class="mt-2 mb-0">No reports submitted yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-centered table-borderless table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted">
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentReports as $report): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark"><?= htmlspecialchars($report['title']) ?></span>
                                        </td>
                                        <td><span class="badge bg-soft-info text-info"><?= ucfirst($report['report_type']) ?></span></td>
                                        <td class="text-muted font-size-13"><?= date('M j, Y', strtotime($report['created_at'])) ?></td>
                                        <td><span class="badge bg-success">Submitted</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Finances Table -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                <h5 class="mb-0 fw-bold"><i class="bx bx-list-ol me-2 text-primary"></i>Recent Transactions</h5>
                <a href="<?= \App\Utilities\AssetHelper::url('my-unit/finance') ?>" class="btn btn-sm btn-light font-size-12 fw-semibold">Record Transaction</a>
            </div>
            <div class="card-body px-4 pb-4">
                <?php if (empty($recentFinance)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bx bx-receipt display-4"></i>
                        <p class="mt-2 mb-0">No financial records recorded yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-centered table-borderless table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted">
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentFinance as $record): ?>
                                    <tr>
                                        <td class="text-muted font-size-13"><?= date('M j, Y', strtotime($record['transaction_date'])) ?></td>
                                        <td>
                                            <?php if ($record['transaction_type'] === 'income'): ?>
                                                <span class="badge bg-soft-success text-success"><i class="bx bx-trending-up me-1"></i>Income</span>
                                            <?php else: ?>
                                                <span class="badge bg-soft-danger text-danger"><i class="bx bx-trending-down me-1"></i>Expense</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-dark fw-semibold"><?= htmlspecialchars($record['category']) ?></td>
                                        <td class="text-end fw-bold <?= $record['transaction_type'] === 'income' ? 'text-success' : 'text-danger' ?>">
                                            <?= $record['transaction_type'] === 'income' ? '+' : '-' ?>$<?= number_format($record['amount'], 2) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
