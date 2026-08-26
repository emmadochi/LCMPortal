<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Membership;
use App\Utilities\Security;
use App\Models\ActivityLog;

class MyProfileController extends BaseController {
    private $userModel;
    private $membershipModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->membershipModel = new Membership();
    }

    /**
     * Show personal profile
     */
    public function index() {
        $userId = $this->session->get('user_id');
        $member = $this->userModel->find($userId);

        if (!$member) {
            $this->session->setFlash('error', 'Profile not found.');
            $this->redirect('/');
        }

        $memberships = $this->membershipModel->getByUserId($userId);
        $units = $this->userModel->getUnits($userId);
        $engagementScore = $this->userModel->getEngagementScore($userId);
        $aiInsights = $this->userModel->getAIInsights($userId);

        $this->render('profile/show', [
            'title' => 'My Profile',
            'pageTitle' => 'My Personal Profile',
            'member' => $member,
            'memberships' => $memberships,
            'units' => $units,
            'engagementScore' => $engagementScore,
            'aiInsights' => $aiInsights,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/'],
                ['label' => 'My Profile', 'active' => true]
            ]
        ]);
    }

    /**
     * Show edit form
     */
    public function edit() {
        $userId = $this->session->get('user_id');
        $member = $this->userModel->find($userId);

        if (!$member) {
            $this->session->setFlash('error', 'Profile not found.');
            $this->redirect('/');
        }

        $csrfToken = Security::generateCSRFToken();

        $this->render('profile/edit', [
            'title' => 'Edit My Profile',
            'pageTitle' => 'Edit My Profile',
            'member' => $member,
            'csrf_token' => $csrfToken,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/'],
                ['label' => 'My Profile', 'url' => '/profile'],
                ['label' => 'Edit', 'active' => true]
            ]
        ]);
    }

    /**
     * Update personal profile
     */
    public function update() {
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/profile/edit');
        }

        $userId = $this->session->get('user_id');

        $validation = $this->validate([
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'email' => 'required|email'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/profile/edit');
        }

        $email = $this->request->post('email');
        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser && $existingUser['id'] != $userId) {
            $this->session->setFlash('error', 'Email already in use by another account.');
            $this->redirect('/profile/edit');
        }

        $data = [
            'first_name' => $this->request->post('first_name'),
            'last_name' => $this->request->post('last_name'),
            'email' => $email
        ];

        // Password update if provided
        $password = $this->request->post('password');
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $this->session->setFlash('error', 'Password must be at least 6 characters.');
                $this->redirect('/profile/edit');
            }
            $data['password'] = Security::hashPassword($password);
        }

        if ($this->userModel->update($userId, $data)) {
            // Update session name if changed
            $this->session->set('user_name', $data['first_name'] . ' ' . $data['last_name']);
            $this->session->set('user_email', $data['email']);

            ActivityLog::log($userId, 'update', 'User', $userId, "Member updated their own profile.");
            $this->session->setFlash('success', 'Profile updated successfully.');
            $this->redirect('/profile');
        } else {
            $this->session->setFlash('error', 'Failed to update profile.');
            $this->redirect('/profile/edit');
        }
    }

    /**
     * Update profile details (phone, age_group, address, name)
     */
    public function updateDetails() {
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/profile/edit');
        }

        $userId = $this->session->get('user_id');

        $validation = $this->validate([
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'phone' => 'max:20',
            'age_group' => 'in:adult,child,teen',
            'address' => 'max:255'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/profile/edit');
        }

        $data = [
            'first_name' => $this->request->post('first_name'),
            'last_name' => $this->request->post('last_name'),
            'phone' => $this->request->post('phone') ?: null,
            'age_group' => $this->request->post('age_group') ?: null,
            'address' => $this->request->post('address') ?: null
        ];

        // Password update if provided
        $password = $this->request->post('password');
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $this->session->setFlash('error', 'Password must be at least 6 characters.');
                $this->redirect('/profile/edit');
            }
            $data['password'] = Security::hashPassword($password);
        }

        if ($this->userModel->update($userId, $data)) {
            $this->session->set('user_name', $data['first_name'] . ' ' . $data['last_name']);
            ActivityLog::log($userId, 'update', 'User', $userId, "Member updated their profile details.");
            $this->session->setFlash('success', 'Profile updated successfully.');
            $this->redirect('/profile');
        } else {
            $this->session->setFlash('error', 'Failed to update profile.');
            $this->redirect('/profile/edit');
        }
    }

    /**
     * Upload profile picture (AJAX)
     */
    public function updateProfilePicture() {
        if (!Security::validateCSRFToken($this->request->post('_token') ?? '')) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 403);
            return;
        }

        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            $this->json(['success' => false, 'message' => 'User not found'], 404);
            return;
        }

        if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'Please select an image to upload.'], 400);
            return;
        }

        $publicPath = realpath(__DIR__ . '/../../public');
        $uploadDir = $publicPath . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'avatars';
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        $fileUpload = new \App\Utilities\FileUpload($uploadDir, $allowedTypes);
        $fileUpload->setMaxSize(2097152); // 2MB
        
        $result = $fileUpload->upload($_FILES['profile_picture'], 'avatar_' . $userId . '_');
        if (!$result['success']) {
            $this->json(['success' => false, 'message' => $result['error'] ?? 'Upload failed.'], 400);
            return;
        }

        $imagePath = 'uploads/avatars/' . $result['filename'];
        $oldPath = $user['profile_picture'] ?? null;

        if ($this->userModel->update($userId, ['profile_picture' => $imagePath])) {
            if ($oldPath) {
                $oldFullPath = $publicPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $oldPath);
                if (file_exists($oldFullPath)) {
                    @unlink($oldFullPath);
                }
            }
            $this->session->set('user_profile_picture', $imagePath);
            $baseUrl = rtrim(\App\Utilities\AssetHelper::baseUrl(''), '/');
            $this->json([
                'success' => true,
                'message' => 'Profile picture updated successfully.',
                'image_url' => $baseUrl . '/' . $imagePath
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to save profile picture.'], 500);
        }
    }
}
