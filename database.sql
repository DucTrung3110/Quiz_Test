
-- MySQL Database Schema for Quiz Test System
-- Run this on your MySQL database

-- Create database (if needed)
-- CREATE DATABASE IF NOT EXISTS quiz_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE quiz_system;

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    reset_token VARCHAR(255) NULL,
    reset_token_expires DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: quizzes
CREATE TABLE IF NOT EXISTS quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    time_limit INT NULL,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: questions
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
    INDEX idx_order_num (order_num)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: results
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
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_quiz_id (quiz_id),
    INDEX idx_email (email),
    INDEX idx_completed_at (completed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user
INSERT IGNORE INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- Insert sample quizzes and questions
INSERT IGNORE INTO quizzes (id, title, description, time_limit) VALUES 
(1, 'General Knowledge', 'Test your general knowledge with these questions covering various topics.', 10),
(2, 'Programming Basics', 'Test your knowledge of basic programming concepts and terminology.', 15),
(3, 'Basic Mathematics', 'Test your basic mathematical skills with these simple problems.', 12),
(4, 'Basic Science', 'Test your knowledge of basic science concepts from physics, chemistry, and biology.', 15),
(5, 'World History', 'Test your knowledge of important historical events and figures.', 18),
(6, 'World Geography', 'Test your knowledge of countries, capitals, and geographical features.', 15),
(7, 'Technology & Computers', 'Test your knowledge of modern technology and computer science.', 20);

-- Insert sample questions for General Knowledge quiz
INSERT IGNORE INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
(1, 'What is the capital of France?', 'London', 'Berlin', 'Paris', 'Madrid', 'C', 1),
(1, 'Which planet is known as the Red Planet?', 'Venus', 'Mars', 'Jupiter', 'Saturn', 'B', 2),
(1, 'Who painted the Mona Lisa?', 'Vincent van Gogh', 'Pablo Picasso', 'Leonardo da Vinci', 'Michelangelo', 'C', 3),
(1, 'What is the largest ocean on Earth?', 'Atlantic Ocean', 'Indian Ocean', 'Arctic Ocean', 'Pacific Ocean', 'D', 4),
(1, 'Which element has the chemical symbol "O"?', 'Gold', 'Oxygen', 'Silver', 'Iron', 'B', 5);

-- Insert sample questions for Programming Basics quiz
INSERT IGNORE INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
(2, 'What does HTML stand for?', 'High Tech Modern Language', 'HyperText Markup Language', 'Home Tool Markup Language', 'Hyperlink and Text Markup Language', 'B', 1),
(2, 'Which of the following is NOT a programming language?', 'Python', 'Java', 'HTML', 'C++', 'C', 2),
(2, 'What is the correct way to create a comment in PHP?', '# This is a comment', '// This is a comment', '/* This is a comment */', 'All of the above', 'D', 3),
(2, 'Which symbol is used to terminate a statement in PHP?', 'Semicolon (;)', 'Colon (:)', 'Period (.)', 'Comma (,)', 'A', 4);

-- Insert sample questions for Basic Mathematics quiz
INSERT IGNORE INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
(3, 'What is 15 + 28?', '42', '43', '44', '45', 'B', 1),
(3, 'What is 144 ÷ 12?', '11', '12', '13', '14', 'B', 2),
(3, 'What is 7 × 8?', '54', '55', '56', '57', 'C', 3),
(3, 'What is the square root of 64?', '6', '7', '8', '9', 'C', 4);
