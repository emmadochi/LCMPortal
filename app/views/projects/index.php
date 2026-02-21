<?php
use App\Utilities\AssetHelper;
$churchFilter = $churchFilter ?? null;
?>
<?php if (!empty($churchFilter)): ?>
<div class="alert alert-info d-flex align-items-center justify-content-between mb-3" role="alert">
    <span><i class="bx bx-church me-2"></i>Viewing church: <strong><?= htmlspecialchars($churchFilter['name']) ?></strong></span>
    <a href="<?= AssetHelper::url('projects') ?>" class="btn btn-sm btn-outline-primary">View all</a>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Projects & Events</h4>
                    <p class="card-title-desc mb-0">Manage church projects and events</p>
                </div>
                <div class="d-flex gap-2">
                    <?php
                    $exportBaseUrl = AssetHelper::url('projects/export');
                    $exportQuery = $_GET ?? [];
                    unset($exportQuery['page']);
                    $queryString = http_build_query($exportQuery);
                    $queryPrefix = $queryString ? ($queryString . '&') : '';
                    ?>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="download" class="me-1"></i> Export
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="<?= $exportBaseUrl . '?' . $queryPrefix . 'format=csv' ?>">
                                CSV
                            </a>
                            <a class="dropdown-item" href="<?= $exportBaseUrl . '?' . $queryPrefix . 'format=excel' ?>">
                                Excel (.xls)
                            </a>
                            <a class="dropdown-item" href="<?= $exportBaseUrl . '?' . $queryPrefix . 'format=json' ?>">
                                JSON
                            </a>
                            <a class="dropdown-item" href="<?= $exportBaseUrl . '?' . $queryPrefix . 'format=pdf' ?>">
                                PDF
                            </a>
                        </div>
                    </div>
                    <a href="<?= AssetHelper::url('projects/create') ?>" class="btn btn-primary">
                        <i data-feather="plus-circle" class="me-1"></i> Create Project
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="projects-datatable" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Budget</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($project['title']) ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'planning' => 'secondary',
                                        'in_progress' => 'primary',
                                        'on_hold' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$project['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $statusColor ?>"><?= ucfirst(str_replace('_', ' ', $project['status'])) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $priorityColors = [
                                        'low' => 'secondary',
                                        'medium' => 'info',
                                        'high' => 'warning',
                                        'urgent' => 'danger'
                                    ];
                                    $priorityColor = $priorityColors[$project['priority']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $priorityColor ?>"><?= ucfirst($project['priority']) ?></span>
                                </td>
                                <td><?= date('M d, Y', strtotime($project['start_date'])) ?></td>
                                <td><?= $project['end_date'] ? date('M d, Y', strtotime($project['end_date'])) : 'N/A' ?></td>
                                <td><?= $project['budget'] ? '$' . number_format($project['budget'], 2) : 'N/A' ?></td>
                                <td><?= htmlspecialchars(($project['first_name'] ?? '') . ' ' . ($project['last_name'] ?? '')) ?></td>
                                <td>
                                    <a href="<?= AssetHelper::url('projects/' . $project['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i data-feather="eye" class="icon-sm"></i>
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
        $('#projects-datatable').DataTable({
            responsive: true,
            order: [[3, 'desc']],
            pageLength: 25,
            language: {
                search: "",
                searchPlaceholder: "Search projects..."
            }
        });
    });
</script>
JS;
?>

