
<?php
require_once 'config/config.php';

echo "<h1>Debug Reset Password System</h1>";

// Check database structure
echo "<h2>1. Database Structure</h2>";
try {
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        foreach ($column as $value) {
            echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Check users with reset tokens
echo "<h2>2. Users with Reset Tokens</h2>";
try {
    $stmt = $pdo->query("SELECT id, username, email, reset_token, reset_token_expires FROM users WHERE reset_token IS NOT NULL");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "<p>No users with reset tokens found.</p>";
    } else {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Token</th><th>Expires</th><th>Status</th></tr>";
        foreach ($users as $user) {
            $is_expired = $user['reset_token_expires'] < date('Y-m-d H:i:s');
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['username'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td style='font-family: monospace; font-size: 12px;'>" . substr($user['reset_token'], 0, 20) . "...</td>";
            echo "<td>" . $user['reset_token_expires'] . "</td>";
            echo "<td style='color: " . ($is_expired ? 'red' : 'green') . ";'>" . ($is_expired ? 'EXPIRED' : 'VALID') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Create test token
echo "<h2>3. Create Test Token</h2>";
echo "<form method='POST'>";
echo "<p>Email: <input type='email' name='test_email' value='admin@example.com' required></p>";
echo "<p><button type='submit' name='create_test'>Create Test Token</button></p>";
echo "</form>";

if (isset($_POST['create_test'])) {
    $test_email = $_POST['test_email'];
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$test_email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $test_token = bin2hex(random_bytes(32));
        $test_expires = date('Y-m-d H:i:s', time() + 3600);
        
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
        $result = $stmt->execute([$test_token, $test_expires, $test_email]);
        
        if ($result) {
            echo "<div style='background: lightgreen; padding: 10px; margin: 10px 0;'>";
            echo "<p><strong>Test token created successfully!</strong></p>";
            echo "<p><strong>Token:</strong> <code>$test_token</code></p>";
            echo "<p><strong>Expires:</strong> $test_expires</p>";
            echo "<p><a href='frontend/reset_password.php?token=$test_token' target='_blank'>Test Reset Link</a></p>";
            echo "</div>";
        } else {
            echo "<p style='color: red;'>Failed to create test token!</p>";
        }
    } else {
        echo "<p style='color: red;'>User with email $test_email not found!</p>";
    }
}

echo "<h2>4. Current Time</h2>";
echo "<p>Server time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Timezone: " . date_default_timezone_get() . "</p>";

echo "<h2>5. Quick Links</h2>";
echo "<p><a href='frontend/forgot_password.php'>Forgot Password</a></p>";
echo "<p><a href='fix_reset_tokens.php'>Fix Database Schema</a></p>";
?>
