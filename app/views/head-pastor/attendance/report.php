<?php
use App\Utilities\AssetHelper;
use App\Models\Attendance;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$startDate = $startDate ?? date('Y-m-01');
$endDate = $endDate ?? date('Y-m-t');
$segmentCounts = $segmentCounts ?? [];
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Attendance Report</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url("churches/{$churchId}/attendance") ?>">Attendance</a></li>
                    <li class="breadcrumb-item active">Report</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="<?= AssetHelper::url("churches/{$churchId}/attendance/report") ?>" class="row g-3 align-items-end mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-filter-alt me-1"></i> Generate Report
                        </button>
                    </div>
                </form>

                <div class="text-center mb-5">
                    <h4 class="mb-1"><?= htmlspecialchars($church['name']) ?></h4>
                    <p class="text-muted text-uppercase mb-0">Attendance Report</p>
                    <p class="text-muted small"><?= date('M j, Y', strtotime($startDate)) ?> — <?= date('M j, Y', strtotime($endDate)) ?></p>
                </div>

                <div class="row mb-5">
                    <div class="col-md-6 border-end">
                        <h5 class="text-center mb-4 text-muted">Membership Retention</h5>
                        <div class="d-flex justify-content-around text-center">
                            <div>
                                <h3 class="fw-bold text-primary"><?= (int)($segmentCounts['returning_adults'] ?? 0) + (int)($segmentCounts['returning_children'] ?? 0) + (int)($segmentCounts['returning_teens'] ?? 0) ?></h3>
                                <p class="text-muted mb-0">Returning</p>
                            </div>
                            <div>
                                <h3 class="fw-bold text-success"><?= (int)($segmentCounts['first_timer_adults'] ?? 0) + (int)($segmentCounts['first_timer_children'] ?? 0) + (int)($segmentCounts['first_timer_teens'] ?? 0) ?></h3>
                                <p class="text-muted mb-0">First Timers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-center mb-4 text-muted">Demographics</h5>
                        <div class="d-flex justify-content-around text-center">
                            <div>
                                <h3 class="fw-bold text-dark"><?= (int)($segmentCounts['returning_adults'] ?? 0) + (int)($segmentCounts['first_timer_adults'] ?? 0) ?></h3>
                                <p class="text-muted mb-0">Adults</p>
                            </div>
                            <div>
                                <h3 class="fw-bold text-dark"><?= (int)($segmentCounts['returning_teens'] ?? 0) + (int)($segmentCounts['first_timer_teens'] ?? 0) ?></h3>
                                <p class="text-muted mb-0">Teens</p>
                            </div>
                            <div>
                                <h3 class="fw-bold text-dark"><?= (int)($segmentCounts['returning_children'] ?? 0) + (int)($segmentCounts['first_timer_children'] ?? 0) ?></h3>
                                <p class="text-muted mb-0">Children</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-light border-0 py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="bx bx-info-circle font-size-24 text-info"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 text-muted">This report represents combined attendance data for all services within the selected period. First timers are identified as members attending their first service at this church during this range.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
