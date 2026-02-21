<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">AI Recommendations</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= AssetHelper::url('insights') ?>">Member Insights</a></li>
                    <li class="breadcrumb-item active">Recommendations</li>
                </ol>
            </div>
        </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Actionable Recommendations</h4>
                <p class="card-title-desc">AI-generated suggestions for improving member engagement and church operations</p>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <h5><i data-feather="lightbulb" class="me-2"></i> AI-Powered Recommendations</h5>
                    <p>Based on your current data, here are our top recommendations:</p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary-subtle">
                                <h5 class="mb-0"><i data-feather="clock" class="me-2"></i> Immediate Actions</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <strong>Contact high-risk members</strong>
                                        <br><small class="text-muted">5 members showing disengagement signs need immediate attention</small>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Schedule follow-ups</strong>
                                        <br><small class="text-muted">12 pending follow-ups require scheduling within 48 hours</small>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Welcome new visitors</strong>
                                        <br><small class="text-muted">3 first-time attendees from last weekend's service</small>
                                    </li>
                                </ul>
                                <button class="btn btn-primary w-100 mt-3">
                                    <i data-feather="play" class="me-1"></i> Execute Top Priority
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-header bg-info-subtle">
                                <h5 class="mb-0"><i data-feather="target" class="me-2"></i> Strategic Initiatives</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <strong>Small group expansion</strong>
                                        <br><small class="text-muted">Recommend launching 2 new small groups based on interest clustering</small>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Youth ministry opportunity</strong>
                                        <br><small class="text-muted">High engagement among teens suggests potential growth area</small>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Volunteer recruitment</strong>
                                        <br><small class="text-muted">Identified 8 highly engaged members suitable for leadership roles</small>
                                    </li>
                                </ul>
                                <button class="btn btn-info w-100 mt-3">
                                    <i data-feather="clipboard" class="me-1"></i> View Detailed Plan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h5>Resource Recommendations</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i data-feather="user-plus" class="icon-lg text-success mb-3"></i>
                                    <h6>Additional Staff</h6>
                                    <p class="text-muted">Consider adding 1 part-time follow-up coordinator</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i data-feather="calendar" class="icon-lg text-warning mb-3"></i>
                                    <h6>Event Planning</h6>
                                    <p class="text-muted">Schedule monthly engagement events for low-activity members</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i data-feather="message-square" class="icon-lg text-primary mb-3"></i>
                                    <h6>Communication Tools</h6>
                                    <p class="text-muted">Invest in automated messaging system for better outreach</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>