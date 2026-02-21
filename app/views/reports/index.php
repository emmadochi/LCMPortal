<?php
use App\Utilities\AssetHelper;
$churchFilter = $churchFilter ?? null;
?>
<?php if (!empty($churchFilter)): ?>
<div class="alert alert-info d-flex align-items-center justify-content-between mb-3" role="alert">
    <span><i class="bx bx-church me-2"></i>Viewing church: <strong><?= htmlspecialchars($churchFilter['name']) ?></strong></span>
    <a href="<?= AssetHelper::url('reports') ?>" class="btn btn-sm btn-outline-primary">View all</a>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Reports</h4>
                    <p class="card-title-desc mb-0">View and manage all reports</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="download" class="me-1"></i> Export
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="<?= AssetHelper::url('reports/export?format=csv') ?>">
                                CSV
                            </a>
                            <a class="dropdown-item" href="<?= AssetHelper::url('reports/export?format=excel') ?>">
                                Excel (.xls)
                            </a>
                            <a class="dropdown-item" href="<?= AssetHelper::url('reports/export?format=json') ?>">
                                JSON
                            </a>
                            <a class="dropdown-item" href="<?= AssetHelper::url('reports/export?format=pdf') ?>">
                                PDF
                            </a>
                        </div>
                    </div>
                    <a href="<?= AssetHelper::url('reports/create') ?>" class="btn btn-primary">
                        <i data-feather="file-plus" class="me-1"></i> Create Report
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filter Form -->
                <form method="GET" action="<?= AssetHelper::url('reports') ?>" class="mb-4">
                    <?php if (!empty($churchFilter)): ?><input type="hidden" name="church_id" value="<?= (int)$churchFilter['id'] ?>"><?php endif; ?>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search reports..." value="<?= htmlspecialchars($search ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <select name="unit_id" class="form-select">
                                <option value="">All Units</option>
                                <?php foreach (($units ?? []) as $unit): ?>
                                    <option value="<?= $unit['id'] ?>" <?= ($unit_id ?? '') == $unit['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($unit['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="report_type" class="form-select">
                                <option value="">All Types</option>
                                <?php foreach ($reportTypes ?? [] as $type): ?>
                                    <option value="<?= htmlspecialchars($type) ?>" <?= ($report_type ?? '') === $type ? 'selected' : '' ?>>
                                        <?= ucfirst(htmlspecialchars($type)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="draft" <?= ($status ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="submitted" <?= ($status ?? '') === 'submitted' ? 'selected' : '' ?>>Submitted</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-1">
                            <a href="<?= AssetHelper::url('reports') ?>" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>
                
                <table id="reports-datatable" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Unit</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Submitted By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($report['title']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($report['unit_name'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge bg-info"><?= ucfirst($report['report_type']) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'submitted' => 'primary',
                                        'approved' => 'success',
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
                                    <div class="d-flex gap-2">
                                        <a href="<?= AssetHelper::url('reports/' . $report['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i data-feather="eye" class="icon-sm"></i>
                                        </a>
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

<?php
$extraCss = [
    'libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
    'libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'
];

$extraJs = [
    'libs/datatables.net/js/jquery.dataTables.min.js',
    'libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
    'libs/datatables.net-responsive/js/dataTables.responsive.min.js',
    'libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js',
    'pages/datatables.init.js'
];

$pageJs = <<<JS
<script>
    $(document).ready(function() {
        $('#reports-datatable').DataTable({
            responsive: true,
            order: [[5, 'desc']],
            pageLength: 25,
            language: {
                search: "",
                searchPlaceholder: "Search reports..."
            }
        });
    });
</script>
JS;
?>

