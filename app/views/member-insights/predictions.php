<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Predictive Analytics</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('insights') ?>">Member Insights</a></li>
                    <li class="breadcrumb-item active">Predictions</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Future Trends & Predictions</h4>
                <p class="card-title-desc">AI-powered predictions for member engagement and church growth</p>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i data-feather="cpu" class="me-2"></i> AI Predictive Analytics Coming Soon</h5>
                    <p>This section will feature:</p>
                    <ul>
                        <li>Member retention predictions</li>
                        <li>Engagement trend forecasting</li>
                        <li>Growth projections</li>
                        <li>Resource planning recommendations</li>
                    </ul>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i data-feather="trending-up" class="icon-lg text-success mb-3"></i>
                                <h5>Engagement Forecast</h5>
                                <p class="text-muted">Predicted engagement trends for next quarter</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <i data-feather="users" class="icon-lg text-primary mb-3"></i>
                                <h5>Membership Growth</h5>
                                <p class="text-muted">Projected membership increases/decreases</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <i data-feather="alert-triangle" class="icon-lg text-warning mb-3"></i>
                                <h5>Risk Predictions</h5>
                                <p class="text-muted">Early warning systems for member disengagement</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>