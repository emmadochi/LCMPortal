<?php
$eventTypeLabels = \App\Models\Attendance::getEventTypes();
$eventTypeLabel = $eventTypeLabels[$attendance['event_type']] ?? ucfirst(str_replace('_', ' ', $attendance['event_type'] ?? ''));
?>
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Attendance Record</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" style="width: 200px;">Event Date :</th>
                                <td><?= date('F d, Y', strtotime($attendance['event_date'])) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Service type :</th>
                                <td><span class="badge bg-info"><?= htmlspecialchars($eventTypeLabel) ?></span></td>
                            </tr>
                            <?php if (!empty($attendance['service_description'])): ?>
                            <tr>
                                <th scope="row">Description :</th>
                                <td><?= htmlspecialchars($attendance['service_description']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th scope="row">Status :</th>
                                <td>
                                    <?php if ($attendance['status'] === 'present'): ?>
                                        <span class="badge bg-success">Present</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= ucfirst($attendance['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if (!empty($attendance['notes'])): ?>
                            <tr>
                                <th scope="row">Notes :</th>
                                <td><?= nl2br(htmlspecialchars($attendance['notes'])) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th scope="row">Recorded :</th>
                                <td><?= date('F d, Y, h:i A', strtotime($attendance['created_at'])) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

