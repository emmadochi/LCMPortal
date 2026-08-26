<?php
use App\Core\Session;
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$session = Session::getInstance();
$csrfToken = Security::generateCSRFToken();
$userRole = $session->get('user_role');
?>
<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <?php
                    $avatarSrc = !empty($user['profile_picture'])
                        ? AssetHelper::url($user['profile_picture'])
                        : AssetHelper::image('users/avatar-1.jpg');
                    ?>
                    <div class="position-relative d-inline-block">
                        <img id="profile-avatar" src="<?= htmlspecialchars($avatarSrc) ?>" alt="" class="avatar-lg rounded-circle img-thumbnail">
                        <button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle p-2" data-bs-toggle="modal" data-bs-target="#updateProfilePictureModal" title="Change photo">
                            <i data-feather="camera" class="icon-sm"></i>
                        </button>
                    </div>
                    <div class="mt-3">
                        <h5 class="mb-1"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                        <p class="text-muted mb-2"><?= htmlspecialchars($user['email']) ?></p>
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
                        <span class="badge bg-info fs-6"><?= ucfirst($user['role']) ?></span>
                        <?php if ($user['status'] === 'active'): ?>
                            <span class="badge bg-success fs-6">Active</span>
                        <?php elseif ($user['status'] === 'inactive'): ?>
                            <span class="badge bg-secondary fs-6">Inactive</span>
                        <?php else: ?>
                            <span class="badge bg-danger fs-6">Suspended</span>
                        <?php endif; ?>
                    </div>
                    <div class="mt-4">
                        <a href="<?= AssetHelper::url('users/' . $user['id'] . '/edit') ?>" class="btn btn-primary btn-sm">
                            <i data-feather="edit" class="me-1"></i> Edit User
                        </a>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Engagement Rating Section -->
                <?php if (isset($engagementScore) && $engagementScore !== null): ?>
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
                <?php endif; ?>

                <div class="text-start">
                    <h5 class="font-size-15">Information</h5>
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">Email :</th>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Role :</th>
                                    <td><span class="badge bg-info"><?= ucfirst($user['role']) ?></span></td>
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
                                        <?php if ($user['status'] === 'active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php elseif ($user['status'] === 'inactive'): ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Suspended</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Member Since :</th>
                                    <td><?= date('F d, Y', strtotime($user['created_at'])) ?></td>
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
        <?php if (isset($aiInsights) && !empty($aiInsights['insights'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i data-feather="zap" class="me-1"></i> AI-Powered Insights
                    </h5>
                </div>
                <div class="card-body">
                    <?php foreach ($aiInsights['insights'] as $insight): ?>
                        <div class="alert alert-<?= $insight['type'] ?> alert-dismissible fade show" role="alert">
                            <i data-feather="<?= $insight['icon'] ?>" class="me-2"></i>
                            <strong><?= htmlspecialchars($insight['title']) ?>:</strong> 
                            <?= htmlspecialchars($insight['message']) ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (!empty($aiInsights['recommendations'])): ?>
                        <div class="mt-3">
                            <h6 class="font-size-14 mb-2">
                                <i data-feather="lightbulb" class="me-1"></i> Recommendations
                            </h6>
                            <ul class="list-unstyled mb-0">
                                <?php foreach ($aiInsights['recommendations'] as $recommendation): ?>
                                    <li class="mb-2">
                                        <i data-feather="check-circle" class="me-2 text-success" style="width: 16px; height: 16px;"></i>
                                        <?= htmlspecialchars($recommendation) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i data-feather="users" class="me-1"></i> Member Of</h5>
                        <?php if ($userRole === 'admin'): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignUnitModal">
                                <i data-feather="plus" class="icon-sm"></i> Assign
                            </button>
                        <?php endif; ?>
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
                                                <?php if ($userRole === 'admin'): ?>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-danger remove-unit" 
                                                            data-unit-id="<?= $unit['id'] ?>"
                                                            data-user-id="<?= $user['id'] ?>">
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
                        <h5 class="card-title mb-0"><i data-feather="user-check" class="me-1"></i> Director Of</h5>
                        <?php if ($userRole === 'admin'): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignDirectorModal">
                                <i data-feather="plus" class="icon-sm"></i> Assign
                            </button>
                        <?php endif; ?>
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
                                                <?php if ($userRole === 'admin'): ?>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-danger remove-director-unit" 
                                                            data-unit-id="<?= $unit['id'] ?>"
                                                            data-user-id="<?= $user['id'] ?>">
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

        <?php if (!empty($showFinanceSection)): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center gap-2">
                        <h5 class="card-title mb-0"><i data-feather="dollar-sign" class="me-1"></i> Tithe &amp; Giving</h5>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            <label class="mb-0 text-muted small">Month</label>
                            <select class="form-select form-select-sm" id="finance-month" style="width: auto;">
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>" <?= (int)($financeMonth ?? date('n')) === $m ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                <?php endfor; ?>
                            </select>
                            <label class="mb-0 text-muted small">Year</label>
                            <select class="form-select form-select-sm" id="finance-year" style="width: auto;">
                                <?php
                                $currentYear = (int) date('Y');
                                for ($y = $currentYear + 1; $y >= 2020; $y--):
                                ?>
                                    <option value="<?= $y ?>" <?= (int)($financeYear ?? $currentYear) === $y ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="user-finance-tbody">
                                    <?php
                                    $records = $financeRecords ?? [];
                                    include __DIR__ . '/_finance_records_rows.php';
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Assign Unit Modal -->
<div class="modal fade" id="assignUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign to Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignUnitForm">
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <div class="mb-3">
                        <label for="unit_id" class="form-label">Select Unit</label>
                        <select class="form-select" id="unit_id" name="unit_id" required>
                            <option value="">Choose a unit...</option>
                            <?php foreach ($allUnits as $unit): ?>
                                <option value="<?= $unit['id'] ?>">
                                    <?= htmlspecialchars($unit['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="member">Member</option>
                            <option value="officer">Officer</option>
                            <option value="secretary">Secretary</option>
                            <option value="treasurer">Treasurer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Profile Picture Modal -->
<div class="modal fade" id="updateProfilePictureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="profilePictureForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Choose image</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp" required>
                        <div class="form-text">JPG, PNG, GIF or WebP. Max 2 MB.</div>
                    </div>
                    <div id="profile-picture-preview" class="text-center mt-2" style="display: none;">
                        <img id="profile-picture-preview-img" src="" alt="Preview" class="rounded-circle img-thumbnail" style="max-width: 120px; max-height: 120px; object-fit: cover;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="profilePictureSubmitBtn">
                        <span class="spinner-border spinner-border-sm me-1" role="status" style="display: none;"></span>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Director Modal -->
<div class="modal fade" id="assignDirectorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign as Director</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignDirectorForm">
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <div class="mb-3">
                        <label for="director_unit_id" class="form-label">Select Unit</label>
                        <select class="form-select" id="director_unit_id" name="unit_id" required>
                            <option value="">Choose a unit...</option>
                            <?php foreach ($allUnits as $unit): ?>
                                <option value="<?= $unit['id'] ?>">
                                    <?= htmlspecialchars($unit['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$urlAssignUnit = json_encode(AssetHelper::url('users/assign-unit'));
$urlAssignDirectorUnit = json_encode(AssetHelper::url('users/assign-director-unit'));
$urlRemoveUnit = json_encode(AssetHelper::url('users/remove-unit'));
$urlRemoveDirectorUnit = json_encode(AssetHelper::url('users/remove-director-unit'));
$financeAjaxJs = '';
if (!empty($showFinanceSection)) {
    $financeRecordsUrl = json_encode(AssetHelper::url('users/' . $user['id'] . '/finance-records'));
    $financeAjaxJs = '
    var financeRecordsUrl = ' . $financeRecordsUrl . ';
    $("#finance-month, #finance-year").on("change", function() {
        var month = $("#finance-month").val();
        var year = $("#finance-year").val();
        $.get(financeRecordsUrl, { month: month, year: year }, function(html) {
            $("#user-finance-tbody").html(html);
            if (typeof feather !== "undefined") feather.replace();
        });
    });';
}
$profilePictureUrl = json_encode(AssetHelper::url('users/' . $user['id'] . '/profile-picture'));
$pageJs = '<script>
$(document).ready(function() {
    // Profile picture preview
    $("#profile_picture").on("change", function() {
        var file = this.files[0];
        var preview = $("#profile-picture-preview");
        var previewImg = $("#profile-picture-preview-img");
        if (!file) { preview.hide(); return; }
        if (!file.type.match(/^image\//)) { preview.hide(); return; }
        var reader = new FileReader();
        reader.onload = function(e) {
            previewImg.attr("src", e.target.result);
            preview.show();
        };
        reader.readAsDataURL(file);
        if (typeof feather !== "undefined") feather.replace();
    });

    // Profile picture upload
    $("#profilePictureForm").on("submit", function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = $("#profilePictureSubmitBtn");
        var spinner = btn.find(".spinner-border-sm");
        var formData = new FormData(form[0]);
        btn.prop("disabled", true);
        spinner.show();
        $.ajax({
            url: ' . $profilePictureUrl . ',
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(r) {
                if (r.success && r.image_url) {
                    $("#profile-avatar").attr("src", r.image_url);
                    $("#updateProfilePictureModal").modal("hide");
                    form[0].reset();
                    $("#profile-picture-preview").hide();
                } else {
                    alert(r.message || "Update failed.");
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Failed to update profile picture.";
                alert(msg);
            },
            complete: function() {
                btn.prop("disabled", false);
                spinner.hide();
            }
        });
    });

    $("#assignUnitForm").on("submit", function(e) {
        e.preventDefault();
        $.ajax({ url: ' . $urlAssignUnit . ', method: "POST", data: $(this).serialize(),
            success: function(r) { if (r.success) location.reload(); else alert(r.message || "Failed to assign user"); },
            error: function() { alert("An error occurred"); }
        });
    });
    $("#assignDirectorForm").on("submit", function(e) {
        e.preventDefault();
        $.ajax({ url: ' . $urlAssignDirectorUnit . ', method: "POST", data: $(this).serialize(),
            success: function(r) { if (r.success) location.reload(); else alert(r.message || "Failed to assign director"); },
            error: function() { alert("An error occurred"); }
        });
    });
    $(".remove-unit").on("click", function() {
        if (!confirm("Remove this user from the unit?")) return;
        $.ajax({ url: ' . $urlRemoveUnit . ', method: "POST", data: { user_id: $(this).data("user-id"), unit_id: $(this).data("unit-id") },
            success: function(r) { if (r.success) location.reload(); else alert(r.message || "Failed to remove"); },
            error: function() { alert("An error occurred"); }
        });
    });
    $(".remove-director-unit").on("click", function() {
        if (!confirm("Remove this user as director?")) return;
        $.ajax({ url: ' . $urlRemoveDirectorUnit . ', method: "POST", data: { user_id: $(this).data("user-id"), unit_id: $(this).data("unit-id") },
            success: function(r) { if (r.success) location.reload(); else alert(r.message || "Failed to remove director"); },
            error: function() { alert("An error occurred"); }
        });
    });' . $financeAjaxJs . '
});
</script>';
?>
