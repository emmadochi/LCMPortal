<?php
use App\Utilities\AssetHelper;
$report = $report ?? null;
?>
<div class="row">
    <div class="col-12">
        <form method="post" action="<?= AssetHelper::url('outreach-reports') ?>" id="outreach-report-form">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Program / Event</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required minlength="3" maxlength="255" placeholder="e.g. Community Outreach March 2025" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <?php foreach ($statuses as $val => $label): ?>
                                        <option value="<?= htmlspecialchars($val) ?>" <?= ($_POST['status'] ?? 'draft') === $val ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief about the program..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="program_date" class="form-label">Program date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="program_date" name="program_date" required value="<?= htmlspecialchars($_POST['program_date'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="event_id" class="form-label">Link to event (optional)</label>
                                <select class="form-select" id="event_id" name="event_id">
                                    <option value="">— None —</option>
                                    <?php foreach ($events as $ev): ?>
                                        <option value="<?= $ev['id'] ?>" <?= (int)($_POST['event_id'] ?? 0) === (int)$ev['id'] ? 'selected' : '' ?>><?= htmlspecialchars($ev['title']) ?> (<?= date('M j, Y', strtotime($ev['start_date'])) ?>)</option>
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
                                        <option value="<?= $ch['id'] ?>" <?= (int)($_POST['church_id'] ?? 0) === (int)$ch['id'] ? 'selected' : '' ?>><?= htmlspecialchars($ch['name']) ?></option>
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
                                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Attendance &amp; budget summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="total_attendance" class="form-label">Total attendance</label>
                                <input type="number" class="form-control" id="total_attendance" name="total_attendance" min="0" placeholder="0" value="<?= htmlspecialchars($_POST['total_attendance'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="first_timers_count" class="form-label">First-timers</label>
                                <input type="number" class="form-control" id="first_timers_count" name="first_timers_count" min="0" placeholder="0" value="<?= htmlspecialchars($_POST['first_timers_count'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="budget_total" class="form-label">Budget total</label>
                                <input type="number" class="form-control" id="budget_total" name="budget_total" step="0.01" min="0" placeholder="0.00" value="<?= htmlspecialchars($_POST['budget_total'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="actual_total" class="form-label">Actual total</label>
                                <input type="number" class="form-control" id="actual_total" name="actual_total" step="0.01" min="0" placeholder="0.00" value="<?= htmlspecialchars($_POST['actual_total'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Publicity -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Publicity</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-publicity"><i data-feather="plus" class="icon-sm"></i> Add</button>
                </div>
                <div class="card-body">
                    <div id="publicity-rows">
                        <div class="row publicity-row mb-2">
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="publicity[0][channel]" placeholder="Channel (e.g. Social media)" /></div>
                            <div class="col-md-3"><input type="text" class="form-control form-control-sm" name="publicity[0][details]" placeholder="Details" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="publicity[0][estimated_reach]" min="0" placeholder="Est. reach" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="publicity[0][cost]" step="0.01" min="0" placeholder="Cost" /></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logistics -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Logistics</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-logistics"><i data-feather="plus" class="icon-sm"></i> Add</button>
                </div>
                <div class="card-body">
                    <div id="logistics-rows">
                        <div class="row logistics-row mb-2">
                            <div class="col-md-2">
                                <select class="form-select form-select-sm" name="logistics[0][category]">
                                    <option value="venue">Venue</option>
                                    <option value="setup">Setup</option>
                                    <option value="materials">Materials</option>
                                    <option value="transport">Transport</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-5"><input type="text" class="form-control form-control-sm" name="logistics[0][description]" placeholder="Description" /></div>
                            <div class="col-md-5"><input type="text" class="form-control form-control-sm" name="logistics[0][notes]" placeholder="Notes" /></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost line items -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Cost breakdown</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-costs"><i data-feather="plus" class="icon-sm"></i> Add</button>
                </div>
                <div class="card-body">
                    <div id="costs-rows">
                        <div class="row costs-row mb-2">
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="costs[0][category]" placeholder="Category" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="costs[0][budgeted_amount]" step="0.01" min="0" placeholder="Budgeted" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="costs[0][actual_amount]" step="0.01" min="0" placeholder="Actual" /></div>
                            <div class="col-md-6"><input type="text" class="form-control form-control-sm" name="costs[0][notes]" placeholder="Notes" /></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Challenges -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Challenges</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-challenges"><i data-feather="plus" class="icon-sm"></i> Add</button>
                </div>
                <div class="card-body">
                    <div id="challenges-rows">
                        <div class="row challenges-row mb-2">
                            <div class="col-md-5"><input type="text" class="form-control form-control-sm" name="challenges[0][description]" placeholder="Description" /></div>
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="challenges[0][category]" placeholder="Category" /></div>
                            <div class="col-md-2">
                                <select class="form-select form-select-sm" name="challenges[0][severity]">
                                    <option value="">—</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Targets vs actuals -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Targets vs actuals</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-targets"><i data-feather="plus" class="icon-sm"></i> Add</button>
                </div>
                <div class="card-body">
                    <div id="targets-rows">
                        <div class="row targets-row mb-2">
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="targets[0][target_name]" placeholder="Metric (e.g. Attendance)" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="targets[0][target_value]" step="0.01" placeholder="Target" /></div>
                            <div class="col-md-2"><input type="number" class="form-control form-control-sm" name="targets[0][actual_value]" step="0.01" placeholder="Actual" /></div>
                            <div class="col-md-2"><input type="text" class="form-control form-control-sm" name="targets[0][unit]" placeholder="Unit (e.g. people)" /></div>
                            <div class="col-md-4"><input type="text" class="form-control form-control-sm" name="targets[0][notes]" placeholder="Notes" /></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <a href="<?= AssetHelper::url('outreach-reports') ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i data-feather="check" class="me-1"></i> Save report</button>
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
    document.getElementById('add-publicity')?.addEventListener('click', function() {
        cloneRow('publicity-rows', 'publicity-row', nextIndex('publicity-rows', 'publicity-row'));
    });
    document.getElementById('add-logistics')?.addEventListener('click', function() {
        cloneRow('logistics-rows', 'logistics-row', nextIndex('logistics-rows', 'logistics-row'));
    });
    document.getElementById('add-costs')?.addEventListener('click', function() {
        cloneRow('costs-rows', 'costs-row', nextIndex('costs-rows', 'costs-row'));
    });
    document.getElementById('add-challenges')?.addEventListener('click', function() {
        cloneRow('challenges-rows', 'challenges-row', nextIndex('challenges-rows', 'challenges-row'));
    });
    document.getElementById('add-targets')?.addEventListener('click', function() {
        cloneRow('targets-rows', 'targets-row', nextIndex('targets-rows', 'targets-row'));
    });
})();
</script>
