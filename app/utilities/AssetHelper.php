<?php
namespace App\Utilities;

class AssetHelper {
    /**
     * Resolve the base path for the app (supports subfolder installs).
     * Examples:
     *  - /index.php                 => base path: ''
     *  - /ADMIN_PORTAL/index.php    => base path: '/ADMIN_PORTAL'
     *  - /ADMIN_PORTAL/public/index.php => base path: '/ADMIN_PORTAL/public'
     */
    protected static function basePath() {
        if (!isset($_SERVER['SCRIPT_NAME'])) {
            return '';
        }

        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
        $baseDir = dirname($scriptName);
        
        // Normalize the base path
        $baseDir = str_replace('\\', '/', $baseDir);
        
        // If the path ends in 'public', we return it as is
        // This handles cases like /ADMIN_PORTAL/public/index.php
        if (substr($baseDir, -7) === '/public') {
            return rtrim($baseDir, '/');
        }
        
        // If the SCRIPT_NAME itself contains /public/index.php 
        // but dirname returned something else due to trailing slashes
        if (strpos($scriptName, '/public/') !== false) {
            $publicPos = strpos($scriptName, '/public/');
            return substr($scriptName, 0, $publicPos + 7);
        }

        return ($baseDir === '/' || $baseDir === '.') ? '' : rtrim($baseDir, '/');
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

