<?php
use App\Models\User;
use App\Utilities\AssetHelper;

$userModel = new User();
$users = $userModel->findAll(['status' => 'active'], 'first_name, last_name');
$units = $units ?? [];
$eventTypes = $eventTypes ?? [];
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Record Attendance</h4>
            </div>
            <div class="card-body">
                <?php if (empty($units)): ?>
                    <div class="alert alert-warning">
                        <i class="bx bx-info-circle me-2"></i>No active units found. Please <a href="<?= AssetHelper::url('units/create') ?>">create a unit</a> first, or activate existing units from the Units section.
                    </div>
                <?php endif; ?>
                <form method="POST" action="<?= AssetHelper::url('attendance') ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                                <select class="form-select" id="unit_id" name="unit_id" required <?= empty($units) ? 'disabled' : '' ?>>
                                    <option value="">Select Unit...</option>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?= $unit['id'] ?>" 
                                            <?= (isset($_POST['unit_id']) && $_POST['unit_id'] == $unit['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($unit['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Attendee <span class="text-danger">*</span></label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Select Attendee...</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>" 
                                            <?= (isset($_POST['user_id']) && $_POST['user_id'] == $user['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_date" class="form-label">Event Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="event_date" name="event_date" 
                                       value="<?= htmlspecialchars($_POST['event_date'] ?? date('Y-m-d')) ?>" 
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_type" class="form-label">Service type <span class="text-danger">*</span></label>
                                <select class="form-select" id="event_type" name="event_type" required>
                                    <option value="">Select type...</option>
                                    <?php foreach ($eventTypes as $value => $label): ?>
                                        <option value="<?= htmlspecialchars($value) ?>" 
                                            <?= (isset($_POST['event_type']) && $_POST['event_type'] === $value) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="service_description" class="form-label">Short description <small class="text-muted">(optional)</small></label>
                        <input type="text" class="form-control" id="service_description" name="service_description" 
                               maxlength="255" placeholder="e.g. First service, Easter Sunday"
                               value="<?= htmlspecialchars($_POST['service_description'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" 
                                  rows="3" placeholder="Additional notes..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?= AssetHelper::url('attendance') ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="check-circle" class="me-1"></i> Record Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

