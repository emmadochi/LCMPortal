<?php
namespace App\Controllers;

use App\Models\Church;
use App\Models\User;
use App\Models\PasswordResetRequest;
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\NotificationHelper;

class AuthController extends BaseController {
    private $userModel;
    private $resetRequestModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->resetRequestModel = new PasswordResetRequest();
    }

    public function showLogin() {
        // If already logged in, redirect to dashboard
        if ($this->session->has('user_id')) {
            $this->redirect('/');
        }

        $csrfToken = Security::generateCSRFToken();
        $this->render('auth/login', [
            'title' => 'Login',
            'csrf_token' => $csrfToken
        ]);
    }

    public function login() {
        // Validate CSRF token
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token. Please try again.');
            $this->redirect('/login');
        }

        // Validate input
        $validation = $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/login');
        }

        $email = $this->request->post('email');
        $password = $this->request->post('password');

        // Authenticate user
        $user = $this->userModel->authenticate($email, $password);

        if (!$user) {
            $this->session->setFlash('error', 'Invalid email or password.');
            $this->redirect('/login');
        }

        // Clear any previous flash messages (e.g. from a failed attempt)
        $this->session->remove('_flash');

        // Set session data
        $this->session->set('user_id', $user['id']);
        $this->session->set('user_email', $user['email']);
        $this->session->set('user_name', $user['first_name'] . ' ' . $user['last_name']);
        $this->session->set('user_role', $user['role']);
        $this->session->set('user_permissions', $this->getUserPermissions($user['role']));
        if (!empty($user['profile_picture'])) {
            $this->session->set('user_profile_picture', $user['profile_picture']);
        } else {
            $this->session->remove('user_profile_picture');
        }

        $churchModel = new Church();
        $headPastorChurch = $churchModel->getChurchByHeadPastor($user['id']);
        if ($headPastorChurch) {
            $this->session->set('head_pastor_church_id', $headPastorChurch['id']);
        } else {
            $this->session->remove('head_pastor_church_id');
        }

        $canSendNotifications = $this->userModel->hasPermission($user['id'], 'send_broadcast_notifications')
            || !empty($headPastorChurch)
            || !empty($this->userModel->getDirectorUnits($user['id']));
        $this->session->set('can_send_notifications', $canSendNotifications);

        // Log activity
        ActivityLog::log(
            $user['id'],
            'login',
            'User',
            $user['id'],
            "User logged in: {$user['email']}"
        );

        // Redirect based on role
        $this->redirect('/');
    }

    public function logout() {
        if ($this->session->has('user_id')) {
            $userId = $this->session->get('user_id');
            $userEmail = $this->session->get('user_email', 'Unknown');
            
            // Log activity
            ActivityLog::log(
                $userId,
                'logout',
                'User',
                $userId,
                "User logged out: {$userEmail}"
            );
        }

        // Clear session completely
        $this->session->destroy();
        // Start new session to avoid session reuse issues
        session_start();
        session_destroy();
        
        $this->redirect('/login');
    }

    /**
     * Get permissions for a role
     */
    private function getUserPermissions($role) {
        $rolePermissions = [
            'admin' => [
                'manage_users',
                'manage_units',
                'manage_reports',
                'view_all_reports',
                'manage_finance',
                'manage_media',
                'manage_projects',
                'view_dashboard',
                'send_broadcast_notifications'
            ],
            'director' => [
                'manage_units',
                'manage_reports',
                'view_unit_reports',
                'manage_unit_finance',
                'manage_unit_media',
                'manage_unit_projects',
                'view_dashboard'
            ],
            'officer' => [
                'create_reports',
                'view_unit_reports',
                'view_dashboard'
            ],
            'pastor' => [
                'view_all_reports',
                'view_dashboard'
            ],
            'user' => [
                'view_dashboard'
            ]
        ];

        return $rolePermissions[$role] ?? [];
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword() {
        // If already logged in, redirect to dashboard
        if ($this->session->has('user_id')) {
            $this->redirect('/');
        }

        $csrfToken = Security::generateCSRFToken();
        $this->render('auth/forgot_password', [
            'title' => 'Forgot Password',
            'csrf_token' => $csrfToken
        ]);
    }

    /**
     * Handle forgot password request
     */
    public function requestPasswordReset() {
        // Validate CSRF token
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token. Please try again.');
            $this->redirect('/forgot-password');
            return;
        }

        // Validate input
        $validation = $this->validate([
            'email' => 'required|email'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/forgot-password');
            return;
        }

        $email = $this->request->post('email');
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            // For security, we don't reveal if email exists
            $this->session->setFlash('success', 'If your email exists in our system, you will receive instructions shortly.');
            $this->redirect('/forgot-password');
            return;
        }

        // Check if user already has a pending request
        $existingRequest = $this->resetRequestModel->findPendingByEmail($email);
        if ($existingRequest) {
            $this->session->setFlash('error', 'You already have a pending password reset request. Please check your notifications.');
            $this->redirect('/forgot-password');
            return;
        }

        // Create password reset request
        $requestId = $this->resetRequestModel->createRequest($user['id'], $email);
        if (!$requestId) {
            $this->session->setFlash('error', 'Failed to create password reset request. Please try again.');
            $this->redirect('/forgot-password');
            return;
        }

        // Notify admins about the new request
        $this->notifyAdminsOfResetRequest($user, $requestId);

        // Log activity
        ActivityLog::log(
            $user['id'],
            'password_reset_request',
            'User',
            $user['id'],
            "Password reset requested for: {$email}"
        );

        $this->session->setFlash('success', 'Password reset request submitted. An administrator will review your request.');
        $this->redirect('/forgot-password');
    }

    /**
     * Show reset password form
     */
    public function showResetPassword($token) {
        // If already logged in, redirect to dashboard
        if ($this->session->has('user_id')) {
            $this->redirect('/');
        }

        // Validate token
        if (!$this->resetRequestModel->isValidRequest($token)) {
            $this->session->setFlash('error', 'Invalid or expired password reset link.');
            $this->redirect('/login');
            return;
        }

        $csrfToken = Security::generateCSRFToken();
        $this->render('auth/reset_password', [
            'title' => 'Reset Password',
            'csrf_token' => $csrfToken,
            'token' => $token
        ]);
    }

    /**
     * Handle password reset
     */
    public function resetPassword($token) {
        // If already logged in, redirect to dashboard
        if ($this->session->has('user_id')) {
            $this->redirect('/');
        }

        // Validate CSRF token
        $csrfToken = $this->request->post('_token');
        if (!$csrfToken || !Security::validateCSRFToken($csrfToken)) {
            $this->session->setFlash('error', 'Invalid security token. Please try again.');
            $this->redirect("/reset-password/{$token}");
            return;
        }

        // Validate token
        $request = $this->resetRequestModel->findByToken($token);
        if (!$request) {
            $this->session->setFlash('error', 'Invalid password reset link.');
            $this->redirect('/login');
            return;
        }
        
        if ($request['status'] !== 'approved') {
            if ($request['status'] === 'pending') {
                $this->session->setFlash('error', 'Your password reset request is still pending approval by an administrator.');
            } elseif ($request['status'] === 'rejected') {
                $this->session->setFlash('error', 'Your password reset request has been rejected by an administrator.');
            } else {
                $this->session->setFlash('error', 'Invalid or expired password reset link.');
            }
            $this->redirect('/login');
            return;
        }
        
        if (strtotime($request['expires_at']) < time()) {
            $this->session->setFlash('error', 'Your password reset link has expired. Please submit a new password reset request.');
            $this->redirect('/login');
            return;
        }

        // Validate input
        $validation = $this->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/reset-password/{$token}");
            return;
        }

        $password = $this->request->post('password');
        $user = $this->resetRequestModel->getUserByToken($token);

        if (!$user) {
            $this->session->setFlash('error', 'User not found.');
            $this->redirect('/login');
            return;
        }

        // Update password
        $result = $this->userModel->updatePassword($user['id'], $password);
        if (!$result) {
            $this->session->setFlash('error', 'Failed to reset password. Please try again.');
            $this->redirect("/reset-password/{$token}");
            return;
        }

        // Complete the reset request
        $request = $this->resetRequestModel->findByToken($token);
        if ($request) {
            $this->resetRequestModel->complete($request['id']);
        }

        // Log activity
        ActivityLog::log(
            $user['id'],
            'password_reset',
            'User',
            $user['id'],
            "Password successfully reset for: {$user['email']}"
        );

        $this->session->setFlash('success', 'Password successfully reset. You can now login with your new password.');
        $this->redirect('/login');
    }

    /**
     * Notify admins of new password reset request
     */
    private function notifyAdminsOfResetRequest($user, $requestId) {
        $admins = $this->userModel->findAll(['role' => 'admin', 'status' => 'active']);
        
        foreach ($admins as $admin) {
            // Send in-app notification
            NotificationHelper::notify(
                $admin['id'],
                'info',
                'New Password Reset Request',
                "User {$user['first_name']} {$user['last_name']} ({$user['email']}) has requested a password reset. Please review the request in the admin panel.",
                "/admin/password-reset-requests"
            );
            
            // Send email notification
            $config = require __DIR__ . '/../../../config/config.php';
            $baseUrl = $config['app_url'];
            $emailSubject = 'Password Reset Request Requires Approval';
            $emailMessage = '<h2>Password Reset Request</h2>'
                         . "<p>User: {$user['first_name']} {$user['last_name']} ({$user['email']})</p>"
                         . "<p>Has requested a password reset and requires your approval.</p>"
                         . "<p>Please log in to the admin portal to review and approve/reject this request.</p>"
                         . "<p><a href='{$baseUrl}/admin/password-reset-requests'>View Password Reset Requests</a></p>";
            
            NotificationHelper::sendEmail($admin['email'], $emailSubject, $emailMessage);
        }
    }

    /**
     * Show admin password reset requests
     */
    public function showAdminResetRequests() {
        // Check if user is admin
        if (!$this->session->hasPermission('manage_users')) {
            $this->session->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('/');
            return;
        }

        $pendingRequests = $this->resetRequestModel->findPendingRequests();
        
        // Use admin layout for this page
        $this->render('admin/password_reset_requests', [
            'title' => 'Password Reset Requests',
            'requests' => $pendingRequests,
            'csrf_token' => Security::generateCSRFToken()
        ]);
    }

    /**
     * Approve password reset request
     */
    public function approvePasswordReset($id) {
        // Check if user is admin
        if (!$this->session->hasPermission('manage_users')) {
            $this->session->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('/');
            return;
        }

        // Validate CSRF token
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/admin/password-reset-requests');
            return;
        }

        $adminId = $this->session->get('user_id');
        $result = $this->resetRequestModel->approve($id, $adminId);
        
        if ($result) {
            $this->session->setFlash('success', 'Password reset request approved successfully.');
            
            // Log activity
            $request = $this->resetRequestModel->find($id);
            if ($request) {
                ActivityLog::log(
                    $adminId,
                    'password_reset_approved',
                    'PasswordResetRequest',
                    $id,
                    "Approved password reset request for user ID: {$request['user_id']}"
                );
            }
        } else {
            $this->session->setFlash('error', 'Failed to approve password reset request.');
        }
        
        $this->redirect('/admin/password-reset-requests');
    }

    /**
     * Reject password reset request
     */
    public function rejectPasswordReset($id) {
        // Check if user is admin
        if (!$this->session->hasPermission('manage_users')) {
            $this->session->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('/');
            return;
        }

        // Validate CSRF token
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/admin/password-reset-requests');
            return;
        }

        $reason = $this->request->post('reason', '');
        $adminId = $this->session->get('user_id');
        $result = $this->resetRequestModel->reject($id, $adminId, $reason);
        
        if ($result) {
            $this->session->setFlash('success', 'Password reset request rejected.');
            
            // Log activity
            $request = $this->resetRequestModel->find($id);
            if ($request) {
                $reasonText = $reason ? " Reason: {$reason}" : '';
                ActivityLog::log(
                    $adminId,
                    'password_reset_rejected',
                    'PasswordResetRequest',
                    $id,
                    "Rejected password reset request for user ID: {$request['user_id']}.{$reasonText}"
                );
            }
        } else {
            $this->session->setFlash('error', 'Failed to reject password reset request.');
        }
        
        $this->redirect('/admin/password-reset-requests');
    }

}
