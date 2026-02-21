<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Communication History</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('communications/create') ?>">Bulk Communication</a></li>
                    <li class="breadcrumb-item active">History</li>
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
                    <h4 class="card-title mb-0">Sent Communications</h4>
                    <a href="<?= AssetHelper::url('communications/create') ?>" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>New Communication
                    </a>
                </div>
                <p class="card-title-desc">View history of all sent bulk communications</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="communications-table">
                        <thead>
                            <tr>
                                <th>Date Sent</th>
                                <th>Subject</th>
                                <th>Delivery Method</th>
                                <th>Recipients</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($communications)): ?>
                                <?php foreach ($communications as $comm): ?>
                                    <tr>
                                        <td><?= date('M j, Y g:i A', strtotime($comm['created_at'])) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($comm['subject']) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?= substr(htmlspecialchars($comm['message']), 0, 100) ?>...
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?= strtoupper($comm['delivery_method']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= $comm['recipient_count'] ?> recipients</span>
                                        </td>
                                        <td>
                                            <?php if ($comm['delivered_count'] == $comm['recipient_count']): ?>
                                                <span class="badge bg-success">Delivered</span>
                                            <?php elseif ($comm['failed_count'] > 0): ?>
                                                <span class="badge bg-warning">Partially Delivered</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info" onclick="viewDetails(<?= $comm['id'] ?>)">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                                <button class="btn btn-warning" onclick="resendCommunication(<?= $comm['id'] ?>)">
                                                    <i class="bx bx-refresh"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bx bx-envelope-open fs-1 text-muted mb-3"></i>
                                        <h5>No communications sent yet</h5>
                                        <p class="text-muted">Start sending bulk messages to your members</p>
                                        <a href="<?= AssetHelper::url('communications/create') ?>" class="btn btn-primary">
                                            <i class="bx bx-send me-1"></i>Send First Message
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Communication Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Communication Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="communication-details">
                    <!-- Details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewDetails(communicationId) {
    // In a real implementation, this would fetch communication details via AJAX
    const detailsHtml = `
        <div class="row">
            <div class="col-md-6">
                <h6>Basic Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>ID:</strong></td><td>${communicationId}</td></tr>
                    <tr><td><strong>Subject:</strong></td><td>Loading...</td></tr>
                    <tr><td><strong>Sent:</strong></td><td>Loading...</td></tr>
                    <tr><td><strong>By:</strong></td><td>Loading...</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Statistics</h6>
                <table class="table table-sm">
                    <tr><td><strong>Total Recipients:</strong></td><td>Loading...</td></tr>
                    <tr><td><strong>Delivered:</strong></td><td>Loading...</td></tr>
                    <tr><td><strong>Failed:</strong></td><td>Loading...</td></tr>
                    <tr><td><strong>Pending:</strong></td><td>Loading...</td></tr>
                </table>
            </div>
        </div>
        <div class="mt-3">
            <h6>Message Content</h6>
            <div class="border rounded p-3 bg-light">
                Loading message content...
            </div>
        </div>
    `;
    
    document.getElementById('communication-details').innerHTML = detailsHtml;
    new bootstrap.Modal(document.getElementById('detailsModal')).show();
}

function resendCommunication(communicationId) {
    if (confirm('Are you sure you want to resend this communication?')) {
        // In a real implementation, this would trigger a resend via AJAX
        alert('Resend functionality would be implemented here.');
    }
}

// Initialize DataTable
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#communications-table').DataTable({
            order: [[0, 'desc']],
            pageLength: 25,
            responsive: true
        });
    }
});
</script>