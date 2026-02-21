<?php
use App\Utilities\AssetHelper;
$detail = $detail ?? [];
$scopeLabel = $scopeLabel ?? '';
$eventTypeLabel = $eventTypeLabel ?? ucfirst(str_replace('_', ' ', $detail['event_type'] ?? ''));
$serviceDescription = $detail['service_description'] ?? '';
$present = $detail['present'] ?? [];
$absent = $detail['absent'] ?? [];
$presentCount = count($present);
$absentCount = count($absent);
$total = $presentCount + $absentCount;
$segmentCounts = $segmentCounts ?? [];
?>
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0"><?= htmlspecialchars($scopeLabel) ?></h4>
                <p class="card-title-desc mb-0 mt-1"><?= htmlspecialchars($eventTypeLabel) ?> — <?= date('F d, Y', strtotime($detail['event_date'] ?? '')) ?></p>
                <?php if ($serviceDescription): ?>
                    <p class="text-muted small mb-0 mt-1"><?= htmlspecialchars($serviceDescription) ?></p>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-0">
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h3 class="text-success mb-0"><?= $presentCount ?></h3>
                            <small class="text-muted">Present</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h3 class="text-secondary mb-0"><?= $absentCount ?></h3>
                            <small class="text-muted">Absent</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h3 class="text-primary mb-0"><?= $total ?></h3>
                            <small class="text-muted">Total marked</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bx bx-pie-chart-alt me-1"></i> Attendance by segment (returning vs first-timer × age group)</h5>
                <p class="card-title-desc mb-0 mt-1 small text-muted">Present only. First timer = first time at this church.</p>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="text-muted border-bottom pb-2">Returning (attended before at this church)</h6>
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <strong class="text-primary"><?= (int)($segmentCounts['returning_adults'] ?? 0) ?></strong>
                                    <div class="small text-muted">Adults</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <strong class="text-info"><?= (int)($segmentCounts['returning_children'] ?? 0) ?></strong>
                                    <div class="small text-muted">Children</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <strong class="text-info"><?= (int)($segmentCounts['returning_teens'] ?? 0) ?></strong>
                                    <div class="small text-muted">Teens</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted border-bottom pb-2">First timers (first time at this church)</h6>
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <strong class="text-success"><?= (int)($segmentCounts['first_timer_adults'] ?? 0) ?></strong>
                                    <div class="small text-muted">Adults</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <strong class="text-warning"><?= (int)($segmentCounts['first_timer_children'] ?? 0) ?></strong>
                                    <div class="small text-muted">Children</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <strong class="text-warning"><?= (int)($segmentCounts['first_timer_teens'] ?? 0) ?></strong>
                                    <div class="small text-muted">Teens</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bx bx-user-check me-1 text-success"></i> Present (<?= $presentCount ?>)</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($present)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($present as $i => $user): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?></td>
                                                <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted p-3 mb-0">No members marked present.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bx bx-user-x me-1 text-secondary"></i> Absent (<?= $absentCount ?>)</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($absent)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($absent as $i => $user): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?></td>
                                                <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted p-3 mb-0">No members marked absent.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <?php
            $backUrl = AssetHelper::url('attendance');
            if (!empty($detail['church_id'])) {
                $backUrl .= '?church_id=' . (int)$detail['church_id'];
            }
            ?>
            <a href="<?= $backUrl ?>" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to attendance
            </a>
        </div>
    </div>
</div>
