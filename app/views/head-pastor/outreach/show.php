<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$report = $report ?? null;
$publicity = $publicity ?? [];
$logistics = $logistics ?? [];
$costs = $costs ?? [];
$challenges = $challenges ?? [];
$targets = $targets ?? [];
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-1"><?= htmlspecialchars($report['title']) ?></h3>
                        <p class="text-muted mb-0">
                            <i class="bx bx-calendar me-1"></i> <?= date('M d, Y', strtotime($report['program_date'])) ?>
                            <?php if ($report['end_date']): ?> - <?= date('M d, Y', strtotime($report['end_date'])) ?><?php endif; ?>
                            <span class="mx-2">|</span>
                            <i class="bx bx-map-pin me-1"></i> <?= htmlspecialchars($report['unit_name'] ?? 'General') ?>
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/{$report['id']}/edit") ?>" class="btn btn-primary">
                            <i class="bx bx-edit-alt me-1"></i> Edit Report
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="bx bx-trash me-1"></i> Delete
                        </button>
                        <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/records") ?>" class="btn btn-light">
                            <i class="bx bx-arrow-back me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Details & Stats -->
    <div class="col-lg-8">
        <!-- Overview & Description -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">Overview</h5>
            </div>
            <div class="card-body">
                <p class="lead font-size-15"><?= nl2br(htmlspecialchars($report['description'] ?? 'No description provided.')) ?></p>
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted mb-1 d-block">Overall Status</label>
                        <?php 
                            $statusClass = 'bg-soft-secondary text-secondary';
                            if ($report['status'] === 'submitted') $statusClass = 'bg-soft-primary text-primary';
                            elseif ($report['status'] === 'approved') $statusClass = 'bg-soft-success text-success';
                        ?>
                        <span class="badge rounded-pill <?= $statusClass ?> font-size-12 px-3 py-2">
                            <?= ucfirst($report['status']) ?>
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted mb-1 d-block">Reported By</label>
                        <span class="fw-bold font-size-14"><?= htmlspecialchars($report['creator_name'] ?? 'System') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Targets & Outcomes -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">Outcome Targets</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Target Metric</th>
                                <th class="text-center">Goal</th>
                                <th class="text-center">Actual</th>
                                <th class="text-center">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($targets as $t): ?>
                                <?php 
                                    $perf = $t['target_value'] > 0 ? ($t['actual_value'] / $t['target_value']) * 100 : 0;
                                    $perfColor = 'text-danger';
                                    if ($perf >= 90) $perfColor = 'text-success';
                                    elseif ($perf >= 60) $perfColor = 'text-primary';
                                    elseif ($perf >= 30) $perfColor = 'text-warning';
                                ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold"><?= htmlspecialchars($t['target_name']) ?></span>
                                        <br><small class="text-muted"><?= htmlspecialchars($t['notes'] ?? '') ?></small>
                                    </td>
                                    <td class="text-center"><?= number_format($t['target_value'], 1) ?> <?= htmlspecialchars($t['unit'] ?? '') ?></td>
                                    <td class="text-center fw-bold"><?= number_format($t['actual_value'], 1) ?> <?= htmlspecialchars($t['unit'] ?? '') ?></td>
                                    <td class="text-center">
                                        <span class="fw-bold <?= $perfColor ?>"><?= round($perf) ?>%</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($targets)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No specific targets recorded.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Logistics Area (End of Card) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">Logistics & Execution</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if (empty($logistics)): ?>
                        <div class="col-12 text-center py-3 text-muted">No logistics details recorded.</div>
                    <?php else: ?>
                        <?php foreach ($logistics as $l): ?>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-soft-primary text-primary text-uppercase font-size-10 me-2"><?= htmlspecialchars($l['category']) ?></span>
                                        <h6 class="mb-0"><?= htmlspecialchars($l['description']) ?></h6>
                                    </div>
                                    <p class="text-muted small mb-0"><?= htmlspecialchars($l['notes'] ?? 'Status: Operational') ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Event Gallery -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">Event Gallery</h5>
            </div>
            <div class="card-body">
                <?php if (empty($images)): ?>
                    <div class="text-center py-5 text-muted bg-light rounded-3">
                        <i class="bx bx-images font-size-40 d-block mb-3"></i>
                        <p class="mb-0">No photos uploaded for this report.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($images as $img): ?>
                            <div class="col-md-3 col-6">
                                <a href="javascript:void(0)" class="gallery-item" onclick="openLightbox('<?= AssetHelper::url($img['file_path']) ?>')">
                                    <img src="<?= AssetHelper::url($img['file_path']) ?>" class="img-thumbnail w-100 shadow-sm transition-transform" 
                                         style="object-fit: cover; aspect-ratio: 4/3; cursor: zoom-in;" 
                                         onmouseover="this.style.transform='scale(1.03)'" 
                                         onmouseout="this.style.transform='scale(1)'">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Lightbox Modal -->
    <div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 bg-transparent">
                <div class="modal-body p-0 position-relative text-center">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 shadow" data-bs-dismiss="modal" aria-label="Close"></button>
                    <img src="" id="lightboxImg" class="img-fluid rounded shadow-lg" style="max-height: 85vh;">
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Stats & Publicity -->
    <div class="col-lg-4">
        <!-- Attendance Card -->
        <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
            <div class="card-body">
                <h5 class="text-white-50 mb-3 text-uppercase font-size-12">Attendance Impact</h5>
                <div class="d-flex align-items-end mb-4">
                    <h2 class="text-white mb-0 me-2"><?= number_format($report['total_attendance'] ?? 0) ?></h2>
                    <span class="text-white-50">Total attendees</span>
                </div>
                <div class="row text-center mt-3 pt-3 border-top border-white-50">
                    <div class="col-6 border-end border-white-50">
                        <h4 class="text-white mb-0"><?= number_format($report['first_timers_count'] ?? 0) ?></h4>
                        <small class="text-white-50">First Timers</small>
                    </div>
                    <div class="col-6">
                        <?php $per = $report['total_attendance'] > 0 ? (($report['first_timers_count'] ?? 0) / $report['total_attendance']) * 100 : 0; ?>
                        <h4 class="text-white mb-0"><?= round($per) ?>%</h4>
                        <small class="text-white-50">Conversion</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">Financial Summary</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Budget Used</span>
                        <?php $eff = $report['budget_total'] > 0 ? (($report['actual_total'] ?? 0) / $report['budget_total']) * 100 : 0; ?>
                        <span class="fw-bold <?= $eff > 100 ? 'text-danger' : 'text-success' ?>"><?= round($eff) ?>%</span>
                    </div>
                    <div class="progress animated-progess custom-progress">
                        <div class="progress-bar <?= $eff > 100 ? 'bg-danger' : 'bg-success' ?>" role="progressbar" style="width: <?= min($eff, 100) ?>%"></div>
                    </div>
                </div>
                <div class="d-grid gap-3">
                    <div class="bg-light p-3 rounded">
                        <span class="text-muted d-block small mb-1">Budgeted Amount</span>
                        <h5 class="mb-0">₦<?= number_format($report['budget_total'] ?? 0, 2) ?></h5>
                    </div>
                    <div class="bg-light p-3 rounded">
                        <span class="text-muted d-block small mb-1">Actual Expenditure</span>
                        <h5 class="mb-0 text-primary">₦<?= number_format($report['actual_total'] ?? 0, 2) ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Publicity Channels -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">Publicity Channels</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($publicity as $p): ?>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($p['channel']) ?></h6>
                                <small class="text-muted"><?= number_format($p['estimated_reach'] ?? 0) ?> est. reach</small>
                            </div>
                            <span class="text-primary fw-bold">₦<?= number_format($p['cost'] ?? 0, 2) ?></span>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($publicity)): ?>
                        <li class="list-group-item px-0 text-center text-muted py-3">No publicity data recorded.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" action="<?= AssetHelper::url("churches/{$churchId}/outreach/{$report['id']}") ?>" style="display: none;">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" value="<?= App\Utilities\Security::generateCSRFToken() ?>">
</form>

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this outreach report? This cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}

function openLightbox(url) {
    document.getElementById('lightboxImg').src = url;
    var modal = new bootstrap.Modal(document.getElementById('lightboxModal'));
    modal.show();
}
</script>
