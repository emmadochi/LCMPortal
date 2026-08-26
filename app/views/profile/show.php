<?php
use App\Utilities\AssetHelper;
?>

<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-10">
        <!-- Profile Header -->
        <div class="card metric-card mb-4 shadow-sm animate__animated animate__fadeIn">
            <div class="card-body p-0">
                <div class="profile-header bg-primary py-5 px-4 text-white rounded-top" style="background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);">
                    <div class="d-md-flex align-items-end gap-4">
                        <div class="avatar-lg bg-white p-1 rounded-circle mb-3 mb-md-0" style="width: 120px; height: 120px;">
                            <?php if (!empty($member['profile_picture'])): ?>
                                <img src="<?= AssetHelper::url($member['profile_picture']) ?>" class="w-100 h-100 rounded-circle" style="object-fit: cover;" />
                            <?php else: ?>
                                <div class="w-100 h-100 rounded-circle d-flex align-items-center justify-content-center bg-light text-primary h1 fw-bold">
                                    <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="text-white mb-1"><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></h2>
                            <p class="mb-0 opacity-75"><i class='bx bx-envelope'></i> <?= htmlspecialchars($member['email']) ?></p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <a href="<?= AssetHelper::url('profile/edit') ?>" class="btn btn-light">
                                <i class='bx bx-edit-alt'></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="row">
                        <div class="col-md-4 mb-4 mb-md-0">
                            <h6 class="text-muted text-uppercase fw-bold small mb-3">Identity & Status</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <span class="text-muted">Role:</span> 
                                    <span class="badge bg-primary-soft text-primary"><?= ucfirst($member['role']) ?></span>
                                </li>
                                <li class="mb-2">
                                    <span class="text-muted">Status:</span> 
                                    <span class="badge bg-success-soft text-success"><?= ucfirst($member['status']) ?></span>
                                </li>
                                <li class="mb-2">
                                    <span class="text-muted">Age Group:</span> 
                                    <span class="badge bg-info-soft text-info"><?= ucfirst($member['age_group'] ?? 'Not set') ?></span>
                                </li>
                                <li class="mb-2">
                                    <span class="text-muted">Phone:</span> 
                                    <span><?= htmlspecialchars($member['phone'] ?? 'Not set') ?></span>
                                </li>
                                <li class="mb-2">
                                    <span class="text-muted">Address:</span> 
                                    <span class="d-inline-block text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($member['address'] ?? '') ?>"><?= htmlspecialchars($member['address'] ?? 'Not set') ?></span>
                                </li>
                                <li class="mb-2">
                                    <span class="text-muted">Joined:</span> 
                                    <span><?= date('M d, Y', strtotime($member['created_at'])) ?></span>
                                </li>
                            </ul>

                            <hr>

                            <h6 class="text-muted text-uppercase fw-bold small mb-3">Church Pulse</h6>
                            <div class="d-flex align-items-center gap-3">
                                <div class="engagement-circle" style="--percentage: <?= $engagementScore ?>%" data-score="<?= round($engagementScore) ?>"></div>
                                <div>
                                    <div class="fw-bold">Level: Growing</div>
                                    <small class="text-muted">Keep engaging to grow!</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <h6 class="text-muted text-uppercase fw-bold small mb-3">Assigned Units & Domains</h6>
                            <div class="row">
                                <?php if (empty($units)): ?>
                                    <div class="col-12">
                                        <p class="text-muted">No units assigned yet.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($units as $unit): ?>
                                        <div class="col-sm-6 mb-3">
                                            <div class="p-3 border rounded shadow-sm bg-light">
                                                <div class="d-flex align-items-center gap-3">
                                                    <i class='bx bxs-institution text-primary h3 mb-0'></i>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($unit['name']) ?></h6>
                                                        <small class="badge bg-primary-soft text-primary"><?= ucfirst($unit['role']) ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <hr>

                            <h6 class="text-muted text-uppercase fw-bold small mb-3">Personal Growth Path (AI Insights)</h6>
                            <div class="bg-light p-3 rounded border-start border-primary border-4">
                                <?php if (!empty($aiInsights['recommendations'])): ?>
                                    <?php foreach ($aiInsights['recommendations'] as $rec): ?>
                                        <div class="d-flex gap-2 mb-2">
                                            <i class='bx bx-check-circle text-success mt-1'></i>
                                            <span><?= htmlspecialchars($rec) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="mb-0 text-muted">No insights available yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
