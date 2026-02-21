<?php
use App\Utilities\AssetHelper;
$r = $report;
$statusColors = ['draft' => 'secondary', 'submitted' => 'primary', 'approved' => 'success'];
?>
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-1"><?= htmlspecialchars($r['title']) ?></h4>
                    <p class="text-muted mb-0">
                        <span class="badge bg-<?= $statusColors[$r['status']] ?? 'secondary' ?>"><?= ucfirst($r['status']) ?></span>
                        <?php if (!empty($r['church_name'])): ?>
                            <span class="ms-2"><?= htmlspecialchars($r['church_name']) ?></span>
                            <?php if (!empty($r['unit_name'])): ?> / <?= htmlspecialchars($r['unit_name']) ?><?php endif; ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="d-flex gap-1">
                    <a href="<?= AssetHelper::url('outreach-reports/' . $r['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary"><i data-feather="edit" class="icon-sm"></i> Edit</a>
                    <a href="<?= AssetHelper::url('outreach-reports') ?>" class="btn btn-sm btn-outline-secondary">Back to list</a>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($r['description'])): ?>
                    <div class="mb-4">
                        <h6>Description</h6>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($r['description'])) ?></p>
                    </div>
                <?php endif; ?>
                <div class="row mb-4">
                    <div class="col-md-3"><strong>Program date</strong><br><?= date('F j, Y', strtotime($r['program_date'])) ?></div>
                    <?php if (!empty($r['end_date'])): ?>
                        <div class="col-md-3"><strong>End date</strong><br><?= date('F j, Y', strtotime($r['end_date'])) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($r['event_title'])): ?>
                        <div class="col-md-6"><strong>Linked event</strong><br><?= htmlspecialchars($r['event_title']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="h4 mb-0"><?= $r['total_attendance'] !== null ? (int)$r['total_attendance'] : '—' ?></div>
                            <small class="text-muted">Attendance</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="h4 mb-0"><?= $r['first_timers_count'] !== null ? (int)$r['first_timers_count'] : '—' ?></div>
                            <small class="text-muted">First-timers</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="h4 mb-0"><?= $r['budget_total'] !== null ? number_format((float)$r['budget_total'], 2) : '—' ?></div>
                            <small class="text-muted">Budget total</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="h4 mb-0"><?= $r['actual_total'] !== null ? number_format((float)$r['actual_total'], 2) : '—' ?></div>
                            <small class="text-muted">Actual total</small>
                        </div>
                    </div>
                </div>

                <?php if (!empty($publicity)): ?>
                    <h6 class="border-bottom pb-2 mb-2">Publicity</h6>
                    <table class="table table-sm table-bordered mb-4">
                        <thead><tr><th>Channel</th><th>Details</th><th>Est. reach</th><th>Cost</th></tr></thead>
                        <tbody>
                            <?php foreach ($publicity as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['channel']) ?></td>
                                    <td><?= htmlspecialchars($p['details'] ?? '') ?></td>
                                    <td><?= $p['estimated_reach'] !== null ? (int)$p['estimated_reach'] : '—' ?></td>
                                    <td><?= $p['cost'] !== null ? number_format((float)$p['cost'], 2) : '—' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php if (!empty($logistics)): ?>
                    <h6 class="border-bottom pb-2 mb-2">Logistics</h6>
                    <table class="table table-sm table-bordered mb-4">
                        <thead><tr><th>Category</th><th>Description</th><th>Notes</th></tr></thead>
                        <tbody>
                            <?php foreach ($logistics as $l): ?>
                                <tr>
                                    <td><?= htmlspecialchars(ucfirst($l['category'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($l['description'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($l['notes'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php if (!empty($costs)): ?>
                    <h6 class="border-bottom pb-2 mb-2">Cost breakdown</h6>
                    <table class="table table-sm table-bordered mb-4">
                        <thead><tr><th>Category</th><th>Budgeted</th><th>Actual</th><th>Notes</th></tr></thead>
                        <tbody>
                            <?php foreach ($costs as $c): ?>
                                <tr>
                                    <td><?= htmlspecialchars($c['category'] ?? '') ?></td>
                                    <td><?= $c['budgeted_amount'] !== null ? number_format((float)$c['budgeted_amount'], 2) : '—' ?></td>
                                    <td><?= $c['actual_amount'] !== null ? number_format((float)$c['actual_amount'], 2) : '—' ?></td>
                                    <td><?= htmlspecialchars($c['notes'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php if (!empty($challenges)): ?>
                    <h6 class="border-bottom pb-2 mb-2">Challenges</h6>
                    <ul class="list-group mb-4">
                        <?php foreach ($challenges as $ch): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <span><?= htmlspecialchars($ch['description']) ?></span>
                                <span>
                                    <?php if (!empty($ch['category'])): ?><span class="badge bg-secondary"><?= htmlspecialchars($ch['category']) ?></span><?php endif; ?>
                                    <?php if (!empty($ch['severity'])): ?><span class="badge bg-<?= $ch['severity'] === 'high' ? 'danger' : ($ch['severity'] === 'medium' ? 'warning' : 'info') ?>"><?= ucfirst($ch['severity']) ?></span><?php endif; ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (!empty($targets)): ?>
                    <h6 class="border-bottom pb-2 mb-2">Targets vs actuals</h6>
                    <table class="table table-sm table-bordered mb-4">
                        <thead><tr><th>Metric</th><th>Target</th><th>Actual</th><th>Unit</th><th>Notes</th></tr></thead>
                        <tbody>
                            <?php foreach ($targets as $t): ?>
                                <tr>
                                    <td><?= htmlspecialchars($t['target_name']) ?></td>
                                    <td><?= number_format((float)$t['target_value'], 2) ?></td>
                                    <td><?= $t['actual_value'] !== null ? number_format((float)$t['actual_value'], 2) : '—' ?></td>
                                    <td><?= htmlspecialchars($t['unit'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($t['notes'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Report info</h5></div>
            <div class="card-body">
                <table class="table table-sm table-nowrap mb-0">
                    <tr><th>Created by</th><td><?= htmlspecialchars(trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''))) ?: '—' ?></td></tr>
                    <tr><th>Created</th><td><?= date('M j, Y g:i A', strtotime($r['created_at'])) ?></td></tr>
                    <tr><th>Updated</th><td><?= date('M j, Y g:i A', strtotime($r['updated_at'] ?? $r['created_at'])) ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
