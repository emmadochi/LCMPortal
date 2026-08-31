<?php
use App\Utilities\AssetHelper;

$csrf_token = $csrf_token ?? '';
?>

<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-9 col-md-11">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-1 small">
                                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('units') ?>" class="text-decoration-none text-muted">Units</a></li>
                                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('units/' . $unit['id']) ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($unit['name']) ?></a></li>
                                    <li class="breadcrumb-item active text-primary fw-semibold">Edit Unit</li>
                                </ol>
                            </nav>
                            <h3 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2 font-size-22">
                                <i class="bx bx-edit text-primary"></i> Edit Ministry Unit
                            </h3>
                        </div>
                        <a href="<?= AssetHelper::url('units/' . $unit['id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="bx bx-arrow-back me-1"></i> Back to Unit
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-4 p-md-5">
                    <form method="POST" action="<?= AssetHelper::url('units/' . $unit['id']) ?>">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="_method" value="PUT">
                        
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold text-dark font-size-14">Unit Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0 rounded-start-pill ps-3"><i class="bx bx-buildings font-size-16"></i></span>
                                <input type="text" class="form-control form-control-lg border-start-0 rounded-end-pill font-size-14" id="name" name="name" 
                                       value="<?= htmlspecialchars($unit['name']) ?>" 
                                       required minlength="3" maxlength="255" placeholder="e.g. Media & Technical Department">
                            </div>
                            <small class="text-muted font-size-12 mt-1 d-block">The official ministry department name visible across the portal.</small>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold text-dark font-size-14">Description & Scope of Duty</label>
                            <textarea class="form-control rounded-3 font-size-13 p-3" id="description" name="description" 
                                      rows="4" maxlength="1000" placeholder="Describe the ministry's role, duties, and objectives..."><?= htmlspecialchars($unit['description'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label fw-bold text-dark font-size-14">Status <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg rounded-pill font-size-14" id="status" name="status" required>
                                <option value="active" <?= $unit['status'] === 'active' ? 'selected' : '' ?>>Active (Accepting members & reports)</option>
                                <option value="inactive" <?= $unit['status'] === 'inactive' ? 'selected' : '' ?>>Inactive (Archived / Suspended)</option>
                            </select>
                        </div>

                        <div class="pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="<?= AssetHelper::url('units/' . $unit['id']) ?>" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                                <i class="bx bx-check-circle me-1"></i> Update Unit Details
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

