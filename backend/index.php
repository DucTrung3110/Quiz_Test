<?php
require_once '../config/config.php';

$controller = new AdminController();

// Check if admin is logged in
if (!$controller->isAuthenticated()) {
    header('Location: login.php');
    exit();
}

// Get dashboard data with error handling
try {
    $dashboardData = $controller->getDashboardData();
    $stats = $controller->getDashboardStats();

    // Debug output for troubleshooting
    error_log("Admin Dashboard - Dashboard data loaded: " . json_encode($dashboardData));

} catch (Exception $e) {
    error_log("Error loading admin dashboard: " . $e->getMessage());
    $dashboardData = ['total_quizzes' => 0, 'recent_results' => []];
    $stats = ['total_quizzes' => 0, 'total_questions' => 0, 'total_results' => 0, 'avg_score' => 0];
}

include '../views/layout/admin_header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1>Admin Dashboard</h1>
            <p class="lead">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $dashboardData['total_quizzes']; ?></h4>
                            <p>Total Quizzes</p>
                        </div>
                        <div>
                            <i class="fas fa-quiz fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo count($dashboardData['recent_results']); ?></h4>
                            <p>Recent Results</p>
                        </div>
                        <div>
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Quiz Results</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($dashboardData['recent_results'])): ?>
                        <p>No quiz results yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Quiz</th>
                                        <th>Score</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dashboardData['recent_results'] as $result): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($result['student_name'] ?: 'Guest User'); ?></td>
                                            <td><?php echo htmlspecialchars($result['quiz_title']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $result['percentage'] >= 70 ? 'bg-success' : ($result['percentage'] >= 50 ? 'bg-warning' : 'bg-danger'); ?>">
                                                    <?php echo $result['percentage']; ?>%
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($result['completed_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="quiz_form.php" class="btn btn-primary">Create New Quiz</a>
                        <a href="quiz_list.php" class="btn btn-secondary">Manage Quizzes</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../views/layout/admin_footer.php'; ?>