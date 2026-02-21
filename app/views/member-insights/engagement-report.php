<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Engagement Report</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('insights') ?>">Member Insights</a></li>
                    <li class="breadcrumb-item active">Engagement Report</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Member Engagement Analysis</h4>
                <p class="card-title-desc">Detailed breakdown of member engagement levels and improvement opportunities</p>
            </div>
            <div class="card-body">
                <!-- Individual Scores Table -->
                <h5>Individual Engagement Scores</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Membership Type</th>
                                <th>Engagement Score</th>
                                <th>Status</th>
                                <th>Last Activity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($engagementData['individual_scores'] as $scoreData): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($scoreData['user']['first_name'] . ' ' . $scoreData['user']['last_name']) ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($scoreData['user']['email']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= ucfirst($scoreData['membership']['membership_type'] ?? 'Visitor') ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                        $score = $scoreData['engagement_score'];
                                        $scoreClass = $score >= 75 ? 'success' : ($score >= 40 ? 'warning' : 'danger');
                                        ?>
                                        <span class="badge bg-<?= $scoreClass ?>-subtle text-<?= $scoreClass ?> fs-6">
                                            <?= $score ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($score >= 75): ?>
                                            <span class="badge bg-success">Highly Engaged</span>
                                        <?php elseif ($score >= 40): ?>
                                            <span class="badge bg-warning">Moderate</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Low Engagement</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($scoreData['user']['updated_at']): ?>
                                            <?= date('M j, Y', strtotime($scoreData['user']['updated_at'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">No recent activity</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= AssetHelper::url('users/' . $scoreData['user']['id']) ?>" 
                                               class="btn btn-sm btn-outline-primary">View Profile</a>
                                            <button class="btn btn-sm btn-outline-info" 
                                                    onclick="generateFollowUp(<?= $scoreData['user']['id'] ?>)">
                                                Follow Up
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Behavioral Patterns -->
                <div class="mt-5">
                    <h5>Behavioral Patterns</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <h3 class="text-primary">
                                        <?= $engagementData['individual_scores'][0]['engagement_score'] ?? 0 ?>%
                                    </h3>
                                    <p>Highest Engagement Score</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h3 class="text-warning">
                                        <?= round(array_sum(array_column($engagementData['individual_scores'], 'engagement_score')) / count($engagementData['individual_scores']), 1) ?>%
                                    </h3>
                                    <p>Average Engagement</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-danger">
                                <div class="card-body text-center">
                                    <h3 class="text-danger">
                                        <?= min(array_column($engagementData['individual_scores'], 'engagement_score')) ?? 0 ?>%
                                    </h3>
                                    <p>Lowest Engagement Score</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Improvement Areas -->
                <div class="mt-5">
                    <h5>Improvement Opportunities</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-warning-subtle">
                                    <h6 class="mb-0">Low Engagement Members</h6>
                                </div>
                                <div class="card-body">
                                    <p>Members with engagement scores below 40%:</p>
                                    <ul>
                                        <?php 
                                        $lowEngagement = array_filter($engagementData['individual_scores'], function($item) {
                                            return $item['engagement_score'] < 40;
                                        });
                                        foreach (array_slice($lowEngagement, 0, 5) as $member): ?>
                                            <li><?= htmlspecialchars($member['user']['first_name'] . ' ' . $member['user']['last_name']) ?> (<?= $member['engagement_score'] ?>%)</li>
                                        <?php endforeach; ?>
                                        <?php if (count($lowEngagement) > 5): ?>
                                            <li>and <?= count($lowEngagement) - 5 ?> more...</li>
                                        <?php endif; ?>
                                    </ul>
                                    <button class="btn btn-warning" onclick="createBulkFollowUps()">
                                        <i data-feather="users" class="me-1"></i> Create Bulk Follow-ups
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info-subtle">
                                    <h6 class="mb-0">High Potential Members</h6>
                                </div>
                                <div class="card-body">
                                    <p>Members with moderate engagement (40-74%) who could increase involvement:</p>
                                    <ul>
                                        <?php 
                                        $moderateEngagement = array_filter($engagementData['individual_scores'], function($item) {
                                            return $item['engagement_score'] >= 40 && $item['engagement_score'] < 75;
                                        });
                                        foreach (array_slice($moderateEngagement, 0, 5) as $member): ?>
                                            <li><?= htmlspecialchars($member['user']['first_name'] . ' ' . $member['user']['last_name']) ?> (<?= $member['engagement_score'] ?>%)</li>
                                        <?php endforeach; ?>
                                        <?php if (count($moderateEngagement) > 5): ?>
                                            <li>and <?= count($moderateEngagement) - 5 ?> more...</li>
                                        <?php endif; ?>
                                    </ul>
                                    <button class="btn btn-info" onclick="inviteToMinistries()">
                                        <i data-feather="plus-circle" class="me-1"></i> Invite to Ministries
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateFollowUp(userId) {
    // This would open a modal or redirect to create follow-up
    alert('Generating follow-up for user ID: ' + userId);
    // In real implementation, this would make an AJAX call to create follow-up
}

function createBulkFollowUps() {
    if (confirm('Create follow-ups for all low-engagement members?')) {
        // AJAX call to bulk create follow-ups
        alert('Bulk follow-ups created!');
    }
}

function inviteToMinistries() {
    alert('Opening ministry invitation interface...');
    // This would open a modal with ministry options
}
</script>