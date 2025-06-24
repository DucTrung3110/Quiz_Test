
<?php
class QuizController {
    private $quizModel;
    private $questionModel;
    private $resultModel;
    
    public function __construct() {
        $this->quizModel = new Quiz();
        $this->questionModel = new Question();
        $this->resultModel = new Result();
    }
    
    /**
     * Display all active quizzes
     */
    public function index() {
        try {
            $quizzes = $this->quizModel->getAllQuizzes();
            return $quizzes;
        } catch (Exception $e) {
            error_log("Error in QuizController::index: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Start quiz - get quiz details with questions for taking quiz
     */
    public function startQuiz($quizId) {
        try {
            $quiz = $this->quizModel->getQuizById($quizId);
            if (!$quiz) {
                throw new Exception("Quiz not found");
            }
            
            $questions = $this->questionModel->getQuestionsByQuizId($quizId);
            if (empty($questions)) {
                throw new Exception("No questions found for this quiz");
            }
            
            return [
                'quiz' => $quiz,
                'questions' => $questions
            ];
        } catch (Exception $e) {
            error_log("Error in QuizController::startQuiz: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Display quiz details with questions
     */
    public function show($quizId) {
        try {
            $quiz = $this->quizModel->getQuizById($quizId);
            if (!$quiz) {
                throw new Exception("Quiz not found");
            }
            
            $questions = $this->questionModel->getQuestionsByQuizId($quizId);
            
            return [
                'quiz' => $quiz,
                'questions' => $questions
            ];
        } catch (Exception $e) {
            error_log("Error in QuizController::show: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Submit quiz and calculate score (alias for submit method)
     */
    public function submitQuiz($quizId, $studentName, $email, $answers) {
        return $this->submit($quizId, $studentName, $email, $answers);
    }
    
    /**
     * Submit quiz and calculate score
     */
    public function submit($quizId, $studentName, $email, $answers) {
        try {
            // Get quiz questions
            $questions = $this->questionModel->getQuestionsByQuizId($quizId);
            if (empty($questions)) {
                throw new Exception("No questions found for this quiz");
            }
            
            // Calculate score
            $score = 0;
            $totalQuestions = count($questions);
            $detailedAnswers = [];
            
            foreach ($questions as $question) {
                $questionId = $question['id'];
                $userAnswer = isset($answers[$questionId]) ? $answers[$questionId] : '';
                $correctAnswer = $question['correct_answer'];
                $isCorrect = (strtoupper($userAnswer) === strtoupper($correctAnswer));
                
                if ($isCorrect) {
                    $score++;
                }
                
                $detailedAnswers[] = [
                    'question_id' => $questionId,
                    'question' => $question['question'],
                    'user_answer' => $userAnswer,
                    'correct_answer' => $correctAnswer,
                    'is_correct' => $isCorrect,
                    'options' => [
                        'A' => $question['option_a'],
                        'B' => $question['option_b'],
                        'C' => $question['option_c'],
                        'D' => $question['option_d']
                    ]
                ];
            }
            
            // Save result to database
            $resultId = $this->resultModel->saveResult(
                $quizId, 
                $studentName, 
                $email, 
                $score, 
                $totalQuestions, 
                $detailedAnswers
            );
            
            return [
                'result_id' => $resultId,
                'score' => $score,
                'total_questions' => $totalQuestions,
                'percentage' => round(($score / $totalQuestions) * 100, 2),
                'detailed_answers' => $detailedAnswers
            ];
            
        } catch (Exception $e) {
            error_log("Error in QuizController::submit: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get quiz result by ID
     */
    public function getResult($resultId) {
        try {
            $result = $this->resultModel->getResultById($resultId);
            if (!$result) {
                throw new Exception("Result not found");
            }
            
            // Decode answers JSON
            $result['answers'] = json_decode($result['answers'], true);
            
            return $result;
        } catch (Exception $e) {
            error_log("Error in QuizController::getResult: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get quiz statistics
     */
    public function getQuizStats($quizId) {
        try {
            return $this->quizModel->getQuizStats($quizId);
        } catch (Exception $e) {
            error_log("Error in QuizController::getQuizStats: " . $e->getMessage());
            return false;
        }
    }
}
?>
