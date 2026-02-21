<?php
use App\Utilities\AssetHelper;
use App\Utilities\Helper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Password Reset Requests</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Password Reset Requests</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php if ($this->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($this->session->getFlash('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($this->session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($this->session->getFlash('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Pending Requests</h4>
                <p class="card-title-desc">Review and manage password reset requests from users.</p>
            </div>
            <div class="card-body">
                <?php if (empty($requests)): ?>
                    <div class="text-center py-5">
                        <i class="mdi mdi-account-key-outline text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3">No pending requests</h5>
                        <p class="text-muted">There are currently no password reset requests awaiting approval.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Requested At</th>
                                    <th>Expires In</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td>
                                        <h5 class="font-size-14 mb-1">
                                            <?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?>
                                        </h5>
                                        <p class="text-muted mb-0">User ID: <?= $request['user_id'] ?></p>
                                    </td>
                                    <td>
                                        <i class="mdi mdi-email-outline me-1"></i>
                                        <?= htmlspecialchars($request['user_email']) ?>
                                    </td>
                                    <td>
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        <?= Helper::formatDateTime($request['created_at']) ?>
                                    </td>
                                    <td>
                                        <?php
                                        $expiresAt = strtotime($request['expires_at']);
                                        $now = time();
                                        $diff = $expiresAt - $now;
                                        $hours = floor($diff / 3600);
                                        $minutes = floor(($diff % 3600) / 60);
                                        ?>
                                        <?php if ($diff > 0): ?>
                                            <span class="badge bg-warning">
                                                <?= $hours ?>h <?= $minutes ?>m
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Expired</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-success btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#approveModal<?= $request['id'] ?>">
                                                <i class="mdi mdi-check me-1"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm ms-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#rejectModal<?= $request['id'] ?>">
                                                <i class="mdi mdi-close me-1"></i> Reject
                                            </button>
                                        </div>
                                        
                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveModal<?= $request['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Approve Password Reset</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to approve the password reset request for:</p>
                                                        <p><strong><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></strong></p>
                                                        <p><strong>Email:</strong> <?= htmlspecialchars($request['user_email']) ?></p>
                                                        <div class="alert alert-info">
                                                            <p class="mb-0 small">
                                                                <i class="mdi mdi-information-outline me-1"></i>
                                                                When approved, the user will receive an email with a password reset link.
                                                                The link will be valid for 24 hours from the time of approval.
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form method="POST" action="<?= AssetHelper::url("admin/password-reset-requests/{$request['id']}/approve") ?>" class="d-inline">
                                                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="mdi mdi-check me-1"></i> Approve
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal<?= $request['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Password Reset</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="<?= AssetHelper::url("admin/password-reset-requests/{$request['id']}/reject") ?>">
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to reject the password reset request for:</p>
                                                            <p><strong><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></strong></p>
                                                            <p><strong>Email:</strong> <?= htmlspecialchars($request['user_email']) ?></p>
                                                            
                                                            <div class="alert alert-warning">
                                                                <p class="mb-2 small">
                                                                    <i class="mdi mdi-information-outline me-1"></i>
                                                                    The user will be notified by email about the rejection.
                                                                </p>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Reason (Optional)</label>
                                                                <textarea class="form-control" name="reason" rows="3" 
                                                                          placeholder="Enter reason for rejection..."></textarea>
                                                                <div class="form-text">This reason will be sent to the user.</div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="mdi mdi-close me-1"></i> Reject
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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