<?php
use App\Utilities\AssetHelper;
?>

<div class="container-fluid p-0">
    <!-- Breadcrumbs -->
    <div class="bg-white border-bottom px-4 py-3">
        <div class="row align-items-center">
            <div class="col-12">
                <ul class="breadcrumb mb-0">
                    <?php foreach ($breadcrumbs as $crumb): ?>
                        <?php if (isset($crumb['url'])): ?>
                            <li class="breadcrumb-item"><a href="<?= AssetHelper::url($crumb['url']) ?>"><?= $crumb['label'] ?></a></li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" aria-current="page"><?= $crumb['label'] ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="p-4">
        <div class="card">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><?= htmlspecialchars($title) ?></h5>
                <div>
                    <span class="badge bg-primary"><?= count($records) ?> Records</span>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" 
                                   value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" 
                                   value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="income" <?= ($filters['type'] ?? '') === 'income' ? 'selected' : '' ?>>Income</option>
                                <option value="expense" <?= ($filters['type'] ?? '') === 'expense' ? 'selected' : '' ?>>Expense</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search title, category..." 
                                   value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Records Table -->
                <div class="table-responsive">
                    <table class="table table-nowrap table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Church</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Unit</th>
                                <th>Recorded By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($records)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No financial records found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($records as $record): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($record['transaction_date'])) ?></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?= htmlspecialchars($record['church_name'] ?? 'Unknown') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($record['title'] ?? '') ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($record['transaction_type'] === 'income'): ?>
                                                <span class="badge bg-success">
                                                    <i class="bx bx-trending-up"></i> Income
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <i class="bx bx-trending-down"></i> Expense
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= htmlspecialchars($record['category'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="<?= $record['transaction_type'] === 'income' ? 'text-success' : 'text-danger' ?>">
                                                <?= $record['transaction_type'] === 'income' ? '+' : '-' ?>$<?= number_format($record['amount'], 2) ?>
                                            </strong>
                                        </td>
                                        <td><?= htmlspecialchars($record['unit_name'] ?? '—') ?></td>
                                        <td>
                                            <?= htmlspecialchars(trim(($record['first_name'] ?? '') . ' ' . ($record['last_name'] ?? ''))) ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= AssetHelper::url('finance/' . $record['church_id'] . '/' . $record['id']) ?>" 
                                                   class="btn btn-outline-primary" title="View">
                                                    <i class="bx bx-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-submit filter changes
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = document.querySelectorAll('select[name="type"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
