<?php
use App\Utilities\AssetHelper;
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="card-title mb-0">Church & Unit Targets</h4>
                    <a href="<?= AssetHelper::url('targets/create') ?>" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Set Target
                    </a>
                </div>
                <p class="card-title-desc mb-0 mt-2">Set and manage goals for churches and church-specific units</p>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= AssetHelper::url('targets') ?>" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label for="filter_church_id" class="form-label small">Church</label>
                            <select name="church_id" id="filter_church_id" class="form-select form-select-sm">
                                <option value="">All churches</option>
                                <?php foreach ($churches as $c): ?>
                                    <option value="<?= (int)$c['id'] ?>" <?= (string)($filters['church_id'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filter_period_type" class="form-label small">Period type</label>
                            <select name="period_type" id="filter_period_type" class="form-select form-select-sm">
                                <option value="">All</option>
                                <?php foreach ($periodTypes as $k => $v): ?>
                                    <option value="<?= htmlspecialchars($k) ?>" <?= ($filters['period_type'] ?? '') === $k ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filter_period_value" class="form-label small">Period</label>
                            <input type="text" name="period_value" id="filter_period_value" class="form-control form-control-sm" placeholder="e.g. 2025-Q1" value="<?= htmlspecialchars($filters['period_value'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                            <a href="<?= AssetHelper::url('targets') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>

                <?php if (empty($targets)): ?>
                    <div class="alert alert-info mb-0">
                        <i class="bx bx-info-circle me-2"></i>No targets found. <a href="<?= AssetHelper::url('targets/create') ?>">Set a target</a> for a church or unit.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Church</th>
                                    <th>Scope</th>
                                    <th>Target type</th>
                                    <th>Target value</th>
                                    <th>Period</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($targets as $t): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($t['church_name'] ?? '') ?></td>
                                        <td>
                                            <?php if (!empty($t['unit_name'])): ?>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($t['unit_name']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Church-wide</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($targetTypes[$t['target_type']] ?? $t['target_type']) ?></td>
                                        <td>
                                            <?= number_format((float)$t['target_value'], 2) ?>
                                            <?php if (!empty($t['unit_label'])): ?>
                                                <small class="text-muted"><?= htmlspecialchars($t['unit_label']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($t['period_type']) ?>: <?= htmlspecialchars($t['period_value']) ?></td>
                                        <td class="text-end">
                                            <a href="<?= AssetHelper::url("targets/{$t['id']}/edit") ?>" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                                            <form method="POST" action="<?= AssetHelper::url("targets/{$t['id']}") ?>" class="d-inline" onsubmit="return confirm('Remove this target?');">
                                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
$csrf_token = $csrf_token ?? \App\Utilities\Security::generateCSRFToken();
?>
