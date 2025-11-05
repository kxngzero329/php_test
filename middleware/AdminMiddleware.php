<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/ResponseHandler.php';
require_once __DIR__ . '/../utils/NotifyUser.php';
require_once __DIR__ . '/AuthMiddleware.php';
class AdminMiddleware {
    private $db;
    private $responseHandler;

    public function __construct() {
        $this->db = new Database();
        $this->responseHandler = new ResponseHandler();
    }

    public function verifyAdmin() {
        // First verify the token
        $user = AuthMiddleware::protect();

        if (!$user) {
            $this->responseHandler->send([
                'success' => false, 
                'message' => 'No token provided'
            ], 401);
            exit;
        }

        try {
            // Get user from database and verify role
            $stmt = $this->db->query(
                "SELECT e.is_admin 
                 FROM employees e 
                 JOIN account_auth aa ON e.employee_id = aa.employee_id 
                 WHERE aa.auth_id = ?", 
                [$user['id']]
            );
            
            $userData = $stmt->fetch();

            if (!$userData || $userData['is_admin'] != 1) {
                $this->responseHandler->send([
                    'success' => false, 
                    'message' => 'Access denied — Admins only'
                ], 403);
                exit;
            }

            // Add admin flag to user object
            $user['is_admin'] = true;
            $_SESSION['user'] = $user;

            return $user;

        } catch (Exception $err) {
            error_log("Admin verification failed: " . $err->getMessage());
            $this->responseHandler->send([
                'success' => false, 
                'message' => 'Invalid or expired token'
            ], 401);
            exit;
        }
    }

    // Static method for easy usage in routes
    public static function requireAdmin() {
        $middleware = new self();
        return $middleware->verifyAdmin();
    }

    // Check if user is admin (for views)
    public static function isAdmin() {
        $user = AuthMiddleware::getUser();
        return $user && isset($user['is_admin']) && $user['is_admin'] == 1;
    }
}
?>