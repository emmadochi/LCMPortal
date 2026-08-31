<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Follow-up Management</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Follow-ups</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php
$myConverts = $myConverts ?? [];
$careStats = $careStats ?? [];
$isAdminOrPastor = $isAdminOrPastor ?? false;
?>

<!-- Souls Won & Discipleship Care Pipeline -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border: 1px solid rgba(79, 70, 229, 0.15) !important;">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-heart text-danger me-2 font-size-20"></i> Souls Won & Assigned for Discipleship
                    </h5>
                    <p class="text-muted font-size-13 mb-0">Follow up with the souls you won or were assigned to you by your Pastor.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= AssetHelper::url('evangelism/converts-export') ?>" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">
                        <i class="bx bx-download me-1"></i> Export CSV
                    </a>
                    <a href="<?= AssetHelper::url('evangelism/create') ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                        <i class="bx bx-user-plus me-1"></i> Log / Record Follow-Up
                    </a>
                    <a href="<?= AssetHelper::url('evangelism') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold">
                        <i class="bx bx-book-open me-1"></i> Full Journal
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Soul Care Metrics -->
                <div class="row g-3 mb-4">
                    <div class="col-md-2 col-6">
                        <div class="p-3 rounded-3 bg-light text-center border">
                            <h4 class="fw-bold text-primary mb-1"><?= (int)($careStats['total_converts'] ?? count($myConverts)) ?></h4>
                            <small class="text-muted font-size-12">Total in Care</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="p-3 rounded-3 bg-light text-center border">
                            <h4 class="fw-bold text-info mb-1"><?= (int)($careStats['contacted_count'] ?? 0) ?></h4>
                            <small class="text-muted font-size-12">1st Contact Done</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="p-3 rounded-3 bg-light text-center border">
                            <h4 class="fw-bold text-success mb-1"><?= (int)($careStats['attended_church_count'] ?? 0) ?></h4>
                            <small class="text-muted font-size-12">Attended Service</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="p-3 rounded-3 bg-light text-center border">
                            <h4 class="fw-bold text-warning mb-1"><?= (int)($careStats['holy_ghost_baptized_count'] ?? 0) ?></h4>
                            <small class="text-muted font-size-12">Holy Ghost Baptized</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="p-3 rounded-3 bg-light text-center border">
                            <h4 class="fw-bold text-primary mb-1"><?= (int)($careStats['water_baptized_count'] ?? 0) ?></h4>
                            <small class="text-muted font-size-12">Water Baptized</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="p-3 rounded-3 bg-light text-center border">
                            <h4 class="fw-bold text-dark mb-1"><?= (int)($careStats['foundation_enrolled_count'] ?? 0) ?></h4>
                            <small class="text-muted font-size-12">Foundation Class</small>
                        </div>
                    </div>
                </div>

                <!-- Converts Pipeline List -->
                <?php if (empty($myConverts)): ?>
                    <div class="text-center py-4 bg-light rounded-4">
                        <i class="bx bx-user-x font-size-36 text-muted mb-2"></i>
                        <h6 class="fw-bold text-dark">No souls assigned to you yet</h6>
                        <p class="text-muted font-size-13 mb-3">When you win souls or your pastor assigns new converts to you, they will appear here for follow-up care.</p>
                        <a href="<?= AssetHelper::url('evangelism/create') ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                            <i class="bx bx-plus me-1"></i> Record a Soul Won
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Soul / Convert</th>
                                    <th>Care Role</th>
                                    <th>Decision</th>
                                    <th>Contact & Actions</th>
                                    <th>Spiritual Journey</th>
                                    <th>Next Follow-up</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myConverts as $c): ?>
                                    <?php
                                    $mCompleted = 0;
                                    if (!empty($c['first_contact_done'])) $mCompleted++;
                                    if (!empty($c['attended_service'])) $mCompleted++;
                                    if (!empty($c['baptized_holy_ghost'])) $mCompleted++;
                                    if (!empty($c['baptized_water'])) $mCompleted++;
                                    if (!empty($c['foundation_class_enrolled'])) $mCompleted++;
                                    if (!empty($c['department_joined'])) $mCompleted++;
                                    $pct = round(($mCompleted / 6) * 100);
                                    
                                    $isWonByMe = (int)($c['soul_winner_id'] ?? 0) === (int)$this->session->get('user_id');
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center me-2 font-size-12 fw-bold">
                                                    <?= strtoupper(substr($c['full_name'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <a href="<?= AssetHelper::url('evangelism/converts/' . $c['id']) ?>" class="fw-bold text-dark text-decoration-none">
                                                        <?= htmlspecialchars($c['full_name']) ?>
                                                    </a>
                                                    <div class="font-size-11 text-muted">
                                                        Added <?= date('M d, Y', strtotime($c['created_at'])) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($isWonByMe): ?>
                                                <span class="badge bg-soft-success text-success rounded-pill font-size-11 px-2 py-1">
                                                    <i class="bx bx-trophy me-1"></i> Won by You
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-soft-warning text-dark rounded-pill font-size-11 px-2 py-1">
                                                    <i class="bx bx-user-check me-1"></i> Assigned by Pastor
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-size-11">
                                                <?= ucfirst($c['decision_type'] ?? 'Salvation') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($c['phone'])): ?>
                                                <div class="d-flex gap-1">
                                                    <button type="button" onclick="openWhatsAppTemplateModal('<?= htmlspecialchars(addslashes($c['full_name'])) ?>', '<?= htmlspecialchars(addslashes($c['phone'])) ?>')" class="btn btn-sm btn-success py-0 px-2 rounded-pill font-size-11" title="1-Click WhatsApp Follow-up">
                                                        <i class="bx bxl-whatsapp"></i> Chat
                                                    </button>
                                                    <a href="tel:<?= htmlspecialchars($c['phone']) ?>" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill font-size-11" title="Call">
                                                        <i class="bx bx-phone"></i> Call
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted font-size-11">No phone</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="min-width: 140px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 6px; border-radius: 4px;">
                                                    <div class="progress-bar bg-success" style="width: <?= $pct ?>%"></div>
                                                </div>
                                                <span class="font-size-11 fw-bold text-muted"><?= $pct ?>%</span>
                                            </div>
                                            <small class="font-size-10 text-muted"><?= $mCompleted ?>/6 milestones</small>
                                        </td>
                                        <td>
                                            <?php if (!empty($c['next_followup_date'])): ?>
                                                <span class="font-size-12 <?= ($c['next_followup_date'] <= date('Y-m-d')) ? 'text-danger fw-bold' : 'text-dark' ?>">
                                                    <?= date('M d, Y', strtotime($c['next_followup_date'])) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted font-size-12">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= AssetHelper::url('evangelism/converts/' . $c['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-size-12 fw-semibold">
                                                <i class="bx bx-clipboard me-1"></i> Care Profile
                                            </a>
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="card-title mb-0"><?= $isAdminOrPastor ? 'General Church Member Follow-ups' : 'Assigned Pastoral Care Tasks' ?></h4>
                        <p class="card-title-desc mb-0">Manage general member care and visitation tasks</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <?php if ($isAdminOrPastor): ?>
                        <a href="<?= AssetHelper::url('follow-ups/statistics') ?>" class="btn btn-outline-secondary me-2">
                            <i data-feather="bar-chart-2" class="me-1"></i> Statistics
                        </a>
                        <a href="<?= AssetHelper::url('follow-ups/create') ?>" class="btn btn-primary">
                            <i data-feather="plus-circle" class="me-1"></i> Create Follow-up
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Statistics Overview -->
                <div class="row mb-4" id="followUpsStatsRow">
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <h3 class="text-primary" id="stat-pending"><?= count(array_filter($followUps, function($f) { return $f['status'] === 'pending'; })) ?></h3>
                                <p class="mb-0">Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <h3 class="text-warning" id="stat-overdue"><?= count(array_filter($followUps, function($f) { return $f['status'] === 'overdue'; })) ?></h3>
                                <p class="mb-0">Overdue</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <h3 class="text-success" id="stat-completed"><?= count(array_filter($followUps, function($f) { return $f['status'] === 'completed'; })) ?></h3>
                                <p class="mb-0">Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <h3 class="text-info" id="stat-total"><?= count($followUps) ?></h3>
                                <p class="mb-0">Total</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-12">
                        <form method="GET" class="row g-3" id="followUpsFilterForm">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?= htmlspecialchars($filters['search'] ?? '') ?>" 
                                       placeholder="Member name...">
                            </div>
                            
                            <div class="col-md-2">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="">All Types</option>
                                    <?php foreach ($types as $type): ?>
                                        <option value="<?= $type ?>" <?= ($filters['type'] == $type) ? 'selected' : '' ?>>
                                            <?= ucfirst(str_replace('_', ' ', $type)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="">All Priorities</option>
                                    <?php foreach ($priorities as $priority): ?>
                                        <option value="<?= $priority ?>" <?= ($filters['priority'] == $priority) ? 'selected' : '' ?>>
                                            <?= ucfirst($priority) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <?php foreach ($statuses as $status): ?>
                                        <option value="<?= $status ?>" <?= ($filters['status'] == $status) ? 'selected' : '' ?>>
                                            <?= ucfirst($status) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="assigned_to" class="form-label">Assigned To</label>
                                <select class="form-select" id="assigned_to" name="assigned_to">
                                    <option value="">All Staff</option>
                                    <option value="unassigned" <?= ($filters['assigned_to'] == 'unassigned') ? 'selected' : '' ?>>Unassigned</option>
                                    <?php foreach ($members as $staff): ?>
                                        <option value="<?= $staff['id'] ?>" <?= ($filters['assigned_to'] == $staff['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-3 mt-4">
                                <button type="submit" class="btn btn-primary" id="applyFiltersBtn">
                                    <i data-feather="filter" class="me-1"></i> <span class="btn-text">Apply Filters</span>
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" id="resetFiltersBtn">
                                    <i data-feather="refresh-ccw" class="me-1"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Follow-ups Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="follow-ups-table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Type</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="followUpsTableBody">
                            <?php if (empty($followUps)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i data-feather="clipboard" class="icon-lg text-muted mb-3"></i>
                                        <h5>No follow-ups found</h5>
                                        <p class="text-muted">Create your first follow-up to get started</p>
                                        <a href="<?= AssetHelper::url('follow-ups/create') ?>" class="btn btn-primary">
                                            <i data-feather="plus" class="me-1"></i> Create First Follow-up
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
                                                        <?= strtoupper(substr($followUp['first_name'], 0, 1) . substr($followUp['last_name'], 0, 1)) ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h5 class="font-size-14 mb-1">
                                                        <?= htmlspecialchars($followUp['first_name'] . ' ' . $followUp['last_name']) ?>
                                                    </h5>
                                                    <p class="text-muted mb-0">
                                                        <?= htmlspecialchars($followUp['email']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="badge bg-info-subtle text-info">
                                                <?= ucfirst(str_replace('_', ' ', $followUp['type'])) ?>
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <?php if ($followUp['assigned_to']): ?>
                                                <span class="badge bg-primary-subtle text-primary">
                                                    <?= htmlspecialchars($followUp['assigned_first_name'] . ' ' . $followUp['assigned_last_name']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Unassigned</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td>
                                            <?php 
                                            $priorityClass = $followUp['priority'] === 'urgent' ? 'danger' : 
                                                          ($followUp['priority'] === 'high' ? 'warning' : 
                                                          ($followUp['priority'] === 'medium' ? 'primary' : 'secondary'));
                                            ?>
                                            <span class="badge bg-<?= $priorityClass ?>-subtle text-<?= $priorityClass ?>">
                                                <?= ucfirst($followUp['priority']) ?>
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <?php 
                                            $statusClass = $followUp['status'] === 'completed' ? 'success' : 
                                                         ($followUp['status'] === 'overdue' ? 'danger' : 'warning');
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?>">
                                                <?= ucfirst($followUp['status']) ?>
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <span class="<?= strtotime($followUp['due_date']) < time() && $followUp['status'] !== 'completed' ? 'text-danger' : 'text-muted' ?>">
                                                <?= date('M j, Y', strtotime($followUp['due_date'])) ?>
                                            </span>
                                            <?php if (strtotime($followUp['due_date']) < time() && $followUp['status'] !== 'completed'): ?>
                                                <br><small class="text-danger">Overdue</small>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td>
                                            <?= date('M j, Y', strtotime($followUp['created_at'])) ?>
                                        </td>
                                        
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= AssetHelper::url('follow-ups/' . $followUp['id']) ?>" 
                                                   class="btn btn-sm btn-primary" title="View Details">
                                                    <i data-feather="eye" class="me-1"></i>
                                                </a>
                                                
                                                <?php if ($followUp['status'] !== 'completed'): ?>
                                                    <button class="btn btn-sm btn-success" 
                                                            onclick="completeFollowUp(<?= $followUp['id'] ?>)" 
                                                            title="Mark Complete">
                                                        <i data-feather="check" class="me-1"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <a href="<?= AssetHelper::url('follow-ups/' . $followUp['id'] . '/edit') ?>" 
                                                   class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    <i data-feather="edit" class="me-1"></i>
                                                </a>
                                                
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteFollowUp(<?= $followUp['id'] ?>)" 
                                                        title="Delete">
                                                    <i data-feather="trash" class="me-1"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Complete Follow-up Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Follow-up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark this follow-up as completed?</p>
                <div class="mb-3">
                    <label for="completionNotes" class="form-label">Completion Notes (Optional)</label>
                    <textarea class="form-control" id="completionNotes" rows="3" 
                              placeholder="Add any notes about this follow-up..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmComplete">Complete</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Follow-up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this follow-up?</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<form id="actionForm" method="POST" style="display: none;">
    <input type="hidden" name="_token" value="<?= htmlspecialchars(Security::generateCSRFToken()) ?>">
    <input type="hidden" name="completion_notes" id="completionNotesInput">
</form>

<script>
let currentFollowUpId = null;

function completeFollowUp(followUpId) {
    currentFollowUpId = followUpId;
    document.getElementById('completionNotes').value = '';
    const modal = new bootstrap.Modal(document.getElementById('completeModal'));
    modal.show();
}

function deleteFollowUp(followUpId) {
    currentFollowUpId = followUpId;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmComplete').addEventListener('click', function() {
    if (currentFollowUpId) {
        const form = document.getElementById('actionForm');
        form.action = '<?= AssetHelper::url('follow-ups') ?>/' + currentFollowUpId + '/complete';
        document.getElementById('completionNotesInput').value = document.getElementById('completionNotes').value;
        form.submit();
    }
});

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (currentFollowUpId) {
        const form = document.getElementById('actionForm');
        form.action = '<?= AssetHelper::url('follow-ups') ?>/' + currentFollowUpId + '/delete';
        form.submit();
    }
});

// AJAX filter
var followUpsTableUrl = '<?= AssetHelper::url('follow-ups/table') ?>';

function getFilterParams() {
    var form = document.getElementById('followUpsFilterForm');
    if (!form) return {};
    var data = new FormData(form);
    var params = {};
    data.forEach(function(value, key) {
        if (value) params[key] = value;
    });
    return params;
}

function applyFiltersAjax() {
    var params = getFilterParams();
    var qs = new URLSearchParams(params).toString();
    var url = followUpsTableUrl + (qs ? '?' + qs : '');
    var tbody = document.getElementById('followUpsTableBody');
    var btn = document.getElementById('applyFiltersBtn');
    if (!tbody || !btn) return;
    var btnText = btn.querySelector('.btn-text');
    var origText = btnText ? btnText.textContent : 'Apply Filters';
    if (btnText) btnText.textContent = 'Loading...';
    btn.disabled = true;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) { return r.json(); })
.then(function(data) {
            tbody.innerHTML = data.rowsHtml || '';
            var statPending = document.getElementById('stat-pending');
            var statOverdue = document.getElementById('stat-overdue');
            var statCompleted = document.getElementById('stat-completed');
            var statTotal = document.getElementById('stat-total');
            if (data.stats) {
                if (statPending) statPending.textContent = data.stats.pending || 0;
                if (statOverdue) statOverdue.textContent = data.stats.overdue || 0;
                if (statCompleted) statCompleted.textContent = data.stats.completed || 0;
                if (statTotal) statTotal.textContent = data.stats.total || 0;
            }
            if (typeof feather !== 'undefined') feather.replace();
            initFollowUpsDataTable();
        })
        .catch(function() {
            if (tbody) tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Failed to load. <a href="<?= AssetHelper::url('follow-ups') ?>">Refresh page</a>.</td></tr>';
        })
        .finally(function() {
            if (btnText) btnText.textContent = origText;
            btn.disabled = false;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('followUpsFilterForm');
    var resetBtn = document.getElementById('resetFiltersBtn');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            applyFiltersAjax();
        });
    }
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            var form = document.getElementById('followUpsFilterForm');
            if (form) {
                form.reset();
                applyFiltersAjax();
            }
        });
    }
    initFollowUpsDataTable();
});

function initFollowUpsDataTable() {
    var table = document.getElementById('follow-ups-table');
    if (!table) return;
    try {
        if (typeof $ !== 'undefined' && $.fn && $.fn.dataTable) {
            $.fn.dataTable.ext.errMode = 'none';
        }
        if (typeof $ !== 'undefined' && $.fn.DataTable && $.fn.DataTable.isDataTable('#follow-ups-table')) {
            $('#follow-ups-table').DataTable().destroy();
        }
    } catch (e) {}

    // Check if table contains data rows (not just a single empty/colspan placeholder row)
    var tbody = table.querySelector('tbody');
    if (!tbody || tbody.querySelector('td[colspan]') || tbody.querySelectorAll('tr').length === 0) {
        return; // Skip DataTables initialization on empty state
    }

    try {
        if (typeof DataTable !== 'undefined') {
            new DataTable('#follow-ups-table', {
                paging: true,
                searching: false,
                ordering: true,
                info: true,
                responsive: true
            });
        }
    } catch (e) {}
}
</script>