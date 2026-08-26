<?php
use App\Core\Session;
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$session = Session::getInstance();
$csrfToken = Security::generateCSRFToken();
$userRole = $session->get('user_role');
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Member Profile</h4>
            <div class="page-title-right">
                <a href="<?= AssetHelper::url('members/' . $member['id'] . '/edit') ?>" class="btn btn-primary">
                    <i data-feather="edit-2" class="me-1"></i> Edit Member
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <?php
                    $avatarSrc = !empty($member['profile_picture'])
                        ? AssetHelper::url($member['profile_picture'])
                        : AssetHelper::image('users/avatar-1.jpg');
                    ?>
                    <div class="position-relative d-inline-block">
                        <img id="profile-avatar" src="<?= htmlspecialchars($avatarSrc) ?>" alt="" class="avatar-lg rounded-circle img-thumbnail">
                    </div>
                    <div class="mt-3">
                        <h5 class="mb-1"><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></h5>
                        <p class="text-muted mb-2"><?= htmlspecialchars($member['email']) ?></p>
                        <?php if (!empty($church)): ?>
                            <p class="text-primary font-size-14 mb-3">
                                <i data-feather="home" class="icon-sm me-1"></i><?= htmlspecialchars($church['name']) ?>
                            </p>
                        <?php else: ?>
                            <p class="text-muted font-size-14 mb-3">
                                <i data-feather="globe" class="icon-sm me-1"></i>All Branches (Global Admin)
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mb-3">
                        <span class="badge bg-info fs-6"><?= ucfirst($member['role']) ?></span>
                        <?php if (isset($member['status'])): ?>
                            <?php if ($member['status'] === 'active'): ?>
                                <span class="badge bg-success fs-6">Active</span>
                            <?php elseif ($member['status'] === 'inactive'): ?>
                                <span class="badge bg-secondary fs-6">Inactive</span>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6">Suspended</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge bg-success fs-6">Active</span>
                        <?php endif; ?>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Engagement Rating Section -->
                <?php /* if (isset($engagementScore) && $engagementScore !== null): ?>
                    <div class="text-start mb-4">
                        <h5 class="font-size-15 mb-3">
                            <i data-feather="star" class="me-1"></i> Engagement Rating
                        </h5>
                        <?php 
                        $scoreClass = $engagementScore >= 75 ? 'success' : 
                                    ($engagementScore >= 40 ? 'warning' : 'danger');
                        $ratingLabel = $engagementScore >= 75 ? 'High' : 
                                     ($engagementScore >= 40 ? 'Moderate' : 'Low');
                        ?>
                        <div class="text-center mb-3">
                            <h2 class="mb-0 text-<?= $scoreClass ?>"><?= $engagementScore ?>%</h2>
                            <p class="text-muted mb-0">
                                <span class="badge bg-<?= $scoreClass ?>"><?= $ratingLabel ?> Engagement</span>
                            </p>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-<?= $scoreClass ?>" 
                                 role="progressbar" 
                                 style="width: <?= $engagementScore ?>%"
                                 aria-valuenow="<?= $engagementScore ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                <?= $engagementScore ?>%
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            Based on attendance, reports, activity, and unit participation
                        </small>
                    </div>
                    <hr class="my-4">
                <?php endif; */ ?>

                <div class="text-start">
                    <h5 class="font-size-15">Information</h5>
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">Email :</th>
                                    <td><?= htmlspecialchars($member['email']) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Role :</th>
                                    <td><span class="badge bg-info"><?= ucfirst($member['role']) ?></span></td>
                                </tr>
                                <tr>
                                    <th scope="row">Church Branch :</th>
                                    <td>
                                        <?php if (!empty($church)): ?>
                                            <span class="text-primary font-weight-semibold">
                                                <i data-feather="home" class="icon-sm me-1"></i><?= htmlspecialchars($church['name']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <i data-feather="globe" class="icon-sm me-1"></i>All Branches (Global Admin)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Status :</th>
                                    <td>
                                        <?php if (isset($member['status'])): ?>
                                            <?php if ($member['status'] === 'active'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php elseif ($member['status'] === 'inactive'): ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Suspended</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Member Since :</th>
                                    <td><?= isset($member['created_at']) ? date('F d, Y', strtotime($member['created_at'])) : 'N/A' ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <!-- AI Insights Section -->
        <?php /* if (isset($predictedNeeds) && !empty($predictedNeeds)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i data-feather="zap" class="me-1"></i> AI-Powered Insights
                    </h5>
                </div>
                <div class="card-body">
                    <?php foreach ($predictedNeeds as $need): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i data-feather="info" class="me-2"></i>
                            <strong>Predicted Need:</strong> 
                            <?= htmlspecialchars($need)
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; */ ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i data-feather="users" class="me-1"></i> Member Of</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($units)): ?>
                            <p class="text-muted mb-0">Not a member of any unit</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle mb-0">
                                    <tbody>
                                        <?php foreach ($units as $unit): ?>
                                            <tr>
                                                <td>
                                                    <h5 class="font-size-14 mb-1">
                                                        <a href="<?= AssetHelper::url('units/' . $unit['id']) ?>" class="text-dark">
                                                            <?= htmlspecialchars($unit['name']) ?>
                                                        </a>
                                                    </h5>
                                                    <p class="text-muted mb-0 font-size-12">
                                                        Role: <span class="badge bg-secondary"><?= htmlspecialchars($unit['role']) ?></span>
                                                    </p>
                                                </td>
                                                <td>
                                                    <small class="text-muted"><?= date('M d, Y', strtotime($unit['joined_at'])) ?></small>
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

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i data-feather="user-check" class="me-1"></i> Director Of</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($directorUnits)): ?>
                            <p class="text-muted mb-0">Not a director of any unit</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle mb-0">
                                    <tbody>
                                        <?php foreach ($directorUnits as $unit): ?>
                                            <tr>
                                                <td>
                                                    <h5 class="font-size-14 mb-1">
                                                        <a href="<?= AssetHelper::url('units/' . $unit['id']) ?>" class="text-dark">
                                                            <?= htmlspecialchars($unit['name']) ?>
                                                        </a>
                                                    </h5>
                                                    <p class="text-muted mb-0 font-size-12">
                                                        Assigned: <?= date('M d, Y', strtotime($unit['assigned_at'])) ?>
                                                    </p>
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

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i data-feather="calendar" class="me-1"></i> Attendance History (Last 90 days)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Event Date</th>
                                        <th>Event Name</th>
                                        <th>Service</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($attendanceHistory)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No attendance records found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($attendanceHistory as $attendance): ?>
                                            <tr>
                                                <td><?= date('M d, Y', strtotime($attendance['event_date'])) ?></td>
                                                <td><?= htmlspecialchars($attendance['event_type_label'] ?? 'Service') ?></td>
                                                <td><?= htmlspecialchars($attendance['service_description'] ?? 'N/A') ?></td>
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

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i data-feather="clipboard" class="me-1"></i> Follow-up History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Follow-up Type</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($followUpHistory)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No follow-up records found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($followUpHistory as $followUp): ?>
                                            <tr>
                                                <td><?= date('M d, Y', strtotime($followUp['created_at'])) ?></td>
                                                <td><?= htmlspecialchars($followUp['follow_up_type']) ?></td>
                                                <td><?= htmlspecialchars($followUp['status']) ?></td>
                                                <td><?= htmlspecialchars($followUp['assigned_to_name']) ?></td>
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

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i data-feather="activity" class="me-1"></i> Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Activity Type</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentActivity)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No recent activity found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentActivity as $activity): ?>
                                            <tr>
                                                <td><?= date('M d, Y', strtotime($activity['activity_date'])) ?></td>
                                                <td><?= htmlspecialchars($activity['activity_type']) ?></td>
                                                <td><?= htmlspecialchars($activity['description']) ?></td>
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
    </div>
</div>
