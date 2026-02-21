<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-1"><?= htmlspecialchars($project['title']) ?></h4>
                        <div class="d-flex gap-2">
                            <?php
                            $statusColors = [
                                'planning' => 'secondary',
                                'in_progress' => 'primary',
                                'on_hold' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger'
                            ];
                            $statusColor = $statusColors[$project['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $statusColor ?> fs-6"><?= ucfirst(str_replace('_', ' ', $project['status'])) ?></span>
                            <?php
                            $priorityColors = [
                                'low' => 'secondary',
                                'medium' => 'info',
                                'high' => 'warning',
                                'urgent' => 'danger'
                            ];
                            $priorityColor = $priorityColors[$project['priority']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $priorityColor ?> fs-6"><?= ucfirst($project['priority']) ?> Priority</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h5>Description</h5>
                    <div class="p-3 bg-light rounded">
                        <?= nl2br(htmlspecialchars($project['description'])) ?>
                    </div>
                </div>

                <?php if (!empty($projectUnits)): ?>
                    <div class="mb-4">
                        <h5>Assigned Units</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($projectUnits as $pu): ?>
                                <a href="<?= AssetHelper::url('units/' . $pu['unit_id']) ?>" class="badge bg-primary fs-6">
                                    <?= htmlspecialchars($pu['unit_name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted mb-1"><strong>Start Date:</strong> 
                            <?= date('F d, Y', strtotime($project['start_date'])) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1"><strong>End Date:</strong> 
                            <?= $project['end_date'] ? date('F d, Y', strtotime($project['end_date'])) : 'Not set' ?>
                        </p>
                    </div>
                    <?php if ($project['budget']): ?>
                    <div class="col-md-6">
                        <p class="text-muted mb-1"><strong>Budget:</strong> 
                            $<?= number_format($project['budget'], 2) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6">
                        <p class="text-muted mb-1"><strong>Created:</strong> 
                            <?= date('F d, Y, h:i A', strtotime($project['created_at'])) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Project Information</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">Status :</th>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'planning' => 'secondary',
                                        'in_progress' => 'primary',
                                        'on_hold' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$project['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $statusColor ?>"><?= ucfirst(str_replace('_', ' ', $project['status'])) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Priority :</th>
                                <td>
                                    <?php
                                    $priorityColors = [
                                        'low' => 'secondary',
                                        'medium' => 'info',
                                        'high' => 'warning',
                                        'urgent' => 'danger'
                                    ];
                                    $priorityColor = $priorityColors[$project['priority']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $priorityColor ?>"><?= ucfirst($project['priority']) ?></span>
                                </td>
                            </tr>
                            <?php if ($project['budget']): ?>
                            <tr>
                                <th scope="row">Budget :</th>
                                <td>$<?= number_format($project['budget'], 2) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th scope="row">Units :</th>
                                <td><?= count($projectUnits) ?> unit(s)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

