<?php
class ErrorHandler {
    public static function handle($errno, $errstr, $errfile, $errline) {
        error_log("Error: {$errstr} in {$errfile} on line {$errline}");
        
        if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal Server Error'
            ]);
        } else {
            http_response_code(500);
            echo "Internal Server Error";
        }
        
        exit;
    }

    public static function handleException($exception) {
        error_log("Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
        
        if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        } else {
            http_response_code(500);
            echo "Internal Server Error: " . $exception->getMessage();
        }
        
        exit;
    }

    public static function register() {
        set_error_handler([self::class, 'handle']);
        set_exception_handler([self::class, 'handleException']);
    }
}
?>