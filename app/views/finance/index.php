<?php
use App\Utilities\AssetHelper;

$summary = $summary ?? [];
$records = $records ?? [];
$chartMonthly = $chartMonthly ?? [];
$chartIncomeByCategory = $chartIncomeByCategory ?? [];
$chartExpenseByCategory = $chartExpenseByCategory ?? [];
$period = $period ?? ['period_type' => 'range', 'month' => date('n'), 'year' => date('Y'), 'from_month' => 1, 'from_year' => date('Y'), 'to_month' => date('n'), 'to_year' => date('Y'), 'start_date' => null, 'end_date' => null];
$churchFilter = $churchFilter ?? null;
$churchSummaries = $churchSummaries ?? [];
$chartChurches = $chartChurches ?? [];
$isChurchScoped = !empty($churchFilter);

$totalIncome = 0;
$totalExpense = 0;
foreach ($summary as $item) {
    if ($item['transaction_type'] === 'income') {
        $totalIncome = (float)$item['total'];
    } elseif ($item['transaction_type'] === 'expense') {
        $totalExpense = (float)$item['total'];
    }
}
$netTotal = $totalIncome - $totalExpense;

$months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];
$years = range((int)date('Y') - 10, (int)date('Y') + 1);
$financeUrl = AssetHelper::url('finance');
$baseQuery = $churchFilter ? '?church_id=' . (int)$churchFilter['id'] . '&' : '?';
?>
<?php if (!empty($churchFilter)): ?>
<div class="alert alert-info d-flex align-items-center justify-content-between mb-3" role="alert">
    <span><i class="bx bx-church me-2"></i>Viewing church: <strong><?= htmlspecialchars($churchFilter['name']) ?></strong></span>
    <a href="<?= AssetHelper::url('finance') ?>" class="btn btn-sm btn-outline-primary">View all</a>
</div>
<?php endif; ?>

<!-- Period selector -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="bx bx-calendar me-1"></i> Report period</h5>
        <div class="btn-group">
            <?php
            // Export should hit the dedicated /finance/export endpoint
            $exportBaseUrl = AssetHelper::url('finance/export');
            $exportQuery = $_GET ?? [];
            unset($exportQuery['page']); // remove any pagination if present
            $queryString = http_build_query($exportQuery);
            $queryPrefix = $queryString ? ($queryString . '&') : '';
            ?>
            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i data-feather="download" class="me-1"></i> Export
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="<?= $exportBaseUrl . '?' . $queryPrefix . 'format=csv' ?>">
                    CSV
                </a>
                <a class="dropdown-item" href="<?= $exportBaseUrl . '?' . $queryPrefix . 'format=excel' ?>">
                    Excel (.xls)
                </a>
                <a class="dropdown-item" href="<?= $exportBaseUrl . '?' . $queryPrefix . 'format=json' ?>">
                    JSON
                </a>
                <a class="dropdown-item" href="<?= $exportBaseUrl . '?' . $queryPrefix . 'format=pdf' ?>">
                    PDF
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="get" action="<?= $financeUrl ?>" id="period-form" class="row g-3 align-items-end">
            <?php if ($churchFilter): ?>
                <input type="hidden" name="church_id" value="<?= (int)$churchFilter['id'] ?>">
            <?php endif; ?>
            <div class="col-auto">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="period" id="period-month" value="month" <?= ($period['period_type'] ?? '') === 'month' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="period-month">Single month</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="period" id="period-range" value="range" <?= ($period['period_type'] ?? '') === 'range' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="period-range">Date range</label>
                </div>
            </div>
            <div id="single-month-fields" class="col-auto" style="<?= ($period['period_type'] ?? '') !== 'month' ? 'display:none' : '' ?>">
                <label for="month" class="form-label small mb-0">Month</label>
                <select name="month" id="month" class="form-select form-select-sm" style="width: auto;">
                    <?php foreach ($months as $num => $name): ?>
                        <option value="<?= $num ?>" <?= (int)($period['month'] ?? 0) === $num ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="single-year-fields" class="col-auto" style="<?= ($period['period_type'] ?? '') !== 'month' ? 'display:none' : '' ?>">
                <label for="year" class="form-label small mb-0">Year</label>
                <select name="year" id="year" class="form-select form-select-sm" style="width: auto;">
                    <?php foreach ($years as $y): ?>
                        <option value="<?= $y ?>" <?= (int)($period['year'] ?? 0) === $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="range-fields" class="col-auto d-flex flex-wrap align-items-end gap-2" style="<?= ($period['period_type'] ?? '') !== 'range' ? 'display:none' : '' ?>">
                <div>
                    <label class="form-label small mb-0">From</label>
                    <div class="d-flex gap-1">
                        <select name="from_month" class="form-select form-select-sm" style="width: auto;">
                            <?php foreach ($months as $num => $name): ?>
                                <option value="<?= $num ?>" <?= (int)($period['from_month'] ?? 1) === $num ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="from_year" class="form-select form-select-sm" style="width: auto;">
                            <?php foreach ($years as $y): ?>
                                <option value="<?= $y ?>" <?= (int)($period['from_year'] ?? date('Y')) === $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="form-label small mb-0">To</label>
                    <div class="d-flex gap-1">
                        <select name="to_month" class="form-select form-select-sm" style="width: auto;">
                            <?php foreach ($months as $num => $name): ?>
                                <option value="<?= $num ?>" <?= (int)($period['to_month'] ?? date('n')) === $num ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="to_year" class="form-select form-select-sm" style="width: auto;">
                            <?php foreach ($years as $y): ?>
                                <option value="<?= $y ?>" <?= (int)($period['to_year'] ?? date('Y')) === $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-filter-alt me-1"></i> Apply</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary cards -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Income</span>
                        <h4 class="mb-3 text-success">$<?= number_format($totalIncome, 2) ?></h4>
                    </div>
                    <div class="flex-shrink-0">
                        <i data-feather="arrow-down-circle" class="icon-lg text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Expense</span>
                        <h4 class="mb-3 text-danger">$<?= number_format($totalExpense, 2) ?></h4>
                    </div>
                    <div class="flex-shrink-0">
                        <i data-feather="arrow-up-circle" class="icon-lg text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Net Total</span>
                        <h4 class="mb-3 <?= $netTotal >= 0 ? 'text-success' : 'text-danger' ?>">$<?= number_format($netTotal, 2) ?></h4>
                    </div>
                    <div class="flex-shrink-0">
                        <i data-feather="dollar-sign" class="icon-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Transactions</span>
                        <h4 class="mb-3"><?= count($records) ?></h4>
                    </div>
                    <div class="flex-shrink-0">
                        <i data-feather="file-text" class="icon-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <?= $isChurchScoped ? 'Income vs Expense by month' : 'Income vs Expense (all churches)' ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="financeTrendChart"></canvas>
                    <?php if (empty($chartMonthly)): ?>
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;">
                            No transaction data for this period.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Income by category</h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 280px;">
                    <canvas id="incomeCategoryChart"></canvas>
                    <?php if (empty($chartIncomeByCategory)): ?>
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;">
                            No income in this period.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Expense by category</h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 280px;">
                    <canvas id="expenseCategoryChart"></canvas>
                    <?php if (empty($chartExpenseByCategory)): ?>
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;">
                            No expenses in this period.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$isChurchScoped): ?>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Income vs Expense by church</h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 280px;">
                    <canvas id="churchComparisonChart"></canvas>
                    <?php if (empty($chartChurches)): ?>
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;">
                            No church finance data for this period.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Records table -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <?php if ($isChurchScoped): ?>
                    <h4 class="card-title">Finance records</h4>
                    <p class="card-title-desc">Transactions in selected period</p>
                <?php else: ?>
                    <h4 class="card-title">Church finance overview</h4>
                    <p class="card-title-desc">Summary by church in selected period</p>
                <?php endif; ?>
                <div class="d-flex gap-2">
                    <?php
                    // Preserve church context when creating a new record.
                    $createFinanceUrl = $churchFilter
                        ? AssetHelper::url('finance/create?church_id=' . (int)$churchFilter['id'])
                        : AssetHelper::url('finance/create');
                    ?>
                    <?php if ($isChurchScoped): ?>
                        <a href="<?= $createFinanceUrl ?>" class="btn btn-primary">
                            <i data-feather="plus-circle" class="me-1"></i> Create record
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if ($isChurchScoped): ?>
                    <table id="finance-datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($record['transaction_date'])) ?></td>
                                    <td>
                                        <?php if ($record['transaction_type'] === 'income'): ?>
                                            <span class="badge bg-success">Income</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Expense</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="<?= $record['transaction_type'] === 'income' ? 'text-success' : 'text-danger' ?>">
                                            <?= $record['transaction_type'] === 'income' ? '+' : '-' ?>$<?= number_format($record['amount'], 2) ?>
                                        </strong>
                                    </td>
                                    <td><span class="badge bg-info"><?= ucfirst(htmlspecialchars($record['category'] ?? '')) ?></span></td>
                                    <td><?= htmlspecialchars($record['unit_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($record['description'] ?: '—') ?></td>
                                    <td>
                                        <a href="<?= AssetHelper::url('finance/' . $record['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i data-feather="eye" class="icon-sm"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <table id="finance-datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Church</th>
                                <th>Total Income</th>
                                <th>Total Expense</th>
                                <th>Net</th>
                                <th>Transactions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($churchSummaries as $row): ?>
                                <?php
                                $income = (float)($row['total_income'] ?? 0);
                                $expense = (float)($row['total_expense'] ?? 0);
                                $net = $income - $expense;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['church_name'] ?? 'Unknown') ?></td>
                                    <td class="text-success">$<?= number_format($income, 2) ?></td>
                                    <td class="text-danger">$<?= number_format($expense, 2) ?></td>
                                    <td class="<?= $net >= 0 ? 'text-success' : 'text-danger' ?>">
                                        $<?= number_format($net, 2) ?>
                                    </td>
                                    <td><?= (int)($row['transaction_count'] ?? 0) ?></td>
                                    <td>
                                        <?php if (!empty($row['church_id'])): ?>
                                            <a href="<?= AssetHelper::url('finance?church_id=' . (int)$row['church_id']) ?>" class="btn btn-sm btn-outline-primary">
                                                View details
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$extraCss = [
    'libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
    'libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'
];

$extraJs = [
    'libs/datatables.net/js/jquery.dataTables.min.js',
    'libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
    'libs/datatables.net-responsive/js/dataTables.responsive.min.js',
    'libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js',
    'pages/datatables.init.js'
];

$chartMonthlyJson = json_encode($chartMonthly);
$chartIncomeJson = json_encode($chartIncomeByCategory);
$chartExpenseJson = json_encode($chartExpenseByCategory);
$chartChurchesJson = json_encode($chartChurches);

$pageJs = <<<JS
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    var chartMonthly = {$chartMonthlyJson};
    var chartIncome = {$chartIncomeJson};
    var chartExpense = {$chartExpenseJson};
    var chartChurches = {$chartChurchesJson};

    // Period form toggle
    document.querySelectorAll('input[name="period"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var isMonth = this.value === 'month';
            document.getElementById('single-month-fields').style.display = isMonth ? 'block' : 'none';
            document.getElementById('single-year-fields').style.display = isMonth ? 'block' : 'none';
            document.getElementById('range-fields').style.display = isMonth ? 'none' : 'flex';
        });
    });

    // Trend chart (Income vs Expense by month)
    var trendCtx = document.getElementById('financeTrendChart');
    if (trendCtx && chartMonthly.length > 0) {
        var labels = chartMonthly.map(function(m) { return m.label; });
        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Income', data: chartMonthly.map(function(m) { return m.income; }), backgroundColor: 'rgba(42, 181, 125, 0.8)', borderColor: '#2ab57d', borderWidth: 1 },
                    { label: 'Expense', data: chartMonthly.map(function(m) { return m.expense; }), backgroundColor: 'rgba(253, 98, 94, 0.8)', borderColor: '#fd625e', borderWidth: 1 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { position: 'top' } }
            }
        });
    }

    // Income by category (doughnut)
    var incomeCtx = document.getElementById('incomeCategoryChart');
    if (incomeCtx && chartIncome.length) {
        var colors = ['#2ab57d', '#5156be', '#4ba6ef', '#ffbf53', '#fd625e', '#6c757d', '#17a2b8', '#e83e8c'];
        new Chart(incomeCtx, {
            type: 'doughnut',
            data: {
                labels: chartIncome.map(function(c) { return (c.category || 'Other').replace(/_/g, ' '); }),
                datasets: [{ data: chartIncome.map(function(c) { return c.total; }), backgroundColor: colors.slice(0, chartIncome.length) }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
        });
    }

    // Expense by category (doughnut)
    var expenseCtx = document.getElementById('expenseCategoryChart');
    if (expenseCtx && chartExpense.length) {
        var colors = ['#fd625e', '#ffbf53', '#5156be', '#6c757d', '#17a2b8', '#2ab57d', '#e83e8c', '#4ba6ef'];
        new Chart(expenseCtx, {
            type: 'doughnut',
            data: {
                labels: chartExpense.map(function(c) { return (c.category || 'Other').replace(/_/g, ' '); }),
                datasets: [{ data: chartExpense.map(function(c) { return c.total; }), backgroundColor: colors.slice(0, chartExpense.length) }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
        });
    }

    // Income vs Expense by church (global view only)
    var churchCtx = document.getElementById('churchComparisonChart');
    if (churchCtx && chartChurches && chartChurches.length) {
        var churchLabels = chartChurches.map(function(c) { return c.label; });
        var churchIncome = chartChurches.map(function(c) { return c.income; });
        var churchExpense = chartChurches.map(function(c) { return c.expense; });
        var churchNet = chartChurches.map(function(c) { return c.net; });

        new Chart(churchCtx, {
            type: 'bar',
            data: {
                labels: churchLabels,
                datasets: [
                    {
                        label: 'Income',
                        data: churchIncome,
                        backgroundColor: 'rgba(42, 181, 125, 0.8)',
                        borderColor: '#2ab57d',
                        borderWidth: 1
                    },
                    {
                        label: 'Expense',
                        data: churchExpense,
                        backgroundColor: 'rgba(253, 98, 94, 0.8)',
                        borderColor: '#fd625e',
                        borderWidth: 1
                    },
                    {
                        label: 'Net',
                        data: churchNet,
                        backgroundColor: 'rgba(81, 86, 190, 0.8)',
                        borderColor: '#5156be',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { position: 'top' } }
            }
        });
    }
})();
</script>
<script>
    $(document).ready(function() {
        $('#finance-datatable').DataTable({
            responsive: true,
            order: [[0, 'desc']],
            pageLength: 25,
            language: { search: "", searchPlaceholder: "Search records..." }
        });
    });
</script>
JS;
?>
