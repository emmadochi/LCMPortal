<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">My Assigned Units</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Units</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Unit Cards & Resources -->
    <div class="col-xl-8">
        <!-- Units Cards -->
        <div class="card metric-card shadow-sm animate__animated animate__fadeInLeft">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">My Departments & Roles</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if (empty($units)): ?>
                        <div class="col-12 text-center py-4">
                            <i class='bx bx-group h1 text-muted'></i>
                            <p class="text-muted">You are not currently assigned to any units.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($units as $unit): ?>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded shadow-sm hover-slide bg-light h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="mb-0 fw-bold text-primary"><?= htmlspecialchars($unit['name']) ?></h6>
                                            <span class="badge bg-primary-soft text-primary"><?= ucfirst($unit['role']) ?></span>
                                        </div>
                                        <p class="text-muted small mb-3"><?= htmlspecialchars($unit['description'] ?? 'No description provided.') ?></p>
                                    </div>
                                    <div class="border-top pt-2 mt-2">
                                        <small class="text-muted"><i class='bx bx-user'></i> Joined: <?= date('M d, Y', strtotime($unit['joined_at'] ?? $unit['created_at'])) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Resources / Materials Section -->
        <div class="card metric-card shadow-sm animate__animated animate__fadeInLeft mt-4" style="animation-delay: 0.1s">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Unit Shared Resources & Manuals</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Resource Name</th>
                                <th>Department</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($media)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class='bx bx-file-off h2'></i>
                                        <p class="mb-0 small">No shared resources found for your units.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($media as $m): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class='bx bxs-file text-primary h4 mb-0 me-2'></i>
                                                <div>
                                                    <span class="fw-bold"><?= htmlspecialchars($m['title']) ?></span>
                                                    <br/>
                                                    <small class="text-muted"><?= htmlspecialchars($m['description'] ?? '') ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-info-soft text-info"><?= htmlspecialchars($m['unit_name']) ?></span></td>
                                        <td><span class="text-uppercase small fw-bold"><?= htmlspecialchars($m['file_type']) ?></span></td>
                                        <td><?= round($m['file_size'] / 1024, 1) ?> KB</td>
                                        <td class="text-end">
                                            <a href="<?= AssetHelper::url($m['file_path']) ?>" download class="btn btn-sm btn-primary">
                                                <i class='bx bx-download'></i> Download
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Announcements Notice Board -->
    <div class="col-xl-4 mt-4 mt-xl-0">
        <div class="card metric-card shadow-sm animate__animated animate__fadeInRight">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0"><i class='bx bx-bullhorn text-warning'></i> Unit Announcements</h5>
            </div>
            <div class="card-body">
                <?php if (empty($announcements)): ?>
                    <div class="text-center py-4">
                        <i class='bx bx-bell-off h1 text-muted'></i>
                        <p class="text-muted small">No announcements posted for your units.</p>
                    </div>
                <?php else: ?>
                    <div class="announcement-timeline">
                        <?php foreach ($announcements as $ann): ?>
                            <div class="p-3 mb-3 border rounded bg-light" id="announcement-card-<?= $ann['notification_id'] ?>">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($ann['title']) ?></h6>
                                    <span class="text-muted x-small"><?= date('M d, H:i', strtotime($ann['created_at'])) ?></span>
                                </div>
                                <p class="small text-muted mb-2"><?= nl2br(htmlspecialchars($ann['message'])) ?></p>
                                
                                <?php if ($ann['image_path']): ?>
                                    <div class="mb-2">
                                        <img src="<?= AssetHelper::url($ann['image_path']) ?>" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;" />
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                    <small class="text-muted x-small">By: <?= htmlspecialchars($ann['sender_first'] . ' ' . $ann['sender_last']) ?></small>
                                    
                                    <div class="ack-section" data-nid="<?= $ann['notification_id'] ?>">
                                        <?php if ($ann['acknowledged']): ?>
                                            <span class="badge bg-success-soft text-success">
                                                <i class='bx bx-check-double'></i> Acknowledged
                                            </span>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-xs btn-outline-primary btn-ack-announcement" data-nid="<?= $ann['notification_id'] ?>">
                                                <i class='bx bx-check'></i> Acknowledge
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener for Acknowledge buttons
    const ackButtons = document.querySelectorAll('.btn-ack-announcement');
    ackButtons.forEach(button => {
        button.addEventListener('click', function() {
            const nid = this.getAttribute('data-nid');
            const parentDiv = this.parentElement;
            
            if (!nid) return;
            
            // Set loading state
            this.disabled = true;
            this.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Acknowledging...";
            
            // Post read status using native fetch
            fetch("<?= AssetHelper::url('my-units/announcements') ?>/" + nid + "/acknowledge", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI on success
                    parentDiv.innerHTML = `<span class="badge bg-success-soft text-success"><i class='bx bx-check-double'></i> Acknowledged</span>`;
                } else {
                    // Restore button state
                    this.disabled = false;
                    this.innerHTML = "<i class='bx bx-check'></i> Acknowledge";
                    alert(data.message || 'Error acknowledging announcement.');
                }
            })
            .catch(error => {
                this.disabled = false;
                this.innerHTML = "<i class='bx bx-check'></i> Acknowledge";
                alert('Connection error occurred.');
            });
        });
    });
});
</script>
