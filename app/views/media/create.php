<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Upload Media</h4>
            </div>
            <div class="card-body">
                <form id="media-upload-form" method="POST" action="<?= AssetHelper::url('media') ?>" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">File <span class="text-danger">*</span></label>
                        <div id="media-dropzone" class="dropzone border border-dashed rounded-3 bg-light-subtle">
                            <div class="fallback">
                                <input name="file" type="file" required>
                            </div>
                            <div class="dz-message needsclick py-5">
                                <p class="text-muted mb-0">Drop a file here or click to browse. Max 25MB.</p>
                            </div>
                        </div>
                        <div class="form-text">Images, videos, and documents are supported.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unit_id" class="form-label">Unit</label>
                                <select class="form-select" id="unit_id" name="unit_id">
                                    <option value="">No Unit (General)</option>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?= $unit['id'] ?>">
                                            <?= htmlspecialchars($unit['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat ?>" <?= $cat === 'other' ? 'selected' : '' ?>>
                                            <?= ucfirst($cat) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" 
                               placeholder="Media title (optional)">
                        <div class="form-text">Leave blank to use filename</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" placeholder="Media description..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" class="form-control" id="tags" name="tags" 
                               value="<?= htmlspecialchars($_POST['tags'] ?? '') ?>" 
                               placeholder="tag1, tag2, tag3">
                        <div class="form-text">Separate tags with commas</div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?= AssetHelper::url('media') ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" id="media-submit-btn" class="btn btn-primary">
                            <i data-feather="upload" class="me-1"></i> Upload Media
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

        const form = document.getElementById('media-upload-form');
        const submitBtn = document.getElementById('media-submit-btn');
        const dropzoneElement = document.getElementById('media-dropzone');

        if (!form || !submitBtn || !dropzoneElement) {
            return;
        }

        const mediaDropzone = new Dropzone(dropzoneElement, {
            url: form.getAttribute('action'),
            autoProcessQueue: false,
            maxFiles: 1,
            maxFilesize: 25,
            acceptedFiles: '.jpg,.jpeg,.png,.gif,.webp,.mp4,.mov,.avi,.mkv,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx',
            addRemoveLinks: true,
            dictDefaultMessage: 'Drop file here or click to upload',
            clickable: '#media-dropzone'
        });

        form.addEventListener('submit', function(event) {
            if (!mediaDropzone.getAcceptedFiles().length) {
                event.preventDefault();
                dropzoneElement.classList.add('border-danger');
                dropzoneElement.classList.remove('border-dashed');
                setTimeout(() => {
                    dropzoneElement.classList.remove('border-danger');
                    dropzoneElement.classList.add('border-dashed');
                }, 1500);
                return;
            }

            event.preventDefault();
            submitBtn.disabled = true;
            submitBtn.classList.add('disabled');

            const formData = new FormData(form);
            const [file] = mediaDropzone.getAcceptedFiles();
            if (file) {
                formData.append('file', file, file.name);
            }

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
                alert('Unable to upload media right now. Please try again.');
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

