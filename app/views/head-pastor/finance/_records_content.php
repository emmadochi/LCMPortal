<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0"><?= htmlspecialchars($church['name'] ?? 'My Church') ?></h4>
                    <p class="text-muted mb-0">Financial Records</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">All Transactions</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="<?= AssetHelper::url('churches/' . $churchId . '/finance/create') ?>" class="btn btn-success">
                                <i class="bx bx-plus"></i> Add Transaction
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <?php if (!empty($filters)): ?>
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label>Type</label>
                                <select name="type" class="form-control">
                                    <option value="">All</option>
                                    <option value="income" <?= ($filters['type'] ?? '') === 'income' ? 'selected' : '' ?>>Income</option>
                                    <option value="expense" <?= ($filters['type'] ?? '') === 'expense' ? 'selected' : '' ?>>Expense</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bx bx-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>

                    <!-- Table -->
                    <?php if (empty($transactions)): ?>
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle"></i> No transactions found.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Unit</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td><?= date('M j, Y', strtotime($transaction['transaction_date'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $transaction['transaction_type'] === 'income' ? 'success' : 'danger' ?>">
                                                    <?= ucfirst($transaction['transaction_type']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($transaction['category'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($transaction['description'] ?? '') ?></td>
                                            <td class="text-<?= $transaction['transaction_type'] === 'income' ? 'success' : 'danger' ?>">
                                                ₦<?= number_format($transaction['amount'], 2) ?>
                                            </td>
                                            <td><?= htmlspecialchars($transaction['unit_name'] ?? 'Church-wide') ?></td>
                                            <td>
                                                <a href="<?= AssetHelper::url('churches/' . $churchId . '/finance/' . $transaction['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bx bx-show"></i>
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
</div>
