<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Unit;
use App\Models\Church;
use App\Models\FinanceRecord;
use App\Models\ActivityLog;
use App\Utilities\FileUpload;
use App\Utilities\Security;
use App\Utilities\ExportHelper;
use App\Utilities\SearchHelper;

class UserController extends BaseController {
    private $userModel;
    private $unitModel;
    private $churchModel;
    private $financeModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->unitModel = new Unit();
        $this->churchModel = new Church();
        $this->financeModel = new FinanceRecord();
        
        // Check permission
        $this->authorize('manage_users');
    }

    /**
     * List all users (optionally scoped to a church's membership for super admin)
     */
    public function index() {
        $search = $this->request->get('search', '');
        $role = $this->request->get('role', '');
        $status = $this->request->get('status', '');
        $churchId = (int) $this->request->get('church_id', 0);
        $churchFilter = null;
        $users = [];

        if ($churchId && $this->session->get('user_role') === 'admin') {
            $church = $this->churchModel->find($churchId);
            if ($church) {
                $unitIds = $this->churchModel->getChurchUnitIds($churchId);
                $users = $this->userModel->getUsersByUnitIds($unitIds, 'last_name ASC');
                $churchFilter = ['id' => $churchId, 'name' => $church['name']];
            }
        }

        if ($churchFilter === null) {
            $conditions = [];
            if ($role) {
                $conditions['role'] = $role;
            }
            if ($status) {
                $conditions['status'] = $status;
            }
            $users = $this->userModel->findAll($conditions, 'created_at DESC');
        }
        
        if ($search) {
            $searchTerm = SearchHelper::sanitize($search);
            $users = array_filter($users, function($user) use ($searchTerm) {
                return stripos($user['first_name'], $searchTerm) !== false ||
                       stripos($user['last_name'], $searchTerm) !== false ||
                       stripos($user['email'], $searchTerm) !== false;
            });
        }
        
        $roles = ['admin', 'director', 'officer', 'pastor', 'user'];
        
        $this->render('users/index', [
            'title' => 'Users',
            'pageTitle' => $churchFilter ? 'Members — ' . $churchFilter['name'] : 'Users',
            'users' => $users,
            'roles' => $roles,
            'search' => $search,
            'role' => $role,
            'status' => $status,
            'churchFilter' => $churchFilter
        ]);
    }

    /**
     * Show single user
     */
    public function show($id) {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->session->setFlash('error', 'User not found.');
            $this->redirect('/users');
        }
        
        // Get user's units and director units
        $units = $this->userModel->getUnits($id);
        $directorUnits = $this->userModel->getDirectorUnits($id);
        
        // Fetch church branch
        $church = null;
        if (!empty($user['church_id'])) {
            $church = $this->churchModel->find($user['church_id']);
        }
        
        // Get all units for assignment
        $allUnits = $this->unitModel->getActiveUnits();
        
        // Calculate engagement score
        $engagementScore = $this->userModel->getEngagementScore($id);
        
        // Get AI insights
        $aiInsights = $this->userModel->getAIInsights($id);

        // Financial records (tithe, donation, etc.) — only for users with finance permission
        $showFinanceSection = $this->session->hasPermission('manage_finance');
        $financeRecords = [];
        $financeMonth = (int) $this->request->get('finance_month', date('n'));
        $financeYear = (int) $this->request->get('finance_year', date('Y'));
        if ($showFinanceSection) {
            $financeMonth = max(1, min(12, $financeMonth));
            $financeYear = max(2020, min((int) date('Y') + 1, $financeYear));
            $startDate = sprintf('%04d-%02d-01', $financeYear, $financeMonth);
            $endDate = date('Y-m-t', strtotime($startDate));
            $financeRecords = $this->financeModel->getFinanceWithDetails(['member_id' => $id], null, $startDate, $endDate);
        }
        
        $this->render('users/show', [
            'title' => $user['first_name'] . ' ' . $user['last_name'],
            'pageTitle' => $user['first_name'] . ' ' . $user['last_name'],
            'user' => $user,
            'church' => $church,
            'units' => $units,
            'directorUnits' => $directorUnits,
            'allUnits' => $allUnits,
            'engagementScore' => $engagementScore,
            'aiInsights' => $aiInsights,
            'showFinanceSection' => $showFinanceSection,
            'financeRecords' => $financeRecords,
            'financeMonth' => $financeMonth,
            'financeYear' => $financeYear
        ]);
    }

    /**
     * Update user profile picture (AJAX)
     */
    public function updateProfilePicture($id) {
        if (!Security::validateCSRFToken($this->request->post('_token') ?? '')) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 403);
            return;
        }
        $user = $this->userModel->find($id);
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
        $fileUpload = new FileUpload($uploadDir, $allowedTypes);
        $fileUpload->setMaxSize(2097152); // 2MB
        $result = $fileUpload->upload($_FILES['profile_picture'], 'avatar_' . $id . '_');
        if (!$result['success']) {
            $this->json(['success' => false, 'message' => $result['error'] ?? 'Upload failed.'], 400);
            return;
        }
        $imagePath = 'uploads/avatars/' . $result['filename'];
        $oldPath = $user['profile_picture'] ?? null;
        if ($this->userModel->update($id, ['profile_picture' => $imagePath])) {
            if ($oldPath) {
                $oldFullPath = $publicPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $oldPath);
                if (file_exists($oldFullPath)) {
                    @unlink($oldFullPath);
                }
            }
            $currentUserId = (int) $this->session->get('user_id');
            if ($currentUserId === (int) $id) {
                $this->session->set('user_profile_picture', $imagePath);
            }
            $baseUrl = rtrim(\App\Utilities\AssetHelper::baseUrl(''), '/');
            $this->json([
                'success' => true,
                'message' => 'Profile picture updated.',
                'image_url' => $baseUrl . '/' . $imagePath
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to save.'], 500);
        }
    }

    /**
     * AJAX: return HTML fragment of finance table rows for a user (filtered by month/year).
     */
    public function financeRecords($id) {
        $user = $this->userModel->find($id);
        if (!$user) {
            http_response_code(404);
            header('Content-Type: text/html; charset=UTF-8');
            echo '';
            exit;
        }
        if (!$this->session->hasPermission('manage_finance')) {
            http_response_code(403);
            header('Content-Type: text/html; charset=UTF-8');
            echo '';
            exit;
        }
        $month = (int) $this->request->get('month', date('n'));
        $year = (int) $this->request->get('year', date('Y'));
        $month = max(1, min(12, $month));
        $year = max(2020, min((int) date('Y') + 1, $year));
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));
        $records = $this->financeModel->getFinanceWithDetails(['member_id' => $id], null, $startDate, $endDate);
        header('Content-Type: text/html; charset=UTF-8');
        $viewPath = __DIR__ . '/../views/users/_finance_records_rows.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        }
        exit;
    }

    /**
     * Show create form
     */
    public function create() {
        $csrfToken = Security::generateCSRFToken();
        $roles = ['admin', 'director', 'officer', 'pastor', 'user'];
        $ageGroups = \App\Models\User::getAgeGroups();
        $churches = $this->churchModel->findAll([], 'name ASC');
        
        $this->render('users/create', [
            'title' => 'Create User',
            'pageTitle' => 'Create User',
            'csrf_token' => $csrfToken,
            'roles' => $roles,
            'ageGroups' => $ageGroups,
            'churches' => $churches,
            'breadcrumbs' => [
                ['label' => 'Users', 'url' => '/users'],
                ['label' => 'Create', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new user
     */
    public function store() {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/users/create');
        }

        // Validate input
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'role' => 'required'
        ];
        
        if ($this->request->post('role') !== 'admin') {
            $rules['church_id'] = 'required';
        }
        
        $validation = $this->validate($rules);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/users/create');
        }

        // Check if email already exists
        $existingUser = $this->userModel->findByEmail($this->request->post('email'));
        if ($existingUser) {
            $this->session->setFlash('error', 'Email already exists.');
            $this->redirect('/users/create');
        }

        $data = [
            'email' => $this->request->post('email'),
            'password' => $this->request->post('password'), // Will be hashed in createUser
            'first_name' => $this->request->post('first_name'),
            'last_name' => $this->request->post('last_name'),
            'age_group' => $this->request->post('age_group') ?: null,
            'role' => $this->request->post('role'),
            'status' => $this->request->post('status', 'active'),
            'church_id' => $this->request->post('church_id') ? (int)$this->request->post('church_id') : null
        ];

        $id = $this->userModel->createUser($data);
        if ($id) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'create',
                'User',
                $id,
                "Created user: {$data['first_name']} {$data['last_name']} ({$data['email']})"
            );
            
            $this->session->setFlash('success', 'User created successfully.');
            $this->redirect("/users/{$id}");
        } else {
            $this->session->setFlash('error', 'Failed to create user.');
            $this->redirect('/users/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit($id) {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->session->setFlash('error', 'User not found.');
            $this->redirect('/users');
        }
        
        $csrfToken = Security::generateCSRFToken();
        $roles = ['admin', 'director', 'officer', 'pastor', 'user'];
        $ageGroups = \App\Models\User::getAgeGroups();
        $churches = $this->churchModel->findAll([], 'name ASC');
        
        $this->render('users/edit', [
            'title' => 'Edit User',
            'pageTitle' => 'Edit User',
            'user' => $user,
            'csrf_token' => $csrfToken,
            'roles' => $roles,
            'ageGroups' => $ageGroups,
            'churches' => $churches,
            'breadcrumbs' => [
                ['label' => 'Users', 'url' => '/users'],
                ['label' => $user['first_name'] . ' ' . $user['last_name'], 'url' => '/users/' . $id],
                ['label' => 'Edit', 'active' => true]
            ]
        ]);
    }

    /**
     * Update user
     */
    public function update($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/users/{$id}/edit");
        }

        // Validate input
        $rules = [
            'email' => 'required|email',
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'role' => 'required',
            'status' => 'required'
        ];
        
        if ($this->request->post('role') !== 'admin') {
            $rules['church_id'] = 'required';
        }
        
        $validation = $this->validate($rules);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/users/{$id}/edit");
        }

        // Check if email is being changed and if it already exists
        $user = $this->userModel->find($id);
        $newEmail = $this->request->post('email');
        if ($user['email'] !== $newEmail) {
            $existingUser = $this->userModel->findByEmail($newEmail);
            if ($existingUser) {
                $this->session->setFlash('error', 'Email already exists.');
                $this->redirect("/users/{$id}/edit");
            }
        }

        $data = [
            'email' => $newEmail,
            'first_name' => $this->request->post('first_name'),
            'last_name' => $this->request->post('last_name'),
            'age_group' => $this->request->post('age_group') ?: null,
            'role' => $this->request->post('role'),
            'status' => $this->request->post('status'),
            'church_id' => $this->request->post('church_id') ? (int)$this->request->post('church_id') : null
        ];

        // Update password only if provided
        if ($this->request->post('password')) {
            $this->userModel->updatePassword($id, $this->request->post('password'));
        }

        if ($this->userModel->update($id, $data)) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'update',
                'User',
                $id,
                "Updated user: {$data['first_name']} {$data['last_name']}"
            );
            
            $this->session->setFlash('success', 'User updated successfully.');
            $this->redirect("/users/{$id}");
        } else {
            $this->session->setFlash('error', 'Failed to update user.');
            $this->redirect("/users/{$id}/edit");
        }
    }

    /**
     * Delete user
     */
    public function delete($id) {
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/users');
        }

        // Prevent deleting yourself
        $currentUserId = $this->session->get('user_id');
        if ($id == $currentUserId) {
            $this->session->setFlash('error', 'You cannot delete your own account.');
            $this->redirect('/users');
        }

        $user = $this->userModel->find($id);
        $userName = $user ? "{$user['first_name']} {$user['last_name']}" : 'Unknown';
        
        if ($this->userModel->delete($id)) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'delete',
                'User',
                $id,
                "Deleted user: {$userName}"
            );
            
            $this->session->setFlash('success', 'User deleted successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to delete user.');
        }
        
        $this->redirect('/users');
    }

    /**
     * Assign user to unit (AJAX)
     */
    public function assignUnit() {
        $userId = (int)$this->request->post('user_id');
        $unitId = (int)$this->request->post('unit_id');
        $role = $this->request->post('role', 'member');

        if (!$userId || !$unitId) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        if ($this->unitModel->assignMember($unitId, $userId, $role)) {
            // Log activity
            $user = $this->userModel->find($userId);
            $unit = $this->unitModel->find($unitId);
            ActivityLog::log(
                $this->session->get('user_id'),
                'assign',
                'User',
                $userId,
                "Assigned user to unit {$unit['name']}"
            );
            
            $this->json(['success' => true, 'message' => 'User assigned to unit successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to assign user. User may already be assigned.'], 400);
        }
    }

    /**
     * Remove user from unit (AJAX)
     */
    public function removeUnit() {
        $userId = (int)$this->request->post('user_id');
        $unitId = (int)$this->request->post('unit_id');

        if (!$userId || !$unitId) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        if ($this->unitModel->removeMember($unitId, $userId)) {
            $this->json(['success' => true, 'message' => 'User removed from unit successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to remove user'], 400);
        }
    }

    /**
     * Assign user as director to unit (AJAX)
     */
    public function assignDirectorUnit() {
        $userId = (int)$this->request->post('user_id');
        $unitId = (int)$this->request->post('unit_id');

        if (!$userId || !$unitId) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        if ($this->unitModel->assignDirector($unitId, $userId)) {
            $this->json(['success' => true, 'message' => 'User assigned as director successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to assign director. User may already be assigned.'], 400);
        }
    }

    /**
     * Remove user as director from unit (AJAX)
     */
    public function removeDirectorUnit() {
        $userId = (int)$this->request->post('user_id');
        $unitId = (int)$this->request->post('unit_id');

        if (!$userId || !$unitId) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        if ($this->unitModel->removeDirector($unitId, $userId)) {
            $this->json(['success' => true, 'message' => 'Director removed successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to remove director'], 400);
        }
    }

    /**
     * Export users
     */
    public function export() {
        $format = $this->request->get('format', 'csv');
        $users = $this->userModel->findAll([], 'created_at DESC');
        
        $headers = ['ID', 'First Name', 'Last Name', 'Email', 'Role', 'Status', 'Created At'];
        $data = [];
        
        foreach ($users as $user) {
            $data[] = [
                'id' => $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'status' => $user['status'],
                'created_at' => $user['created_at']
            ];
        }
        
        $filename = 'users_' . date('Y-m-d_His') . '.' . $format;
        
        if ($format === 'json') {
            ExportHelper::exportJSON($data, $filename);
        } elseif ($format === 'pdf') {
            ExportHelper::exportPDF($data, $headers, 'Users Export', $filename);
        } else {
            ExportHelper::exportCSV($data, $headers, $filename);
        }
    }
}

