<?php
use App\Utilities\AssetHelper;

$post = $post ?? [];
$statusOptions = $statusOptions ?? [];
$churches = $churches ?? [];
$isAdmin = $isAdmin ?? false;
$isHeadPastor = $isHeadPastor ?? false;
$headPastorChurchId = $headPastorChurchId ?? null;
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Add Property</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('properties') ?>">Properties</a></li>
                    <li class="breadcrumb-item active">Add</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Add New Property</h4>
            </div>
            <div class="card-body">
                <form action="<?= AssetHelper::url('properties') ?>" method="POST" enctype="multipart/form-data" id="property-form">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                    <?php if ($isAdmin): ?>
                        <div class="mb-3">
                            <label for="church_id" class="form-label">Church <span class="text-danger">*</span></label>
                            <select class="form-select" id="church_id" name="church_id" required>
                                <option value="">Select Church</option>
                                <?php foreach ($churches as $church): ?>
                                    <option value="<?= $church['id'] ?>"
                                        <?= ($post['church_id'] ?? '') == $church['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($church['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php elseif ($isHeadPastor && $headPastorChurchId): ?>
                        <div class="mb-3">
                            <label class="form-label">Church</label>
                            <input type="hidden" name="church_id" value="<?= (int)$headPastorChurchId ?>">
                            <p class="form-control-plaintext mb-0">
                                This property will be recorded under your church.
                            </p>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" 
                                                <?= ($post['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <?php foreach ($statusOptions as $key => $label): ?>
                                        <option value="<?= $key ?>" 
                                                <?= ($post['status'] ?? 'available') === $key ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Property Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required
                               value="<?= htmlspecialchars($post['name'] ?? '') ?>"
                               placeholder="e.g. Yamaha Keyboard">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Describe the property..."><?= htmlspecialchars($post['description'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location"
                                       value="<?= htmlspecialchars($post['location'] ?? '') ?>"
                                       placeholder="e.g. Main Hall">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="serial_number" class="form-label">Serial Number</label>
                                <input type="text" class="form-control" id="serial_number" name="serial_number"
                                       value="<?= htmlspecialchars($post['serial_number'] ?? '') ?>"
                                       placeholder="Serial number if applicable">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="purchase_date" class="form-label">Purchase Date</label>
                                <input type="date" class="form-control" id="purchase_date" name="purchase_date"
                                       value="<?= htmlspecialchars($post['purchase_date'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="purchase_cost" class="form-label">Purchase Cost</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="purchase_cost" name="purchase_cost"
                                           step="0.01" min="0"
                                           value="<?= htmlspecialchars($post['purchase_cost'] ?? '') ?>"
                                           placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Property Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                                <div class="form-text">JPG, PNG, GIF or WebP. Max 5 MB.</div>
                                <div id="image-preview-container" class="mt-3" style="display: none;">
                                    <div class="border rounded p-2 bg-light d-inline-block">
                                        <img id="image-preview" src="" alt="Preview" class="rounded d-block" 
                                             style="max-height: 200px; max-width: 100%; object-fit: contain;">
                                        <button type="button" id="image-remove-btn" class="btn btn-sm btn-outline-danger mt-2">
                                            <i data-feather="x" class="icon-sm"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="Additional notes..."><?= htmlspecialchars($post['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Add Property
                        </button>
                        <a href="<?= AssetHelper::url('properties') ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    var imageInput = document.getElementById('image');
    var previewContainer = document.getElementById('image-preview-container');
    var previewImg = document.getElementById('image-preview');
    var removeBtn = document.getElementById('image-remove-btn');

    if (imageInput) {
        imageInput.addEventListener('change', function() {
            var file = this.files[0];
            if (!file) {
                previewContainer.style.display = 'none';
                return;
            }
            if (!file.type.match(/^image\/(jpeg|png|gif|webp)$/)) {
                alert('Please select a valid image file.');
                this.value = '';
                return;
            }
            var reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
                if (typeof feather !== 'undefined') feather.replace();
            };
            reader.readAsDataURL(file);
        });
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            imageInput.value = '';
            previewImg.src = '';
            previewContainer.style.display = 'none';
            if (typeof feather !== 'undefined') feather.replace();
        });
    }
});
</script>
