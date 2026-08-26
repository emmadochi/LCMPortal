<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">My Evangelism Reports</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Evangelism Reports</li>
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
                    <h4 class="card-title mb-0">My Reports</h4>
                    <div class="btn-group">
                        <a href="<?= AssetHelper::url('evangelism/export?format=csv') ?>" class="btn btn-outline-secondary">
                            <i class="bx bx-export me-1"></i>Export
                        </a>
                        <a href="<?= AssetHelper::url('evangelism/create') ?>" class="btn btn-primary">
                            <i data-feather="plus" class="me-1"></i> New Report
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Report Date</th>
                                <th>Souls Won</th>
                                <th>Notes</th>
                                <th>Submitted On</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reports)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No reports found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reports as $report): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($report['report_date'])) ?></td>
                                        <td><?= $report['souls_won'] ?></td>
                                        <td><?= htmlspecialchars($report['notes']) ?></td>
                                        <td><?= date('M d, Y H:i', strtotime($report['created_at'])) ?></td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= AssetHelper::url('evangelism/' . (int)$report['id']) ?>" class="btn btn-outline-primary" title="View">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a href="<?= AssetHelper::url('evangelism/' . (int)$report['id'] . '/edit') ?>" class="btn btn-outline-secondary" title="Edit">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form method="POST" action="<?= AssetHelper::url('evangelism/' . (int)$report['id'] . '/delete') ?>" style="display:inline;">
                                                    <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Utilities\Security::generateCSRFToken()) ?>">
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Delete this report?');">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
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
