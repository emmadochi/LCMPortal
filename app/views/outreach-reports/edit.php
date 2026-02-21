<?php
use App\Utilities\AssetHelper;
$r = $report;
$publicity = $publicity ?? [];
$logistics = $logistics ?? [];
$costs = $costs ?? [];
$challenges = $challenges ?? [];
$targets = $targets ?? [];
?>
<div class="row">
    <div class="col-12">
        <form method="post" action="<?= AssetHelper::url('outreach-reports/' . $r['id']) ?>" id="outreach-report-form">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="_method" value="PUT">

            <div class="card mb-3">
                <div class="card-header"><h5 class="card-title mb-0">Program / Event</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required minlength="3" maxlength="255" value="<?= htmlspecialchars($r['title']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <?php foreach ($statuses as $val => $label): ?>
                                        <option value="<?= htmlspecialchars($val) ?>" <?= $r['status'] === $val ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($r['description'] ?? '') ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="program_date" class="form-label">Program date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="program_date" name="program_date" required value="<?= htmlspecialchars($r['program_date']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($r['end_date'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="event_id" class="form-label">Link to event</label>
                                <select class="form-select" id="event_id" name="event_id">
                                    <option value="">— None —</option>
                                    <?php foreach ($events as $ev): ?>
                                        <option value="<?= $ev['id'] ?>" <?= (int)($r['event_id'] ?? 0) === (int)$ev['id'] ? 'selected' : '' ?>><?= htmlspecialchars($ev['title']) ?> (<?= date('M j, Y', strtotime($ev['start_date'])) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="church_id" class="form-label">Church</label>
                                <select class="form-select" id="church_id" name="church_id">
                                    <option value="">— None —</option>
                                    <?php foreach ($churches as $ch): ?>
                                        <option value="<?= $ch['id'] ?>" <?= (int)($r['church_id'] ?? 0) === (int)$ch['id'] ? 'selected' : '' ?>><?= htmlspecialchars($ch['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="unit_id" class="form-label">Unit</label>
                                <select class="form-select" id="unit_id" name="unit_id">
                                    <option value="">— None —</option>
                                    <?php foreach ($units as $u): ?>
                                        <option value="<?= $u['id'] ?>" <?= (int)($r['unit_id'] ?? 0) === (int)$u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h5 class="card-title mb-0">Attendance &amp; budget summary</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="total_attendance" class="form-label">Total attendance</label>
                                <input type="number" class="form-control" id="total_attendance" name="total_attendance" min="0" value="<?= $r['total_attendance'] !== null ? (int)$r['total_attendance'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="first_timers_count" class="form-label">First-timers</label>
                                <input type="number" class="form-control" id="first_timers_count" name="first_timers_count" min="0" value="<?= $r['first_timers_count'] !== null ? (int)$r['first_timers_count'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="budget_total" class="form-label">Budget total</label>
                                <input type="number" class="form-control" id="budget_total" name="budget_total" step="0.01" min="0" value="<?= $r['budget_total'] !== null ? (float)$r['budget_total'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="actual_total" class="form-label">Actual total</label>
                                <input type="number" class="form-control" id="actual_total" name="actual_total" step="0.01" min="0" value="<?= $r['actual_total'] !== null ? (float)$r['actual_total'] : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Publicity</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-publicity"><i data-feather="plus" class="icon-sm"></i> Add</button>
                </div>
                <div class="card-body">
                    <div id="publicity-rows">
                        <?php foreach ($publicity as $i => $p): ?>
                        <div class="row publicity-row mb-2">
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="publicity[<?= $i ?>][channel]" placeholder="Channel" value="<?= htmlspecialchars($p['channel'] ?? '') ?>" /></div>
                            <div class="col-md-3"><input type="text" class="form-control form-control-sm" name="publicity[<?= $i ?>][details]" placeholder="Details" value="<?= htmlspecialchars($p['details'] ?? '') ?>" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="publicity[<?= $i ?>][estimated_reach]" min="0" placeholder="Reach" value="<?= $p['estimated_reach'] !== null ? (int)$p['estimated_reach'] : '' ?>" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="publicity[<?= $i ?>][cost]" step="0.01" min="0" placeholder="Cost" value="<?= $p['cost'] !== null ? (float)$p['cost'] : '' ?>" /></div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($publicity)): ?>
                        <div class="row publicity-row mb-2">
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="publicity[0][channel]" placeholder="Channel" /></div>
                            <div class="col-md-3"><input type="text" class="form-control form-control-sm" name="publicity[0][details]" placeholder="Details" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="publicity[0][estimated_reach]" min="0" placeholder="Reach" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="publicity[0][cost]" step="0.01" min="0" placeholder="Cost" /></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Logistics</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-logistics"><i data-feather="plus" class="icon-sm"></i> Add</button>
                </div>
                <div class="card-body">
                    <div id="logistics-rows">
                        <?php foreach ($logistics as $i => $l): ?>
                        <div class="row logistics-row mb-2">
                            <div class="col-md-2">
                                <select class="form-select form-select-sm" name="logistics[<?= $i ?>][category]">
                                    <option value="venue" <?= ($l['category'] ?? '') === 'venue' ? 'selected' : '' ?>>Venue</option>
                                    <option value="setup" <?= ($l['category'] ?? '') === 'setup' ? 'selected' : '' ?>>Setup</option>
                                    <option value="materials" <?= ($l['category'] ?? '') === 'materials' ? 'selected' : '' ?>>Materials</option>
                                    <option value="transport" <?= ($l['category'] ?? '') === 'transport' ? 'selected' : '' ?>>Transport</option>
                                    <option value="other" <?= ($l['category'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-5"><input type="text" class="form-control form-control-sm" name="logistics[<?= $i ?>][description]" placeholder="Description" value="<?= htmlspecialchars($l['description'] ?? '') ?>" /></div>
                            <div class="col-md-5"><input type="text" class="form-control form-control-sm" name="logistics[<?= $i ?>][notes]" placeholder="Notes" value="<?= htmlspecialchars($l['notes'] ?? '') ?>" /></div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($logistics)): ?>
                        <div class="row logistics-row mb-2">
                            <div class="col-md-2"><select class="form-select form-select-sm" name="logistics[0][category]"><option value="venue">Venue</option><option value="setup">Setup</option><option value="materials">Materials</option><option value="transport">Transport</option><option value="other">Other</option></select></div>
                            <div class="col-md-5"><input type="text" class="form-control form-control-sm" name="logistics[0][description]" placeholder="Description" /></div>
                            <div class="col-md-5"><input type="text" class="form-control form-control-sm" name="logistics[0][notes]" placeholder="Notes" /></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Cost breakdown</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-costs"><i data-feather="plus" class="icon-sm"></i> Add</button>
                </div>
                <div class="card-body">
                    <div id="costs-rows">
                        <?php foreach ($costs as $i => $c): ?>
                        <div class="row costs-row mb-2">
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="costs[<?= $i ?>][category]" placeholder="Category" value="<?= htmlspecialchars($c['category'] ?? '') ?>" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="costs[<?= $i ?>][budgeted_amount]" step="0.01" min="0" value="<?= $c['budgeted_amount'] !== null ? (float)$c['budgeted_amount'] : '' ?>" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="costs[<?= $i ?>][actual_amount]" step="0.01" min="0" value="<?= $c['actual_amount'] !== null ? (float)$c['actual_amount'] : '' ?>" /></div>
                            <div class="col-md-6"><input type="text" class="form-control form-control-sm" name="costs[<?= $i ?>][notes]" placeholder="Notes" value="<?= htmlspecialchars($c['notes'] ?? '') ?>" /></div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($costs)): ?>
                        <div class="row costs-row mb-2">
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="costs[0][category]" placeholder="Category" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="costs[0][budgeted_amount]" step="0.01" min="0" placeholder="Budgeted" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="costs[0][actual_amount]" step="0.01" min="0" placeholder="Actual" /></div>
                            <div class="col-md-6"><input type="text" class="form-control form-control-sm" name="costs[0][notes]" placeholder="Notes" /></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Challenges</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-challenges"><i data-feather="plus" class="icon-sm"></i> Add</button>
                </div>
                <div class="card-body">
                    <div id="challenges-rows">
                        <?php foreach ($challenges as $i => $ch): ?>
                        <div class="row challenges-row mb-2">
                            <div class="col-md-5"><input type="text" class="form-control form-control-sm" name="challenges[<?= $i ?>][description]" placeholder="Description" value="<?= htmlspecialchars($ch['description'] ?? '') ?>" /></div>
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="challenges[<?= $i ?>][category]" placeholder="Category" value="<?= htmlspecialchars($ch['category'] ?? '') ?>" /></div>
                            <div class="col-md-2">
                                <select class="form-select form-select-sm" name="challenges[<?= $i ?>][severity]">
                                    <option value="">—</option>
                                    <option value="low" <?= ($ch['severity'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                                    <option value="medium" <?= ($ch['severity'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                                    <option value="high" <?= ($ch['severity'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                                </select>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($challenges)): ?>
                        <div class="row challenges-row mb-2">
                            <div class="col-md-5"><input type="text" class="form-control form-control-sm" name="challenges[0][description]" placeholder="Description" /></div>
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="challenges[0][category]" placeholder="Category" /></div>
                            <div class="col-md-2"><select class="form-select form-select-sm" name="challenges[0][severity]"><option value="">—</option><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Targets vs actuals</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-targets"><i data-feather="plus" class="icon-sm"></i> Add</button>
                </div>
                <div class="card-body">
                    <div id="targets-rows">
                        <?php foreach ($targets as $i => $t): ?>
                        <div class="row targets-row mb-2">
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="targets[<?= $i ?>][target_name]" placeholder="Metric" value="<?= htmlspecialchars($t['target_name'] ?? '') ?>" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="targets[<?= $i ?>][target_value]" step="0.01" value="<?= (float)($t['target_value'] ?? 0) ?>" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="targets[<?= $i ?>][actual_value]" step="0.01" value="<?= $t['actual_value'] !== null ? (float)$t['actual_value'] : '' ?>" /></div>
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="targets[<?= $i ?>][unit]" placeholder="Unit" value="<?= htmlspecialchars($t['unit'] ?? '') ?>" /></div>
                            <div class="col-md-4"><input type="text" class="form-control form-control-sm" name="targets[<?= $i ?>][notes]" placeholder="Notes" value="<?= htmlspecialchars($t['notes'] ?? '') ?>" /></div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($targets)): ?>
                        <div class="row targets-row mb-2">
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="targets[0][target_name]" placeholder="Metric" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="targets[0][target_value]" step="0.01" placeholder="Target" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="targets[0][actual_value]" step="0.01" placeholder="Actual" /></div>
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="targets[0][unit]" placeholder="Unit" /></div>
                            <div class="col-md-4"><input type="text" class="form-control form-control-sm" name="targets[0][notes]" placeholder="Notes" /></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <a href="<?= AssetHelper::url('outreach-reports/' . $r['id']) ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i data-feather="check" class="me-1"></i> Update report</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    function cloneRow(containerId, rowClass, index) {
        var container = document.getElementById(containerId);
        if (!container) return;
        var row = container.querySelector('.' + rowClass);
        if (!row) return;
        var clone = row.cloneNode(true);
        clone.querySelectorAll('[name]').forEach(function(inp) {
            inp.name = inp.name.replace(/\[\d+\]/, '[' + index + ']');
            if (inp.type === 'text' || inp.type === 'number' || inp.tagName === 'SELECT') inp.value = '';
        });
        container.appendChild(clone);
    }
    function nextIndex(containerId, rowClass) {
        var container = document.getElementById(containerId);
        var rows = container ? container.querySelectorAll('.' + rowClass) : [];
        return rows.length;
    }
    document.getElementById('add-publicity')?.addEventListener('click', function() { cloneRow('publicity-rows', 'publicity-row', nextIndex('publicity-rows', 'publicity-row')); });
    document.getElementById('add-logistics')?.addEventListener('click', function() { cloneRow('logistics-rows', 'logistics-row', nextIndex('logistics-rows', 'logistics-row')); });
    document.getElementById('add-costs')?.addEventListener('click', function() { cloneRow('costs-rows', 'costs-row', nextIndex('costs-rows', 'costs-row')); });
    document.getElementById('add-challenges')?.addEventListener('click', function() { cloneRow('challenges-rows', 'challenges-row', nextIndex('challenges-rows', 'challenges-row')); });
    document.getElementById('add-targets')?.addEventListener('click', function() { cloneRow('targets-rows', 'targets-row', nextIndex('targets-rows', 'targets-row')); });
})();
</script>
