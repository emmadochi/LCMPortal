<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Create Event</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('events') ?>">Events</a></li>
                    <li class="breadcrumb-item active">Create Event</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create New Event</h4>
                <p class="card-title-desc">Fill in the event details below</p>
            </div>
            <div class="card-body">
                <form action="<?= AssetHelper::url('events') ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" 
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
                                            <?= (isset($_POST['event_type']) && $_POST['event_type'] === $key) ? 'selected' : '' ?>>
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
                                  placeholder="Enter event description..." required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                                       value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" 
                                       value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location" name="location" 
                               value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" 
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
                                            <?= (isset($_POST['organizer_id']) && $_POST['organizer_id'] == $organizer['id']) ? 'selected' : '' ?>>
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
                                            <?= (isset($_POST['status']) && $_POST['status'] === $key) ? 'selected' : '' ?>>
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
                                       value="<?= htmlspecialchars($_POST['capacity'] ?? '') ?>" 
                                       placeholder="Enter capacity (optional)" min="1">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="registration_required" name="registration_required" 
                                   value="1" <?= (isset($_POST['registration_required']) && $_POST['registration_required']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="registration_required">
                                Registration Required
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= AssetHelper::url('events') ?>" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Events
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i>Create Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default dates to today
    const now = new Date();
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (!startDateInput.value) {
        const startDate = new Date(now);
        startDate.setDate(startDate.getDate() + 1); // Tomorrow
        startDate.setHours(9, 0, 0, 0); // 9:00 AM
        startDateInput.value = startDate.toISOString().slice(0, 16);
    }
    
    if (!endDateInput.value) {
        const endDate = new Date(now);
        endDate.setDate(endDate.getDate() + 1); // Tomorrow
        endDate.setHours(11, 0, 0, 0); // 11:00 AM
        endDateInput.value = endDate.toISOString().slice(0, 16);
    }
    
    // Validate end date is after start date
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
});
</script>