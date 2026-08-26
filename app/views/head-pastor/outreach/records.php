<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$reports = $reports ?? [];
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Outreach & Event Records</h4>
                <div class="d-flex gap-2">
                    <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/export") ?>" class="btn btn-sm btn-info">
                        <i class="bx bx-download me-1"></i> Export CSV
                    </a>
                    <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/create") ?>" class="btn btn-sm btn-success">
                        <i class="bx bx-plus me-1"></i> New Report
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="outreachRecordsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Unit</th>
                                <th class="text-center">Attendance</th>
                                <th class="text-center">First Timers</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $index => $r): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <h5 class="font-size-14 mb-1">
                                            <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/{$r['id']}") ?>" class="text-dark">
                                                <?= htmlspecialchars($r['title']) ?>
                                            </a>
                                        </h5>
                                        <small class="text-muted"><?= substr(strip_tags($r['description'] ?? ''), 0, 50) ?>...</small>
                                    </td>
                                    <td><?= date('d M, Y', strtotime($r['program_date'])) ?></td>
                                    <td><?= htmlspecialchars($r['unit_name'] ?? 'General') ?></td>
                                    <td class="text-center fw-bold text-primary"><?= number_format($r['total_attendance'] ?? 0) ?></td>
                                    <td class="text-center fw-bold text-success"><?= number_format($r['first_timers_count'] ?? 0) ?></td>
                                    <td class="text-center">
                                        <?php 
                                            $statusClass = 'bg-soft-secondary text-secondary';
                                            if ($r['status'] === 'submitted') $statusClass = 'bg-soft-primary text-primary';
                                            elseif ($r['status'] === 'approved') $statusClass = 'bg-soft-success text-success';
                                        ?>
                                        <span class="badge rounded-pill <?= $statusClass ?> font-size-11">
                                            <?= ucfirst($r['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/{$r['id']}") ?>" class="btn btn-sm btn-light" title="View">
                                                <i class="bx bx-show-alt"></i>
                                            </a>
                                            <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/{$r['id']}/edit") ?>" class="btn btn-sm btn-soft-primary" title="Edit">
                                                <i class="bx bx-edit-alt"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmDelete(<?= $r['id'] ?>)" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" value="<?= App\Utilities\Security::generateCSRFToken() ?>">
</form>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this outreach report? This action cannot be undone.')) {
        const form = document.getElementById('deleteForm');
        form.action = `<?= AssetHelper::url("churches/{$churchId}/outreach") ?>/${id}`;
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Optional: Add DataTable initialization if available
    if (typeof jQuery !== 'undefined' && typeof jQuery().DataTable !== 'undefined') {
        jQuery('#outreachRecordsTable').DataTable({
            "order": [[2, "desc"]],
            "pageLength": 25
        });
    }
});
</script>
