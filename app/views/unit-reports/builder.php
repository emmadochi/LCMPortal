<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$template = $template ?? null;
$fields = $template['fields'] ?? [];
$isEdit = !empty($template['id']);
$csrfToken = Security::generateCSRFToken();
?>

<style>
.builder-canvas {
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 16px;
    min-height: 380px;
    padding: 24px;
}
.field-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    padding: 18px 20px;
    margin-bottom: 16px;
    transition: all 0.2s ease;
    cursor: move;
}
.field-card:hover {
    border-color: #4f46e5;
    box-shadow: 0 6px 16px rgba(79, 70, 229, 0.08);
}
.palette-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border-radius: 10px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    font-size: 0.84rem;
    font-weight: 600;
    color: #334155;
    width: 100%;
    margin-bottom: 8px;
    transition: all 0.15s ease;
}
.palette-btn:hover {
    background: #eef2ff;
    border-color: #4f46e5;
    color: #4f46e5;
}
.preset-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px;
    margin-bottom: 12px;
    background: #ffffff;
    transition: all 0.2s ease;
    cursor: pointer;
}
.preset-card:hover {
    border-color: #10b981;
    background: #f0fdf4;
}
</style>

<div class="container-fluid p-0">
    <div class="row g-4">
        <!-- Left: Field Palette & Starter Presets -->
        <div class="col-xl-3 col-lg-4">
            <!-- 1-Click Ministry Presets -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-bolt-circle text-success me-2 font-size-18"></i> 1-Click Starter Presets
                    </h6>
                </div>
                <div class="card-body p-3">
                    <p class="text-muted font-size-11 mb-2">Load a battle-tested template configured for your ministry:</p>
                    
                    <div class="preset-card" onclick="loadMinistryPreset('ushering')">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-soft-primary text-primary rounded-pill font-size-10">Operations</span>
                            <h6 class="mb-0 font-size-13 fw-bold text-dark">Ushering & Protocol</h6>
                        </div>
                        <small class="text-muted font-size-11">Headcounts, offering count, guest seating</small>
                    </div>

                    <div class="preset-card" onclick="loadMinistryPreset('choir')">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-soft-warning text-warning rounded-pill font-size-10">Music</span>
                            <h6 class="mb-0 font-size-13 fw-bold text-dark">Choir & Ministration</h6>
                        </div>
                        <small class="text-muted font-size-11">Rehearsal roster, ministration songs</small>
                    </div>

                    <div class="preset-card" onclick="loadMinistryPreset('media')">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-soft-info text-info rounded-pill font-size-10">Tech</span>
                            <h6 class="mb-0 font-size-13 fw-bold text-dark">Media & Production</h6>
                        </div>
                        <small class="text-muted font-size-11">Live stream viewers, sound health, gear</small>
                    </div>

                    <div class="preset-card" onclick="loadMinistryPreset('children')">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-soft-danger text-danger rounded-pill font-size-10">Discipleship</span>
                            <h6 class="mb-0 font-size-13 fw-bold text-dark">Children's Church</h6>
                        </div>
                        <small class="text-muted font-size-11">Age-group roll-call, Bible lessons</small>
                    </div>

                    <div class="preset-card" onclick="loadMinistryPreset('welfare_evangelism')">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-soft-success text-success rounded-pill font-size-10">Care</span>
                            <h6 class="mb-0 font-size-13 fw-bold text-dark">Welfare & Outreach</h6>
                        </div>
                        <small class="text-muted font-size-11">Families assisted, visitations, benevolence</small>
                    </div>
                </div>
            </div>

            <!-- Field Palette -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-plus-circle text-primary me-2 font-size-18"></i> Add Question Field
                    </h6>
                </div>
                <div class="card-body p-3">
                    <button type="button" class="palette-btn" onclick="addField('text')">
                        <i class="bx bx-font text-primary font-size-18"></i> Short Text (Single line)
                    </button>
                    <button type="button" class="palette-btn" onclick="addField('number')">
                        <i class="bx bx-calculator text-success font-size-18"></i> Number / Headcount
                    </button>
                    <button type="button" class="palette-btn" onclick="addField('textarea')">
                        <i class="bx bx-paragraph text-warning font-size-18"></i> Long Notes (Paragraph)
                    </button>
                    <button type="button" class="palette-btn" onclick="addField('select')">
                        <i class="bx bx-list-ul text-info font-size-18"></i> Dropdown Options
                    </button>
                    <button type="button" class="palette-btn" onclick="addField('checkbox')">
                        <i class="bx bx-check-square text-secondary font-size-18"></i> Checkbox Verification
                    </button>
                    <button type="button" class="palette-btn" onclick="addField('date')">
                        <i class="bx bx-calendar text-danger font-size-18"></i> Date & Time Picker
                    </button>
                    <button type="button" class="palette-btn" onclick="addField('file')">
                        <i class="bx bx-cloud-upload text-purple font-size-18"></i> Photo / File Attachment
                    </button>
                </div>
            </div>
        </div>

        <!-- Center & Right: Form Canvas & Configurations -->
        <div class="col-xl-9 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="bx bx-layout text-primary me-1"></i> Form Canvas & Schema Designer
                        </h5>
                        <small class="text-muted font-size-12">Drag or add fields to build your custom department report.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= AssetHelper::url('unit-reports/templates') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            Cancel
                        </a>
                        <button type="button" onclick="submitFormSchema()" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold">
                            <i class="bx bx-save me-1"></i> Save Form Template
                        </button>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form id="templateMetaForm">
                        <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                        <input type="hidden" id="templateId" name="template_id" value="<?= $template['id'] ?? '' ?>">

                        <!-- Metadata Row -->
                        <div class="row g-3 mb-4 p-3 bg-light rounded-4 border">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark font-size-13">Ministry Department / Unit <span class="text-danger">*</span></label>
                                <select id="unitId" name="unit_id" class="form-select rounded-pill" required>
                                    <option value="">-- Select Unit --</option>
                                    <?php foreach ($directorUnits as $u): ?>
                                        <option value="<?= $u['id'] ?>" <?= ((int)($template['unit_id'] ?? $unitId) === (int)$u['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($u['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark font-size-13">Form Title <span class="text-danger">*</span></label>
                                <input type="text" id="formTitle" name="title" class="form-control rounded-pill" placeholder="e.g. Weekly Ushers Service Report" value="<?= htmlspecialchars($template['title'] ?? '') ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark font-size-13">Category</label>
                                <input type="text" id="formCategory" name="category" class="form-control rounded-pill" placeholder="e.g. Service Operations" value="<?= htmlspecialchars($template['category'] ?? 'General') ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark font-size-13">Submission Frequency</label>
                                <select id="formFrequency" name="frequency" class="form-select rounded-pill">
                                    <option value="weekly" <?= (($template['frequency'] ?? 'weekly') === 'weekly') ? 'selected' : '' ?>>Weekly</option>
                                    <option value="per_service" <?= (($template['frequency'] ?? '') === 'per_service') ? 'selected' : '' ?>>After Every Service</option>
                                    <option value="biweekly" <?= (($template['frequency'] ?? '') === 'biweekly') ? 'selected' : '' ?>>Bi-Weekly</option>
                                    <option value="monthly" <?= (($template['frequency'] ?? '') === 'monthly') ? 'selected' : '' ?>>Monthly</option>
                                    <option value="on_demand" <?= (($template['frequency'] ?? '') === 'on_demand') ? 'selected' : '' ?>>On-Demand</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark font-size-13">Due Deadline</label>
                                <div class="input-group">
                                    <input type="text" id="deadlineDay" name="deadline_day" class="form-control rounded-start-pill" placeholder="e.g. Sunday" value="<?= htmlspecialchars($template['deadline_day'] ?? 'Sunday') ?>">
                                    <input type="time" id="deadlineTime" name="deadline_time" class="form-control rounded-end-pill" value="<?= htmlspecialchars($template['deadline_time'] ?? '18:00') ?>">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-dark font-size-13">Form Description / Instructions for Members</label>
                                <textarea id="formDescription" name="description" class="form-control rounded-3" rows="2" placeholder="Brief instructions on how to fill and submit this report..."><?= htmlspecialchars($template['description'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- Canvas Questions Area -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bx bx-list-check text-primary me-1"></i> Form Questions & Input Fields (<span id="fieldCountText"><?= count($fields) ?></span>)
                            </h6>
                            <button type="button" onclick="addField('text')" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bx bx-plus me-1"></i> Add Custom Field
                            </button>
                        </div>

                        <div id="fieldsContainer" class="builder-canvas">
                            <?php if (empty($fields)): ?>
                                <div id="emptyCanvasNotice" class="text-center py-5 text-muted">
                                    <i class="bx bx-layout font-size-36 opacity-50 mb-2 d-block"></i>
                                    <h6 class="text-dark fw-semibold">No questions added yet</h6>
                                    <p class="font-size-12 mb-3">Click on any field type on the left or select a 1-Click Ministry Preset to get started.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var fieldsList = <?= json_encode(!empty($fields) ? $fields : []) ?>;

document.addEventListener('DOMContentLoaded', function() {
    renderFields();
});

function renderFields() {
    var container = document.getElementById('fieldsContainer');
    var countText = document.getElementById('fieldCountText');
    if (!container) return;

    countText.textContent = fieldsList.length;

    if (fieldsList.length === 0) {
        container.innerHTML = `
            <div id="emptyCanvasNotice" class="text-center py-5 text-muted">
                <i class="bx bx-layout font-size-36 opacity-50 mb-2 d-block"></i>
                <h6 class="text-dark fw-semibold">No questions added yet</h6>
                <p class="font-size-12 mb-3">Click on any field type on the left or select a 1-Click Ministry Preset to get started.</p>
            </div>
        `;
        return;
    }

    var html = '';
    fieldsList.forEach(function(f, idx) {
        var isReq = f.is_required == 1;
        var fTypeBadge = getFieldTypeBadge(f.field_type);

        html += `
            <div class="field-card" data-index="${idx}">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border font-size-11 px-2 py-1">${idx + 1}</span>
                        ${fTypeBadge}
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch font-size-12 mb-0">
                            <input class="form-check-input" type="checkbox" id="req_${idx}" ${isReq ? 'checked' : ''} onchange="updateFieldProp(${idx}, 'is_required', this.checked ? 1 : 0)">
                            <label class="form-check-label text-muted" for="req_${idx}">Required</label>
                        </div>
                        <button type="button" onclick="moveField(${idx}, -1)" class="btn btn-sm btn-light py-0 px-1" title="Move Up" ${idx === 0 ? 'disabled' : ''}>
                            <i class="bx bx-chevron-up"></i>
                        </button>
                        <button type="button" onclick="moveField(${idx}, 1)" class="btn btn-sm btn-light py-0 px-1" title="Move Down" ${idx === fieldsList.length - 1 ? 'disabled' : ''}>
                            <i class="bx bx-chevron-down"></i>
                        </button>
                        <button type="button" onclick="removeField(${idx})" class="btn btn-sm btn-outline-danger py-0 px-1.5" title="Delete Question">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-md-7">
                        <label class="form-label font-size-11 text-muted mb-0.5">Question Label / Prompt</label>
                        <input type="text" class="form-control form-control-sm font-size-13 fw-semibold" value="${escapeHtml(f.field_label || '')}" oninput="updateFieldProp(${idx}, 'field_label', this.value)" placeholder="e.g. Total First Timers Received">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label font-size-11 text-muted mb-0.5">Placeholder / Help Tip</label>
                        <input type="text" class="form-control form-control-sm font-size-12" value="${escapeHtml(f.placeholder || f.help_text || '')}" oninput="updateFieldProp(${idx}, 'placeholder', this.value)" placeholder="Hint text for member...">
                    </div>
                    ${f.field_type === 'select' ? `
                    <div class="col-12 mt-2">
                        <label class="form-label font-size-11 text-muted mb-0.5">Dropdown Options (Comma-separated)</label>
                        <input type="text" class="form-control form-control-sm font-size-12" value="${escapeHtml(getOptionsString(f.field_options))}" oninput="updateOptionsProp(${idx}, this.value)" placeholder="Option 1, Option 2, Option 3">
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function getFieldTypeBadge(type) {
    switch (type) {
        case 'number': return '<span class="badge bg-soft-success text-success font-size-11"><i class="bx bx-calculator me-1"></i>Number</span>';
        case 'textarea': return '<span class="badge bg-soft-warning text-warning font-size-11"><i class="bx bx-paragraph me-1"></i>Paragraph</span>';
        case 'select': return '<span class="badge bg-soft-info text-info font-size-11"><i class="bx bx-list-ul me-1"></i>Dropdown</span>';
        case 'checkbox': return '<span class="badge bg-soft-secondary text-secondary font-size-11"><i class="bx bx-check-square me-1"></i>Checkbox</span>';
        case 'date': return '<span class="badge bg-soft-danger text-danger font-size-11"><i class="bx bx-calendar me-1"></i>Date</span>';
        case 'file': return '<span class="badge bg-soft-purple text-purple font-size-11"><i class="bx bx-cloud-upload me-1"></i>Attachment</span>';
        default: return '<span class="badge bg-soft-primary text-primary font-size-11"><i class="bx bx-font me-1"></i>Short Text</span>';
    }
}

function addField(type) {
    var count = fieldsList.length + 1;
    fieldsList.push({
        field_label: 'Question ' + count,
        field_key: 'field_' + count + '_' + Date.now().toString().slice(-4),
        field_type: type,
        field_options: type === 'select' ? JSON.stringify(['Option 1', 'Option 2', 'Option 3']) : null,
        placeholder: '',
        help_text: '',
        is_required: 1,
        sort_order: count
    });
    renderFields();
}

function removeField(index) {
    fieldsList.splice(index, 1);
    renderFields();
}

function moveField(index, direction) {
    var target = index + direction;
    if (target < 0 || target >= fieldsList.length) return;
    var temp = fieldsList[index];
    fieldsList[index] = fieldsList[target];
    fieldsList[target] = temp;
    renderFields();
}

function updateFieldProp(index, prop, val) {
    if (fieldsList[index]) {
        fieldsList[index][prop] = val;
    }
}

function updateOptionsProp(index, rawStr) {
    if (fieldsList[index]) {
        var arr = rawStr.split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s.length > 0; });
        fieldsList[index]['field_options'] = JSON.stringify(arr);
    }
}

function getOptionsString(optVal) {
    if (!optVal) return '';
    try {
        var parsed = typeof optVal === 'string' ? JSON.parse(optVal) : optVal;
        if (Array.isArray(parsed)) return parsed.join(', ');
    } catch(e) {}
    return optVal;
}

function loadMinistryPreset(presetKey) {
    fetch('<?= AssetHelper::url('unit-reports/templates/preset') ?>/' + presetKey)
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success && data.preset) {
                var p = data.preset;
                document.getElementById('formTitle').value = p.title || '';
                document.getElementById('formCategory').value = p.category || 'General';
                document.getElementById('formDescription').value = p.description || '';
                document.getElementById('formFrequency').value = p.frequency || 'weekly';
                document.getElementById('deadlineDay').value = p.deadline_day || 'Sunday';
                document.getElementById('deadlineTime').value = p.deadline_time || '18:00';

                fieldsList = p.fields || [];
                renderFields();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Preset Loaded!',
                        text: 'Loaded "' + p.title + '" with ' + fieldsList.length + ' configured questions.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
        });
}

function submitFormSchema() {
    var unitId = document.getElementById('unitId').value;
    var title = document.getElementById('formTitle').value.trim();

    if (!unitId) {
        alert('Please select a Ministry Unit.');
        return;
    }
    if (!title) {
        alert('Please provide a Form Title.');
        return;
    }
    if (fieldsList.length === 0) {
        alert('Please add at least one question field to this form.');
        return;
    }

    var formData = new FormData();
    formData.append('_token', '<?= $csrfToken ?>');
    formData.append('template_id', document.getElementById('templateId').value);
    formData.append('unit_id', unitId);
    formData.append('title', title);
    formData.append('category', document.getElementById('formCategory').value);
    formData.append('frequency', document.getElementById('formFrequency').value);
    formData.append('deadline_day', document.getElementById('deadlineDay').value);
    formData.append('deadline_time', document.getElementById('deadlineTime').value);
    formData.append('description', document.getElementById('formDescription').value);
    formData.append('fields_json', JSON.stringify(fieldsList));

    fetch('<?= AssetHelper::url('unit-reports/templates/save') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            window.location.href = '<?= AssetHelper::url('unit-reports/templates') ?>?unit_id=' + unitId;
        } else {
            alert(data.message || 'Error saving template.');
        }
    })
    .catch(function(err) {
        console.error(err);
        alert('Network error while saving template.');
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
</script>
