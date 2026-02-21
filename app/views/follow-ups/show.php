<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$followUp = $followUp ?? null;
if (!$followUp) {
    return;
}
$memberName = htmlspecialchars(($followUp['first_name'] ?? '') . ' ' . ($followUp['last_name'] ?? ''));
$assignedName = !empty($followUp['assigned_to']) && !empty($followUp['assigned_first_name'])
    ? htmlspecialchars($followUp['assigned_first_name'] . ' ' . $followUp['assigned_last_name'])
    : null;
$priorityClass = $followUp['priority'] === 'urgent' ? 'danger' : ($followUp['priority'] === 'high' ? 'warning' : ($followUp['priority'] === 'medium' ? 'primary' : 'secondary'));
$statusClass = $followUp['status'] === 'completed' ? 'success' : ($followUp['status'] === 'overdue' ? 'danger' : 'warning');
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Follow-up Details</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('follow-ups') ?>">Follow-ups</a></li>
                    <li class="breadcrumb-item active">#<?= (int)$followUp['id'] ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0"><?= ucfirst(str_replace('_', ' ', $followUp['type'])) ?> — <?= $memberName ?></h4>
                    <div class="d-flex gap-1">
                        <?php if ($followUp['status'] !== 'completed'): ?>
                            <a href="<?= AssetHelper::url('follow-ups/' . $followUp['id'] . '/edit') ?>" class="btn btn-outline-primary btn-sm">
                                <i data-feather="edit" class="me-1"></i> Edit
                            </a>
                            <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('completeForm').submit();">
                                <i data-feather="check" class="me-1"></i> Mark Complete
                            </button>
                        <?php endif; ?>
                        <form id="deleteForm" method="POST" action="<?= AssetHelper::url('follow-ups/' . $followUp['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this follow-up? This cannot be undone.');">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars(Security::generateCSRFToken()) ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i data-feather="trash" class="me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th class="text-muted" style="width: 180px;">Member</th>
                                <td>
                                    <strong><?= $memberName ?></strong>
                                    <br><span class="text-muted"><?= htmlspecialchars($followUp['email'] ?? '') ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Type</th>
                                <td><span class="badge bg-info-subtle text-info"><?= ucfirst(str_replace('_', ' ', $followUp['type'])) ?></span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Status</th>
                                <td><span class="badge bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?>"><?= ucfirst($followUp['status']) ?></span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Priority</th>
                                <td><span class="badge bg-<?= $priorityClass ?>-subtle text-<?= $priorityClass ?>"><?= ucfirst($followUp['priority']) ?></span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Due Date</th>
                                <td><?= $followUp['due_date'] ? date('l, F j, Y', strtotime($followUp['due_date'])) : '—' ?></td>
                            </tr>
                            <?php if ($assignedName): ?>
                            <tr>
                                <th class="text-muted">Assigned To</th>
                                <td><?= $assignedName ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th class="text-muted">Created</th>
                                <td><?= date('M j, Y \a\t g:i A', strtotime($followUp['created_at'])) ?></td>
                            </tr>
                            <?php if (!empty($followUp['completed_date'])): ?>
                            <tr>
                                <th class="text-muted">Completed</th>
                                <td><?= date('M j, Y \a\t g:i A', strtotime($followUp['completed_date'])) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty(trim($followUp['notes'] ?? ''))): ?>
                            <tr>
                                <th class="text-muted align-top">Notes</th>
                                <td><div class="text-break"><?= nl2br(htmlspecialchars($followUp['notes'])) ?></div></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="<?= AssetHelper::url('follow-ups') ?>" class="btn btn-outline-secondary w-100 mb-2">
                    <i data-feather="list" class="me-1"></i> Back to Follow-ups
                </a>
                <a href="<?= AssetHelper::url('follow-ups/create?member_id=' . (int)$followUp['member_id']) ?>" class="btn btn-outline-primary w-100">
                    <i data-feather="plus" class="me-1"></i> New Follow-up for Same Member
                </a>
            </div>
        </div>
    </div>
</div>

<?php if ($followUp['status'] !== 'completed'): ?>
<form id="completeForm" method="POST" action="<?= AssetHelper::url('follow-ups/' . $followUp['id'] . '/complete') ?>" style="display: none;">
    <input type="hidden" name="_token" value="<?= htmlspecialchars(Security::generateCSRFToken()) ?>">
    <input type="hidden" name="completion_notes" value="">
</form>
<?php endif; ?>
