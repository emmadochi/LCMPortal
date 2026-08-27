<?php
use App\Core\Session;
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$session = Session::getInstance();
$userRole = $session->get('user_role');
$csrfToken = Security::generateCSRFToken();
?>
<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="avatar-lg mx-auto mb-4">
                        <div class="avatar-title bg-primary-subtle text-primary rounded-circle font-size-24">
                            <i data-feather="users" class="icon-lg"></i>
                        </div>
                    </div>
                    <h5 class="mb-1"><?= htmlspecialchars($unit['name']) ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($unit['description'] ?: 'No description') ?></p>
                    <div class="d-flex gap-2 justify-content-center mb-3">
                        <?php if ($unit['status'] === 'active'): ?>
                            <span class="badge bg-success fs-6">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary fs-6">Inactive</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($canManage ?? false): ?>
                        <div class="mt-4">
                            <a href="<?= AssetHelper::url('units/' . $unit['id'] . '/edit') ?>" class="btn btn-primary btn-sm">
                                <i data-feather="edit" class="me-1"></i> Edit Unit
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <hr class="my-4">

                <div class="text-start">
                    <h5 class="font-size-15">Statistics</h5>
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">Members :</th>
                                    <td><span class="badge bg-primary"><?= $statistics['members_count'] ?></span></td>
                                </tr>
                                <tr>
                                    <th scope="row">Directors :</th>
                                    <td><span class="badge bg-info"><?= $statistics['directors_count'] ?></span></td>
                                </tr>
                                <tr>
                                    <th scope="row">Reports :</th>
                                    <td><span class="badge bg-success"><?= $statistics['reports_count'] ?></span></td>
                                </tr>
                                <tr>
                                    <th scope="row">Attendance :</th>
                                    <td><span class="badge bg-warning"><?= $statistics['attendance_count'] ?></span></td>
                                </tr>
                                <tr>
                                    <th scope="row">Created :</th>
                                    <td><?= date('F d, Y', strtotime($unit['created_at'])) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i data-feather="user-check" class="me-1"></i> Directors</h5>
                        <?php if ($canManage ?? false): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignDirectorModal">
                                <i data-feather="plus" class="icon-sm"></i> Assign
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (empty($directors)): ?>
                            <p class="text-muted mb-0">No directors assigned</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle mb-0">
                                    <tbody>
                                        <?php foreach ($directors as $director): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?= AssetHelper::image('users/avatar-1.jpg') ?>" 
                                                             alt="" class="avatar-sm rounded-circle me-2">
                                                        <div>
                                                            <h5 class="font-size-14 mb-1">
                                                                <?= htmlspecialchars($director['first_name'] . ' ' . $director['last_name']) ?>
                                                            </h5>
                                                            <p class="text-muted mb-0 font-size-12">
                                                                <?= htmlspecialchars($director['email']) ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted"><?= date('M d, Y', strtotime($director['assigned_at'])) ?></small>
                                                </td>
                                                <?php if ($canManage ?? false): ?>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-danger remove-director" 
                                                            data-user-id="<?= $director['id'] ?>"
                                                            data-unit-id="<?= $unit['id'] ?>">
                                                        <i data-feather="x" class="icon-sm"></i>
                                                    </button>
                                                </td>
                                                <?php endif; ?>
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
                        <h5 class="card-title mb-0"><i data-feather="users" class="me-1"></i> Members</h5>
                        <?php if ($canManage ?? false): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignMemberModal">
                                <i data-feather="plus" class="icon-sm"></i> Assign
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (empty($members)): ?>
                            <p class="text-muted mb-0">No members assigned</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle mb-0">
                                    <tbody>
                                        <?php foreach ($members as $member): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?= AssetHelper::image('users/avatar-1.jpg') ?>" 
                                                             alt="" class="avatar-sm rounded-circle me-2">
                                                        <div>
                                                            <h5 class="font-size-14 mb-1">
                                                                <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                                                            </h5>
                                                            <p class="text-muted mb-0 font-size-12">
                                                                <span class="badge bg-secondary"><?= htmlspecialchars($member['role']) ?></span>
                                                                <?= htmlspecialchars($member['email']) ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted"><?= date('M d, Y', strtotime($member['joined_at'])) ?></small>
                                                </td>
                                                <?php if ($canManage ?? false): ?>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-danger remove-member" 
                                                            data-user-id="<?= $member['id'] ?>"
                                                            data-unit-id="<?= $unit['id'] ?>"
                                                            title="Remove from unit">
                                                        <i data-feather="x" class="icon-sm"></i>
                                                    </button>
                                                </td>
                                                <?php endif; ?>
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
    </div>
</div>

<!-- Assign Director Modal -->
<div class="modal fade" id="assignDirectorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Director</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignDirectorForm">
                <div class="modal-body">
                    <input type="hidden" name="unit_id" value="<?= $unit['id'] ?>">
                    <div class="mb-3">
                        <label for="director_user_id" class="form-label">Select User</label>
                        <select class="form-select" id="director_user_id" name="user_id" required>
                            <option value="">Choose a user...</option>
                            <?php foreach ($allUsers as $user): ?>
                                <option value="<?= $user['id'] ?>">
                                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['email'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Director</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Member Modal -->
<div class="modal fade" id="assignMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignMemberForm">
                <div class="modal-body">
                    <input type="hidden" name="unit_id" value="<?= $unit['id'] ?>">
                    <div class="mb-3">
                        <label for="member_user_id" class="form-label">Select User</label>
                        <select class="form-select" id="member_user_id" name="user_id" required>
                            <option value="">Choose a user...</option>
                            <?php foreach ($allUsers as $user): ?>
                                <option value="<?= $user['id'] ?>">
                                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['email'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="member_role" class="form-label">Role</label>
                        <select class="form-select" id="member_role" name="role">
                            <option value="member">Member</option>
                            <option value="officer">Officer</option>
                            <option value="secretary">Secretary</option>
                            <option value="treasurer">Treasurer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$urlAssignDirector = json_encode(AssetHelper::url('units/assign-director'));
$urlAssignMember   = json_encode(AssetHelper::url('units/assign-member'));
$urlRemoveDirector = json_encode(AssetHelper::url('units/remove-director'));
$urlRemoveMember   = json_encode(AssetHelper::url('units/remove-member'));
$pageJs = '<script>
$(document).ready(function() {
    $("#assignDirectorForm").on("submit", function(e) {
        e.preventDefault();
        $.ajax({ url: ' . $urlAssignDirector . ', method: "POST", data: $(this).serialize(),
            success: function(r) { if (r.success) location.reload(); else alert(r.message || "Failed to assign director"); },
            error: function() { alert("An error occurred"); }
        });
    });
    $("#assignMemberForm").on("submit", function(e) {
        e.preventDefault();
        $.ajax({ url: ' . $urlAssignMember . ', method: "POST", data: $(this).serialize(),
            success: function(r) { if (r.success) location.reload(); else alert(r.message || "Failed to assign member"); },
            error: function() { alert("An error occurred"); }
        });
    });
    $(".remove-director").on("click", function() {
        if (!confirm("Remove this director?")) return;
        $.ajax({ url: ' . $urlRemoveDirector . ', method: "POST", data: { unit_id: $(this).data("unit-id"), user_id: $(this).data("user-id") },
            success: function(r) { if (r.success) location.reload(); else alert(r.message || "Failed to remove"); },
            error: function() { alert("An error occurred"); }
        });
    });
    $(".remove-member").on("click", function() {
        if (!confirm("Remove this member?")) return;
        $.ajax({ url: ' . $urlRemoveMember . ', method: "POST", data: { unit_id: $(this).data("unit-id"), user_id: $(this).data("user-id") },
            success: function(r) { if (r.success) location.reload(); else alert(r.message || "Failed to remove"); },
            error: function() { alert("An error occurred"); }
        });
    });
});
</script>';
?>
