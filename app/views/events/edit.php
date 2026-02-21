<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Event</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('events') ?>">Events</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url("events/{$event['id']}") ?>"><?= htmlspecialchars($event['title']) ?></a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Event: <?= htmlspecialchars($event['title']) ?></h4>
                <p class="card-title-desc">Update the event details below</p>
            </div>
            <div class="card-body">
                <form action="<?= AssetHelper::url("events/{$event['id']}") ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?= htmlspecialchars($event['title'] ?? $_POST['title'] ?? '') ?>" 
                                       placeholder="Enter event title" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="event_type" class="form-label">Event Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="event_type" name="event_type" required>
                                    <option value="">Select Type...</option>
                                    <?php foreach ($eventTypes as $key => $value): ?>
                                        <option value="<?= $key ?>" 
                                            <?= ((isset($_POST['event_type']) && $_POST['event_type'] === $key) || $event['event_type'] === $key) ? 'selected' : '' ?>>
                                            <?= $value ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Enter event description..." required><?= htmlspecialchars($event['description'] ?? $_POST['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                                       value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($event['start_date'] ?? $_POST['start_date'] ?? ''))) ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" 
                                       value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($event['end_date'] ?? $_POST['end_date'] ?? ''))) ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location" name="location" 
                               value="<?= htmlspecialchars($event['location'] ?? $_POST['location'] ?? '') ?>" 
                               placeholder="Enter event location" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="organizer_id" class="form-label">Organizer</label>
                                <select class="form-select" id="organizer_id" name="organizer_id">
                                    <option value="">Select Organizer...</option>
                                    <?php foreach ($organizers as $organizer): ?>
                                        <option value="<?= $organizer['id'] ?>" 
                                            <?= ((isset($_POST['organizer_id']) && $_POST['organizer_id'] == $organizer['id']) || $event['organizer_id'] == $organizer['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($organizer['first_name'] . ' ' . $organizer['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <?php foreach ($statuses as $key => $value): ?>
                                        <option value="<?= $key ?>" 
                                            <?= ((isset($_POST['status']) && $_POST['status'] === $key) || $event['status'] === $key) ? 'selected' : '' ?>>
                                            <?= $value ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Capacity</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" 
                                       value="<?= htmlspecialchars($event['capacity'] ?? $_POST['capacity'] ?? '') ?>" 
                                       placeholder="Enter capacity (optional)" min="1">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="registration_required" name="registration_required" 
                                   value="1" <?= ((isset($_POST['registration_required']) && $_POST['registration_required']) || $event['registration_required']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="registration_required">
                                Registration Required
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= AssetHelper::url("events/{$event['id']}") ?>" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Event
                        </a>
                        <div>
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bx bx-save me-1"></i>Update Event
                            </button>
                            <form method="POST" action="<?= AssetHelper::url("events/{$event['id']}/delete") ?>" class="d-inline" 
                                  onsubmit="return confirm('Are you sure you want to delete this event? This cannot be undone.')">
                                <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bx bx-trash me-1"></i>Delete Event
                                </button>
                            </form>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Event Form Styling */
.event-form-card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
}

.event-form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 0.5rem 0.5rem 0 0 !important;
    padding: 1.25rem 1.5rem;
}

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 2px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 0.75rem 1rem;
    transition: all 0.15s ease-in-out;
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    outline: 0;
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

.text-danger {
    color: #dc3545 !important;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

@media (max-width: 768px) {
    .event-form-header {
        padding: 1rem;
    }
    
    .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validate end date is after start date
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    startDateInput.addEventListener('change', function() {
        if (endDateInput.value && new Date(endDateInput.value) <= new Date(this.value)) {
            const newEndDate = new Date(this.value);
            newEndDate.setHours(newEndDate.getHours() + 2); // Add 2 hours
            endDateInput.value = newEndDate.toISOString().slice(0, 16);
        }
    });
    
    endDateInput.addEventListener('change', function() {
        if (new Date(this.value) <= new Date(startDateInput.value)) {
            alert('End date must be after start date');
            this.value = '';
        }
    });
    
    // Add form validation feedback
    const form = document.querySelector('form');
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
});
</script>