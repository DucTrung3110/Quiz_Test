<?php
require_once '../config/config.php';

$controller = new AdminController();

// Check if admin is logged in
if (!$controller->isAuthenticated()) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';
$quiz = null;
$questions = [];
$editQuestion = null;

// Get quiz ID
$quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
$editQuestionId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;

if ($quizId <= 0) {
    header('Location: quiz_list.php');
    exit();
}

// Get quiz details
$quizModel = new Quiz();
$quiz = $quizModel->getQuizById($quizId);
if (!$quiz) {
    header('Location: quiz_list.php');
    exit();
}

// Get existing questions
$questions = $controller->getQuizQuestions($quizId);

// Handle question deletion
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    try {
        $controller->deleteQuestion($deleteId);
        header('Location: question_form.php?quiz_id=' . $quizId);
        exit();
    } catch (Exception $e) {
        $error = 'Error deleting question: ' . $e->getMessage();
    }
}

// Get question for editing
if ($editQuestionId > 0) {
    $questionModel = new Question();
    $editQuestion = $questionModel->getQuestionById($editQuestionId);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question'] ?? '');
    $optionA = trim($_POST['option_a'] ?? '');
    $optionB = trim($_POST['option_b'] ?? '');
    $optionC = trim($_POST['option_c'] ?? '');
    $optionD = trim($_POST['option_d'] ?? '');
    $correctAnswer = $_POST['correct_answer'] ?? '';
    $orderNum = (int)($_POST['order_num'] ?? 0);
    
    try {
        if ($editQuestion) {
            $controller->updateQuestion($editQuestionId, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum);
            $success = 'Question updated successfully';
            header('Location: question_form.php?quiz_id=' . $quizId);
            exit();
        } else {
            $controller->createQuestion($quizId, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum);
            $success = 'Question added successfully';
            // Refresh questions list
            $questions = $controller->getQuizQuestions($quizId);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include '../views/layout/admin_header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>Manage Questions</h1>
                    <p class="text-muted">Quiz: <?php echo htmlspecialchars($quiz['title']); ?></p>
                </div>
                <a href="quiz_list.php" class="btn btn-secondary">Back to Quizzes</a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><?php echo $editQuestion ? 'Edit Question' : 'Add New Question'; ?></h5>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="question" class="form-label">Question *</label>
                            <textarea class="form-control" id="question" name="question" rows="3" required><?php echo htmlspecialchars($editQuestion['question'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="option_a" class="form-label">Option A *</label>
                            <input type="text" class="form-control" id="option_a" name="option_a" 
                                   value="<?php echo htmlspecialchars($editQuestion['option_a'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="option_b" class="form-label">Option B *</label>
                            <input type="text" class="form-control" id="option_b" name="option_b" 
                                   value="<?php echo htmlspecialchars($editQuestion['option_b'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="option_c" class="form-label">Option C *</label>
                            <input type="text" class="form-control" id="option_c" name="option_c" 
                                   value="<?php echo htmlspecialchars($editQuestion['option_c'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="option_d" class="form-label">Option D *</label>
                            <input type="text" class="form-control" id="option_d" name="option_d" 
                                   value="<?php echo htmlspecialchars($editQuestion['option_d'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="correct_answer" class="form-label">Correct Answer *</label>
                            <select class="form-control" id="correct_answer" name="correct_answer" required>
                                <option value="">Select correct answer</option>
                                <option value="A" <?php echo ($editQuestion['correct_answer'] ?? '') === 'A' ? 'selected' : ''; ?>>A</option>
                                <option value="B" <?php echo ($editQuestion['correct_answer'] ?? '') === 'B' ? 'selected' : ''; ?>>B</option>
                                <option value="C" <?php echo ($editQuestion['correct_answer'] ?? '') === 'C' ? 'selected' : ''; ?>>C</option>
                                <option value="D" <?php echo ($editQuestion['correct_answer'] ?? '') === 'D' ? 'selected' : ''; ?>>D</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="order_num" class="form-label">Order Number</label>
                            <input type="number" class="form-control" id="order_num" name="order_num" 
                                   value="<?php echo $editQuestion['order_num'] ?? count($questions) + 1; ?>" min="0">
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $editQuestion ? 'Update Question' : 'Add Question'; ?>
                            </button>
                            <?php if ($editQuestion): ?>
                                <a href="question_form.php?quiz_id=<?php echo $quizId; ?>" class="btn btn-secondary">Cancel Edit</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Existing Questions (<?php echo count($questions); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($questions)): ?>
                        <p class="text-muted">No questions added yet. Add your first question to get started.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($questions as $index => $q): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6>Question <?php echo $index + 1; ?></h6>
                                            <p class="mb-1"><?php echo htmlspecialchars(substr($q['question'], 0, 100)); ?>...</p>
                                            <small class="text-muted">Correct Answer: <?php echo $q['correct_answer']; ?></small>
                                        </div>
                                        <div class="btn-group">
                                            <a href="question_form.php?quiz_id=<?php echo $quizId; ?>&edit=<?php echo $q['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="question_form.php?quiz_id=<?php echo $quizId; ?>&delete=<?php echo $q['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Are you sure you want to delete this question?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-3">
                            <a href="../frontend/quiz.php?id=<?php echo $quizId; ?>" class="btn btn-success" target="_blank">
                                <i class="fas fa-eye"></i> Preview Quiz
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../views/layout/admin_footer.php'; ?>
