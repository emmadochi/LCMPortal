<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Member Insights Dashboard</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Member Insights</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="unit_id" class="form-label">Filter by Unit</label>
                        <select class="form-select" id="unit_id" name="unit_id">
                            <option value="">All Units</option>
                            <?php foreach ($units as $unit): ?>
                                <option value="<?= $unit['id'] ?>" <?= ($selectedUnit == $unit['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($unit['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control">Apply Filter</button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <a href="<?= AssetHelper::url('insights/export?format=pdf&unit_id=' . ($selectedUnit ?? '')) ?>" 
                           class="btn btn-outline-secondary form-control">
                            <i data-feather="download" class="me-1"></i> Export Report
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Key Metrics -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-6">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Active Members</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $insights['membership_stats']['total_active'] ?? 0 ?>">0</span>
                        </h4>
                    </div>
                    <div class="col-6">
                        <div class="text-end">
                            <i data-feather="users" class="icon-lg text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="text-nowrap">
                    <span class="badge bg-primary-subtle text-primary">Active</span>
                    <span class="ms-1 text-muted font-size-13">Members</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-6">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Avg Engagement</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $insights['engagement_metrics']['average_score'] ?? 0 ?>">0</span>%
                        </h4>
                    </div>
                    <div class="col-6">
                        <div class="text-end">
                            <i data-feather="trending-up" class="icon-lg text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="text-nowrap">
                    <span class="badge bg-success-subtle text-success">Score</span>
                    <span class="ms-1 text-muted font-size-13">Community</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-6">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Pending Follow-ups</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $insights['follow_up_insights']['pending_count'] ?? 0 ?>">0</span>
                        </h4>
                    </div>
                    <div class="col-6">
                        <div class="text-end">
                            <i data-feather="clipboard" class="icon-lg text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="text-nowrap">
                    <span class="badge bg-warning-subtle text-warning">Action</span>
                    <span class="ms-1 text-muted font-size-13">Required</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-6">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Completion Rate</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?= $insights['follow_up_insights']['completion_rate'] ?? 0 ?>">0</span>%
                        </h4>
                    </div>
                    <div class="col-6">
                        <div class="text-end">
                            <i data-feather="check-circle" class="icon-lg text-info"></i>
                        </div>
                    </div>
                </div>
                <div class="text-nowrap">
                    <span class="badge bg-info-subtle text-info">Follow-ups</span>
                    <span class="ms-1 text-muted font-size-13">Completed</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Insights Section -->
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">AI-Powered Insights</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Engagement Distribution -->
                    <div class="col-md-6">
                        <h5>Engagement Distribution</h5>
                        <canvas id="engagementChart" height="200"></canvas>
                    </div>
                    
                    <!-- Membership Types -->
                    <div class="col-md-6">
                        <h5>Membership Composition</h5>
                        <canvas id="membershipChart" height="200"></canvas>
                    </div>
                </div>
                
                <!-- Trend Analysis -->
                <div class="mt-4">
                    <h5>Engagement Trends (Last 6 Months)</h5>
                    <canvas id="trendChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <!-- Risk Assessment -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Risk Assessment</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($insights['risk_assessment']['high_risk_members'])): ?>
                    <div class="alert alert-danger">
                        <h6><i data-feather="alert-triangle" class="me-1"></i> High Risk Members</h6>
                        <p class="mb-0"><?= count($insights['risk_assessment']['high_risk_members']) ?> members showing disengagement signs</p>
                        <a href="#high-risk-details" class="btn btn-sm btn-danger mt-2">View Details</a>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($insights['risk_assessment']['moderate_risk_members'])): ?>
                    <div class="alert alert-warning">
                        <h6><i data-feather="alert-circle" class="me-1"></i> Monitor These Members</h6>
                        <p class="mb-0"><?= count($insights['risk_assessment']['moderate_risk_members']) ?> members need attention</p>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($insights['risk_assessment']['high_risk_members']) && empty($insights['risk_assessment']['moderate_risk_members'])): ?>
                    <div class="alert alert-success">
                        <h6><i data-feather="thumbs-up" class="me-1"></i> Healthy Community</h6>
                        <p class="mb-0">No immediate risk concerns identified</p>
                    </div>
                <?php endif; ?>
                
                <div class="mt-3">
                    <h6>Risk Factors:</h6>
                    <ul class="list-unstyled">
                        <?php foreach ($insights['risk_assessment']['risk_factors'] as $factor => $count): ?>
                            <li class="mb-1">
                                <span class="badge bg-secondary"><?= $count ?></span>
                                <?= ucfirst(str_replace('_', ' ', $factor)) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Quick Actions</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= AssetHelper::url('insights/engagement') ?>" class="btn btn-primary">
                        <i data-feather="bar-chart-2" class="me-1"></i> Detailed Engagement Report
                    </a>
                    <a href="<?= AssetHelper::url('insights/recommendations') ?>" class="btn btn-info">
                        <i data-feather="cpu" class="me-1"></i> AI Recommendations
                    </a>
                    <a href="<?= AssetHelper::url('insights/predictions') ?>" class="btn btn-success">
                        <i data-feather="trending-up" class="me-1"></i> Future Predictions
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- High Risk Members Detail (Hidden by default) -->
<div class="row" id="high-risk-details" style="display: none;">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">High-Risk Members Details</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Engagement Score</th>
                                <th>Risk Level</th>
                                <th>Key Factors</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($insights['risk_assessment']['high_risk_members'] as $riskMember): ?>
                                <tr>
                                    <td><?= htmlspecialchars($riskMember['member']['first_name'] . ' ' . $riskMember['member']['last_name']) ?></td>
                                    <td><span class="badge bg-danger"><?= $riskMember['member']['engagement_score'] ?></span></td>
                                    <td><span class="badge bg-danger">High</span></td>
                                    <td>
                                        <?php foreach ($riskMember['risk_data']['factors'] as $factor => $description): ?>
                                            <small class="d-block"><?= htmlspecialchars($description) ?></small>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <a href="<?= AssetHelper::url('users/' . $riskMember['member']['user_id']) ?>" 
                                           class="btn btn-sm btn-outline-primary">View Profile</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Counter animation
    const counters = document.querySelectorAll('.counter-value');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                counter.textContent = target;
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current);
            }
        }, 30);
    });
    
    // Engagement Chart
    const engagementCtx = document.getElementById('engagementChart');
    if (engagementCtx) {
        new Chart(engagementCtx, {
            type: 'doughnut',
            data: {
                labels: ['Highly Engaged', 'Moderately Engaged', 'Low Engagement'],
                datasets: [{
                    data: [
                        <?= $insights['engagement_metrics']['highly_engaged'] ?? 0 ?>,
                        <?= $insights['engagement_metrics']['moderately_engaged'] ?? 0 ?>,
                        <?= $insights['engagement_metrics']['low_engagement'] ?? 0 ?>
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.6)',
                        'rgba(255, 193, 7, 0.6)',
                        'rgba(220, 53, 69, 0.6)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    // Membership Chart
    const membershipCtx = document.getElementById('membershipChart');
    if (membershipCtx) {
        const membershipData = <?= json_encode(array_values($insights['membership_stats']['by_type'] ?? [])) ?>;
        const membershipLabels = <?= json_encode(array_keys($insights['membership_stats']['by_type'] ?? [])) ?>;
        
        new Chart(membershipCtx, {
            type: 'pie',
            data: {
                labels: membershipLabels,
                datasets: [{
                    data: membershipData,
                    backgroundColor: [
                        'rgba(0, 123, 255, 0.6)',
                        'rgba(108, 117, 125, 0.6)',
                        'rgba(23, 162, 184, 0.6)',
                        'rgba(102, 16, 242, 0.6)',
                        'rgba(255, 105, 180, 0.6)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    // Trend Chart
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        const trendLabels = <?= json_encode(array_column($insights['trends']['engagement_trend'] ?? [], 'month')) ?>;
        const trendData = <?= json_encode(array_column($insights['trends']['engagement_trend'] ?? [], 'score')) ?>;
        
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Avg Engagement Score',
                    data: trendData,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }
});
</script>