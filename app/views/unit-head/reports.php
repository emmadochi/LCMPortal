<!-- Sub-header with Assignment Selector -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #5b73e8 0%, #4430e7 100%);">
            <div class="card-body p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-white mb-1 fw-bold">Unit Workspace: <?= htmlspecialchars($unitName) ?></h3>
                        <p class="text-white-50 mb-0 font-size-14"><i class="bx bx-church me-1"></i> Branch: <?= htmlspecialchars($churchName) ?></p>
                    </div>
                    <?php if (count($assignments) > 1): ?>
                    <div class="col-md-4 mt-3 mt-md-0">
                        <div class="d-flex justify-content-md-end align-items-center">
                            <label class="text-white-50 me-2 text-nowrap font-size-13 mb-0">Switch Workspace:</label>
                            <select name="switch_assignment" class="form-select form-select-sm bg-white text-dark border-0 shadow-sm" style="width: auto; min-width: 180px;" onchange="window.location.href = window.location.pathname + '?church_id=' + this.value.split('-')[0] + '&unit_id=' + this.value.split('-')[1];">
                                <?php foreach ($assignments as $assign): ?>
                                    <option value="<?= $assign['church_id'] ?>-<?= $assign['unit_id'] ?>" <?= ((int)$assign['church_id'] === (int)$churchId && (int)$assign['unit_id'] === (int)$unitId) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($assign['unit_name'] . ' (' . $assign['church_name'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- narrative Reports list -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                <h5 class="mb-0 fw-bold"><i class="bx bx-file-find me-2 text-primary"></i>Narrative Reports</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newReportModal">
                    <i class="bx bx-plus me-1"></i>New Report
                </button>
            </div>
            <div class="card-body px-4 pb-4">
                <?php if (empty($reports)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bx bx-file display-2"></i>
                        <h5 class="mt-3">No Reports Found</h5>
                        <p>No reports have been submitted for this unit branch yet.</p>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newReportModal">
                            Submit First Report
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Submitted By</th>
                                    <th>Submission Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $report): ?>
                                    <tr>
                                        <td>
                                            <h5 class="font-size-14 mb-0 fw-semibold text-dark"><?= htmlspecialchars($report['title']) ?></h5>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-info text-info font-size-11">
                                                <?= htmlspecialchars(ucfirst($report['report_type'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-dark">
                                            <?= htmlspecialchars($report['first_name'] . ' ' . $report['last_name']) ?>
                                        </td>
                                        <td class="text-muted"><?= date('F j, Y g:i A', strtotime($report['created_at'])) ?></td>
                                        <td>
                                            <span class="badge bg-soft-success text-success">Submitted</span>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewReportModal_<?= $report['id'] ?>">
                                                <i class="bx bx-show me-1"></i>View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- View Modal for each report -->
                                    <div class="modal fade" id="viewReportModal_<?= $report['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white"><?= htmlspecialchars($report['title']) ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <p class="text-muted mb-1 font-size-12">Report Type</p>
                                                            <span class="badge bg-soft-info text-info font-size-13"><?= ucfirst($report['report_type']) ?></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="text-muted mb-1 font-size-12">Submitted On</p>
                                                            <span class="text-dark fw-semibold"><?= date('F j, Y g:i A', strtotime($report['created_at'])) ?></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="mb-0">
                                                        <p class="text-muted mb-2 font-size-12">Report Body</p>
                                                        <div class="p-3 bg-light rounded text-dark" style="white-space: pre-wrap; line-height: 1.6; min-height: 150px;"><?= htmlspecialchars($report['content']) ?></div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Submit Report Modal -->
<div class="modal fade" id="newReportModal" tabindex="-1" aria-labelledby="newReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="newReportModalLabel">Submit narrative Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= \App\Utilities\AssetHelper::url("my-unit/reports/store") ?>">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="church_id" value="<?= $churchId ?>">
                <input type="hidden" name="unit_id" value="<?= $unitId ?>">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Report Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="e.g. Choir Weekly Report - Week 24" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="report_type" class="form-label">Report Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="report_type" name="report_type" required>
                                <?php foreach ($reportTypes as $type): ?>
                                    <option value="<?= $type ?>"><?= ucfirst($type) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Narrative / Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="10" placeholder="Provide details of your unit's activities, challenges, achievements, etc..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
