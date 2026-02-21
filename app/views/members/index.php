<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Member Directory</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Member Directory</li>
                </ol>
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
                        <h4 class="card-title mb-0">All Church Members</h4>
                        <p class="card-title-desc mb-0">Complete directory of all members with detailed information</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="btn-group">
                            <a href="<?= AssetHelper::url('members/export?format=csv') ?>" class="btn btn-outline-secondary">
                                <i data-feather="download" class="me-1"></i> Export CSV
                            </a>
                            <a href="<?= AssetHelper::url('members/export?format=json') ?>" class="btn btn-outline-secondary">
                                <i data-feather="code" class="me-1"></i> Export JSON
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-12">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?= htmlspecialchars($filters['search'] ?? '') ?>" 
                                       placeholder="Name, email...">
                            </div>
                            
                            <div class="col-md-2">
                                <label for="unit_id" class="form-label">Unit</label>
                                <select class="form-select" id="unit_id" name="unit_id">
                                    <option value="">All Units</option>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?= $unit['id'] ?>" <?= ($filters['unit_id'] == $unit['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($unit['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="membership_type" class="form-label">Membership Type</label>
                                <select class="form-select" id="membership_type" name="membership_type">
                                    <option value="">All Types</option>
                                    <?php foreach ($membershipTypes as $type): ?>
                                        <option value="<?= $type ?>" <?= ($filters['membership_type'] == $type) ? 'selected' : '' ?>>
                                            <?= ucfirst($type) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <?php foreach ($statuses as $statusOption): ?>
                                        <option value="<?= $statusOption ?>" <?= ($filters['status'] == $statusOption) ? 'selected' : '' ?>>
                                            <?= ucfirst($statusOption) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="sort_by" class="form-label">Sort By</label>
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="name" <?= ($filters['sort_by'] == 'name') ? 'selected' : '' ?>>Name</option>
                                    <option value="email" <?= ($filters['sort_by'] == 'email') ? 'selected' : '' ?>>Email</option>
                                    <option value="membership_type" <?= ($filters['sort_by'] == 'membership_type') ? 'selected' : '' ?>>Membership Type</option>
                                    <option value="engagement_score" <?= ($filters['sort_by'] == 'engagement_score') ? 'selected' : '' ?>>Engagement Score</option>
                                    <option value="join_date" <?= ($filters['sort_by'] == 'join_date') ? 'selected' : '' ?>>Join Date</option>
                                </select>
                            </div>
                            
                            <div class="col-md-1">
                                <label for="sort_order" class="form-label">Order</label>
                                <select class="form-select" id="sort_order" name="sort_order">
                                    <option value="asc" <?= ($filters['sort_order'] == 'asc') ? 'selected' : '' ?>>Asc</option>
                                    <option value="desc" <?= ($filters['sort_order'] == 'desc') ? 'selected' : '' ?>>Desc</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12 mt-2">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="filter" class="me-1"></i> Apply Filters
                                </button>
                                <a href="<?= AssetHelper::url('members') ?>" class="btn btn-outline-secondary ms-2">
                                    <i data-feather="refresh-ccw" class="me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Summary Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <h3 class="text-primary"><?= count($members) ?></h3>
                                <p class="mb-0">Total Members</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <h3 class="text-success">
                                    <?= count(array_filter($members, function($m) { return ($m['engagement_score'] ?? 0) >= 75; })) ?>
                                </h3>
                                <p class="mb-0">Highly Engaged</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <h3 class="text-warning">
                                    <?= count(array_filter($members, function($m) { return ($m['days_since_last_attendance'] ?? 0) > 30; })) ?>
                                </h3>
                                <p class="mb-0">Need Attention</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <h3 class="text-info">
                                    <?= round(array_sum(array_column($members, 'engagement_score')) / count($members), 1) ?>%
                                </h3>
                                <p class="mb-0">Avg Engagement</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Members Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="members-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Membership</th>
                                <th>Unit</th>
                                <th>Engagement</th>
                                <th>Attendance</th>
                                <th>Last Seen</th>
                                <th>Tithe Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($members)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i data-feather="users" class="icon-lg text-muted mb-3"></i>
                                        <h5>No members found</h5>
                                        <p class="text-muted">Try adjusting your search filters</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-3">
                                                    <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                        <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h5 class="font-size-14 mb-1">
                                                        <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                                                    </h5>
                                                    <p class="text-muted mb-0">
                                                        <?= ucfirst($member['role']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <a href="mailto:<?= $member['email'] ?>"><?= htmlspecialchars($member['email']) ?></a>
                                        </td>
                                        
                                        <td>
                                            <?php if ($member['membership_type']): ?>
                                                <span class="badge bg-info-subtle text-info">
                                                    <?= ucfirst($member['membership_type']) ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    Joined: <?= $member['join_date'] ? date('M j, Y', strtotime($member['join_date'])) : 'N/A' ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">No Membership</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td>
                                            <?php if ($member['unit_name']): ?>
                                                <span class="badge bg-primary-subtle text-primary">
                                                    <?= htmlspecialchars($member['unit_name']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">None</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td>
                                            <?php if ($member['engagement_score'] !== null): ?>
                                                <?php 
                                                $score = $member['engagement_score'];
                                                $scoreClass = $score >= 75 ? 'success' : ($score >= 40 ? 'warning' : 'danger');
                                                ?>
                                                <span class="badge bg-<?= $scoreClass ?>-subtle text-<?= $scoreClass ?> fs-6">
                                                    <?= $score ?>%
                                                </span>
                                                <div class="progress mt-1" style="height: 5px; width: 80px;">
                                                    <div class="progress-bar bg-<?= $scoreClass ?>" 
                                                         style="width: <?= $score ?>%"></div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td>
                                            <span class="badge bg-info-subtle text-info">
                                                <?= $member['attendance_count_30_days'] ?> days
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <?php if ($member['last_attendance_date']): ?>
                                                <span class="badge bg-<?= ($member['days_since_last_attendance'] ?? 0) > 30 ? 'danger' : 'success' ?>-subtle text-<?= ($member['days_since_last_attendance'] ?? 0) > 30 ? 'danger' : 'success' ?>">
                                                    <?= date('M j', strtotime($member['last_attendance_date'])) ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    <?= $member['days_since_last_attendance'] ?? 0 ?> days ago
                                                </small>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Never</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td>
                                            <?php if ($member['tithe_status']): ?>
                                                <?php 
                                                $titheClass = $member['tithe_status'] === 'regular' ? 'success' : 
                                                            ($member['tithe_status'] === 'irregular' ? 'warning' : 'secondary');
                                                ?>
                                                <span class="badge bg-<?= $titheClass ?>-subtle text-<?= $titheClass ?>">
                                                    <?= ucfirst($member['tithe_status']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= AssetHelper::url('members/' . $member['id']) ?>" 
                                                   class="btn btn-sm btn-primary" title="View Details">
                                                    <i data-feather="eye" class="me-1"></i> View
                                                </a>
                                                <?php if ($member['user_status'] === 'active'): ?>
                                                    <button class="btn btn-sm btn-outline-warning" 
                                                            onclick="createFollowUp(<?= $member['id'] ?>)" 
                                                            title="Create Follow-up">
                                                        <i data-feather="clipboard" class="me-1"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if (!empty($members)): ?>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p class="text-muted">
                                Showing <?= count($members) ?> members
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function createFollowUp(memberId) {
    window.location.href = '<?= AssetHelper::url('follow-ups/create?member_id=') ?>' + memberId;
}

// Initialize DataTable
document.addEventListener('DOMContentLoaded', function() {
    if (typeof DataTable !== 'undefined') {
        new DataTable('#members-table', {
            paging: true,
            searching: false, // We have our own search
            ordering: true,
            info: true,
            responsive: true
        });
    }
});
</script>