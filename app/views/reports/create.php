<?php
use App\Utilities\AssetHelper;
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create New Report</h4>
            </div>
            <div class="card-body">
                <form id="report-create-form" method="POST" action="<?= AssetHelper::url('reports') ?>" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                                <select class="form-select" id="unit_id" name="unit_id" required>
                                    <option value="">Select Unit...</option>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?= $unit['id'] ?>" 
                                            <?= (isset($_POST['unit_id']) && $_POST['unit_id'] == $unit['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($unit['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="report_type" class="form-label">Report Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="report_type" name="report_type" required>
                                    <option value="">Select Type...</option>
                                    <?php foreach ($reportTypes as $type): ?>
                                        <option value="<?= $type ?>" 
                                            <?= (isset($_POST['report_type']) && $_POST['report_type'] === $type) ? 'selected' : '' ?>>
                                            <?= ucfirst($type) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" 
                               required minlength="3" maxlength="255" placeholder="Enter report title">
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" 
                                  rows="10" required minlength="10" placeholder="Enter report content"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                        <div class="form-text">Provide detailed information about the report</div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" selected>Draft</option>
                            <option value="submitted">Submit Now</option>
                        </select>
                        <div class="form-text">Save as draft or submit immediately</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Attachments</label>
                        <div id="report-files-dropzone" class="dropzone border border-dashed rounded-3 bg-light-subtle">
                            <div class="fallback">
                                <input type="file" name="files[]" multiple>
                            </div>
                            <div class="dz-message needsclick py-5">
                                <p class="text-muted mb-0">Drop files here or click to browse (PDF, Images, Documents).</p>
                            </div>
                        </div>
                        <div class="form-text">You can upload up to 10 files, max 10MB each.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?= AssetHelper::url('reports') ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" id="report-submit-btn" class="btn btn-primary">
                            <i data-feather="check-circle" class="me-1"></i> Create Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$extraCss = array_merge($extraCss ?? [], [
    '../libs/dropzone/min/dropzone.min.css',
]);

$extraJs = array_merge($extraJs ?? [], [
    '../libs/dropzone/min/dropzone.min.js',
]);

$pageJs = ($pageJs ?? '') . <<<JS
<script>
    (function() {
        if (typeof Dropzone === 'undefined') {
            return;
        }

        Dropzone.autoDiscover = false;

        const form = document.getElementById('report-create-form');
        const submitBtn = document.getElementById('report-submit-btn');
        const dropzoneElement = document.getElementById('report-files-dropzone');

        if (!form || !submitBtn || !dropzoneElement) {
            return;
        }

        const attachmentsDropzone = new Dropzone(dropzoneElement, {
            url: form.getAttribute('action'),
            autoProcessQueue: false,
            maxFiles: 10,
            maxFilesize: 10,
            uploadMultiple: true,
            parallelUploads: 5,
            paramName: 'files[]',
            addRemoveLinks: true,
            acceptedFiles: '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.csv,.jpg,.jpeg,.png,.gif,.webp',
            dictDefaultMessage: 'Drop files here or click to upload',
            clickable: '#report-files-dropzone'
        });

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            submitBtn.disabled = true;
            submitBtn.classList.add('disabled');

            const formData = new FormData(form);
            attachmentsDropzone.getAcceptedFiles().forEach((file) => {
                formData.append('files[]', file, file.name);
            });

            fetch(form.getAttribute('action'), {
                method: form.getAttribute('method') || 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then((response) => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return null;
                }
                return response.text();
            })
            .then((body) => {
                if (body) {
                    document.open();
                    document.write(body);
                    document.close();
                }
            })
            .catch(() => {
                alert('Unable to create the report right now. Please try again.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.classList.remove('disabled');
            });
        });
    })();
</script>
JS;
?>

