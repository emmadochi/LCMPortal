<?php
namespace App\Models;

use App\Utilities\Security;
use App\Utilities\NotificationHelper;

class PasswordResetRequest extends BaseModel {
    protected $table = 'password_reset_requests';
    protected $fillable = [
        'user_id', 'email', 'token', 'status', 'expires_at', 
        'approved_by', 'rejected_by', 'approved_at', 'rejected_at', 'reason'
    ];

    /**
     * Create a new password reset request
     */
    public function createRequest($userId, $email) {
        $token = $this->generateToken();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        return $this->create([
            'user_id' => $userId,
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);
    }

    /**
     * Generate a unique token
     */
    private function generateToken() {
        do {
            $token = bin2hex(random_bytes(32));
            $existing = $this->findByToken($token);
        } while ($existing);
        
        return $token;
    }

    /**
     * Find one record by conditions
     */
    public function findOne($conditions = []) {
        $results = $this->findAll($conditions, null, 1);
        return !empty($results) ? $results[0] : null;
    }

    /**
     * Find request by token
     */
    public function findByToken($token) {
        return $this->findOne(['token' => $token]);
    }

    /**
     * Find pending requests by email
     */
    public function findPendingByEmail($email) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE email = ? AND status = 'pending' AND expires_at > NOW() 
                ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Find all pending requests for admin
     */
    public function findPendingRequests() {
        $sql = "SELECT pr.*, u.first_name, u.last_name, u.email as user_email 
                FROM {$this->table} pr 
                JOIN users u ON pr.user_id = u.id 
                WHERE pr.status = 'pending' 
                ORDER BY pr.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Approve a password reset request
     */
    public function approve($id, $adminId) {
        $request = $this->find($id);
        if (!$request || $request['status'] !== 'pending') {
            return false;
        }
        
        $result = $this->update($id, [
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($result) {
            // Send notification to user that request is approved
            $userModel = new User();
            $user = $userModel->find($request['user_id']);
            if ($user) {
                NotificationHelper::notify(
                    $request['user_id'],
                    'success',
                    'Password Reset Approved',
                    'Your password reset request has been approved. You can now reset your password.'
                );
                
                // Send email notification to user
                $config = require __DIR__ . '/../../../config/config.php';
                $baseUrl = $config['app_url'];
                $resetUrl = $baseUrl . '/reset-password/' . $request['token'];
                
                $emailSubject = 'Password Reset Request Approved';
                $emailMessage = '<h2>Password Reset Approved</h2>'
                             . '<p>Your password reset request has been approved.</p>'
                             . '<p>You can now reset your password by clicking the link below:</p>'
                             . "<p><a href='{$resetUrl}'>Reset Your Password</a></p>"
                             . '<p>This link will expire in 24 hours.</p>';
                
                NotificationHelper::sendEmail($user['email'], $emailSubject, $emailMessage);
            }
        }
        
        return $result;
    }

    /**
     * Reject a password reset request
     */
    public function reject($id, $adminId, $reason = null) {
        $request = $this->find($id);
        if (!$request || $request['status'] !== 'pending') {
            return false;
        }
        
        $result = $this->update($id, [
            'status' => 'rejected',
            'rejected_by' => $adminId,
            'rejected_at' => date('Y-m-d H:i:s'),
            'reason' => $reason
        ]);
        
        if ($result) {
            // Send notification to user that request is rejected
            $userModel = new User();
            $user = $userModel->find($request['user_id']);
            if ($user) {
                $message = 'Your password reset request has been rejected.';
                if ($reason) {
                    $message .= ' Reason: ' . $reason;
                }
                NotificationHelper::notify(
                    $request['user_id'],
                    'error',
                    'Password Reset Rejected',
                    $message
                );
                
                // Send email notification to user
                $emailSubject = 'Password Reset Request Rejected';
                $emailMessage = '<h2>Password Reset Rejected</h2>'
                             . '<p>Your password reset request has been rejected.</p>';
                if ($reason) {
                    $emailMessage .= "<p><strong>Reason:</strong> {$reason}</p>";
                }
                $emailMessage .= '<p>If you believe this was done in error, please contact your administrator.</p>';
                
                NotificationHelper::sendEmail($user['email'], $emailSubject, $emailMessage);
            }
        }
        
        return $result;
    }

    /**
     * Complete a password reset (after user resets password)
     */
    public function complete($id) {
        $request = $this->find($id);
        $result = $this->update($id, [
            'status' => 'completed',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($result && $request) {
            // Send notification to user that password has been reset
            $userModel = new User();
            $user = $userModel->find($request['user_id']);
            if ($user) {
                NotificationHelper::notify(
                    $request['user_id'],
                    'success',
                    'Password Reset Successful',
                    'Your password has been successfully reset. You can now log in with your new password.'
                );
                
                // Send email notification to user
                $emailSubject = 'Password Reset Successful';
                $emailMessage = '<h2>Password Reset Successful</h2>'
                             . '<p>Your password has been successfully reset.</p>'
                             . '<p>You can now log in to your account with your new password.</p>'
                             . '<p>If you did not make this change, please contact your administrator immediately.</p>';
                
                NotificationHelper::sendEmail($user['email'], $emailSubject, $emailMessage);
            }
        }
        
        return $result;
    }

    /**
     * Check if request is valid and not expired
     */
    public function isValidRequest($token) {
        $request = $this->findByToken($token);
        if (!$request) {
            return false;
        }
        
        if ($request['status'] !== 'approved') {
            return false;
        }
        
        if (strtotime($request['expires_at']) < time()) {
            return false;
        }
        
        return true;
    }

    /**
     * Get user by token
     */
    public function getUserByToken($token) {
        $request = $this->findByToken($token);
        if (!$request) {
            return null;
        }
        
        $userModel = new User();
        return $userModel->find($request['user_id']);
    }

    /**
     * Cleanup expired requests
     */
    public function cleanupExpired() {
        $sql = "DELETE FROM {$this->table} WHERE expires_at < NOW() AND status = 'pending'";
        return $this->db->query($sql);
    }
}