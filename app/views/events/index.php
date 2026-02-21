<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Events Management</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Events</li>
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
                    <h4 class="card-title mb-0">All Events</h4>
                    <div>
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bx bx-export me-1"></i>Export
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?= AssetHelper::url('events/export') ?>">
                                    <i class="bx bx-spreadsheet me-2"></i>Export All Events (CSV)
                                </a>
                                <a class="dropdown-item" href="<?= AssetHelper::url('events/export') ?>?<?= http_build_query(array_filter([
                                    'search' => $filters['search'] ?? '',
                                    'event_type' => $filters['event_type'] ?? '',
                                    'status' => $filters['status'] ?? ''
                                ])) ?>">
                                    <i class="bx bx-filter me-2"></i>Export Filtered Results (CSV)
                                </a>
                            </div>
                        </div>
                        <a href="<?= AssetHelper::url('events/statistics') ?>" class="btn btn-outline-success me-2">
                            <i class="bx bx-bar-chart me-1"></i>Analytics
                        </a>
                        <a href="<?= AssetHelper::url('events/calendar') ?>" class="btn btn-outline-primary me-2">
                            <i class="bx bx-calendar me-1"></i>Calendar View
                        </a>
                        <a href="<?= AssetHelper::url('events/create') ?>" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Create Event
                        </a>
                    </div>
                </div>
                <p class="card-title-desc">Manage all church events and activities</p>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="filter-section">
                    <h5 class="mb-3"><i class="bx bx-filter me-2"></i>Filter Events</h5>
                    <form method="GET" action="<?= AssetHelper::url('events') ?>" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Search Events</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search by title or description..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Event Type</label>
                            <select class="form-select" name="event_type">
                                <option value="">All Types</option>
                                <?php foreach ($eventTypes as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= (isset($filters['event_type']) && $filters['event_type'] === $key) ? 'selected' : '' ?>>
                                        <?= $value ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                <?php foreach ($statuses as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= (isset($filters['status']) && $filters['status'] === $key) ? 'selected' : '' ?>>
                                        <?= $value ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Date Range</label>
                            <input type="date" class="form-control" name="date_range" 
                                   value="<?= htmlspecialchars($filters['date_range'] ?? '') ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-filter me-1"></i>Apply Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Events Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="events-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Date & Time</th>
                                <th>Location</th>
                                <th>Organizer</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($events)): ?>
                                <?php foreach ($events as $event): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($event['title']) ?></strong>
                                            <?php if ($event['description']): ?>
                                                <br><small class="text-muted"><?= substr(htmlspecialchars($event['description']), 0, 100) ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= $eventTypes[$event['event_type']] ?? $event['event_type'] ?></span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= date('M j, Y', strtotime($event['start_date'])) ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?= date('g:i A', strtotime($event['start_date'])) ?> - 
                                                    <?= date('g:i A', strtotime($event['end_date'])) ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($event['location']) ?></td>
                                        <td>
                                            <?php if ($event['organizer_id']): ?>
                                                <?= htmlspecialchars($event['first_name'] . ' ' . $event['last_name']) ?>
                                                <?php if ($event['organizer_email']): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($event['organizer_email']) ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'draft' => 'secondary',
                                                'published' => 'success',
                                                'cancelled' => 'danger',
                                                'completed' => 'primary'
                                            ];
                                            $statusClass = $statusClass[$event['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>"><?= $statuses[$event['status']] ?? $event['status'] ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= AssetHelper::url("events/{$event['id']}") ?>" class="btn btn-info" title="View">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a href="<?= AssetHelper::url("events/{$event['id']}/edit") ?>" class="btn btn-warning" title="Edit">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form method="POST" action="<?= AssetHelper::url("events/{$event['id']}/delete") ?>" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this event?')">
                                                    <input type="hidden" name="_token" value="<?= Security::generateCSRFToken() ?>">
                                                    <button type="submit" class="btn btn-danger" title="Delete">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="p-0">
                                        <div class="empty-state">
                                            <i class="bx bx-calendar-x"></i>
                                            <h5>No Events Found</h5>
                                            <p>There are no events matching your current filters.<br>Create your first event to get started!</p>
                                            <a href="<?= AssetHelper::url('events/create') ?>" class="btn btn-primary btn-lg">
                                                <i class="bx bx-plus me-2"></i>Create Your First Event
                                            </a>
                                        </div>
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

<style>
/* Custom Events Page Styling - Consistent with other event pages */
.events-container {
    background-color: #f8f9fa;
    min-height: 100vh;
}

.event-card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    transition: all 0.15s ease-in-out;
}

.event-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.event-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 0.5rem 0.5rem 0 0 !important;
    padding: 1.25rem 1.5rem;
    border: none;
}

.page-title-box {
    padding: 1rem 0;
    margin-bottom: 1.5rem;
}

/* Filter Section Styling */
.filter-section {
    background-color: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
}

.filter-section h5 {
    color: #495057;
    margin-bottom: 1rem;
    font-weight: 600;
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

.input-group-text {
    background-color: #f8f9fa;
    border: 2px solid #e9ecef;
    border-right: none;
    border-radius: 0.375rem 0 0 0.375rem;
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

/* Table Styling */
.table-container {
    background-color: #fff;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table {
    background-color: #fff;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    margin-bottom: 0;
}

.table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 1.25rem 1rem;
}

.table td {
    padding: 1.25rem 1rem;
    vertical-align: middle;
    border-top: 1px solid #e9ecef;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(102, 126, 234, 0.03);
}

.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.08);
    transform: scale(1.01);
    transition: all 0.15s ease-in-out;
}

/* Badge Styling */
.badge {
    font-weight: 500;
    padding: 0.6em 0.9em;
    border-radius: 2rem;
    font-size: 0.85em;
}

/* Action Buttons */
.btn-group .btn {
    margin: 0 0.125rem;
    border-radius: 50%;
    width: 2.5rem;
    height: 2.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    transition: all 0.15s ease-in-out;
}

.btn-group .btn i {
    font-size: 1.1rem;
}

.btn-group .btn:hover {
    transform: translateY(-2px) scale(1.1);
}

.btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
}

.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
    color: #212529;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 0.5rem;
    margin: 2rem 0;
}

.empty-state i {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    color: #6c757d;
    opacity: 0.7;
}

.empty-state h5 {
    color: #495057;
    margin-bottom: 1rem;
    font-weight: 600;
}

.empty-state p {
    color: #6c757d;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

/* Responsive Improvements */
@media (max-width: 768px) {
    .filter-section {
        padding: 1rem;
    }
    
    .filter-section .row > div {
        margin-bottom: 1rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group .btn {
        width: 2rem;
        height: 2rem;
        font-size: 0.9rem;
    }
    
    .empty-state {
        padding: 2rem 1rem;
    }
    
    .empty-state i {
        font-size: 3rem;
    }
}

/* Loading Animation */
.loading-spinner {
    display: inline-block;
    width: 1.5rem;
    height: 1.5rem;
    border: 3px solid rgba(0, 0, 0, 0.1);
    border-radius: 50%;
    border-top-color: #0d6efd;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable if available
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#events-table').DataTable({
            order: [[2, 'asc']], // Sort by date
            pageLength: 25,
            responsive: true,
            language: {
                search: 'Search:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ events',
                paginate: {
                    previous: '‹',
                    next: '›'
                }
            }
        });
    }
    
    // Add hover effect to action buttons
    $('.btn-group .btn').hover(
        function() {
            $(this).addClass('shadow-sm');
        },
        function() {
            $(this).removeClass('shadow-sm');
        }
    );
});
</script>