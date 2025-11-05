<?php
require_once '../config/database.php';

class NotifyUser {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Save a notification for a specific user.
     * Automatically skips duplicates (same title+message within 1 minute).
     * 
     * @param int $userId - The user's ID.
     * @param string $title - Short title of the notification.
     * @param string $message - Detailed message body.
     */
    public function notify($userId, $title, $message) {
        try {
            // Check user exists
            $stmt = $this->db->query("SELECT employee_id FROM employees WHERE employee_id = ?", [$userId]);
            if ($stmt->rowCount() === 0) {
                error_log("notifyUser: Skipped — user {$userId} not found");
                return;
            }

            // Prevent duplicate notifications within 1 minute
            $stmt = $this->db->query(
                "SELECT notification_id FROM notifications_records 
                 WHERE employee_id = ? AND title = ? AND message = ? 
                 AND date_created >= NOW() - INTERVAL 1 MINUTE",
                [$userId, $title, $message]
            );
            
            if ($stmt->rowCount() > 0) {
                error_log("notifyUser: Skipped duplicate notification for user {$userId}");
                return;
            }

            // Insert notification
            $this->db->query(
                "INSERT INTO notifications_records (employee_id, title, message, date_created, is_broadcast) 
                 VALUES (?, ?, ?, CURDATE(), 0)",
                [$userId, $title, $message]
            );

            error_log("Notification saved for user {$userId}: {$title}");

        } catch (Exception $err) {
            error_log("❌ Error saving notification: " . $err->getMessage());
        }
    }

    /**
     * Send broadcast notification to all staff members
     */
    public function broadcast($title, $message) {
        try {
            // Get all staff users (non-admin employees)
            $stmt = $this->db->query(
                "SELECT employee_id FROM employees WHERE is_admin = 0"
            );
            $staff = $stmt->fetchAll();

            // Send to each staff member
            foreach ($staff as $user) {
                $this->notify($user['employee_id'], $title, $message);
            }

            // Also create a broadcast record
            $this->db->query(
                "INSERT INTO notifications_records (employee_id, title, message, date_created, is_broadcast) 
                 VALUES (0, ?, ?, CURDATE(), 1)",
                [$title, $message]
            );

            return true;

        } catch (Exception $err) {
            error_log("Broadcast notification error: " . $err->getMessage());
            return false;
        }
    }
}
?>