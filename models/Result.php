
<?php
if (!class_exists('Result')) {
class Result {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Save quiz result
     */
    public function saveResult($quizId, $studentName, $email, $score, $totalQuestions, $answers) {
        $percentage = ($totalQuestions > 0) ? round(($score / $totalQuestions) * 100, 2) : 0;
        $answersJson = json_encode($answers);
        
        $sql = "INSERT INTO results (quiz_id, student_name, email, score, total_questions, percentage, answers, completed_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $this->db->execute($sql, [$quizId, $studentName, $email, $score, $totalQuestions, $percentage, $answersJson]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Get result by ID
     */
    public function getResultById($id) {
        $sql = "SELECT r.*, q.title as quiz_title FROM results r 
                JOIN quizzes q ON r.quiz_id = q.id WHERE r.id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Get all results for a quiz
     */
    public function getResultsByQuizId($quizId) {
        $sql = "SELECT * FROM results WHERE quiz_id = ? ORDER BY completed_at DESC";
        return $this->db->fetchAll($sql, [$quizId]);
    }
    
    /**
     * Get recent results
     */
    public function getRecentResults($limit = 10) {
        $sql = "SELECT r.*, q.title as quiz_title FROM results r 
                JOIN quizzes q ON r.quiz_id = q.id 
                ORDER BY r.completed_at DESC LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    /**
     * Delete result
     */
    public function deleteResult($id) {
        $sql = "DELETE FROM results WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
}
}
?>
