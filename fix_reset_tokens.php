
<?php
require_once 'config/config.php';

echo "<h2>Fixing Reset Password Database Schema</h2>";

try {
    // Check if columns exist
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Current columns in users table:</h3>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>$column</li>";
    }
    echo "</ul>";
    
    // Add missing columns if they don't exist
    if (!in_array('reset_token', $columns)) {
        echo "<p>Adding reset_token column...</p>";
        $pdo->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(64) NULL");
        echo "<p style='color: green;'>✓ reset_token column added successfully</p>";
    } else {
        echo "<p style='color: blue;'>✓ reset_token column already exists</p>";
    }
    
    if (!in_array('reset_token_expires', $columns)) {
        echo "<p>Adding reset_token_expires column...</p>";
        $pdo->exec("ALTER TABLE users ADD COLUMN reset_token_expires TIMESTAMP NULL");
        echo "<p style='color: green;'>✓ reset_token_expires column added successfully</p>";
    } else {
        echo "<p style='color: blue;'>✓ reset_token_expires column already exists</p>";
    }
    
    // Test creating a reset token
    echo "<h3>Testing reset token creation:</h3>";
    $test_token = bin2hex(random_bytes(32));
    $test_expires = date('Y-m-d H:i:s', time() + 3600);
    
    echo "<p>Generated test token: <code>$test_token</code></p>";
    echo "<p>Token expires at: <code>$test_expires</code></p>";
    
    // Check admin user for testing
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
        $result = $stmt->execute([$test_token, $test_expires, $admin['id']]);
        
        if ($result) {
            echo "<p style='color: green;'>✓ Test token saved successfully for admin user</p>";
            echo "<p>You can test reset password with this link:</p>";
            echo "<p><a href='frontend/reset_password.php?token=$test_token' target='_blank' style='color: blue;'>Test Reset Password Link</a></p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to save test token</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<h3>Database schema fix completed!</h3>";
echo "<p><a href='frontend/forgot_password.php'>Go to Forgot Password Page</a></p>";
?>
