<?php
use App\Utilities\AssetHelper;
use App\Utilities\Helper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= htmlspecialchars($title ?? 'Module Management') ?></h4>
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
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">
                            <i class="bx bx-<?= $moduleIcon ?? 'file' ?> me-2"></i>
                            <?= htmlspecialchars($title ?? 'Module Records') ?>
                        </h4>
                        <p class="card-title-desc mb-0"><?= $description ?? 'Manage module records' ?></p>
                    </div>
                    <div class="d-flex gap-2">
                        <?php if (isset($showExport) && $showExport): ?>
                        <a href="<?= AssetHelper::url("{$moduleName}/{$churchId}/export") ?>" class="btn btn-outline-secondary">
                            <i class="bx bx-export me-1"></i>Export
                        </a>
                        <?php endif; ?>
                        <?php if (isset($showCreate) && $showCreate): ?>
                        <a href="<?= AssetHelper::url("{$moduleName}/{$churchId}/create") ?>" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Create <?= ucfirst($moduleName ?? 'Record') ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Filters Section -->
                <?php if (isset($showFilters) && $showFilters): ?>
                <div class="filter-section mb-4">
                    <h5 class="mb-3"><i class="bx bx-filter me-2"></i>Filter Records</h5>
                    <form method="GET" action="<?= AssetHelper::url("{$moduleName}/{$churchId}") ?>" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Search</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search records..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Date From</label>
                            <input type="date" class="form-control" name="date_from" 
                                   value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Date To</label>
                            <input type="date" class="form-control" name="date_to" 
                                   value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="btn-group w-100" role="group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-filter-alt me-1"></i>Filter
                                </button>
                                <a href="<?= AssetHelper::url("{$moduleName}/{$churchId}") ?>" class="btn btn-outline-secondary">
                                    <i class="bx bx-x"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
                
                <!-- Records Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="recordsTable">
                        <thead class="table-light">
                            <tr>
                                <?php foreach ($tableColumns ?? [] as $column): ?>
                                    <th><?= htmlspecialchars($column['label']) ?></th>
                                <?php endforeach; ?>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($records)): ?>
                                <tr>
                                    <td colspan="<?= count($tableColumns ?? []) + 1 ?>" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bx bx-<?= $moduleIcon ?? 'file' ?>" style="font-size: 3rem;"></i>
                                            <p class="mt-3 mb-0">No records found</p>
                                            <small>Create your first record to get started</small>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($records as $record): ?>
                                    <tr>
                                        <?php foreach ($tableColumns ?? [] as $column): ?>
                                            <td>
                                                <?php if (isset($column['format'])): ?>
                                                    <?= $column['format']($record) ?>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($record[$column['field']] ?? '') ?>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= AssetHelper::url("{$moduleName}/{$churchId}/{$record['id']}") ?>" 
                                                   class="btn btn-outline-primary" title="View Details">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <?php if (isset($showEdit) && $showEdit): ?>
                                                <a href="<?= AssetHelper::url("{$moduleName}/{$churchId}/{$record['id']}/edit") ?>" 
                                                   class="btn btn-outline-secondary" title="Edit">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if (isset($showDelete) && $showDelete): ?>
                                                <button type="button" class="btn btn-outline-danger delete-record" 
                                                        data-id="<?= $record['id'] ?>" 
                                                        data-title="<?= htmlspecialchars($this->getRecordTitle($record) ?? 'this record') ?>"
                                                        title="Delete">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if (isset($pagination) && $pagination): ?>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing <?= $pagination['from'] ?> to <?= $pagination['to'] ?> of <?= $pagination['total'] ?> records
                    </div>
                    <nav>
                        <ul class="pagination mb-0">
                            <?php if ($pagination['current'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= AssetHelper::url("{$moduleName}/{$churchId}?page=" . ($pagination['current'] - 1)) ?>">
                                        <i class="bx bx-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $pagination['current'] - 2); $i <= min($pagination['last'], $pagination['current'] + 2); $i++): ?>
                                <li class="page-item <?= $i == $pagination['current'] ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= AssetHelper::url("{$moduleName}/{$churchId}?page={$i}") ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['current'] < $pagination['last']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= AssetHelper::url("{$moduleName}/{$churchId}?page=" . ($pagination['current'] + 1)) ?>">
                                        <i class="bx bx-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
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
    document.querySelectorAll('.delete-record').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            
            document.getElementById('deleteRecordTitle').textContent = title;
            document.getElementById('deleteForm').action = `<?= AssetHelper::url("{$moduleName}/{$churchId}") ?>/${id}/delete`;
            
            modal.show();
        });
    });
    
    // Initialize DataTable if available
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#recordsTable').DataTable({
            paging: false,
            info: false,
            searching: false,
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
    }
});
</script>