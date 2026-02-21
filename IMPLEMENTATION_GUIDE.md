# 💻 Implementation Guide
## Code Patterns & Best Practices

This guide provides concrete code examples and patterns to follow for consistent, scalable implementation.

---

## 🏗️ **1. BASE CLASSES (Foundation for DRY Code)**

### **BaseModel Pattern**
```php
<?php
// app/models/BaseModel.php
namespace App\Models;

use App\Core\Database;

abstract class BaseModel {
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Find single record
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Find all with optional conditions
    public function findAll($conditions = [], $orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $types = "";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = ?";
                $params[] = $value;
                $types .= is_int($value) ? "i" : "s";
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        if ($limit) $sql .= " LIMIT {$limit}";
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Create record
    public function create($data) {
        $data = $this->filterFillable($data);
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        $values = array_values($data);
        $types = str_repeat('s', count($values));
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    // Update record
    public function update($id, $data) {
        $data = $this->filterFillable($data);
        $fields = [];
        $values = [];
        $types = "";
        
        foreach ($data as $field => $value) {
            $fields[] = "{$field} = ?";
            $values[] = $value;
            $types .= is_int($value) ? "i" : "s";
        }
        
        $values[] = $id;
        $types .= "i";
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . 
               " WHERE {$this->primaryKey} = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    // Delete record
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Pagination
    public function paginate($page = 1, $perPage = 10, $conditions = []) {
        $offset = ($page - 1) * $perPage;
        $data = $this->findAll($conditions, "{$this->primaryKey} DESC", "{$offset}, {$perPage}");
        $total = $this->count($conditions);
        
        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    // Count records
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];
        $types = "";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = ?";
                $params[] = $value;
                $types .= is_int($value) ? "i" : "s";
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }

    // Filter fillable fields
    protected function filterFillable($data) {
        return array_intersect_key($data, array_flip($this->fillable));
    }
}
```

### **BaseController Pattern**
```php
<?php
// app/controllers/BaseController.php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Utilities\Validator;

abstract class BaseController {
    protected $request;
    protected $response;
    protected $session;

    public function __construct() {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = Session::getInstance();
    }

    // Render view
    protected function render($view, $data = []) {
        extract($data);
        $layout = $this->getLayout();
        
        ob_start();
        require_once __DIR__ . "/../views/{$view}.php";
        $content = ob_get_clean();
        
        require_once __DIR__ . "/../views/layouts/{$layout}.php";
    }

    // JSON response
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Redirect
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    // Validate input
    protected function validate($rules) {
        $validator = new Validator();
        return $validator->validate($this->request->all(), $rules);
    }

    // Check authorization
    protected function authorize($permission) {
        if (!$this->session->hasPermission($permission)) {
            $this->redirect('/unauthorized');
        }
    }

    // Get layout based on user role
    protected function getLayout() {
        $role = $this->session->get('user_role');
        return match($role) {
            'admin' => 'admin',
            'director' => 'main',
            default => 'main'
        };
    }
}
```

---

## 🗄️ **2. MODEL IMPLEMENTATION (Extending BaseModel)**

### **Example: Unit Model**
```php
<?php
// app/models/Unit.php
namespace App\Models;

class Unit extends BaseModel {
    protected $table = 'units';
    protected $fillable = ['name', 'description', 'status'];

    // Get unit members
    public function getMembers($unitId) {
        $sql = "SELECT u.*, uu.role, uu.joined_at 
                FROM users u 
                INNER JOIN unit_user uu ON u.id = uu.user_id 
                WHERE uu.unit_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $unitId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get unit directors
    public function getDirectors($unitId) {
        $sql = "SELECT u.*, ud.assigned_at 
                FROM users u 
                INNER JOIN unit_directors ud ON u.id = ud.user_id 
                WHERE ud.unit_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $unitId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Assign member to unit
    public function assignMember($unitId, $userId, $role = 'member') {
        $sql = "INSERT INTO unit_user (unit_id, user_id, role) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iis", $unitId, $userId, $role);
        return $stmt->execute();
    }

    // Assign director to unit
    public function assignDirector($unitId, $userId) {
        $sql = "INSERT INTO unit_directors (unit_id, user_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $unitId, $userId);
        return $stmt->execute();
    }
}
```

### **Example: Report Model**
```php
<?php
// app/models/Report.php
namespace App\Models;

class Report extends BaseModel {
    protected $table = 'reports';
    protected $fillable = ['unit_id', 'user_id', 'title', 'content', 'report_type', 'status'];

    // Get reports with unit and user info
    public function getReportsWithDetails($conditions = []) {
        $sql = "SELECT r.*, u.name as unit_name, us.first_name, us.last_name 
                FROM reports r 
                LEFT JOIN units u ON r.unit_id = u.id 
                LEFT JOIN users us ON r.user_id = us.id";
        
        $params = [];
        $types = "";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "r.{$field} = ?";
                $params[] = $value;
                $types .= is_int($value) ? "i" : "s";
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY r.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get report files
    public function getFiles($reportId) {
        $fileModel = new ReportFile();
        return $fileModel->findAll(['report_id' => $reportId]);
    }
}
```

---

## 🎮 **3. CONTROLLER IMPLEMENTATION (Extending BaseController)**

### **Example: UnitController**
```php
<?php
// app/controllers/UnitController.php
namespace App\Controllers;

use App\Models\Unit;
use App\Models\User;

class UnitController extends BaseController {
    private $unitModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->unitModel = new Unit();
        $this->userModel = new User();
        $this->authorize('manage_units'); // Check permission
    }

    // List all units
    public function index() {
        $page = $this->request->get('page', 1);
        $units = $this->unitModel->paginate($page, 10);
        $this->render('units/index', ['units' => $units]);
    }

    // Show single unit
    public function show($id) {
        $unit = $this->unitModel->find($id);
        if (!$unit) {
            $this->redirect('/units');
        }
        
        $members = $this->unitModel->getMembers($id);
        $directors = $this->unitModel->getDirectors($id);
        
        $this->render('units/show', [
            'unit' => $unit,
            'members' => $members,
            'directors' => $directors
        ]);
    }

    // Create unit form
    public function create() {
        $this->render('units/create');
    }

    // Store new unit
    public function store() {
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
            'description' => $this->request->post('description'),
            'status' => 'active'
        ];

        $id = $this->unitModel->create($data);
        if ($id) {
            $this->session->setFlash('success', 'Unit created successfully');
            $this->redirect("/units/{$id}");
        } else {
            $this->session->setFlash('error', 'Failed to create unit');
            $this->redirect('/units/create');
        }
    }

    // Update unit
    public function update($id) {
        $validation = $this->validate([
            'name' => 'required|min:3|max:255',
            'description' => 'optional|max:1000'
        ]);

        if (!$validation['valid']) {
            $this->session->setFlash('errors', $validation['errors']);
            $this->redirect("/units/{$id}/edit");
        }

        $data = [
            'name' => $this->request->post('name'),
            'description' => $this->request->post('description')
        ];

        if ($this->unitModel->update($id, $data)) {
            $this->session->setFlash('success', 'Unit updated successfully');
            $this->redirect("/units/{$id}");
        } else {
            $this->session->setFlash('error', 'Failed to update unit');
            $this->redirect("/units/{$id}/edit");
        }
    }

    // Delete unit
    public function delete($id) {
        if ($this->unitModel->delete($id)) {
            $this->session->setFlash('success', 'Unit deleted successfully');
        } else {
            $this->session->setFlash('error', 'Failed to delete unit');
        }
        $this->redirect('/units');
    }

    // Assign member (AJAX endpoint)
    public function assignMember() {
        $unitId = $this->request->post('unit_id');
        $userId = $this->request->post('user_id');
        $role = $this->request->post('role', 'member');

        if ($this->unitModel->assignMember($unitId, $userId, $role)) {
            $this->json(['success' => true, 'message' => 'Member assigned']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to assign'], 400);
        }
    }
}
```

---

## 🛠️ **4. REUSABLE UTILITIES**

### **Validator Utility**
```php
<?php
// app/utilities/Validator.php
namespace App\Utilities;

class Validator {
    private $errors = [];

    public function validate($data, $rules) {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return [
            'valid' => empty($this->errors),
            'errors' => $this->errors
        ];
    }

    private function applyRule($field, $value, $rule) {
        if (strpos($rule, ':') !== false) {
            [$ruleName, $param] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $param = null;
        }

        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->errors[$field][] = ucfirst($field) . ' is required';
                }
                break;
            case 'min':
                if (strlen($value) < $param) {
                    $this->errors[$field][] = ucfirst($field) . " must be at least {$param} characters";
                }
                break;
            case 'max':
                if (strlen($value) > $param) {
                    $this->errors[$field][] = ucfirst($field) . " must not exceed {$param} characters";
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be a valid email';
                }
                break;
            case 'optional':
                // Skip validation if field is empty
                break;
        }
    }
}
```

### **FileUpload Utility**
```php
<?php
// app/utilities/FileUpload.php
namespace App\Utilities;

class FileUpload {
    private $allowedTypes = [];
    private $maxSize = 5242880; // 5MB
    private $uploadPath = '';

    public function __construct($uploadPath, $allowedTypes = []) {
        $this->uploadPath = $uploadPath;
        $this->allowedTypes = $allowedTypes;
    }

    public function upload($file, $prefix = '') {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Upload error occurred'];
        }

        // Validate file type
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($this->allowedTypes) && !in_array($ext, $this->allowedTypes)) {
            return ['success' => false, 'error' => 'File type not allowed'];
        }

        // Validate file size
        if ($file['size'] > $this->maxSize) {
            return ['success' => false, 'error' => 'File size exceeds limit'];
        }

        // Generate unique filename
        $filename = $prefix . '_' . uniqid() . '_' . time() . '.' . $ext;
        $filepath = $this->uploadPath . '/' . $filename;

        // Create directory if it doesn't exist
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'size' => $file['size'],
                'type' => $file['type']
            ];
        }

        return ['success' => false, 'error' => 'Failed to move uploaded file'];
    }

    public function delete($filepath) {
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
}
```

---

## 🎨 **5. VIEW COMPONENTS (Reusable UI)**

### **Alert Component**
```php
<?php
// app/views/components/alerts.php
$session = \App\Core\Session::getInstance();

if ($session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($session->getFlash('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($session->getFlash('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($session->hasFlash('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            <?php foreach ($session->getFlash('errors') as $field => $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <li><?= htmlspecialchars($message) ?></li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
```

### **Pagination Component**
```php
<?php
// app/views/components/pagination.php
if (isset($pagination) && $pagination['total_pages'] > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php if ($pagination['current_page'] > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
```

---

## 🔒 **6. MIDDLEWARE IMPLEMENTATION**

### **AuthMiddleware**
```php
<?php
// app/middleware/AuthMiddleware.php
namespace App\Middleware;

use App\Core\Session;

class AuthMiddleware {
    public function handle($next) {
        $session = Session::getInstance();
        
        if (!$session->has('user_id')) {
            header('Location: /login');
            exit;
        }
        
        return $next();
    }
}
```

### **RoleMiddleware**
```php
<?php
// app/middleware/RoleMiddleware.php
namespace App\Middleware;

use App\Core\Session;

class RoleMiddleware {
    private $allowedRoles;

    public function __construct(...$roles) {
        $this->allowedRoles = $roles;
    }

    public function handle($next) {
        $session = Session::getInstance();
        $userRole = $session->get('user_role');
        
        if (!in_array($userRole, $this->allowedRoles)) {
            header('Location: /unauthorized');
            exit;
        }
        
        return $next();
    }
}
```

---

## 📝 **7. ROUTING PATTERN**

### **Route Definition**
```php
<?php
// routes/web.php
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

// Public routes
$router->get('/', 'DashboardController@index', [AuthMiddleware::class]);
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Unit routes (Admin only)
$router->group('/units', function($router) {
    $router->get('', 'UnitController@index');
    $router->get('/create', 'UnitController@create');
    $router->post('', 'UnitController@store');
    $router->get('/{id}', 'UnitController@show');
    $router->get('/{id}/edit', 'UnitController@edit');
    $router->put('/{id}', 'UnitController@update');
    $router->delete('/{id}', 'UnitController@delete');
    $router->post('/assign-member', 'UnitController@assignMember');
}, [AuthMiddleware::class, new RoleMiddleware('admin', 'director')]);

// Report routes
$router->group('/reports', function($router) {
    $router->get('', 'ReportController@index');
    $router->get('/create', 'ReportController@create');
    $router->post('', 'ReportController@store');
    $router->get('/{id}', 'ReportController@show');
}, [AuthMiddleware::class]);
```

---

## ✅ **KEY TAKEAWAYS**

1. **All Models extend BaseModel** → No repeated CRUD code
2. **All Controllers extend BaseController** → Shared HTTP handling
3. **Reusable Utilities** → Validator, FileUpload used everywhere
4. **View Components** → Alerts, pagination, forms reusable
5. **Middleware System** → Clean authorization/authentication
6. **Consistent Patterns** → Same structure across all modules

This approach ensures:
- ✅ Zero code repetition
- ✅ Easy to maintain
- ✅ Simple to extend
- ✅ Professional structure
- ✅ Scalable architecture

