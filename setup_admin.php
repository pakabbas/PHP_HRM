<?php
/**
 * Setup Script - Create First Admin User
 * Run this file once to create an admin account
 * 
 * Usage: php setup_admin.php
 * Or access via browser: http://localhost:8080/HRM/PHP_HRM/setup_admin.php
 */

require_once __DIR__ . '/app/bootstrap.php';

$db = Database::getInstance();

// Default admin credentials
$username = 'admin';
$password = 'admin123'; // Change this after first login!
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

try {
    // Check if admin already exists
    $check = $db->query("SELECT id FROM users WHERE username = :username", [':username' => $username]);
    if ($check->fetch()) {
        echo "<h2>Admin user already exists!</h2>";
        echo "<p>Username: <strong>{$username}</strong></p>";
        echo "<p>If you forgot the password, you can reset it in the database or delete this user and run this script again.</p>";
        exit;
    }

    // Create admin user
    $db->insert(
        "INSERT INTO users (username, password, role, status) VALUES (:username, :password, 'admin', 1)",
        [
            ':username' => $username,
            ':password' => $hashedPassword
        ]
    );

    echo "<h2>✅ Admin User Created Successfully!</h2>";
    echo "<div style='background: #e6f2ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>Login Credentials:</h3>";
    echo "<p><strong>Username:</strong> {$username}</p>";
    echo "<p><strong>Password:</strong> {$password}</p>";
    echo "</div>";
    echo "<p style='color: #ff3b30;'><strong>⚠️ IMPORTANT:</strong> Change this password immediately after first login!</p>";
    echo "<p><a href='?route=auth/login' style='background: #0A84FF; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; display: inline-block; margin-top: 20px;'>Go to Login Page</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: #ff3b30;'>❌ Error Creating Admin User</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Make sure:</p>";
    echo "<ul>";
    echo "<li>Database connection is configured in <code>creds.php</code></li>";
    echo "<li>Database tables are created (run the SQL from <code>db.txt</code>)</li>";
    echo "</ul>";
}
