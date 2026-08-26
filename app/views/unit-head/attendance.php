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

<!-- Attendance Form -->
<form method="POST" action="<?= \App\Utilities\AssetHelper::url('my-unit/attendance') ?>">
    <input type="hidden" name="_token" value="<?= $csrf_token ?>">
    <input type="hidden" name="church_id" value="<?= $churchId ?>">
    <input type="hidden" name="unit_id" value="<?= $unitId ?>">

    <div class="row">
        <!-- Event Details Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-info-circle me-2 text-primary"></i>Event Details</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <label for="event_date" class="form-label">Event Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="event_date" name="event_date" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="event_type" class="form-label">Event Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="event_type" name="event_type" required>
                            <?php foreach ($eventTypes as $key => $val): ?>
                                <option value="<?= $key ?>" <?= $key === 'mid_week_service' ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="service_description" class="form-label">Title/Description</label>
                        <input type="text" class="form-control" id="service_description" name="service_description" placeholder="e.g. Weekly Choir Rehearsal">
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roster Roll-Call Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-checkbox-checked me-2 text-primary"></i>Mark Roster</h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-success me-2" onclick="toggleAll('present')">All Present</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAll('absent')">All Absent</button>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <?php if (empty($members)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bx bx-group display-3"></i>
                            <h5 class="mt-3">No Members in Roster</h5>
                            <p>Please <a href="<?= \App\Utilities\AssetHelper::url('my-unit/members') ?>">assign members</a> to this unit first.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Member Name</th>
                                        <th>Email</th>
                                        <th class="text-center">Status</th>
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
                                                    <span class="fw-semibold text-dark"><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></span>
                                                </div>
                                            </td>
                                            <td class="text-muted"><?= htmlspecialchars($member['email']) ?></td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-3">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input attendance-radio-present" type="radio" name="attendance[<?= $member['id'] ?>]" id="pres_<?= $member['id'] ?>" value="present" checked>
                                                        <label class="form-check-label text-success" for="pres_<?= $member['id'] ?>"><i class="bx bx-check-circle me-1"></i>Present</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input attendance-radio-absent" type="radio" name="attendance[<?= $member['id'] ?>]" id="abs_<?= $member['id'] ?>" value="absent">
                                                        <label class="form-check-label text-secondary" for="abs_<?= $member['id'] ?>"><i class="bx bx-x-circle me-1"></i>Absent</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bx bx-save me-1"></i>Save Attendance</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function toggleAll(status) {
    if (status === 'present') {
        $('.attendance-radio-present').prop('checked', true);
    } else {
        $('.attendance-radio-absent').prop('checked', true);
    }
}
</script>
