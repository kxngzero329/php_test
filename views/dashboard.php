<?php
$pageTitle = "Dashboard - ClockIt";
require_once __DIR__ . '/../layouts/header.php';

// Check authentication using middleware
if (!AuthMiddleware::checkAuth()) {
    header('Location: /login');
    exit;
}

$user = AuthMiddleware::getUser();
?>

<div class="dashboard-container">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="auth-card">
                    <div class="auth-header">
                        <h1 class="auth-title">Welcome, <?php echo htmlspecialchars($user['first_name'] ?? 'User'); ?>!</h1>
                        <p class="auth-subtitle">Here's your profile info:</p>
                    </div>

                    <div class="user-info mt-4">
                        <div class="info-item"><strong>Name:</strong> <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?></div>
                        <div class="info-item"><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
                        <div class="info-item"><strong>Employee ID:</strong> <?php echo htmlspecialchars($user['employee_id'] ?? ''); ?></div>
                        <div class="info-item"><strong>Role:</strong> <?php echo ($user['is_admin'] ?? false) ? "Administrator" : "Employee"; ?></div>
                        
                        <?php if (AdminMiddleware::isAdmin()): ?>
                            <div class="info-item"><strong>Status:</strong> <span class="text-success">Admin Access</span></div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="/api/auth/logout" class="btn btn-primary">Sign Out</a>
                        
                        <?php if (AdminMiddleware::isAdmin()): ?>
                            <a href="/admin" class="btn btn-secondary ms-2">Admin Panel</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>