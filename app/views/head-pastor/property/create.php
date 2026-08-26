<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
$categories = $categories ?? [];
$users = $users ?? [];
$statusOptions = $statusOptions ?? [];
$csrf_token = $csrf_token ?? '';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                <h4 class="card-title mb-0">Register New Property</h4>
                <a href="<?= AssetHelper::url("churches/{$churchId}/property/records") ?>" class="btn btn-sm btn-light">
                    <i class="bx bx-arrow-back me-1"></i> Back to Inventory
                </a>
            </div>
            <div class="card-body p-4">
                <form action="<?= AssetHelper::url("churches/{$churchId}/property") ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">

                    <div class="row">
                        <!-- Left Column: Primary Details -->
                        <div class="col-md-7">
                            <div class="mb-4">
                                <label for="name" class="form-label">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg border-light bg-light" id="name" name="name" placeholder="e.g., Yamaha MG16X Mixer" required>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control border-light bg-light" id="description" name="description" rows="4" placeholder="Briefly describe the asset..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select border-light bg-light" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="serial_number" class="form-label">Serial Number</label>
                                    <input type="text" class="form-control border-light bg-light" id="serial_number" name="serial_number" placeholder="S/N or Tag ID">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="location" class="form-label">Location / Storage</label>
                                <input type="text" class="form-control border-light bg-light" id="location" name="location" placeholder="e.g., Main Sanctuary, Store Room A">
                            </div>
                        </div>

                        <!-- Right Column: Acquisition & Assignment -->
                        <div class="col-md-5">
                            <div class="card bg-light border-0 mb-4 h-100">
                                <div class="card-body">
                                    <h5 class="font-size-14 mb-3 d-flex align-items-center"><i class="bx bx-info-circle me-1 text-primary"></i> Lifecycle & Cost</h5>
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label text-muted font-size-12 mb-1">Status <span class="text-danger">*</span></label>
                                        <select class="form-select border-0 shadow-sm" id="status" name="status" required>
                                            <?php foreach ($statusOptions as $val => $label): ?>
                                                <option value="<?= $val ?>" <?= $val === 'available' ? 'selected' : '' ?>><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="purchase_date" class="form-label text-muted font-size-12 mb-1">Acquisition Date</label>
                                        <input type="date" class="form-control border-0 shadow-sm" id="purchase_date" name="purchase_date" value="<?= date('Y-m-d') ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="purchase_cost" class="form-label text-muted font-size-12 mb-1">Purchase Cost ($)</label>
                                        <input type="number" step="0.01" class="form-control border-0 shadow-sm" id="purchase_cost" name="purchase_cost" placeholder="0.00">
                                    </div>

                                    <hr class="my-3">

                                    <h5 class="font-size-14 mb-3 d-flex align-items-center"><i class="bx bx-user me-1 text-primary"></i> Initial Assignment</h5>
                                    
                                    <div class="mb-0">
                                        <label for="assigned_to_user_id" class="form-label text-muted font-size-12 mb-1">Person In-charge</label>
                                        <select class="form-select border-0 shadow-sm select2" id="assigned_to_user_id" name="assigned_to_user_id">
                                            <option value="">-- No Assignment --</option>
                                            <?php foreach ($users as $u): ?>
                                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted mt-1 d-block">Who is currently holding/using this asset?</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Internal Notes</label>
                                <textarea class="form-control border-light bg-light" id="notes" name="notes" rows="3" placeholder="Additional details, warranty info, etc."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="reset" class="btn btn-light px-4 me-2">Clear Form</button>
                        <button type="submit" class="btn btn-primary px-5">Save Asset Registration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
