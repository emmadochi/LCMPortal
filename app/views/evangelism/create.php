<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">New Evangelism Report</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('evangelism') ?>">Evangelism Reports</a></li>
                    <li class="breadcrumb-item active">New Report</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">New Report</h4>
            </div>
            <div class="card-body">
                <form action="<?= AssetHelper::url('evangelism/store') ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="report_date" class="form-label">Report Date</label>
                                <input type="date" class="form-control" id="report_date" name="report_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="souls_won" class="form-label">Souls Won</label>
                                <input type="number" class="form-control" id="souls_won" name="souls_won" required min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="5"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </form>
            </div>
        </div>
    </div>
</div>
