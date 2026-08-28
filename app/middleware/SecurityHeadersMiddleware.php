<?php
namespace App\Middleware;

class SecurityHeadersMiddleware {
    public function handle($next) {
        $secure = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1 || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        if ($secure) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        
        // Content Security Policy
        // Allows self scripts/styles, Google Fonts, and required CDNs (jsdelivr, unpkg, cdnjs)
        $csp = "default-src 'self'; "
             . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com; "
             . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; "
             . "font-src 'self' data: https://fonts.gstatic.com https://unpkg.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; "
             . "img-src 'self' data: blob: https:; "
             . "connect-src 'self' https:; "
             . "manifest-src 'self'; "
             . "worker-src 'self' blob:; "
             . "frame-ancestors 'none';";
        header("Content-Security-Policy: {$csp}");

        return $next();
    }
}
