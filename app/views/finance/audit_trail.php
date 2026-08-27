<?php
use App\Utilities\AssetHelper;
?>

<style>
:root {
    --fin-emerald: #10b981;
    --fin-rose: #f43f5e;
    --fin-indigo: #4f46e5;
    --fin-amber: #f59e0b;
    --fin-dark: #0f172a;
    --fin-surface: #ffffff;
    --fin-border: #e2e8f0;
    --fin-sub: #64748b;
    --fin-radius: 16px;
}

.fin-dashboard {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: var(--fin-dark);
}

.fin-header-card {
    background: #ffffff;
    border-radius: var(--fin-radius);
    padding: 22px 28px;
    border: 1px solid var(--fin-border);
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    margin-bottom: 24px;
}

.fin-panel {
    background: #ffffff;
    border-radius: var(--fin-radius);
    border: 1px solid var(--fin-border);
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    overflow: hidden;
    margin-bottom: 24px;
}
.fin-panel-header {
    padding: 18px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
}
.fin-panel-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--fin-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.fin-table {
    width: 100%;
    border-collapse: collapse;
}
.fin-table thead th {
    background: #f8fafc;
    color: #64748b;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 12px 20px;
    border-bottom: 1px solid var(--fin-border);
}
.fin-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}
.fin-table tbody tr:hover {
    background: #f8fafc;
}
.fin-table td {
    padding: 14px 20px;
    font-size: 0.88rem;
    color: var(--fin-dark);
    vertical-align: middle;
}
</style>

<div class="container-fluid p-0 fin-dashboard">
    <div class="fin-header-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('finance') ?>" class="text-decoration-none text-muted">Finances</a></li>
                        <li class="breadcrumb-item active text-secondary fw-semibold">Audit Trail</li>
                    </ol>
                </nav>
                <h3 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-shield-quarter text-secondary"></i> Financial Audit Trail & Activity Log
                </h3>
            </div>
            
            <?php if ($this->session->hasPermission('manage_users') && !empty($churches)): ?>
            <form method="GET" class="d-flex gap-2 align-items-center">
                <select name="church_id" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                    <option value="">All Churches (Global)</option>
                    <?php foreach ($churches as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($churchId == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="fin-panel">
        <div class="fin-panel-header">
            <h5 class="fin-panel-title">
                <i class="bx bx-history text-primary fs-5"></i> Financial Modifications & Events (<?= count($logs) ?>)
            </h5>
            <span class="badge bg-soft-info text-info rounded-pill px-3 py-2">Immutable Security Trail</span>
        </div>
        <div class="fin-panel-body p-0">
            <?php if (empty($logs)): ?>
                <div class="text-center py-5">
                    <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                        <i class="bx bx-shield-x text-muted font-size-24"></i>
                    </div>
                    <h5 class="text-dark fw-bold">No audit events recorded yet</h5>
                    <p class="text-muted small">Financial events (transactions created, budgets set, pledge payments redeemed) will appear here in real-time.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="fin-table mb-0">
                        <thead>
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
                                            <div class="avatar-xs bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem; background: #eef2ff;">
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
                                        <span class="badge rounded-pill bg-soft-<?= $actionBadge ?> text-<?= $actionBadge ?> px-3 py-1 fw-bold">
                                            <?= ucwords(str_replace('_', ' ', $log['action'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= htmlspecialchars(ucfirst($log['entity_type'] ?? 'Finance')) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-medium small"><?= htmlspecialchars($log['description'] ?? '') ?></div>
                                    </td>
                                    <td class="text-end pe-4 text-muted small font-monospace">
                                        <?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?>
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
