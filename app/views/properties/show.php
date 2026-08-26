<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
use App\Models\Property;

$statusOptions = $statusOptions ?? Property::getStatusOptions();
$logs = $logs ?? [];
$property = $property ?? [];
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= htmlspecialchars($property['name'] ?? 'Property') ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('properties') ?>">Properties</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($property['name'] ?? '') ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <?php if (!empty($property['image_path'])): ?>
                    <img src="<?= AssetHelper::baseUrl($property['image_path']) ?>" 
                         class="img-fluid rounded mb-3" 
                         alt="<?= htmlspecialchars($property['name']) ?>">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center bg-light rounded mb-3" 
                         style="height: 300px;">
                        <i data-feather="package" class="icon-lg text-muted"></i>
                    </div>
                <?php endif; ?>

                <div class="text-center mb-3">
                    <h5 class="mb-1"><?= htmlspecialchars($property['name']) ?></h5>
                    <p class="text-muted mb-2">
                        <span class="badge bg-info"><?= htmlspecialchars($property['category_name']) ?></span>
                    </p>
                    <?php
                    $statusClass = [
                        'available' => 'bg-success',
                        'in_use' => 'bg-primary',
                        'maintenance' => 'bg-warning',
                        'damaged' => 'bg-danger',
                        'disposed' => 'bg-secondary',
                        'lost' => 'bg-dark'
                    ];
                    $statusLabel = $statusOptions[$property['status']] ?? $property['status'];
                    $class = $statusClass[$property['status']] ?? 'bg-secondary';
                    ?>
                    <span class="badge <?= $class ?> fs-6"><?= htmlspecialchars($statusLabel) ?></span>
                </div>

                <div class="d-flex gap-2 justify-content-center mb-3">
                    <?php if (!empty($canEditDetails)): ?>
                        <a href="<?= AssetHelper::url('properties/' . $property['id'] . '/edit') ?>" class="btn btn-primary btn-sm">
                            <i data-feather="edit" class="me-1"></i> Edit
                        </a>
                    <?php endif; ?>
                </div>

                <hr class="my-4">

                <div class="text-start">
                    <h5 class="font-size-15 mb-3">Property Information</h5>
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 40%;">Category:</th>
                                    <td><?= htmlspecialchars($property['category_name']) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Status:</th>
                                    <td>
                                        <span class="badge <?= $class ?>"><?= htmlspecialchars($statusLabel) ?></span>
                                    </td>
                                </tr>
                                <?php if (!empty($property['church_name'])): ?>
                                    <tr>
                                        <th scope="row">Church:</th>
                                        <td><?= htmlspecialchars($property['church_name']) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($property['location']): ?>
                                    <tr>
                                        <th scope="row">Location:</th>
                                        <td><?= htmlspecialchars($property['location']) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($property['serial_number']): ?>
                                    <tr>
                                        <th scope="row">Serial Number:</th>
                                        <td><?= htmlspecialchars($property['serial_number']) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($property['purchase_date']): ?>
                                    <tr>
                                        <th scope="row">Purchase Date:</th>
                                        <td><?= date('M d, Y', strtotime($property['purchase_date'])) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($property['purchase_cost']): ?>
                                    <tr>
                                        <th scope="row">Purchase Cost:</th>
                                        <td>₦<?= number_format((float)$property['purchase_cost'], 2) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <th scope="row">Created:</th>
                                    <td><?= date('M d, Y', strtotime($property['created_at'])) ?></td>
                                </tr>
                                <?php if ($property['creator_first_name']): ?>
                                    <tr>
                                        <th scope="row">Created By:</th>
                                        <td><?= htmlspecialchars($property['creator_first_name'] . ' ' . $property['creator_last_name']) ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($property['description']): ?>
                    <hr class="my-4">
                    <div class="text-start">
                        <h5 class="font-size-15 mb-2">Description</h5>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($property['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($property['notes']): ?>
                    <hr class="my-4">
                    <div class="text-start">
                        <h5 class="font-size-15 mb-2">Notes</h5>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($property['notes'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <?php if (!empty($canManageMovement)): ?>
        <!-- Status Update Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="refresh-cw" class="me-1"></i> Update Status
                </h5>
            </div>
            <div class="card-body">
                <form id="status-update-form">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">New Status <span class="text-danger">*</span></label>
                                <?php
                                if (empty($statusOptions)) {
                                    $statusOptions = Property::getStatusOptions();
                                }
                                $currentStatus = $property['status'] ?? 'available';
                                ?>
                                <input type="hidden" id="status" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
                                <div class="btn-group d-flex flex-wrap" role="group" aria-label="Status options">
                                    <?php foreach ($statusOptions as $key => $label): ?>
                                        <?php
                                            $isActive = $currentStatus === $key;
                                            $btnClass = $isActive ? 'btn-primary' : 'btn-outline-primary';
                                        ?>
                                        <button 
                                            type="button" 
                                            class="btn <?= $btnClass ?> btn-sm m-1 flex-fill status-option-btn" 
                                            data-status-option="<?= htmlspecialchars($key) ?>">
                                            <?= htmlspecialchars($label) ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status_notes" class="form-label">Notes (optional)</label>
                                <input type="text" class="form-control" id="status_notes" name="notes"
                                       placeholder="Reason for status change">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save" class="me-1"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($canAssign)): ?>
        <!-- Assignment Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="user-check" class="me-1"></i> Assign to User
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= AssetHelper::url('properties/' . $property['id'] . '/assign') ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="assigned_to_user_id" class="form-label">Assigned User</label>
                            <select class="form-select" id="assigned_to_user_id" name="assigned_to_user_id">
                                <option value="">Unassigned</option>
                                <?php foreach (($assignableUsers ?? []) as $user): ?>
                                    <option value="<?= $user['id'] ?>"
                                        <?= ($property['assigned_to_user_id'] ?? null) == $user['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['last_name'] . ', ' . $user['first_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="assign_notes" class="form-label">Notes (optional)</label>
                            <input type="text" class="form-control" id="assign_notes" name="notes"
                                   placeholder="Reason for assignment change">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-primary mt-3">
                        <i data-feather="save" class="me-1"></i> Save Assignment
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Activity Log -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="activity" class="me-1"></i> Activity Log
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($logs)): ?>
                    <p class="text-muted text-center py-3">No activity recorded yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>User</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?= date('M d, Y H:i', strtotime($log['created_at'])) ?></td>
                                        <td>
                                            <?php
                                            $actionLabels = [
                                                'created' => 'Created',
                                                'updated' => 'Updated',
                                                'status_change' => 'Status Changed'
                                            ];
                                            echo htmlspecialchars($actionLabels[$log['action']] ?? ucfirst($log['action']));
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($log['user_first_name']): ?>
                                                <?= htmlspecialchars($log['user_first_name'] . ' ' . $log['user_last_name']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">System</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($log['action'] === 'status_change'): ?>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($log['old_status'] ?? '-') ?></span>
                                                <i data-feather="arrow-right" class="icon-xs mx-1"></i>
                                                <span class="badge bg-primary"><?= htmlspecialchars($log['new_status'] ?? '-') ?></span>
                                            <?php endif; ?>
                                            <?php if ($log['notes']): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($log['notes']) ?></small>
                                            <?php endif; ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status button group handling
    var statusField = document.getElementById('status');
    var statusButtons = document.querySelectorAll('.status-option-btn');
    if (statusField && statusButtons.length) {
        statusButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var newStatus = this.getAttribute('data-status-option');
                if (!newStatus) return;

                // Update hidden field
                statusField.value = newStatus;

                // Toggle active styles
                statusButtons.forEach(function(b) {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-primary');
                });
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
            });
        });
    }

    var statusForm = document.getElementById('status-update-form');
    if (statusForm) {
        statusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var propertyId = <?= $property['id'] ?? 0 ?>;
            
            fetch("<?= AssetHelper::url('properties/' . ($property['id'] ?? 0) . '/status') ?>", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message || 'Failed to update status.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    }
});
</script>
