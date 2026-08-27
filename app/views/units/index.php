<?php
use App\Core\Session;
use App\Utilities\AssetHelper;

$session = Session::getInstance();
$userRole = $session->get('user_role');
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Units</h4>
                    <p class="card-title-desc mb-0">
                        <?php if ($showMyUnitsFilter ?? false): ?>
                            <?= $isMyUnitsView ?? false ? 'Your assigned units' : 'All church units' ?>
                        <?php else: ?>
                            Manage all church units and departments
                        <?php endif; ?>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <?php if ($showMyUnitsFilter ?? false): ?>
                        <?php if ($isMyUnitsView ?? false): ?>
                            <a href="<?= AssetHelper::url('units') ?>" class="btn btn-outline-primary">
                                <i data-feather="list" class="me-1"></i> View All Units
                            </a>
                        <?php else: ?>
                            <a href="<?= AssetHelper::url('units?my_units=1') ?>" class="btn btn-primary">
                                <i data-feather="check-square" class="me-1"></i> Show My Units Only
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <a href="<?= AssetHelper::url('units/export?format=csv') ?>" class="btn btn-sm btn-success">
                        <i data-feather="download" class="me-1"></i> Export CSV
                    </a>
                    <a href="<?= AssetHelper::url('units/export?format=json') ?>" class="btn btn-sm btn-info">
                        <i data-feather="download" class="me-1"></i> Export JSON
                    </a>
                    <a href="<?= AssetHelper::url('units/export?format=pdf') ?>" class="btn btn-sm btn-danger">
                        <i data-feather="file-text" class="me-1"></i> Export PDF
                    </a>
                    <?php if ($session->hasPermission('manage_units')): ?>
                        <a href="<?= AssetHelper::url('units/create') ?>" class="btn btn-primary">
                            <i data-feather="plus-circle" class="me-1"></i> Create Unit
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filter Form -->
                <form method="GET" action="<?= AssetHelper::url('units') ?>" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search units..." value="<?= htmlspecialchars($search ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="<?= AssetHelper::url('units') ?>" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>
                
                <table id="units-datatable" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($units as $unit): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($unit['name']) ?></strong>
                                </td>
                                <td>
                                    <?= htmlspecialchars($unit['description'] ?: 'No description') ?>
                                </td>
                                <td>
                                    <?php if ($unit['status'] === 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($unit['created_at'])) ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="<?= AssetHelper::url('units/' . $unit['id']) ?>" class="btn btn-sm btn-outline-primary" title="View & Manage Unit Leadership">
                                            <i data-feather="eye" class="icon-sm"></i> Manage
                                        </a>
                                        <?php if ($userRole === 'admin'): ?>
                                            <a href="<?= AssetHelper::url('units/' . $unit['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">
                                                <i data-feather="edit" class="icon-sm"></i>
                                            </a>
                                        <?php endif; ?>
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
        $('#units-datatable').DataTable({
            responsive: true,
            order: [[3, 'desc']],
            pageLength: 25,
            language: {
                search: "",
                searchPlaceholder: "Search units..."
            }
        });
    });
</script>
JS;
?>
