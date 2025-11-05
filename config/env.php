<?php
// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
}

// Set default values
$_ENV['DB_HOST'] = $_ENV['DB_HOST'] ?? 'localhost';
$_ENV['DB_USER'] = $_ENV['DB_USER'] ?? 'root';
$_ENV['DB_PASSWORD'] = $_ENV['DB_PASSWORD'] ?? 'shakeel2003';
$_ENV['DB_NAME'] = $_ENV['DB_NAME'] ?? 'tracker_db';
$_ENV['JWT_SECRET'] = $_ENV['JWT_SECRET'] ?? 'your-jwt-secret-key';
$_ENV['FRONTEND_ORIGIN'] = $_ENV['FRONTEND_ORIGIN'] ?? 'http://localhost/attendace_tracker';
?>