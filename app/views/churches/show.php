<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= htmlspecialchars($church['name']) ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('churches') ?>">Churches</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($church['name']) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"><?= htmlspecialchars($church['name']) ?></h4>
                    <div>
                        <a href="<?= AssetHelper::url("churches/{$church['id']}/report") ?>" class="btn btn-info me-2">
                            <i class="bx bx-file me-1"></i>Generate Report
                        </a>
                        <?php if ($is_admin): ?>
                            <a href="<?= AssetHelper::url("churches/{$church['id']}/edit") ?>" class="btn btn-warning me-2">
                                <i class="bx bx-edit me-1"></i>Edit Church
                            </a>
                        <?php endif; ?>
                        <a href="<?= AssetHelper::url('churches') ?>" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Churches
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h5><i class="bx bx-info-circle me-2 text-primary"></i>Basic Information</h5>
                            <hr>
                            <p><strong>Name:</strong> <?= htmlspecialchars($church['name']) ?></p>
                            <?php if ($church['description']): ?>
                                <p><strong>Description:</strong> <?= htmlspecialchars($church['description']) ?></p>
                            <?php endif; ?>
                            <?php if ($church['pastor_name']): ?>
                                <p><strong>Pastor:</strong> <?= htmlspecialchars($church['pastor_name']) ?></p>
                            <?php endif; ?>
                            <?php if ($church['head_pastor_name']): ?>
                                <p><strong>Head Pastor:</strong> <span class="badge bg-info"><?= htmlspecialchars($church['head_pastor_name']) ?></span>
                                <?php if ($is_admin): ?>
                                <form method="POST" action="<?= AssetHelper::url("churches/{$church['id']}/remove-head-pastor") ?>" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this head pastor assignment?');">
                                    <input type="hidden" name="_token" value="<?= $csrf_token; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger ms-2">Remove</button>
                                </form>
                                <?php endif; ?>
                                </p>
                            <?php else: ?>
                                <p><strong>Head Pastor:</strong> <span class="text-muted">Not assigned</span></p>
                            <?php endif; ?>
                            <?php if ($church['established_date']): ?>
                                <p><strong>Established:</strong> <?= date('F j, Y', strtotime($church['established_date'])) ?></p>
                            <?php endif; ?>
                            <?php if ($church['is_headquarters']): ?>
                                <p><span class="badge bg-primary"><i class="bx bx-home me-1"></i>Headquarters</span></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Head Pastor Assignment Form -->
                        <?php if ($is_admin && !$church['head_pastor_name']): ?>
                        <div class="mt-3 p-3 bg-light rounded">
                            <h6><i class="bx bx-user-plus me-2 text-primary"></i>Assign Head Pastor</h6>
                            <form method="POST" action="<?= AssetHelper::url("churches/{$church['id']}/assign-head-pastor") ?>">
                                <input type="hidden" name="_token" value="<?= $csrf_token; ?>">
                                <div class="mb-2">
                                    <select name="user_id" class="form-select" required>
                                        <option value="">Select a user...</option>
                                        <?php foreach ($possible_head_pastors as $user): ?>
                                            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['full_name'] . ' (' . $user['email'] . ')') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-sm btn-success">Assign Head Pastor</button>
                            </form>
                        </div>
                        <?php endif; ?>
                        </div>
                        
                        <div class="mb-4">
                            <h5><i class="bx bx-map me-2 text-primary"></i>Location</h5>
                            <hr>
                            <p><strong>Address:</strong> <?= htmlspecialchars($church['address']) ?></p>
                            <p><strong>City:</strong> <?= htmlspecialchars($church['city']) ?></p>
                            <p><strong>State:</strong> <?= htmlspecialchars($church['state']) ?></p>
                            <p><strong>Postal Code:</strong> <?= htmlspecialchars($church['postal_code']) ?></p>
                            <p><strong>Country:</strong> <?= htmlspecialchars($church['country']) ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h5><i class="bx bx-phone me-2 text-primary"></i>Contact Information</h5>
                            <hr>
                            <?php if ($church['phone']): ?>
                                <p><i class="bx bx-phone me-2"></i><?= htmlspecialchars($church['phone']) ?></p>
                            <?php endif; ?>
                            <?php if ($church['email']): ?>
                                <p><i class="bx bx-envelope me-2"></i><?= htmlspecialchars($church['email']) ?></p>
                            <?php endif; ?>
                            <?php if ($church['website']): ?>
                                <p><i class="bx bx-globe me-2"></i><a href="<?= $church['website'] ?>" target="_blank"><?= htmlspecialchars($church['website']) ?></a></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-4">
                            <h5><i class="bx bx-calendar me-2 text-primary"></i>System Information</h5>
                            <hr>
                            <?php
                            $statusClasses = [
                                'active' => 'success',
                                'inactive' => 'secondary',
                                'suspended' => 'danger'
                            ];
                            $statusLabels = [
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended'
                            ];
                            ?>
                            <p><strong>Status:</strong> <span class="badge bg-<?= $statusClasses[$church['status']] ?>"><?= $statusLabels[$church['status']] ?></span></p>
                            <p><strong>Created:</strong> <?= date('M j, Y g:i A', strtotime($church['created_at'])) ?></p>
                            <p><strong>Last Updated:</strong> <?= date('M j, Y g:i A', strtotime($church['updated_at'])) ?></p>
                            <p><strong>Created By:</strong> <?= htmlspecialchars(trim(($church['creator_first_name'] ?? '') . ' ' . ($church['creator_last_name'] ?? '')) ?: '—') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Units Section -->
        <div class="card" id="units-section">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Associated Units</h4>
                    <?php if (!empty($availableUnits)): ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignUnitModal">
                            <i class="bx bx-plus me-1"></i>Assign Unit
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($units)): ?>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Unit Name</th>
                                    <th>Unit Head</th>
                                    <th>Assigned Date</th>
                                    <th>Assigned By</th>
                                    <th>Primary</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($units as $unit): ?>
                                    <tr>
                                        <td>
                                            <h5 class="font-size-14 mb-0"><?= htmlspecialchars($unit['unit_name']) ?></h5>
                                            <?php if ($unit['unit_description']): ?>
                                                <p class="text-muted mb-0"><?= htmlspecialchars(substr($unit['unit_description'], 0, 50)) ?><?= strlen($unit['unit_description']) > 50 ? '...' : '' ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($unit['unit_head_name'])): ?>
                                                <div class="d-flex align-items-center">
                                                    <span class="fw-medium text-primary"><?= htmlspecialchars($unit['unit_head_name']) ?></span>
                                                    <button type="button" class="btn btn-sm btn-soft-primary ms-2 p-1" 
                                                            onclick="openAppointHeadModal(<?= (int)$unit['unit_id'] ?>, '<?= htmlspecialchars($unit['unit_name']) ?>', <?= (int)$unit['unit_head_user_id'] ?>)"
                                                            title="Change Unit Head">
                                                        <i class="bx bx-user-voice"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex align-items-center">
                                                    <span class="text-muted small">Not appointed</span>
                                                    <button type="button" class="btn btn-sm btn-soft-success ms-2 p-1" 
                                                            onclick="openAppointHeadModal(<?= (int)$unit['unit_id'] ?>, '<?= htmlspecialchars($unit['unit_name']) ?>')"
                                                            title="Appoint Unit Head">
                                                        <i class="bx bx-plus"></i>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($unit['assigned_date'])) ?></td>
                                        <td><?= htmlspecialchars($unit['assigner_first_name'] . ' ' . $unit['assigner_last_name']) ?></td>
                                        <td>
                                            <?php if ($unit['is_primary']): ?>
                                                <span class="badge bg-success">Primary</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Secondary</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="<?= AssetHelper::url("units/{$unit['unit_id']}") ?>" class="btn btn-sm btn-outline-primary" title="Manage Unit Leadership & Members">
                                                    <i class="bx bx-cog me-1"></i> Manage Unit
                                                </a>
                                                <form method="POST" action="<?= AssetHelper::url("churches/{$church['id']}/remove-unit") ?>" 
                                                      class="d-inline" onsubmit="return confirm('Are you sure you want to remove this unit from the church?')">
                                                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                                                    <input type="hidden" name="unit_id" value="<?= $unit['unit_id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove unit from branch">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bx bx-group text-muted" style="font-size: 2rem;"></i>
                        <h5 class="mt-2">No Units Assigned</h5>
                        <p class="text-muted">This church doesn't have any units assigned yet.</p>
                        <?php if (!empty($availableUnits)): ?>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignUnitModal">
                                <i class="bx bx-plus me-1"></i>Assign First Unit
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Targets Section (admin) -->
        <?php if (!empty($is_admin)): ?>
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"><i class="bx bx-target-lock me-2"></i>Targets</h4>
                    <a href="<?= AssetHelper::url('targets/create?church_id=' . (int)$church['id']) ?>" class="btn btn-sm btn-primary">
                        <i class="bx bx-plus me-1"></i>Set Target
                    </a>
                </div>
                <p class="card-title-desc mb-0 mt-1">Goals for this church or its units</p>
            </div>
            <div class="card-body">
                <?php if (empty($churchTargets)): ?>
                    <p class="text-muted mb-0">No targets set yet. <a href="<?= AssetHelper::url('targets/create?church_id=' . (int)$church['id']) ?>">Set a target</a> for this church or one of its units.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Scope</th>
                                    <th>Type</th>
                                    <th>Target</th>
                                    <th>Period</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($churchTargets as $t): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($t['unit_name'])): ?>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($t['unit_name']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Church-wide</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($targetTypes[$t['target_type']] ?? $t['target_type']) ?></td>
                                        <td><?= number_format((float)$t['target_value'], 2) ?><?= !empty($t['unit_label']) ? ' ' . htmlspecialchars($t['unit_label']) : '' ?></td>
                                        <td><?= htmlspecialchars($t['period_type']) ?>: <?= htmlspecialchars($t['period_value']) ?></td>
                                        <td class="text-end">
                                            <a href="<?= AssetHelper::url('targets/' . (int)$t['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">
                        <a href="<?= AssetHelper::url('targets?church_id=' . (int)$church['id']) ?>">View all targets for this church</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-4">
        <?php if (!empty($is_admin)): ?>
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bx bx-folder-open me-2"></i>Church Records (Super Admin)</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">View this church's data across all modules:</p>
                <div class="d-grid gap-2">
                    <a href="<?= AssetHelper::url('finance?church_id=' . (int)$church['id']) ?>" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bx bx-wallet me-2"></i>Finances
                    </a>
                    <a href="<?= AssetHelper::url('attendance?church_id=' . (int)$church['id']) ?>" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bx bx-calendar-check me-2"></i>Attendance
                    </a>
                    <a href="<?= AssetHelper::url('attendance/mark?church_id=' . (int)$church['id']) ?>" class="btn btn-outline-success btn-sm text-start">
                        <i class="bx bx-list-check me-2"></i>Mark attendance (roll-call)
                    </a>
                    <a href="<?= AssetHelper::url('churches/' . (int)$church['id'] . '/membership') ?>" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bx bx-pie-chart-alt me-2"></i>Membership Dashboard
                    </a>
                    <a href="<?= AssetHelper::url('targets/create?church_id=' . (int)$church['id']) ?>" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bx bx-target-lock me-2"></i>Set Target
                    </a>
                    <a href="<?= AssetHelper::url('targets?church_id=' . (int)$church['id']) ?>" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bx bx-list-ul me-2"></i>All Targets
                    </a>
                    <a href="<?= AssetHelper::url('users?church_id=' . (int)$church['id']) ?>" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bx bx-group me-2"></i>Members (Users List)
                    </a>
                    <a href="<?= AssetHelper::url('projects?church_id=' . (int)$church['id']) ?>" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bx bx-briefcase me-2"></i>Projects
                    </a>
                    <a href="<?= AssetHelper::url('reports?church_id=' . (int)$church['id']) ?>" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bx bx-file me-2"></i>Reports
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bx bx-church me-2"></i>Church Quick Stats</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Units:</span>
                    <span class="fw-bold"><?= count($units) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Primary Units:</span>
                    <span class="fw-bold"><?= count(array_filter($units, function($u) { return $u['is_primary']; })) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Available Units:</span>
                    <span class="fw-bold"><?= count($availableUnits) ?></span>
                </div>
            </div>
        </div>
        
        <?php if ($church['status'] !== 'active'): ?>
        <div class="card border-warning">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0"><i class="bx bx-alert-triangle me-2"></i>Status Notice</h5>
            </div>
            <div class="card-body">
                <p>This church is currently marked as <strong><?= strtolower($statusLabels[$church['status']]) ?></strong>.</p>
                <p class="mb-0">Only active churches can have new units assigned.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Assign Unit Modal -->
<?php if (!empty($availableUnits)): ?>
<div class="modal fade" id="assignUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Unit to Church</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= AssetHelper::url("churches/{$church['id']}/assign-unit") ?>">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="unit_id" class="form-label">Select Unit <span class="text-danger">*</span></label>
                        <select class="form-select" id="unit_id" name="unit_id" required>
                            <option value="">Choose a unit...</option>
                            <?php foreach ($availableUnits as $unit): ?>
                                <option value="<?= $unit['id'] ?>"><?= htmlspecialchars($unit['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1">
                            <label class="form-check-label" for="is_primary">
                                Mark as Primary Unit
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Optional notes about this assignment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Appoint Unit Head Modal -->
<div class="modal fade" id="appointUnitHeadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="headModalTitle">Appoint Unit Head</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= AssetHelper::url("churches/{$church['id']}/assign-unit-head") ?>">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="unit_id" id="head_unit_id">
                <div class="modal-body">
                    <p class="text-muted" id="headModalDesc">Appoint a leader for this department at this branch.</p>
                    <div class="mb-3">
                        <label for="head_user_id" class="form-label">Select Member <span class="text-danger">*</span></label>
                        <select class="form-select" id="head_user_id" name="user_id" required>
                            <option value="">Choose a user...</option>
                            <?php foreach ($possible_unit_heads as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['full_name'] . ' (' . $user['email'] . ')') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="submit" form="removeHeadForm" class="btn btn-outline-danger" id="btnRemoveHead" style="display:none;" onclick="return confirm('Are you sure you want to remove the current head for this unit?')">Remove Head</button>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Appointment</button>
                    </div>
                </div>
            </form>
            <form id="removeHeadForm" method="POST" action="<?= AssetHelper::url("churches/{$church['id']}/remove-unit-head") ?>" style="display:none;">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="unit_id" id="remove_head_unit_id">
            </form>
        </div>
    </div>
</div>

<script>
function openAppointHeadModal(unitId, unitName, currentHeadId = null) {
    document.getElementById('head_unit_id').value = unitId;
    document.getElementById('remove_head_unit_id').value = unitId;
    document.getElementById('headModalTitle').innerText = 'Appoint Head for ' + unitName;
    document.getElementById('headModalDesc').innerText = 'Select an active member to lead the ' + unitName + ' department.';
    
    const userSelect = document.getElementById('head_user_id');
    userSelect.value = currentHeadId || '';
    
    // Show/hide remove button
    document.getElementById('btnRemoveHead').style.display = currentHeadId ? 'block' : 'none';
    
    // Show modal
    var myModal = new bootstrap.Modal(document.getElementById('appointUnitHeadModal'));
    myModal.show();
}
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.75em;
    border-radius: 0.375rem;
}

.btn {
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.15s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
}

.modal-content {
    border-radius: 0.5rem;
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
}
</style>