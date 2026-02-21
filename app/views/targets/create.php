<?php
use App\Utilities\AssetHelper;
use App\Core\Session;
$session = Session::getInstance();
$old = $session->getFlash('old') ?? [];
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Set Church or Unit Target</h4>
                <p class="card-title-desc mb-0">Define a goal for a church (church-wide) or for a specific unit within a church</p>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= AssetHelper::url('targets') ?>" id="target-form">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="church_id" class="form-label">Church <span class="text-danger">*</span></label>
                                <select class="form-select" id="church_id" name="church_id" required>
                                    <option value="">Select church...</option>
                                    <?php foreach ($churches as $c): ?>
                                        <option value="<?= (int)$c['id'] ?>" <?= (isset($old['church_id']) && (int)$old['church_id'] === (int)$c['id']) || $preselectedChurchId === (string)$c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unit_id" class="form-label">Scope</label>
                                <select class="form-select" id="unit_id" name="unit_id">
                                    <option value="">Church-wide</option>
                                    <?php
                                    $preselected = $preselectedChurchId ? (int)$preselectedChurchId : (isset($old['church_id']) ? (int)$old['church_id'] : 0);
                                    if ($preselected && !empty($churchUnits[$preselected])):
                                        foreach ($churchUnits[$preselected] as $u):
                                            $uid = (int)$u['unit_id'];
                                            $selected = isset($old['unit_id']) && (int)$old['unit_id'] === $uid;
                                    ?>
                                        <option value="<?= $uid ?>" data-church="<?= $preselected ?>" <?= $selected ? 'selected' : '' ?>><?= htmlspecialchars($u['unit_name'] ?? '') ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                                <small class="text-muted">Leave as "Church-wide" for a goal for the whole church; otherwise choose a unit assigned to the church.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="target_type" class="form-label">Target type <span class="text-danger">*</span></label>
                                <select class="form-select" id="target_type" name="target_type" required>
                                    <option value="">Select type...</option>
                                    <?php foreach ($targetTypes as $k => $v): ?>
                                        <option value="<?= htmlspecialchars($k) ?>" <?= isset($old['target_type']) && $old['target_type'] === $k ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="target_value" class="form-label">Target value <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="target_value" name="target_value" step="0.01" min="0" required
                                       value="<?= htmlspecialchars($old['target_value'] ?? '') ?>" placeholder="e.g. 500">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="unit_label" class="form-label">Unit label (optional)</label>
                                <input type="text" class="form-control" id="unit_label" name="unit_label" maxlength="50"
                                       value="<?= htmlspecialchars($old['unit_label'] ?? '') ?>" placeholder="e.g. people, NGN">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="period_type" class="form-label">Period type <span class="text-danger">*</span></label>
                                <select class="form-select" id="period_type" name="period_type" required>
                                    <option value="">Select...</option>
                                    <?php foreach ($periodTypes as $k => $v): ?>
                                        <option value="<?= htmlspecialchars($k) ?>" <?= isset($old['period_type']) && $old['period_type'] === $k ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="period_value" class="form-label">Period value <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="period_value" name="period_value" required
                                       value="<?= htmlspecialchars($old['period_value'] ?? '') ?>"
                                       placeholder="e.g. 2025-01, 2025-Q1, 2025">
                                <small class="text-muted" id="period_hint">Month: YYYY-MM, Quarter: YYYY-Q1..Q4, Year: YYYY</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Optional notes"><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save Target</button>
                        <a href="<?= AssetHelper::url('targets') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
(function() {
    var churchUnits = <?= json_encode(array_map(function($units) {
        return array_map(function($u) {
            return ['id' => (int)$u['unit_id'], 'name' => $u['unit_name'] ?? ''];
        }, $units);
    }, $churchUnits)) ?>;

    var unitSelect = document.getElementById('unit_id');
    var churchSelect = document.getElementById('church_id');

    function updateUnitOptions() {
        var churchId = churchSelect.value;
        var options = unitSelect.querySelectorAll('option');
        for (var i = options.length - 1; i >= 0; i--) {
            if (options[i].value !== '') options[i].remove();
        }
        if (!churchId) return;
        var units = churchUnits[churchId];
        if (units && units.length) {
            units.forEach(function(u) {
                var opt = document.createElement('option');
                opt.value = u.id;
                opt.textContent = u.name;
                unitSelect.appendChild(opt);
            });
        }
    }

    churchSelect.addEventListener('change', updateUnitOptions);
    updateUnitOptions();

    var periodType = document.getElementById('period_type');
    var periodValue = document.getElementById('period_value');
    var hint = document.getElementById('period_hint');
    var hints = { month: 'e.g. 2025-01', quarter: 'e.g. 2025-Q1', year: 'e.g. 2025' };
    periodType.addEventListener('change', function() {
        hint.textContent = (hints[this.value] || '') ? hints[this.value] : 'Month: YYYY-MM, Quarter: YYYY-Q1..Q4, Year: YYYY';
        if (this.value && !periodValue.value) {
            if (this.value === 'year') periodValue.placeholder = '2025';
            else if (this.value === 'quarter') periodValue.placeholder = '2025-Q1';
            else periodValue.placeholder = '2025-01';
        }
    });
})();
</script>
