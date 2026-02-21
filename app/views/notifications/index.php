<?php
use App\Utilities\AssetHelper;
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Notifications</h4>
                    <p class="card-title-desc mb-0">View and manage your notifications</p>
                </div>
                <div>
                    <?php if ($unreadCount > 0): ?>
                        <button type="button" class="btn btn-sm btn-primary" id="markAllReadBtn">
                            <i data-feather="check" class="me-1"></i> Mark All as Read
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($notifications)): ?>
                    <div class="text-center py-5">
                        <i data-feather="bell-off" class="icon-lg text-muted mb-3"></i>
                        <p class="text-muted">No notifications found.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($notifications as $notification): ?>
                            <div class="list-group-item <?= !$notification['is_read'] ? 'list-group-item-action' : '' ?> <?= !$notification['is_read'] ? 'bg-light' : '' ?>" 
                                 data-notification-id="<?= $notification['id'] ?>">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <h6 class="mb-0 me-2">
                                                <?php if (!$notification['is_read']): ?>
                                                    <span class="badge bg-primary me-2">New</span>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($notification['title']) ?>
                                            </h6>
                                            <span class="badge bg-<?= match($notification['type']) {
                                                'success' => 'success',
                                                'warning' => 'warning',
                                                'error' => 'danger',
                                                default => 'info'
                                            } ?>">
                                                <?= ucfirst($notification['type']) ?>
                                            </span>
                                        </div>
                                        <p class="mb-1"><?= htmlspecialchars($notification['message']) ?></p>
                                        <?php if (!empty($notification['image_path'])): ?>
                                            <div class="mt-2">
                                                <img src="<?= htmlspecialchars(AssetHelper::baseUrl($notification['image_path'])) ?>" alt="" class="rounded img-fluid" style="max-height: 180px; object-fit: contain;">
                                            </div>
                                        <?php endif; ?>
                                        <small class="text-muted d-block mt-2">
                                            <?= date('M d, Y H:i', strtotime($notification['created_at'])) ?>
                                        </small>
                                    </div>
                                    <div class="ms-3">
                                        <?php if ($notification['link']): ?>
                                            <a href="<?= htmlspecialchars($notification['link']) ?>" class="btn btn-sm btn-outline-primary notification-view-link" data-id="<?= (int)$notification['id'] ?>" data-read="<?= $notification['is_read'] ? '1' : '0' ?>">
                                                View
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!$notification['is_read']): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mark-read-btn" data-id="<?= $notification['id'] ?>">
                                                Mark Read
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$pageJs = <<<JS
<script>
    $(document).ready(function() {
        var baseUrl = '<?= AssetHelper::url('notifications') ?>';
        var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

        function markAsRead(id, onSuccess) {
            $.ajax({
                url: baseUrl + '/' + id + '/read',
                method: 'POST',
                data: { _token: csrfToken },
                success: function(response) {
                    if (response.success && typeof onSuccess === 'function') {
                        onSuccess();
                    }
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to mark as read.';
                    alert(msg);
                }
            });
        }

        function updateUnreadCount() {
            $.get(baseUrl + '?unread_only=1', function(data) {
                if (data.success) {
                    var count = data.unread_count || 0;
                    var badge = document.querySelector('.notification-count');
                    if (badge) {
                        badge.textContent = count;
                        badge.style.display = count > 0 ? 'inline-block' : 'none';
                    }
                }
            });
        }

        function applyReadStyle(item) {
            item.removeClass('bg-light list-group-item-action');
            item.find('.badge.bg-primary').remove();
            item.find('.mark-read-btn').remove();
            updateUnreadCount();
        }

        // Click row (except Mark Read button) to mark as read
        $(document).on('click', '.list-group-item[data-notification-id]', function(e) {
            if ($(e.target).closest('.mark-read-btn').length) return;
            var item = $(this);
            if (!item.hasClass('bg-light')) return;
            var id = item.data('notification-id');
            markAsRead(id, function() { applyReadStyle(item); });
        });

        // View link: mark as read then navigate (stopPropagation to avoid row handler)
        $(document).on('click', '.notification-view-link', function(e) {
            var link = $(this);
            if (link.data('read') === 1) return; // already read, let default happen
            e.preventDefault();
            e.stopPropagation();
            var id = link.data('id');
            var href = link.attr('href');
            markAsRead(id, function() {
                applyReadStyle(link.closest('.list-group-item'));
                window.location.href = href;
            });
        });

        // Mark Read button
        $(document).on('click', '.mark-read-btn', function(e) {
            e.stopPropagation();
            var id = $(this).data('id');
            var item = $(this).closest('.list-group-item');
            markAsRead(id, function() { applyReadStyle(item); });
        });
        
        // Mark all as read
        $('#markAllReadBtn').on('click', function() {
            var btn = $(this);
            $.ajax({
                url: baseUrl + '/read-all',
                method: 'POST',
                data: { _token: csrfToken },
                success: function(response) {
                    if (response.success) {
                        $('.list-group-item').removeClass('bg-light list-group-item-action');
                        $('.badge.bg-primary').remove();
                        $('.mark-read-btn').remove();
                        btn.remove();
                        updateUnreadCount();
                    }
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to mark all as read.';
                    alert(msg);
                }
            });
        });
    });
</script>
JS;
?>

