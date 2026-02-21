<?php
use App\Utilities\AssetHelper;
$churchFilter = $churchFilter ?? null;
?>
<?php if (!empty($churchFilter)): ?>
<div class="alert alert-info d-flex align-items-center justify-content-between mb-3" role="alert">
    <span><i class="bx bx-church me-2"></i>Viewing church: <strong><?= htmlspecialchars($churchFilter['name']) ?></strong></span>
    <a href="<?= AssetHelper::url('outreach-reports') ?>" class="btn btn-sm btn-outline-primary">View all</a>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-0">Outreach & Event Reports</h4>
                    <p class="card-title-desc mb-0">Analyse program outcomes: publicity, logistics, cost, attendance, challenges & targets</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="download" class="me-1"></i> Export
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="<?= AssetHelper::url('outreach-reports/export?format=csv' . ($churchFilter ? '&church_id=' . $churchFilter['id'] : '')) ?>">CSV</a>
                            <a class="dropdown-item" href="<?= AssetHelper::url('outreach-reports/export?format=excel' . ($churchFilter ? '&church_id=' . $churchFilter['id'] : '')) ?>">Excel</a>
                            <a class="dropdown-item" href="<?= AssetHelper::url('outreach-reports/export?format=json' . ($churchFilter ? '&church_id=' . $churchFilter['id'] : '')) ?>">JSON</a>
                            <a class="dropdown-item" href="<?= AssetHelper::url('outreach-reports/export?format=pdf' . ($churchFilter ? '&church_id=' . $churchFilter['id'] : '')) ?>">PDF</a>
                        </div>
                    </div>
                    <a href="<?= AssetHelper::url('outreach-reports/create') ?>" class="btn btn-primary">
                        <i data-feather="file-plus" class="me-1"></i> New Report
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= AssetHelper::url('outreach-reports') ?>" class="mb-4">
                    <?php if (!empty($churchFilter)): ?><input type="hidden" name="church_id" value="<?= (int)$churchFilter['id'] ?>"><?php endif; ?>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search reports..." value="<?= htmlspecialchars($search ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                <?php foreach ($statuses as $val => $label): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= ($status ?? '') === $val ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if (!empty($churches) && empty($churchFilter)): ?>
                        <div class="col-md-2">
                            <select name="church_id" class="form-select">
                                <option value="">All churches</option>
                                <?php foreach ($churches as $ch): ?>
                                    <option value="<?= $ch['id'] ?>" <?= (int)($get_church_id ?? 0) === (int)$ch['id'] ? 'selected' : '' ?>><?= htmlspecialchars($ch['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-1">
                            <a href="<?= AssetHelper::url('outreach-reports') ?>" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Program / Title</th>
                                <th>Date</th>
                                <th>Church / Unit</th>
                                <th>Status</th>
                                <th>Attendance</th>
                                <th>Budget / Actual</th>
                                <th>Created by</th>
                                <th style="width:140px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reports)): ?>
                                <tr><td colspan="8" class="text-center text-muted py-4">No outreach reports yet. <a href="<?= AssetHelper::url('outreach-reports/create') ?>">Create one</a>.</td></tr>
                            <?php else: ?>
                                <?php foreach ($reports as $r): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($r['title']) ?></strong>
                                        <?php if (!empty($r['event_title'])): ?>
                                            <br><small class="text-muted">Event: <?= htmlspecialchars($r['event_title']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($r['program_date'])) ?></td>
                                    <td>
                                        <?= htmlspecialchars($r['church_name'] ?? '—') ?>
                                        <?php if (!empty($r['unit_name'])): ?><br><small><?= htmlspecialchars($r['unit_name']) ?></small><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $sc = ['draft' => 'secondary', 'submitted' => 'primary', 'approved' => 'success'];
                                        $st = $sc[$r['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $st ?>"><?= ucfirst($r['status']) ?></span>
                                    </td>
                                    <td>
                                        <?= $r['total_attendance'] !== null ? (int)$r['total_attendance'] : '—' ?>
                                        <?php if (isset($r['first_timers_count']) && $r['first_timers_count'] !== null): ?>
                                            <br><small class="text-muted"><?= (int)$r['first_timers_count'] ?> first-timers</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($r['budget_total'] !== null || $r['actual_total'] !== null): ?>
                                            <?= $r['budget_total'] !== null ? number_format((float)$r['budget_total'], 2) : '—' ?> / <?= $r['actual_total'] !== null ? number_format((float)$r['actual_total'], 2) : '—' ?>
                                        <?php else: ?>—<?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars(trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''))) ?: '—' ?></td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="<?= AssetHelper::url('outreach-reports/' . $r['id']) ?>" class="btn btn-sm btn-outline-primary" title="View"><i data-feather="eye" class="icon-sm"></i></a>
                                            <a href="<?= AssetHelper::url('outreach-reports/' . $r['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary" title="Edit"><i data-feather="edit" class="icon-sm"></i></a>
                                            <form method="post" action="<?= AssetHelper::url('outreach-reports/' . $r['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this report?');">
                                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i data-feather="trash-2" class="icon-sm"></i></button>
                                            </form>
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
