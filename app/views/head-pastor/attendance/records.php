<?php
use App\Utilities\AssetHelper;
use App\Models\Attendance;

$church = $church ?? null;
$services = $services ?? [];
$eventTypeLabels = Attendance::getEventTypes();
$churchId = $church['id'] ?? 0;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Attendance Records</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url("churches/{$churchId}/attendance") ?>">Attendance</a></li>
                    <li class="breadcrumb-item active">Records</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                <h4 class="card-title mb-0">Recorded Services</h4>
                <div class="d-flex gap-2">
                    <a href="<?= AssetHelper::url("churches/{$churchId}/attendance/export") ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-download me-1"></i> Export CSV
                    </a>
                    <a href="<?= AssetHelper::url("churches/{$churchId}/attendance/mark") ?>" class="btn btn-sm btn-success">
                        <i class="bx bx-plus me-1"></i> Record New
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="attendance-datatable" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Event type</th>
                            <th>Scope</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Absent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $svc): ?>
                            <?php
                            $present = (int)($svc['present_count'] ?? 0);
                            $absent = (int)($svc['absent_count'] ?? 0);
                            $unitId = $svc['unit_id'] ?? null;
                            $scopeLabel = ($unitId === null) ? 'All church' : ($svc['unit_name'] ?? 'Unit #' . (int)$unitId);
                            $serviceUrl = AssetHelper::url("churches/{$churchId}/attendance/service") . '?event_date=' . rawurlencode($svc['event_date']) . '&event_type=' . rawurlencode($svc['event_type']);
                            if ($unitId !== null) {
                                $serviceUrl .= '&unit_id=' . (int)$unitId;
                            }
                            ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($svc['event_date'])) ?></td>
                                <td>
                                    <span class="badge rounded-pill bg-soft-info text-info"><?= htmlspecialchars($eventTypeLabels[$svc['event_type']] ?? str_replace('_', ' ', $svc['event_type'] ?? '')) ?></span>
                                    <?php if (!empty($svc['service_description'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($svc['service_description']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($scopeLabel) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?= $present ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $absent ?></span>
                                </td>
                                <td>
                                    <a href="<?= $serviceUrl ?>" class="btn btn-sm btn-outline-primary" title="View Detail">
                                        <i class="bx bx-show me-1"></i> View
                                    </a>
                                    <a href="<?= AssetHelper::url("churches/{$churchId}/attendance/mark") . "?unit_id=" . ($unitId ?? 0) . "&event_date=" . $svc['event_date'] . "&event_type=" . $svc['event_type'] ?>" class="btn btn-sm btn-outline-info" title="Edit">
                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#attendance-datatable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
