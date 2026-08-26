<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>
<div class="db-page premium-form">
    <!-- ── Hero Header ── -->
    <div class="db-hero mb-4">
        <div class="row align-items-center position-relative" style="z-index: 1;">
            <div class="col-md-8">
                <div class="db-hero-greeting">
                    <span class="live-dot me-1"></span> DIRECTORY
                </div>
                <div class="db-hero-name">Member Directory</div>
                <p class="db-hero-sub">Complete directory of all church members with detailed information and engagement stats.</p>
            </div>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <a href="<?= AssetHelper::url('members/export?format=csv') ?>" class="btn-premium btn-success">
                        <i class="bx bx-download"></i> CSV
                    </a>
                    <a href="<?= AssetHelper::url('members/export?format=json') ?>" class="btn-premium btn-info">
                        <i class="bx bx-code-alt"></i> JSON
                    </a>
                    <a href="<?= AssetHelper::url('members/create') ?>" class="btn-premium btn-primary">
                        <i class="bx bx-user-plus"></i> New Member
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Summary Stats -->
    <div class="db-kpi-grid mb-4">
        <div class="db-kpi kpi-users">
            <div class="db-kpi-accent"></div>
            <div class="db-kpi-icon-wrap"><i class="bx bx-group"></i></div>
            <div class="db-kpi-label">Total Members</div>
            <div class="db-kpi-value"><?= count($members) ?></div>
            <div class="db-kpi-sub">registered in portal</div>
            <div class="db-kpi-bg-icon">👥</div>
        </div>
        
        <div class="db-kpi kpi-present">
            <div class="db-kpi-accent"></div>
            <div class="db-kpi-icon-wrap"><i class="bx bx-heart"></i></div>
            <div class="db-kpi-label">Highly Engaged</div>
            <div class="db-kpi-value"><?= count(array_filter($members, function($m) { return ($m['engagement_score'] ?? 0) >= 75; })) ?></div>
            <div class="db-kpi-sub">score >= 75%</div>
            <div class="db-kpi-bg-icon">🔥</div>
        </div>

        <div class="db-kpi kpi-attendance">
            <div class="db-kpi-accent"></div>
            <div class="db-kpi-icon-wrap"><i class="bx bx-error-circle"></i></div>
            <div class="db-kpi-label">Need Attention</div>
            <div class="db-kpi-value"><?= count(array_filter($members, function($m) { return ($m['days_since_last_attendance'] ?? 0) > 30; })) ?></div>
            <div class="db-kpi-sub">absent > 30 days</div>
            <div class="db-kpi-bg-icon">⚠️</div>
        </div>

        <div class="db-kpi kpi-reports">
            <div class="db-kpi-accent"></div>
            <div class="db-kpi-icon-wrap"><i class="bx bx-bar-chart-alt-2"></i></div>
            <div class="db-kpi-label">Avg Engagement</div>
            <div class="db-kpi-value">
                <?php 
                $memberCount = count($members);
                echo $memberCount > 0 ? round(array_sum(array_column($members, 'engagement_score')) / $memberCount, 1) : 0;
                ?>%
            </div>
            <div class="db-kpi-sub">overall platform score</div>
            <div class="db-kpi-bg-icon">📊</div>
        </div>
    </div>

    <div class="db-panel">
        <div class="db-panel-header">
            <h6 class="db-panel-title">
                <span class="pi-blue"><i class="bx bx-list-ul"></i></span>
                All Church Members
            </h6>
        </div>
        <div class="db-panel-body">
            <!-- Filters -->
            <form method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="search" name="search" 
                                value="<?= htmlspecialchars($filters['search'] ?? '') ?>" 
                                placeholder="Search Name, email...">
                    </div>
                    
                    <div class="col-md-2">
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
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="name" <?= ($filters['sort_by'] == 'name') ? 'selected' : '' ?>>Sort: Name</option>
                            <option value="email" <?= ($filters['sort_by'] == 'email') ? 'selected' : '' ?>>Sort: Email</option>
                            <option value="engagement_score" <?= ($filters['sort_by'] == 'engagement_score') ? 'selected' : '' ?>>Sort: Engagement</option>
                            <option value="join_date" <?= ($filters['sort_by'] == 'join_date') ? 'selected' : '' ?>>Sort: Join Date</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn-premium btn-primary w-100">
                            <i class="bx bx-filter"></i> Filter
                        </button>
                        <a href="<?= AssetHelper::url('members') ?>" class="btn-premium btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
            
            <!-- Members Table -->
            <div class="table-responsive">
                <table class="db-table" id="members-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Membership</th>
                            <th>Unit</th>
                            <th>Engagement</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-3">
                                                <span class="avatar-title rounded-circle bg-primary-subtle text-primary fw-bold" style="font-family: 'Inter', sans-serif;">
                                                    <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
                                                </span>
                                            </div>
                                            <div>
                                                <strong class="font-size-14 mb-0" style="color: var(--db-text);">
                                                    <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                                                </strong>
                                                <div class="text-muted" style="font-size: .75rem; margin-top: 2px;">
                                                    <?= $member['role'] === 'user' ? 'Member' : ($member['role'] === 'pastor' ? 'Pastor' : ($member['role'] === 'head_pastor' ? 'Head Pastor' : ucfirst($member['role']))) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <a href="mailto:<?= $member['email'] ?>" style="color: var(--db-text); font-weight: 500; font-size: .85rem;"><?= htmlspecialchars($member['email']) ?></a>
                                    </td>
                                    
                                    <td>
                                        <?php if ($member['membership_type']): ?>
                                            <span class="premium-badge premium-badge-info">
                                                <?= ucfirst($member['membership_type']) ?>
                                            </span>
                                            <div class="text-muted mt-1" style="font-size: .75rem;">
                                                Joined: <?= $member['join_date'] ? date('M j, Y', strtotime($member['join_date'])) : 'N/A' ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="premium-badge premium-badge-secondary">No Membership</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <?php if ($member['unit_name']): ?>
                                            <span class="premium-badge premium-badge-primary">
                                                <?= htmlspecialchars($member['unit_name']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted fs-6">—</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <?php if ($member['engagement_score'] !== null): ?>
                                            <?php 
                                            $score = $member['engagement_score'];
                                            $scoreClass = $score >= 75 ? 'success' : ($score >= 40 ? 'warning' : 'danger');
                                            ?>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="text-<?= $scoreClass ?> fw-bold" style="font-size: .85rem;">
                                                    <?= $score ?>%
                                                </span>
                                                <div class="progress flex-grow-1" style="height: 6px; max-width: 80px; background: rgba(0,0,0,.05);">
                                                    <div class="progress-bar bg-<?= $scoreClass ?>" style="width: <?= $score ?>%"></div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted fs-6">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="<?= AssetHelper::url('members/' . $member['id']) ?>" 
                                                class="btn-premium btn-premium-sm btn-info" title="View Details">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a href="<?= AssetHelper::url('members/' . $member['id'] . '/edit') ?>" 
                                                class="btn-premium btn-premium-sm btn-secondary" title="Edit Member">
                                                <i class="bx bx-edit-alt"></i>
                                            </a>
                                            <?php if ($member['user_status'] === 'active'): ?>
                                                <button class="btn-premium btn-premium-sm btn-warning" 
                                                        onclick="createFollowUp(<?= $member['id'] ?>)" 
                                                        title="Create Follow-up">
                                                    <i class="bx bx-clipboard"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination info -->
            <?php if (!empty($members)): ?>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <p class="text-muted" style="font-size: .85rem;">
                            Showing <?= count($members) ?> members
                        </p>
                    </div>
                </div>
            <?php endif; ?>
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
            responsive: true,
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });
    }
});
</script>