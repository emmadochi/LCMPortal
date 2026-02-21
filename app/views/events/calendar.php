<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Event Calendar</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('events') ?>">Events</a></li>
                    <li class="breadcrumb-item active">Calendar</li>
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
                    <h4 class="card-title mb-0">Event Calendar</h4>
                    <div>
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bx bx-filter me-1"></i>Filter
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onclick="filterEvents('all')">All Events</a>
                                <a class="dropdown-item" href="#" onclick="filterEvents('published')">Published Only</a>
                                <a class="dropdown-item" href="#" onclick="filterEvents('upcoming')">Upcoming Events</a>
                                <a class="dropdown-item" href="#" onclick="filterEvents('this-week')">This Week</a>
                                <a class="dropdown-item" href="#" onclick="filterEvents('this-month')">This Month</a>
                            </div>
                        </div>
                        <a href="<?= AssetHelper::url('events') ?>" class="btn btn-outline-primary me-2">
                            <i class="bx bx-list-ul me-1"></i>List View
                        </a>
                        <a href="<?= AssetHelper::url('events/create') ?>" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Create Event
                        </a>
                    </div>
                </div>
                <p class="card-title-desc">View all events in calendar format. Click events for details.</p>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body text-center">
                                <h2 id="total-events">0</h2>
                                <p class="mb-0">Total Events</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body text-center">
                                <h2 id="upcoming-events">0</h2>
                                <p class="mb-0">Upcoming Events</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white h-100">
                            <div class="card-body text-center">
                                <h2 id="ongoing-events">0</h2>
                                <p class="mb-0">Ongoing Events</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body text-center">
                                <h2 id="registrations-count">0</h2>
                                <p class="mb-0">Total Registrations</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="event-details">
                    <!-- Event details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="view-event-btn" class="btn btn-primary">View Details</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    // Convert PHP events to FullCalendar format
    const events = [
        <?php foreach ($events as $event): ?>
        {
            id: '<?= $event['id'] ?>',
            title: '<?= addslashes($event['title']) ?>',
            start: '<?= $event['start_date'] ?>',
            end: '<?= $event['end_date'] ?>',
            location: '<?= addslashes($event['location']) ?>',
            description: '<?= addslashes(substr($event['description'], 0, 100)) ?>...',
            eventType: '<?= $event['event_type'] ?>',
            status: '<?= $event['status'] ?>',
            backgroundColor: getEventColor('<?= $event['event_type'] ?>', '<?= $event['status'] ?>'),
            borderColor: getEventColor('<?= $event['event_type'] ?>', '<?= $event['status'] ?>'),
            allDay: isAllDayEvent('<?= $event['start_date'] ?>', '<?= $event['end_date'] ?>'),
            className: getEventClass('<?= $event['status'] ?>')
        },
        <?php endforeach; ?>
    ];
    
    // Update statistics
    updateStatistics(events);
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: events,
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        eventMouseEnter: function(info) {
            showEventTooltip(info);
        },
        eventMouseLeave: function(info) {
            hideEventTooltip(info);
        },
        dateClick: function(info) {
            // Optional: Add functionality to create events by clicking dates
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: 'short'
        },
        slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: 'short'
        },
        views: {
            timeGridWeek: {
                slotDuration: '01:00:00',
                slotLabelInterval: '02:00:00'
            },
            timeGridDay: {
                slotDuration: '00:30:00',
                slotLabelInterval: '01:00:00'
            }
        }
    });
    
    calendar.render();
    window.calendar = calendar;
});

function getEventColor(eventType, status) {
    const colors = {
        'worship_service': '#007bff',
        'bible_study': '#28a745',
        'prayer_meeting': '#ffc107',
        'youth_program': '#17a2b8',
        'children_ministry': '#6f42c1',
        'outreach': '#dc3545',
        'conference': '#fd7e14',
        'seminar': '#6610f2',
        'workshop': '#20c997',
        'fellowship': '#e83e8c',
        'wedding': '#6f42c1',
        'funeral': '#343a40',
        'other': '#6c757d'
    };
    
    if (status === 'cancelled') {
        return '#dc3545';
    } else if (status === 'completed') {
        return '#28a745';
    }
    
    return colors[eventType] || '#007bff';
}

function getEventClass(status) {
    const classes = {
        'draft': 'event-draft',
        'published': 'event-published',
        'cancelled': 'event-cancelled',
        'completed': 'event-completed'
    };
    return classes[status] || 'event-default';
}

function isAllDayEvent(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const diffHours = (end - start) / (1000 * 60 * 60);
    return diffHours >= 24;
}

function showEventDetails(event) {
    const eventDetails = `
        <h6>${event.title}</h6>
        <p><strong>Start:</strong> ${formatDateTime(event.start)}</p>
        <p><strong>End:</strong> ${formatDateTime(event.end)}</p>
        <p><strong>Location:</strong> ${event.extendedProps.location}</p>
        <p><strong>Description:</strong> ${event.extendedProps.description}</p>
        <p><strong>Type:</strong> ${getEventTypeLabel(event.extendedProps.eventType)}</p>
        <p><strong>Status:</strong> <span class="badge bg-${getStatusClass(event.extendedProps.status)}">${getStatusLabel(event.extendedProps.status)}</span></p>
        ${event.allDay ? '<p><span class="badge bg-info">All Day Event</span></p>' : ''}
    `;
    
    document.getElementById('event-details').innerHTML = eventDetails;
    document.getElementById('view-event-btn').href = '<?= AssetHelper::url('events/') ?>' + event.id;
    
    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
    modal.show();
}

function showEventTooltip(info) {
    const tooltip = document.createElement('div');
    tooltip.className = 'event-tooltip';
    tooltip.innerHTML = `
        <div class="tooltip-header">
            <strong>${info.event.title}</strong>
            ${info.event.allDay ? '<span class="badge bg-info ms-2">All Day</span>' : ''}
        </div>
        <div class="tooltip-body">
            ${info.event.extendedProps.description}<br>
            <small><i class="bx bx-map me-1"></i>${info.event.extendedProps.location}</small><br>
            <small><i class="bx bx-time me-1"></i>${formatTimeRange(info.event.start, info.event.end)}</small>
        </div>
    `;
    document.body.appendChild(tooltip);
    
    const rect = info.el.getBoundingClientRect();
    tooltip.style.position = 'fixed';
    tooltip.style.left = rect.left + 'px';
    tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
    tooltip.style.zIndex = '1000';
    tooltip.style.backgroundColor = 'rgba(0,0,0,0.9)';
    tooltip.style.color = 'white';
    tooltip.style.padding = '10px';
    tooltip.style.borderRadius = '6px';
    tooltip.style.fontSize = '13px';
    tooltip.style.maxWidth = '250px';
    tooltip.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
    
    info.el.tooltip = tooltip;
}

function hideEventTooltip(info) {
    if (info.el.tooltip) {
        document.body.removeChild(info.el.tooltip);
        info.el.tooltip = null;
    }
}

function formatDateTime(date) {
    return new Date(date).toLocaleString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        timeZoneName: 'short'
    });
}

function formatTimeRange(start, end) {
    const startDate = new Date(start);
    const endDate = new Date(end);
    
    if (startDate.toDateString() === endDate.toDateString()) {
        // Same day
        return `${startDate.toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'})} - ${endDate.toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'})}`;
    } else {
        // Different days
        return `${startDate.toLocaleDateString()} ${startDate.toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'})} - ${endDate.toLocaleDateString()} ${endDate.toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'})}`;
    }
}

function getEventTypeLabel(type) {
    const labels = {
        'worship_service': 'Worship Service',
        'bible_study': 'Bible Study',
        'prayer_meeting': 'Prayer Meeting',
        'youth_program': 'Youth Program',
        'children_ministry': 'Children Ministry',
        'outreach': 'Outreach Event',
        'conference': 'Conference',
        'seminar': 'Seminar',
        'workshop': 'Workshop',
        'fellowship': 'Fellowship',
        'wedding': 'Wedding',
        'funeral': 'Funeral',
        'other': 'Other'
    };
    return labels[type] || type;
}

function getStatusLabel(status) {
    const labels = {
        'draft': 'Draft',
        'published': 'Published',
        'cancelled': 'Cancelled',
        'completed': 'Completed'
    };
    return labels[status] || status;
}

function getStatusClass(status) {
    const classes = {
        'draft': 'secondary',
        'published': 'success',
        'cancelled': 'danger',
        'completed': 'primary'
    };
    return classes[status] || 'secondary';
}

function updateStatistics(events) {
    const now = new Date();
    const thisWeekStart = new Date(now.getFullYear(), now.getMonth(), now.getDate() - now.getDay());
    const thisWeekEnd = new Date(thisWeekStart.getTime() + 6 * 24 * 60 * 60 * 1000);
    const thisMonthStart = new Date(now.getFullYear(), now.getMonth(), 1);
    const thisMonthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    
    document.getElementById('total-events').textContent = events.length;
    
    const upcomingEvents = events.filter(event => new Date(event.start) > now).length;
    document.getElementById('upcoming-events').textContent = upcomingEvents;
    
    const ongoingEvents = events.filter(event => {
        const start = new Date(event.start);
        const end = new Date(event.end);
        return start <= now && end >= now;
    }).length;
    document.getElementById('ongoing-events').textContent = ongoingEvents;
    
    // Registration count would need to be fetched separately
    // For now, showing a placeholder
    document.getElementById('registrations-count').textContent = 'N/A';
}

function filterEvents(filterType) {
    let filteredEvents = [];
    const now = new Date();
    const thisWeekStart = new Date(now.getFullYear(), now.getMonth(), now.getDate() - now.getDay());
    const thisWeekEnd = new Date(thisWeekStart.getTime() + 6 * 24 * 60 * 60 * 1000);
    const thisMonthStart = new Date(now.getFullYear(), now.getMonth(), 1);
    const thisMonthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    
    switch(filterType) {
        case 'all':
            filteredEvents = window.calendar.getEvents();
            break;
        case 'published':
            filteredEvents = window.calendar.getEvents().filter(event => event.extendedProps.status === 'published');
            break;
        case 'upcoming':
            filteredEvents = window.calendar.getEvents().filter(event => new Date(event.start) > now);
            break;
        case 'this-week':
            filteredEvents = window.calendar.getEvents().filter(event => {
                const eventDate = new Date(event.start);
                return eventDate >= thisWeekStart && eventDate <= thisWeekEnd;
            });
            break;
        case 'this-month':
            filteredEvents = window.calendar.getEvents().filter(event => {
                const eventDate = new Date(event.start);
                return eventDate >= thisMonthStart && eventDate <= thisMonthEnd;
            });
            break;
    }
    
    window.calendar.removeAllEvents();
    filteredEvents.forEach(event => window.calendar.addEvent(event));
    updateStatistics(filteredEvents.map(e => e.toPlainObject()));
}
</script>

<style>
/* Custom Calendar Styling */
.calendar-card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
}

.calendar-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 0.5rem 0.5rem 0 0 !important;
    padding: 1.25rem 1.5rem;
}

.fc {
    font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.fc-event {
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 0.25rem;
    border: none;
    padding: 2px 4px;
    font-size: 0.85rem;
    overflow: hidden;
    text-overflow: ellipsis;
}

.fc-event:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    z-index: 10;
}

.fc-event.event-draft {
    opacity: 0.7;
}

.fc-event.event-cancelled {
    text-decoration: line-through;
    opacity: 0.6;
}

.fc-toolbar {
    margin-bottom: 1.5rem;
}

.fc-toolbar .fc-button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.15s ease-in-out;
}

.fc-toolbar .fc-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.fc-toolbar .fc-button:disabled {
    background: #6c757d;
    transform: none;
    box-shadow: none;
}

.fc-view {
    border-radius: 0.5rem;
    overflow: hidden;
}

.fc-daygrid-day-number {
    font-weight: 600;
}

.fc-timegrid-slot-label {
    font-size: 0.85rem;
}

.event-tooltip {
    pointer-events: none;
    z-index: 1000;
    max-width: 250px;
}

.tooltip-header {
    font-weight: 600;
    margin-bottom: 5px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    padding-bottom: 5px;
}

.tooltip-body {
    font-size: 0.9rem;
    line-height: 1.4;
}

.modal-content {
    border-radius: 0.5rem;
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 0.5rem 0.5rem 0 0 !important;
    border: none;
}

.btn-close {
    filter: invert(1);
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.75em;
    border-radius: 0.375rem;
}

.stat-card {
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.dropdown-menu {
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

@media (max-width: 768px) {
    .calendar-header {
        padding: 1rem;
    }
    
    .fc-toolbar .fc-button {
        padding: 0.4rem 0.75rem;
        font-size: 0.9rem;
    }
    
    .fc-event {
        font-size: 0.75rem;
        padding: 1px 2px;
    }
    
    .stat-card h2 {
        font-size: 1.5rem;
    }
    
    .dropdown-menu {
        font-size: 0.9rem;
    }
}

/* Responsive calendar adjustments */
@media (max-width: 576px) {
    .fc-header-toolbar {
        flex-direction: column;
        gap: 10px;
    }
    
    .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
    }
    
    .fc-event {
        font-size: 0.7rem;
    }
}
</style>