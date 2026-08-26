<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0"><?= htmlspecialchars($church['name'] ?? 'My Church') ?></h4>
                    <p class="text-muted mb-0">Add New Transaction</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Transaction Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= AssetHelper::url('churches/' . $churchId . '/finance') ?>">
                        <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                        
                        <!-- Transaction Type -->
                        <div class="mb-3">
                            <label class="form-label">Transaction Type <span class="text-danger">*</span></label>
                            <select name="transaction_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                            </select>
                        </div>

                        <!-- Amount -->
                        <div class="mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <input type="text" name="category" class="form-control" placeholder="e.g., Tithe, Offering, Utilities, etc." required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <!-- Transaction Date -->
                        <div class="mb-3">
                            <label class="form-label">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="check">Check</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="online">Online Payment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Reference Number -->
                        <div class="mb-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control" placeholder="Check #, Transaction ID, etc.">
                        </div>

                        <!-- Unit Assignment -->
                        <div class="mb-3">
                            <label class="form-label">Assign to Unit (Optional)</label>
                            <select name="unit_id" class="form-control">
                                <option value="">Church-wide</option>
                                <?php foreach ($units ?? [] as $unit): ?>
                                    <option value="<?= $unit['id'] ?>"><?= htmlspecialchars($unit['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Member Assignment -->
                        <div class="mb-3">
                            <label class="form-label">Member (Optional)</label>
                            <select name="member_id" class="form-control">
                                <option value="">None</option>
                                <?php foreach ($members ?? [] as $member): ?>
                                    <option value="<?= $member['id'] ?>">
                                        <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Save Transaction
                            </button>
                            <a href="<?= AssetHelper::url('churches/' . $churchId . '/finance') ?>" class="btn btn-secondary">
                                <i class="bx bx-x"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Guidelines</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Tips:</strong>
                        <ul class="mb-0 mt-2">
                            <li>All fields marked with * are required</li>
                            <li>Choose the correct transaction type</li>
                            <li>Use clear, descriptive descriptions</li>
                            <li>Include reference numbers when available</li>
                        </ul>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h6>Common Categories</h6>
                            <small class="text-muted">Income:</small>
                            <ul class="small">
                                <li>Tithe</li>
                                <li>Offering</li>
                                <li>Donations</li>
                                <li>Event Income</li>
                            </ul>
                            <small class="text-muted">Expense:</small>
                            <ul class="small">
                                <li>Utilities</li>
                                <li>Salaries</li>
                                <li>Maintenance</li>
                                <li>Ministry Expenses</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
