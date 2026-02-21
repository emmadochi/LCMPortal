<?php
use App\Utilities\AssetHelper;

$followUps = $followUps ?? [];
?>
<?php if (empty($followUps)): ?>
<tr>
    <td colspan="8" class="text-center py-5">
        <i data-feather="clipboard" class="icon-lg text-muted mb-3"></i>
        <h5>No follow-ups found</h5>
        <p class="text-muted">Try adjusting your filters or create a new follow-up</p>
        <a href="<?= AssetHelper::url('follow-ups/create') ?>" class="btn btn-primary">
            <i data-feather="plus" class="me-1"></i> Create Follow-up
        </a>
    </td>
</tr>
<?php else: ?>
<?php foreach ($followUps as $followUp): ?>
<tr class="<?= $followUp['status'] === 'overdue' ? 'table-danger' : '' ?>">
    <td>
        <div class="d-flex align-items-center">
            <div class="avatar-xs me-3">
                <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                    <?= strtoupper(substr($followUp['first_name'] ?? '', 0, 1) . substr($followUp['last_name'] ?? '', 0, 1)) ?>
                </span>
            </div>
            <div>
                <h5 class="font-size-14 mb-1">
                    <?= htmlspecialchars(($followUp['first_name'] ?? '') . ' ' . ($followUp['last_name'] ?? '')) ?>
                </h5>
                <p class="text-muted mb-0">
                    <?= htmlspecialchars($followUp['email'] ?? '') ?>
                </p>
            </div>
        </div>
    </td>
    <td>
        <span class="badge bg-info-subtle text-info">
            <?= ucfirst(str_replace('_', ' ', $followUp['type'] ?? '')) ?>
        </span>
    </td>
    <td>
        <?php if (!empty($followUp['assigned_to'])): ?>
        <span class="badge bg-primary-subtle text-primary">
            <?= htmlspecialchars(($followUp['assigned_first_name'] ?? '') . ' ' . ($followUp['assigned_last_name'] ?? '')) ?>
        </span>
        <?php else: ?>
        <span class="text-muted">Unassigned</span>
        <?php endif; ?>
    </td>
    <td>
        <?php
        $priorityClass = ($followUp['priority'] ?? '') === 'urgent' ? 'danger' :
            (($followUp['priority'] ?? '') === 'high' ? 'warning' :
            (($followUp['priority'] ?? '') === 'medium' ? 'primary' : 'secondary'));
        ?>
        <span class="badge bg-<?= $priorityClass ?>-subtle text-<?= $priorityClass ?>">
            <?= ucfirst($followUp['priority'] ?? '') ?>
        </span>
    </td>
    <td>
        <?php
        $statusClass = ($followUp['status'] ?? '') === 'completed' ? 'success' :
            (($followUp['status'] ?? '') === 'overdue' ? 'danger' : 'warning');
        ?>
        <span class="badge bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?>">
            <?= ucfirst($followUp['status'] ?? '') ?>
        </span>
    </td>
    <td>
        <?php if (!empty($followUp['due_date'])): ?>
        <span class="<?= strtotime($followUp['due_date']) < time() && ($followUp['status'] ?? '') !== 'completed' ? 'text-danger' : 'text-muted' ?>">
            <?= date('M j, Y', strtotime($followUp['due_date'])) ?>
        </span>
        <?php if (strtotime($followUp['due_date']) < time() && ($followUp['status'] ?? '') !== 'completed'): ?>
        <br><small class="text-danger">Overdue</small>
        <?php endif; ?>
        <?php else: ?>
        <span class="text-muted">—</span>
        <?php endif; ?>
    </td>
    <td>
        <?= !empty($followUp['created_at']) ? date('M j, Y', strtotime($followUp['created_at'])) : '—' ?>
    </td>
    <td>
        <div class="btn-group">
            <a href="<?= AssetHelper::url('follow-ups/' . (int)($followUp['id'] ?? 0)) ?>"
               class="btn btn-sm btn-primary" title="View Details">
                <i data-feather="eye" class="me-1"></i>
            </a>
            <?php if (($followUp['status'] ?? '') !== 'completed'): ?>
            <button class="btn btn-sm btn-success"
                    onclick="completeFollowUp(<?= (int)($followUp['id'] ?? 0) ?>)"
                    title="Mark Complete">
                <i data-feather="check" class="me-1"></i>
            </button>
            <?php endif; ?>
            <a href="<?= AssetHelper::url('follow-ups/' . (int)($followUp['id'] ?? 0) . '/edit') ?>"
               class="btn btn-sm btn-outline-secondary" title="Edit">
                <i data-feather="edit" class="me-1"></i>
            </a>
            <button class="btn btn-sm btn-outline-danger"
                    onclick="deleteFollowUp(<?= (int)($followUp['id'] ?? 0) ?>)"
                    title="Delete">
                <i data-feather="trash" class="me-1"></i>
            </button>
        </div>
    </td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
