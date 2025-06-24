
<?php
if (!class_exists('Question')) {
class Question {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Get questions by quiz ID
     */
    public function getQuestionsByQuizId($quizId) {
        $sql = "SELECT * FROM questions WHERE quiz_id = ? ORDER BY order_num, id";
        return $this->db->fetchAll($sql, [$quizId]);
    }
    
    /**
     * Get question by ID
     */
    public function getQuestionById($id) {
        $sql = "SELECT * FROM questions WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Create new question
     */
    public function createQuestion($quizId, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum = 0) {
        $sql = "INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $this->db->execute($sql, [$quizId, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Update question
     */
    public function updateQuestion($id, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum = 0) {
        $sql = "UPDATE questions SET question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, 
                correct_answer = ?, order_num = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum, $id]);
    }
    
    /**
     * Delete question
     */
    public function deleteQuestion($id) {
        $sql = "DELETE FROM questions WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    /**
     * Get question count for quiz
     */
    public function getQuestionCount($quizId) {
        $sql = "SELECT COUNT(*) as count FROM questions WHERE quiz_id = ?";
        $result = $this->db->fetch($sql, [$quizId]);
        return $result['count'];
    }
}
}
?>
