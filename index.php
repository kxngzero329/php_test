<?php
require_once 'config/database.php';
require_once 'config/env.php';
require_once 'utils/ResponseHandler.php';
require_once 'utils/JWT.php';
require_once 'utils/HashPassword.php';
require_once 'middleware/AuthMiddleware.php';
require_once 'middleware/AdminMiddleware.php';
require_once 'middleware/ErrorHandler.php';

// Register error handlers
ErrorHandler::register();

session_start();

// Simple routing with middleware support
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Remove base directory if exists
$base_dir = '/php-app';
if (strpos($path, $base_dir) === 0) {
    $path = substr($path, strlen($base_dir));
}

$routes = [
    // Public routes
    '/' => ['view' => 'views/auth/login.php'],
    '/login' => ['view' => 'views/auth/login.php'],
    '/register' => ['view' => 'views/auth/register.php'],
    '/forgot-password' => ['view' => 'views/auth/forgot-password.php'],
    '/reset-password' => ['view' => 'views/auth/reset-password.php'],
    
    // Protected routes
    '/dashboard' => [
        'view' => 'views/dashboard.php', 
        'middleware' => 'AuthMiddleware::protect'
    ],
    
    // API routes - Auth
    '/api/auth/login' => ['controller' => 'AuthController@login'],
    '/api/auth/register' => ['controller' => 'AuthController@register'],
    '/api/auth/forgot-password' => ['controller' => 'AuthController@forgotPassword'],
    '/api/auth/reset-password' => ['controller' => 'AuthController@resetPassword'],
    '/api/auth/logout' => ['controller' => 'AuthController@logout'],
    '/api/auth/unlock-account' => [
        'controller' => 'AuthController@unlockAccount',
        'middleware' => 'AdminMiddleware::requireAdmin'
    ],
    '/api/auth/profile' => [
        'controller' => 'AuthController@getUserProfile',
        'middleware' => 'AuthMiddleware::protect'
    ],
    '/api/theme' => ['controller' => 'AuthController@setTheme'],
    
    // API routes - Users
    '/api/users/profile' => [
        'controller' => 'UserController@getProfile',
        'middleware' => 'AuthMiddleware::protect'
    ],
    '/api/users/change-password' => [
        'controller' => 'UserController@changePassword',
        'middleware' => 'AuthMiddleware::protect'
    ],
    
    // API routes - Notifications
    '/api/notifications' => [
        'controller' => 'NotificationController@getNotifications',
        'middleware' => 'AuthMiddleware::protect'
    ],
    '/api/admin/notify/all' => [
        'controller' => 'AdminNotificationController@broadcastNotification',
        'middleware' => 'AdminMiddleware::requireAdmin'
    ],
    '/api/admin/notify/user' => [
        'controller' => 'AdminNotificationController@personalNotification',
        'middleware' => 'AdminMiddleware::requireAdmin'
    ],
    '/api/notifications/broadcast' => [
        'controller' => 'NotificationController@sendBroadcastNotification',
        'middleware' => 'AdminMiddleware::requireAdmin'
    ],
    '/api/notifications/personal' => [
        'controller' => 'NotificationController@sendPersonalNotification',
        'middleware' => 'AdminMiddleware::requireAdmin'
    ],
];

// Handle the request
if (isset($routes[$path])) {
    $route = $routes[$path];
    
    // Apply middleware if specified
    if (isset($route['middleware'])) {
        call_user_func($route['middleware']);
    }
    
    // Handle controller routes
    if (isset($route['controller'])) {
        list($controller, $method) = explode('@', $route['controller']);
        require_once 'controllers/' . $controller . '.php';
        $controllerInstance = new $controller();
        $controllerInstance->$method();
    } 
    // Handle view routes
    elseif (isset($route['view'])) {
        // Additional protection for views
        if (strpos($path, '/dashboard') === 0 && !AuthMiddleware::checkAuth()) {
            header('Location: /login');
            exit;
        }
        
        // Redirect authenticated users away from auth pages
        if (in_array($path, ['/', '/login', '/register']) && AuthMiddleware::checkAuth()) {
            header('Location: /dashboard');
            exit;
        }
        
        require_once $route['view'];
    }
} else {
    http_response_code(404);
    
    if (strpos($path, '/api/') === 0) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint not found. Please check the API documentation at the root endpoint.'
        ]);
    } else {
        echo "Page not found";
    }
}
?>