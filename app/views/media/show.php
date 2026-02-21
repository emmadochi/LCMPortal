<?php
use App\Utilities\AssetHelper;
?>
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0"><?= htmlspecialchars($media['title']) ?></h4>
            </div>
            <div class="card-body">
                <?php if (in_array(strtolower($media['file_type']), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                    <div class="text-center mb-4">
                        <img src="<?= AssetHelper::baseUrl($media['file_path']) ?>" 
                             class="img-fluid rounded" 
                             alt="<?= htmlspecialchars($media['title']) ?>">
                    </div>
                <?php elseif (in_array(strtolower($media['file_type']), ['mp4', 'webm', 'ogg'])): ?>
                    <div class="text-center mb-4">
                        <video controls class="w-100 rounded" style="max-height: 500px;">
                            <source src="<?= AssetHelper::baseUrl($media['file_path']) ?>" 
                                    type="video/<?= $media['file_type'] ?>">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-4">
                        <div class="d-flex align-items-center justify-content-center bg-light rounded p-5">
                            <div class="text-center">
                                <i data-feather="file" class="icon-lg text-muted mb-3"></i>
                                <p class="text-muted"><?= strtoupper($media['file_type']) ?> File</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($media['description'])): ?>
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p><?= nl2br(htmlspecialchars($media['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="d-flex gap-2">
                    <a href="<?= AssetHelper::baseUrl($media['file_path']) ?>" 
                       target="_blank" 
                       class="btn btn-primary">
                        <i data-feather="download" class="me-1"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Media Information</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">File Name :</th>
                                <td><?= htmlspecialchars($media['file_name']) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Type :</th>
                                <td><span class="badge bg-secondary"><?= strtoupper($media['file_type']) ?></span></td>
                            </tr>
                            <tr>
                                <th scope="row">Size :</th>
                                <td><?= number_format($media['file_size'] / 1024, 2) ?> KB</td>
                            </tr>
                            <tr>
                                <th scope="row">Category :</th>
                                <td><span class="badge bg-info"><?= ucfirst($media['category']) ?></span></td>
                            </tr>
                            <?php if (!empty($media['tags'])): ?>
                            <tr>
                                <th scope="row">Tags :</th>
                                <td>
                                    <?php 
                                    $tags = explode(',', $media['tags']);
                                    foreach ($tags as $tag): 
                                        $tag = trim($tag);
                                        if (!empty($tag)):
                                    ?>
                                        <span class="badge bg-light text-dark"><?= htmlspecialchars($tag) ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th scope="row">Uploaded :</th>
                                <td><?= date('F d, Y, h:i A', strtotime($media['created_at'])) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

