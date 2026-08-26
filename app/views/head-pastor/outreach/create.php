<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$units = $units ?? [];
$statuses = $statuses ?? ['draft' => 'Draft', 'submitted' => 'Submitted', 'approved' => 'Approved'];
$csrf_token = $csrf_token ?? '';
?>

<div class="row">
    <div class="col-12">
        <form method="POST" action="<?= AssetHelper::url("churches/{$churchId}/outreach") ?>" id="outreach-report-form" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title mb-0">Program Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="title" class="form-label">Program Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required minlength="3" maxlength="255" placeholder="e.g. Community Health Outreach" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <?php foreach ($statuses as $val => $label): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= ($_POST['status'] ?? 'draft') === $val ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description / Objectives</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Briefly describe the purpose and execution of this program..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="program_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="program_date" name="program_date" required value="<?= htmlspecialchars($_POST['program_date'] ?? date('Y-m-d')) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unit_id" class="form-label">Organizing Unit <span class="text-danger">*</span></label>
                            <select class="form-select" id="unit_id" name="unit_id" required>
                                <option value="">— Select Unit —</option>
                                <?php foreach ($units as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= (int)($_POST['unit_id'] ?? 0) === (int)$u['id'] ? 'selected' : '' ?>>
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
                            <input type="number" class="form-control" id="total_attendance" name="total_attendance" min="0" placeholder="0" value="<?= htmlspecialchars($_POST['total_attendance'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="first_timers_count" class="form-label">First Timers</label>
                            <input type="number" class="form-control" id="first_timers_count" name="first_timers_count" min="0" placeholder="0" value="<?= htmlspecialchars($_POST['first_timers_count'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="budget_total" class="form-label">Total Budget (₦)</label>
                            <input type="number" class="form-control" id="budget_total" name="budget_total" step="0.01" min="0" placeholder="0.00" value="<?= htmlspecialchars($_POST['budget_total'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="actual_total" class="form-label">Actual Spent (₦)</label>
                            <input type="number" class="form-control" id="actual_total" name="actual_total" step="0.01" min="0" placeholder="0.00" value="<?= htmlspecialchars($_POST['actual_total'] ?? '') ?>">
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
                        <div class="row publicity-row mb-2 border-bottom pb-2">
                            <div class="col-md-3 mb-2">
                                <input type="text" class="form-control form-control-sm" name="publicity[0][channel]" placeholder="Channel (e.g. Facebook)" />
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" class="form-control form-control-sm" name="publicity[0][details]" placeholder="Details / Remarks" />
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="number" class="form-control form-control-sm" name="publicity[0][estimated_reach]" min="0" placeholder="Est. Reach" />
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="number" class="form-control form-control-sm" name="publicity[0][cost]" step="0.01" min="0" placeholder="Cost" />
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-soft-danger remove-row"><i class="bx bx-trash"></i></button>
                            </div>
                        </div>
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
                        <div class="row logistics-row mb-2 border-bottom pb-2">
                            <div class="col-md-3 mb-2">
                                <select class="form-select form-select-sm" name="logistics[0][category]">
                                    <option value="venue">Venue / Space</option>
                                    <option value="setup">Setup & Decor</option>
                                    <option value="materials">Materials / Printing</option>
                                    <option value="transport">Transportation</option>
                                    <option value="feeding">Feeding / Welfare</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" class="form-control form-control-sm" name="logistics[0][description]" placeholder="Description of requirement" />
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" class="form-control form-control-sm" name="logistics[0][notes]" placeholder="Status / Vendor / Note" />
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-soft-danger remove-row"><i class="bx bx-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Targets Section -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Outcome Targets</h5>
                    <button type="button" class="btn btn-sm btn-soft-primary" id="add-targets"><i class="bx bx-plus me-1"></i> Add Target</button>
                </div>
                <div class="card-body">
                    <div id="targets-rows">
                        <div class="row targets-row mb-2 border-bottom pb-2">
                            <div class="col-md-3 mb-2">
                                <input type="text" class="form-control form-control-sm" name="targets[0][target_name]" placeholder="Metric (e.g. Conversions)" />
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="number" class="form-control form-control-sm" name="targets[0][target_value]" step="0.01" placeholder="Goal" />
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="number" class="form-control form-control-sm" name="targets[0][actual_value]" step="0.01" placeholder="Actual" />
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="text" class="form-control form-control-sm" name="targets[0][unit]" placeholder="Unit (e.g. souls)" />
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="text" class="form-control form-control-sm" name="targets[0][notes]" placeholder="Notes" />
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-soft-danger remove-row"><i class="bx bx-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Gallery Section -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title mb-0">Event Gallery</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="images" class="form-label">Upload Program Photos (Max 5MB each)</label>
                        <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                        <div class="form-text">You can select multiple images at once. Allowed formats: JPG, PNG, WEBP.</div>
                    </div>
                    <div id="image-preview-container" class="row g-2 mt-2">
                        <!-- Previews will appear here -->
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= AssetHelper::url("churches/{$churchId}/outreach") ?>" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bx bx-save me-1"></i> Save Report
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
        
        // Add listener to new remove button
        clone.querySelector('.remove-row').addEventListener('click', function() {
            if (container.querySelectorAll('.' + rowClass).length > 1) {
                clone.remove();
            }
        });
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
    document.getElementById('add-targets')?.addEventListener('click', function() {
        cloneRow('targets-rows', 'targets-row', nextIndex('targets-rows', 'targets-row'));
    });

    // Initial remove listeners
    document.querySelectorAll('.remove-row').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var row = btn.closest('.row');
            var container = row.parentElement;
            var rowClass = Array.from(row.classList).find(c => c.endsWith('-row'));
            if (container.querySelectorAll('.' + rowClass).length > 1) {
                row.remove();
            }
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
