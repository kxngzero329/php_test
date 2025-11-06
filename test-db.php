<?php
require_once 'config/database.php';
require_once 'config/env.php';

echo "<h1>Database Connection Test</h1>";

try {
    $db = new Database();
    $connection = $db->getConnection();
    
    echo "<p style='color: green;'>✅ Database connected successfully!</p>";
    
    // Test tables
    $tables = ['employees', 'account_auth', 'emp_classification'];
    foreach ($tables as $table) {
        $stmt = $connection->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p>✅ Table '$table' exists</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' NOT found</p>";
        }
    }
    
    // Test if we can read data
    $stmt = $connection->query("SELECT COUNT(*) as count FROM employees");
    $result = $stmt->fetch();
    echo "<p>✅ Employees table has " . $result['count'] . " records</p>";
    
    $stmt = $connection->query("SELECT COUNT(*) as count FROM emp_classification");
    $result = $stmt->fetch();
    echo "<p>✅ emp_classification table has " . $result['count'] . " records</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}
?>