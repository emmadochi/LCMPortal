<?php
namespace App\Utilities;

class FileUpload {
    private $allowedTypes = [];
    private $maxSize = 5242880; // 5MB
    private $uploadPath = '';

    public function __construct($uploadPath, $allowedTypes = []) {
        $this->uploadPath = rtrim($uploadPath, '/');
        $this->allowedTypes = $allowedTypes;
    }

    public function upload($file, $prefix = '') {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Upload error occurred'];
        }

        // Validate file type
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Strict blacklist of executable and script extensions that are forbidden under all circumstances
        $blockedExtensions = [
            'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps', 'phar', 'pht',
            'html', 'htm', 'js', 'jsp', 'asp', 'aspx', 'sh', 'cgi', 'bat', 'cmd', 'exe', 'pl',
            'htaccess', 'htpasswd', 'ini', 'conf'
        ];
        
        if (in_array($ext, $blockedExtensions) || empty($ext)) {
            return ['success' => false, 'error' => 'Forbidden file extension or empty extension.'];
        }

        if (!empty($this->allowedTypes) && !in_array($ext, $this->allowedTypes)) {
            return ['success' => false, 'error' => 'File type not allowed. Allowed types: ' . implode(', ', $this->allowedTypes)];
        }

        // Validate file size
        if ($file['size'] > $this->maxSize) {
            $maxSizeMB = round($this->maxSize / 1048576, 2);
            return ['success' => false, 'error' => "File size exceeds limit of {$maxSizeMB}MB"];
        }

        // Generate unique filename
        $filename = ($prefix ? $prefix . '_' : '') . uniqid() . '_' . time() . '.' . $ext;
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
                'type' => $file['type'],
                'extension' => $ext
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

    public function setMaxSize($size) {
        $this->maxSize = $size;
        return $this;
    }

    public function setAllowedTypes($types) {
        $this->allowedTypes = $types;
        return $this;
    }
}

