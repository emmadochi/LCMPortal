<?php
use App\Utilities\AssetHelper;
$church = $church ?? null;
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
$churchId = $church['id'] ?? 0;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Service Detail</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url("churches/{$churchId}/attendance") ?>">Attendance</a></li>
                    <li class="breadcrumb-item active">Service Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- Service Info Header -->
        <div class="card bg-soft-primary border-0 mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-primary me-2"><?= htmlspecialchars($eventTypeLabel) ?></span>
                            <span class="text-muted fw-bold"><?= date('l, F j, Y', strtotime($detail['event_date'] ?? '')) ?></span>
                        </div>
                        <h3 class="card-title text-primary mb-1"><?= htmlspecialchars($scopeLabel) ?></h3>
                        <?php if ($serviceDescription): ?>
                            <p class="text-muted mb-0"><i class="bx bx-info-circle me-1"></i><?= htmlspecialchars($serviceDescription) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                        <div class="d-flex gap-2 justify-content-sm-end">
                            <a href="<?= AssetHelper::url("churches/{$churchId}/attendance/mark") . "?unit_id=" . ($detail['unit_id'] ?? 0) . "&event_date=" . $detail['event_date'] . "&event_type=" . $detail['event_type'] ?>" class="btn btn-primary">
                                <i class="bx bx-edit-alt me-1"></i> Edit Record
                            </a>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4 border-primary opacity-10">
                
                <div class="row text-center">
                    <div class="col-4 border-end">
                        <h4 class="mb-0 text-success fw-bold"><?= $presentCount ?></h4>
                        <p class="text-muted mb-0 small uppercase">Present</p>
                    </div>
                    <div class="col-4 border-end">
                        <h4 class="mb-0 text-danger fw-bold"><?= $absentCount ?></h4>
                        <p class="text-muted mb-0 small uppercase">Absent</p>
                    </div>
                    <div class="col-4">
                        <h4 class="mb-0 text-primary fw-bold"><?= $total ?></h4>
                        <p class="text-muted mb-0 small uppercase">Total Marked</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segmentation Breakdown -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-transparent py-3">
                <h5 class="card-title mb-0"><i class="bx bx-pie-chart-alt-2 me-2 text-primary"></i>Attendance Segmentation</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6 border-end">
                        <h6 class="text-muted mb-3 d-flex align-items-center">
                            <i class="bx bx-repeat me-2 text-info"></i> Returning Members
                        </h6>
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="bg-light rounded p-3 text-center h-100">
                                    <h4 class="mb-1 text-dark"><?= (int)($segmentCounts['returning_adults'] ?? 0) ?></h4>
                                    <div class="text-muted small">Adults</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light rounded p-3 text-center h-100">
                                    <h4 class="mb-1 text-dark"><?= (int)($segmentCounts['returning_children'] ?? 0) ?></h4>
                                    <div class="text-muted small">Children</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light rounded p-3 text-center h-100">
                                    <h4 class="mb-1 text-dark"><?= (int)($segmentCounts['returning_teens'] ?? 0) ?></h4>
                                    <div class="text-muted small">Teens</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3 d-flex align-items-center">
                            <i class="bx bx-user-plus me-2 text-success"></i> First Timers
                        </h6>
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="bg-light rounded p-3 text-center h-100">
                                    <h4 class="mb-1 text-dark"><?= (int)($segmentCounts['first_timer_adults'] ?? 0) ?></h4>
                                    <div class="text-muted small">Adults</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light rounded p-3 text-center h-100">
                                    <h4 class="mb-1 text-dark"><?= (int)($segmentCounts['first_timer_children'] ?? 0) ?></h4>
                                    <div class="text-muted small">Children</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light rounded p-3 text-center h-100">
                                    <h4 class="mb-1 text-dark"><?= (int)($segmentCounts['first_timer_teens'] ?? 0) ?></h4>
                                    <div class="text-muted small">Teens</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Present/Absent Lists -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-success"><i class="bx bx-check-double me-2"></i>Present (<?= $presentCount ?>)</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($present)): ?>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Age Group</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($present as $i => $user): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td>
                                                    <div class="fw-bold"><?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?></div>
                                                    <?php if ($user['is_first_timer'] ?? false): ?>
                                                        <span class="badge bg-soft-warning text-warning font-size-10">FIRST TIMER</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><span class="badge bg-light text-dark"><?= htmlspecialchars(ucfirst($user['age_group'] ?? 'adult')) ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">No members marked present.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-transparent py-3">
                        <h5 class="card-title mb-0 text-muted"><i class="bx bx-x me-2"></i>Absent (<?= $absentCount ?>)</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($absent)): ?>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Age Group</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($absent as $i => $user): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><div class="fw-bold text-muted"><?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?></div></td>
                                                <td><span class="badge bg-light text-muted"><?= htmlspecialchars(ucfirst($user['age_group'] ?? 'adult')) ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">No members marked absent.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 pb-4">
            <a href="<?= AssetHelper::url("churches/{$churchId}/attendance") ?>" class="btn btn-light px-4">
                <i class="bx bx-arrow-back me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>
