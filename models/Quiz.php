
<?php
if (!class_exists('Quiz')) {
class Quiz {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Get all quizzes
     */
    public function getAllQuizzes() {
        $sql = "SELECT * FROM quizzes WHERE status = 'active' ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get quiz by ID
     */
    public function getQuizById($id) {
        $sql = "SELECT * FROM quizzes WHERE id = ? AND status = 'active'";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Create new quiz
     */
    public function createQuiz($title, $description, $time_limit = null) {
        $sql = "INSERT INTO quizzes (title, description, time_limit, created_at) VALUES (?, ?, ?, NOW())";
        $this->db->execute($sql, [$title, $description, $time_limit]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Update quiz
     */
    public function updateQuiz($id, $title, $description, $time_limit = null) {
        $sql = "UPDATE quizzes SET title = ?, description = ?, time_limit = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$title, $description, $time_limit, $id]);
    }
    
    /**
     * Delete quiz (soft delete)
     */
    public function deleteQuiz($id) {
        $sql = "UPDATE quizzes SET status = 'deleted', updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    /**
     * Get quiz statistics
     */
    public function getQuizStats($quizId) {
        $sql = "SELECT COUNT(*) as total_attempts, AVG(score) as avg_score FROM results WHERE quiz_id = ?";
        return $this->db->fetch($sql, [$quizId]);
    }
}
}
?>
