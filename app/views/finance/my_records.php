<?php
use App\Utilities\AssetHelper;

$totalContribution = array_sum(array_column($records, 'amount'));
$currentYearRecords = array_filter($records, function($r) {
    return date('Y', strtotime($r['transaction_date'])) === date('Y');
});
$currentYearContribution = array_sum(array_column($currentYearRecords, 'amount'));
?>

<div class="container-fluid p-0">
    <div class="bg-white border-bottom px-4 py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('profile') ?>">Personal Space</a></li>
                        <li class="breadcrumb-item active">My Giving</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-money text-success me-1"></i> My Giving History & Contributions</h4>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="<?= AssetHelper::url('giving/my-pledges') ?>" class="btn btn-outline-success">
                    <i class="bx bx-gift me-1"></i> My Pledges
                </a>
                <a href="<?= AssetHelper::url('giving/my-records/export/csv') ?>" class="btn btn-outline-secondary">
                    <i class="bx bx-download me-1"></i> Export CSV
                </a>
                <a href="<?= AssetHelper::url('giving/my-records/export/pdf') ?>" class="btn btn-outline-danger">
                    <i class="bx bxs-file-pdf me-1"></i> Annual Statement (PDF)
                </a>
            </div>
        </div>
    </div>

    <div class="p-4">
        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 bg-gradient" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-white-50 small text-uppercase fw-semibold">Lifetime Contributions</span>
                            <i class="bx bx-coin-stack font-size-22"></i>
                        </div>
                        <h3 class="mb-0 text-white fw-bold">$<?= number_format($totalContribution, 2) ?></h3>
                        <small class="text-white-50"><?= count($records) ?> Total Transactions</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 bg-gradient" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-white-50 small text-uppercase fw-semibold"><?= date('Y') ?> Year-to-Date Giving</span>
                            <i class="bx bx-calendar font-size-22"></i>
                        </div>
                        <h3 class="mb-0 text-white fw-bold">$<?= number_format($currentYearContribution, 2) ?></h3>
                        <small class="text-white-50"><?= count($currentYearRecords) ?> Contributions This Year</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-white-50 small text-uppercase fw-semibold">Pledge Campaigns</span>
                            <i class="bx bx-gift font-size-22"></i>
                        </div>
                        <a href="<?= AssetHelper::url('giving/my-pledges') ?>" class="text-white text-decoration-none">
                            <h5 class="mb-0 text-white fw-bold mt-1">Track My Active Pledges &rarr;</h5>
                            <small class="text-white-50">View installment progress & receipts</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Giving Records Table -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-list-check me-1 text-success"></i> Giving Records & Tithes</h5>
                <span class="badge bg-soft-success text-success">Verified Church Record</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Category / Fund</th>
                                <th>Purpose / Notes</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Verification</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($records)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                                            <i class="bx bx-wallet text-muted font-size-24"></i>
                                        </div>
                                        <h6 class="text-muted">No personal giving records found</h6>
                                        <p class="text-muted small">Contributions linked to your member email or phone will appear here automatically.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($records as $record): ?>
                                    <tr>
                                        <td class="ps-4 small fw-semibold text-dark"><?= date('M d, Y', strtotime($record['transaction_date'])) ?></td>
                                        <td>
                                            <span class="badge bg-soft-primary text-primary text-uppercase">
                                                <?= htmlspecialchars($record['category'] ?: 'Tithe / Offering') ?>
                                            </span>
                                        </td>
                                        <td class="small text-dark">
                                            <?= htmlspecialchars($record['description'] ?: $record['title'] ?: 'Contribution') ?>
                                        </td>
                                        <td class="text-end fw-bold text-success fs-6">$<?= number_format($record['amount'], 2) ?></td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-soft-success text-success px-2 py-1">
                                                <i class="bx bx-check"></i> Recorded
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="alert alert-info border-0 rounded-3 d-flex align-items-center" role="alert">
            <i class="bx bx-info-circle fs-4 me-2"></i>
            <div>
                These records reflect all verified contributions linked to your member profile. If you notice any omissions or require a specialized tax-deductible statement, please contact the Church Finance Office.
            </div>
        </div>
    </div>
</div>
