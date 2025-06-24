
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug Information</h2>";

// Test database connection
try {
    require_once 'config/config.php';
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test basic query
    global $pdo;
    $result = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch();
    echo "<p>👥 Users in database: " . $result['count'] . "</p>";
    
    $result = $pdo->query("SELECT COUNT(*) as count FROM quizzes")->fetch();
    echo "<p>📝 Quizzes in database: " . $result['count'] . "</p>";
    
    // Test session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    echo "<p>🔐 Session ID: " . session_id() . "</p>";
    echo "<p>📂 Session data: " . print_r($_SESSION, true) . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<a href='frontend/login.php'>Go to Login</a> | ";
echo "<a href='setup_mysql.php'>Setup Page</a>";
?>
