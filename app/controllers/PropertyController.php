<?php
namespace App\Controllers;

use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\PropertyLog;
use App\Models\PropertyTransfer;
use App\Models\PropertyAssignmentLog;
use App\Models\ActivityLog;
use App\Models\Church;
use App\Models\User;
use App\Utilities\Security;
use App\Utilities\FileUpload;

class PropertyController extends BaseController {
    private $propertyModel;
    private $categoryModel;
    private $churchModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->propertyModel = new Property();
        $this->categoryModel = new PropertyCategory();
        $this->churchModel = new Church();
        $this->userModel = new User();
    }

    private function isAdmin(): bool {
        return $this->session->get('user_role') === 'admin';
    }

    private function isHeadPastor(): bool {
        return $this->session->isHeadPastor();
    }

    private function getHeadPastorChurchId(): ?int {
        return $this->session->getHeadPastorChurchId();
    }

    private function canEditDetails(array $property): bool {
        if ($this->isAdmin()) {
            return true;
        }
        if ($this->isHeadPastor() && (int)($property['church_id'] ?? 0) === $this->getHeadPastorChurchId()) {
            return true;
        }
        return false;
    }

    private function canManageMovement(array $property): bool {
        $userId = (int)$this->session->get('user_id');
        if ($this->isAdmin()) {
            return true;
        }
        if ($this->isHeadPastor() && (int)($property['church_id'] ?? 0) === $this->getHeadPastorChurchId()) {
            return true;
        }
        if (!empty($property['assigned_to_user_id']) && (int)$property['assigned_to_user_id'] === $userId) {
            return true;
        }
        return false;
    }

    private function canAssign(array $property): bool {
        return $this->canEditDetails($property);
    }

    private function canView(array $property): bool {
        if ($this->isAdmin()) {
            return true;
        }
        if ($this->isHeadPastor() && (int)($property['church_id'] ?? 0) === $this->getHeadPastorChurchId()) {
            return true;
        }
        $userId = (int)$this->session->get('user_id');
        if (!empty($property['assigned_to_user_id']) && (int)$property['assigned_to_user_id'] === $userId) {
            return true;
        }
        return false;
    }

    /**
     * List all properties
     */
    public function index() {
        $categoryId = $this->request->get('category_id', '');
        $status = $this->request->get('status', '');
        $search = $this->request->get('search', '');
        $churchIdFilter = $this->request->get('church_id', '');

        $filters = [];
        if ($categoryId) {
            $filters['category_id'] = (int)$categoryId;
        }
        if ($status) {
            $filters['status'] = $status;
        }
        if ($search) {
            $filters['search'] = $search;
        }

        if ($this->isAdmin()) {
            if ($churchIdFilter) {
                $filters['church_id'] = (int)$churchIdFilter;
            }
        } elseif ($this->isHeadPastor()) {
            $filters['church_id'] = $this->getHeadPastorChurchId();
        } else {
            $filters['assigned_to_user_id'] = (int)$this->session->get('user_id');
        }

        $properties = $this->propertyModel->getAllWithDetails($filters);
        $categories = $this->categoryModel->findAll([], 'name ASC');
        $statusOptions = Property::getStatusOptions();
        $churches = $this->isAdmin() ? $this->churchModel->getChurches() : [];

        $this->render('properties/index', [
            'title' => 'Properties',
            'pageTitle' => 'Church Properties',
            'properties' => $properties,
            'categories' => $categories,
            'statusOptions' => $statusOptions,
            'filters' => $filters,
            'churches' => $churches,
            'isAdmin' => $this->isAdmin(),
            'isHeadPastor' => $this->isHeadPastor(),
        ]);
    }

    /**
     * Show create form
     */
    public function create() {
        // Only admin or head pastor can create properties
        if (!$this->isAdmin() && !$this->isHeadPastor()) {
            $this->redirect('unauthorized');
        }

        $categories = $this->categoryModel->findAll([], 'name ASC');
        $statusOptions = Property::getStatusOptions();
        $churches = $this->isAdmin() ? $this->churchModel->getChurches() : [];
        
        $post = $this->session->getFlash('_post');
        if (!is_array($post)) {
            $post = [];
        }

        $this->render('properties/create', [
            'title' => 'Add Property',
            'pageTitle' => 'Add Property',
            'categories' => $categories,
            'statusOptions' => $statusOptions,
            'csrf_token' => Security::generateCSRFToken(),
            'post' => $post,
            'churches' => $churches,
            'isAdmin' => $this->isAdmin(),
            'isHeadPastor' => $this->isHeadPastor(),
            'headPastorChurchId' => $this->getHeadPastorChurchId(),
        ]);
    }

    /**
     * Store new property
     */
    public function store() {
        if ($this->request->method() !== 'POST') {
            $this->redirect('properties/create');
            return;
        }

        if (!Security::validateCSRFToken($this->request->post('_token'))) {
            $this->session->setFlash('error', 'Invalid request. Please try again.');
            $this->redirect('properties/create');
            return;
        }

        // Only admin or head pastor can create properties
        if (!$this->isAdmin() && !$this->isHeadPastor()) {
            $this->redirect('unauthorized');
        }

        $errors = [];
        $churchId = 0;
        if ($this->isAdmin()) {
            $churchId = (int)$this->request->post('church_id', 0);
            if ($churchId <= 0) {
                $errors[] = 'Please select a church.';
            }
        } else {
            $churchId = $this->getHeadPastorChurchId();
        }

        $categoryId = (int) $this->request->post('category_id', 0);
        $name = trim($this->request->post('name', ''));
        $description = trim($this->request->post('description', ''));
        $status = $this->request->post('status', 'available');
        $location = trim($this->request->post('location', ''));
        $purchaseDate = $this->request->post('purchase_date', '');
        $purchaseCost = $this->request->post('purchase_cost', '');
        $serialNumber = trim($this->request->post('serial_number', ''));
        $notes = trim($this->request->post('notes', ''));

        if ($categoryId <= 0) {
            $errors[] = 'Please select a category.';
        }
        if ($name === '') {
            $errors[] = 'Property name is required.';
        }
        if (!in_array($status, array_keys(Property::getStatusOptions()))) {
            $status = 'available';
        }

        if (!empty($errors)) {
            $this->session->setFlash('error', implode(' ', $errors));
            $this->session->setFlash('_post', [
                'church_id' => $churchId,
                'category_id' => $categoryId,
                'name' => $name,
                'description' => $description,
                'status' => $status,
                'location' => $location,
                'purchase_date' => $purchaseDate,
                'purchase_cost' => $purchaseCost,
                'serial_number' => $serialNumber,
                'notes' => $notes
            ]);
            $this->redirect('properties/create');
            return;
        }

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $publicPath = realpath(__DIR__ . '/../../public');
            $uploadDir = $publicPath . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'properties';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileUpload = new FileUpload($uploadDir, $allowedImageTypes);
            $fileUpload->setMaxSize(5242880); // 5MB
            
            $result = $fileUpload->upload($_FILES['image'], 'property_' . time());
            if ($result['success']) {
                $imagePath = 'uploads/properties/' . $result['filename'];
            } else {
                $this->session->setFlash('error', $result['error'] ?? 'Image upload failed.');
                $this->session->setFlash('_post', [
                    'category_id' => $categoryId,
                    'name' => $name,
                    'description' => $description,
                    'status' => $status,
                    'location' => $location,
                    'purchase_date' => $purchaseDate,
                    'purchase_cost' => $purchaseCost,
                    'serial_number' => $serialNumber,
                    'notes' => $notes
                ]);
                $this->redirect('properties/create');
                return;
            }
        }

        $data = [
            'category_id' => $categoryId,
            'church_id' => $churchId,
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'image_path' => $imagePath,
            'location' => $location !== '' ? $location : null,
            'purchase_date' => $purchaseDate !== '' ? $purchaseDate : null,
            'purchase_cost' => $purchaseCost !== '' ? (float)$purchaseCost : null,
            'serial_number' => $serialNumber !== '' ? $serialNumber : null,
            'notes' => $notes !== '' ? $notes : null,
            'created_by' => (int) $this->session->get('user_id')
        ];

        $id = $this->propertyModel->create($data);

        if ($id) {
            $userId = (int) $this->session->get('user_id');
            $logModel = new PropertyLog();
            $logModel->logAction($id, $userId, 'created', null, $status, 'Property created');

            ActivityLog::log(
                $userId,
                'create',
                'Property',
                $id,
                "Created property: {$name}"
            );
            $this->session->setFlash('success', 'Property added successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to add property.');
        }

        $this->redirect('properties');
    }

    /**
     * Show property details
     */
    public function show($id) {
        $property = $this->propertyModel->getWithDetails($id);
        
        if (!$property) {
            $this->session->setFlash('error', 'Property not found.');
            $this->redirect('properties');
            return;
        }

        if (!$this->canView($property)) {
            $this->redirect('unauthorized');
            return;
        }

        $logModel = new PropertyLog();
        $logs = $logModel->getPropertyLogs($id);
        $statusOptions = Property::getStatusOptions();

        $assignableUsers = [];
        if ($this->canAssign($property)) {
            $assignableUsers = $this->userModel->findAll(['status' => 'active'], 'last_name ASC, first_name ASC');
        }

        $this->render('properties/show', [
            'title' => $property['name'],
            'pageTitle' => $property['name'],
            'property' => $property,
            'logs' => $logs,
            'statusOptions' => $statusOptions,
            'csrf_token' => Security::generateCSRFToken(),
            'canManageMovement' => $this->canManageMovement($property),
            'canAssign' => $this->canAssign($property),
            'canEditDetails' => $this->canEditDetails($property),
            'assignableUsers' => $assignableUsers,
        ]);
    }

    /**
     * Show edit form
     */
    public function edit($id) {
        $property = $this->propertyModel->find($id);
        
        if (!$property) {
            $this->session->setFlash('error', 'Property not found.');
            $this->redirect('properties');
            return;
        }

        if (!$this->canEditDetails($property)) {
            $this->redirect('unauthorized');
            return;
        }

        $categories = $this->categoryModel->findAll([], 'name ASC');
        $statusOptions = Property::getStatusOptions();
        
        $post = $this->session->getFlash('_post');
        if (!is_array($post)) {
            $post = $property;
        }

        $this->render('properties/edit', [
            'title' => 'Edit Property',
            'pageTitle' => 'Edit Property',
            'property' => $property,
            'categories' => $categories,
            'statusOptions' => $statusOptions,
            'csrf_token' => Security::generateCSRFToken(),
            'post' => $post
        ]);
    }

    /**
     * Update property
     */
    public function update($id) {
        if ($this->request->method() !== 'POST') {
            $this->redirect("properties/{$id}/edit");
            return;
        }

        if (!Security::validateCSRFToken($this->request->post('_token'))) {
            $this->session->setFlash('error', 'Invalid request. Please try again.');
            $this->redirect("properties/{$id}/edit");
            return;
        }

        $property = $this->propertyModel->find($id);
        if (!$property) {
            $this->session->setFlash('error', 'Property not found.');
            $this->redirect('properties');
            return;
        }

        if (!$this->canEditDetails($property)) {
            $this->redirect('unauthorized');
            return;
        }

        if (!$this->canEditDetails($property)) {
            $this->redirect('unauthorized');
            return;
        }

        $errors = [];
        $categoryId = (int) $this->request->post('category_id', 0);
        $name = trim($this->request->post('name', ''));
        $description = trim($this->request->post('description', ''));
        $status = $this->request->post('status', 'available');
        $location = trim($this->request->post('location', ''));
        $purchaseDate = $this->request->post('purchase_date', '');
        $purchaseCost = $this->request->post('purchase_cost', '');
        $serialNumber = trim($this->request->post('serial_number', ''));
        $notes = trim($this->request->post('notes', ''));

        if ($categoryId <= 0) {
            $errors[] = 'Please select a category.';
        }
        if ($name === '') {
            $errors[] = 'Property name is required.';
        }
        if (!in_array($status, array_keys(Property::getStatusOptions()))) {
            $status = $property['status'];
        }

        if (!empty($errors)) {
            $this->session->setFlash('error', implode(' ', $errors));
            $this->session->setFlash('_post', [
                'category_id' => $categoryId,
                'name' => $name,
                'description' => $description,
                'status' => $status,
                'location' => $location,
                'purchase_date' => $purchaseDate,
                'purchase_cost' => $purchaseCost,
                'serial_number' => $serialNumber,
                'notes' => $notes
            ]);
            $this->redirect("properties/{$id}/edit");
            return;
        }

        $imagePath = $property['image_path'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $publicPath = realpath(__DIR__ . '/../../public');
            $uploadDir = $publicPath . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'properties';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileUpload = new FileUpload($uploadDir, $allowedImageTypes);
            $fileUpload->setMaxSize(5242880); // 5MB
            
            $result = $fileUpload->upload($_FILES['image'], 'property_' . time());
            if ($result['success']) {
                // Delete old image if exists
                if ($imagePath && file_exists($publicPath . '/' . $imagePath)) {
                    @unlink($publicPath . '/' . $imagePath);
                }
                $imagePath = 'uploads/properties/' . $result['filename'];
            }
        }

        $oldStatus = $property['status'];
        $data = [
            'category_id' => $categoryId,
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'image_path' => $imagePath,
            'location' => $location !== '' ? $location : null,
            'purchase_date' => $purchaseDate !== '' ? $purchaseDate : null,
            'purchase_cost' => $purchaseCost !== '' ? (float)$purchaseCost : null,
            'serial_number' => $serialNumber !== '' ? $serialNumber : null,
            'notes' => $notes !== '' ? $notes : null
        ];

        $updated = $this->propertyModel->update($id, $data);

        if ($updated) {
            $userId = (int) $this->session->get('user_id');
            
            // Log status change if status changed
            if ($oldStatus !== $status) {
                $logModel = new PropertyLog();
                $logModel->logAction($id, $userId, 'status_change', $oldStatus, $status, 'Status updated via edit');
            } else {
                $logModel = new PropertyLog();
                $logModel->logAction($id, $userId, 'updated', null, null, 'Property details updated');
            }

            ActivityLog::log(
                $userId,
                'update',
                'Property',
                $id,
                "Updated property: {$name}"
            );
            $this->session->setFlash('success', 'Property updated successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to update property.');
        }

        $this->redirect("properties/{$id}");
    }

    /**
     * Update property status
     */
    public function updateStatus($id) {
        if ($this->request->method() !== 'POST') {
            $this->redirect("properties/{$id}");
            return;
        }

        if (!Security::validateCSRFToken($this->request->post('_token'))) {
            $this->json(['success' => false, 'message' => 'Invalid request.'], 403);
            return;
        }

        $property = $this->propertyModel->find($id);
        if (!$property) {
            $this->json(['success' => false, 'message' => 'Property not found.'], 404);
            return;
        }

        if (!$this->canManageMovement($property)) {
            $this->json(['success' => false, 'message' => 'Not authorized to update this property.'], 403);
            return;
        }

        $newStatus = $this->request->post('status', '');
        $notes = trim($this->request->post('notes', ''));

        if (!in_array($newStatus, array_keys(Property::getStatusOptions()))) {
            $this->json(['success' => false, 'message' => 'Invalid status.'], 400);
            return;
        }

        $userId = (int) $this->session->get('user_id');
        $updated = $this->propertyModel->updateStatus($id, $newStatus, $userId, $notes, $property['status']);

        if ($updated) {
            ActivityLog::log(
                $userId,
                'update',
                'Property',
                $id,
                "Updated property status to: {$newStatus}"
            );
            $this->json(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update status.'], 500);
        }
    }

    /**
     * Delete property
     */
    public function delete($id) {
        if ($this->request->method() !== 'POST') {
            $this->redirect('properties');
            return;
        }

        if (!Security::validateCSRFToken($this->request->post('_token'))) {
            $this->session->setFlash('error', 'Invalid request.');
            $this->redirect('properties');
            return;
        }

        $property = $this->propertyModel->find($id);
        if (!$property) {
            $this->session->setFlash('error', 'Property not found.');
            $this->redirect('properties');
            return;
        }

        $name = $property['name'];
        $deleted = $this->propertyModel->delete($id);

        if ($deleted) {
            // Delete image if exists
            if (!empty($property['image_path'])) {
                $publicPath = realpath(__DIR__ . '/../../public');
                $imagePath = $publicPath . '/' . $property['image_path'];
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }

            $userId = (int) $this->session->get('user_id');
            ActivityLog::log(
                $userId,
                'delete',
                'Property',
                $id,
                "Deleted property: {$name}"
            );
            $this->session->setFlash('success', 'Property deleted successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to delete property.');
        }

        $this->redirect('properties');
    }

    /**
     * Assign or reassign property to a user.
     */
    public function assign($id) {
        if ($this->request->method() !== 'POST') {
            $this->redirect("properties/{$id}");
            return;
        }

        if (!Security::validateCSRFToken($this->request->post('_token'))) {
            $this->session->setFlash('error', 'Invalid request.');
            $this->redirect("properties/{$id}");
            return;
        }

        $property = $this->propertyModel->find($id);
        if (!$property) {
            $this->session->setFlash('error', 'Property not found.');
            $this->redirect('properties');
            return;
        }

        if (!$this->canAssign($property)) {
            $this->redirect('unauthorized');
            return;
        }

        $toUserId = (int)$this->request->post('assigned_to_user_id', 0);
        $notes = trim($this->request->post('notes', ''));
        $userId = (int)$this->session->get('user_id');

        $updated = $this->propertyModel->assignToUser($id, $toUserId ?: null, $userId, $notes);

        if ($updated) {
            $this->session->setFlash('success', 'Property assignment updated.');
        } else {
            $this->session->setFlash('error', 'Failed to update assignment.');
        }

        $this->redirect("properties/{$id}");
    }

    /**
     * Transfer property between churches or relocate within same church.
     */
    public function transfer($id) {
        if ($this->request->method() !== 'POST') {
            $this->redirect("properties/{$id}");
            return;
        }

        if (!Security::validateCSRFToken($this->request->post('_token'))) {
            $this->session->setFlash('error', 'Invalid request.');
            $this->redirect("properties/{$id}");
            return;
        }

        $property = $this->propertyModel->find($id);
        if (!$property) {
            $this->session->setFlash('error', 'Property not found.');
            $this->redirect('properties');
            return;
        }

        if (!$this->canManageMovement($property)) {
            $this->redirect('unauthorized');
            return;
        }

        $currentChurchId = (int)($property['church_id'] ?? 0);
        $currentLocation = $property['location'] ?? null;

        $targetChurchId = (int)$this->request->post('church_id', $currentChurchId);
        $newLocation = trim($this->request->post('location', $currentLocation ?? ''));
        $notes = trim($this->request->post('notes', ''));
        $userId = (int)$this->session->get('user_id');

        if ($targetChurchId !== $currentChurchId) {
            $ok = $this->propertyModel->transferToChurch($id, $targetChurchId, $newLocation, $userId, $notes);
            $message = $ok ? 'Property transferred successfully.' : 'Failed to transfer property.';
        } else {
            $ok = $this->propertyModel->update($id, [
                'location' => $newLocation !== '' ? $newLocation : null,
            ]);
            if ($ok) {
                $logModel = new PropertyLog();
                $logModel->logAction($id, $userId, 'relocated', null, null, $notes ?: 'Location updated');
            }
            $message = $ok ? 'Property location updated.' : 'Failed to update location.';
        }

        if ($ok) {
            $this->session->setFlash('success', $message);
        } else {
            $this->session->setFlash('error', $message);
        }

        $this->redirect("properties/{$id}");
    }
}
