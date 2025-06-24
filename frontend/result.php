<?php
require_once '../config/config.php';

$controller = new QuizController();
$error = '';
$result = null;

// Get result ID from URL
$resultId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($resultId <= 0) {
    $error = 'Invalid result ID';
} else {
    try {
        $result = $controller->getResult($resultId);
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
    <?php elseif ($result): ?>
        <div class="card">
            <div class="card-header text-center">
                <h2>Quiz Results</h2>
                <h4><?php echo htmlspecialchars($result['quiz_title']); ?></h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Student Information</h5>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($result['student_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($result['email']); ?></p>
                        <p><strong>Completed:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($result['completed_at'])); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Score Summary</h5>
                        <div class="text-center">
                            <div class="display-4 <?php echo $result['percentage'] >= 70 ? 'text-success' : ($result['percentage'] >= 50 ? 'text-warning' : 'text-danger'); ?>">
                                <?php echo $result['percentage']; ?>%
                            </div>
                            <p class="lead">
                                <?php echo $result['score']; ?> out of <?php echo $result['total_questions']; ?> correct
                            </p>
                            <?php if ($result['percentage'] >= 70): ?>
                                <div class="badge badge-success badge-lg">Excellent!</div>
                            <?php elseif ($result['percentage'] >= 50): ?>
                                <div class="badge badge-warning badge-lg">Good Job!</div>
                            <?php else: ?>
                                <div class="badge badge-danger badge-lg">Need Improvement</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <h5>Detailed Results</h5>
                <div class="accordion" id="resultAccordion">
                    <?php foreach ($result['answers'] as $index => $answer): ?>
                        <div class="card">
                            <div class="card-header" id="heading<?php echo $index; ?>">
                                <h6 class="mb-0">
                                    <button class="btn btn-link <?php echo $answer['is_correct'] ? 'text-success' : 'text-danger'; ?>" 
                                            type="button" data-toggle="collapse" 
                                            data-target="#collapse<?php echo $index; ?>" 
                                            aria-expanded="false" 
                                            aria-controls="collapse<?php echo $index; ?>">
                                        Question <?php echo $index + 1; ?>
                                        <?php if ($answer['is_correct']): ?>
                                            <i class="fas fa-check text-success"></i>
                                        <?php else: ?>
                                            <i class="fas fa-times text-danger"></i>
                                        <?php endif; ?>
                                    </button>
                                </h6>
                            </div>
                            <div id="collapse<?php echo $index; ?>" class="collapse" 
                                 aria-labelledby="heading<?php echo $index; ?>" 
                                 data-parent="#resultAccordion">
                                <div class="card-body">
                                    <p><strong>Question:</strong> <?php echo htmlspecialchars($answer['question']); ?></p>
                                    <p><strong>Your Answer:</strong> 
                                        <span class="<?php echo $answer['is_correct'] ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo $answer['user_answer'] ?: 'No answer selected'; ?>
                                        </span>
                                    </p>
                                    <p><strong>Correct Answer:</strong> 
                                        <span class="text-success"><?php echo $answer['correct_answer']; ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-primary btn-lg">Take Another Quiz</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../views/layout/footer.php'; ?>
