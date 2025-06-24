<?php
require_once '../config/config.php';

$controller = new AdminController();

// Check if admin is logged in
if (!$controller->isAuthenticated()) {
    header('Location: login.php');
    exit();
}

$message = '';
$messageType = '';

// Handle quiz deletion
if (isset($_GET['delete'])) {
    $quizId = (int)$_GET['delete'];
    $result = $controller->deleteQuiz($quizId);
    
    if ($result['success']) {
        $message = $result['message'];
        $messageType = 'success';
    } else {
        $message = 'Error deleting quiz: ' . $result['message'];
        $messageType = 'danger';
    }
}

// Get all quizzes
$quizzes = $controller->getAllQuizzes();

include '../views/layout/admin_header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Manage Quizzes</h1>
                <a href="quiz_form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Quiz
                </a>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <?php if (empty($quizzes)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-quiz fa-3x text-muted mb-3"></i>
                            <h4>No Quizzes Created</h4>
                            <p class="text-muted">Create your first quiz to get started.</p>
                            <a href="quiz_form.php" class="btn btn-primary">Create Quiz</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Time Limit</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($quizzes as $quiz): ?>
                                        <tr>
                                            <td><?php echo $quiz['id']; ?></td>
                                            <td><?php echo htmlspecialchars($quiz['title']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($quiz['description'], 0, 100)); ?>...</td>
                                            <td><?php echo $quiz['time_limit'] ? $quiz['time_limit'] . ' min' : 'No limit'; ?></td>
                                            <td><?php echo date('M j, Y', strtotime($quiz['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="quiz_form.php?id=<?php echo $quiz['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="question_form.php?quiz_id=<?php echo $quiz['id']; ?>" 
                                                       class="btn btn-sm btn-outline-secondary" title="Manage Questions">
                                                        <i class="fas fa-question"></i>
                                                    </a>
                                                    <a href="../frontend/quiz.php?id=<?php echo $quiz['id']; ?>" 
                                                       class="btn btn-sm btn-outline-success" title="Preview" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="?delete=<?php echo $quiz['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" title="Delete"
                                                       onclick="return confirm('Are you sure you want to delete this quiz?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../views/layout/admin_footer.php'; ?>
