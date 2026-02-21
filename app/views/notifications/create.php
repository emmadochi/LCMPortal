<?php
use App\Utilities\AssetHelper;

$post = $post ?? [];
$roles = $roles ?? [];
$ctx = $senderContext ?? [];
if (empty($post['audience_type']) && !empty($ctx)) {
    if ($ctx['isAdmin'] ?? false) {
        $post['audience_type'] = 'all';
    } elseif (($ctx['isHeadPastor'] ?? false) && ($ctx['churchId'] ?? 0) > 0) {
        $post['audience_type'] = 'church_members';
    } elseif (!empty($ctx['directorUnits'] ?? [])) {
        $post['audience_type'] = 'unit_members';
    } else {
        $post['audience_type'] = 'all';
    }
}
$isAdmin = $ctx['isAdmin'] ?? false;
$isHeadPastor = $ctx['isHeadPastor'] ?? false;
$isDirector = $ctx['isDirector'] ?? false;
$churchId = (int) ($ctx['churchId'] ?? 0);
$churchName = $ctx['churchName'] ?? 'Your church';
$directorUnits = $ctx['directorUnits'] ?? [];
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Send Notification</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('notifications/show') ?>">Notifications</a></li>
                    <li class="breadcrumb-item active">Send Notification</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Broadcast Notification</h4>
                <p class="card-title-desc mb-0">Send a notification to all users or to specific roles. Recipients will receive it in-app and/or by email based on your choice.</p>
            </div>
            <div class="card-body">
                <form action="<?= AssetHelper::url('notifications/send') ?>" method="POST" id="notification-form" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required
                               value="<?= htmlspecialchars($post['title'] ?? '') ?>"
                               placeholder="e.g. Important announcement">
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="5" required
                                  placeholder="Enter the notification content..."><?= htmlspecialchars($post['message'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="link" class="form-label">Link (optional)</label>
                        <input type="url" class="form-control" id="link" name="link"
                               value="<?= htmlspecialchars($post['link'] ?? '') ?>"
                               placeholder="https://...">
                        <div class="form-text">If provided, recipients can click through from the notification.</div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Attach image (optional)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                        <div class="form-text">JPG, PNG, GIF or WebP. Max 2 MB. Shown in-app and in email.</div>
                        <div id="image-preview-container" class="mt-3" style="display: none;">
                            <div class="border rounded p-2 bg-light d-inline-block">
                                <p class="small text-muted mb-2">Preview (this is what will be sent):</p>
                                <img id="image-preview" src="" alt="Preview" class="rounded d-block" style="max-height: 200px; max-width: 100%; object-fit: contain;">
                                <button type="button" id="image-remove-btn" class="btn btn-sm btn-outline-danger mt-2">
                                    <i data-feather="x" class="icon-sm"></i> Remove image
                                </button>
                            </div>
                        </div>
                        <div id="image-size-warning" class="alert alert-warning mt-2 py-2" style="display: none;">Image exceeds 2 MB and may fail to upload.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Audience <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-3 flex-column">
                            <?php if ($isAdmin): ?>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="audience_type" id="audience_all" value="all"
                                        <?= ($post['audience_type'] ?? 'all') === 'all' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="audience_all">All active users</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="audience_type" id="audience_roles" value="roles"
                                        <?= ($post['audience_type'] ?? '') === 'roles' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="audience_roles">By global role</label>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if ($isHeadPastor && $churchId > 0): ?>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="audience_type" id="audience_church_members" value="church_members"
                                        <?= in_array($post['audience_type'] ?? 'church_members', ['church_members',''], true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="audience_church_members">All church members (<?= htmlspecialchars($churchName) ?>)</label>
                                </div>
                                <input type="hidden" name="audience_church_id" value="<?= $churchId ?>">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="audience_type" id="audience_church_unit_heads" value="church_unit_heads"
                                        <?= ($post['audience_type'] ?? '') === 'church_unit_heads' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="audience_church_unit_heads">Unit heads (directors)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="audience_type" id="audience_church_officers" value="church_by_unit_role"
                                        <?= ($post['audience_type'] ?? '') === 'church_by_unit_role' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="audience_church_officers">Officers / Leaders</label>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if ($isDirector && !empty($directorUnits)): ?>
                            <div class="d-flex flex-wrap gap-4 align-items-start">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="audience_type" id="audience_unit_members" value="unit_members"
                                        <?= in_array($post['audience_type'] ?? 'unit_members', ['unit_members',''], true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="audience_unit_members">Unit members</label>
                                </div>
                                <div id="unit-members-container" class="ps-3 border-start border-2 border-secondary" style="<?= ($post['audience_type'] ?? '') === 'unit_members' ? '' : 'display:none;' ?>">
                                    <p class="text-muted small mb-2">Select unit(s):</p>
                                    <?php foreach ($directorUnits as $u): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="audience_unit_ids[]" id="unit_<?= (int)$u['id'] ?>" value="<?= (int)$u['id'] ?>"
                                                <?= in_array((int)$u['id'], $post['audience_unit_ids'] ?? [], true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="unit_<?= (int)$u['id'] ?>"><?= htmlspecialchars($u['name']) ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($isAdmin): ?>
                        <div id="roles-container" class="mt-3 ps-3 border-start border-2 border-secondary" style="<?= ($post['audience_type'] ?? 'all') === 'roles' ? '' : 'display:none;' ?>">
                            <p class="text-muted small mb-2">Select one or more global roles:</p>
                            <?php foreach ($roles as $role): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="roles[]" id="role_<?= htmlspecialchars($role) ?>" value="<?= htmlspecialchars($role) ?>"
                                        <?= in_array($role, $post['roles'] ?? [], true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="role_<?= htmlspecialchars($role) ?>"><?= ucfirst($role) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($isHeadPastor): ?>
                        <div id="officers-unit-roles" class="mt-3 ps-3 border-start border-2 border-secondary" style="<?= ($post['audience_type'] ?? '') === 'church_by_unit_role' ? '' : 'display:none;' ?>">
                            <input type="hidden" name="audience_unit_roles[]" value="officer">
                            <input type="hidden" name="audience_unit_roles[]" value="secretary">
                            <input type="hidden" name="audience_unit_roles[]" value="treasurer">
                            <p class="text-muted small mb-0">Members with unit role: officer, secretary, or treasurer.</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="channels" class="form-label">Delivery channels <span class="text-danger">*</span></label>
                        <select class="form-select" id="channels" name="channels" style="max-width: 280px;">
                            <option value="both" <?= ($post['channels'] ?? 'both') === 'both' ? 'selected' : '' ?>>In-app and email</option>
                            <option value="in_app" <?= ($post['channels'] ?? '') === 'in_app' ? 'selected' : '' ?>>In-app only</option>
                            <option value="email" <?= ($post['channels'] ?? '') === 'email' ? 'selected' : '' ?>>Email only</option>
                        </select>
                        <div class="form-text">Recipients will see the notification when logged in and/or receive it by email.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="send" class="me-1 icon-sm"></i> Send notification
                        </button>
                        <a href="<?= AssetHelper::url('notifications/show') ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var audienceAll = document.getElementById('audience_all');
    var audienceRoles = document.getElementById('audience_roles');
    var rolesContainer = document.getElementById('roles-container');
    var audienceChurchOfficers = document.getElementById('audience_church_officers');
    var officersUnitRoles = document.getElementById('officers-unit-roles');
    var audienceUnitMembers = document.getElementById('audience_unit_members');
    var unitMembersContainer = document.getElementById('unit-members-container');

    function toggleRoles() {
        if (rolesContainer) rolesContainer.style.display = (audienceRoles && audienceRoles.checked) ? 'block' : 'none';
    }
    function toggleOfficersRoles() {
        if (officersUnitRoles) officersUnitRoles.style.display = (audienceChurchOfficers && audienceChurchOfficers.checked) ? 'block' : 'none';
    }
    function toggleUnitMembers() {
        if (unitMembersContainer) unitMembersContainer.style.display = (audienceUnitMembers && audienceUnitMembers.checked) ? 'block' : 'none';
    }
    if (audienceAll) audienceAll.addEventListener('change', toggleRoles);
    if (audienceRoles) audienceRoles.addEventListener('change', toggleRoles);
    if (audienceChurchOfficers) audienceChurchOfficers.addEventListener('change', toggleOfficersRoles);
    if (audienceUnitMembers) audienceUnitMembers.addEventListener('change', toggleUnitMembers);

    toggleRoles();
    toggleOfficersRoles();
    toggleUnitMembers();

    // Image preview
    var imageInput = document.getElementById('image');
    var previewContainer = document.getElementById('image-preview-container');
    var previewImg = document.getElementById('image-preview');
    var sizeWarning = document.getElementById('image-size-warning');
    var removeBtn = document.getElementById('image-remove-btn');

    imageInput.addEventListener('change', function() {
        var file = this.files[0];
        if (!file) {
            previewContainer.style.display = 'none';
            sizeWarning.style.display = 'none';
            return;
        }
        if (!file.type.match(/^image\/(jpeg|png|gif|webp)$/)) {
            return;
        }
        if (file.size > 2097152) {
            sizeWarning.style.display = 'block';
        } else {
            sizeWarning.style.display = 'none';
        }
        var reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
            if (typeof feather !== 'undefined') feather.replace();
        };
        reader.readAsDataURL(file);
    });

    removeBtn.addEventListener('click', function() {
        imageInput.value = '';
        previewImg.src = '';
        previewContainer.style.display = 'none';
        sizeWarning.style.display = 'none';
        if (typeof feather !== 'undefined') feather.replace();
    });

    document.getElementById('notification-form').addEventListener('submit', function() {
        if (audienceRoles && audienceRoles.checked && rolesContainer) {
            var checked = document.querySelectorAll('#roles-container input[name="roles[]"]:checked');
            if (checked.length === 0) {
                alert('Please select at least one role when targeting by role.');
                return false;
            }
        }
        if (audienceUnitMembers && audienceUnitMembers.checked && unitMembersContainer) {
            var unitChecked = document.querySelectorAll('#unit-members-container input[name="audience_unit_ids[]"]:checked');
            if (unitChecked.length === 0) {
                alert('Please select at least one unit.');
                return false;
            }
        }
        return true;
    });
});
</script>
