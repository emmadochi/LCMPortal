<?php
use App\Utilities\AssetHelper;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?= isset($title) ? htmlspecialchars($title) . ' | Church Admin Portal' : 'Church Admin Portal' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= \App\Utilities\Security::generateCSRFToken() ?>">
    <meta content="Church Admin Portal" name="description" />
    <meta content="Church Admin" name="author" />
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= AssetHelper::image('favicon.ico') ?>">
    
    <!-- PWA Manifest & Mobile Web App Meta -->
    <link rel="manifest" href="<?= AssetHelper::url('manifest.json') ?>">
    <meta name="theme-color" content="#4f46e5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="LCM Portal">
    <meta name="sw-path" content="<?= AssetHelper::url('sw.js') ?>">
    <meta name="pwa-icon" content="<?= AssetHelper::image('pwa/icon-192x192.png') ?>">
    <link rel="apple-touch-icon" href="<?= AssetHelper::image('pwa/apple-touch-icon.png') ?>">
    
    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Boxicons CDN Fallback -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Bootstrap Css -->
    <link href="<?= AssetHelper::css('bootstrap.min.css') ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?= AssetHelper::css('icons.min.css') ?>" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="<?= AssetHelper::css('app.min.css') ?>" id="app-style" rel="stylesheet" type="text/css" />
    <!-- Custom Admin Css -->
    <link href="<?= AssetHelper::css('admin-custom.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= AssetHelper::css('premium-theme.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= AssetHelper::css('mobile-pwa.css') ?>?v=<?= time() ?>" rel="stylesheet" type="text/css" />
    
    <!-- DataTables -->
    <link href="<?= AssetHelper::lib('datatables.net-bs4/css/dataTables.bootstrap4.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= AssetHelper::lib('datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') ?>" rel="stylesheet" type="text/css" />
    
    <!-- Select2 -->
    <link href="<?= AssetHelper::lib('select2/css/select2.min.css') ?>" rel="stylesheet" type="text/css" />
    
    <!-- SweetAlert2 -->
    <link href="<?= AssetHelper::lib('sweetalert2/sweetalert2.min.css') ?>" rel="stylesheet" type="text/css" />
    
    <!-- Custom CSS -->
    <style>
        body, .main-content, .page-content {
            font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        }
        .sidebar-menu li.mm-active > a {
            color: #4f46e5 !important;
            background-color: rgba(79, 70, 229, 0.08) !important;
        }
        .sidebar-menu li.mm-active .has-arrow:after {
            color: #4f46e5 !important;
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



                    <!-- Operations & Engagement -->
                    <?php if ($this->session->hasPermission('view_dashboard') || $this->session->isHeadPastor()): ?>
                    <li class="menu-title" key="t-operations">Operations</li>
                    
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-calendar-check"></i>
                            <span key="t-attendance">Attendance</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <?php if ($this->session->isHeadPastor()): ?>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '/attendance') ?>" key="t-hp-attendance-list">Dashboard & Services</a></li>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '/attendance/mark') ?>" key="t-hp-record-attendance">Mark Attendance (Roll-Call)</a></li>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '/attendance/records') ?>" key="t-hp-attendance-records">Attendance Ledger</a></li>
                            <?php else: ?>
                            <li><a href="<?= AssetHelper::url('attendance') ?>" key="t-attendance-dash">Dashboard & Services</a></li>
                            <li><a href="<?= AssetHelper::url('attendance/mark') ?>" key="t-mark-attendance">Mark Attendance (Roll-Call)</a></li>
                            <li><a href="<?= AssetHelper::url('attendance/create') ?>" key="t-record-attendance">Single Check-In</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-clipboard"></i>
                            <span key="t-follow-ups">Follow-ups & Care</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('follow-ups') ?>" key="t-follow-up-list">Pending Follow-ups</a></li>
                            <li><a href="<?= AssetHelper::url('follow-ups/create') ?>" key="t-add-follow-up">Create New</a></li>
                        </ul>
                    </li>

                    <?php if ($this->session->get('can_send_notifications', false) || $this->session->hasPermission('send_broadcast_notifications')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-bell"></i>
                            <span key="t-communications">Communications</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('notifications/' . $this->session->get('church_id')) ?>" key="t-notification-history">History</a></li>
                            <li><a href="<?= AssetHelper::url('notifications/' . $this->session->get('church_id') . '/create') ?>" key="t-send-notification">Send Broadcast</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>

                    <!-- Administration & Management -->
                    <?php if ($this->session->hasPermission('manage_users') || $this->session->isHeadPastor() || $this->session->isDirector()): ?>
                    <li class="menu-title" key="t-administration">Administration</li>

                    <?php if ($this->session->hasPermission('manage_users') || $this->session->isHeadPastor()): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-user-shield"></i>
                            <span key="t-people-security"><?= $this->session->isHeadPastor() ? 'Members & Directory' : 'People & Security' ?></span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="<?= AssetHelper::url('members') ?>" key="t-member-dir">Member Directory</a></li>
                            <li><a href="<?= AssetHelper::url('members/create') ?>" key="t-add-member">Add New Member</a></li>
                            <?php if ($this->session->hasPermission('manage_users')): ?>
                            <li><a href="<?= AssetHelper::url('users') ?>" key="t-sys-users">System Users</a></li>
                            <li><a href="<?= AssetHelper::url('admin/password-reset-requests') ?>" key="t-pw-resets">Password Requests</a></li>
                            <li><a href="<?= AssetHelper::url('activity-logs') ?>" key="t-activity">Activity Logs</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-buildings"></i>
                            <span key="t-churches-units">Churches & Units</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <?php if ($this->session->hasPermission('manage_users')): ?>
                            <li><a href="<?= AssetHelper::url('churches') ?>" key="t-church-ctrl">Churches Control</a></li>
                            <li><a href="<?= AssetHelper::url('units') ?>" key="t-manage-units">Manage Units</a></li>
                            <?php endif; ?>
                            <?php if ($this->session->isHeadPastor()): ?>
                            <li><a href="<?= AssetHelper::url('units') ?>" key="t-hp-units">Units & Leadership</a></li>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '#units-section') ?>" key="t-unit-mgmt">Unit Management</a></li>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '/performance') ?>" key="t-perf-matrix">Performance Matrix</a></li>
                            <?php endif; ?>
                            <?php if ($this->session->isDirector()): ?>
                            <?php foreach ($this->session->getDirectorUnits() as $unit): ?>
                            <li><a href="<?= AssetHelper::url('units/' . $unit['id']) ?>"><?= htmlspecialchars($unit['name']) ?></a></li>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <?php if ($this->session->hasPermission('manage_users') || $this->session->isHeadPastor()): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-money"></i>
                            <span key="t-finance-assets">Finances & Budgets</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <?php if ($this->session->isHeadPastor()): ?>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '/finance') ?>" key="t-fin-dash">Financial Dashboard</a></li>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '/budgets') ?>" key="t-hp-budgets">Budget Management</a></li>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '/pledges') ?>" key="t-hp-pledges">Pledges & Campaigns</a></li>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '/finance/cashflow') ?>" key="t-hp-cashflow">Cashflow & Trends</a></li>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '/finance/audit-trail') ?>" key="t-hp-audit">Audit Trail</a></li>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '/property') ?>" key="t-prop-dash">Property Dashboard</a></li>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '/projects') ?>" key="t-proj-dash">Projects Dashboard</a></li>
                            <?php endif; ?>
                            <?php if ($this->session->hasPermission('manage_users')): ?>
                            <li><a href="<?= AssetHelper::url('finance') ?>" key="t-admin-fin-dash">Financial Dashboard</a></li>
                            <li><a href="<?= AssetHelper::url('budgets') ?>" key="t-admin-budgets">Budget Management</a></li>
                            <li><a href="<?= AssetHelper::url('pledges') ?>" key="t-admin-pledges">Pledges & Campaigns</a></li>
                            <li><a href="<?= AssetHelper::url('finance/cashflow') ?>" key="t-admin-cashflow">Cashflow & Trends</a></li>
                            <li><a href="<?= AssetHelper::url('finance/audit-trail') ?>" key="t-admin-audit">Audit Trail</a></li>
                            <li><a href="<?= AssetHelper::url('admin/finance-report') ?>" key="t-admin-finance">Global Financial Report</a></li>
                            <li><a href="<?= AssetHelper::url('properties') ?>" key="t-global-prop">Global Properties</a></li>
                            <li><a href="<?= AssetHelper::url('property-categories') ?>" key="t-prop-cats">Property Categories</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if ($this->session->hasPermission('manage_users') || $this->session->isHeadPastor() || $this->session->hasPermission('manage_reports')): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-bar-chart-square"></i>
                            <span key="t-reports-analytics">Reports & Analytics</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <?php if ($this->session->isHeadPastor()): ?>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '/unit-reports') ?>" key="t-unit-rep">Unit Narrative Reports</a></li>
                            <li><a href="<?= AssetHelper::url('churches/' . $this->session->getHeadPastorChurchId() . '/outreach') ?>" key="t-outreach-rep">Outreach Reports</a></li>
                            <?php else: ?>
                            <li><a href="<?= AssetHelper::url('reports/' . $this->session->get('church_id')) ?>" key="t-gen-rep">General Reports</a></li>
                            <li><a href="<?= AssetHelper::url('outreach-reports') ?>" key="t-outreach-rep">Outreach Reports</a></li>
                            <?php endif; ?>
                            <?php if ($this->session->hasPermission('manage_users')): ?>
                            <li><a href="<?= AssetHelper::url('admin/attendance-overview') ?>" key="t-admin-att">Global Attendance Overview</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php endif; ?>

                    <!-- Personal Space -->
                    <li class="menu-title" key="t-personal-space">Personal Space</li>

                    <li>
                        <a href="<?= AssetHelper::url('profile') ?>" class="<?= (strpos($_SERVER['REQUEST_URI'], '/profile') !== false) ? 'active' : '' ?> waves-effect">
                            <i class="bx bx-user-circle"></i>
                            <span key="t-member-profile">My Profile</span>
                        </a>
                    </li>
                    <?php if ($this->session->get('user_role') === 'user' || $this->session->hasPermission('view_dashboard')): ?>
                    <li>
                        <a href="<?= AssetHelper::url('attendance/my-history') ?>" class="<?= (strpos($_SERVER['REQUEST_URI'], '/attendance/my-history') !== false) ? 'active' : '' ?> waves-effect">
                            <i class="bx bx-calendar-check"></i>
                            <span key="t-my-attendance">My Attendance</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= AssetHelper::url('giving/my-records') ?>" class="<?= (strpos($_SERVER['REQUEST_URI'], '/giving/my-records') !== false) ? 'active' : '' ?> waves-effect">
                            <i class="bx bx-money"></i>
                            <span key="t-my-giving">My Giving</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= AssetHelper::url('giving/my-pledges') ?>" class="<?= (strpos($_SERVER['REQUEST_URI'], '/giving/my-pledges') !== false) ? 'active' : '' ?> waves-effect">
                            <i class="bx bx-gift"></i>
                            <span key="t-my-pledges">My Pledges</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= AssetHelper::url('my-units') ?>" class="<?= (strpos($_SERVER['REQUEST_URI'], '/my-units') !== false) ? 'active' : '' ?> waves-effect">
                            <i class="bx bx-group"></i>
                            <span key="t-my-departments">My Departments</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- PWA Quick Install Button in Sidebar -->
                    <li class="pwa-install-trigger" style="display: none;">
                        <a href="javascript:void(0);" class="waves-effect text-primary fw-bold" style="background: rgba(79, 70, 229, 0.08); border-radius: 8px; margin: 8px 12px;">
                            <i class="bx bx-download text-primary"></i>
                            <span>Install LCM App</span>
                        </a>
                    </li>

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
                <?php if ($this->session->hasFlash('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $this->session->getFlash('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->hasFlash('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $this->session->getFlash('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->hasFlash('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            <?php foreach ($this->session->getFlash('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

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

<!-- Mobile App Bottom Navigation Dock (PWA Ergonomics) -->
<nav class="mobile-app-dock" style="display: none;" aria-label="Mobile Navigation">
    <a href="<?= AssetHelper::url('') ?>" class="mobile-dock-item <?= ($_SERVER['REQUEST_URI'] === '/' || strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : '' ?>">
        <i class="bx bx-home-alt-2"></i>
        <span>Home</span>
    </a>
    <a href="<?= AssetHelper::url($this->session->isHeadPastor() ? 'churches/' . $this->session->getHeadPastorChurchId() . '/finance' : 'finance') ?>" class="mobile-dock-item <?= (strpos($_SERVER['REQUEST_URI'], '/finance') !== false) ? 'active' : '' ?>">
        <i class="bx bx-wallet"></i>
        <span>Finances</span>
    </a>
    <a href="<?= AssetHelper::url($this->session->isHeadPastor() ? 'churches/' . $this->session->getHeadPastorChurchId() . '/attendance' : 'attendance') ?>" class="mobile-dock-item <?= (strpos($_SERVER['REQUEST_URI'], '/attendance') !== false) ? 'active' : '' ?>">
        <i class="bx bx-calendar-check"></i>
        <span>Attendance</span>
    </a>
    <?php if ($this->session->hasPermission('manage_users')): ?>
    <a href="<?= AssetHelper::url('churches') ?>" class="mobile-dock-item <?= (strpos($_SERVER['REQUEST_URI'], '/churches') !== false) ? 'active' : '' ?>">
        <i class="bx bx-church"></i>
        <span>Branches</span>
    </a>
    <?php else: ?>
    <a href="<?= AssetHelper::url('profile') ?>" class="mobile-dock-item <?= (strpos($_SERVER['REQUEST_URI'], '/profile') !== false) ? 'active' : '' ?>">
        <i class="bx bx-user-circle"></i>
        <span>Profile</span>
    </a>
    <?php endif; ?>
    <a href="javascript:void(0);" id="vertical-menu-btn-mobile" class="mobile-dock-item" onclick="document.body.classList.toggle('sidebar-enable');">
        <i class="bx bx-menu"></i>
        <span>Menu</span>
    </a>
</nav>

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

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<!-- App js -->
<script src="<?= AssetHelper::js('app.js') ?>"></script>

<!-- PWA Installation & Service Worker registration -->
<script src="<?= AssetHelper::js('pwa-install.js') ?>?v=<?= time() ?>"></script>

<script>
// Setup jQuery AJAX to automatically include CSRF token in header
if (typeof jQuery !== 'undefined') {
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
    });
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?= $pageJs ?? '' ?>

</body>
</html>
