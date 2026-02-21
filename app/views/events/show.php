<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= htmlspecialchars($event['title']) ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('events') ?>">Events</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($event['title']) ?></li>
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
                    <h4 class="card-title mb-0"><?= htmlspecialchars($event['title']) ?></h4>
                    <div>
                        <a href="<?= AssetHelper::url("events/{$event['id']}/edit") ?>" class="btn btn-warning me-2">
                            <i class="bx bx-edit me-1"></i>Edit Event
                        </a>
                        <a href="<?= AssetHelper::url('events') ?>" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Events
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <h5>Description</h5>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6><i class="bx bx-calendar me-2 text-primary"></i>Event Dates</h6>
                                    <p class="mb-1"><strong>Start:</strong> <?= date('F j, Y g:i A', strtotime($event['start_date'])) ?></p>
                                    <p class="mb-0"><strong>End:</strong> <?= date('F j, Y g:i A', strtotime($event['end_date'])) ?></p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6><i class="bx bx-map me-2 text-primary"></i>Location</h6>
                                    <p class="mb-0"><?= htmlspecialchars($event['location']) ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6><i class="bx bx-category me-2 text-primary"></i>Event Type</h6>
                                    <p class="mb-0">
                                        <?php
                                        $eventTypes = (new \App\Models\Event())->getEventTypes();
                                        echo $eventTypes[$event['event_type']] ?? $event['event_type'];
                                        ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6><i class="bx bx-flag me-2 text-primary"></i>Status</h6>
                                    <p class="mb-0">
                                        <?php
                                        $statuses = (new \App\Models\Event())->getStatuses();
                                        $statusClass = [
                                            'draft' => 'secondary',
                                            'published' => 'success',
                                            'cancelled' => 'danger',
                                            'completed' => 'primary'
                                        ];
                                        $statusClass = $statusClass[$event['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>"><?= $statuses[$event['status']] ?? $event['status'] ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($event['capacity']): ?>
                        <div class="mb-3">
                            <h6><i class="bx bx-user me-2 text-primary"></i>Capacity</h6>
                            <p class="mb-0"><?= $event['capacity'] ?> people</p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($event['registration_required']): ?>
                        <div class="mb-3">
                            <span class="badge bg-info"><i class="bx bx-check-circle me-1"></i>Registration Required</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0"><i class="bx bx-user me-2"></i>Organizer</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($event['organizer_id']): ?>
                                    <h6><?= htmlspecialchars($event['first_name'] . ' ' . $event['last_name']) ?></h6>
                                    <?php if ($event['organizer_email']): ?>
                                        <p class="mb-0"><i class="bx bx-envelope me-1"></i> <?= htmlspecialchars($event['organizer_email']) ?></p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="text-muted mb-0">No organizer assigned</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bx bx-calendar-check me-2"></i>Event Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary" onclick="shareEvent()">
                                        <i class="bx bx-share-alt me-1"></i>Share Event
                                    </button>
                                    <button class="btn btn-info" onclick="addToCalendar()">
                                        <i class="bx bx-calendar-plus me-1"></i>Add to Calendar
                                    </button>
                                    <?php if ($event['registration_required']): ?>
                                        <button class="btn btn-success">
                                            <i class="bx bx-user-plus me-1"></i>Register
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php 
                $userId = $_SESSION['user_id'] ?? null;
                $isOrganizer = $userId && ($event['organizer_id'] == $userId || $event['created_by'] == $userId || ($_SESSION['role'] ?? '') === 'admin');
                $canRegister = $event['registration_required'] && $event['status'] === 'published' && $userId;
                $isRegistered = false;
                
                if ($userId) {
                    $regModel = new \App\Models\EventRegistration();
                    $isRegistered = $regModel->isRegistered($event['id'], $userId);
                }
                ?>
                
                <?php if ($canRegister && !$isRegistered): ?>
                <div class="card border-success mt-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0"><i class="bx bx-user-check me-2"></i>Register for this Event</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= AssetHelper::url("events/{$event['id']}/register") ?>">
                            <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional Notes (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any special requirements or notes..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-check-circle me-1"></i>Register for Event
                            </button>
                        </form>
                    </div>
                </div>
                <?php elseif ($isRegistered): ?>
                <div class="card border-info mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0"><i class="bx bx-check-double me-2"></i>You're Registered!</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">You are registered for this event. We look forward to seeing you there!</p>
                        <form method="POST" action="<?= AssetHelper::url("events/{$event['id']}/cancel-registration") ?>" class="d-inline">
                            <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to cancel your registration?')">
                                <i class="bx bx-x-circle me-1"></i>Cancel Registration
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($isOrganizer): ?>
                <div class="card border-primary mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="bx bx-cog me-2"></i>Organizer Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= AssetHelper::url("events/{$event['id']}/registrations") ?>" class="btn btn-outline-primary">
                                <i class="bx bx-user-plus me-1"></i>View Registrations
                            </a>
                            <a href="<?= AssetHelper::url("events/{$event['id']}/download-ical") ?>" class="btn btn-outline-info">
                                <i class="bx bx-calendar me-1"></i>Add to Calendar
                            </a>
                            <a href="<?= AssetHelper::url("events/{$event['id']}/edit") ?>" class="btn btn-outline-warning">
                                <i class="bx bx-edit me-1"></i>Edit Event
                            </a>
                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete(<?= $event['id'] ?>)">
                                <i class="bx bx-trash me-1"></i>Delete Event
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Event Detail Styling */
.event-detail-card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
}

.event-detail-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 0.5rem 0.5rem 0 0 !important;
    padding: 1.25rem 1.5rem;
}

.event-sidebar-card {
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
}

.event-sidebar-header {
    background-color: #0d6efd;
    color: white;
    border-radius: 0.5rem 0.5rem 0 0 !important;
    padding: 1rem;
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.75em;
    border-radius: 0.375rem;
}

.btn {
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.15s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
}

.text-primary {
    color: #0d6efd !important;
}

.text-muted {
    color: #6c757d !important;
}

@media (max-width: 768px) {
    .event-detail-header {
        padding: 1rem;
    }
    
    .event-sidebar-header {
        padding: 0.75rem;
    }
    
    .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>

<script>
function shareEvent() {
    if (navigator.share) {
        navigator.share({
            title: '<?= addslashes($event['title']) ?>',
            text: '<?= addslashes(substr($event['description'], 0, 100)) ?>...',
            url: window.location.href
        }).catch(console.error);
    } else {
        // Fallback for browsers that don't support Web Share API
        navigator.clipboard.writeText(window.location.href).then(() => {
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                <strong>Success!</strong> Event link copied to clipboard.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            // Auto dismiss after 3 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 3000);
        }).catch(() => {
            alert('Failed to copy link to clipboard');
        });
    }
}

function addToCalendar() {
    const startDate = '<?= date('Ymd\THis', strtotime($event['start_date'])) ?>';
    const endDate = '<?= date('Ymd\THis', strtotime($event['end_date'])) ?>';
    const title = '<?= addslashes($event['title']) ?>';
    const location = '<?= addslashes($event['location']) ?>';
    const description = '<?= addslashes($event['description']) ?>';
    
    const googleCalendarUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${startDate}/${endDate}&details=${encodeURIComponent(description)}&location=${encodeURIComponent(location)}`;
    
    window.open(googleCalendarUrl, '_blank');
}

// Add smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to action buttons
    const actionButtons = document.querySelectorAll('.btn');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>