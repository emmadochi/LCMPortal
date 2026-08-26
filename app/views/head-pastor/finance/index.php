<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
?>

<div class="row">
    <div class="col-12">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h3 class="text-white mb-2">Financial Dashboard</h3>
                        <p class="mb-0 text-white-50">Monitoring the financial health and stewardship of <?= htmlspecialchars($church['name'] ?? 'My Church') ?></p>
                    </div>
                    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                        <div class="d-flex gap-2 justify-content-sm-end">
                            <a href="<?= AssetHelper::url("churches/{$churchId}/finance/create") ?>" class="btn btn-success">
                                <i class="bx bx-plus me-1"></i> Add Transaction
                            </a>
                            <a href="<?= AssetHelper::url("churches/{$churchId}/finance/records") ?>" class="btn btn-light">
                                <i class="bx bx-list-ul me-1"></i> View Records
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card mini-stats-wid">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-success text-success font-size-24">
                        <i class="bx bx-trending-up"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">Total Income</h5>
                <h3 class="mb-0 text-success">₦<?= number_format($incomeTotal ?? 0, 2) ?></h3>
                <small class="text-muted">All time revenue</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mini-stats-wid">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-soft-danger text-danger font-size-24">
                        <i class="bx bx-trending-down"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">Total Expenses</h5>
                <h3 class="mb-0 text-danger">₦<?= number_format($expenseTotal ?? 0, 2) ?></h3>
                <small class="text-muted">All time expenses</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mini-stats-wid">
            <div class="card-body text-center">
                <div class="avatar-sm mx-auto mb-3">
                    <span class="avatar-title rounded-circle <?= ($balance ?? 0) >= 0 ? 'bg-soft-primary text-primary' : 'bg-soft-warning text-warning' ?> font-size-24">
                        <i class="bx bx-wallet"></i>
                    </span>
                </div>
                <h5 class="text-muted mb-2">Net Balance</h5>
                <h3 class="mb-0 <?= ($balance ?? 0) >= 0 ? 'text-primary' : 'text-warning' ?>">₦<?= number_format($balance ?? 0, 2) ?></h3>
                <small class="text-muted"><?= ($balance ?? 0) >= 0 ? 'Positive balance' : 'Negative balance' ?></small>
            </div>
        </div>
    </div>
</div>

<!-- Financial Trend Chart -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-transparent border-bottom">
                <h4 class="card-title mb-0">Financial Trend (6 Months)</h4>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 350px;">
                    <canvas id="financeTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
<!-- Summary by Unit -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h4 class="card-title mb-0"><i class="bx bx-briefcase me-2 text-primary"></i>Financial Summary by Unit</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Unit Name</th>
                                <th class="text-end">Total Income</th>
                                <th class="text-end">Total Expenses</th>
                                <th class="text-end">Net Position</th>
                                <th class="text-center">Transactions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unitSummaries ?? [] as $unit): ?>
                                <tr>
                                    <td>
                                        <a href="<?= AssetHelper::url("churches/{$churchId}/performance/{$unit['unit_id']}") ?>" class="text-primary fw-bold">
                                            <?= htmlspecialchars($unit['unit_name']) ?>
                                        </a>
                                    </td>
                                    <td class="text-end text-success">₦<?= number_format($unit['total_income'], 2) ?></td>
                                    <td class="text-end text-danger">₦<?= number_format($unit['total_expense'], 2) ?></td>
                                    <td class="text-end fw-bold <?= ($unit['total_income'] - $unit['total_expense']) >= 0 ? 'text-primary' : 'text-warning' ?>">
                                        ₦<?= number_format($unit['total_income'] - $unit['total_expense'], 2) ?>
                                    </td>
                                    <td class="text-center"><?= $unit['transaction_count'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($unitSummaries)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No unit-level financial data found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Transactions -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                <h4 class="card-title mb-0">Recent Transactions</h4>
                <a href="<?= AssetHelper::url("churches/{$churchId}/finance/records") ?>" class="btn btn-sm btn-link">View all</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentTransactions ?? [] as $transaction): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($transaction['transaction_date'])) ?></td>
                                    <td>
                                        <span class="badge rounded-pill bg-soft-<?= $transaction['transaction_type'] === 'income' ? 'success text-success' : 'danger text-danger' ?> font-size-12">
                                            <?= ucfirst($transaction['transaction_type']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($transaction['description'] ?? $transaction['title'] ?? '') ?></td>
                                    <td class="text-end fw-bold text-<?= $transaction['transaction_type'] === 'income' ? 'success' : 'danger' ?>">
                                        ₦<?= number_format($transaction['amount'], 2) ?>
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
                <?php if (empty($recentTransactions)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">No transactions found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Category Distribution -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-transparent border-bottom">
                <h4 class="card-title mb-0">Expense Distribution</h4>
            </div>
            <div class="card-body">
                <?php if (empty($expenseCategories)): ?>
                    <p class="text-center text-muted py-5">No category data available</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-nowrap align-middle mb-0">
                            <tbody>
                                <?php foreach (array_slice($expenseCategories, 0, 8) as $cat): ?>
                                    <tr>
                                        <td><i class="bx bx-circle me-1 text-danger font-size-10"></i> <?= htmlspecialchars($cat['category']) ?></td>
                                        <td class="text-end font-size-13 fw-bold">₦<?= number_format($cat['total'], 2) ?></td>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('financeTrendChart');
    if (!ctx) return;

    const chartData = <?= json_encode($monthlyData ?? []) ?>;
    const labels = chartData.map(d => d.label);
    const income = chartData.map(d => d.income);
    const expense = chartData.map(d => d.expense);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Income',
                    data: income,
                    backgroundColor: '#34c38f',
                    borderRadius: 4
                },
                {
                    label: 'Expense',
                    data: expense,
                    backgroundColor: '#f46a6a',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, padding: 20 } }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { callback: function(value) { return '₦' + value.toLocaleString(); } }
                },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
