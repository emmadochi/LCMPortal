<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Church Report: <?= htmlspecialchars($report['church']['name']) ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('churches') ?>">Churches</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url("churches/{$report['church']['id']}") ?>"><?= htmlspecialchars($report['church']['name']) ?></a></li>
                    <li class="breadcrumb-item active">Report</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Detailed Report for <?= htmlspecialchars($report['church']['name']) ?></h4>
                    <div>
                        <a href="<?= AssetHelper::url("churches/{$report['church']['id']}") ?>" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Church
                        </a>
                    </div>
                </div>
                <?php if ($startDate && $endDate): ?>
                    <p class="card-title-desc mb-0">Report Period: <?= date('M j, Y', strtotime($startDate)) ?> - <?= date('M j, Y', strtotime($endDate)) ?></p>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <!-- Church Overview -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><i class="bx bx-info-circle me-2"></i>Church Information</h5>
                                <p><strong>Name:</strong> <?= htmlspecialchars($report['church']['name']) ?></p>
                                <p><strong>Location:</strong> <?= htmlspecialchars($report['church']['city']) ?>, <?= htmlspecialchars($report['church']['state']) ?></p>
                                <p><strong>Status:</strong> <span class="badge bg-<?= $report['church']['status'] === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($report['church']['status']) ?></span></p>
                                <?php if ($report['church']['pastor_name']): ?>
                                    <p><strong>Pastor:</strong> <?= htmlspecialchars($report['church']['pastor_name']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-body">
                                <h5 class="card-title text-info"><i class="bx bx-bar-chart me-2"></i>Quick Statistics</h5>
                                <p><strong>Total Units:</strong> <?= count($report['units']) ?></p>
                                <p><strong>Primary Units:</strong> <?= count(array_filter($report['units'], function($u) { return $u['is_primary']; })) ?></p>
                                <p><strong>Created:</strong> <?= date('M j, Y', strtotime($report['church']['created_at'])) ?></p>
                                <?php if ($report['church']['established_date']): ?>
                                    <p><strong>Established:</strong> <?= date('M j, Y', strtotime($report['church']['established_date'])) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Units Overview -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bx bx-group me-2"></i>Associated Units</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($report['units'])): ?>
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Unit Name</th>
                                            <th>Assigned Date</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($report['units'] as $unit): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($unit['unit_name']) ?></td>
                                                <td><?= date('M j, Y', strtotime($unit['assigned_date'])) ?></td>
                                                <td>
                                                    <?php if ($unit['is_primary']): ?>
                                                        <span class="badge bg-success">Primary</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Secondary</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">Assigned</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-3">
                                <i class="bx bx-group text-muted" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0 text-muted">No units assigned to this church</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Optional Date-based Reports (if dates provided) -->
                <?php if ($startDate && $endDate): ?>
                    <!-- Attendance Summary (if available) -->
                    <?php if (!empty($report['attendance_data'])): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="bx bx-calendar-check me-2"></i>Attendance Summary (<?= $startDate ?> to <?= $endDate ?>)</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Unit</th>
                                            <th>Attendance Records</th>
                                            <th>Average Attendance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $unitAttendance = [];
                                        foreach ($report['attendance_data'] as $record) {
                                            $unitName = $record['unit_name'];
                                            if (!isset($unitAttendance[$unitName])) {
                                                $unitAttendance[$unitName] = ['count' => 0, 'total' => 0];
                                            }
                                            $unitAttendance[$unitName]['count']++;
                                            $unitAttendance[$unitName]['total'] += $record['attendance_count'];
                                        }
                                        ?>
                                        <?php foreach ($unitAttendance as $unitName => $data): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($unitName) ?></td>
                                                <td><?= $data['count'] ?> records</td>
                                                <td><?= $data['count'] > 0 ? round($data['total'] / $data['count']) : 0 ?> people</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Financial Summary (if available) -->
                    <?php if (!empty($report['financial_data'])): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="bx bx-money me-2"></i>Financial Summary (<?= $startDate ?> to <?= $endDate ?>)</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Unit</th>
                                            <th>Total Income</th>
                                            <th>Total Expenses</th>
                                            <th>Net</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($report['financial_data'] as $financial): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($financial['unit_name']) ?></td>
                                                <td class="text-success">$<?= number_format($financial['total_income'], 2) ?></td>
                                                <td class="text-danger">$<?= number_format($financial['total_expense'], 2) ?></td>
                                                <td class="<?= ($financial['total_income'] - $financial['total_expense']) >= 0 ? 'text-success' : 'text-danger' ?>">
                                                    $<?= number_format($financial['total_income'] - $financial['total_expense'], 2) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        For detailed attendance and financial reports, please specify a date range when generating the report.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.75em;
    border-radius: 0.375rem;
}

.btn {
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.15s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
}

.table th {
    font-weight: 600;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
}
</style>