<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$report = $report ?? null;
$units = $units ?? [];
$statuses = $statuses ?? ['draft' => 'Draft', 'submitted' => 'Submitted', 'approved' => 'Approved'];
$csrf_token = $csrf_token ?? '';

$publicity = $publicity ?? [];
$logistics = $logistics ?? [];
$costs = $costs ?? [];
$challenges = $challenges ?? [];
$targets = $targets ?? [];
?>

<div class="row">
    <div class="col-12">
        <form method="POST" action="<?= AssetHelper::url("churches/{$churchId}/outreach/{$report['id']}") ?>" id="outreach-report-form" enctype="multipart/form-data">
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title mb-0">Program Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="title" class="form-label">Program Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required minlength="3" maxlength="255" placeholder="e.g. Community Health Outreach" value="<?= htmlspecialchars($report['title'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <?php foreach ($statuses as $val => $label): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= ($report['status'] ?? 'draft') === $val ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description / Objectives</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Briefly describe the purpose and execution of this program..."><?= htmlspecialchars($report['description'] ?? '') ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="program_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="program_date" name="program_date" required value="<?= htmlspecialchars($report['program_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($report['end_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unit_id" class="form-label">Organizing Unit <span class="text-danger">*</span></label>
                            <select class="form-select" id="unit_id" name="unit_id" required>
                                <option value="">— Select Unit —</option>
                                <?php foreach ($units as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= (int)($report['unit_id'] ?? 0) === (int)$u['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($u['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title mb-0">Impact & Finance Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="total_attendance" class="form-label">Total Attendance</label>
                            <input type="number" class="form-control" id="total_attendance" name="total_attendance" min="0" placeholder="0" value="<?= htmlspecialchars($report['total_attendance'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="first_timers_count" class="form-label">First Timers</label>
                            <input type="number" class="form-control" id="first_timers_count" name="first_timers_count" min="0" placeholder="0" value="<?= htmlspecialchars($report['first_timers_count'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="budget_total" class="form-label">Total Budget (₦)</label>
                            <input type="number" class="form-control" id="budget_total" name="budget_total" step="0.01" min="0" placeholder="0.00" value="<?= htmlspecialchars($report['budget_total'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="actual_total" class="form-label">Actual Spent (₦)</label>
                            <input type="number" class="form-control" id="actual_total" name="actual_total" step="0.01" min="0" placeholder="0.00" value="<?= htmlspecialchars($report['actual_total'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Publicity Section -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Publicity Channels</h5>
                    <button type="button" class="btn btn-sm btn-soft-primary" id="add-publicity"><i class="bx bx-plus me-1"></i> Add Channel</button>
                </div>
                <div class="card-body">
                    <div id="publicity-rows">
                        <?php if (empty($publicity)): ?>
                            <div class="row publicity-row mb-2 border-bottom pb-2">
                                <div class="col-md-3 mb-2"><input type="text" class="form-control form-control-sm" name="publicity[0][channel]" placeholder="Channel" /></div>
                                <div class="col-md-4 mb-2"><input type="text" class="form-control form-control-sm" name="publicity[0][details]" placeholder="Details" /></div>
                                <div class="col-md-2 mb-2"><input type="number" class="form-control form-control-sm" name="publicity[0][estimated_reach]" min="0" placeholder="Reach" /></div>
                                <div class="col-md-2 mb-2"><input type="number" class="form-control form-control-sm" name="publicity[0][cost]" step="0.01" min="0" placeholder="Cost" /></div>
                                <div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-soft-danger remove-row"><i class="bx bx-trash"></i></button></div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($publicity as $i => $p): ?>
                                <div class="row publicity-row mb-2 border-bottom pb-2">
                                    <div class="col-md-3 mb-2"><input type="text" class="form-control form-control-sm" name="publicity[<?= $i ?>][channel]" placeholder="Channel" value="<?= htmlspecialchars($p['channel']) ?>" /></div>
                                    <div class="col-md-4 mb-2"><input type="text" class="form-control form-control-sm" name="publicity[<?= $i ?>][details]" placeholder="Details" value="<?= htmlspecialchars($p['details'] ?? '') ?>" /></div>
                                    <div class="col-md-2 mb-2"><input type="number" class="form-control form-control-sm" name="publicity[<?= $i ?>][estimated_reach]" min="0" placeholder="Reach" value="<?= $p['estimated_reach'] ?>" /></div>
                                    <div class="col-md-2 mb-2"><input type="number" class="form-control form-control-sm" name="publicity[<?= $i ?>][cost]" step="0.01" min="0" placeholder="Cost" value="<?= $p['cost'] ?>" /></div>
                                    <div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-soft-danger remove-row"><i class="bx bx-trash"></i></button></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Logistics Section -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Logistics & Resources</h5>
                    <button type="button" class="btn btn-sm btn-soft-primary" id="add-logistics"><i class="bx bx-plus me-1"></i> Add Item</button>
                </div>
                <div class="card-body">
                    <div id="logistics-rows">
                        <?php if (empty($logistics)): ?>
                            <div class="row logistics-row mb-2 border-bottom pb-2">
                                <div class="col-md-3 mb-2">
                                    <select class="form-select form-select-sm" name="logistics[0][category]">
                                        <option value="venue">Venue</option><option value="setup">Setup</option><option value="materials">Materials</option><option value="transport">Transport</option><option value="feeding">Feeding</option><option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2"><input type="text" class="form-control form-control-sm" name="logistics[0][description]" placeholder="Description" /></div>
                                <div class="col-md-4 mb-2"><input type="text" class="form-control form-control-sm" name="logistics[0][notes]" placeholder="Notes" /></div>
                                <div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-soft-danger remove-row"><i class="bx bx-trash"></i></button></div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($logistics as $i => $l): ?>
                                <div class="row logistics-row mb-2 border-bottom pb-2">
                                    <div class="col-md-3 mb-2">
                                        <select class="form-select form-select-sm" name="logistics[<?= $i ?>][category]">
                                            <option value="venue" <?= $l['category'] == 'venue' ? 'selected' : '' ?>>Venue</option>
                                            <option value="setup" <?= $l['category'] == 'setup' ? 'selected' : '' ?>>Setup</option>
                                            <option value="materials" <?= $l['category'] == 'materials' ? 'selected' : '' ?>>Materials</option>
                                            <option value="transport" <?= $l['category'] == 'transport' ? 'selected' : '' ?>>Transport</option>
                                            <option value="feeding" <?= $l['category'] == 'feeding' ? 'selected' : '' ?>>Feeding</option>
                                            <option value="other" <?= $l['category'] == 'other' ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2"><input type="text" class="form-control form-control-sm" name="logistics[<?= $i ?>][description]" value="<?= htmlspecialchars($l['description']) ?>" /></div>
                                    <div class="col-md-4 mb-2"><input type="text" class="form-control form-control-sm" name="logistics[<?= $i ?>][notes]" value="<?= htmlspecialchars($l['notes'] ?? '') ?>" /></div>
                                    <div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-soft-danger remove-row"><i class="bx bx-trash"></i></button></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Outcome Targets Section -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Outcome Targets</h5>
                    <button type="button" class="btn btn-sm btn-soft-primary" id="add-targets"><i class="bx bx-plus me-1"></i> Add Target</button>
                </div>
                <div class="card-body">
                    <div id="targets-rows">
                        <?php if (empty($targets)): ?>
                            <div class="row targets-row mb-2 border-bottom pb-2">
                                <div class="col-md-3 mb-2"><input type="text" class="form-control form-control-sm" name="targets[0][target_name]" placeholder="Metric" /></div>
                                <div class="col-md-2 mb-2"><input type="number" class="form-control form-control-sm" name="targets[0][target_value]" step="0.01" placeholder="Goal" /></div>
                                <div class="col-md-2 mb-2"><input type="number" class="form-control form-control-sm" name="targets[0][actual_value]" step="0.01" placeholder="Actual" /></div>
                                <div class="col-md-2 mb-2"><input type="text" class="form-control form-control-sm" name="targets[0][unit]" placeholder="Unit" /></div>
                                <div class="col-md-2 mb-2"><input type="text" class="form-control form-control-sm" name="targets[0][notes]" placeholder="Notes" /></div>
                                <div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-soft-danger remove-row"><i class="bx bx-trash"></i></button></div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($targets as $i => $t): ?>
                                <div class="row targets-row mb-2 border-bottom pb-2">
                                    <div class="col-md-3 mb-2"><input type="text" class="form-control form-control-sm" name="targets[<?= $i ?>][target_name]" value="<?= htmlspecialchars($t['target_name']) ?>" /></div>
                                    <div class="col-md-2 mb-2"><input type="number" class="form-control form-control-sm" name="targets[<?= $i ?>][target_value]" step="0.01" value="<?= $t['target_value'] ?>" /></div>
                                    <div class="col-md-2 mb-2"><input type="number" class="form-control form-control-sm" name="targets[<?= $i ?>][actual_value]" step="0.01" value="<?= $t['actual_value'] ?>" /></div>
                                    <div class="col-md-2 mb-2"><input type="text" class="form-control form-control-sm" name="targets[<?= $i ?>][unit]" value="<?= htmlspecialchars($t['unit'] ?? '') ?>" /></div>
                                    <div class="col-md-2 mb-2"><input type="text" class="form-control form-control-sm" name="targets[<?= $i ?>][notes]" value="<?= htmlspecialchars($t['notes'] ?? '') ?>" /></div>
                                    <div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-soft-danger remove-row"><i class="bx bx-trash"></i></button></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Event Gallery Section -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title mb-0">Event Gallery</h5>
                </div>
                <div class="card-body">
                    <!-- Existing Images -->
                    <?php if (!empty($images)): ?>
                        <h6 class="text-muted mb-3">Existing Photos</h6>
                        <div class="row g-3 mb-4">
                            <?php foreach ($images as $img): ?>
                                <div class="col-md-2 col-4">
                                    <div class="position-relative">
                                        <img src="<?= AssetHelper::url($img['file_path']) ?>" class="img-thumbnail w-100 h-100 shadow-sm" style="object-fit: cover; aspect-ratio: 1/1;">
                                        <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/{$report['id']}/images/{$img['id']}/delete") ?>" 
                                           class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-0 px-1 shadow"
                                           onclick="return confirm('Remove this image?')">
                                            <i class="bx bx-x"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <hr>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="images" class="form-label">Upload New Photos</label>
                        <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                        <div class="form-text">New photos will be added to the gallery. Allowed formats: JPG, PNG, WEBP.</div>
                    </div>
                    <div id="image-preview-container" class="row g-2 mt-2">
                        <!-- Previews will appear here -->
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= AssetHelper::url("churches/{$churchId}/outreach/{$report['id']}") ?>" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bx bx-save me-1"></i> Update Report
                        </button>
                    </div>
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
            if (inp.tagName === 'INPUT' || inp.tagName === 'SELECT' || inp.tagName === 'TEXTAREA') {
                inp.value = '';
            }
        });
        container.appendChild(clone);
        clone.querySelector('.remove-row').addEventListener('click', function() {
            if (container.querySelectorAll('.' + rowClass).length > 1) { clone.remove(); }
        });
    }

    function nextIndex(containerId, rowClass) {
        var container = document.getElementById(containerId);
        return container ? container.querySelectorAll('.' + rowClass).length : 0;
    }

    document.getElementById('add-publicity')?.addEventListener('click', function() { cloneRow('publicity-rows', 'publicity-row', nextIndex('publicity-rows', 'publicity-row')); });
    document.getElementById('add-logistics')?.addEventListener('click', function() { cloneRow('logistics-rows', 'logistics-row', nextIndex('logistics-rows', 'logistics-row')); });
    document.getElementById('add-targets')?.addEventListener('click', function() { cloneRow('targets-rows', 'targets-row', nextIndex('targets-rows', 'targets-row')); });

    document.querySelectorAll('.remove-row').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var row = btn.closest('.row');
            var container = row.parentElement;
            var rowClass = Array.from(row.classList).find(c => c.endsWith('-row'));
            if (container.querySelectorAll('.' + rowClass).length > 1) { row.remove(); }
        });
    });

    // Image preview logic
    document.getElementById('images')?.addEventListener('change', function(e) {
        var container = document.getElementById('image-preview-container');
        container.innerHTML = '';
        if (this.files) {
            Array.from(this.files).forEach(file => {
                var reader = new FileReader();
                reader.onload = function(event) {
                    var div = document.createElement('div');
                    div.className = 'col-md-2 col-3';
                    div.innerHTML = `<img src="${event.target.result}" class="img-thumbnail w-100 h-100" style="object-fit: cover; aspect-ratio: 1/1;">`;
                    container.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        }
    });
})();
</script>
