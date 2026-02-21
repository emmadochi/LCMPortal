<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$followUp = $followUp ?? [];
$members = $members ?? [];
$types = $types ?? [];
$csrf_token = $csrf_token ?? Security::generateCSRFToken();
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Follow-up</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('follow-ups') ?>">Follow-ups</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('follow-ups/' . ($followUp['id'] ?? '')) ?>">#<?= (int)($followUp['id'] ?? 0) ?></a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Update Follow-up</h4>
            </div>
            <div class="card-body">
                <?php if (isset($this->session) && $this->session->hasFlash('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($this->session->getFlash('errors') as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= AssetHelper::url('follow-ups/' . (int)$followUp['id']) ?>">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
                            <select class="form-select" id="member_id" name="member_id" required>
                                <option value="">Select Member...</option>
                                <?php foreach ($members as $member): ?>
                                    <option value="<?= (int)$member['id'] ?>" <?= (int)($followUp['member_id'] ?? 0) === (int)$member['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?> (<?= htmlspecialchars($member['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Follow-up Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Select Type...</option>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= htmlspecialchars($type) ?>" <?= ($followUp['type'] ?? '') === $type ? 'selected' : '' ?>>
                                        <?= ucfirst(str_replace('_', ' ', $type)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="urgent" <?= ($followUp['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                <option value="high" <?= ($followUp['priority'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                                <option value="medium" <?= ($followUp['priority'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="low" <?= ($followUp['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="assigned_to" class="form-label">Assign To</label>
                            <select class="form-select" id="assigned_to" name="assigned_to">
                                <option value="">Unassigned</option>
                                <?php foreach ($members as $staff): ?>
                                    <option value="<?= (int)$staff['id'] ?>" <?= (int)($followUp['assigned_to'] ?? 0) === (int)$staff['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?> (<?= htmlspecialchars($staff['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="due_date" name="due_date"
                                   value="<?= htmlspecialchars($followUp['due_date'] ?? date('Y-m-d')) ?>" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Notes..."><?= htmlspecialchars($followUp['notes'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= AssetHelper::url('follow-ups/' . (int)$followUp['id']) ?>" class="btn btn-secondary">
                            <i data-feather="arrow-left" class="me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Update Follow-up
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Follow-up #<?= (int)$followUp['id'] ?></h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-0">Created <?= !empty($followUp['created_at']) ? date('M j, Y', strtotime($followUp['created_at'])) : '—' ?>.</p>
                <?php if ($followUp['status'] === 'completed'): ?>
                    <span class="badge bg-success mt-2">Completed</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
