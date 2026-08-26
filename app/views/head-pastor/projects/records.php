<?php
use App\Utilities\AssetHelper;

$church = $church ?? null;
$churchId = $church['id'] ?? 0;
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="card-title mb-1">Project Records</h4>
                        <p class="text-muted mb-0">Complete history and management of church projects.</p>
                    </div>
                    <div>
                        <a href="<?= AssetHelper::url("churches/{$churchId}/projects/create") ?>" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> New Project
                        </a>
                        <a href="<?= AssetHelper::url("churches/{$churchId}/projects/export") ?>" class="btn btn-outline-secondary ms-2">
                            <i class="bx bx-export me-1"></i> Export CSV
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="projectsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Project Title</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Start Date</th>
                                <th class="text-end">Budget</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($projects)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="avatar-md mx-auto mb-3">
                                            <span class="avatar-title rounded-circle bg-light text-primary font-size-24">
                                                <i class="bx bx-info-circle"></i>
                                            </span>
                                        </div>
                                        <h5>No projects found</h5>
                                        <p class="text-muted">You haven't added any projects yet for this church.</p>
                                        <a href="<?= AssetHelper::url("churches/{$churchId}/projects/create") ?>" class="btn btn-primary btn-sm">Create first project</a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($projects as $index => $p): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($p['title']) ?></div>
                                            <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">
                                                <?= htmlspecialchars(substr($p['description'], 0, 80)) . (strlen($p['description']) > 80 ? '...' : '') ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php 
                                                $statusClass = 'bg-soft-secondary text-secondary';
                                                if ($p['status'] === 'in_progress') $statusClass = 'bg-soft-primary text-primary';
                                                elseif ($p['status'] === 'completed') $statusClass = 'bg-soft-success text-success';
                                                elseif ($p['status'] === 'on_hold') $statusClass = 'bg-soft-warning text-warning';
                                                elseif ($p['status'] === 'cancelled') $statusClass = 'bg-soft-danger text-danger';
                                            ?>
                                            <span class="badge rounded-pill <?= $statusClass ?> font-size-11 px-3">
                                                <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                                $prioIcon = 'bx-circle';
                                                $prioColor = 'text-muted';
                                                if ($p['priority'] === 'urgent') { $prioIcon = 'bx-error-circle'; $prioColor = 'text-danger'; }
                                                elseif ($p['priority'] === 'high') { $prioIcon = 'bx-up-arrow-circle'; $prioColor = 'text-warning'; }
                                                elseif ($p['priority'] === 'medium') { $prioIcon = 'bx-right-arrow-circle'; $prioColor = 'text-info'; }
                                            ?>
                                            <span class="<?= $prioColor ?> d-flex align-items-center">
                                                <i class="bx <?= $prioIcon ?> me-1"></i> <?= ucfirst($p['priority']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($p['start_date'])) ?></td>
                                        <td class="text-end fw-bold">₦<?= number_format($p['budget'] ?? 0, 2) ?></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li><a class="dropdown-item" href="<?= AssetHelper::url("churches/{$churchId}/projects/{$p['id']}") ?>"><i class="bx bx-show-alt me-2 text-primary"></i> View Details</a></li>
                                                    <li><a class="dropdown-item" href="<?= AssetHelper::url("churches/{$churchId}/projects/{$p['id']}/edit") ?>"><i class="bx bx-edit-alt me-2 text-info"></i> Edit</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="<?= AssetHelper::url("churches/{$churchId}/projects/{$p['id']}/delete") ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this project? This action cannot be undone.');">
                                                            <button type="submit" class="dropdown-item text-danger"><i class="bx bx-trash me-2"></i> Delete</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
