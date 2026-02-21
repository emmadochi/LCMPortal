<?php
use App\Utilities\AssetHelper;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?= isset($title) ? htmlspecialchars($title) . ' | Church Admin Portal' : 'Church Admin Portal' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Church Admin Portal" name="description" />
    <meta content="Church Admin" name="author" />
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= AssetHelper::image('favicon.ico') ?>"
    
    <!-- preloader css -->
    <link rel="stylesheet" href="<?= AssetHelper::css('preloader.min.css') ?>" type="text/css" />
    
    <!-- Bootstrap Css -->
    <link href="<?= AssetHelper::css('bootstrap.min.css') ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?= AssetHelper::css('icons.min.css') ?>" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="<?= AssetHelper::css('app.min.css') ?>" id="app-style" rel="stylesheet" type="text/css" />
    
    <!-- DataTables -->
    <link href="<?= AssetHelper::lib('datatables.net-bs4/css/dataTables.bootstrap4.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= AssetHelper::lib('datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') ?>" rel="stylesheet" type="text/css" />
    
    <!-- Select2 -->
    <link href="<?= AssetHelper::lib('select2/css/select2.min.css') ?>" rel="stylesheet" type="text/css" />
    
    <!-- SweetAlert2 -->
    <link href="<?= AssetHelper::lib('sweetalert2/sweetalert2.min.css') ?>" rel="stylesheet" type="text/css" />
    
    <!-- Custom CSS -->
    <style>
        .sidebar-menu li.mm-active > a {
            color: #556ee6 !important;
            background-color: rgba(85, 110, 230, 0.1) !important;
        }
        .sidebar-menu li.mm-active .has-arrow:after {
            color: #556ee6 !important;
        }
    </style>
</head>
<body>

<!-- Begin page -->
<div id="layout-wrapper">

    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box">
                    <a href="<?= AssetHelper::url('') ?>" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="<?= AssetHelper::image('logo-sm.svg') ?>" alt="" height="24">
                        </span>
                        <span class="logo-lg">
                            <img src="<?= AssetHelper::image('logo-sm.svg') ?>" alt="" height="24"> 
                            <span class="logo-txt">Church Portal</span>
                        </span>
                    </a>

                    <a href="<?= AssetHelper::url('') ?>" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="<?= AssetHelper::image('logo-sm.svg') ?>" alt="" height="24">
                        </span>
                        <span class="logo-lg">
                            <img src="<?= AssetHelper::image('logo-sm.svg') ?>" alt="" height="24"> 
                            <span class="logo-txt">Church Portal</span>
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                    <i class="fa fa-fw fa-bars"></i>
                </button>
            </div>

            <div class="d-flex">
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="rounded-circle header-profile-user" 
                             src="<?= $this->session->has('user_profile_picture') ? 
                                   AssetHelper::url($this->session->get('user_profile_picture')) : 
                                   AssetHelper::image('users/avatar-1.jpg') ?>" 
                             alt="Header Avatar">
                        <span class="d-none d-xl-inline-block ms-1" key="t-henry">
                            <?= htmlspecialchars($this->session->get('user_name', 'User')) ?>
                        </span>
                        <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a class="dropdown-item" href="<?= AssetHelper::url('profile') ?>">
                            <i class="bx bx-user font-size-16 align-middle me-1"></i> 
                            <span key="t-profile">Profile</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="<?= AssetHelper::url('logout') ?>">
                            <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> 
                            <span key="t-logout">Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ========== Left Sidebar Start ========== -->
    <div class="vertical-menu">
        <div data-simplebar class="h-100">
            <!--- Sidemenu -->
            <div id="sidebar-menu">
                <!-- Left Menu Start -->
                <ul class="metismenu list-unstyled" id="side-menu">
                    <li class="menu-title" key="t-menu">Menu</li>

                    <li>
                        <a href="<?= AssetHelper::url('') ?>" class="<?= (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false || $_SERVER['REQUEST_URI'] === '/') ? 'active' : '' ?>">
                            <i class="bx bx-home-circle"></i>
                            <span key="t-dashboards">Dashboard</span>
                        </a>
                    </li>

                    <?php if ($this->session->hasPermission('manage_users')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-user"></i>
                            <span key="t-users">Users</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('users') ?>" key="t-user-list">User List</a></li>
                            <li><a href="<?= AssetHelper::url('users/create') ?>" key="t-add-user">Add User</a></li>
                        </ul>
                    </li>
                    
                    <li>
                        <a href="<?= AssetHelper::url('admin/password-reset-requests') ?>" class="<?= (strpos($_SERVER['REQUEST_URI'], '/admin/password-reset-requests') !== false) ? 'active' : '' ?>">
                            <i class="bx bx-key"></i>
                            <span key="t-password-requests">Password Requests</span>
                        </a>
                    </li>
                    
                    <li class="menu-title" key="t-apps">Apps</li>
                    <?php endif; ?>

                    <!-- Units Management -->
                    <?php if ($this->session->hasPermission('manage_units') || $this->session->hasPermission('view_unit_reports')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-building"></i>
                            <span key="t-units">Units</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('units') ?>" key="t-unit-list">Unit List</a></li>
                            <li><a href="<?= AssetHelper::url('units/create') ?>" key="t-add-unit">Add Unit</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Members Management -->
                    <?php if ($this->session->hasPermission('manage_users') || $this->session->hasPermission('view_unit_reports')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-group"></i>
                            <span key="t-members">Members</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('members') ?>" key="t-member-list">Member List</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Reports Management -->
                    <?php if ($this->session->hasPermission('manage_reports') || $this->session->hasPermission('view_all_reports') || $this->session->hasPermission('create_reports')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-file"></i>
                            <span key="t-reports">Reports</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('reports') ?>" key="t-report-list">Report List</a></li>
                            <li><a href="<?= AssetHelper::url('reports/create') ?>" key="t-add-report">Create Report</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Attendance Management -->
                    <?php if ($this->session->hasPermission('manage_reports') || $this->session->hasPermission('view_unit_reports')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-calendar-check"></i>
                            <span key="t-attendance">Attendance</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('attendance') ?>" key="t-attendance-list">Attendance List</a></li>
                            <li><a href="<?= AssetHelper::url('attendance/create') ?>" key="t-record-attendance">Record Attendance</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Finance Management -->
                    <?php if ($this->session->hasPermission('manage_finance') || $this->session->hasPermission('manage_unit_finance')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-money"></i>
                            <span key="t-finance">Finance</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('finance') ?>" key="t-finance-list">Finance Records</a></li>
                            <li><a href="<?= AssetHelper::url('finance/create') ?>" key="t-add-transaction">Add Transaction</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Media Management -->
                    <?php if ($this->session->hasPermission('manage_media') || $this->session->hasPermission('manage_unit_media')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-image"></i>
                            <span key="t-media">Media</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('media') ?>" key="t-media-library">Media Library</a></li>
                            <li><a href="<?= AssetHelper::url('media/create') ?>" key="t-upload-media">Upload Media</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Projects Management -->
                    <?php if ($this->session->hasPermission('manage_projects') || $this->session->hasPermission('manage_unit_projects')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-briefcase"></i>
                            <span key="t-projects">Projects</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('projects') ?>" key="t-project-list">Project List</a></li>
                            <li><a href="<?= AssetHelper::url('projects/create') ?>" key="t-add-project">Add Project</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Follow-ups Management -->
                    <?php if ($this->session->hasPermission('view_dashboard')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-clipboard"></i>
                            <span key="t-follow-ups">Follow-ups</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('follow-ups') ?>" key="t-follow-up-list">Follow-up List</a></li>
                            <li><a href="<?= AssetHelper::url('follow-ups/create') ?>" key="t-add-follow-up">Create Follow-up</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Church Management (Admin only) -->
                    <?php if ($this->session->hasPermission('manage_users')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-church"></i>
                            <span key="t-churches">Churches</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('churches') ?>" key="t-church-list">Church List</a></li>
                            <li><a href="<?= AssetHelper::url('churches/create') ?>" key="t-add-church">Add Church</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Activity Logs (Admin only) -->
                    <?php if ($this->session->hasPermission('manage_users')): ?>
                    <li>
                        <a href="<?= AssetHelper::url('activity-logs') ?>">
                            <i class="bx bx-history"></i>
                            <span key="t-activity-logs">Activity Logs</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- Notifications -->
                    <?php if ($this->session->get('can_send_notifications', false) || $this->session->hasPermission('send_broadcast_notifications')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-bell"></i>
                            <span key="t-notifications">Notifications</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('notifications') ?>" key="t-notification-list">Notification List</a></li>
                            <li><a href="<?= AssetHelper::url('notifications/create') ?>" key="t-send-notification">Send Notification</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Property Management -->
                    <?php if ($this->session->hasPermission('manage_users')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-home-alt"></i>
                            <span key="t-property">Property</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('properties') ?>" key="t-property-list">Property List</a></li>
                            <li><a href="<?= AssetHelper::url('properties/create') ?>" key="t-add-property">Add Property</a></li>
                            <li><a href="<?= AssetHelper::url('property-categories') ?>" key="t-property-categories">Categories</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Outreach Reports -->
                    <?php if ($this->session->hasPermission('manage_reports') || $this->session->hasPermission('view_all_reports')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-world"></i>
                            <span key="t-outreach-reports">Outreach Reports</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('outreach-reports') ?>" key="t-outreach-list">Reports List</a></li>
                            <li><a href="<?= AssetHelper::url('outreach-reports/create') ?>" key="t-add-outreach">Create Report</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- Sidebar -->
        </div>
    </div>
    <!-- Left Sidebar End -->

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <!-- Content -->
                <?= $content ?? '' ?>
            </div>
        </div>
        <!-- End Page-content -->

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <script>document.write(new Date().getFullYear())</script> © Church Admin Portal
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            Crafted with <i class="mdi mdi-heart text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->

<!-- Right Sidebar -->
<!-- Add if needed -->

<!-- JAVASCRIPT -->
<script src="<?= AssetHelper::lib('jquery/jquery.min.js') ?>"></script>
<script src="<?= AssetHelper::lib('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= AssetHelper::lib('metismenu/metisMenu.min.js') ?>"></script>
<script src="<?= AssetHelper::lib('simplebar/simplebar.min.js') ?>"></script>
<script src="<?= AssetHelper::lib('node-waves/waves.min.js') ?>"></script>
<script src="<?= AssetHelper::lib('feather-icons/feather.min.js') ?>"></script>

<!-- DataTables -->
<script src="<?= AssetHelper::lib('datatables.net/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= AssetHelper::lib('datatables.net-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= AssetHelper::lib('datatables.net-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= AssetHelper::lib('datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') ?>"></script>

<!-- Select2 -->
<script src="<?= AssetHelper::lib('select2/js/select2.min.js') ?>"></script>

<!-- SweetAlert2 -->
<script src="<?= AssetHelper::lib('sweetalert2/sweetalert2.min.js') ?>"></script>

<!-- Password addon init -->
<script src="<?= AssetHelper::js('pages/pass-addon.init.js') ?>"></script>

<!-- App js -->
<script src="<?= AssetHelper::js('app.js') ?>"></script>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

</body>
</html>
