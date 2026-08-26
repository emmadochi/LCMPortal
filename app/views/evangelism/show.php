<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Evangelism Report</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('evangelism') ?>">Evangelism Reports</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Report Details</h4>
                <div class="btn-group">
                    <a href="<?= AssetHelper::url('evangelism/' . (int)$record['id'] . '/edit') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bx bx-edit me-1"></i>Edit
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bx bx-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Report Date</label>
                            <div class="form-control-plaintext fw-medium"><?= date('M d, Y', strtotime($record['report_date'])) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Souls Won</label>
                            <div class="form-control-plaintext fw-medium"><?= (int)$record['souls_won'] ?></div>
                        </div>
                    </div>
                </div>
                <?php if (!empty($record['notes'])): ?>
                <div class="mb-3">
                    <label class="form-label text-muted small mb-1">Notes</label>
                    <div class="bg-light p-3 rounded"><?= nl2br(htmlspecialchars($record['notes'])) ?></div>
                </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Submitted On</small>
                        <strong><?= date('M d, Y H:i', strtotime($record['created_at'] ?? 'now')) ?></strong>
                    </div>
                    <?php if (!empty($record['updated_at']) && $record['updated_at'] !== $record['created_at']): ?>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Last Updated</small>
                        <strong><?= date('M d, Y H:i', strtotime($record['updated_at'])) ?></strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0"><i class="bx bx-info-circle me-2"></i>Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= AssetHelper::url('evangelism') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i>Back to list
                    </a>
                    <a href="<?= AssetHelper::url('evangelism/export?format=csv') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bx bx-export me-1"></i>Export My Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this report?</p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="<?= AssetHelper::url('evangelism/' . (int)$record['id'] . '/delete') ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Utilities\Security::generateCSRFToken()) ?>">
                    <button type="submit" class="btn btn-danger"><i class="bx bx-trash me-1"></i>Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
