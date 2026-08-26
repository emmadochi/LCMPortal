<?php
namespace App\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\ExportHelper;
use App\Utilities\SearchHelper;

class UnitController extends BaseController {
    private $unitModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->unitModel = new Unit();
        $this->userModel = new User();
        
        // No global authorization - check per method
        // Directors can view their units, admins can manage all
    }

    /**
     * Check if user is admin or director of specific unit
     */
    private function canAccessUnit($unitId) {
        // Admin has full access
        if ($this->session->hasPermission('manage_units')) {
            return true;
        }
        
        // Director can access their assigned units
        if ($this->session->isDirector()) {
            return $this->session->isDirectorOfUnit($unitId);
        }
        
        return false;
    }

    /**
     * Check if user can manage units (admin only)
     */
    private function requireManagePermission() {
        $this->authorize('manage_units');
    }

    /**
     * List all units
     */
    public function index() {
        $search = $this->request->get('search', '');
        $status = $this->request->get('status', '');
        $myUnits = $this->request->get('my_units', '');
        
        // Build conditions using SearchHelper
        $conditions = [];
        
        // Filter by status
        if ($status) {
            $conditions['status'] = $status;
        }
        
        // Check if user is admin or director
        $isAdmin = $this->session->hasPermission('manage_units');
        $isDirector = $this->session->isDirector();
        
        // Get units based on role
        if ($myUnits && $isDirector) {
            // Show only director's units
            $directorUnits = $this->session->getDirectorUnits();
            $unitIds = array_column($directorUnits, 'id');
            
            if (!empty($unitIds)) {
                $allUnits = $this->unitModel->findAll($conditions, 'created_at DESC');
                $units = array_filter($allUnits, function($unit) use ($unitIds) {
                    return in_array($unit['id'], $unitIds);
                });
            } else {
                $units = [];
            }
        } elseif ($isAdmin) {
            // Admin sees all units
            $units = $this->unitModel->findAll($conditions, 'created_at DESC');
        } elseif ($isDirector) {
            // Director sees their units by default
            $directorUnits = $this->session->getDirectorUnits();
            $unitIds = array_column($directorUnits, 'id');
            
            if (!empty($unitIds)) {
                $allUnits = $this->unitModel->findAll($conditions, 'created_at DESC');
                $units = array_filter($allUnits, function($unit) use ($unitIds) {
                    return in_array($unit['id'], $unitIds);
                });
            } else {
                $units = [];
            }
        } else {
            // No permission
            $this->session->setFlash('error', 'Access denied. Unit management privileges required.');
            $this->redirect('/unauthorized');
            return;
        }
        
        // Apply search filter if provided
        if ($search) {
            $searchTerm = SearchHelper::sanitize($search);
            $units = array_filter($units, function($unit) use ($searchTerm) {
                return stripos($unit['name'], $searchTerm) !== false ||
                       stripos($unit['description'] ?? '', $searchTerm) !== false;
            });
        }
        
        $this->render('units/index', [
            'title' => 'Units',
            'pageTitle' => 'Units',
            'units' => $units,
            'search' => $search,
            'status' => $status,
            'showMyUnitsFilter' => $isDirector && !$isAdmin,
            'isMyUnitsView' => $myUnits && $isDirector
        ]);
    }

    /**
     * Show single unit
     */
    public function show($id) {
        // Check if user can access this unit
        if (!$this->canAccessUnit($id)) {
            $this->session->setFlash('error', 'Access denied. You can only view units you are assigned to direct.');
            $this->redirect('/units');
            return;
        }
        
        $unit = $this->unitModel->find($id);
        
        if (!$unit) {
            $this->session->setFlash('error', 'Unit not found.');
            $this->redirect('/units');
        }
        
        $members = $this->unitModel->getMembers($id);
        $directors = $this->unitModel->getDirectors($id);
        $statistics = $this->unitModel->getStatistics($id);
        
        // Get all users for assignment dropdowns (only for admins)
        $allUsers = [];
        if ($this->session->hasPermission('manage_units')) {
            $allUsers = $this->userModel->findAll(['status' => 'active'], 'first_name, last_name');
        }
        
        $this->render('units/show', [
            'title' => $unit['name'],
            'pageTitle' => $unit['name'],
            'unit' => $unit,
            'members' => $members,
            'directors' => $directors,
            'statistics' => $statistics,
            'allUsers' => $allUsers,
            'canManage' => $this->session->hasPermission('manage_units'),
            'breadcrumbs' => [
                ['label' => 'Units', 'url' => '/units'],
                ['label' => $unit['name'], 'active' => true]
            ]
        ]);
    }

    /**
     * Show create form
     */
    public function create() {
        // Admin only
        $this->requireManagePermission();
        
        $csrfToken = Security::generateCSRFToken();
        $this->render('units/create', [
            'title' => 'Create Unit',
            'pageTitle' => 'Create Unit',
            'csrf_token' => $csrfToken,
            'breadcrumbs' => [
                ['label' => 'Units', 'url' => '/units'],
                ['label' => 'Create', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new unit
     */
    public function store() {
        // Admin only
        $this->requireManagePermission();
        
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/units/create');
        }

        // Validate input
        $validation = $this->validate([
            'name' => 'required|min:3|max:255',
            'description' => 'optional|max:1000'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect('/units/create');
        }

        $data = [
            'name' => $this->request->post('name'),
            'description' => $this->request->post('description', ''),
            'status' => 'active'
        ];

        $id = $this->unitModel->create($data);
        if ($id) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'create',
                'Unit',
                $id,
                "Created unit: {$data['name']}"
            );
            
            $this->session->setFlash('success', 'Unit created successfully.');
            $this->redirect("/units/{$id}");
        } else {
            $this->session->setFlash('error', 'Failed to create unit.');
            $this->redirect('/units/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit($id) {
        // Admin only
        $this->requireManagePermission();
        
        $unit = $this->unitModel->find($id);
        
        if (!$unit) {
            $this->session->setFlash('error', 'Unit not found.');
            $this->redirect('/units');
        }
        
        $csrfToken = Security::generateCSRFToken();
        $this->render('units/edit', [
            'title' => 'Edit Unit',
            'pageTitle' => 'Edit Unit',
            'unit' => $unit,
            'csrf_token' => $csrfToken,
            'breadcrumbs' => [
                ['label' => 'Units', 'url' => '/units'],
                ['label' => $unit['name'], 'url' => '/units/' . $id],
                ['label' => 'Edit', 'active' => true]
            ]
        ]);
    }

    /**
     * Update unit
     */
    public function update($id) {
        // Admin only
        $this->requireManagePermission();
        
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect("/units/{$id}/edit");
        }

        // Validate input
        $validation = $this->validate([
            'name' => 'required|min:3|max:255',
            'description' => 'optional|max:1000',
            'status' => 'required'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/units/{$id}/edit");
        }

        $data = [
            'name' => $this->request->post('name'),
            'description' => $this->request->post('description', ''),
            'status' => $this->request->post('status')
        ];

        if ($this->unitModel->update($id, $data)) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'update',
                'Unit',
                $id,
                "Updated unit: {$data['name']}"
            );
            
            $this->session->setFlash('success', 'Unit updated successfully.');
            $this->redirect("/units/{$id}");
        } else {
            $this->session->setFlash('error', 'Failed to update unit.');
            $this->redirect("/units/{$id}/edit");
        }
    }

    /**
     * Delete unit
     */
    public function delete($id) {
        // Admin only
        $this->requireManagePermission();
        
        // Validate CSRF
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/units');
        }

        $unit = $this->unitModel->find($id);
        $unitName = $unit ? $unit['name'] : 'Unknown';
        
        if ($this->unitModel->delete($id)) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'delete',
                'Unit',
                $id,
                "Deleted unit: {$unitName}"
            );
            
            $this->session->setFlash('success', 'Unit deleted successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to delete unit.');
        }
        
        $this->redirect('/units');
    }

    /**
     * Assign member to unit (AJAX)
     */
    public function assignMember() {
        // Admin only
        $this->requireManagePermission();
        
        $unitId = (int)$this->request->post('unit_id');
        $userId = (int)$this->request->post('user_id');
        $role = $this->request->post('role', 'member');

        if (!$unitId || !$userId) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        if ($this->unitModel->assignMember($unitId, $userId, $role)) {
            // Log activity
            $user = $this->userModel->find($userId);
            $unit = $this->unitModel->find($unitId);
            ActivityLog::log(
                $this->session->get('user_id'),
                'assign',
                'Unit',
                $unitId,
                "Assigned member {$user['first_name']} {$user['last_name']} to unit {$unit['name']}"
            );
            
            $this->json(['success' => true, 'message' => 'Member assigned successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to assign member. User may already be assigned.'], 400);
        }
    }

    /**
     * Remove member from unit (AJAX)
     */
    public function removeMember() {
        // Admin only
        $this->requireManagePermission();
        
        $unitId = (int)$this->request->post('unit_id');
        $userId = (int)$this->request->post('user_id');

        if (!$unitId || !$userId) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        if ($this->unitModel->removeMember($unitId, $userId)) {
            // Log activity
            $user = $this->userModel->find($userId);
            $unit = $this->unitModel->find($unitId);
            ActivityLog::log(
                $this->session->get('user_id'),
                'remove',
                'Unit',
                $unitId,
                "Removed member {$user['first_name']} {$user['last_name']} from unit {$unit['name']}"
            );
            
            $this->json(['success' => true, 'message' => 'Member removed successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to remove member'], 400);
        }
    }

    /**
     * Assign director to unit (AJAX)
     */
    public function assignDirector() {
        // Admin only
        $this->requireManagePermission();
        
        $unitId = (int)$this->request->post('unit_id');
        $userId = (int)$this->request->post('user_id');

        if (!$unitId || !$userId) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        if ($this->unitModel->assignDirector($unitId, $userId)) {
            // Log activity
            $user = $this->userModel->find($userId);
            $unit = $this->unitModel->find($unitId);
            ActivityLog::log(
                $this->session->get('user_id'),
                'assign',
                'Unit',
                $unitId,
                "Assigned director {$user['first_name']} {$user['last_name']} to unit {$unit['name']}"
            );
            
            $this->json(['success' => true, 'message' => 'Director assigned successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to assign director. User may already be assigned.'], 400);
        }
    }

    /**
     * Remove director from unit (AJAX)
     */
    public function removeDirector() {
        // Admin only
        $this->requireManagePermission();
        
        $unitId = (int)$this->request->post('unit_id');
        $userId = (int)$this->request->post('user_id');

        if (!$unitId || !$userId) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        if ($this->unitModel->removeDirector($unitId, $userId)) {
            // Log activity
            $user = $this->userModel->find($userId);
            $unit = $this->unitModel->find($unitId);
            ActivityLog::log(
                $this->session->get('user_id'),
                'remove',
                'Unit',
                $unitId,
                "Removed director {$user['first_name']} {$user['last_name']} from unit {$unit['name']}"
            );
            
            $this->json(['success' => true, 'message' => 'Director removed successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to remove director'], 400);
        }
    }

    /**
     * Export units
     */
    public function export() {
        $format = $this->request->get('format', 'csv');
        $units = $this->unitModel->findAll([], 'created_at DESC');
        
        $headers = ['ID', 'Name', 'Description', 'Status', 'Created At'];
        $data = [];
        
        foreach ($units as $unit) {
            $data[] = [
                'id' => $unit['id'],
                'name' => $unit['name'],
                'description' => $unit['description'] ?? '',
                'status' => $unit['status'],
                'created_at' => $unit['created_at']
            ];
        }
        
        $filename = 'units_' . date('Y-m-d_His') . '.' . $format;
        
        if ($format === 'json') {
            ExportHelper::exportJSON($data, $filename);
        } elseif ($format === 'pdf') {
            ExportHelper::exportPDF($data, $headers, 'Units Export', $filename);
        } else {
            ExportHelper::exportCSV($data, $headers, $filename);
        }
    }

    /**
     * Show units assigned to standard user, including unit announcements and files
     */
    public function myUnits() {
        $userId = $this->session->get('user_id');
        $units = $this->userModel->getUnits($userId);

        // Fetch unit announcements (broadcasts)
        $db = \App\Core\Database::getInstance();
        $broadcastsRes = $db->query("
            SELECT nb.*, u.first_name as sender_first, u.last_name as sender_last 
            FROM notification_broadcasts nb
            JOIN users u ON nb.sent_by_user_id = u.id
            WHERE nb.audience_type = 'unit_members'
            ORDER BY nb.created_at DESC
        ");
        
        $allBroadcasts = [];
        if ($broadcastsRes) {
            while ($row = $broadcastsRes->fetch_assoc()) {
                $allBroadcasts[] = $row;
            }
        }

        // Filter broadcasts that target user's units
        $unitAnnouncements = [];
        $unitIds = array_column($units, 'id');
        
        // Also fetch user's notifications to match read status
        $notificationModel = new Notification();
        $userNotifications = $notificationModel->getUserNotifications($userId, 100);

        foreach ($allBroadcasts as $b) {
            $scope = json_decode($b['audience_scope'], true);
            $targetUnitIds = $scope['unit_ids'] ?? [];
            
            // Check intersection of user's units and targeted units
            $intersect = array_intersect($unitIds, $targetUnitIds);
            if (!empty($intersect)) {
                // Find matching notification to get read/acknowledged status
                $notificationId = null;
                $acknowledged = false;
                
                foreach ($userNotifications as $n) {
                    if ($n['title'] === $b['title'] && $n['message'] === $b['message']) {
                        $notificationId = $n['id'];
                        $acknowledged = (int)$n['is_read'] === 1;
                        break;
                    }
                }
                
                $b['notification_id'] = $notificationId;
                $b['acknowledged'] = $acknowledged;
                $unitAnnouncements[] = $b;
            }
        }

        // Fetch unit files / media (if any)
        $unitMedia = [];
        if (!empty($unitIds)) {
            $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
            $types = str_repeat('i', count($unitIds));
            
            $stmt = $db->prepare("
                SELECT m.*, u.name as unit_name 
                FROM media m
                JOIN units u ON m.unit_id = u.id
                WHERE m.unit_id IN ({$placeholders})
                ORDER BY m.created_at DESC
            ");
            $stmt->bind_param($types, ...$unitIds);
            $stmt->execute();
            $unitMedia = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        $this->render('units/my_units', [
            'title' => 'My Units & Announcements',
            'pageTitle' => 'My Assigned Units',
            'units' => $units,
            'announcements' => $unitAnnouncements,
            'media' => $unitMedia,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/'],
                ['label' => 'My Units', 'active' => true]
            ]
        ]);
    }

    /**
     * Acknowledge/Mark announcement notification as read (AJAX)
     */
    public function acknowledgeAnnouncement($id) {
        $userId = $this->session->get('user_id');
        $notificationId = (int)$id;

        if ($notificationId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
            return;
        }

        $notificationModel = new Notification();
        $notification = $notificationModel->find($notificationId);

        if (!$notification || (int)$notification['user_id'] !== $userId) {
            $this->json(['success' => false, 'message' => 'Notification not found'], 404);
            return;
        }

        if ($notificationModel->markAsRead($notificationId)) {
            $this->json(['success' => true, 'message' => 'Announcement acknowledged successfully.']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to acknowledge announcement.'], 500);
        }
    }
}

