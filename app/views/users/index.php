<?php
use App\Utilities\AssetHelper;
$churchFilter = $churchFilter ?? null;
?>
<div class="db-page premium-form">
    <!-- ── Hero Header ── -->
    <div class="db-hero mb-4">
        <div class="row align-items-center position-relative" style="z-index: 1;">
            <div class="col-md-8">
                <div class="db-hero-greeting">
                    <span class="live-dot me-1"></span> DIRECTORY
                </div>
                <div class="db-hero-name">System Users</div>
                <p class="db-hero-sub">Manage all user accounts and access levels across the platform.</p>
                <?php if (!empty($churchFilter)): ?>
                <div class="mt-3">
                    <span class="db-role-pill">
                        <i class="bx bx-church"></i>
                        Viewing church: <?= htmlspecialchars($churchFilter['name']) ?>
                    </span>
                    <a href="<?= AssetHelper::url('users') ?>" class="btn btn-sm btn-outline-light rounded-pill ms-2" style="font-size: .75rem; border-color: rgba(255,255,255,.2); color: rgba(255,255,255,.8);">View All</a>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <a href="<?= AssetHelper::url('users/export?format=csv') ?>" class="btn-premium btn-success">
                        <i class="bx bx-download"></i> CSV
                    </a>
                    <a href="<?= AssetHelper::url('users/create') ?>" class="btn-premium btn-primary">
                        <i class="bx bx-user-plus"></i> New User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="db-panel">
        <div class="db-panel-header">
            <h6 class="db-panel-title">
                <span class="pi-blue"><i class="bx bx-group"></i></span>
                User Directory
            </h6>
        </div>
        <div class="db-panel-body">
            <!-- Search and Filter Form -->
            <form method="GET" action="<?= AssetHelper::url('users') ?>" class="mb-4">
                <?php if (!empty($churchFilter)): ?><input type="hidden" name="church_id" value="<?= (int)$churchFilter['id'] ?>"><?php endif; ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn-premium btn-primary w-100">Filter</button>
                        <a href="<?= AssetHelper::url('users') ?>" class="btn-premium btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
            
            <div class="table-responsive">
                <table id="users-datatable" class="db-table">
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
                                    <span class="premium-badge premium-badge-info"><?= ucfirst($user['role']) ?></span>
                                </td>
                                <td>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <span class="premium-badge premium-badge-success">Active</span>
                                    <?php elseif ($user['status'] === 'inactive'): ?>
                                        <span class="premium-badge premium-badge-secondary">Inactive</span>
                                    <?php else: ?>
                                        <span class="premium-badge premium-badge-danger">Suspended</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="<?= AssetHelper::url('users/' . $user['id']) ?>" class="btn-premium btn-premium-sm btn-info" title="View">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <a href="<?= AssetHelper::url('users/' . $user['id'] . '/edit') ?>" class="btn-premium btn-premium-sm btn-secondary" title="Edit">
                                            <i class="bx bx-edit-alt"></i>
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
            },
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });
    });
</script>
JS;
?>
