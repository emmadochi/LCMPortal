<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Event Statistics & Analytics</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('events') ?>">Events</a></li>
                    <li class="breadcrumb-item active">Statistics</li>
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
                    <h4 class="card-title mb-0">Event Analytics Dashboard</h4>
                    <div>
                        <form method="GET" class="d-inline">
                            <select name="period" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                <?php foreach ($periods as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $currentPeriod === $key ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                        <a href="<?= AssetHelper::url('events') ?>" class="btn btn-outline-primary btn-sm ms-2">
                            <i class="bx bx-arrow-back me-1"></i>Back to Events
                        </a>
                    </div>
                </div>
                <p class="card-title-desc mb-0">Comprehensive analytics and insights for your events</p>
            </div>
            <div class="card-body">
                <!-- Key Metrics Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white stat-card">
                            <div class="card-body text-center">
                                <i class="bx bx-calendar-event" style="font-size: 2rem;"></i>
                                <h2 class="mt-2"><?= $stats['total_events'] ?></h2>
                                <p class="mb-0">Total Events</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white stat-card">
                            <div class="card-body text-center">
                                <i class="bx bx-calendar-check" style="font-size: 2rem;"></i>
                                <h2 class="mt-2"><?= $stats['upcoming_events'] ?></h2>
                                <p class="mb-0">Upcoming Events</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white stat-card">
                            <div class="card-body text-center">
                                <i class="bx bx-calendar-star" style="font-size: 2rem;"></i>
                                <h2 class="mt-2"><?= $stats['ongoing_events'] ?></h2>
                                <p class="mb-0">Ongoing Events</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white stat-card">
                            <div class="card-body text-center">
                                <i class="bx bx-user-check" style="font-size: 2rem;"></i>
                                <h2 class="mt-2"><?= $stats['attended_registrations'] ?></h2>
                                <p class="mb-0">Total Attendees</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white stat-card">
                            <div class="card-body text-center">
                                <i class="bx bx-bar-chart" style="font-size: 2rem;"></i>
                                <h2 class="mt-2"><?= $stats['avg_events_per_month'] ?></h2>
                                <p class="mb-0">Avg Events/Month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-dark text-white stat-card">
                            <div class="card-body text-center">
                                <i class="bx bx-trending-up" style="font-size: 2rem;"></i>
                                <h2 class="mt-2"><?= $stats['attendance_rate'] ?>%</h2>
                                <p class="mb-0">Attendance Rate</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white stat-card">
                            <div class="card-body text-center">
                                <i class="bx bx-history" style="font-size: 2rem;"></i>
                                <h2 class="mt-2"><?= $stats['past_events'] ?></h2>
                                <p class="mb-0">Past Events</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-purple text-white stat-card">
                            <div class="card-body text-center">
                                <i class="bx bx-shape-circle" style="font-size: 2rem;"></i>
                                <h2 class="mt-2"><?= ucfirst(str_replace('_', ' ', $stats['most_popular_type'])) ?></h2>
                                <p class="mb-0">Most Popular</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Events by Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="position: relative; height:300px">
                                    <canvas id="statusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Events by Type</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="position: relative; height:300px">
                                    <canvas id="typeChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Monthly Trends (Last 12 Months)</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="position: relative; height:400px">
                                    <canvas id="trendChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Event Participation Statistics</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($participationStats)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-centered table-nowrap mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Event</th>
                                                    <th>Date</th>
                                                    <th>Total Registered</th>
                                                    <th>Confirmed</th>
                                                    <th>Attended</th>
                                                    <th>Attendance %</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($participationStats as $eventStat): ?>
                                                    <tr>
                                                        <td>
                                                            <h5 class="font-size-14 mb-1">
                                                                <a href="<?= AssetHelper::url("events/{$eventStat['id']}") ?>" class="text-dark">
                                                                    <?= htmlspecialchars($eventStat['title']) ?>
                                                                </a>
                                                            </h5>
                                                        </td>
                                                        <td><?= date('M j, Y', strtotime($eventStat['start_date'])) ?></td>
                                                        <td><span class="badge bg-primary"><?= $eventStat['total_registered'] ?></span></td>
                                                        <td><span class="badge bg-success"><?= $eventStat['confirmed'] ?></span></td>
                                                        <td><span class="badge bg-info"><?= $eventStat['attended'] ?></span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-grow-1 me-2">
                                                                    <div class="progress" style="height: 10px;">
                                                                        <div class="progress-bar bg-success" 
                                                                             role="progressbar" 
                                                                             style="width: <?= $eventStat['attendance_percentage'] ?>%" 
                                                                             aria-valuenow="<?= $eventStat['attendance_percentage'] ?>" 
                                                                             aria-valuemin="0" 
                                                                             aria-valuemax="100">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span class="text-muted"><?= $eventStat['attendance_percentage'] ?>%</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="bx bx-bar-chart text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="mt-3">No Participation Data</h5>
                                        <p class="text-muted">No events with registration data available.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = {
        labels: [
            'Draft (<?= $stats['by_status']['draft'] ?? 0 ?>)',
            'Published (<?= $stats['by_status']['published'] ?? 0 ?>)',
            'Cancelled (<?= $stats['by_status']['cancelled'] ?? 0 ?>)',
            'Completed (<?= $stats['by_status']['completed'] ?? 0 ?>)'
        ],
        datasets: [{
            data: [
                <?= $stats['by_status']['draft'] ?? 0 ?>,
                <?= $stats['by_status']['published'] ?? 0 ?>,
                <?= $stats['by_status']['cancelled'] ?? 0 ?>,
                <?= $stats['by_status']['completed'] ?? 0 ?>
            ],
            backgroundColor: [
                '#6c757d',
                '#28a745',
                '#dc3545',
                '#007bff'
            ],
            borderWidth: 2
        }]
    };

    new Chart(statusCtx, {
        type: 'doughnut',
        data: statusData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Type Chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    const typeLabels = [
        <?php foreach ($stats['by_type'] as $type): ?>
            '<?= ucfirst(str_replace('_', ' ', $type['event_type'])) ?> (<?= $type['count'] ?>)',
        <?php endforeach; ?>
    ];
    const typeData = [
        <?php foreach ($stats['by_type'] as $type): ?>
            <?= $type['count'] ?>,
        <?php endforeach; ?>
    ];

    new Chart(typeCtx, {
        type: 'pie',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeData,
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                    '#6f42c1', '#fd7e14', '#6610f2', '#20c997', '#e83e8c'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const months = [
        <?php foreach ($monthlyTrends as $trend): ?>
            '<?= $trend['month'] ?>',
        <?php endforeach; ?>
    ];
    const eventCounts = [
        <?php foreach ($monthlyTrends as $trend): ?>
            <?= $trend['event_count'] ?>,
        <?php endforeach; ?>
    ];
    const publishedCounts = [
        <?php foreach ($monthlyTrends as $trend): ?>
            <?= $trend['published_count'] ?>,
        <?php endforeach; ?>
    ];
    const completedCounts = [
        <?php foreach ($monthlyTrends as $trend): ?>
            <?= $trend['completed_count'] ?>,
        <?php endforeach; ?>
    ];

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Total Events',
                    data: eventCounts,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Published Events',
                    data: publishedCounts,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Completed Events',
                    data: completedCounts,
                    borderColor: '#6c757d',
                    backgroundColor: 'rgba(108, 117, 125, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
});
</script>

<style>
.stat-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.bg-purple {
    background-color: #6f42c1 !important;
}

.chart-container {
    position: relative;
    height: 300px;
}

.table th {
    font-weight: 600;
}

.progress {
    border-radius: 5px;
}

.progress-bar {
    border-radius: 5px;
}

.badge {
    font-weight: 500;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem 0.5rem 0 0 !important;
    padding: 1rem 1.25rem;
}

@media (max-width: 768px) {
    .stat-card h2 {
        font-size: 1.5rem;
    }
    
    .chart-container {
        height: 250px;
    }
}
</style>