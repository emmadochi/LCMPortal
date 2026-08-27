<?php
use App\Utilities\AssetHelper;
$post = $post ?? [];
?>

<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1 small">
                            <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?= AssetHelper::url('property-categories') ?>" class="text-decoration-none text-muted">Categories</a></li>
                            <li class="breadcrumb-item active text-primary fw-semibold">Create</li>
                        </ol>
                    </nav>
                    <h3 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bx bx-folder-plus text-primary"></i> Create Property Category
                    </h3>
                </div>
                <a href="<?= AssetHelper::url('property-categories') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bx bx-arrow-back me-1"></i> Back to Categories
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="<?= AssetHelper::url('property-categories') ?>" method="POST">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold small text-muted text-uppercase">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" required
                                   value="<?= htmlspecialchars($post['name'] ?? '') ?>"
                                   placeholder="e.g. Musical & Audio Gear">
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold small text-muted text-uppercase">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                      placeholder="Optional description for items classified under this category"><?= htmlspecialchars($post['description'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bx bx-check me-1"></i> Create Category
                            </button>
                            <a href="<?= AssetHelper::url('property-categories') ?>" class="btn btn-light rounded-pill px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
