<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Create Follow-up</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('follow-ups') ?>">Follow-ups</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">New Follow-up</h4>
            </div>
            
            <div class="card-body">
                <?php if ($this->session->hasFlash('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($this->session->getFlash('errors') as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?= AssetHelper::url('follow-ups') ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
                            <select class="form-select" id="member_id" name="member_id" required>
                                <option value="">Select Member...</option>
                                <?php foreach ($members as $member): ?>
                                    <option value="<?= $member['id'] ?>" 
                                        <?= (isset($_POST['member_id']) && $_POST['member_id'] == $member['id']) || ($selectedMember == $member['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?> 
                                        (<?= htmlspecialchars($member['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Select the member this follow-up is for</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Follow-up Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Select Type...</option>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= $type ?>" 
                                        <?= (isset($_POST['type']) && $_POST['type'] === $type) ? 'selected' : '' ?>>
                                        <?= ucfirst(str_replace('_', ' ', $type)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="">Select Priority...</option>
                                <option value="urgent" <?= (isset($_POST['priority']) && $_POST['priority'] === 'urgent') ? 'selected' : '' ?>>Urgent</option>
                                <option value="high" <?= (isset($_POST['priority']) && $_POST['priority'] === 'high') ? 'selected' : '' ?>>High</option>
                                <option value="medium" <?= (isset($_POST['priority']) && $_POST['priority'] === 'medium') ? 'selected' : '' ?>>Medium</option>
                                <option value="low" <?= (isset($_POST['priority']) && $_POST['priority'] === 'low') ? 'selected' : '' ?>>Low</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="assigned_to" class="form-label">Assign To</label>
                            <select class="form-select" id="assigned_to" name="assigned_to">
                                <option value="">Unassigned</option>
                                <?php foreach ($members as $staff): ?>
                                    <option value="<?= $staff['id'] ?>" 
                                        <?= (isset($_POST['assigned_to']) && $_POST['assigned_to'] == $staff['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?> 
                                        (<?= htmlspecialchars($staff['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Optional: Assign this follow-up to a specific staff member</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="due_date" name="due_date" 
                                   value="<?= htmlspecialchars($_POST['due_date'] ?? date('Y-m-d', strtotime('+7 days'))) ?>" 
                                   required min="<?= date('Y-m-d') ?>">
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" 
                                      placeholder="Add any additional notes or context for this follow-up..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                            <div class="form-text">Optional: Provide context or specific instructions for this follow-up</div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= AssetHelper::url('follow-ups') ?>" class="btn btn-secondary">
                            <i data-feather="arrow-left" class="me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Create Follow-up
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Follow-up Guidelines</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i data-feather="check-circle" class="text-success me-2"></i>
                        <strong>Member Selection:</strong> Choose the member who needs follow-up attention
                    </li>
                    <li class="mb-3">
                        <i data-feather="check-circle" class="text-success me-2"></i>
                        <strong>Staff Assignment:</strong> Assign follow-ups to specific team members for accountability
                    </li>
                    <li class="mb-3">
                        <i data-feather="check-circle" class="text-success me-2"></i>
                        <strong>Priority Levels:</strong>
                        <ul class="ps-4 mt-1">
                            <li><span class="badge bg-danger">Urgent</span> - Immediate attention required</li>
                            <li><span class="badge bg-warning">High</span> - Important, time-sensitive</li>
                            <li><span class="badge bg-primary">Medium</span> - Standard follow-up</li>
                            <li><span class="badge bg-secondary">Low</span> - Routine check-in</li>
                        </ul>
                    </li>
                    <li class="mb-3">
                        <i data-feather="check-circle" class="text-success me-2"></i>
                        <strong>Due Dates:</strong> Set realistic deadlines based on priority
                    </li>
                    <li class="mb-3">
                        <i data-feather="check-circle" class="text-success me-2"></i>
                        <strong>Notes:</strong> Include specific details to help with follow-up
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Common Follow-up Types</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <strong>New Member Welcome</strong> - First-time visitor follow-up
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Engagement Check</strong> - Member showing low participation
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Prayer Request</strong> - Follow-up on specific prayer needs
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Discipleship</strong> - Spiritual growth guidance
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Ministry Invitation</strong> - Invite to serve or join groups
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>