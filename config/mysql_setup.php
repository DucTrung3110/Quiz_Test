
<?php
// File setup database MySQL - chạy một lần duy nhất
require_once 'database.php';

try {
    // Tạo bảng users
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            reset_token VARCHAR(255) NULL,
            reset_token_expires DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Tạo bảng quizzes
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS quizzes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            time_limit INT NULL COMMENT 'Time limit in minutes',
            status ENUM('active', 'inactive', 'deleted') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Tạo bảng questions
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
            FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
            INDEX idx_quiz_id (quiz_id),
            INDEX idx_order (quiz_id, order_num)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Tạo bảng results
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS results (
            id INT AUTO_INCREMENT PRIMARY KEY,
            quiz_id INT NOT NULL,
            student_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            score INT NOT NULL,
            total_questions INT NOT NULL,
            percentage DECIMAL(5,2) NOT NULL,
            answers TEXT NOT NULL COMMENT 'Store detailed answers in JSON format',
            completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
            INDEX idx_quiz_id (quiz_id),
            INDEX idx_email (email),
            INDEX idx_completed_at (completed_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Insert sample data
    $count = $pdo->query("SELECT COUNT(*) as count FROM quizzes")->fetch();
    if ($count['count'] == 0) {
        // Insert sample quizzes
        $pdo->exec("INSERT INTO quizzes (title, description, time_limit) VALUES 
            ('General Knowledge Quiz', 'Test your general knowledge with this basic quiz covering various topics.', 10)");
        $quiz1_id = $pdo->lastInsertId();
        
        $pdo->exec("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
            ($quiz1_id, 'What is the capital of France?', 'London', 'Berlin', 'Paris', 'Madrid', 'C', 1),
            ($quiz1_id, 'Which planet is known as the Red Planet?', 'Venus', 'Mars', 'Jupiter', 'Saturn', 'B', 2),
            ($quiz1_id, 'Who painted the Mona Lisa?', 'Vincent van Gogh', 'Pablo Picasso', 'Leonardo da Vinci', 'Michelangelo', 'C', 3),
            ($quiz1_id, 'What is the largest ocean on Earth?', 'Atlantic Ocean', 'Indian Ocean', 'Arctic Ocean', 'Pacific Ocean', 'D', 4),
            ($quiz1_id, 'Which element has the chemical symbol \"O\"?', 'Gold', 'Oxygen', 'Silver', 'Iron', 'B', 5)");
        
        $pdo->exec("INSERT INTO quizzes (title, description, time_limit) VALUES 
            ('Programming Basics', 'Test your knowledge of basic programming concepts and terminology.', 15)");
        $quiz2_id = $pdo->lastInsertId();
        
        $pdo->exec("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
            ($quiz2_id, 'What does HTML stand for?', 'High Tech Modern Language', 'HyperText Markup Language', 'Home Tool Markup Language', 'Hyperlink and Text Markup Language', 'B', 1),
            ($quiz2_id, 'Which of the following is NOT a programming language?', 'Python', 'Java', 'HTML', 'C++', 'C', 2),
            ($quiz2_id, 'What is the correct way to create a comment in PHP?', '# This is a comment', '// This is a comment', '/* This is a comment */', 'All of the above', 'D', 3),
            ($quiz2_id, 'Which symbol is used to terminate a statement in PHP?', 'Semicolon (;)', 'Colon (:)', 'Period (.)', 'Comma (,)', 'A', 4)");
        
        // Thêm các quiz khác tương tự...
        $pdo->exec("INSERT INTO quizzes (title, description, time_limit) VALUES 
            ('Basic Mathematics', 'Test your basic mathematical skills with these simple problems.', 12),
            ('Basic Science', 'Test your knowledge of basic science concepts from physics, chemistry, and biology.', 15),
            ('World History', 'Test your knowledge of important historical events and figures.', 18),
            ('World Geography', 'Test your knowledge of countries, capitals, and geographical features.', 15),
            ('Technology & Computers', 'Test your knowledge of modern technology and computer science.', 20)");
        
        // Create demo user account
        $hashedPassword = password_hash('demo123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, email, password, full_name, created_at) VALUES 
            ('demo', 'demo@example.com', '$hashedPassword', 'Người dùng Demo', NOW())");
    }
    
    echo "Database setup completed successfully!";
    
} catch(PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>
