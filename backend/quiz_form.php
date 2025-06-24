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
$isEdit = false;

// Check if editing existing quiz
if (isset($_GET['id'])) {
    $quizId = (int)$_GET['id'];
    $quizModel = new Quiz();
    $quiz = $quizModel->getQuizById($quizId);
    if ($quiz) {
        $isEdit = true;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $timeLimit = !empty($_POST['time_limit']) ? (int)$_POST['time_limit'] : null;
    
    try {
        if ($isEdit) {
            $controller->updateQuiz($quizId, $title, $description, $timeLimit);
            $success = 'Quiz updated successfully';
        } else {
            $newQuizId = $controller->createQuiz($title, $description, $timeLimit);
            $success = 'Quiz created successfully';
            header('Location: question_form.php?quiz_id=' . $newQuizId);
            exit();
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include '../views/layout/admin_header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2><?php echo $isEdit ? 'Edit Quiz' : 'Create New Quiz'; ?></h2>
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
                            <label for="title" class="form-label">Quiz Title *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($quiz['title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($quiz['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                            <input type="number" class="form-control" id="time_limit" name="time_limit" 
                                   value="<?php echo $quiz['time_limit'] ?? ''; ?>" min="1">
                            <div class="form-text">Leave empty for no time limit</div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $isEdit ? 'Update Quiz' : 'Create Quiz'; ?>
                            </button>
                            <a href="quiz_list.php" class="btn btn-secondary">Cancel</a>
                            <?php if ($isEdit): ?>
                                <a href="question_form.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn btn-success">
                                    Manage Questions
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Instructions</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Enter a descriptive title for your quiz</li>
                        <li><i class="fas fa-check text-success"></i> Add a clear description explaining the quiz topic</li>
                        <li><i class="fas fa-check text-success"></i> Set a time limit if needed (optional)</li>
                        <li><i class="fas fa-check text-success"></i> After creating, add questions to complete the quiz</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../views/layout/admin_footer.php'; ?>
