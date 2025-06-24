
<?php
require_once 'config/config.php';

echo "<h1>Reset Token Test & Debug</h1>";

// Test 1: Check current database tokens
echo "<h2>1. Current Tokens in Database</h2>";
try {
    $stmt = $pdo->query("SELECT id, email, reset_token, reset_token_expires, 
                         UNIX_TIMESTAMP(reset_token_expires) as expires_unix,
                         UNIX_TIMESTAMP(NOW()) as current_unix,
                         (UNIX_TIMESTAMP(reset_token_expires) - UNIX_TIMESTAMP(NOW())) as time_diff
                         FROM users WHERE reset_token IS NOT NULL");
    $tokens = $stmt->fetchAll();
    
    if (empty($tokens)) {
        echo "<p>No tokens found in database.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Email</th><th>Token (truncated)</th><th>Expires</th><th>Expires Unix</th><th>Current Unix</th><th>Time Diff (sec)</th><th>Status</th></tr>";
        foreach ($tokens as $token) {
            $status = $token['time_diff'] > 0 ? 'VALID' : 'EXPIRED';
            $color = $status === 'VALID' ? 'green' : 'red';
            echo "<tr>";
            echo "<td>" . $token['id'] . "</td>";
            echo "<td>" . $token['email'] . "</td>";
            echo "<td>" . substr($token['reset_token'], 0, 20) . "...</td>";
            echo "<td>" . $token['reset_token_expires'] . "</td>";
            echo "<td>" . $token['expires_unix'] . "</td>";
            echo "<td>" . $token['current_unix'] . "</td>";
            echo "<td>" . $token['time_diff'] . "</td>";
            echo "<td style='color: $color;'><strong>$status</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Test 2: Create new token for admin
echo "<h2>2. Create New Token for Admin</h2>";
try {
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE username = 'admin' OR email = 'admin@example.com' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        $new_token = bin2hex(random_bytes(32));
        $expires_timestamp = time() + 3600;
        $expires = date('Y-m-d H:i:s', $expires_timestamp);
        
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
        $result = $stmt->execute([$new_token, $expires, $admin['id']]);
        
        if ($result) {
            echo "<div style='background: lightgreen; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
            echo "<h3>✅ New Token Created Successfully!</h3>";
            echo "<p><strong>Email:</strong> " . $admin['email'] . "</p>";
            echo "<p><strong>Token:</strong> <code style='font-family: monospace; background: white; padding: 2px 4px;'>$new_token</code></p>";
            echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . " (Unix: " . time() . ")</p>";
            echo "<p><strong>Expires:</strong> $expires (Unix: $expires_timestamp)</p>";
            echo "<p><strong>Time Difference:</strong> " . ($expires_timestamp - time()) . " seconds (" . round(($expires_timestamp - time())/60) . " minutes)</p>";
            echo "<p><a href='frontend/reset_password.php?token=$new_token' target='_blank' style='background: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>🔗 Test Reset Link</a></p>";
            echo "</div>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create token</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Admin user not found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Test 3: Database timezone info
echo "<h2>3. Database Timezone Information</h2>";
try {
    $stmt = $pdo->query("SELECT NOW() as db_time, UNIX_TIMESTAMP(NOW()) as db_unix");
    $db_info = $stmt->fetch();
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Source</th><th>Time</th><th>Unix Timestamp</th></tr>";
    echo "<tr><td>Database NOW()</td><td>" . $db_info['db_time'] . "</td><td>" . $db_info['db_unix'] . "</td></tr>";
    echo "<tr><td>PHP time()</td><td>" . date('Y-m-d H:i:s') . "</td><td>" . time() . "</td></tr>";
    echo "<tr><td>PHP timezone</td><td>" . date_default_timezone_get() . "</td><td>-</td></tr>";
    echo "</table>";
    
    $time_diff = $db_info['db_unix'] - time();
    if (abs($time_diff) > 5) {
        echo "<p style='color: orange;'>⚠️ Warning: Database and PHP time differ by $time_diff seconds</p>";
    } else {
        echo "<p style='color: green;'>✅ Database and PHP time are synchronized</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<h2>4. Quick Links</h2>";
echo "<p><a href='frontend/forgot_password.php'>🔄 Create New Token (Forgot Password)</a></p>";
echo "<p><a href='debug_reset.php'>🔍 Debug Reset System</a></p>";
?>
