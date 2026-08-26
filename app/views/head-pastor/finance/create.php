<?php
use App\Utilities\AssetHelper;
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h4 class="text-white mb-1"><?= htmlspecialchars($church['name'] ?? 'My Church') ?></h4>
                <p class="mb-0 text-white-50">Add New Financial Transaction</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">Transaction Details</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= AssetHelper::url('churches/' . $churchId . '/finance') ?>">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                    
                    <div class="row">
                        <!-- Transaction Type -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Transaction Type <span class="text-danger">*</span></label>
                            <select name="transaction_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                            </select>
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                        <input type="text" name="category" class="form-control" placeholder="e.g., Tithe, Offering, Utilities, Maintenance" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Provide details about this transaction" required></textarea>
                    </div>

                    <div class="row">
                        <!-- Transaction Date -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="check">Check</option>
                                <option value="online">Online Payment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Unit/Member Assignment -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Assign to Unit</label>
                            <select name="unit_id" class="form-select">
                                <option value="">Church-wide</option>
                                <?php foreach ($units ?? [] as $unit): ?>
                                    <option value="<?= $unit['id'] ?>"><?= htmlspecialchars($unit['unit_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Member (Optional)</label>
                            <select name="member_id" class="form-select">
                                <option value="">None</option>
                                <?php foreach ($members ?? [] as $member): ?>
                                    <option value="<?= $member['id'] ?>">
                                        <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bx bx-save me-1"></i> Save Transaction
                        </button>
                        <a href="<?= AssetHelper::url('churches/' . $churchId . '/finance') ?>" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Help Sidebar -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-info bg-soft-info">
            <div class="card-header bg-transparent">
                <h5 class="card-title text-info mb-0"><i class="bx bx-info-circle me-1"></i> Guidelines</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="bx bx-check-circle text-success me-2"></i> Ensure all <strong>* required</strong> fields are filled correctly.</li>
                    <li class="mb-2"><i class="bx bx-check-circle text-success me-2"></i> Double-check the <strong>amount</strong> and <strong>type</strong>.</li>
                    <li class="mb-2"><i class="bx bx-check-circle text-success me-2"></i> Use descriptive categories for better reporting.</li>
                    <li><i class="bx bx-check-circle text-success me-2"></i> Assigning a unit helps in department-level accounting.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
