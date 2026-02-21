<?php
namespace App\Controllers;

use App\Models\Media;
use App\Models\Unit;
use App\Models\ActivityLog;
use App\Utilities\Security;
use App\Utilities\FileUpload;

class MediaController extends BaseController {
    private $mediaModel;
    private $unitModel;

    public function __construct() {
        parent::__construct();
        $this->mediaModel = new Media();
        $this->unitModel = new Unit();
        
        // Check permission
        $this->authorize('manage_media');
    }

    /**
     * List all media
     */
    public function index() {
        $media = $this->mediaModel->getMediaWithDetails([], 'created_at DESC');
        
        $this->render('media/index', [
            'title' => 'Media Library',
            'pageTitle' => 'Media Library',
            'media' => $media
        ]);
    }

    /**
     * Show upload form
     */
    public function create() {
        $csrfToken = Security::generateCSRFToken();
        $units = $this->unitModel->getActiveUnits();
        $categories = ['image', 'video', 'audio', 'document', 'presentation', 'other'];
        
        $this->render('media/create', [
            'title' => 'Upload Media',
            'pageTitle' => 'Upload Media',
            'csrf_token' => $csrfToken,
            'units' => $units,
            'categories' => $categories,
            'breadcrumbs' => [
                ['label' => 'Media', 'url' => '/media'],
                ['label' => 'Upload', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new media
     */
    public function store() {
        $token = $this->request->post('_token');
        if (!$token || !Security::validateCSRFToken($token)) {
            $this->session->setFlash('error', 'Invalid security token.');
            $this->redirect('/media/create');
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->session->setFlash('error', 'Please select a file to upload.');
            $this->redirect('/media/create');
        }

        $config = require __DIR__ . '/../../config/config.php';
        $uploadPath = $config['upload']['path'] . '/media';
        $allowedTypes = $config['upload']['allowed_types'];
        
        $fileUpload = new FileUpload($uploadPath, $allowedTypes);
        $fileUpload->setMaxSize($config['upload']['max_size']);
        
        $result = $fileUpload->upload($_FILES['file'], 'media_' . time());
        
        if (!$result['success']) {
            $this->session->setFlash('error', $result['message'] ?? 'File upload failed.');
            $this->redirect('/media/create');
        }

        $data = [
            'unit_id' => (int)$this->request->post('unit_id', 0),
            'uploaded_by' => $this->session->get('user_id'),
            'file_name' => $result['filename'],
            'file_path' => str_replace('\\', '/', $result['filepath']),
            'file_type' => $result['extension'],
            'file_size' => $result['size'],
            'title' => $this->request->post('title', $result['filename']),
            'description' => $this->request->post('description', ''),
            'category' => $this->request->post('category', 'other'),
            'tags' => $this->request->post('tags', '')
        ];

        $id = $this->mediaModel->create($data);
        
        if ($id) {
            // Log activity
            ActivityLog::log(
                $this->session->get('user_id'),
                'create',
                'Media',
                $id,
                "Uploaded media: {$data['title']}"
            );
            
            $this->session->setFlash('success', 'Media uploaded successfully.');
            $this->redirect('/media');
        } else {
            $this->session->setFlash('error', 'Failed to save media record.');
            $this->redirect('/media/create');
        }
    }

    /**
     * Show single media item
     */
    public function show($id) {
        $media = $this->mediaModel->find($id);
        
        if (!$media) {
            $this->session->setFlash('error', 'Media not found.');
            $this->redirect('/media');
        }
        
        $this->render('media/show', [
            'title' => $media['title'],
            'pageTitle' => $media['title'],
            'media' => $media
        ]);
    }
}

