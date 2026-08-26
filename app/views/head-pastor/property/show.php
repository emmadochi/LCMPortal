<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$property = $property ?? null;
$logs = $logs ?? [];
$users = $users ?? [];
$statusOptions = $statusOptions ?? [];
?>

<div class="row">
    <div class="col-lg-8">
        <!-- Asset Main Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-start mb-4">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <h3 class="mb-0 me-2"><?= htmlspecialchars($property['name']) ?></h3>
                            <span class="badge bg-soft-info text-info rounded-pill font-size-12 px-3"><?= htmlspecialchars($property['category_name']) ?></span>
                        </div>
                        <p class="text-muted mb-0">Asset ID: #<?= $property['id'] ?> | Registered: <?= date('M d, Y', strtotime($property['created_at'])) ?></p>
                    </div>
                    <div class="ms-3">
                        <?php 
                            $statusClass = 'bg-soft-secondary text-secondary';
                            switch($property['status']) {
                                case 'available':
                                case 'in_use': $statusClass = 'bg-soft-success text-success'; break;
                                case 'maintenance': $statusClass = 'bg-soft-warning text-warning'; break;
                                case 'damaged':
                                case 'lost':
                                case 'disposed': $statusClass = 'bg-soft-danger text-danger'; break;
                            }
                        ?>
                        <div class="text-end">
                            <span class="badge rounded-pill <?= $statusClass ?> font-size-12 px-4 py-2 mb-2 d-inline-block">
                                <?= ucfirst(str_replace('_', ' ', $property['status'])) ?>
                            </span>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                    <i class="bx bx-refresh me-1"></i> Update Status
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="asset-details mb-4">
                    <h5 class="font-size-16 mb-2">Description</h5>
                    <div class="text-muted font-size-14 mb-4">
                        <?= $property['description'] ? nl2br(htmlspecialchars($property['description'])) : '<span class="italic">No description provided.</span>' ?>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted font-size-13 mb-1">Serial Number / Tag</h6>
                            <p class="fw-medium"><?= htmlspecialchars($property['serial_number'] ?: 'Not Available') ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted font-size-13 mb-1">Current Location</h6>
                            <p class="fw-medium text-primary"><i class="bx bx-map-pin me-1"></i> <?= htmlspecialchars($property['location'] ?: 'Not Specified') ?></p>
                        </div>
                    </div>
                </div>

                <hr class="my-4 border-light">

                <div class="row text-center border-bottom border-light pb-4 mb-4">
                    <div class="col-6 border-end border-light">
                        <p class="text-muted mb-2 font-size-13">Purchase Date</p>
                        <h5 class="mb-0"><?= $property['purchase_date'] ? date('M d, Y', strtotime($property['purchase_date'])) : '--' ?></h5>
                    </div>
                    <div class="col-6">
                        <p class="text-muted mb-2 font-size-13">Acquisition Cost</p>
                        <h5 class="mb-0 text-dark">₦<?= number_format($property['purchase_cost'] ?? 0, 2) ?></h5>
                    </div>
                </div>

                <div class="internal-notes">
                    <h5 class="font-size-16 mb-2">Internal Notes</h5>
                    <div class="bg-light p-3 rounded text-muted font-size-14 border-start border-3 border-info">
                        <?= $property['notes'] ? nl2br(htmlspecialchars($property['notes'])) : 'No internal notes registered for this asset.' ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lifecycle History Table -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">Lifecycle History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Status Change</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td class="text-muted font-size-12"><?= date('M d, Y H:i', strtotime($log['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($log['user_first_name'] . ' ' . $log['user_last_name']) ?></td>
                                    <td><span class="badge border border-secondary text-secondary"><?= ucfirst($log['action']) ?></span></td>
                                    <td>
                                        <?php if ($log['old_status'] || $log['new_status']): ?>
                                            <span class="font-size-11 text-muted"><?= ucfirst($log['old_status']) ?></span>
                                            <i class="bx bx-right-arrow-alt mx-1"></i>
                                            <span class="font-size-11 fw-bold"><?= ucfirst($log['new_status']) ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="font-size-12"><?= htmlspecialchars($log['notes'] ?: '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">No history found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Assignment Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h5 class="card-title mb-4">Current Assignment</h5>
                
                <div class="d-flex align-items-center mb-4">
                    <?php if ($property['assigned_to_user_id']): ?>
                        <div class="avatar-md me-3">
                            <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-24">
                                <?= strtoupper(substr($property['assigned_first_name'], 0, 1) . substr($property['assigned_last_name'], 0, 1)) ?>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-1 font-size-15"><?= htmlspecialchars($property['assigned_first_name'] . ' ' . $property['assigned_last_name']) ?></h6>
                            <p class="text-muted mb-0 font-size-13">Person In-charge</p>
                        </div>
                    <?php else: ?>
                        <div class="avatar-md me-3">
                            <span class="avatar-title rounded-circle bg-soft-warning text-warning font-size-24">
                                <i class="bx bx-user-x"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-1 font-size-15 text-muted italic">Unassigned</h6>
                            <p class="text-muted mb-0 font-size-13">No one is currently assigned.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-grid mt-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                        <i class="bx bx-repost me-1"></i> Change Assignment
                    </button>
                </div>
            </div>
        </div>

        <!-- Management Actions -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title mb-3">Asset Controls</h5>
                <div class="d-grid gap-2">
                    <a href="<?= AssetHelper::url("churches/{$churchId}/property/{$property['id']}/edit") ?>" class="btn btn-info d-flex align-items-center justify-content-center">
                        <i class="bx bx-edit-alt me-2"></i> Edit Information
                    </a>
                    <a href="<?= AssetHelper::url("churches/{$churchId}/property/records") ?>" class="btn btn-light d-flex align-items-center justify-content-center">
                        <i class="bx bx-list-ul me-2"></i> Detailed Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Update Status -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title">Update Asset Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= AssetHelper::url("churches/{$churchId}/property/{$property['id']}/status") ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">New Status</label>
                        <select class="form-select" name="status" required>
                            <?php foreach ($statusOptions as $val => $label): ?>
                                <option value="<?= $val ?>" <?= $property['status'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Notes for history</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Explain the reason for status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Assign User -->
<div class="modal fade" id="assignUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title">Assign Person In-Charge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= AssetHelper::url("churches/{$churchId}/property/{$property['id']}/assign") ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Church Member</label>
                        <select class="form-select" name="user_id">
                            <option value="">-- Unassign Asset --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= $property['assigned_to_user_id'] == $u['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Assignment Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Reason for assignment change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>
