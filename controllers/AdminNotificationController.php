<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/ResponseHandler.php';
require_once __DIR__ . '/../utils/NotifyUser.php';
require_once __DIR__ . '/../middleware/AdminMiddleware.php';

class AdminNotificationController {
    private $db;
    private $responseHandler;
    private $notifyUser;

    public function __construct() {
        $this->db = new Database();
        $this->responseHandler = new ResponseHandler();
        $this->notifyUser = new NotifyUser();
    }

    /**
     * Send a notification to ALL staff members (broadcast message).
     * Only admins can use this route.
     */
    public function broadcastNotification() {
        // Verify admin access
        AdminMiddleware::requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'] ?? '';
        $message = $data['message'] ?? '';

        if (!$title || !$message) {
            $this->responseHandler->send([
                'success' => false, 
                'message' => 'Title and message are required'
            ], 400);
            return;
        }

        try {
            $result = $this->notifyUser->broadcast($title, $message);

            if ($result) {
                $this->responseHandler->send([
                    'success' => true, 
                    'message' => 'Broadcast message sent to all staff'
                ]);
            } else {
                $this->responseHandler->send([
                    'success' => false, 
                    'message' => 'Failed to send broadcast message'
                ], 500);
            }

        } catch (Exception $err) {
            error_log("Broadcast failed: " . $err->getMessage());
            $this->responseHandler->send([
                'success' => false, 
                'message' => 'Failed to send broadcast message'
            ], 500);
        }
    }

    /**
     * Send a notification to a specific staff member.
     * Only admins can use this route.
     */
    public function personalNotification() {
        // Verify admin access
        AdminMiddleware::requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $data['userId'] ?? '';
        $title = $data['title'] ?? '';
        $message = $data['message'] ?? '';

        if (!$userId || !$title || !$message) {
            $this->responseHandler->send([
                'success' => false, 
                'message' => 'userId, title, and message are required'
            ], 400);
            return;
        }

        try {
            // Check if user exists
            $stmt = $this->db->query("SELECT employee_id FROM employees WHERE employee_id = ?", [$userId]);
            if ($stmt->rowCount() === 0) {
                $this->responseHandler->send([
                    'success' => false, 
                    'message' => 'User not found'
                ], 404);
                return;
            }

            $this->notifyUser->notify($userId, $title, $message);

            $this->responseHandler->send([
                'success' => true, 
                'message' => 'Message sent to selected staff member'
            ]);

        } catch (Exception $err) {
            error_log("Personal message failed: " . $err->getMessage());
            $this->responseHandler->send([
                'success' => false, 
                'message' => 'Failed to send personal message'
            ], 500);
        }
    }
}
?>