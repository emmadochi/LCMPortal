<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$property = $property ?? null;
$categories = $categories ?? [];
$users = $users ?? [];
$statusOptions = $statusOptions ?? [];
$csrf_token = $csrf_token ?? '';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                <h4 class="card-title mb-0">Update Asset Details</h4>
                <a href="<?= AssetHelper::url("churches/{$churchId}/property/{$property['id']}") ?>" class="btn btn-sm btn-light">
                    <i class="bx bx-arrow-back me-1"></i> Back to Asset Profile
                </a>
            </div>
            <div class="card-body p-4">
                <form action="<?= AssetHelper::url("churches/{$churchId}/property/{$property['id']}") ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="_method" value="PUT">

                    <div class="row">
                        <!-- Left Column: Primary Details -->
                        <div class="col-md-7">
                            <div class="mb-4">
                                <label for="name" class="form-label font-size-13 text-muted">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg border-light bg-light" id="name" name="name" value="<?= htmlspecialchars($property['name']) ?>" required>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label font-size-13 text-muted">Description</label>
                                <textarea class="form-control border-light bg-light" id="description" name="description" rows="4"><?= htmlspecialchars($property['description']) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="category_id" class="form-label font-size-13 text-muted">Category <span class="text-danger">*</span></label>
                                    <select class="form-select border-light bg-light" id="category_id" name="category_id" required>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= $property['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="serial_number" class="form-label font-size-13 text-muted">Serial Number</label>
                                    <input type="text" class="form-control border-light bg-light" id="serial_number" name="serial_number" value="<?= htmlspecialchars($property['serial_number']) ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="location" class="form-label font-size-13 text-muted">Location / Storage</label>
                                <input type="text" class="form-control border-light bg-light" id="location" name="location" value="<?= htmlspecialchars($property['location']) ?>">
                            </div>
                        </div>

                        <!-- Right Column: Acquisition & Assignment -->
                        <div class="col-md-5">
                            <div class="card bg-light border-0 mb-4 h-100 shadow-none">
                                <div class="card-body">
                                    <h5 class="font-size-14 mb-3 d-flex align-items-center"><i class="bx bx-info-circle me-1 text-primary"></i> Lifecycle & Financials</h5>
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label text-muted font-size-12 mb-1">Status <span class="text-danger">*</span></label>
                                        <select class="form-select border-0 shadow-sm" id="status" name="status" required>
                                            <?php foreach ($statusOptions as $val => $label): ?>
                                                <option value="<?= $val ?>" <?= $property['status'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="purchase_date" class="form-label text-muted font-size-12 mb-1">Acquisition Date</label>
                                        <input type="date" class="form-control border-0 shadow-sm" id="purchase_date" name="purchase_date" value="<?= $property['purchase_date'] ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="purchase_cost" class="form-label text-muted font-size-12 mb-1">Purchase Cost ($)</label>
                                        <input type="number" step="0.01" class="form-control border-0 shadow-sm" id="purchase_cost" name="purchase_cost" value="<?= $property['purchase_cost'] ?>">
                                    </div>

                                    <hr class="my-3">

                                    <h5 class="font-size-14 mb-3 d-flex align-items-center"><i class="bx bx-repost me-1 text-primary"></i> Person In-charge</h5>
                                    
                                    <div class="mb-0">
                                        <select class="form-select border-0 shadow-sm" id="assigned_to_user_id" name="assigned_to_user_id">
                                            <option value="">-- No Assignment --</option>
                                            <?php foreach ($users as $u): ?>
                                                <option value="<?= $u['id'] ?>" <?= $property['assigned_to_user_id'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted mt-1 d-block font-size-11">Assign a member responsible for this asset.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label font-size-13 text-muted">Internal Notes</label>
                                <textarea class="form-control border-light bg-light" id="notes" name="notes" rows="3"><?= htmlspecialchars($property['notes']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="<?= AssetHelper::url("churches/{$churchId}/property/{$property['id']}") ?>" class="btn btn-light px-4 me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
