<?php
namespace App\Traits;

use App\Models\Church;
use App\Core\Session;

trait ChurchScopedAccess {
    /**
     * Validate that the current user has access to a specific church
     * 
     * @param int|null $churchId The church ID to validate (null will use session)
     * @return array ['valid' => bool, 'church_id' => int|null, 'error' => string|null]
     */
    protected function validateChurchAccess($churchId = null) {
        $session = Session::getInstance();
        $userId = $session->get('user_id');
        $userRole = $session->get('user_role');
        
        if (!$userId) {
            return [
                'valid' => false,
                'church_id' => null,
                'error' => 'User not authenticated'
            ];
        }
        
        // Admin has access to all churches
        if ($userRole === 'admin') {
            return [
                'valid' => true,
                'church_id' => $churchId,
                'error' => null
            ];
        }
        
        // Head pastor can only access their assigned church
        if ($session->isHeadPastor()) {
            $headPastorChurchId = $session->getHeadPastorChurchId();
            
            // If no church ID provided, use the session's church ID
            if ($churchId === null) {
                $churchId = $headPastorChurchId;
            }
            
            // Verify the requested church matches the assigned church
            if ($churchId !== $headPastorChurchId) {
                return [
                    'valid' => false,
                    'church_id' => null,
                    'error' => 'Access denied. You can only access your assigned church.'
                ];
            }
            
            return [
                'valid' => true,
                'church_id' => $churchId,
                'error' => null
            ];
        }
        
        // For other roles (director, staff), check unit-based access
        if (in_array($userRole, ['director', 'officer', 'pastor'])) {
            // If no church ID provided, try to infer from session
            if ($churchId === null && $session->has('church_id')) {
                $churchId = $session->get('church_id');
            }
            
            if ($churchId) {
                // Verify user has access to this church through units
                $churchModel = new Church();
                $unitIds = $churchModel->getChurchUnitIds($churchId);
                
                if (!empty($unitIds)) {
                    // User has access if they belong to any unit in this church
                    return [
                        'valid' => true,
                        'church_id' => $churchId,
                        'error' => null
                    ];
                }
            }
        }
        
        return [
            'valid' => false,
            'church_id' => null,
            'error' => 'Access denied. Insufficient permissions for this church.'
        ];
    }
    
    /**
     * Get the current user's accessible church ID
     * Returns church ID if user has access, null otherwise
     * 
     * @return int|null
     */
    protected function getAccessibleChurchId() {
        $validation = $this->validateChurchAccess(null);
        return $validation['valid'] ? $validation['church_id'] : null;
    }
    
    /**
     * Redirect if user doesn't have church access
     * 
     * @param int|null $churchId
     * @param string $redirectUrl
     * @return bool True if valid, false if redirected
     */
    protected function redirectIfNoChurchAccess($churchId, $redirectUrl = '/') {
        $validation = $this->validateChurchAccess($churchId);
        
        if (!$validation['valid']) {
            $this->session->setFlash('error', $validation['error']);
            $this->redirect($redirectUrl);
            return false;
        }
        
        return true;
    }
}
