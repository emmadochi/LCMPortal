<?php
use App\Utilities\AssetHelper;
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Media Library</h4>
                <p class="card-title-desc">Manage all media files and resources</p>
                <div class="d-flex gap-2">
                    <a href="<?= AssetHelper::url('media/create') ?>" class="btn btn-primary">
                        <i data-feather="upload" class="me-1"></i> Upload Media
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($media as $item): ?>
                        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                            <div class="card">
                                <div class="card-body p-2">
                                    <?php if (in_array(strtolower($item['file_type']), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                        <img src="<?= AssetHelper::baseUrl($item['file_path']) ?>" 
                                             class="img-fluid rounded" 
                                             alt="<?= htmlspecialchars($item['title']) ?>"
                                             style="height: 200px; width: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center bg-light rounded" 
                                             style="height: 200px;">
                                            <i data-feather="file" class="icon-lg text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mt-2">
                                        <h6 class="mb-1 text-truncate" title="<?= htmlspecialchars($item['title']) ?>">
                                            <?= htmlspecialchars($item['title']) ?>
                                        </h6>
                                        <p class="text-muted mb-1 font-size-12">
                                            <span class="badge bg-secondary"><?= strtoupper($item['file_type']) ?></span>
                                            <span class="ms-1"><?= number_format($item['file_size'] / 1024, 1) ?> KB</span>
                                        </p>
                                        <div class="d-flex gap-1">
                                            <a href="<?= AssetHelper::url('media/' . $item['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i data-feather="eye" class="icon-sm"></i>
                                            </a>
                                            <a href="<?= AssetHelper::baseUrl($item['file_path']) ?>" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-secondary">
                                                <i data-feather="download" class="icon-sm"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (empty($media)): ?>
                    <div class="text-center py-5">
                        <i data-feather="image" class="icon-lg text-muted mb-3"></i>
                        <p class="text-muted">No media files uploaded yet.</p>
                        <a href="<?= AssetHelper::url('media/create') ?>" class="btn btn-primary">Upload Your First File</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

