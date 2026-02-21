<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Activity Logs</h4>
                <div>
                    <a href="<?= AssetHelper::url('activity-logs/export?format=csv') ?>" class="btn btn-sm btn-success">
                        <i data-feather="download" class="icon-sm me-1"></i> Export CSV
                    </a>
                    <a href="<?= AssetHelper::url('activity-logs/export?format=json') ?>" class="btn btn-sm btn-info">
                        <i data-feather="download" class="icon-sm me-1"></i> Export JSON
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="<?= AssetHelper::url('activity-logs') ?>" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($search ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <select name="action" class="form-select">
                                <option value="">All Actions</option>
                                <?php foreach ($actions ?? [] as $act): ?>
                                    <option value="<?= htmlspecialchars($act) ?>" <?= ($action ?? '') === $act ? 'selected' : '' ?>>
                                        <?= ucfirst(htmlspecialchars($act)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="model_type" class="form-select">
                                <option value="">All Models</option>
                                <?php foreach ($modelTypes ?? [] as $type): ?>
                                    <option value="<?= htmlspecialchars($type) ?>" <?= ($modelType ?? '') === $type ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="<?= AssetHelper::url('activity-logs') ?>" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>

                <!-- Activity Logs Table -->
                <?php
                $pagination = $pagination ?? null;
                $total = $pagination['total'] ?? count($logs ?? []);
                $perPage = $pagination['per_page'] ?? 25;
                $currentPage = $pagination['current_page'] ?? 1;
                $from = $total === 0 ? 0 : (($currentPage - 1) * $perPage) + 1;
                $to = min($currentPage * $perPage, $total);
                ?>
                <?php if ($pagination && $total > 0): ?>
                <p class="text-muted small mb-2">Showing <?= $from ?> to <?= $to ?> of <?= $total ?> logs</p>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Model</th>
                                <th>Description</th>
                                <th>IP Address</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No activity logs found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?= $log['id'] ?></td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?></strong>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($log['email'] ?? '') ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= getActionBadgeColor($log['action']) ?>">
                                                <?= ucfirst(htmlspecialchars($log['action'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($log['model_type']): ?>
                                                <span class="badge bg-info-subtle text-info">
                                                    <?= htmlspecialchars($log['model_type']) ?>
                                                    <?php if ($log['model_id']): ?>
                                                        #<?= $log['model_id'] ?>
                                                    <?php endif; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($log['description'] ?? '') ?></td>
                                        <td>
                                            <small class="text-muted"><?= htmlspecialchars($log['ip_address'] ?? '') ?></small>
                                        </td>
                                        <td>
                                            <small><?= date('M d, Y H:i', strtotime($log['created_at'])) ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                <div class="d-flex justify-content-center mt-3">
                    <?php require __DIR__ . '/../components/pagination.php'; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Helper function for badge colors
function getActionBadgeColor($action) {
    return match($action) {
        'create' => 'success',
        'update' => 'primary',
        'delete' => 'danger',
        'login' => 'info',
        'logout' => 'secondary',
        'assign' => 'warning',
        'remove' => 'dark',
        default => 'secondary'
    };
}
?>

