<!-- Sub-header with Assignment Selector -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #5b73e8 0%, #4430e7 100%);">
            <div class="card-body p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-white mb-1 fw-bold">Unit Workspace: <?= htmlspecialchars($unitName) ?></h3>
                        <p class="text-white-50 mb-0 font-size-14"><i class="bx bx-church me-1"></i> Branch: <?= htmlspecialchars($churchName) ?></p>
                    </div>
                    <?php if (count($assignments) > 1): ?>
                    <div class="col-md-4 mt-3 mt-md-0">
                        <div class="d-flex justify-content-md-end align-items-center">
                            <label class="text-white-50 me-2 text-nowrap font-size-13 mb-0">Switch Workspace:</label>
                            <select name="switch_assignment" class="form-select form-select-sm bg-white text-dark border-0 shadow-sm" style="width: auto; min-width: 180px;" onchange="window.location.href = window.location.pathname + '?church_id=' + this.value.split('-')[0] + '&unit_id=' + this.value.split('-')[1];">
                                <?php foreach ($assignments as $assign): ?>
                                    <option value="<?= $assign['church_id'] ?>-<?= $assign['unit_id'] ?>" <?= ((int)$assign['church_id'] === (int)$churchId && (int)$assign['unit_id'] === (int)$unitId) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($assign['unit_name'] . ' (' . $assign['church_name'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ledger Card -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                <h5 class="mb-0 fw-bold"><i class="bx bx-receipt me-2 text-primary"></i>Finance Ledger</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newTransactionModal">
                    <i class="bx bx-plus me-1"></i>Record Transaction
                </button>
            </div>
            <div class="card-body px-4 pb-4">
                <?php if (empty($records)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bx bx-receipt display-2"></i>
                        <h5 class="mt-3">No Financial Records Found</h5>
                        <p>No transactions have been logged for this department branch yet.</p>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newTransactionModal">
                            Record First Transaction
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Transaction Date</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Method</th>
                                    <th>Ref. Number</th>
                                    <th>Recorded By</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($records as $record): ?>
                                    <tr>
                                        <td class="text-muted"><?= date('M j, Y', strtotime($record['transaction_date'])) ?></td>
                                        <td>
                                            <?php if ($record['transaction_type'] === 'income'): ?>
                                                <span class="badge bg-soft-success text-success font-size-11"><i class="bx bx-trending-up me-1"></i>Income</span>
                                            <?php else: ?>
                                                <span class="badge bg-soft-danger text-danger font-size-11"><i class="bx bx-trending-down me-1"></i>Expense</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-dark fw-semibold"><?= htmlspecialchars($record['category']) ?></td>
                                        <td class="text-muted" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($record['description'] ?? '') ?>">
                                            <?= htmlspecialchars($record['description'] ?: '—') ?>
                                        </td>
                                        <td><span class="text-muted"><?= ucfirst(htmlspecialchars($record['payment_method'])) ?></span></td>
                                        <td><span class="font-size-12 font-monospace"><?= htmlspecialchars($record['reference_number'] ?: '—') ?></span></td>
                                        <td class="text-dark"><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></td>
                                        <td class="text-end fw-bold <?= $record['transaction_type'] === 'income' ? 'text-success' : 'text-danger' ?>">
                                            <?= $record['transaction_type'] === 'income' ? '+' : '-' ?>$<?= number_format($record['amount'], 2) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Record Transaction Modal -->
<div class="modal fade" id="newTransactionModal" tabindex="-1" aria-labelledby="newTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="newTransactionModalLabel">Record Transaction</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= \App\Utilities\AssetHelper::url("my-unit/finance/store") ?>">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="church_id" value="<?= $churchId ?>">
                <input type="hidden" name="unit_id" value="<?= $unitId ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Transaction Type <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="transaction_type" id="type_income" value="income" checked>
                                <label class="form-check-label text-success fw-semibold" for="type_income">
                                    <i class="bx bx-trending-up me-1"></i>Income (Offering, Donation)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="transaction_type" id="type_expense" value="expense">
                                <label class="form-check-label text-danger fw-semibold" for="type_expense">
                                    <i class="bx bx-trending-down me-1"></i>Expense (Budget, Purchases)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control form-control-lg" id="amount" name="amount" placeholder="0.00" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="Departmental Budget">Departmental Budget</option>
                                <option value="Unit Offering">Unit Offering</option>
                                <option value="Special Donation">Special Donation</option>
                                <option value="Equipment Purchase">Equipment Purchase</option>
                                <option value="Event Expense">Event Expense</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="transaction_date" class="form-label">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="transaction_date" name="transaction_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method">
                                <option value="cash">Cash</option>
                                <option value="check">Check</option>
                                <option value="card">Debit/Credit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reference_number" class="form-label">Ref / Check Number</label>
                            <input type="text" class="form-control" id="reference_number" name="reference_number" placeholder="Optional">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description / Purpose</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Provide context about this transaction..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>
