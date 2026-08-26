<?php
namespace App\Controllers;

use App\Models\Church;

abstract class BaseHeadPastorController extends BaseController {
    protected $church;
    protected $churchId;

    public function __construct() {
        parent::__construct();
        $this->validateAccess();
        $this->loadChurchData();
    }

    private function validateAccess() {
        if ($this->session->get('user_role') === 'admin') {
            return; // Admins have access
        }

        if (!$this->session->isHeadPastor()) {
            $this->session->setFlash('error', 'Access denied. Head pastor privileges required.');
            $this->redirect('/unauthorized');
        }
    }

    private function loadChurchData() {
        if ($this->session->get('user_role') === 'admin') {
            // For admins, church ID might come from a request parameter
            $this->churchId = $this->request->get('church_id');
            if ($this->churchId) {
                $this->church = (new Church())->find($this->churchId);
            }
        } else {
            // For Head Pastors, always use the session
            $this->churchId = $this->session->getHeadPastorChurchId();
            if ($this->churchId) {
                $this->church = (new Church())->find($this->churchId);
            }
        }

        if (!$this->church) {
            $this->session->setFlash('error', 'Church not found or you do not have permission to access it.');
            $this->redirect('/');
        }
    }
}
