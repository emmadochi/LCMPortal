<!-- Sub-header with Assignment Selector -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #5b73e8 0%, #4430e7 100%);">
            <div class="card-body p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-white mb-1 fw-bold">Unit Workspace: <?= htmlspecialchars($unitName) ?></h3>
                        <p class="text-white-50 mb-0 font-size-14"><i class="bx bx-church me-1"></i> Branch: <?= htmlspecialchars($churchName) ?></p>
                    </div>
                    <?php if (count($assignments) > 1): ?>
                    <div class="col-md-4 mt-3 mt-md-0">
                        <div class="d-flex justify-content-md-end align-items-center">
                            <label class="text-white-50 me-2 text-nowrap font-size-13 mb-0">Switch Workspace:</label>
                            <select name="switch_assignment" class="form-select form-select-sm bg-white text-dark border-0 shadow-sm" style="width: auto; min-width: 180px;" onchange="window.location.href = window.location.pathname + '?church_id=' + this.value.split('-')[0] + '&unit_id=' + this.value.split('-')[1];">
                                <?php foreach ($assignments as $assign): ?>
                                    <option value="<?= $assign['church_id'] ?>-<?= $assign['unit_id'] ?>" <?= ((int)$assign['church_id'] === (int)$churchId && (int)$assign['unit_id'] === (int)$unitId) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($assign['unit_name'] . ' (' . $assign['church_name'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Roster Card -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                <h5 class="mb-0 fw-bold"><i class="bx bx-group me-2 text-primary"></i>Unit Roster</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignMemberModal">
                    <i class="bx bx-plus me-1"></i>Assign Member
                </button>
            </div>
            <div class="card-body px-4 pb-4">
                <?php if (empty($members)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bx bx-group display-2"></i>
                        <h5 class="mt-3">No Members Assigned</h5>
                        <p>There are no members assigned to this department unit yet.</p>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignMemberModal">
                            Assign First Member
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role within Unit</th>
                                    <th>Joined Unit</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3">
                                                    <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <h5 class="font-size-14 mb-0 fw-semibold">
                                                        <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                                                    </h5>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($member['email']) ?></td>
                                        <td>
                                            <span class="badge bg-soft-info text-info font-size-12">
                                                <?= htmlspecialchars(ucfirst($member['unit_role'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-muted"><?= date('M j, Y', strtotime($member['joined_at'])) ?></td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-member-btn" data-user-id="<?= $member['id'] ?>" data-name="<?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>">
                                                <i class="bx bx-trash me-1"></i>Remove
                                            </button>
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

<!-- Assign Member Modal -->
<div class="modal fade" id="assignMemberModal" tabindex="-1" aria-labelledby="assignMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="assignMemberModalLabel">Assign Member to Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignMemberForm">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="church_id" value="<?= $churchId ?>">
                <input type="hidden" name="unit_id" value="<?= $unitId ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Select Church Member <span class="text-danger">*</span></label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">Choose a member...</option>
                            <?php foreach ($candidates as $cand): ?>
                                <option value="<?= $cand['id'] ?>">
                                    <?= htmlspecialchars($cand['last_name'] . ', ' . $cand['first_name'] . ' (' . $cand['email'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Unit Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="member">Member</option>
                            <option value="officer">Officer</option>
                            <option value="secretary">Secretary</option>
                            <option value="treasurer">Treasurer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Assign Member via AJAX
    $('#assignMemberForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= \App\Utilities\AssetHelper::url("my-unit/members/assign") ?>',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Failed to assign member.');
                }
            },
            error: function(xhr) {
                const res = xhr.responseJSON;
                alert((res && res.message) ? res.message : 'An error occurred.');
            }
        });
    });

    // Remove Member via AJAX
    $('.remove-member-btn').on('click', function() {
        const userId = $(this).data('user-id');
        const name = $(this).data('name');
        
        if (!confirm('Are you sure you want to remove ' + name + ' from this unit?')) {
            return;
        }

        $.ajax({
            url: '<?= \App\Utilities\AssetHelper::url("my-unit/members/remove") ?>',
            method: 'POST',
            data: {
                _token: '<?= $csrf_token ?>',
                church_id: '<?= $churchId ?>',
                unit_id: '<?= $unitId ?>',
                user_id: userId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Failed to remove member.');
                }
            },
            error: function(xhr) {
                const res = xhr.responseJSON;
                alert((res && res.message) ? res.message : 'An error occurred.');
            }
        });
    });
});
</script>
