<?php
use App\Core\Session;
use App\Core\Request;
use App\Utilities\AssetHelper;

$session = Session::getInstance();
$userRole = $session->get('user_role', 'user');
$request = new Request();
$currentUri = $request->uri();

// Helper function to check if menu item is active
function isActive($uri, $currentUri) {
    return strpos($currentUri, $uri) !== false ? 'active' : '';
}
?>
<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Menu</li>

                <li>
                    <a href="<?= AssetHelper::url('/') ?>" class="<?= isActive('/', $currentUri) ?>">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                <?php if ($userRole === 'admin' || $userRole === 'director'): ?>
                <li>
                    <a href="javascript: void(0);" class="has-arrow <?= isActive('/units', $currentUri) ?>">
                        <i data-feather="users"></i>
                        <span data-key="t-units">Units</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="<?= AssetHelper::url('units') ?>" class="<?= isActive('/units', $currentUri) ?>">
                                <span data-key="t-units-list">All Units</span>
                            </a>
                        </li>
                        <?php if ($userRole === 'admin'): ?>
                        <li>
                            <a href="<?= AssetHelper::url('units/create') ?>" class="<?= isActive('/units/create', $currentUri) ?>">
                                <span data-key="t-create-unit">Create Unit</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if ($userRole === 'admin'): ?>
                <li>
                    <a href="javascript: void(0);" class="has-arrow <?= isActive('/users', $currentUri) ?>">
                        <i data-feather="user"></i>
                        <span data-key="t-users">Users</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="<?= AssetHelper::url('users') ?>" class="<?= isActive('/users', $currentUri) ?>">
                                <span data-key="t-users-list">All Users</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('users/create') ?>" class="<?= isActive('/users/create', $currentUri) ?>">
                                <span data-key="t-create-user">Create User</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if ($userRole === 'admin'): ?>
                <li>
                    <a href="javascript: void(0);" class="has-arrow <?= isActive('/churches', $currentUri) ?>">
                        <i data-feather="layers"></i>
                        <span data-key="t-churches">Church Management</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="<?= AssetHelper::url('churches') ?>" class="<?= isActive('/churches', $currentUri) ?>">
                                <span data-key="t-churches-list">All Churches</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('churches/create') ?>" class="<?= isActive('/churches/create', $currentUri) ?>">
                                <span data-key="t-create-church">Create Church</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('targets') ?>" class="<?= isActive('/targets', $currentUri) ?>">
                                <span>Church & Unit Targets</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('targets/create') ?>" class="<?= isActive('/targets/create', $currentUri) ?>">
                                <span>Set Target</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if ($userRole === 'admin' || $userRole === 'director' || $session->isHeadPastor()): ?>
                <li>
                    <a href="<?= AssetHelper::url('members') ?>" class="<?= isActive('/members', $currentUri) ?>">
                        <i data-feather="user-check"></i>
                        <span data-key="t-members">Member Directory</span>
                    </a>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow <?= isActive('/follow-ups', $currentUri) ?>">
                        <i data-feather="clipboard"></i>
                        <span data-key="t-followups">Follow-ups</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="<?= AssetHelper::url('follow-ups') ?>" class="<?= isActive('/follow-ups', $currentUri) ?>">
                                <span>All Follow-ups</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('follow-ups/create') ?>" class="<?= isActive('/follow-ups/create', $currentUri) ?>">
                                <span>Create Follow-up</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('follow-ups/statistics') ?>" class="<?= isActive('/follow-ups/statistics', $currentUri) ?>">
                                <span>Statistics</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <li>
                    <a href="javascript: void(0);" class="has-arrow <?= isActive('/outreach-reports', $currentUri) ?>">
                        <i data-feather="bar-chart-2"></i>
                        <span>Outreach / Event Reports</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="<?= AssetHelper::url('outreach-reports') ?>" class="<?= isActive('/outreach-reports', $currentUri) ?>">
                                <span>All Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('outreach-reports/create') ?>" class="<?= isActive('/outreach-reports/create', $currentUri) ?>">
                                <span>New Report</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow <?= isActive('/reports', $currentUri) ?>">
                        <i data-feather="file-text"></i>
                        <span data-key="t-reports">Reports</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="<?= AssetHelper::url('reports') ?>" class="<?= isActive('/reports', $currentUri) ?>">
                                <span data-key="t-reports-list">All Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('reports/create') ?>" class="<?= isActive('/reports/create', $currentUri) ?>">
                                <span data-key="t-create-report">Create Report</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow <?= isActive('/attendance', $currentUri) ?>">
                        <i data-feather="calendar"></i>
                        <span data-key="t-attendance">Attendance</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="<?= AssetHelper::url('attendance') ?>" class="<?= isActive('/attendance', $currentUri) ?>">
                                <span data-key="t-attendance-list">View Attendance</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('attendance/mark') ?>" class="<?= isActive('/attendance/mark', $currentUri) ?>">
                                <span data-key="t-mark-attendance">Mark (roll-call)</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('attendance/create') ?>" class="<?= isActive('/attendance/create', $currentUri) ?>">
                                <span data-key="t-record-attendance">Record single</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <?php if ($userRole === 'admin' || $userRole === 'director'): ?>
                <li>
                    <a href="javascript: void(0);" class="has-arrow <?= isActive('/finance', $currentUri) ?>">
                        <i data-feather="dollar-sign"></i>
                        <span data-key="t-finance">Finance</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="<?= AssetHelper::url('finance') ?>" class="<?= isActive('/finance', $currentUri) ?>">
                                <span data-key="t-finance-list">Financial Records</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('finance/create') ?>" class="<?= isActive('/finance/create', $currentUri) ?>">
                                <span data-key="t-add-record">Add Record</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <li>
                    <a href="javascript: void(0);" class="has-arrow <?= isActive('/media', $currentUri) ?>">
                        <i data-feather="image"></i>
                        <span data-key="t-media">Media</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="<?= AssetHelper::url('media') ?>" class="<?= isActive('/media', $currentUri) ?>">
                                <span data-key="t-media-library">Media Library</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('media/create') ?>" class="<?= isActive('/media/create', $currentUri) ?>">
                                <span data-key="t-upload-media">Upload Media</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow <?= isActive('/projects', $currentUri) ?>">
                        <i data-feather="briefcase"></i>
                        <span data-key="t-projects">Projects</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="<?= AssetHelper::url('projects') ?>" class="<?= isActive('/projects', $currentUri) ?>">
                                <span data-key="t-projects-list">All Projects</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('projects/create') ?>" class="<?= isActive('/projects/create', $currentUri) ?>">
                                <span data-key="t-create-project">Create Project</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <?php if ($userRole === 'admin'): ?>
                <li>
                    <a href="javascript: void(0);" class="has-arrow <?= isActive('/properties', $currentUri) || isActive('/property-categories', $currentUri) ?>">
                        <i data-feather="package"></i>
                        <span>Properties</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="<?= AssetHelper::url('properties') ?>" class="<?= isActive('/properties', $currentUri) ?>">
                                <span>All Properties</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('properties/create') ?>" class="<?= isActive('/properties/create', $currentUri) ?>">
                                <span>Add Property</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('property-categories') ?>" class="<?= isActive('/property-categories', $currentUri) ?>">
                                <span>Categories</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <li>
                    <a href="<?= AssetHelper::url('notifications/show') ?>" class="<?= isActive('/notifications/show', $currentUri) ?>">
                        <i data-feather="bell"></i>
                        <span data-key="t-notifications">Notifications</span>
                    </a>
                </li>
                <?php if ($session->get('can_send_notifications', false)): ?>
                <li>
                    <a href="<?= AssetHelper::url('notifications/create') ?>" class="<?= isActive('/notifications/create', $currentUri) ?>">
                        <i data-feather="send"></i>
                        <span>Send Notification</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if ($userRole === 'admin'): ?>
                <li>
                    <a href="<?= AssetHelper::url('activity-logs') ?>" class="<?= isActive('/activity-logs', $currentUri) ?>">
                        <i data-feather="activity"></i>
                        <span data-key="t-activity-logs">Activity Logs</span>
                    </a>
                </li>
                <?php endif; ?>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->

