<?php
namespace App\Core;

class Session {
    private static $instance = null;

    private function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
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
        session_destroy();
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
}

