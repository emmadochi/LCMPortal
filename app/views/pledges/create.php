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
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('pledges') ?>">Pledges</a></li>
                        <li class="breadcrumb-item active">Record Pledge</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-gift text-success me-1"></i> Record Member / Donor Pledge</h4>
            </div>
            <a href="<?= AssetHelper::url('pledges') ?>" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to Pledges
            </a>
        </div>
    </div>

    <div class="p-4">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <form action="<?= AssetHelper::url('pledges') ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                            <!-- Church Selection -->
                            <?php if ($this->session->hasPermission('manage_users') && !empty($churches)): ?>
                            <div class="mb-3">
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

                            <!-- Donor Section -->
                            <div class="card bg-light border-0 rounded-3 mb-4">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold text-dark mb-3"><i class="bx bx-user me-1 text-primary"></i> Donor Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Registered Member</label>
                                            <select name="member_id" class="form-select select2" id="member_select">
                                                <option value="">-- Select Church Member --</option>
                                                <?php if (!empty($members)): ?>
                                                    <?php foreach ($members as $m): ?>
                                                        <option value="<?= $m['id'] ?>">
                                                            <?= htmlspecialchars($m['first_name'] . ' ' . $m['last_name']) ?> (<?= htmlspecialchars($m['email'] ?: $m['phone'] ?: 'ID #' . $m['id']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <small class="text-muted">Select if donor is a registered portal member</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Guest / External Donor Name</label>
                                            <input type="text" name="donor_name" class="form-control" placeholder="Enter name if non-member donor">
                                            <small class="text-muted">Used when donor is not registered</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Donor Email</label>
                                            <input type="email" name="donor_email" class="form-control" placeholder="donor@example.com">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Donor Phone</label>
                                            <input type="text" name="donor_phone" class="form-control" placeholder="+1 ...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pledge Details -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Campaign / Purpose Name <span class="text-danger">*</span></label>
                                    <input type="text" name="campaign_name" class="form-control" placeholder="e.g. 2026 Building Fund, Youth Camp Sponsorship, Missions" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Target Commitment Amount ($) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="target_amount" class="form-control" placeholder="0.00" min="1" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Due Date (Optional)</label>
                                    <input type="date" name="due_date" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Giving Frequency</label>
                                    <select name="frequency" class="form-select">
                                        <option value="one_time">One-time</option>
                                        <option value="monthly">Monthly Installments</option>
                                        <option value="weekly">Weekly Installments</option>
                                        <option value="quarterly">Quarterly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Commitment Notes & Special Requests</label>
                                    <textarea name="notes" rows="2" class="form-control" placeholder="e.g. Pledged during Sunday Anniversary Service"></textarea>
                                </div>
                            </div>

                            <!-- Initial Payment Section (Optional) -->
                            <div class="card border border-success border-opacity-25 rounded-3 mb-4 bg-soft-success">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold text-success mb-2"><i class="bx bx-dollar me-1"></i> Initial Payment (Optional)</h6>
                                    <p class="small text-muted mb-3">If the donor is making an initial payment right now, enter the details below to log the payment and generate an accounting record automatically.</p>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Initial Payment Amount ($)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" name="initial_payment" class="form-control" placeholder="0.00" min="0">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Payment Method</label>
                                            <select name="initial_payment_method" class="form-select">
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="cash">Cash</option>
                                                <option value="pos">POS / Card</option>
                                                <option value="online">Online</option>
                                                <option value="cheque">Cheque</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Reference Number</label>
                                            <input type="text" name="initial_payment_ref" class="form-control" placeholder="e.g. TRF-102938">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                                <a href="<?= AssetHelper::url('pledges') ?>" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-success px-4"><i class="bx bx-check me-1"></i> Save Pledge Commitment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
