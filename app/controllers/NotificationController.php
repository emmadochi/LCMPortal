<?php
namespace App\Controllers;

use App\Models\Church;
use App\Models\Notification;
use App\Models\User;
use App\Utilities\FileUpload;
use App\Utilities\NotificationBroadcastService;
use App\Utilities\Security;

class NotificationController extends BaseController {
    private $notificationModel;

    public function __construct() {
        parent::__construct();
        $this->notificationModel = new Notification();
    }

    /**
     * Check if current user can send broadcast notifications (admin, head pastor, or director)
     */
    private function canSendNotifications() {
        if ($this->session->get('can_send_notifications')) {
            return true;
        }
        if ($this->session->hasPermission('send_broadcast_notifications')) {
            return true;
        }
        if ($this->session->isHeadPastor()) {
            return true;
        }
        $userId = $this->session->get('user_id');
        if ($userId) {
            $userModel = new User();
            return !empty($userModel->getDirectorUnits($userId));
        }
        return false;
    }

    /**
     * Get notifications for current user (AJAX)
     */
    public function index() {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            $this->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $unreadOnly = $this->request->get('unread_only', false);
        
        if ($unreadOnly) {
            $notifications = $this->notificationModel->getUnreadNotifications($userId, 10);
        } else {
            $notifications = $this->notificationModel->getUserNotifications($userId, 20);
        }

        $unreadCount = $this->notificationModel->getUnreadCount($userId);

        $baseUrl = rtrim(\App\Utilities\AssetHelper::baseUrl(''), '/');
        foreach ($notifications as &$n) {
            if (!empty($n['image_path'])) {
                $n['image_url'] = $baseUrl . '/' . ltrim($n['image_path'], '/');
            } else {
                $n['image_url'] = null;
            }
        }
        unset($n);

        $this->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id) {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            $this->json(['success' => false, 'message' => 'Not authenticated'], 401);
            return;
        }
        if (!Security::validateCSRFToken($this->request->post('_token') ?? '')) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 403);
            return;
        }

        $id = (int) $id;
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid notification'], 400);
            return;
        }

        // Verify notification belongs to user
        $notification = $this->notificationModel->find($id);
        if (!$notification || (int) $notification['user_id'] !== $userId) {
            $this->json(['success' => false, 'message' => 'Notification not found'], 404);
            return;
        }

        if ($this->notificationModel->markAsRead($id)) {
            $this->json(['success' => true, 'message' => 'Notification marked as read']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to mark as read'], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead() {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            $this->json(['success' => false, 'message' => 'Not authenticated'], 401);
            return;
        }
        if (!Security::validateCSRFToken($this->request->post('_token') ?? '')) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 403);
            return;
        }

        try {
            $db = \App\Core\Database::getInstance();
            $sql = "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            
            $this->json(['success' => true, 'message' => 'All notifications marked as read']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to mark all as read'], 500);
        }
    }

    /**
     * Show notifications page
     */
    public function show() {
        $userId = $this->session->get('user_id');
        $notifications = $this->notificationModel->getUserNotifications($userId, 50);
        $unreadCount = $this->notificationModel->getUnreadCount($userId);

        $this->render('notifications/index', [
            'title' => 'Notifications',
            'pageTitle' => 'Notifications',
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }

    /**
     * Show send notification form (admin, head pastor, or director)
     */
    public function create() {
        if (!$this->canSendNotifications()) {
            $this->session->setFlash('error', 'You do not have permission to send broadcast notifications.');
            $this->redirect('/');
            return;
        }
        $roles = ['admin', 'director', 'officer', 'pastor', 'user'];
        $post = $this->session->getFlash('_post');
        if (!is_array($post)) {
            $post = [];
        }

        $userId = $this->session->get('user_id');
        $userModel = new User();
        $churchModel = new Church();

        $isAdmin = $this->session->hasPermission('send_broadcast_notifications');
        $isHeadPastor = $this->session->isHeadPastor();
        $headPastorChurchId = $this->session->getHeadPastorChurchId();
        $directorUnits = $userModel->getDirectorUnits($userId);
        $headPastorChurch = $headPastorChurchId ? $churchModel->find($headPastorChurchId) : null;

        $senderContext = [
            'isAdmin' => $isAdmin,
            'isHeadPastor' => $isHeadPastor,
            'isDirector' => !empty($directorUnits),
            'churchId' => $headPastorChurchId,
            'churchName' => $headPastorChurch['name'] ?? null,
            'directorUnits' => $directorUnits,
        ];

        $this->render('notifications/create', [
            'title' => 'Send Notification',
            'pageTitle' => 'Send Notification',
            'roles' => $roles,
            'csrf_token' => Security::generateCSRFToken(),
            'post' => $post,
            'senderContext' => $senderContext
        ]);
    }

    /**
     * Process send broadcast (admin, head pastor, or director)
     */
    public function send() {
        if (!$this->canSendNotifications()) {
            $this->session->setFlash('error', 'You do not have permission to send broadcast notifications.');
            $this->redirect('/');
            return;
        }
        if ($this->request->method() !== 'POST') {
            $this->redirect('notifications/create');
            return;
        }
        if (!Security::validateCSRFToken($this->request->post('_token'))) {
            $this->session->setFlash('error', 'Invalid request. Please try again.');
            $this->redirect('notifications/create');
            return;
        }
        $title = trim($this->request->post('title', ''));
        $message = trim($this->request->post('message', ''));
        $link = trim($this->request->post('link', ''));
        $audienceType = $this->request->post('audience_type', 'all');
        $roles = (array) $this->request->post('roles', []);
        $channels = $this->request->post('channels', 'both');
        $audienceChurchId = (int) $this->request->post('audience_church_id', 0);
        $audienceUnitIds = array_map('intval', (array) $this->request->post('audience_unit_ids', []));
        $audienceUnitRoles = (array) $this->request->post('audience_unit_roles', []);

        $errors = [];
        if ($title === '') {
            $errors['title'] = 'Title is required.';
        }
        if ($message === '') {
            $errors['message'] = 'Message is required.';
        }
        if ($audienceType === 'roles' && empty($roles)) {
            $errors['roles'] = 'Select at least one role when targeting by role.';
        }

        $isAdmin = $this->session->hasPermission('send_broadcast_notifications');
        $isHeadPastor = $this->session->isHeadPastor();
        $headPastorChurchId = $this->session->getHeadPastorChurchId();
        $userModel = new User();
        $directorUnits = $userModel->getDirectorUnits($this->session->get('user_id'));
        $directorUnitIds = array_column($directorUnits, 'id');

        $audienceScope = null;
        if ($audienceType === 'church_members' || $audienceType === 'church_unit_heads' || $audienceType === 'church_by_unit_role') {
            if (!$isHeadPastor || $headPastorChurchId <= 0) {
                $errors['audience'] = 'Invalid audience selection.';
            } elseif ($audienceChurchId !== $headPastorChurchId) {
                $errors['audience'] = 'You can only send to your church.';
            } else {
                $audienceScope = ['church_id' => $headPastorChurchId];
                if ($audienceType === 'church_by_unit_role' && !empty($audienceUnitRoles)) {
                    $audienceScope['unit_roles'] = array_intersect($audienceUnitRoles, ['officer', 'secretary', 'treasurer']);
                }
            }
        } elseif ($audienceType === 'unit_members') {
            if (empty($directorUnitIds)) {
                $errors['audience'] = 'You are not a director of any unit.';
            } elseif (empty($audienceUnitIds)) {
                $errors['audience'] = 'Select at least one unit.';
            } else {
                $allowed = array_intersect($audienceUnitIds, $directorUnitIds);
                if (empty($allowed)) {
                    $errors['audience'] = 'You can only send to units you direct.';
                } else {
                    $audienceScope = ['unit_ids' => $allowed];
                }
            }
        }

        $allowedChannels = ['in_app', 'email', 'both'];
        if (!in_array($channels, $allowedChannels, true)) {
            $channels = 'both';
        }
        if (!empty($errors)) {
            $this->session->setFlash('error', implode(' ', $errors));
            $this->session->setFlash('_post', [
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'audience_type' => $audienceType,
                'roles' => $roles,
                'channels' => $channels,
                'audience_church_id' => $audienceChurchId,
                'audience_unit_ids' => $audienceUnitIds,
                'audience_unit_roles' => $audienceUnitRoles
            ]);
            $this->redirect('notifications/create');
            return;
        }
        $allowedRoles = ['admin', 'director', 'officer', 'pastor', 'user'];
        $roles = array_intersect($roles, $allowedRoles);
        $sentByUserId = (int) $this->session->get('user_id');

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $publicPath = realpath(__DIR__ . '/../../public');
            $uploadDir = $publicPath . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'notifications';
            $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileUpload = new FileUpload($uploadDir, $allowedImageTypes);
            $fileUpload->setMaxSize(2097152); // 2MB for notification images
            $result = $fileUpload->upload($_FILES['image'], 'broadcast_' . time());
            if ($result['success']) {
                $imagePath = 'uploads/notifications/' . $result['filename'];
            } else {
                $this->session->setFlash('error', $result['error'] ?? 'Image upload failed.');
                $this->session->setFlash('_post', [
                    'title' => $title,
                    'message' => $message,
                    'link' => $link,
                    'audience_type' => $audienceType,
                    'roles' => $roles,
                    'channels' => $channels,
                    'audience_church_id' => $audienceChurchId ?? 0,
                    'audience_unit_ids' => $audienceUnitIds ?? [],
                    'audience_unit_roles' => $audienceUnitRoles ?? []
                ]);
                $this->redirect('notifications/create');
                return;
            }
        }

        $result = NotificationBroadcastService::sendBroadcast(
            $sentByUserId,
            $title,
            $message,
            $link !== '' ? $link : null,
            $imagePath,
            $audienceType,
            $roles,
            $channels,
            $audienceScope
        );
        if ($result['success']) {
            $this->session->setFlash('success', $result['message']);
        } else {
            $this->session->setFlash('error', $result['message']);
        }
        $this->redirect('notifications/create');
    }
}

