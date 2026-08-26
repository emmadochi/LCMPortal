<?php
use App\Utilities\AssetHelper;
?>

<div class="container-fluid p-0">
    <div class="p-4">
        <!-- Header -->
        <div class="card shadow-sm mb-4 border-0 overflow-hidden">
            <div class="card-body bg-dark text-white py-4 position-relative">
                <div class="position-absolute bottom-0 end-0 opacity-25 p-3">
                    <i class="bx bxs-bell-ring display-1 text-white"></i>
                </div>
                <div class="d-flex align-items-center justify-content-between position-relative">
                    <div>
                        <h4 class="mb-0 fw-bold"><i class="bx bx-broadcast me-2"></i> <?= htmlspecialchars($churchName) ?></h4>
                        <p class="mb-0 opacity-75">Notification & Broadcast Center</p>
                    </div>
                    <?php if ($canSend): ?>
                    <div class="text-end">
                        <a href="<?= AssetHelper::url('notifications/' . $churchId . '/create') ?>" class="btn btn-primary btn-lg shadow-sm">
                            <i class="bx bx-plus-circle me-1"></i> Send New Notification
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm overflow-hidden glass-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3 bg-soft-primary p-3 rounded-circle text-primary">
                                <i class="bx bx-paper-plane fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-muted text-uppercase fs-12 mb-1 fw-bold">Total Broadcasts</h6>
                                <h4 class="mb-0 fw-bold"><?= number_format($stats['total_sent']) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm overflow-hidden glass-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3 bg-soft-success p-3 rounded-circle text-success">
                                <i class="bx bx-group fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-muted text-uppercase fs-12 mb-1 fw-bold">Total Recipients</h6>
                                <h4 class="mb-0 fw-bold"><?= number_format($stats['recipients']) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm overflow-hidden glass-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3 bg-soft-info p-3 rounded-circle text-info">
                                <i class="bx bx-mobile-alt fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-muted text-uppercase fs-12 mb-1 fw-bold">In-App Messages</h6>
                                <h4 class="mb-0 fw-bold"><?= number_format($stats['in_app']) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm overflow-hidden glass-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3 bg-soft-warning p-3 rounded-circle text-warning">
                                <i class="bx bx-envelope fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-muted text-uppercase fs-12 mb-1 fw-bold">Email Notifications</h6>
                                <h4 class="mb-0 fw-bold"><?= number_format($stats['email']) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Broadcast History -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-history me-2 text-primary"></i> Broadcast History</h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary"><i class="bx bx-filter me-1"></i> Filter</button>
                    <a href="<?= AssetHelper::url('notifications/' . $churchId . '/export') ?>" class="btn btn-sm btn-outline-primary"><i class="bx bx-download me-1"></i> Export Log</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 15%">Date & Time</th>
                                <th style="width: 25%">Title</th>
                                <th style="width: 15%">Audience</th>
                                <th style="width: 15%">Channels</th>
                                <th style="width: 10%">Recipients</th>
                                <th class="text-end pe-4" style="width: 20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($broadcasts)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="opacity-50">
                                        <i class="bx bx-bell-off display-3 mb-3"></i>
                                        <p class="fs-16">No broadcasts sent yet for this church.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($broadcasts as $b): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold"><?= date('M d, Y', strtotime($b['created_at'])) ?></div>
                                        <small class="text-muted"><?= date('H:i', strtotime($b['created_at'])) ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold truncate-text"><?= htmlspecialchars($b['title']) ?></div>
                                        <small class="text-muted truncate-text"><?= htmlspecialchars(substr($b['message'], 0, 50)) ?>...</small>
                                    </td>
                                    <td>
                                        <?php 
                                        $badgeClass = match($b['audience_type']) {
                                            'all' => 'bg-soft-primary text-primary',
                                            'roles' => 'bg-soft-info text-info',
                                            'church_members' => 'bg-soft-success text-success',
                                            default => 'bg-soft-secondary text-secondary'
                                        };
                                        ?>
                                        <span class="badge rounded-pill <?= $badgeClass ?>">
                                            <?= ucfirst(str_replace('_', ' ', $b['audience_type'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if ($b['channels'] === 'in_app' || $b['channels'] === 'both'): ?>
                                                <i class="bx bx-mobile-alt text-info" data-bs-toggle="tooltip" title="In-App"></i>
                                            <?php endif; ?>
                                            <?php if ($b['channels'] === 'email' || $b['channels'] === 'both'): ?>
                                                <i class="bx bx-envelope text-warning" data-bs-toggle="tooltip" title="Email"></i>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold"><?= number_format($b['recipient_count']) ?></span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="<?= AssetHelper::url('notifications/' . $churchId . '/' . $b['id']) ?>" class="btn btn-sm btn-light rounded-pill px-3 shadow-none">
                                                <i class="bx bx-show me-1"></i> Details
                                            </a>
                                            <button type="button" class="btn btn-sm btn-soft-danger rounded-circle p-1" onclick="confirmDelete(<?= $b['id'] ?>)">
                                                <i class="bx bx-trash p-1"></i>
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

<style>
    .bg-soft-primary { background-color: rgba(85, 110, 230, 0.1); }
    .bg-soft-success { background-color: rgba(52, 195, 143, 0.1); }
    .bg-soft-info { background-color: rgba(80, 165, 241, 0.1); }
    .bg-soft-warning { background-color: rgba(241, 180, 76, 0.1); }
    .bg-soft-danger { background-color: rgba(244, 106, 106, 0.1); }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .truncate-text {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .datatable thead th {
        border-top: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This broadcast record will be removed from the history. Actual notifications sent cannot be unsent.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f46a6a',
            cancelButtonColor: '#74788d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit delete form or ajax call
                console.log('Deleting broadcast ' + id);
            }
        })
    }
</script>
