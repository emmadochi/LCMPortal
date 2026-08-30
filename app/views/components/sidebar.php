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

                <li>
                    <a href="<?= AssetHelper::url('evangelism') ?>" class="<?= isActive('/evangelism', $currentUri) ?>">
                        <i data-feather="award"></i>
                        <span data-key="t-evangelism">Evangelism</span>
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

                <?php if ($userRole === 'admin' || $session->isHeadPastor()): ?>
                <li>
                    <a href="javascript: void(0);" class="has-arrow <?= isActive('/churches', $currentUri) ?>">
                        <i data-feather="layers"></i>
                        <span data-key="t-churches">Church Management</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <?php if ($userRole === 'admin'): ?>
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
                        <?php endif; ?>
                        <?php if ($session->isHeadPastor()): ?>
                        <li>
                            <a href="<?= AssetHelper::url('churches/' . $session->getHeadPastorChurchId() . '/membership') ?>" class="<?= strpos($currentUri, '/membership') !== false ? 'active' : '' ?>">
                                <span>Membership Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('churches/' . $session->getHeadPastorChurchId()) ?>" class="<?= strpos($currentUri, '/churches/' . $session->getHeadPastorChurchId()) !== false && strpos($currentUri, '/membership') === false ? 'active' : '' ?>">
                                <span>My Church Details</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('churches/' . $session->getHeadPastorChurchId() . '/finance') ?>" class="<?= strpos($currentUri, '/finance') !== false ? 'active' : '' ?>">
                                <span>Financial Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('churches/' . $session->getHeadPastorChurchId() . '/finance/records') ?>" class="<?= strpos($currentUri, '/finance/records') !== false ? 'active' : '' ?>">
                                <span>Financial Records</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('churches/' . $session->getHeadPastorChurchId() . '/property') ?>" class="<?= strpos($currentUri, '/property') !== false ? 'active' : '' ?>">
                                <span>Property Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('churches/' . $session->getHeadPastorChurchId() . '/property/list') ?>" class="<?= strpos($currentUri, '/property/list') !== false ? 'active' : '' ?>">
                                <span>Property List</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
<?php if ($session->isHeadPastor()): ?>
                <li>
                    <a href="<?= AssetHelper::url('churches/' . $session->getHeadPastorChurchId() . '#units-section') ?>" class="<?= strpos($currentUri, '#units-section') !== false ? 'active' : '' ?>">
                        <i data-feather="users"></i>
                        <span data-key="t-unit-management">Unit Management</span>
                    </a>
                </li>
<?php endif; ?>
                <?php endif; ?>

                <?php if ($session->isUnitHead()): ?>
                <li>
                    <a href="javascript: void(0);" class="has-arrow <?= isActive('/my-unit', $currentUri) ?>">
                        <i data-feather="briefcase"></i>
                        <span data-key="t-unit-leadership">Unit Leadership</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="<?= AssetHelper::url('my-unit/dashboard') ?>" class="<?= isActive('my-unit/dashboard', $currentUri) ?>">
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('my-unit/members') ?>" class="<?= isActive('my-unit/members', $currentUri) ?>">
                                <span>Members</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('my-unit/attendance') ?>" class="<?= isActive('my-unit/attendance', $currentUri) ?>">
                                <span>Roll Call Attendance</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('my-unit/reports') ?>" class="<?= isActive('my-unit/reports', $currentUri) ?>">
                                <span>narrative Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= AssetHelper::url('my-unit/finance') ?>" class="<?= isActive('my-unit/finance', $currentUri) ?>">
                                <span>Finances</span>
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

                <?php if ($session->hasPermission('manage_reports') || $session->hasPermission('view_all_reports') || $this->session->hasPermission('create_reports')): ?>
                <li>
                    <?php $outreachMenuIsActive = isActive('/outreach-reports', $currentUri) || strpos($currentUri, '/outreach') !== false; ?>
                    <a href="javascript: void(0);" class="has-arrow <?= $outreachMenuIsActive ? 'active' : '' ?>">
                        <i data-feather="bar-chart-2"></i>
                        <span>Outreach / Event Reports</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <?php if ($session->isHeadPastor()): ?>
                            <?php $baseUrl = 'churches/' . $session->getHeadPastorChurchId() . '/outreach'; ?>
                            <li>
                                <a href="<?= AssetHelper::url($baseUrl) ?>" class="<?= isActive($baseUrl, $currentUri) ?>">
                                    <span>Outreach Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= AssetHelper::url($baseUrl . '/records') ?>" class="<?= isActive($baseUrl . '/records', $currentUri) ?>">
                                    <span>Outreach Records</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= AssetHelper::url($baseUrl . '/create') ?>" class="<?= isActive($baseUrl . '/create', $currentUri) ?>">
                                    <span>New Report</span>
                                </a>
                            </li>
                        <?php else: ?>
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
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
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

                <?php 
                $canManageAttendance = $session->hasPermission('manage_attendance') || $session->hasPermission('manage_unit_attendance') || $session->isHeadPastor() || $session->get('user_role') === 'admin' || $session->isDirector();
                ?>
                <?php if ($canManageAttendance): ?>
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

