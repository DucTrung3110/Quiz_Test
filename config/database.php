<?php
// Database configuration for InfinityFree MySQL
define('DB_HOST', 'sql307.infinityfree.com');
define('DB_NAME', 'if0_39300491_quizsystem');
define('DB_USER', 'if0_39300491');
define('DB_PASS', 'pCVFEQvzSv');
define('DB_CHARSET', 'utf8mb4');

// Create database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Set timezone
    $pdo->exec("SET time_zone = '+07:00'");

    // Create tables if they don't exist - MySQL version
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role ENUM('admin', 'user') DEFAULT 'user',
            reset_token VARCHAR(64) NULL,
            reset_token_expires TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    // Add missing columns if they don't exist
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(64) NULL");
    } catch (PDOException $e) {
        // Column already exists
    }
    
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN reset_token_expires TIMESTAMP NULL");
    } catch (PDOException $e) {
        // Column already exists
    }

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS quizzes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            time_limit INT NULL,
            status VARCHAR(20) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            quiz_id INT NOT NULL,
            question TEXT NOT NULL,
            option_a VARCHAR(500) NOT NULL,
            option_b VARCHAR(500) NOT NULL,
            option_c VARCHAR(500) NOT NULL,
            option_d VARCHAR(500) NOT NULL,
            correct_answer ENUM('A', 'B', 'C', 'D') NOT NULL,
            order_num INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS results (
            id INT AUTO_INCREMENT PRIMARY KEY,
            quiz_id INT NOT NULL,
            student_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            score INT NOT NULL DEFAULT 0,
            total_questions INT NOT NULL DEFAULT 0,
            percentage DECIMAL(5,2) NOT NULL DEFAULT 0.00,
            answers TEXT,
            completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
        )
    ");

    // Create admin account if not exists
    $adminCheck = $pdo->query("SELECT COUNT(*) FROM users WHERE username = 'admin'")->fetchColumn();
    if ($adminCheck == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, email, password, full_name, role) VALUES 
            ('admin', 'admin@example.com', '$hashedPassword', 'Administrator', 'admin')");
    }

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>