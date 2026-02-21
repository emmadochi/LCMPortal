<?php
namespace App\Utilities;

class AssetHelper {
    /**
     * Resolve the base path for the app (supports subfolder installs).
     * Examples:
     *  - /index.php                 => base path: ''
     *  - /ADMIN_PORTAL/public/index.php => base path: '/ADMIN_PORTAL/public'
     */
    protected static function basePath() {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath   = str_replace('\\', '/', dirname($scriptName));
        $basePath   = rtrim($basePath, '/');
        return $basePath === '.' ? '' : $basePath;
    }

    /**
     * Get CSS asset path
     */
    public static function css($file) {
        return self::basePath() . '/assets/css/' . ltrim($file, '/');
    }

    /**
     * Get JS asset path
     */
    public static function js($file) {
        return self::basePath() . '/assets/js/' . ltrim($file, '/');
    }

    /**
     * Get image asset path
     */
    public static function image($file) {
        return self::basePath() . '/assets/images/' . ltrim($file, '/');
    }

    /**
     * Get library asset path
     */
    public static function lib($file) {
        return self::basePath() . '/assets/libs/' . ltrim($file, '/');
    }

    /**
     * Get base URL
     */
    public static function baseUrl($path = '') {
        // Build URL relative to current host and detected base path
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base   = self::basePath();
        $baseUrl = rtrim($scheme . '://' . $host . $base, '/');

        return $baseUrl . '/' . ltrim($path, '/');
    }

    /**
     * Generate URL for application routes (handles subfolder installs)
     * Use this for all internal links instead of hardcoded paths
     * 
     * @param string $path Route path (e.g., 'units', '/units', 'units/create')
     * @return string Full URL with base path
     */
    public static function url($path = '') {
        $basePath = self::basePath();
        $path = ltrim($path, '/');
        return $basePath . '/' . $path;
    }
}

