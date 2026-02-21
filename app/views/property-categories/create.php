<?php
use App\Utilities\AssetHelper;

$post = $post ?? [];
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Create Category</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('property-categories') ?>">Categories</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create Property Category</h4>
            </div>
            <div class="card-body">
                <form action="<?= AssetHelper::url('property-categories') ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required
                               value="<?= htmlspecialchars($post['name'] ?? '') ?>"
                               placeholder="e.g. Musical Equipment">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Optional description for this category"><?= htmlspecialchars($post['description'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Create Category
                        </button>
                        <a href="<?= AssetHelper::url('property-categories') ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
