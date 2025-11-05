<?php
class EmailSender {
    private $host;
    private $port;
    private $username;
    private $password;
    private $from;

    public function __construct() {
        $this->host = $_ENV['EMAIL_HOST'] ?? 'smtp.gmail.com';
        $this->port = $_ENV['EMAIL_PORT'] ?? 587;
        $this->username = $_ENV['EMAIL_USER'] ?? '';
        $this->password = $_ENV['EMAIL_PASS'] ?? '';
        $this->from = $_ENV['EMAIL_USER'] ?? '';
    }

    public function send($to, $subject, $html, $text = "") {
        // For now, we'll log the email instead of actually sending it
        // In production, you would use PHPMailer or similar
        error_log("📧 Email would be sent to: {$to}");
        error_log("📋 Subject: {$subject}");
        error_log("🔗 Reset Link in HTML");
        
        return true;
        
        /* 
        // Production code would look like this with PHPMailer:
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->port;

            $mail->setFrom($this->from, 'Clock It');
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html;
            $mail->AltBody = $text;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
        */
    }
}
?>