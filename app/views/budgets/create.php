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
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-plus-circle text-primary me-1"></i> Add New Budget Allocation</h4>
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
                        <form action="<?= AssetHelper::url('budgets') ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                            <div class="row g-3">
                                <!-- Church Selection (for Admins) -->
                                <?php if ($this->session->hasPermission('manage_users') && !empty($churches)): ?>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Church / Branch <span class="text-danger">*</span></label>
                                    <select name="church_id" class="form-select select2" required>
                                        <?php foreach ($churches as $c): ?>
                                            <option value="<?= $c['id'] ?>" <?= ($churchId == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php else: ?>
                                    <input type="hidden" name="church_id" value="<?= $churchId ?>">
                                <?php endif; ?>

                                <!-- Unit Selection -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Target Unit / Department</label>
                                    <select name="unit_id" class="form-select">
                                        <option value="">Church-wide (All Departments)</option>
                                        <?php if (!empty($units)): ?>
                                            <?php foreach ($units as $u): ?>
                                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <small class="text-muted">Leave blank if this is a general church budget.</small>
                                </div>

                                <!-- Budget Title -->
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Budget Title / Item Name <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. 2026 Annual Missions & Outreach Allocation" required>
                                </div>

                                <!-- Category -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Expense Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat ?>"><?= $cat ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Total Budget Amount -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Total Budget Target ($) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="total_budget_amount" class="form-control" placeholder="0.00" min="1" required>
                                    </div>
                                </div>

                                <!-- Fiscal Year & Period -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Fiscal Year <span class="text-danger">*</span></label>
                                    <input type="number" name="fiscal_year" class="form-control" value="<?= date('Y') ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Period Type</label>
                                    <select name="period_type" class="form-select">
                                        <option value="annual">Annual</option>
                                        <option value="quarterly">Quarterly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active" selected>Active</option>
                                        <option value="draft">Draft</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>

                                <!-- Start & End Date -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" value="<?= date('Y-01-01') ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control" value="<?= date('Y-12-31') ?>" required>
                                </div>

                                <!-- Description / Notes -->
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Description & Objective Notes</label>
                                    <textarea name="description" rows="3" class="form-control" placeholder="Provide background information or allocation rules for this budget..."></textarea>
                                </div>

                                <div class="col-12 mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                                    <a href="<?= AssetHelper::url('budgets') ?>" class="btn btn-light">Cancel</a>
                                    <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Save Budget</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
