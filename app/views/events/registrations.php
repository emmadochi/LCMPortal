<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Event Registrations</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('events') ?>">Events</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url("events/{$event['id']}") ?>"><?= htmlspecialchars($event['title']) ?></a></li>
                    <li class="breadcrumb-item active">Registrations</li>
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
                    <h4 class="card-title mb-0">Registrations for: <?= htmlspecialchars($event['title']) ?></h4>
                    <div>
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bx bx-export me-1"></i>Export
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?= AssetHelper::url("events/{$event['id']}/export-registrations") ?>">
                                    <i class="bx bx-user me-2"></i>Export Registrations (CSV)
                                </a>
                                <a class="dropdown-item" href="<?= AssetHelper::url("events/{$event['id']}/download-ical") ?>">
                                    <i class="bx bx-calendar me-2"></i>Download iCal Event
                                </a>
                            </div>
                        </div>
                        <a href="<?= AssetHelper::url("events/{$event['id']}") ?>" class="btn btn-outline-primary me-2">
                            <i class="bx bx-arrow-back me-1"></i>Back to Event
                        </a>
                        <a href="<?= AssetHelper::url('events') ?>" class="btn btn-secondary">
                            <i class="bx bx-list-ul me-1"></i>All Events
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Event Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><i class="bx bx-calendar me-2"></i>Event Details</h5>
                                <p><strong>Date:</strong> <?= date('M j, Y g:i A', strtotime($event['start_date'])) ?> - <?= date('M j, Y g:i A', strtotime($event['end_date'])) ?></p>
                                <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                                <p><strong>Type:</strong> <?= htmlspecialchars((new \App\Models\Event())->getEventTypes()[$event['event_type']] ?? $event['event_type']) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-body">
                                <h5 class="card-title text-info"><i class="bx bx-bar-chart-alt-2 me-2"></i>Registration Statistics</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Total Registered:</strong></p>
                                        <h3 class="text-primary"><?= $stats['registered'] + $stats['confirmed'] + $stats['attended'] ?></h3>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Confirmed:</strong></p>
                                        <h3 class="text-success"><?= $stats['confirmed'] ?></h3>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Attended:</strong></p>
                                        <h3 class="text-info"><?= $stats['attended'] ?></h3>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Cancelled:</strong></p>
                                        <h3 class="text-danger"><?= $stats['cancelled'] ?></h3>
                                    </div>
                                </div>
                                <?php if ($event['capacity']): ?>
                                    <hr>
                                    <p class="mb-1"><strong>Capacity:</strong> <?= $event['capacity'] ?> people</p>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?= min(100, (($stats['registered'] + $stats['confirmed'] + $stats['attended']) / $event['capacity']) * 100) ?>%" 
                                             aria-valuenow="<?= $stats['registered'] + $stats['confirmed'] + $stats['attended'] ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="<?= $event['capacity'] ?>">
                                            <?= $stats['registered'] + $stats['confirmed'] + $stats['attended'] ?>/<?= $event['capacity'] ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registrations Table -->
                <?php if (!empty($registrations)): ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Registered Attendees</h5>
                        <div>
                            <form method="POST" action="<?= AssetHelper::url("events/{$event['id']}/import-attendance") ?>" class="d-inline">
                                <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                                <button type="submit" class="btn btn-outline-info btn-sm" title="Import registrations to attendance system">
                                    <i class="bx bx-import me-1"></i>Import to Attendance
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Attendee</th>
                                    <th>Contact</th>
                                    <th>Registration Date</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registrations as $registration): ?>
                                    <tr>
                                        <td>
                                            <h5 class="font-size-14 mb-1"><?= htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']) ?></h5>
                                        </td>
                                        <td>
                                            <p class="mb-1"><i class="bx bx-envelope me-1 text-muted"></i><?= htmlspecialchars($registration['email']) ?></p>
                                            <?php if ($registration['phone']): ?>
                                                <p class="mb-0"><i class="bx bx-phone me-1 text-muted"></i><?= htmlspecialchars($registration['phone']) ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= date('M j, Y g:i A', strtotime($registration['registration_date'])) ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClasses = [
                                                'registered' => 'warning',
                                                'confirmed' => 'success',
                                                'attended' => 'primary',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusLabels = [
                                                'registered' => 'Registered',
                                                'confirmed' => 'Confirmed',
                                                'attended' => 'Attended',
                                                'cancelled' => 'Cancelled'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $statusClasses[$registration['status']] ?>">
                                                <?= $statusLabels[$registration['status']] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($registration['notes']): ?>
                                                <small class="text-muted"><?= htmlspecialchars($registration['notes']) ?></small>
                                            <?php else: ?>
                                                <small class="text-muted">-</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <?php if ($registration['status'] !== 'attended' && in_array($registration['status'], ['confirmed', 'registered'])): ?>
                                                    <form method="POST" action="<?= AssetHelper::url("events/registrations/{$registration['id']}/mark-attended") ?>" class="d-inline">
                                                        <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                                                        <button type="submit" class="btn btn-sm btn-success" title="Mark as Attended">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <form method="POST" action="<?= AssetHelper::url("events/registrations/{$registration['id']}/update-status") ?>" class="d-inline">
                                                    <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                                                        <?php foreach ($statusLabels as $status => $label): ?>
                                                            <option value="<?= $status ?>" <?= $registration['status'] === $status ? 'selected' : '' ?>><?= $label ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bx bx-user-plus text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">No Registrations Yet</h5>
                        <p class="text-muted">No one has registered for this event yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
}

.progress {
    height: 1.25rem;
    border-radius: 0.625rem;
}

.progress-bar {
    border-radius: 0.625rem;
}

.table th {
    font-weight: 600;
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

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
}
</style>