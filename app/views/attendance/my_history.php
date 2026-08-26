<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">My Attendance History</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Attendance History</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card metric-card shadow-sm animate__animated animate__fadeInUp">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Service/Event</th>
                                <th>Unit/Department</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($records)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="p-3">
                                            <i class='bx bx-calendar-x h1 text-muted'></i>
                                            <p class="text-muted">No attendance records found for your account.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($records as $record): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($record['event_date'])) ?></td>
                                        <td>
                                            <span class="fw-bold"><?= ucfirst(str_replace('_', ' ', $record['event_type'])) ?></span>
                                            <?php if ($record['is_first_timer']): ?>
                                                <span class="badge bg-info ms-1">First Timer</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($record['unit_name']) ?></td>
                                        <td>
                                            <span class="badge rounded-pill bg-success-soft text-success">
                                                <i class='bx bx-check-circle'></i> Present
                                            </span>
                                        </td>
                                        <td><small class="text-muted"><?= htmlspecialchars($record['notes'] ?: '-') ?></small></td>
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
