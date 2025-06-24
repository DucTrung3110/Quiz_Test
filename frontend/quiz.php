<?php
require_once '../config/config.php';

$userController = new UserController();
$controller = new QuizController();

// Check if user is logged in
if (!$userController->isAuthenticated()) {
    header('Location: login.php');
    exit();
}

$error = '';
$quizData = null;
$currentUser = $userController->getCurrentUser();

// Get quiz ID from URL
$quizId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($quizId <= 0) {
    $error = 'Invalid quiz ID';
} else {
    try {
        $quizData = $controller->startQuiz($quizId);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $answers = $_POST['answers'] ?? [];
    
    try {
        $result = $controller->submitQuiz($quizId, $currentUser['full_name'], $currentUser['email'], $answers);
        header('Location: result.php?id=' . $result['result_id']);
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include '../views/layout/header.php';
?>

<div class="container mt-4">
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <a href="index.php" class="btn btn-primary">Back to Quiz List</a>
    <?php elseif ($quizData): ?>
        <div class="card">
            <div class="card-header">
                <h2><?php echo htmlspecialchars($quizData['quiz']['title']); ?></h2>
                <p class="mb-0"><?php echo htmlspecialchars($quizData['quiz']['description']); ?></p>
                <?php if ($quizData['quiz']['time_limit']): ?>
                    <div class="mt-2">
                        <span class="badge badge-info">Time Limit: <?php echo $quizData['quiz']['time_limit']; ?> minutes</span>
                        <div id="timer" class="mt-2"></div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form method="POST" id="quizForm">
                    <div class="alert alert-info">
                        <i class="fas fa-user"></i> Làm bài với tài khoản: <strong><?php echo htmlspecialchars($currentUser['full_name']); ?></strong> (<?php echo htmlspecialchars($currentUser['email']); ?>)
                    </div>
                    
                    <hr>
                    
                    <?php foreach ($quizData['questions'] as $index => $question): ?>
                        <div class="mb-4">
                            <h5>Question <?php echo $index + 1; ?></h5>
                            <p><?php echo htmlspecialchars($question['question']); ?></p>
                            
                            <div class="form-check quiz-option">
                                <input class="form-check-input" type="radio" name="answers[<?php echo $question['id']; ?>]" 
                                       id="q<?php echo $question['id']; ?>_a" value="A">
                                <label class="form-check-label quiz-option-label" for="q<?php echo $question['id']; ?>_a">
                                    <span class="option-letter">A</span>
                                    <span class="option-text"><?php echo htmlspecialchars($question['option_a']); ?></span>
                                </label>
                            </div>
                            <div class="form-check quiz-option">
                                <input class="form-check-input" type="radio" name="answers[<?php echo $question['id']; ?>]" 
                                       id="q<?php echo $question['id']; ?>_b" value="B">
                                <label class="form-check-label quiz-option-label" for="q<?php echo $question['id']; ?>_b">
                                    <span class="option-letter">B</span>
                                    <span class="option-text"><?php echo htmlspecialchars($question['option_b']); ?></span>
                                </label>
                            </div>
                            <div class="form-check quiz-option">
                                <input class="form-check-input" type="radio" name="answers[<?php echo $question['id']; ?>]" 
                                       id="q<?php echo $question['id']; ?>_c" value="C">
                                <label class="form-check-label quiz-option-label" for="q<?php echo $question['id']; ?>_c">
                                    <span class="option-letter">C</span>
                                    <span class="option-text"><?php echo htmlspecialchars($question['option_c']); ?></span>
                                </label>
                            </div>
                            <div class="form-check quiz-option">
                                <input class="form-check-input" type="radio" name="answers[<?php echo $question['id']; ?>]" 
                                       id="q<?php echo $question['id']; ?>_d" value="D">
                                <label class="form-check-label quiz-option-label" for="q<?php echo $question['id']; ?>_d">
                                    <span class="option-letter">D</span>
                                    <span class="option-text"><?php echo htmlspecialchars($question['option_d']); ?></span>
                                </label>
                            </div>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg">Submit Quiz</button>
                        <a href="index.php" class="btn btn-secondary btn-lg ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
        <?php if ($quizData['quiz']['time_limit']): ?>
        // Timer functionality
        let timeLimit = <?php echo $quizData['quiz']['time_limit']; ?> * 60; // Convert to seconds
        let timer = setInterval(function() {
            let minutes = Math.floor(timeLimit / 60);
            let seconds = timeLimit % 60;
            
            document.getElementById('timer').innerHTML = 
                '<strong>Time Remaining: ' + minutes + ':' + (seconds < 10 ? '0' : '') + seconds + '</strong>';
            
            if (timeLimit <= 0) {
                clearInterval(timer);
                alert('Time is up! Submitting quiz automatically.');
                document.getElementById('quizForm').submit();
            }
            
            timeLimit--;
        }, 1000);
        <?php endif; ?>
        </script>
    <?php endif; ?>
</div>

<?php include '../views/layout/footer.php'; ?>
