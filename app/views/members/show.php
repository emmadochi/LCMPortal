<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Member Profile</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('members') ?>">Member Directory</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Member Profile Card -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="avatar-xl mx-auto mb-4">
                        <span class="avatar-title rounded-circle bg-primary bg-gradient font-size-24">
                            <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
                        </span>
                    </div>
                    
                    <h5><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></h5>
                    <p class="text-muted"><?= ucfirst($member['role']) ?></p>
                    
                    <!-- Engagement Score -->
                    <?php if ($engagementScore !== null): ?>
                        <div class="mt-3">
                            <h6>Engagement Score</h6>
                            <div class="progress">
                                <?php 
                                $scoreClass = $engagementScore >= 75 ? 'success' : 
                                            ($engagementScore >= 40 ? 'warning' : 'danger');
                                ?>
                                <div class="progress-bar bg-<?= $scoreClass ?>" 
                                     style="width: <?= $engagementScore ?>%">
                                    <?= $engagementScore ?>%
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <hr>
                
                <!-- Contact Information -->
                <div class="mt-4">
                    <h6 class="font-size-14 mb-3">Contact Information</h6>
                    <p class="text-muted mb-2">
                        <i data-feather="mail" class="me-2 text-primary"></i>
                        <?= htmlspecialchars($member['email']) ?>
                    </p>
                    <?php if ($member['phone'] ?? null): ?>
                        <p class="text-muted mb-2">
                            <i data-feather="phone" class="me-2 text-primary"></i>
                            <?= htmlspecialchars($member['phone']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <!-- Membership Information -->
                <?php if ($primaryMembership): ?>
                    <hr>
                    <div class="mt-4">
                        <h6 class="font-size-14 mb-3">Membership Details</h6>
                        <p class="text-muted mb-1">
                            <strong>Type:</strong> 
                            <span class="badge bg-info-subtle text-info">
                                <?= ucfirst($primaryMembership['membership_type']) ?>
                            </span>
                        </p>
                        <p class="text-muted mb-1">
                            <strong>Status:</strong> 
                            <span class="badge bg-<?= $primaryMembership['status'] === 'active' ? 'success' : 'secondary' ?>-subtle text-<?= $primaryMembership['status'] === 'active' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($primaryMembership['status']) ?>
                            </span>
                        </p>
                        <?php if ($primaryMembership['join_date']): ?>
                            <p class="text-muted mb-1">
                                <strong>Joined:</strong> 
                                <?= date('F j, Y', strtotime($primaryMembership['join_date'])) ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($primaryMembership['baptism_date']): ?>
                            <p class="text-muted mb-1">
                                <strong>Baptized:</strong> 
                                <?= date('F j, Y', strtotime($primaryMembership['baptism_date'])) ?>
                            </p>
                        <?php endif; ?>
                        <p class="text-muted mb-1">
                            <strong>Tithe Status:</strong> 
                            <span class="badge bg-<?= $primaryMembership['tithe_status'] === 'regular' ? 'success' : ($primaryMembership['tithe_status'] === 'irregular' ? 'warning' : 'secondary') ?>-subtle text-<?= $primaryMembership['tithe_status'] === 'regular' ? 'success' : ($primaryMembership['tithe_status'] === 'irregular' ? 'warning' : 'secondary') ?>">
                                <?= ucfirst($primaryMembership['tithe_status']) ?>
                            </span>
                        </p>
                    </div>
                <?php endif; ?>
                
                <!-- Actions -->
                <hr>
                <div class="mt-4">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="createFollowUp(<?= $member['id'] ?>)">
                            <i data-feather="clipboard" class="me-1"></i> Create Follow-up
                        </button>
                        <a href="mailto:<?= $member['email'] ?>" class="btn btn-outline-primary">
                            <i data-feather="mail" class="me-1"></i> Send Email
                        </a>
                        <?php if ($member['phone'] ?? null): ?>
                            <a href="tel:<?= $member['phone'] ?>" class="btn btn-outline-primary">
                                <i data-feather="phone" class="me-1"></i> Call
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Units Membership -->
        <?php if (!empty($units) || !empty($directorUnits)): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Unit Memberships</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($units)): ?>
                        <h6 class="font-size-14 mb-2">Member Of:</h6>
                        <?php foreach ($units as $unit): ?>
                            <span class="badge bg-primary-subtle text-primary me-1 mb-1">
                                <?= htmlspecialchars($unit['name']) ?>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($directorUnits)): ?>
                        <h6 class="font-size-14 mt-3 mb-2">Director Of:</h6>
                        <?php foreach ($directorUnits as $unit): ?>
                            <span class="badge bg-success-subtle text-success me-1 mb-1">
                                <?= htmlspecialchars($unit['name']) ?>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Detailed Information -->
    <div class="col-xl-8">
        <!-- Tabs -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#overview" role="tab">
                            <i data-feather="home" class="me-1"></i> Overview
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#attendance" role="tab">
                            <i data-feather="calendar" class="me-1"></i> Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#followups" role="tab">
                            <i data-feather="clipboard" class="me-1"></i> Follow-ups
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                            <i data-feather="activity" class="me-1"></i> Activity
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="card-body">
                <div class="tab-content">
                    <!-- Overview Tab -->
                    <div class="tab-pane active" id="overview" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <h5>Personal Information</h5>
                                        <table class="table table-borderless mb-0">
                                            <tr>
                                                <td><strong>Full Name:</strong></td>
                                                <td><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td><?= htmlspecialchars($member['email']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Role:</strong></td>
                                                <td><?= ucfirst($member['role']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Account Status:</strong></td>
                                                <td>
                                                    <span class="badge bg-<?= $member['status'] === 'active' ? 'success' : 'secondary' ?>-subtle text-<?= $member['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                        <?= ucfirst($member['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Member Since:</strong></td>
                                                <td><?= date('F j, Y', strtotime($member['created_at'])) ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-body">
                                        <h5>Engagement Insights</h5>
                                        <div class="text-center">
                                            <?php if ($engagementScore !== null): ?>
                                                <h1 class="display-4 text-<?= $engagementScore >= 75 ? 'success' : ($engagementScore >= 40 ? 'warning' : 'danger') ?>">
                                                    <?= $engagementScore ?>%
                                                </h1>
                                                <p class="text-muted">Overall Engagement Score</p>
                                                
                                                <?php if (!empty($predictedNeeds)): ?>
                                                    <hr>
                                                    <h6>Predicted Needs:</h6>
                                                    <?php foreach ($predictedNeeds as $need): ?>
                                                        <div class="alert alert-<?= $need['priority'] === 'high' ? 'danger' : ($need['priority'] === 'medium' ? 'warning' : 'info') ?> mb-2">
                                                            <strong><?= $need['description'] ?></strong>
                                                            <br>
                                                            <small><?= $need['recommended_action'] ?></small>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p class="text-success">No immediate concerns identified</p>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <p class="text-muted">Engagement score not available</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Attendance Tab -->
                    <div class="tab-pane" id="attendance" role="tabpanel">
                        <h5>Recent Attendance (Last 90 Days)</h5>
                        <?php if (!empty($attendanceHistory)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Event Type</th>
                                            <th>Unit</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($attendanceHistory as $attendance): ?>
                                            <tr>
                                                <td><?= date('M j, Y', strtotime($attendance['event_date'])) ?></td>
                                                <td><?= htmlspecialchars(\App\Models\Attendance::getEventTypes()[$attendance['event_type']] ?? ucfirst(str_replace('_', ' ', $attendance['event_type'] ?? 'Service'))) ?></td>
                                                <td><?= htmlspecialchars($attendance['unit_name'] ?? 'General') ?></td>
                                                <td>
                                                    <span class="badge bg-success-subtle text-success">
                                                        <?= ucfirst($attendance['status'] ?? 'Present') ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i data-feather="calendar" class="icon-lg text-muted mb-3"></i>
                                <h5>No Recent Attendance</h5>
                                <p class="text-muted">This member hasn't recorded any attendance recently</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Follow-ups Tab -->
                    <div class="tab-pane" id="followups" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Follow-up History</h5>
                            <button class="btn btn-primary" onclick="createNewFollowUp(<?= $member['id'] ?>)">
                                <i data-feather="plus" class="me-1"></i> New Follow-up
                            </button>
                        </div>
                        
                        <?php if (!empty($followUpHistory)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Due Date</th>
                                            <th>Priority</th>
                                            <th>Notes</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($followUpHistory as $followUp): ?>
                                            <tr>
                                                <td><?= date('M j, Y', strtotime($followUp['created_at'] ?? 'now')) ?></td>
                                                <td><?= ucfirst(str_replace('_', ' ', $followUp['type'] ?? '')) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= ($followUp['status'] ?? '') === 'completed' ? 'success' : 'warning' ?>-subtle text-<?= ($followUp['status'] ?? '') === 'completed' ? 'success' : 'warning' ?>">
                                                        <?= ucfirst($followUp['status'] ?? 'pending') ?>
                                                    </span>
                                                </td>
                                                <td><?= !empty($followUp['due_date']) ? date('M j, Y', strtotime($followUp['due_date'])) : 'N/A' ?></td>
                                                <td>
                                                    <span class="badge bg-<?= ($followUp['priority'] ?? '') === 'urgent' ? 'danger' : (($followUp['priority'] ?? '') === 'high' ? 'warning' : 'secondary') ?>-subtle text-<?= ($followUp['priority'] ?? '') === 'urgent' ? 'danger' : (($followUp['priority'] ?? '') === 'high' ? 'warning' : 'secondary') ?>">
                                                        <?= ucfirst($followUp['priority'] ?? 'medium') ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($followUp['notes'] ?? 'No notes') ?></td>
                                                <td>
                                                    <?php if (!empty($followUp['id'])): ?>
                                                    <a href="<?= AssetHelper::url('follow-ups/' . (int)$followUp['id']) ?>" class="btn btn-sm btn-outline-primary">View details</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i data-feather="clipboard" class="icon-lg text-muted mb-3"></i>
                                <h5>No Follow-ups Recorded</h5>
                                <p class="text-muted">Click "New Follow-up" to create the first follow-up for this member</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Activity Tab -->
                    <div class="tab-pane" id="activity" role="tabpanel">
                        <h5>Recent Activity</h5>
                        <?php if (!empty($recentActivity)): ?>
                            <div class="timeline">
                                <?php foreach ($recentActivity as $activity): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-point bg-primary"></div>
                                        <div class="timeline-content">
                                            <p class="mb-1"><?= htmlspecialchars($activity['description']) ?></p>
                                            <small class="text-muted">
                                                <?= date('M j, Y g:i A', strtotime($activity['activity_date'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i data-feather="activity" class="icon-lg text-muted mb-3"></i>
                                <h5>No Recent Activity</h5>
                                <p class="text-muted">No recent activity recorded for this member</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function createFollowUp(memberId) {
    window.location.href = '<?= AssetHelper::url('follow-ups/create?member_id=') ?>' + memberId;
}

function createNewFollowUp(memberId) {
    window.location.href = '<?= AssetHelper::url('follow-ups/create?member_id=') ?>' + memberId;
}
</script>