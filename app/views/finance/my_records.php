<?php
use App\Utilities\AssetHelper;

$records = $records ?? [];
$user = $user ?? null;
$church = $church ?? null;
$activePledgesCount = $activePledgesCount ?? 0;
$totalPledgeAmount = (float)($totalPledgeAmount ?? 0);
$totalPledgePaid = (float)($totalPledgePaid ?? 0);
$monthlyGiving = $monthlyGiving ?? array_fill(1, 12, 0);
$categoryBreakdown = $categoryBreakdown ?? [];

$totalContribution = array_sum(array_column($records, 'amount'));
$currentYear = date('Y');
$currentYearRecords = array_filter($records, function($r) use ($currentYear) {
    return date('Y', strtotime($r['transaction_date'])) === $currentYear;
});
$currentYearContribution = array_sum(array_column($currentYearRecords, 'amount'));

// Find primary giving fund
$topCategory = 'General Giving';
if (!empty($categoryBreakdown)) {
    arsort($categoryBreakdown);
    $topCategory = array_key_first($categoryBreakdown);
}

// Chart labels and values
$monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$monthlyValues = array_values($monthlyGiving);

$catLabels = array_keys($categoryBreakdown);
$catValues = array_values($categoryBreakdown);
$catColors = ['#34c38f', '#556ee6', '#f1b44c', '#50a5f1', '#f46a6a', '#74788d', '#e83e8c', '#6f42c1'];

$pledgeProgress = $totalPledgeAmount > 0 ? round(($totalPledgePaid / $totalPledgeAmount) * 100, 1) : 0;
?>

<!-- Breadcrumb & Top Bar -->
<div class="row mb-3 d-print-none">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('profile') ?>">Personal Space</a></li>
                    <li class="breadcrumb-item active">My Giving History</li>
                </ol>
                <h4 class="mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-wallet text-success me-2 font-size-22"></i> My Giving History & Contributions
                </h4>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center mt-2 mt-sm-0">
                <a href="<?= AssetHelper::url('giving/my-pledges') ?>" class="btn btn-outline-success fw-semibold">
                    <i class="bx bx-gift me-1"></i> My Pledges
                    <?php if ($activePledgesCount > 0): ?>
                        <span class="badge bg-success ms-1"><?= $activePledgesCount ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= AssetHelper::url('giving/my-records/export/csv') ?>" class="btn btn-outline-secondary fw-semibold">
                    <i class="bx bx-download me-1"></i> Export CSV
                </a>
                <a href="<?= AssetHelper::url('giving/my-records/export/pdf') ?>" class="btn btn-danger fw-semibold">
                    <i class="bx bxs-file-pdf me-1"></i> Annual Statement (PDF)
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Welcome & Stewardship Hero Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #134e5e 0%, #71b280 100%); border-radius: 12px;">
            <div class="card-body p-4 text-white">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-12 mb-3 mb-lg-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center me-3 flex-shrink-0">
                                <i class="bx bx-heart text-white font-size-28"></i>
                            </div>
                            <div>
                                <span class="badge bg-white bg-opacity-20 text-white px-2 py-1 mb-1 rounded-pill font-size-11">
                                    <i class="bx bx-check-shield me-1"></i> Member Contribution Portal
                                </span>
                                <h3 class="text-white fw-bold mb-1">
                                    <?= htmlspecialchars(($user['first_name'] ?? 'Member') . ' ' . ($user['last_name'] ?? '')) ?>
                                </h3>
                                <p class="text-white-50 font-size-13 mb-0">
                                    Member Profile &bull; <strong><?= htmlspecialchars($church['name'] ?? 'Life Changers Church') ?></strong> &bull; All contributions recorded & verified by Church Treasury
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 text-lg-end">
                        <div class="d-inline-block bg-white bg-opacity-10 rounded-3 p-3 text-center w-100 w-sm-auto">
                            <span class="font-size-11 text-white-50 text-uppercase d-block mb-1">Total Lifetime Giving</span>
                            <h2 class="text-white fw-bold mb-0">₦<?= number_format($totalContribution, 2) ?></h2>
                            <small class="text-white-50 font-size-11"><?= count($records) ?> Verified Transactions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Four High-Contrast Modern KPI Cards -->
<div class="row g-3 mb-4">
    <!-- Lifetime Giving -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm rounded-3 stat-card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 bg-success bg-opacity-10 d-flex align-items-center justify-content-center text-success">
                            <i class="bx bx-coin-stack font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Lifetime Total</p>
                        <h4 class="mb-0 fw-bold text-dark font-size-18">₦<?= number_format($totalContribution, 2) ?></h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge bg-soft-success text-success font-size-11 mb-1 d-block">All Time</span>
                        <small class="text-muted font-size-11"><?= count($records) ?> seeds</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Year-to-Date Giving -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm rounded-3 stat-card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary">
                            <i class="bx bx-calendar font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1"><?= $currentYear ?> YTD Giving</p>
                        <h4 class="mb-0 fw-bold text-dark font-size-18">₦<?= number_format($currentYearContribution, 2) ?></h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge bg-soft-primary text-primary font-size-11 mb-1 d-block"><?= count($currentYearRecords) ?> this year</span>
                        <small class="text-muted font-size-11">Current Year</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $totalContribution > 0 ? round(($currentYearContribution / $totalContribution) * 100) : 100 ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Pledge Campaigns -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm rounded-3 stat-card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 bg-warning bg-opacity-10 d-flex align-items-center justify-content-center text-warning">
                            <i class="bx bx-gift font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Pledges Fulfilled</p>
                        <h4 class="mb-0 fw-bold text-dark font-size-18">₦<?= number_format($totalPledgePaid, 2) ?></h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge bg-soft-warning text-warning font-size-11 mb-1 d-block"><?= $pledgeProgress ?>%</span>
                        <small class="text-muted font-size-11">of ₦<?= number_format($totalPledgeAmount, 0) ?></small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $pledgeProgress ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Primary Giving Fund -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm rounded-3 stat-card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm rounded-3 bg-info bg-opacity-10 d-flex align-items-center justify-content-center text-info">
                            <i class="bx bx-pie-chart-alt-2 font-size-24"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-semibold font-size-11 mb-1">Primary Fund</p>
                        <h4 class="mb-0 fw-bold text-dark font-size-16 text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($topCategory) ?>">
                            <?= htmlspecialchars($topCategory) ?>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <span class="badge bg-soft-info text-info font-size-11 mb-1 d-block"><?= count($categoryBreakdown) ?> Funds</span>
                        <small class="text-muted font-size-11">Supported</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Visual Giving Analytics -->
<div class="row g-4 mb-4 d-print-none">
    <!-- Monthly Giving Trend Chart -->
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-bar-chart text-success me-2 font-size-18"></i> <?= $currentYear ?> Monthly Giving Timeline
                    </h5>
                    <small class="text-muted">Month-by-month seed and contribution trajectory</small>
                </div>
                <span class="badge bg-soft-success text-success font-size-12 px-2 py-1">
                    ₦<?= number_format($currentYearContribution, 2) ?> Total
                </span>
            </div>
            <div class="card-body p-4">
                <div style="position: relative; height: 280px;">
                    <canvas id="monthlyGivingChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Allocation Donut Chart -->
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-pie-chart-alt text-info me-2 font-size-18"></i> Giving by Category
                </h5>
                <small class="text-muted">Allocation across church ministries & funds</small>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-center">
                <?php if (!empty($categoryBreakdown)): ?>
                    <div style="position: relative; height: 180px;">
                        <canvas id="categoryDonutChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless align-middle mb-0 font-size-12">
                                <tbody>
                                    <?php 
                                    $cIdx = 0;
                                    foreach ($categoryBreakdown as $cName => $cAmt): 
                                        $cPct = $totalContribution > 0 ? round(($cAmt / $totalContribution) * 100, 1) : 0;
                                        $cColor = $catColors[$cIdx % count($catColors)];
                                        $cIdx++;
                                    ?>
                                        <tr>
                                            <td>
                                                <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background-color: <?= $cColor ?>;"></span>
                                                <?= htmlspecialchars($cName) ?>
                                            </td>
                                            <td class="text-end fw-bold text-dark">₦<?= number_format($cAmt, 2) ?></td>
                                            <td class="text-end text-muted"><?= $cPct ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bx bx-pie-chart font-size-40 opacity-50 mb-2"></i>
                        <p class="mb-0">No categorical giving records yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Giving Records Table -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-list-ul text-primary me-2 font-size-18"></i> Detailed Giving Records & Tithes
                    </h5>
                    <small class="text-muted">Itemized verified contributions linked to your member account</small>
                </div>
                <div class="d-flex flex-wrap gap-2 d-print-none">
                    <input type="text" id="tableSearchInput" class="form-control form-control-sm" placeholder="Search records..." style="width: 200px;">
                    <select id="categoryFilterSelect" class="form-select form-select-sm" style="width: 170px;">
                        <option value="">All Categories</option>
                        <?php foreach (array_keys($categoryBreakdown) as $catOption): ?>
                            <option value="<?= htmlspecialchars($catOption) ?>"><?= htmlspecialchars($catOption) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="givingRecordsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Category / Fund</th>
                                <th>Purpose / Description</th>
                                <th class="text-end">Amount (₦)</th>
                                <th class="text-center">Verification</th>
                                <th class="text-center pe-4 d-print-none">Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($records)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                                            <i class="bx bx-wallet text-muted font-size-28"></i>
                                        </div>
                                        <h6 class="text-dark fw-bold">No personal giving records found</h6>
                                        <p class="text-muted small mb-0">Contributions linked to your member email or phone will appear here automatically.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($records as $record): ?>
                                    <?php
                                        $amt = (float)$record['amount'];
                                        $catDisplay = !empty($record['category']) ? ucwords(str_replace('_', ' ', $record['category'])) : 'General Giving';
                                        $descDisplay = !empty($record['description']) ? $record['description'] : (!empty($record['title']) ? $record['title'] : 'Contribution');
                                        $receiptData = [
                                            'id' => $record['id'] ?? '',
                                            'date' => date('M d, Y', strtotime($record['transaction_date'])),
                                            'category' => $catDisplay,
                                            'amount' => '₦' . number_format($amt, 2),
                                            'description' => $descDisplay,
                                            'church' => $church['name'] ?? 'Life Changers Church',
                                            'donor' => ($user['first_name'] ?? 'Member') . ' ' . ($user['last_name'] ?? ''),
                                            'ref' => 'LCM-TX-' . strtoupper(substr(md5($record['id'] . $record['transaction_date']), 0, 8))
                                        ];
                                    ?>
                                    <tr data-category="<?= htmlspecialchars($catDisplay) ?>">
                                        <td class="ps-4 fw-semibold text-dark">
                                            <?= date('M d, Y', strtotime($record['transaction_date'])) ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-primary text-primary text-uppercase px-2 py-1 font-size-11">
                                                <?= htmlspecialchars($catDisplay) ?>
                                            </span>
                                        </td>
                                        <td class="text-dark">
                                            <?= htmlspecialchars($descDisplay) ?>
                                        </td>
                                        <td class="text-end fw-bold text-success font-size-15">
                                            ₦<?= number_format($amt, 2) ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-soft-success text-success px-2 py-1 font-size-11">
                                                <i class="bx bx-check-circle me-1 align-middle"></i> Verified
                                            </span>
                                        </td>
                                        <td class="text-center pe-4 d-print-none">
                                            <button type="button" class="btn btn-sm btn-soft-primary px-2 py-1 view-receipt-btn" 
                                                    data-receipt='<?= htmlspecialchars(json_encode($receiptData), ENT_QUOTES, 'UTF-8') ?>'
                                                    title="View Official Receipt Slip">
                                                <i class="bx bx-receipt me-1"></i> Receipt
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($records)): ?>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="3" class="ps-4 text-uppercase">Total Verified Contributions</td>
                                    <td class="text-end text-success fs-6">₦<?= number_format($totalContribution, 2) ?></td>
                                    <td class="text-center text-muted font-size-12"><?= count($records) ?> records</td>
                                    <td class="d-print-none"></td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Information Card -->
<div class="row d-print-none mb-4">
    <div class="col-12">
        <div class="alert alert-info border-0 rounded-3 d-flex align-items-center shadow-sm" role="alert" style="background-color: #f0f7ff;">
            <i class="bx bx-info-circle font-size-24 text-info me-3 flex-shrink-0"></i>
            <div class="font-size-13 text-dark">
                <strong>Official Statement Notice:</strong> All records above represent confirmed and audited giving logs linked directly to your member profile. If you have questions regarding your annual giving statement or need a specialized statement for official or tax purposes, please contact the Church Finance Office.
            </div>
        </div>
    </div>
</div>

<!-- Printable Digital Giving Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header bg-light py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="bx bx-receipt text-success me-2"></i> Official Contribution Receipt
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="printableReceiptSlip">
                <div class="text-center mb-4 pb-3 border-bottom">
                    <h5 class="fw-bold text-dark mb-1" id="receiptChurchName">Life Changers Church</h5>
                    <p class="text-muted font-size-12 text-uppercase mb-1">Official Member Giving Voucher</p>
                    <span class="badge bg-soft-success text-success font-size-11" id="receiptRef">REF: LCM-TX-00000000</span>
                </div>
                <div class="row g-2 font-size-13 mb-3">
                    <div class="col-6 text-muted">Donor Name:</div>
                    <div class="col-6 text-end fw-bold text-dark" id="receiptDonor">Member</div>
                    
                    <div class="col-6 text-muted">Date Recorded:</div>
                    <div class="col-6 text-end fw-bold text-dark" id="receiptDate">Aug 27, 2026</div>

                    <div class="col-6 text-muted">Fund / Category:</div>
                    <div class="col-6 text-end fw-bold text-primary" id="receiptCategory">Tithe</div>

                    <div class="col-6 text-muted">Purpose / Notes:</div>
                    <div class="col-6 text-end text-dark" id="receiptDesc">Tithe Contribution</div>
                </div>

                <div class="bg-light p-3 rounded-3 text-center my-3 border">
                    <span class="text-muted font-size-11 text-uppercase d-block mb-1">Contribution Amount</span>
                    <h3 class="fw-bold text-success mb-0" id="receiptAmount">₦0.00</h3>
                </div>

                <div class="text-center pt-2">
                    <div class="badge bg-soft-success text-success px-3 py-2 font-size-12 rounded-pill mb-2">
                        <i class="bx bx-check-double me-1"></i> Church Treasury Verified
                    </div>
                    <p class="text-muted font-size-11 mb-0">Thank you for your faithful giving and partnership in ministry!</p>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 border-top">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="printReceiptSlip()">
                    <i class="bx bx-printer me-1"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js & Table Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Monthly Giving Timeline Chart
    var monthlyCanvas = document.getElementById('monthlyGivingChart');
    if (monthlyCanvas) {
        new Chart(monthlyCanvas, {
            type: 'bar',
            data: {
                labels: <?= json_encode($monthLabels) ?>,
                datasets: [{
                    label: 'Monthly Giving (₦)',
                    data: <?= json_encode($monthlyValues) ?>,
                    backgroundColor: '#34c38f',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 36
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#2a3042',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Giving: ₦' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#74788d', font: { size: 12 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                        ticks: {
                            color: '#74788d',
                            font: { size: 12 },
                            callback: function(value) {
                                if (value >= 1000000) return '₦' + (value/1000000).toFixed(1) + 'M';
                                if (value >= 1000) return '₦' + (value/1000).toFixed(0) + 'k';
                                return '₦' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Category Allocation Donut Chart
    var donutCanvas = document.getElementById('categoryDonutChart');
    if (donutCanvas && <?= !empty($categoryBreakdown) ? 'true' : 'false' ?>) {
        new Chart(donutCanvas, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($catLabels) ?>,
                datasets: [{
                    data: <?= json_encode($catValues) ?>,
                    backgroundColor: <?= json_encode(array_slice($catColors, 0, count($catLabels))) ?>,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#2a3042',
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ₦' + context.parsed.toLocaleString('en-US', {minimumFractionDigits: 2});
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Live Search & Category Filter for Table
    function filterTable() {
        var query = $("#tableSearchInput").val().toLowerCase();
        var selectedCat = $("#categoryFilterSelect").val();

        $("#givingRecordsTable tbody tr").each(function() {
            var row = $(this);
            var text = row.text().toLowerCase();
            var rowCat = row.data("category") || '';

            var matchesQuery = text.indexOf(query) !== -1;
            var matchesCat = !selectedCat || rowCat === selectedCat;

            if (matchesQuery && matchesCat) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    $("#tableSearchInput").on("keyup", filterTable);
    $("#categoryFilterSelect").on("change", filterTable);

    // 4. Receipt Slip Modal Trigger
    $(".view-receipt-btn").on("click", function() {
        var data = $(this).data("receipt");
        if (!data) return;

        $("#receiptChurchName").text(data.church);
        $("#receiptRef").text("REF: " + data.ref);
        $("#receiptDonor").text(data.donor);
        $("#receiptDate").text(data.date);
        $("#receiptCategory").text(data.category);
        $("#receiptDesc").text(data.description);
        $("#receiptAmount").text(data.amount);

        var modal = new bootstrap.Modal(document.getElementById('receiptModal'));
        modal.show();
    });
});

function printReceiptSlip() {
    var printContent = document.getElementById("printableReceiptSlip").innerHTML;
    var originalContent = document.body.innerHTML;
    document.body.innerHTML = '<div style="max-width: 500px; margin: 40px auto; font-family: sans-serif;">' + printContent + '</div>';
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
}
</script>

<style>
.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
}
@media print {
    .d-print-none, .vertical-menu, .navbar-header, .footer {
        display: none !important;
    }
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>
