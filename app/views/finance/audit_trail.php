<?php
use App\Utilities\AssetHelper;
?>

<div class="container-fluid p-0">
    <div class="bg-white border-bottom px-4 py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('finance') ?>">Finances</a></li>
                        <li class="breadcrumb-item active">Audit Trail</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-shield-quarter text-primary me-1"></i> Financial Audit Trail & Activity Log</h4>
            </div>
            
            <?php if ($this->session->hasPermission('manage_users') && !empty($churches)): ?>
            <form method="GET" class="d-flex gap-2 align-items-center">
                <select name="church_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Churches (Global)</option>
                    <?php foreach ($churches as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($churchId == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="p-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-history me-1 text-primary"></i> Financial Modifications & Events (<?= count($logs) ?>)</h5>
                <span class="badge bg-soft-info text-info">Immutable Security Trail</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($logs)): ?>
                    <div class="text-center py-5">
                        <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                            <i class="bx bx-shield-x text-muted font-size-24"></i>
                        </div>
                        <h6 class="text-muted">No audit events recorded yet</h6>
                        <p class="text-muted small">Financial events (transactions created, budgets set, pledge payments redeemed) will appear here in real-time.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Timestamp</th>
                                    <th>User / Operator</th>
                                    <th>Action</th>
                                    <th>Entity</th>
                                    <th>Description & Details</th>
                                    <th class="text-end pe-4">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                    <?php 
                                        $actionBadge = 'secondary';
                                        if (strpos($log['action'], 'created') !== false || strpos($log['action'], 'store') !== false) {
                                            $actionBadge = 'success';
                                        } elseif (strpos($log['action'], 'updated') !== false || strpos($log['action'], 'edit') !== false) {
                                            $actionBadge = 'warning';
                                        } elseif (strpos($log['action'], 'deleted') !== false) {
                                            $actionBadge = 'danger';
                                        } elseif (strpos($log['action'], 'payment') !== false) {
                                            $actionBadge = 'info';
                                        }
                                    ?>
                                    <tr>
                                        <td class="ps-4 text-nowrap">
                                            <div class="small fw-semibold text-dark"><?= date('M d, Y', strtotime($log['created_at'])) ?></div>
                                            <div class="small text-muted"><?= date('h:i:s A', strtotime($log['created_at'])) ?></div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold">
                                                    <?= strtoupper(substr($log['first_name'] ?? 'U', 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark small">
                                                        <?= htmlspecialchars(trim(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?: 'System / User #' . ($log['user_id'] ?? '')) ?>
                                                    </div>
                                                    <span class="badge bg-light text-muted border" style="font-size: 10px;">
                                                        <?= ucfirst($log['role'] ?? 'User') ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-soft-<?= $actionBadge ?> text-<?= $actionBadge ?> px-2 py-1">
                                                <?= ucwords(str_replace('_', ' ', $log['action'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?= htmlspecialchars($log['model_type'] ?? 'Finance') ?> #<?= $log['model_id'] ?? '' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-dark small"><?= htmlspecialchars($log['description']) ?></div>
                                        </td>
                                        <td class="text-end pe-4 small text-muted text-nowrap">
                                            <code><?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?></code>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
