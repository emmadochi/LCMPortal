<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Bulk Communication</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Bulk Communication</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Send Bulk Message</h4>
                <p class="card-title-desc">Send messages to members based on membership types and filters</p>
            </div>
            <div class="card-body">
                <form action="<?= AssetHelper::url('communications') ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" 
                                       placeholder="Enter message subject" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="delivery_method" class="form-label">Delivery Method <span class="text-danger">*</span></label>
                                <select class="form-select" id="delivery_method" name="delivery_method" required>
                                    <option value="">Select delivery method...</option>
                                    <option value="email" <?= (isset($_POST['delivery_method']) && $_POST['delivery_method'] === 'email') ? 'selected' : '' ?>>Email Only</option>
                                    <option value="sms" <?= (isset($_POST['delivery_method']) && $_POST['delivery_method'] === 'sms') ? 'selected' : '' ?>>SMS Only</option>
                                    <option value="in_app" <?= (isset($_POST['delivery_method']) && $_POST['delivery_method'] === 'in_app') ? 'selected' : '' ?>>In-App Notification</option>
                                    <option value="all" <?= (isset($_POST['delivery_method']) && $_POST['delivery_method'] === 'all') ? 'selected' : '' ?>>All Methods</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="6" 
                                  placeholder="Enter your message here..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        <div class="form-text">You can use placeholders like {{name}}, {{membership_type}} in your message</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Membership Types</label>
                                <div class="card">
                                    <div class="card-body">
                                        <?php foreach ($membershipTypes ?? [] as $type): ?>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="membership_types[]" 
                                                       value="<?= $type['id'] ?>" 
                                                       id="type_<?= $type['id'] ?>"
                                                       <?= (isset($_POST['membership_types']) && in_array($type['id'], $_POST['membership_types'])) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="type_<?= $type['id'] ?>">
                                                    <?= htmlspecialchars($type['name']) ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                        
                                        <?php if (empty($membershipTypes)): ?>
                                            <p class="text-muted">No membership types found. Please create some membership types first.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Additional Filters</label>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="include_unengaged" 
                                                   value="1" 
                                                   id="include_unengaged"
                                                   <?= (isset($_POST['include_unengaged']) && $_POST['include_unengaged']) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="include_unengaged">
                                                Include Unengaged Members
                                            </label>
                                            <small class="form-text text-muted">Members with low engagement scores</small>
                                        </div>
                                        
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="send_immediately" 
                                                   value="1" 
                                                   id="send_immediately"
                                                   <?= (!isset($_POST['send_immediately']) || $_POST['send_immediately']) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="send_immediately">
                                                Send Immediately
                                            </label>
                                        </div>
                                        
                                        <div class="mb-3" id="schedule_section" style="display: none;">
                                            <label for="scheduled_time" class="form-label">Schedule Time</label>
                                            <input type="datetime-local" class="form-control" 
                                                   id="scheduled_time" name="scheduled_time"
                                                   value="<?= htmlspecialchars($_POST['scheduled_time'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Preview Options</label>
                                <div class="card">
                                    <div class="card-body">
                                        <button type="button" class="btn btn-info btn-sm" id="preview_btn">
                                            <i class="bx bx-show-alt me-1"></i>Preview Message
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" id="test_send_btn">
                                            <i class="bx bx-send me-1"></i>Test Send
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= AssetHelper::url('/') ?>" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Dashboard
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-send me-1"></i>Send Messages
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Message Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="preview-content">
                    <!-- Preview will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle schedule section
    const sendImmediatelyCheckbox = document.getElementById('send_immediately');
    const scheduleSection = document.getElementById('schedule_section');
    
    sendImmediatelyCheckbox.addEventListener('change', function() {
        scheduleSection.style.display = this.checked ? 'none' : 'block';
    });
    
    // Preview functionality
    document.getElementById('preview_btn').addEventListener('click', function() {
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value;
        const deliveryMethod = document.getElementById('delivery_method').value;
        
        if (!subject || !message || !deliveryMethod) {
            alert('Please fill in subject, message, and delivery method to preview.');
            return;
        }
        
        // Show preview in modal
        const previewContent = `
            <div class="border rounded p-3 mb-3">
                <h6>Subject:</h6>
                <p class="fw-bold">${subject}</p>
                <h6>Message:</h6>
                <div class="bg-light p-3 rounded">
                    ${message.replace(/\n/g, '<br>')}
                </div>
                <h6 class="mt-3">Delivery Method:</h6>
                <span class="badge bg-primary">${deliveryMethod.toUpperCase()}</span>
            </div>
        `;
        
        document.querySelector('.preview-content').innerHTML = previewContent;
        new bootstrap.Modal(document.getElementById('previewModal')).show();
    });
    
    // Test send functionality
    document.getElementById('test_send_btn').addEventListener('click', function() {
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value;
        
        if (!subject || !message) {
            alert('Please fill in subject and message to test.');
            return;
        }
        
        // In a real implementation, this would send a test message to the admin
        alert('Test message functionality would be implemented here. This would send a test message to your email/SMS.');
    });
});
</script>