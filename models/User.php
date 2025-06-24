<?php
if (!class_exists('User')) {
class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Register new user
     */
    public function register($username, $email, $password, $fullName) {
        // Check if username already exists
        $existingUser = $this->getUserByUsername($username);
        if ($existingUser) {
            throw new Exception("Username already exists");
        }

        // Check if email already exists
        $existingEmail = $this->getUserByEmail($email);
        if ($existingEmail) {
            throw new Exception("Email already exists");
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $sql = "INSERT INTO users (username, email, password, full_name, created_at) VALUES (?, ?, ?, ?, NOW())";
        $this->db->execute($sql, [$username, $email, $hashedPassword, $fullName]);
        return $this->db->lastInsertId();
    }

    /**
     * Authenticate user login
     */
    public function login($username, $password) {
        $user = $this->getUserByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Get user by username
     */
    public function getUserByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        return $this->db->fetch($sql, [$username]);
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        return $this->db->fetch($sql, [$email]);
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Update user profile
     */
    public function updateProfile($id, $email, $fullName) {
        $sql = "UPDATE users SET email = ?, full_name = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$email, $fullName, $id]);
    }

    /**
     * Change password
     */
    public function changePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$hashedPassword, $id]);
    }

    /**
     * Get user's quiz results
     */
    public function getUserResults($userId) {
        $sql = "SELECT r.*, q.title as quiz_title FROM results r 
                JOIN quizzes q ON r.quiz_id = q.id 
                WHERE r.email = (SELECT email FROM users WHERE id = ?) 
                ORDER BY r.completed_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }
}
}
?>