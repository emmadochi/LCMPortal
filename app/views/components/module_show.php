<?php
use App\Utilities\AssetHelper;
use App\Utilities\Helper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= htmlspecialchars($title ?? 'Record Details') ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <?php foreach ($breadcrumbs ?? [] as $breadcrumb): ?>
                        <?php if (isset($breadcrumb['active']) && $breadcrumb['active']): ?>
                            <li class="breadcrumb-item active"><?= htmlspecialchars($breadcrumb['label']) ?></li>
                        <?php elseif (isset($breadcrumb['url'])): ?>
                            <li class="breadcrumb-item">
                                <a href="<?= AssetHelper::url($breadcrumb['url']) ?>"><?= htmlspecialchars($breadcrumb['label']) ?></a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item"><?= htmlspecialchars($breadcrumb['label']) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">
                            <i class="bx bx-<?= $moduleIcon ?? 'file' ?> me-2"></i>
                            <?= $recordTitle ?? 'Record Details' ?>
                        </h4>
                        <?php if (isset($recordSubtitle)): ?>
                            <p class="card-title-desc mb-0"><?= htmlspecialchars($recordSubtitle) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="btn-group">
                        <?php if (isset($showEdit) && $showEdit): ?>
                        <a href="<?= AssetHelper::url("{$moduleName}/{$churchId}/{$record['id']}/edit") ?>" 
                           class="btn btn-outline-primary">
                            <i class="bx bx-edit me-1"></i>Edit
                        </a>
                        <?php endif; ?>
                        <?php if (isset($showDelete) && $showDelete): ?>
                        <button type="button" class="btn btn-outline-danger delete-record" 
                                data-id="<?= $record['id'] ?>" 
                                data-title="<?= htmlspecialchars($recordTitle ?? 'this record') ?>">
                            <i class="bx bx-trash"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <?php if (isset($details)): ?>
                    <div class="row">
                        <?php foreach ($details as $detail): ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small mb-1"><?= htmlspecialchars($detail['label']) ?></label>
                                    <div class="form-control-plaintext fw-medium">
                                        <?php if (isset($detail['format'])): ?>
                                            <?= $detail['format']($record) ?>
                                        <?php elseif (isset($detail['type']) && $detail['type'] === 'date'): ?>
                                            <?= Helper::formatDate($record[$detail['field']] ?? null) ?>
                                        <?php elseif (isset($detail['type']) && $detail['type'] === 'datetime'): ?>
                                            <?= Helper::formatDateTime($record[$detail['field']] ?? null) ?>
                                        <?php elseif (isset($detail['type']) && $detail['type'] === 'currency'): ?>
                                            <?= Helper::formatCurrency($record[$detail['field']] ?? 0) ?>
                                        <?php elseif (isset($detail['type']) && $detail['type'] === 'status'): ?>
                                            <span class="badge bg-<?= Helper::getStatusClass($record[$detail['field']] ?? 'unknown') ?>">
                                                <?= htmlspecialchars(Helper::getStatusText($record[$detail['field']] ?? 'unknown')) ?>
                                            </span>
                                        <?php else: ?>
                                            <?= htmlspecialchars($record[$detail['field']] ?? 'N/A') ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($descriptionField) && !empty($record[$descriptionField])): ?>
                    <div class="mt-4">
                        <label class="form-label text-muted small mb-1">Description</label>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($record[$descriptionField])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (isset($additionalSections)): ?>
            <?php foreach ($additionalSections as $section): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <?php if (isset($section['icon'])): ?>
                                <i class="bx bx-<?= $section['icon'] ?> me-2"></i>
                            <?php endif; ?>
                            <?= htmlspecialchars($section['title']) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?= $section['content'] ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0"><i class="bx bx-info-circle me-2"></i>Record Information</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <div>
                        <small class="text-muted d-block mb-1">Created</small>
                        <strong><?= Helper::formatDateTime($record['created_at'] ?? null) ?></strong>
                        <?php if (isset($record['created_by_name'])): ?>
                            <div class="small text-muted">by <?= htmlspecialchars($record['created_by_name']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (isset($record['updated_at']) && $record['updated_at'] && $record['updated_at'] != $record['created_at']): ?>
                        <div>
                            <small class="text-muted d-block mb-1">Last Updated</small>
                            <strong><?= Helper::formatDateTime($record['updated_at']) ?></strong>
                            <?php if (isset($record['updated_by_name'])): ?>
                                <div class="small text-muted">by <?= htmlspecialchars($record['updated_by_name']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($record['status'])): ?>
                        <div>
                            <small class="text-muted d-block mb-1">Status</small>
                            <span class="badge bg-<?= Helper::getStatusClass($record['status']) ?> fs-6">
                                <?= Helper::getStatusText($record['status']) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (isset($relatedActions) && !empty($relatedActions)): ?>
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="bx bx-link me-2"></i>Related Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php foreach ($relatedActions as $action): ?>
                            <a href="<?= AssetHelper::url($action['url']) ?>" 
                               class="btn btn-outline-primary btn-sm text-start">
                                <i class="bx bx-<?= $action['icon'] ?> me-2"></i>
                                <?= htmlspecialchars($action['label']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteRecordTitle"></strong>?</p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?? '' ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    const deleteButton = document.querySelector('.delete-record');
    if (deleteButton) {
        deleteButton.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            
            document.getElementById('deleteRecordTitle').textContent = title;
            document.getElementById('deleteForm').action = `<?= AssetHelper::url("{$moduleName}/{$churchId}") ?>/${id}/delete`;
            
            modal.show();
        });
    }
    
    // Print functionality
    const printButton = document.querySelector('.print-record');
    if (printButton) {
        printButton.addEventListener('click', function() {
            window.print();
        });
    }
});
</script>