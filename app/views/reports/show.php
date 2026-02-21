<?php
use App\Utilities\AssetHelper;
?>
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-1"><?= htmlspecialchars($report['title']) ?></h4>
                        <p class="text-muted mb-0">
                            <span class="badge bg-info"><?= ucfirst($report['report_type']) ?></span>
                            <?php if ($unit): ?>
                                <span class="ms-2">Unit: <?= htmlspecialchars($unit['name']) ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <?php
                        $statusColors = [
                            'draft' => 'secondary',
                            'submitted' => 'primary',
                            'approved' => 'success',
                            'rejected' => 'danger'
                        ];
                        $statusColor = $statusColors[$report['status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $statusColor ?> fs-6"><?= ucfirst($report['status']) ?></span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h5>Report Content</h5>
                    <div class="p-3 bg-light rounded">
                        <?= nl2br(htmlspecialchars($report['content'])) ?>
                    </div>
                </div>

                <?php if (!empty($files)): ?>
                    <div class="mb-4">
                        <h5>Attachments</h5>
                        <div class="table-responsive">
                            <table class="table table-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($files as $file): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($file['file_name']) ?></td>
                                            <td>
                                                <span class="badge bg-secondary"><?= strtoupper($file['file_type']) ?></span>
                                            </td>
                                            <td><?= number_format($file['file_size'] / 1024, 2) ?> KB</td>
                                            <td>
                                                <?php
                                                // Convert absolute path to relative URL
                                                $filePath = str_replace('\\', '/', $file['file_path']);
                                                $fileUrl = '/' . ltrim(str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath), '/');
                                                $fileUrl = str_replace('//', '/', $fileUrl);
                                                ?>
                                                <a href="<?= htmlspecialchars($fileUrl) ?>" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i data-feather="download" class="icon-sm"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted mb-1"><strong>Submitted:</strong> 
                            <?= $report['submitted_at'] ? date('F d, Y, h:i A', strtotime($report['submitted_at'])) : 'Not submitted' ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1"><strong>Created:</strong> 
                            <?= date('F d, Y, h:i A', strtotime($report['created_at'])) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Report Information</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">Unit :</th>
                                <td>
                                    <?php if ($unit): ?>
                                        <a href="<?= AssetHelper::url('units/' . $unit['id']) ?>"><?= htmlspecialchars($unit['name']) ?></a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Type :</th>
                                <td><span class="badge bg-info"><?= ucfirst($report['report_type']) ?></span></td>
                            </tr>
                            <tr>
                                <th scope="row">Status :</th>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'submitted' => 'primary',
                                        'approved' => 'success',
                                        'rejected' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$report['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $statusColor ?>"><?= ucfirst($report['status']) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Files :</th>
                                <td><?= count($files) ?> attachment(s)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

