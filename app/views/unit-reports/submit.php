<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$template = $template ?? null;
$fields = $fields ?? [];
$csrfToken = Security::generateCSRFToken();
?>

<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <span class="badge bg-soft-primary text-primary px-3 py-1 rounded-pill font-size-12 fw-semibold">
                            <i class="bx bx-group me-1"></i> <?= htmlspecialchars($template['unit_name'] ?? 'Department') ?>
                        </span>
                        <span class="badge bg-soft-info text-info px-3 py-1 rounded-pill font-size-12 fw-semibold">
                            <i class="bx bx-time-five me-1"></i> Due <?= htmlspecialchars($template['deadline_day'] ?? 'Sunday') ?> at <?= date('h:i A', strtotime($template['deadline_time'] ?? '18:00')) ?>
                        </span>
                    </div>

                    <h3 class="fw-bold text-dark mb-1 font-size-22">
                        <?= htmlspecialchars($template['title']) ?>
                    </h3>
                    <?php if (!empty($template['description'])): ?>
                        <p class="text-muted font-size-13 mb-0" style="line-height: 1.6;">
                            <?= nl2br(htmlspecialchars($template['description'])) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dynamic Form Submission Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-5">
                <div class="card-body p-4 p-md-5">
                    <form method="POST" action="<?= AssetHelper::url('unit-reports/submit/' . $template['id']) ?>" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="<?= $csrfToken ?>">

                        <!-- Period Identifier -->
                        <div class="mb-4 pb-3 border-bottom">
                            <label class="form-label fw-bold text-dark font-size-14">
                                Report Period / Date of Service <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="report_period" class="form-control form-control-lg rounded-pill font-size-14" value="<?= date('Y-m-d') ?>" required>
                            <small class="text-muted font-size-12">The exact service, rehearsal, or outreach date this submission accounts for.</small>
                        </div>

                        <!-- Dynamic Questions Loop -->
                        <?php foreach ($fields as $idx => $f): ?>
                            <?php
                            $fKey = htmlspecialchars($f['field_key']);
                            $fLabel = htmlspecialchars($f['field_label']);
                            $isRequired = !empty($f['is_required']);
                            $fType = $f['field_type'];
                            $options = $f['options_array'] ?? [];
                            ?>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark font-size-14 d-flex justify-content-between">
                                    <span>
                                        <?= ($idx + 1) ?>. <?= $fLabel ?>
                                        <?php if ($isRequired): ?><span class="text-danger">*</span><?php endif; ?>
                                    </span>
                                </label>

                                <?php if ($fType === 'textarea'): ?>
                                    <textarea name="<?= $fKey ?>" class="form-control rounded-3 font-size-13" rows="3" placeholder="<?= htmlspecialchars($f['placeholder'] ?? 'Enter your notes...') ?>" <?= $isRequired ? 'required' : '' ?>></textarea>

                                <?php elseif ($fType === 'number'): ?>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill bg-light text-muted"><i class="bx bx-calculator"></i></span>
                                        <input type="number" step="any" name="<?= $fKey ?>" class="form-control form-control-lg rounded-end-pill font-size-14" placeholder="<?= htmlspecialchars($f['placeholder'] ?? '0') ?>" <?= $isRequired ? 'required' : '' ?>>
                                    </div>

                                <?php elseif ($fType === 'select'): ?>
                                    <select name="<?= $fKey ?>" class="form-select form-select-lg rounded-pill font-size-14" <?= $isRequired ? 'required' : '' ?>>
                                        <option value="">-- Choose Option --</option>
                                        <?php foreach ($options as $opt): ?>
                                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                <?php elseif ($fType === 'checkbox'): ?>
                                    <div class="form-check form-switch font-size-14 mt-1">
                                        <input class="form-check-input" type="checkbox" name="<?= $fKey ?>" value="1" id="chk_<?= $idx ?>">
                                        <label class="form-check-label text-muted" for="chk_<?= $idx ?>">
                                            <?= htmlspecialchars($f['placeholder'] ?? 'Yes / Verified') ?>
                                        </label>
                                    </div>

                                <?php elseif ($fType === 'date'): ?>
                                    <input type="date" name="<?= $fKey ?>" class="form-control form-control-lg rounded-pill font-size-14" <?= $isRequired ? 'required' : '' ?>>

                                <?php elseif ($fType === 'file'): ?>
                                    <div class="p-3 border rounded-3 bg-light text-center">
                                        <i class="bx bx-cloud-upload font-size-28 text-primary mb-1 d-block"></i>
                                        <input type="file" name="<?= $fKey ?>" class="form-control form-control-sm font-size-12" <?= $isRequired ? 'required' : '' ?>>
                                        <small class="text-muted font-size-11 d-block mt-1"><?= htmlspecialchars($f['help_text'] ?? 'Images, PDFs, audio or documents up to 10MB') ?></small>
                                    </div>

                                <?php else: ?>
                                    <input type="text" name="<?= $fKey ?>" class="form-control form-control-lg rounded-pill font-size-14" placeholder="<?= htmlspecialchars($f['placeholder'] ?? 'Enter details...') ?>" <?= $isRequired ? 'required' : '' ?>>
                                <?php endif; ?>

                                <?php if (!empty($f['help_text']) && $fType !== 'file'): ?>
                                    <small class="text-muted font-size-11 mt-1 d-block"><?= htmlspecialchars($f['help_text']) ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <!-- Submit Button -->
                        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="<?= AssetHelper::url('dashboard') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm font-size-14">
                                <i class="bx bx-paper-plane me-1"></i> Submit Report to Director
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
