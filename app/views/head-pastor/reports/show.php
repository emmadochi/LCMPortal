<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1"><?= htmlspecialchars($report['title']) ?></h4>
                    <p class="text-muted mb-0">
                        <span class="badge bg-info"><?= ucfirst($report['report_type']) ?></span>
                        <?php if ($unit): ?>
                            <span class="ms-2">Unit: <strong><?= htmlspecialchars($unit['name']) ?></strong></span>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <?php
                    $statusColors = [
                        'draft' => 'secondary',
                        'submitted' => 'success',
                        'approved' => 'info',
                        'rejected' => 'danger'
                    ];
                    $statusColor = $statusColors[$report['status']] ?? 'secondary';
                    ?>
                    <span class="badge bg-<?= $statusColor ?> fs-6"><?= ucfirst($report['status']) ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h5 class="mb-3">Report Details</h5>
                    <div class="p-4 bg-light rounded shadow-sm" style="min-height: 200px; white-space: pre-wrap; line-height: 1.6;">
                        <?= htmlspecialchars($report['content']) ?>
                    </div>
                </div>

                <?php if (!empty($files)): ?>
                    <div class="mb-4">
                        <h5 class="mb-3">Attachments</h5>
                        <div class="row">
                            <?php foreach ($files as $file): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card border shadow-none mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <span class="avatar-title bg-soft-primary text-primary rounded">
                                                        <i class="bx bx-file font-size-24"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <h5 class="font-size-14 text-truncate mb-1"><?= htmlspecialchars($file['file_name']) ?></h5>
                                                    <p class="text-muted text-truncate mb-0"><?= number_format($file['file_size'] / 1024, 2) ?> KB</p>
                                                </div>
                                                <div>
                                                    <?php
                                                    $filePath = str_replace('\\', '/', $file['file_path']);
                                                    $fileUrl = '/' . ltrim(str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath), '/');
                                                    $fileUrl = str_replace('//', '/', $fileUrl);
                                                    ?>
                                                    <a href="<?= htmlspecialchars($fileUrl) ?>" target="_blank" class="text-primary font-size-18">
                                                        <i class="bx bx-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between border-top pt-3">
                    <button onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="bx bx-printer me-1"></i> Print Report
                    </button>
                    <a href="<?= AssetHelper::url('churches/' . $church['id'] . '/unit-reports') ?>" class="btn btn-primary">
                        <i class="bx bx-arrow-back me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header bg-soft-primary">
                <h5 class="card-title mb-0">Submission Info</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Submitted By:</span>
                            <span class="fw-bold"><?= htmlspecialchars(($report['first_name'] ?? '') . ' ' . ($report['last_name'] ?? '')) ?></span>
                        </div>
                    </div>
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Unit:</span>
                            <span class="fw-bold"><?= htmlspecialchars($unit['name']) ?></span>
                        </div>
                    </div>
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Type:</span>
                            <span class="badge badge-soft-info"><?= ucfirst($report['report_type']) ?></span>
                        </div>
                    </div>
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Submission Date:</span>
                            <span class="fw-bold"><?= $report['submitted_at'] ? date('M d, Y, h:i A', strtotime($report['submitted_at'])) : 'Draft' ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
