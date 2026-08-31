<?php
use App\Core\Session;
use App\Utilities\AssetHelper;

$session = Session::getInstance();
$userName = $session->get('user_name', 'User');
$userRole = $session->get('user_role', 'user');
$userEmail = $session->get('user_email', '');
$userId = $session->get('user_id');
$userAvatarSrc = $session->get('user_profile_picture')
    ? AssetHelper::url($session->get('user_profile_picture'))
    : AssetHelper::image('users/avatar-1.jpg');
?>
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="<?= AssetHelper::url('/') ?>" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="<?= AssetHelper::image('logo-sm.svg') ?>" alt="" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="<?= AssetHelper::image('logo-sm.svg') ?>" alt="" height="24"> 
                        <span class="logo-txt">LCM Portal</span>
                    </span>
                </a>

                <a href="<?= AssetHelper::url('/') ?>" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="<?= AssetHelper::image('logo-sm.svg') ?>" alt="" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="<?= AssetHelper::image('logo-sm.svg') ?>" alt="" height="24"> 
                        <span class="logo-txt">LCM Portal</span>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <!-- App Search-->
            <form class="app-search d-none d-lg-block">
                <div class="position-relative">
                    <input type="text" class="form-control" placeholder="Search...">
                    <button class="btn btn-primary" type="button"><i class="bx bx-search-alt align-middle"></i></button>
                </div>
            </form>
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item" id="page-header-search-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i data-feather="search" class="icon-lg"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-search-dropdown">
                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ..." aria-label="Search Result">
                                <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="dropdown d-none d-sm-inline-block">
                <button type="button" class="btn header-item" id="mode-setting-btn">
                    <i data-feather="moon" class="icon-lg layout-mode-dark"></i>
                    <i data-feather="sun" class="icon-lg layout-mode-light"></i>
                </button>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon position-relative" id="page-header-notifications-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i data-feather="bell" class="icon-lg"></i>
                    <span class="badge bg-danger rounded-pill notification-count" id="notificationBadge">0</span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0"> Notifications </h6>
                            </div>
                            <div class="col-auto">
                                <a href="<?= AssetHelper::url('notifications/show') ?>" class="text-muted">View All</a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px;" id="notificationsList">
                        <div class="p-3 text-center text-muted">
                            <p class="mb-0">Loading notifications...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item bg-light-subtle border-start border-end" id="page-header-user-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="<?= htmlspecialchars($userAvatarSrc) ?>"
                        alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1 fw-medium"><?= htmlspecialchars($userName) ?></span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="<?= AssetHelper::url('users/' . $userId) ?>">
                        <i class="mdi mdi-face-man font-size-16 align-middle me-1"></i> Profile
                    </a>
                    <a class="dropdown-item" href="<?= AssetHelper::url('notifications/show') ?>">
                        <i class="mdi mdi-bell font-size-16 align-middle me-1"></i> Notifications
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?= AssetHelper::url('logout') ?>">
                        <i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
// Load notifications on page load
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
});

function loadNotifications() {
    fetch('<?= AssetHelper::url('notifications') ?>?unread_only=1')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationBadge(data.unread_count);
                updateNotificationList(data.notifications);
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationBadge');
    if (badge) {
        badge.textContent = count || 0;
        if (count > 0) {
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
}

function updateNotificationList(notifications) {
    const list = document.getElementById('notificationsList');
    if (!list) return;
    
    if (!notifications || notifications.length === 0) {
        list.innerHTML = '<div class="p-3 text-center text-muted"><p class="mb-0">No notifications</p></div>';
        return;
    }
    
    let html = '';
    const notificationsUrl = '<?= AssetHelper::url('notifications') ?>';
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
    
    notifications.forEach(function(notif) {
        const typeClass = notif.type === 'success' ? 'success' : 
                          notif.type === 'warning' ? 'warning' : 
                          notif.type === 'error' ? 'danger' : 'info';
        const timeAgo = getTimeAgo(notif.created_at);
        const targetUrl = notif.link || notificationsUrl.replace(/\/?$/, '/show');
        html += `
            <a href="${escapeHtml(targetUrl)}" class="text-reset notification-item d-block" data-notification-id="${notif.id || ''}">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        ${notif.image_url ? `<img src="${escapeHtml(notif.image_url)}" alt="" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">` : `<div class="avatar-xs"><span class="avatar-title bg-${typeClass} rounded-circle font-size-16"><i data-feather="bell"></i></span></div>`}
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mt-0 mb-1 font-size-14">${escapeHtml(notif.title)}</h6>
                        <div class="font-size-12 text-muted">
                            <p class="mb-1">${escapeHtml(notif.message || '')}</p>
                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i> ${timeAgo}</p>
                        </div>
                    </div>
                </div>
            </a>
        `;
    });
    
    list.innerHTML = html;
    
    // Mark as read on click, then navigate
    list.querySelectorAll('.notification-item[data-notification-id]').forEach(function(el) {
        var id = el.getAttribute('data-notification-id');
        if (!id) return;
        el.addEventListener('click', function(e) {
            e.preventDefault();
            var href = el.getAttribute('href');
            fetch(notificationsUrl + '/' + id + '/read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: '_token=' + encodeURIComponent(csrfToken)
            }).catch(function() {});
            window.location.href = href;
        });
    });
    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function getTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
    if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
    if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';
    return date.toLocaleDateString();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

