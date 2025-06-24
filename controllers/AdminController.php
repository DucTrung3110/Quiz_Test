
<?php
class AdminController {
    private $quizModel;
    private $questionModel;
    private $resultModel;
    
    public function __construct() {
        $this->quizModel = new Quiz();
        $this->questionModel = new Question();
        $this->resultModel = new Result();
    }
    
    /**
     * Admin login check
     */
    public function login($username, $password) {
        if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            return true;
        }
        return false;
    }
    
    /**
     * Admin logout
     */
    public function logout() {
        // Chỉ xóa session của admin, không ảnh hưởng đến user
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_username']);
        return true;
    }
    
    /**
     * Check if admin is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    /**
     * Check if admin is authenticated (alias for isLoggedIn)
     */
    public function isAuthenticated() {
        return $this->isLoggedIn();
    }
    
    /**
     * Get all quizzes (including inactive)
     */
    public function getAllQuizzes() {
        try {
            global $pdo;
            $stmt = $pdo->query("SELECT *, 
                (SELECT COUNT(*) FROM questions WHERE quiz_id = quizzes.id) as question_count,
                (SELECT COUNT(*) FROM results WHERE quiz_id = quizzes.id) as attempt_count
                FROM quizzes WHERE status != 'deleted' ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error in AdminController::getAllQuizzes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create new quiz
     */
    public function createQuiz($title, $description, $timeLimit = null) {
        try {
            if (empty($title)) {
                throw new Exception("Quiz title is required");
            }
            
            $quizId = $this->quizModel->createQuiz($title, $description, $timeLimit);
            
            return [
                'success' => true,
                'quiz_id' => $quizId,
                'message' => 'Quiz created successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Update quiz
     */
    public function updateQuiz($id, $title, $description, $timeLimit = null) {
        try {
            if (empty($title)) {
                throw new Exception("Quiz title is required");
            }
            
            $result = $this->quizModel->updateQuiz($id, $title, $description, $timeLimit);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Quiz updated successfully'
                ];
            } else {
                throw new Exception("Failed to update quiz");
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Delete quiz
     */
    public function deleteQuiz($id) {
        try {
            global $pdo;
            
            // First delete associated questions
            $stmt = $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?");
            $stmt->execute([$id]);
            
            // Delete associated results  
            $stmt = $pdo->prepare("DELETE FROM results WHERE quiz_id = ?");
            $stmt->execute([$id]);
            
            // Finally delete the quiz
            $stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Quiz deleted successfully'
                ];
            } else {
                throw new Exception("Failed to delete quiz");
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get quiz with questions for editing
     */
    public function getQuizForEdit($id) {
        try {
            $quiz = $this->quizModel->getQuizById($id);
            if (!$quiz) {
                throw new Exception("Quiz not found");
            }
            
            $questions = $this->questionModel->getQuestionsByQuizId($id);
            
            return [
                'quiz' => $quiz,
                'questions' => $questions
            ];
            
        } catch (Exception $e) {
            error_log("Error in AdminController::getQuizForEdit: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create new question
     */
    public function createQuestion($quizId, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum = 0) {
        try {
            if (empty($question) || empty($optionA) || empty($optionB) || empty($optionC) || empty($optionD)) {
                throw new Exception("All question fields are required");
            }
            
            if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
                throw new Exception("Correct answer must be A, B, C, or D");
            }
            
            $questionId = $this->questionModel->createQuestion(
                $quizId, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum
            );
            
            return [
                'success' => true,
                'question_id' => $questionId,
                'message' => 'Question created successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Update question
     */
    public function updateQuestion($id, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum = 0) {
        try {
            if (empty($question) || empty($optionA) || empty($optionB) || empty($optionC) || empty($optionD)) {
                throw new Exception("All question fields are required");
            }
            
            if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
                throw new Exception("Correct answer must be A, B, C, or D");
            }
            
            $result = $this->questionModel->updateQuestion(
                $id, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum
            );
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Question updated successfully'
                ];
            } else {
                throw new Exception("Failed to update question");
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Delete question
     */
    public function deleteQuestion($id) {
        try {
            $result = $this->questionModel->deleteQuestion($id);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Question deleted successfully'
                ];
            } else {
                throw new Exception("Failed to delete question");
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get quiz results for admin view
     */
    public function getQuizResults($quizId) {
        try {
            return $this->resultModel->getResultsByQuizId($quizId);
        } catch (Exception $e) {
            error_log("Error in AdminController::getQuizResults: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get recent results for dashboard
     */
    public function getRecentResults($limit = 10) {
        try {
            return $this->resultModel->getRecentResults($limit);
        } catch (Exception $e) {
            error_log("Error in AdminController::getRecentResults: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Delete result
     */
    public function deleteResult($id) {
        try {
            $result = $this->resultModel->deleteResult($id);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Result deleted successfully'
                ];
            } else {
                throw new Exception("Failed to delete result");
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get quiz questions for admin view
     */
    public function getQuizQuestions($quizId) {
        try {
            return $this->questionModel->getQuestionsByQuizId($quizId);
        } catch (Exception $e) {
            error_log("Error in AdminController::getQuizQuestions: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get dashboard data for admin panel
     */
    public function getDashboardData() {
        try {
            global $pdo;
            
            $data = [];
            
            // Total quizzes (including all status)
            $stmt = $pdo->query("SELECT COUNT(*) FROM quizzes WHERE status != 'deleted'");
            $data['total_quizzes'] = $stmt->fetchColumn();
            
            // Recent results - simplified query to avoid JOIN issues
            $stmt = $pdo->prepare("
                SELECT r.id, r.student_name, r.email, r.score, r.total_questions, 
                       r.percentage, r.completed_at, r.quiz_id,
                       q.title as quiz_title
                FROM results r 
                INNER JOIN quizzes q ON r.quiz_id = q.id 
                WHERE q.status != 'deleted'
                ORDER BY r.completed_at DESC 
                LIMIT 10
            ");
            $stmt->execute();
            $recent_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Ensure student_name is not empty
            foreach ($recent_results as &$result) {
                if (empty($result['student_name'])) {
                    $result['student_name'] = 'Guest User';
                }
            }
            
            // Debug logging
            error_log("Dashboard - Total quizzes: " . $data['total_quizzes']);
            error_log("Dashboard - Recent results count: " . count($recent_results));
            
            $data['recent_results'] = $recent_results;
            
            return $data;
            
        } catch (Exception $e) {
            error_log("Error in AdminController::getDashboardData: " . $e->getMessage());
            
            // Return safe fallback data
            return [
                'total_quizzes' => 0,
                'recent_results' => []
            ];
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats() {
        try {
            global $pdo;
            
            $stats = [];
            
            // Total quizzes
            $stmt = $pdo->query("SELECT COUNT(*) FROM quizzes WHERE status = 'active'");
            $stats['total_quizzes'] = $stmt->fetchColumn();
            
            // Total questions
            $stmt = $pdo->query("SELECT COUNT(*) FROM questions");
            $stats['total_questions'] = $stmt->fetchColumn();
            
            // Total results
            $stmt = $pdo->query("SELECT COUNT(*) FROM results");
            $stats['total_results'] = $stmt->fetchColumn();
            
            // Average score
            $stmt = $pdo->query("SELECT AVG(percentage) FROM results WHERE percentage IS NOT NULL");
            $average = $stmt->fetchColumn();
            $stats['avg_score'] = $average ? round($average, 2) : 0;
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Error in AdminController::getDashboardStats: " . $e->getMessage());
            return [
                'total_quizzes' => 0,
                'total_questions' => 0,
                'total_results' => 0,
                'avg_score' => 0
            ];
        }
    }
}
?>
