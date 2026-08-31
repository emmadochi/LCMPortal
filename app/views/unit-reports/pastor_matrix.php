<?php
use App\Utilities\AssetHelper;

$matrix = $matrix ?? [];
$churches = $churches ?? [];
$selectedChurchId = $selectedChurchId ?? null;
?>

<div class="container-fluid p-0">
    <!-- Header Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                        <i class="bx bx-grid-alt text-primary me-2 font-size-24"></i> Pastoral Departmental Compliance Matrix
                    </h4>
                    <p class="text-muted font-size-13 mb-0">High-level oversight of departmental reporting activity, on-time submissions, and director approvals church-wide.</p>
                </div>

                <!-- Church Branch Filter -->
                <?php if (!empty($churches)): ?>
                    <form method="GET" class="d-flex gap-2 align-items-center">
                        <select name="church_id" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                            <option value="">All Churches (Global Oversight)</option>
                            <?php foreach ($churches as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ((string)$selectedChurchId === (string)$c['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Compliance Matrix Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted font-size-11 text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4">Ministry Unit</th>
                            <th>Church Branch</th>
                            <th class="text-center">Total Submissions</th>
                            <th class="text-center">Approved</th>
                            <th class="text-center">Pending Review</th>
                            <th class="text-center">Revision Requested</th>
                            <th>Last Activity</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($matrix)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bx bx-check-circle font-size-36 opacity-50 mb-2 d-block"></i>
                                    <h6 class="text-dark fw-semibold">No Department Submissions Logged Yet</h6>
                                    <p class="font-size-12 mb-0">Units and members will appear as they begin generating and filing reports.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($matrix as $m): ?>
                                <?php
                                $total = (int)$m['total_submissions'];
                                $approved = (int)$m['approved_count'];
                                $pending = (int)$m['pending_count'];
                                $revision = (int)$m['revision_count'];
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark font-size-14">
                                            <i class="bx bx-group text-primary me-1"></i> <?= htmlspecialchars($m['unit_name']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-size-11">
                                            <i class="bx bx-church me-1"></i> <?= htmlspecialchars($m['church_name'] ?? 'General HQ') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-primary text-primary font-size-12 px-2.5 py-1 rounded-pill fw-bold">
                                            <?= $total ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-success text-success font-size-12 px-2.5 py-1 rounded-pill fw-bold">
                                            <?= $approved ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-warning text-warning font-size-12 px-2.5 py-1 rounded-pill fw-bold">
                                            <?= $pending ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-danger text-danger font-size-12 px-2.5 py-1 rounded-pill fw-bold">
                                            <?= $revision ?>
                                        </span>
                                    </td>
                                    <td class="font-size-12 text-muted">
                                        <?= !empty($m['last_submission_at']) ? date('M d, Y h:i A', strtotime($m['last_submission_at'])) : '<span class="text-muted opacity-50">None yet</span>' ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= AssetHelper::url('unit-reports/submissions?unit_id=' . $m['unit_id'] . '&church_id=' . ($m['church_id'] ?? '')) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bx bx-folder-open me-1"></i> View Ledger
                                        </a>
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
