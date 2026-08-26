<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Unit Narrative Reports</h4>
                    <p class="card-title-desc mb-0">Monitor weekly and departmental reports from all units in <?= htmlspecialchars($church['name']) ?></p>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filter Form -->
                <form method="GET" action="<?= AssetHelper::url('churches/' . $church['id'] . '/unit-reports') ?>" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search keywords..." value="<?= htmlspecialchars($search ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Unit</label>
                            <select name="unit_id" class="form-select">
                                <option value="">All Units</option>
                                <?php foreach ($units as $unit): ?>
                                    <option value="<?= $unit['unit_id'] ?>" <?= ($unit_id ?? '') == $unit['unit_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($unit['unit_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Report Type</label>
                            <select name="report_type" class="form-select">
                                <option value="">All Types</option>
                                <?php foreach ($reportTypes as $type): ?>
                                    <option value="<?= htmlspecialchars($type) ?>" <?= ($report_type ?? '') === $type ? 'selected' : '' ?>>
                                        <?= ucfirst(htmlspecialchars($type)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="submitted" <?= ($status ?? '') === 'submitted' ? 'selected' : '' ?>>Submitted</option>
                                <option value="draft" <?= ($status ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="" <?= ($status ?? '') === '' ? 'selected' : '' ?>>All</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                            <a href="<?= AssetHelper::url('churches/' . $church['id'] . '/unit-reports') ?>" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table id="unit-reports-datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th>Report Title</th>
                                <th>Unit</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Author</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td>
                                        <h6 class="mb-0"><?= htmlspecialchars($report['title']) ?></h6>
                                    </td>
                                    <td>
                                        <a href="<?= AssetHelper::url("churches/{$church['id']}/performance/{$report['unit_id']}") ?>">
                                            <span class="badge badge-soft-primary"><?= htmlspecialchars($report['unit_name'] ?? 'N/A') ?></span>
                                        </a>
                                    </td>
                                    <td><?= ucfirst($report['report_type']) ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'submitted' => 'success',
                                            'approved' => 'info',
                                            'rejected' => 'danger'
                                        ];
                                        $statusColor = $statusColors[$report['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $statusColor ?>"><?= ucfirst($report['status']) ?></span>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars(($report['first_name'] ?? '') . ' ' . ($report['last_name'] ?? '')) ?>
                                    </td>
                                    <td>
                                        <?= $report['submitted_at'] ? date('M d, Y', strtotime($report['submitted_at'])) : date('M d, Y', strtotime($report['created_at'])) ?>
                                    </td>
                                    <td>
                                        <a href="<?= AssetHelper::url('churches/' . $church['id'] . '/unit-reports/' . $report['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="bx bx-show me-1"></i> View
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
</div>

<script>
    $(document).ready(function() {
        $('#unit-reports-datatable').DataTable({
            responsive: true,
            order: [[5, 'desc']],
            pageLength: 25
        });
    });
</script>
