<?php
use App\Utilities\AssetHelper;
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h4 class="text-white mb-1"><?= htmlspecialchars($church['name'] ?? 'My Church') ?></h4>
                <p class="mb-0 text-white-50">Financial Records & History</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">All Transactions</h5>
                    <a href="<?= AssetHelper::url('churches/' . $churchId . '/finance/create') ?>" class="btn btn-success">
                        <i class="bx bx-plus me-1"></i> Add Transaction
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" class="mb-4 bg-light p-3 rounded">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="income" <?= ($filters['type'] ?? '') === 'income' ? 'selected' : '' ?>>Income</option>
                                <option value="expense" <?= ($filters['type'] ?? '') === 'expense' ? 'selected' : '' ?>>Expense</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-filter-alt me-1"></i> Filter Records
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Table -->
                <?php if (empty($transactions)): ?>
                    <div class="text-center py-5">
                        <i class="bx bx-receipt display-4 text-muted mb-3"></i>
                        <h5 class="text-muted">No transactions found</h5>
                        <p class="text-muted">Try adjusting your filters or adding a new transaction.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                    <th>Unit</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($transaction['transaction_date'])) ?></td>
                                        <td>
                                            <span class="badge rounded-pill bg-soft-<?= $transaction['transaction_type'] === 'income' ? 'success text-success' : 'danger text-danger' ?> font-size-12">
                                                <?= ucfirst($transaction['transaction_type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">
                                                <?= htmlspecialchars($transaction['category'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td class="text-dark"><?= htmlspecialchars($transaction['description'] ?? '') ?></td>
                                        <td class="text-end fw-bold text-<?= $transaction['transaction_type'] === 'income' ? 'success' : 'danger' ?>">
                                            ₦<?= number_format($transaction['amount'], 2) ?>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= htmlspecialchars($transaction['unit_name'] ?? 'Church-wide') ?></small>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= AssetHelper::url('churches/' . $churchId . '/finance/' . $transaction['id']) ?>" class="btn btn-sm btn-light">
                                                <i class="bx bx-show align-middle"></i>
                                            </a>
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
