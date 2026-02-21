<?php
use App\Utilities\AssetHelper;

$churches = $churches ?? [];
$units = $units ?? [];
$eventTypes = $eventTypes ?? [];
$members = $members ?? [];
$existingMarks = $existingMarks ?? [];
$unit_id = (int)($unit_id ?? 0);
$event_date = $event_date ?? date('Y-m-d');
$event_type = $event_type ?? '';
$church_id = (int)($church_id ?? 0);
$churchFilter = $churchFilter ?? null;
$markUrl = AssetHelper::url('attendance/mark');
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Mark Attendance (Roll-call)</h4>
                <p class="card-title-desc mb-0 mt-1">Select service type, date and unit, then mark each member present or absent.</p>
            </div>
            <div class="card-body">
                <?php if (!empty($churchFilter)): ?>
                    <div class="alert alert-info mb-3">
                        <i class="bx bx-church me-2"></i>Recording for church: <strong><?= htmlspecialchars($churchFilter['name']) ?></strong>. Only units of this church are listed.
                    </div>
                <?php endif; ?>

                <?php if (empty($units)): ?>
                    <div class="alert alert-warning">
                        <?php if ($church_id): ?>
                            No units assigned to this church. <a href="<?= AssetHelper::url('churches/' . $church_id) ?>">Assign units to the church</a> first.
                        <?php else: ?>
                            No active units found. <a href="<?= AssetHelper::url('units/create') ?>">Create a unit</a> or activate units from the Units section.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form method="GET" action="<?= $markUrl ?>" id="load-form" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <?php if (!empty($churches)): ?>
                        <div class="col-md-3">
                            <label for="church_id_select" class="form-label">Church</label>
                            <select class="form-select" id="church_id_select" name="church_id">
                                <option value="">All units (no church)</option>
                                <?php foreach ($churches as $c): ?>
                                    <option value="<?= (int)$c['id'] ?>" <?= $church_id === (int)$c['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Select a church to get &quot;All church (main service)&quot; in Unit.</small>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-3">
                            <label for="unit_id" class="form-label">Unit / scope <span class="text-danger">*</span></label>
                            <select class="form-select" id="unit_id" name="unit_id" required <?= empty($units) ? 'disabled' : '' ?>>
                                <option value="">Select unit or scope...</option>
                                <?php foreach ($units as $u): ?>
                                    <option value="<?= (int)$u['id'] ?>" <?= $unit_id === (int)$u['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($u['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="event_type" class="form-label">Service type <span class="text-danger">*</span></label>
                            <select class="form-select" id="event_type" name="event_type" required>
                                <option value="">Select...</option>
                                <?php foreach ($eventTypes as $value => $label): ?>
                                    <option value="<?= htmlspecialchars($value) ?>" <?= $event_type === $value ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="event_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="event_date" name="event_date" value="<?= htmlspecialchars($event_date) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-list-ul me-1"></i> Load members
                            </button>
                        </div>
                    </div>
                </form>

                <?php if ($event_date && $event_type && !empty($members)): ?>
                    <?php $eventTypeLabel = $eventTypes[$event_type] ?? ucfirst(str_replace('_', ' ', $event_type)); ?>
                    <hr class="my-4">
                    <form method="POST" action="<?= AssetHelper::url('attendance/mark') ?>" id="submit-form">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <input type="hidden" name="unit_id" value="<?= $unit_id ?>">
                        <input type="hidden" name="event_date" value="<?= htmlspecialchars($event_date) ?>">
                        <input type="hidden" name="event_type" value="<?= htmlspecialchars($event_type) ?>">
                        <?php if ($church_id): ?>
                            <input type="hidden" name="church_id" value="<?= $church_id ?>">
                        <?php endif; ?>

                        <h5 class="mb-2"><?= htmlspecialchars($eventTypeLabel) ?> — <?= date('M j, Y', strtotime($event_date)) ?></h5>
                        <div class="mb-3">
                            <label for="service_description" class="form-label small text-muted">Short description <span class="text-muted">(optional)</span></label>
                            <input type="text" class="form-control form-control-sm" id="service_description" name="service_description" 
                                   maxlength="255" placeholder="e.g. First service, Easter Sunday"
                                   value="<?= htmlspecialchars($service_description ?? '') ?>" style="max-width: 360px;">
                        </div>
                        <p class="text-muted small mb-3">Click <strong>Present</strong> or <strong>Absent</strong> for each member, then submit.</p>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Member</th>
                                        <th>Email</th>
                                        <th class="text-center">Age group</th>
                                        <th class="text-center" style="width: 220px;">Status</th>
                                        <th class="text-center" style="width: 140px;">First time at church?</th>
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
                                            <td><?= htmlspecialchars(trim($member['first_name'] . ' ' . $member['last_name'])) ?></td>
                                            <td><?= htmlspecialchars($member['email'] ?? '') ?></td>
                                            <td class="text-center"><span class="badge bg-secondary"><?= htmlspecialchars($ageGroupLabel) ?></span></td>
                                            <td class="text-center">
                                                <input type="hidden" name="marks[<?= $uid ?>]" id="mark_<?= $uid ?>" value="<?= htmlspecialchars($current) ?>">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-success mark-btn <?= $current === 'present' ? 'active' : '' ?>" data-user="<?= $uid ?>" data-status="present">
                                                        Present
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger mark-btn <?= $current === 'absent' ? 'active' : '' ?>" data-user="<?= $uid ?>" data-status="absent">
                                                        Absent
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input first-timer-cb" name="first_timer[<?= $uid ?>]" value="1" id="ft_<?= $uid ?>" <?= $isFirstTimer ? 'checked' : '' ?>>
                                                <label class="form-check-label small text-muted ms-1" for="ft_<?= $uid ?>">First timer</label>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="<?= AssetHelper::url('attendance') ?><?= $church_id ? '?church_id=' . $church_id : '' ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-check-circle me-1"></i> Submit attendance
                            </button>
                        </div>
                    </form>
                <?php elseif ($event_date && $event_type && empty($members)): ?>
                    <div class="alert alert-warning mt-3">
                        <?php if (isset($isChurchWide) && $isChurchWide): ?>
                            <i class="bx bx-user-x me-2"></i>No members found for this church. <a href="<?= AssetHelper::url('churches/' . $church_id) ?>">Assign units and members to the church</a> first.
                        <?php else: ?>
                            <i class="bx bx-user-x me-2"></i>No members found for this unit. <a href="<?= AssetHelper::url('units/' . $unit_id) ?>">Assign members to the unit</a> first.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.mark-btn.active { font-weight: 600; }
.mark-btn[data-status="present"].active { background-color: var(--bs-success); color: #fff; border-color: var(--bs-success); }
.mark-btn[data-status="absent"].active { background-color: var(--bs-danger); color: #fff; border-color: var(--bs-danger); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var churchSelect = document.getElementById('church_id_select');
    var markUrl = <?= json_encode($markUrl) ?>;
    if (churchSelect) {
        churchSelect.addEventListener('change', function() {
            var churchId = this.value;
            var params = new URLSearchParams(window.location.search);
            if (churchId) params.set('church_id', churchId); else params.delete('church_id');
            params.delete('unit_id');
            params.delete('event_date');
            params.delete('event_type');
            window.location = markUrl + (params.toString() ? '?' + params.toString() : '');
        });
    }
    document.querySelectorAll('.mark-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var user = this.getAttribute('data-user');
            var status = this.getAttribute('data-status');
            var row = this.closest('tr');
            row.querySelectorAll('.mark-btn').forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            document.getElementById('mark_' + user).value = status;
        });
    });
});
</script>
