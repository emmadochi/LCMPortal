<?php
use App\Utilities\AssetHelper;
?>

<div class="container-fluid p-0">
    <div class="bg-white border-bottom px-4 py-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('budgets') ?>">Budgets</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-edit text-primary me-1"></i> Edit Budget: <?= htmlspecialchars($budget['title']) ?></h4>
            </div>
            <a href="<?= AssetHelper::url('budgets') ?>" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to Budgets
            </a>
        </div>
    </div>

    <div class="p-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <form action="<?= AssetHelper::url("budgets/{$budget['id']}/update") ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                            <div class="row g-3">
                                <!-- Unit Selection -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Target Unit / Department</label>
                                    <select name="unit_id" class="form-select">
                                        <option value="">Church-wide (All Departments)</option>
                                        <?php if (!empty($units)): ?>
                                            <?php foreach ($units as $u): ?>
                                                <option value="<?= $u['id'] ?>" <?= ($budget['unit_id'] == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Category -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Expense Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat ?>" <?= ($budget['category'] === $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Budget Title -->
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Budget Title / Item Name <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($budget['title']) ?>" required>
                                </div>

                                <!-- Total Budget Amount -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Total Budget Target ($) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="total_budget_amount" class="form-control" value="<?= $budget['total_budget_amount'] ?>" min="1" required>
                                    </div>
                                </div>

                                <!-- Fiscal Year & Period -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Fiscal Year <span class="text-danger">*</span></label>
                                    <input type="number" name="fiscal_year" class="form-control" value="<?= $budget['fiscal_year'] ?>" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Period Type</label>
                                    <select name="period_type" class="form-select">
                                        <option value="annual" <?= ($budget['period_type'] === 'annual') ? 'selected' : '' ?>>Annual</option>
                                        <option value="quarterly" <?= ($budget['period_type'] === 'quarterly') ? 'selected' : '' ?>>Quarterly</option>
                                        <option value="monthly" <?= ($budget['period_type'] === 'monthly') ? 'selected' : '' ?>>Monthly</option>
                                        <option value="custom" <?= ($budget['period_type'] === 'custom') ? 'selected' : '' ?>>Custom</option>
                                    </select>
                                </div>

                                <!-- Start & End Date -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" value="<?= $budget['start_date'] ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control" value="<?= $budget['end_date'] ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active" <?= ($budget['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                                        <option value="draft" <?= ($budget['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
                                        <option value="closed" <?= ($budget['status'] === 'closed') ? 'selected' : '' ?>>Closed</option>
                                    </select>
                                </div>

                                <!-- Description / Notes -->
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Description & Objective Notes</label>
                                    <textarea name="description" rows="3" class="form-control"><?= htmlspecialchars($budget['description'] ?? '') ?></textarea>
                                </div>

                                <div class="col-12 mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                                    <a href="<?= AssetHelper::url('budgets') ?>" class="btn btn-light">Cancel</a>
                                    <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Update Budget</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
