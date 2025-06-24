<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Prevent duplicate includes
    if (!defined('CONFIG_LOADED')) {
        require_once '../config/config.php';
        define('CONFIG_LOADED', true);
    }

    $userController = new UserController();
    $controller = new QuizController();

    // Check if user is logged in
    if (!$userController->isAuthenticated()) {
        header('Location: login.php');
        exit();
    }

    $quizzes = $controller->index();
    $currentUser = $userController->getCurrentUser();

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

include '../views/layout/header.php';
?>

<div class="container mt-4">
    <div class="jumbotron">
        <h1 class="display-4">Chào mừng <?php echo htmlspecialchars($currentUser['full_name']); ?>!</h1>
        <p class="lead">Hãy kiểm tra kiến thức của bạn với các bài quiz thú vị!</p>
        <hr class="my-4">
        <p>Chọn một quiz bên dưới để bắt đầu.</p>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h2>Available Quizzes</h2>

            <?php if (empty($quizzes)): ?>
                <div class="alert alert-info">
                    <h4>No Quizzes Available</h4>
                    <p>There are currently no quizzes available. Please check back later.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($quizzes as $quiz): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($quiz['title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($quiz['description']); ?></p>
                                    <?php if ($quiz['time_limit']): ?>
                                        <p class="text-muted">
                                            <i class="fas fa-clock"></i> Time Limit: <?php echo $quiz['time_limit']; ?> minutes
                                        </p>
                                    <?php endif; ?>
                                    <a href="quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-primary">Start Quiz</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../views/layout/footer.php'; ?>