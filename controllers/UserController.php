<?php
require_once '../config/database.php';
require_once '../utils/ResponseHandler.php';
require_once '../middleware/AuthMiddleware.php';

class UserController {
    private $db;
    private $responseHandler;

    public function __construct() {
        $this->db = new Database();
        $this->responseHandler = new ResponseHandler();
    }

    // ============================================================
    // Fetch logged-in user's profile
    // ============================================================
    public function getProfile() {
        try {
            $user = AuthMiddleware::getUser();
            $authId = $user['id'];

            $stmt = $this->db->query(
                "SELECT 
                    e.employee_id, e.first_name, e.last_name, e.contact_no, e.email, 
                    e.date_hired, e.supervisor_name, e.leave_balance, e.address, 
                    e.is_admin, e.id as employee_code, e.classification_id,
                    ec.department, ec.position, ec.role, ec.employment_type, ec.employee_level,
                    aa.username, aa.backup_email, aa.created_at
                 FROM account_auth aa
                 JOIN employees e ON aa.employee_id = e.employee_id
                 LEFT JOIN emp_classification ec ON e.classification_id = ec.classification_id
                 WHERE aa.auth_id = ?",
                [$authId]
            );

            $userData = $stmt->fetch();

            if (!$userData) {
                $this->responseHandler->send([
                    'success' => false, 
                    'message' => 'User not found.'
                ], 404);
                return;
            }

            // Generate initials for frontend display
            $initials = $userData['first_name'] && $userData['last_name'] 
                ? strtoupper(substr($userData['first_name'], 0, 1) . substr($userData['last_name'], 0, 1))
                : strtoupper(substr($userData['email'], 0, 1));

            // Send structured response
            $this->responseHandler->send([
                'success' => true,
                'message' => 'Profile fetched successfully.',
                'data' => [
                    'auth_id' => $user['id'],
                    'employee_id' => $userData['employee_id'],
                    'email' => $userData['email'],
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'contact_no' => $userData['contact_no'],
                    'address' => $userData['address'],
                    'employee_code' => $userData['employee_code'],
                    'is_admin' => $userData['is_admin'],
                    'department' => $userData['department'],
                    'position' => $userData['position'],
                    'role' => $userData['role'],
                    'employment_type' => $userData['employment_type'],
                    'employee_level' => $userData['employee_level'],
                    'leave_balance' => $userData['leave_balance'],
                    'supervisor_name' => $userData['supervisor_name'],
                    'date_hired' => $userData['date_hired'],
                    'initials' => $initials,
                ]
            ]);

        } catch (Exception $err) {
            error_log("Profile fetch error: " . $err->getMessage());
            $this->responseHandler->send([
                'success' => false, 
                'message' => 'Failed to fetch profile.'
            ], 500);
        }
    }

    // ============================================================
    // Change password (only for logged-in users)
    // ============================================================
    public function changePassword() {
        try {
            $user = AuthMiddleware::getUser();
            $authId = $user['id'];
            
            $data = json_decode(file_get_contents('php://input'), true);
            $currentPassword = $data['currentPassword'] ?? '';
            $newPassword = $data['newPassword'] ?? '';

            // Validate input
            if (!$currentPassword || !$newPassword) {
                $this->responseHandler->send([
                    'success' => false, 
                    'message' => 'Both current and new passwords are required.'
                ], 400);
                return;
            }

            // Check password strength
            $isStrong = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).{8,}$/', $newPassword);
            if (!$isStrong) {
                $this->responseHandler->send([
                    'success' => false, 
                    'message' => 'Password must include uppercase, lowercase, number, and special character.'
                ], 400);
                return;
            }

            // Fetch current password hash
            $stmt = $this->db->query(
                "SELECT password FROM account_auth WHERE auth_id = ?",
                [$authId]
            );
            
            $userAuth = $stmt->fetch();
            
            if (!$userAuth) {
                $this->responseHandler->send([
                    'success' => false, 
                    'message' => 'User not found.'
                ], 404);
                return;
            }

            // Compare passwords
            $hashPassword = new HashPassword();
            $match = $hashPassword->compare($currentPassword, $userAuth['password']);
            if (!$match) {
                $this->responseHandler->send([
                    'success' => false, 
                    'message' => 'Current password is incorrect.'
                ], 401);
                return;
            }

            // Hash and update new password
            $hashed = $hashPassword->hash($newPassword);
            $this->db->query(
                "UPDATE account_auth SET password = ? WHERE auth_id = ?",
                [$hashed, $authId]
            );

            $this->responseHandler->send([
                'success' => true, 
                'message' => 'Password updated successfully.'
            ]);

        } catch (Exception $err) {
            error_log("Change password error: " . $err->getMessage());
            $this->responseHandler->send([
                'success' => false, 
                'message' => 'Failed to update password.'
            ], 500);
        }
    }
}
?>