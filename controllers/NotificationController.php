<?php
require_once '../config/database.php';
require_once '../utils/ResponseHandler.php';
require_once '../middleware/AuthMiddleware.php';

class NotificationController {
    private $db;
    private $responseHandler;

    public function __construct() {
        $this->db = new Database();
        $this->responseHandler = new ResponseHandler();
    }

    // ============================================================
    // Fetch notifications for the logged-in user
    // ============================================================
    public function getNotifications() {
        // Verify authentication
        $user = AuthMiddleware::protect();
        $userId = $user['employee_id'];

        try {
            // Fetch both personal (employee_id match) and broadcast notifications
            $stmt = $this->db->query(
                "SELECT notification_id, title, message, date_created, is_broadcast
                 FROM notifications_records
                 WHERE employee_id = ? OR is_broadcast = 1
                 ORDER BY date_created DESC, notification_id DESC",
                [$userId]
            );

            $notifications = $stmt->fetchAll();

            if (empty($notifications)) {
                $this->responseHandler->send([
                    'success' => true, 
                    'message' => 'No notifications yet.', 
                    'data' => ['notifications' => []]
                ]);
                return;
            }

            $this->responseHandler->send([
                'success' => true,
                'message' => 'Notifications fetched successfully.',
                'data' => ['notifications' => $notifications]
            ]);

        } catch (Exception $err) {
            error_log("Fetch notifications error: " . $err->getMessage());
            $this->responseHandler->send([
                'success' => false, 
                'message' => 'Failed to fetch notifications.'
            ], 500);
        }
    }

    // ============================================================
    // Admin: Send notification to all users (broadcast)
    // ============================================================
    public function sendBroadcastNotification() {
        // Verify admin access
        require_once '../middleware/AdminMiddleware.php';
        AdminMiddleware::requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'] ?? '';
        $message = $data['message'] ?? '';

        if (!$title || !$message) {
            $this->responseHandler->send([
                'success' => false, 
                'message' => 'Title and message are required.'
            ], 400);
            return;
        }

        try {
            // Insert a single broadcast notification (visible to everyone)
            $this->db->query(
                "INSERT INTO notifications_records (employee_id, title, message, date_created, is_broadcast) 
                 VALUES (0, ?, ?, CURDATE(), 1)",
                [$title, $message]
            );

            $this->responseHandler->send([
                'success' => true, 
                'message' => 'Broadcast notification sent to all users.'
            ], 201);

        } catch (Exception $err) {
            error_log("Broadcast notification error: " . $err->getMessage());
            $this->responseHandler->send([
                'success' => false, 
                'message' => 'Failed to send broadcast notification.'
            ], 500);
        }
    }

    // ============================================================
    // Admin: Send personal notification to a specific user
    // ============================================================
    public function sendPersonalNotification() {
        // Verify admin access
        require_once '../middleware/AdminMiddleware.php';
        AdminMiddleware::requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $data['userId'] ?? '';
        $title = $data['title'] ?? '';
        $message = $data['message'] ?? '';

        if (!$userId || !$title || !$message) {
            $this->responseHandler->send([
                'success' => false, 
                'message' => 'userId, title, and message are required.'
            ], 400);
            return;
        }

        try {
            // Insert personal message for one specific user
            $this->db->query(
                "INSERT INTO notifications_records (employee_id, title, message, date_created, is_broadcast) 
                 VALUES (?, ?, ?, CURDATE(), 0)",
                [$userId, $title, $message]
            );

            $this->responseHandler->send([
                'success' => true, 
                'message' => "Notification sent to user ID {$userId}."
            ], 201);

        } catch (Exception $err) {
            error_log("Personal notification error: " . $err->getMessage());
            $this->responseHandler->send([
                'success' => false, 
                'message' => 'Failed to send personal notification.'
            ], 500);
        }
    }
}
?>