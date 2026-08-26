<?php
namespace App\Core;

class Session {
    private static $instance = null;

    private function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set session name from configuration if available
            $configPath = __DIR__ . '/../../config/config.php';
            $sessionName = 'church_portal_session';
            $sessionLifetime = 7200;
            
            if (file_exists($configPath)) {
                $config = require $configPath;
                $sessionName = $config['session']['name'] ?? $sessionName;
                $sessionLifetime = $config['session']['lifetime'] ?? $sessionLifetime;
            }
            
            session_name($sessionName);
            
            // Determine secure flag dynamically
            $secure = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1 || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
            
            // Set secure session cookie parameters
            session_set_cookie_params([
                'lifetime' => $sessionLifetime,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
            session_start();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function destroy() {
        $_SESSION = [];
        if (session_id() !== '') {
            session_destroy();
        }
    }

    public function setFlash($key, $value) {
        $_SESSION['_flash'][$key] = $value;
    }

    public function getFlash($key, $default = null) {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public function hasFlash($key) {
        return isset($_SESSION['_flash'][$key]);
    }

    public function hasPermission($permission) {
        $userRole = $this->get('user_role');
        $permissions = $this->get('user_permissions', []);
        
        // Admin has all permissions
        if ($userRole === 'admin') {
            return true;
        }
        
        return in_array($permission, $permissions);
    }

    /**
     * Check if current user is a head pastor of any church
     */
    public function isHeadPastor() {
        return $this->has('head_pastor_church_id') && $this->get('head_pastor_church_id') > 0;
    }

    /**
     * Get the church ID the current user is head pastor of (null if not head pastor)
     */
    public function getHeadPastorChurchId() {
        return $this->isHeadPastor() ? (int) $this->get('head_pastor_church_id') : null;
    }

    /**
     * Check if current user is a director of any unit
     */
    public function isDirector() {
        return $this->has('director_units') && !empty($this->get('director_units'));
    }

    /**
     * Get the units the current user is directing (empty array if not director)
     */
    public function getDirectorUnits() {
        return $this->isDirector() ? $this->get('director_units', []) : [];
    }

    /**
     * Check if user is director of a specific unit
     */
    public function isDirectorOfUnit($unitId) {
        $units = $this->getDirectorUnits();
        foreach ($units as $unit) {
            if ($unit['id'] == $unitId) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if current user is any type of pastor (head pastor or regular pastor)
     */
    public function isPastor() {
        return $this->get('is_pastor', false);
    }

    /**
     * Check if current user is a head pastor
     */
    public function isHeadPastorRole() {
        return $this->get('user_role') === 'head_pastor';
    }

    /**
     * Check if current user is a pastor-director (pastor who is also a unit director)
     */
    public function isPastorDirector() {
        return $this->get('is_pastor_director', false);
    }

    /**
     * Check if current user is a unit head of any unit at any church branch
     */
    public function isUnitHead() {
        return $this->has('unit_head_assignments') && !empty($this->get('unit_head_assignments'));
    }

    /**
     * Get the unit head assignments for the current user
     */
    public function getUnitHeadAssignments() {
        return $this->isUnitHead() ? $this->get('unit_head_assignments', []) : [];
    }
}


