<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$units = $units ?? [];
$eventTypes = $eventTypes ?? [];
$members = $members ?? [];
$existingMarks = $existingMarks ?? [];
$unit_id = (int)($unit_id ?? 0);
$event_date = $event_date ?? date('Y-m-d');
$event_type = $event_type ?? '';
$churchId = $church['id'] ?? 0;
$markUrl = AssetHelper::url("churches/{$churchId}/attendance/mark");
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Mark Attendance</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url("churches/{$churchId}/attendance") ?>">Attendance</a></li>
                    <li class="breadcrumb-item active">Mark</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-transparent border-bottom">
                <h4 class="card-title mb-0">Attendance Roll-call</h4>
                <p class="card-title-desc mb-0">Select unit, service type, and date to load members.</p>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= $markUrl ?>" id="load-form" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="unit_id" class="form-label">Unit / Scope <span class="text-danger">*</span></label>
                            <select class="form-select" id="unit_id" name="unit_id" required>
                                <option value="">Select unit...</option>
                                <?php foreach ($units as $u): ?>
                                    <option value="<?= (int)$u['id'] ?>" <?= $unit_id === (int)$u['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($u['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="event_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="event_type" name="event_type" required>
                                <option value="">Select service...</option>
                                <?php foreach ($eventTypes as $value => $label): ?>
                                    <option value="<?= htmlspecialchars($value) ?>" <?= $event_type === $value ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="event_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="event_date" name="event_date" value="<?= htmlspecialchars($event_date) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-refresh me-1"></i> Load Members
                            </button>
                        </div>
                    </div>
                </form>

                <?php if ($event_date && $event_type && !empty($members)): ?>
                    <?php $eventTypeLabel = $eventTypes[$event_type] ?? ucfirst(str_replace('_', ' ', $event_type)); ?>
                    <hr class="my-4 border-light">
                    
                    <form method="POST" action="<?= $markUrl ?>" id="submit-form">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <input type="hidden" name="unit_id" value="<?= $unit_id ?>">
                        <input type="hidden" name="event_date" value="<?= htmlspecialchars($event_date) ?>">
                        <input type="hidden" name="event_type" value="<?= htmlspecialchars($event_type) ?>">

                        <div class="row mb-4 align-items-center">
                            <div class="col-sm-6">
                                <h5 class="mb-0 text-primary"><?= htmlspecialchars($eventTypeLabel) ?></h5>
                                <p class="text-muted mb-0"><?= date('l, F j, Y', strtotime($event_date)) ?></p>
                            </div>
                            <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                                <div class="d-inline-flex align-items-center">
                                    <label for="service_description" class="form-label me-2 mb-0 small">Description:</label>
                                    <input type="text" class="form-control form-control-sm" id="service_description" name="service_description" 
                                           maxlength="255" placeholder="e.g. 1st Service"
                                           value="<?= htmlspecialchars($service_description ?? '') ?>" style="width: 200px;">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle border-light">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Member Name</th>
                                        <th class="text-center">Age Group</th>
                                        <th class="text-center" style="width: 250px;">Status</th>
                                        <th class="text-center" style="width: 150px;">First Timer?</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($members as $i => $member): ?>
                                        <?php
                                        $uid = (int)$member['id'];
                                        $mark = $existingMarks[$uid] ?? null;
                                        $current = is_array($mark) ? ($mark['status'] ?? 'present') : ($mark ?: 'present');
                                        $isFirstTimer = is_array($mark) ? (int)($mark['is_first_timer'] ?? 0) : 0;
                                        $ageGroup = $member['age_group'] ?? '';
                                        $ageGroupLabel = $ageGroup ? (($ageGroups ?? [])[$ageGroup] ?? ucfirst($ageGroup)) : '—';
                                        ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td>
                                                <div class="fw-bold"><?= htmlspecialchars(trim($member['first_name'] . ' ' . $member['last_name'])) ?></div>
                                                <div class="text-muted small"><?= htmlspecialchars($member['email'] ?? '') ?></div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill bg-soft-secondary text-secondary">
                                                    <?= htmlspecialchars($ageGroupLabel) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <input type="hidden" name="marks[<?= $uid ?>]" id="mark_<?= $uid ?>" value="<?= htmlspecialchars($current) ?>">
                                                <div class="btn-group btn-group-sm w-100" role="group">
                                                    <button type="button" class="btn btn-outline-success mark-btn w-50 <?= $current === 'present' ? 'active' : '' ?>" data-user="<?= $uid ?>" data-status="present">
                                                        Present
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger mark-btn w-50 <?= $current === 'absent' ? 'active' : '' ?>" data-user="<?= $uid ?>" data-status="absent">
                                                        Absent
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline m-0">
                                                    <input type="checkbox" class="form-check-input" name="first_timer[<?= $uid ?>]" value="1" id="ft_<?= $uid ?>" <?= $isFirstTimer ? 'checked' : '' ?>>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="<?= AssetHelper::url("churches/{$churchId}/attendance") ?>" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bx bx-save me-1"></i> Save Attendance
                            </button>
                        </div>
                    </form>
                <?php elseif ($event_date && $event_type && empty($members)): ?>
                    <div class="text-center py-5">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title rounded-circle bg-soft-warning text-warning font-size-24">
                                <i class="bx bx-user-x"></i>
                            </span>
                        </div>
                        <h5>No members found</h5>
                        <p class="text-muted">No members were found for the selected scope. Please ensure members are assigned correctly.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.mark-btn.active[data-status="present"] { background-color: #34c38f !important; color: #fff !important; border-color: #34c38f !important; }
.mark-btn.active[data-status="absent"] { background-color: #f46a6a !important; color: #fff !important; border-color: #f46a6a !important; }
.table > :not(caption) > * > * { padding: 0.75rem 0.5rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.mark-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var user = this.getAttribute('data-user');
            var status = this.getAttribute('data-status');
            var container = this.closest('.btn-group');
            container.querySelectorAll('.mark-btn').forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            document.getElementById('mark_' + user).value = status;
        });
    });
});
</script>
