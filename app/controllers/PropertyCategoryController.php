<?php
namespace App\Controllers;

use App\Models\PropertyCategory;
use App\Models\ActivityLog;
use App\Utilities\Security;

class PropertyCategoryController extends BaseController {
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        $this->categoryModel = new PropertyCategory();
        $this->authorize('manage_properties');
    }

    /**
     * List all categories
     */
    public function index() {
        $categories = $this->categoryModel->getAllWithCounts();
        
        $this->render('property-categories/index', [
            'title' => 'Property Categories',
            'pageTitle' => 'Property Categories',
            'categories' => $categories
        ]);
    }

    /**
     * Show create form
     */
    public function create() {
        $post = $this->session->getFlash('_post');
        if (!is_array($post)) {
            $post = [];
        }

        $this->render('property-categories/create', [
            'title' => 'Create Category',
            'pageTitle' => 'Create Property Category',
            'csrf_token' => Security::generateCSRFToken(),
            'post' => $post
        ]);
    }

    /**
     * Store new category
     */
    public function store() {
        if ($this->request->method() !== 'POST') {
            $this->redirect('property-categories/create');
            return;
        }

        if (!Security::validateCSRFToken($this->request->post('_token'))) {
            $this->session->setFlash('error', 'Invalid request. Please try again.');
            $this->redirect('property-categories/create');
            return;
        }

        $name = trim($this->request->post('name', ''));
        $description = trim($this->request->post('description', ''));

        $errors = [];
        if ($name === '') {
            $errors[] = 'Category name is required.';
        }

        if (!empty($errors)) {
            $this->session->setFlash('error', implode(' ', $errors));
            $this->session->setFlash('_post', [
                'name' => $name,
                'description' => $description
            ]);
            $this->redirect('property-categories/create');
            return;
        }

        $userId = (int) $this->session->get('user_id');
        $id = $this->categoryModel->create([
            'name' => $name,
            'description' => $description,
            'created_by' => $userId
        ]);

        if ($id) {
            ActivityLog::log(
                $userId,
                'create',
                'PropertyCategory',
                $id,
                "Created property category: {$name}"
            );
            $this->session->setFlash('success', 'Category created successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to create category.');
        }

        $this->redirect('property-categories');
    }

    /**
     * Show edit form
     */
    public function edit($id) {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            $this->session->setFlash('error', 'Category not found.');
            $this->redirect('property-categories');
            return;
        }

        $post = $this->session->getFlash('_post');
        if (!is_array($post)) {
            $post = $category;
        }

        $this->render('property-categories/edit', [
            'title' => 'Edit Category',
            'pageTitle' => 'Edit Property Category',
            'category' => $category,
            'csrf_token' => Security::generateCSRFToken(),
            'post' => $post
        ]);
    }

    /**
     * Update category
     */
    public function update($id) {
        if ($this->request->method() !== 'POST') {
            $this->redirect("property-categories/{$id}/edit");
            return;
        }

        if (!Security::validateCSRFToken($this->request->post('_token'))) {
            $this->session->setFlash('error', 'Invalid request. Please try again.');
            $this->redirect("property-categories/{$id}/edit");
            return;
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            $this->session->setFlash('error', 'Category not found.');
            $this->redirect('property-categories');
            return;
        }

        $name = trim($this->request->post('name', ''));
        $description = trim($this->request->post('description', ''));

        $errors = [];
        if ($name === '') {
            $errors[] = 'Category name is required.';
        }

        if (!empty($errors)) {
            $this->session->setFlash('error', implode(' ', $errors));
            $this->session->setFlash('_post', [
                'name' => $name,
                'description' => $description
            ]);
            $this->redirect("property-categories/{$id}/edit");
            return;
        }

        $updated = $this->categoryModel->update($id, [
            'name' => $name,
            'description' => $description
        ]);

        if ($updated) {
            $userId = (int) $this->session->get('user_id');
            ActivityLog::log(
                $userId,
                'update',
                'PropertyCategory',
                $id,
                "Updated property category: {$name}"
            );
            $this->session->setFlash('success', 'Category updated successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to update category.');
        }

        $this->redirect('property-categories');
    }

    /**
     * Delete category
     */
    public function delete($id) {
        if ($this->request->method() !== 'POST') {
            $this->redirect('property-categories');
            return;
        }

        if (!Security::validateCSRFToken($this->request->post('_token'))) {
            $this->session->setFlash('error', 'Invalid request.');
            $this->redirect('property-categories');
            return;
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            $this->session->setFlash('error', 'Category not found.');
            $this->redirect('property-categories');
            return;
        }

        if ($this->categoryModel->hasProperties($id)) {
            $this->session->setFlash('error', 'Cannot delete category with existing properties. Please remove or reassign properties first.');
            $this->redirect('property-categories');
            return;
        }

        $name = $category['name'];
        $deleted = $this->categoryModel->delete($id);

        if ($deleted) {
            $userId = (int) $this->session->get('user_id');
            ActivityLog::log(
                $userId,
                'delete',
                'PropertyCategory',
                $id,
                "Deleted property category: {$name}"
            );
            $this->session->setFlash('success', 'Category deleted successfully.');
        } else {
            $this->session->setFlash('error', 'Failed to delete category.');
        }

        $this->redirect('property-categories');
    }
}
