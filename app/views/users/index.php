<?php
use App\Utilities\AssetHelper;
$churchFilter = $churchFilter ?? null;
?>
<?php if (!empty($churchFilter)): ?>
<div class="alert alert-info d-flex align-items-center justify-content-between mb-3" role="alert">
    <span><i class="bx bx-church me-2"></i>Viewing church: <strong><?= htmlspecialchars($churchFilter['name']) ?></strong> (membership)</span>
    <a href="<?= AssetHelper::url('users') ?>" class="btn btn-sm btn-outline-primary">View all</a>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Users</h4>
                    <p class="card-title-desc mb-0">Manage all users in the system</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= AssetHelper::url('users/export?format=csv') ?>" class="btn btn-sm btn-success">
                        <i data-feather="download" class="me-1"></i> Export CSV
                    </a>
                    <a href="<?= AssetHelper::url('users/export?format=json') ?>" class="btn btn-sm btn-info">
                        <i data-feather="download" class="me-1"></i> Export JSON
                    </a>
                    <a href="<?= AssetHelper::url('users/export?format=pdf') ?>" class="btn btn-sm btn-danger">
                        <i data-feather="file-text" class="me-1"></i> Export PDF
                    </a>
                    <a href="<?= AssetHelper::url('users/create') ?>" class="btn btn-primary">
                        <i data-feather="user-plus" class="me-1"></i> Create User
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filter Form -->
                <form method="GET" action="<?= AssetHelper::url('users') ?>" class="mb-4">
                    <?php if (!empty($churchFilter)): ?><input type="hidden" name="church_id" value="<?= (int)$churchFilter['id'] ?>"><?php endif; ?>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search users..." value="<?= htmlspecialchars($search ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <select name="role" class="form-select">
                                <option value="">All Roles</option>
                                <?php foreach ($roles ?? [] as $r): ?>
                                    <option value="<?= htmlspecialchars($r) ?>" <?= ($role ?? '') === $r ? 'selected' : '' ?>>
                                        <?= ucfirst(htmlspecialchars($r)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="suspended" <?= ($status ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="<?= AssetHelper::url('users') ?>" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>
                
                <table id="users-datatable" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge bg-info"><?= ucfirst($user['role']) ?></span>
                                </td>
                                <td>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php elseif ($user['status'] === 'inactive'): ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Suspended</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="<?= AssetHelper::url('users/' . $user['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i data-feather="eye" class="icon-sm"></i>
                                        </a>
                                        <a href="<?= AssetHelper::url('users/' . $user['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">
                                            <i data-feather="edit" class="icon-sm"></i>
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
        $('#users-datatable').DataTable({
            responsive: true,
            order: [[4, 'desc']],
            pageLength: 25,
            language: {
                search: "",
                searchPlaceholder: "Search users..."
            }
        });
    });
</script>
JS;
?>
