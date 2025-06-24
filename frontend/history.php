<?php
require_once '../config/config.php';

$userController = new UserController();

// Check if user is logged in
if (!$userController->isAuthenticated()) {
    header('Location: login.php');
    exit();
}

$currentUser = $userController->getCurrentUser();
$userResults = $userController->getUserResults();

// Calculate statistics
$totalQuizzes = count($userResults);
$totalScore = 0;
$perfectScores = 0;
$averagePercentage = 0;

if ($totalQuizzes > 0) {
    foreach ($userResults as $result) {
        $totalScore += $result['percentage'];
        if ($result['percentage'] == 100) {
            $perfectScores++;
        }
    }
    $averagePercentage = round($totalScore / $totalQuizzes, 1);
}

include '../views/layout/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2>
                <i class="fas fa-history"></i> Lịch sử bài làm
            </h2>
            <p class="text-muted">Xem lại các bài quiz bạn đã hoàn thành và tiến độ học tập</p>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3><?php echo $totalQuizzes; ?></h3>
                    <p class="mb-0">Tổng bài đã làm</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3><?php echo $averagePercentage; ?>%</h3>
                    <p class="mb-0">Điểm trung bình</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3><?php echo $perfectScores; ?></h3>
                    <p class="mb-0">Bài điểm tối đa</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3><?php echo $totalQuizzes > 0 ? round(($perfectScores / $totalQuizzes) * 100, 1) : 0; ?>%</h3>
                    <p class="mb-0">Tỷ lệ hoàn hảo</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quiz History Table -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list"></i> Chi tiết các bài đã làm</h5>
        </div>
        <div class="card-body">
            <?php if (empty($userResults)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h4>Chưa có bài quiz nào</h4>
                    <p class="text-muted">Bạn chưa hoàn thành bài quiz nào. Hãy bắt đầu làm bài để xem lịch sử tại đây.</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-play"></i> Bắt đầu làm quiz
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>Tên Quiz</th>
                                <th>Điểm số</th>
                                <th>Phần trăm</th>
                                <th>Trạng thái</th>
                                <th>Thời gian hoàn thành</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userResults as $index => $result): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($result['quiz_title']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <?php echo $result['score']; ?>/<?php echo $result['total_questions']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar 
                                                <?php 
                                                if ($result['percentage'] >= 80) echo 'bg-success';
                                                elseif ($result['percentage'] >= 50) echo 'bg-warning';
                                                else echo 'bg-danger';
                                                ?>" 
                                                style="width: <?php echo $result['percentage']; ?>%">
                                                <?php echo $result['percentage']; ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($result['percentage'] >= 80): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-star"></i> Xuất sắc
                                            </span>
                                        <?php elseif ($result['percentage'] >= 60): ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-thumbs-up"></i> Khá
                                            </span>
                                        <?php elseif ($result['percentage'] >= 40): ?>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-meh"></i> Trung bình
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-thumbs-down"></i> Cần cải thiện
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small>
                                            <?php echo date('d/m/Y H:i', strtotime($result['completed_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="result.php?id=<?php echo $result['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Performance Chart Section -->
                <div class="mt-4">
                    <h6><i class="fas fa-chart-line"></i> Biểu đồ tiến độ</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="performanceChart" width="400" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Phân tích hiệu suất</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-trophy text-warning"></i> 
                                            Điểm cao nhất: <strong><?php echo max(array_column($userResults, 'percentage')); ?>%</strong>
                                        </li>
                                        <li><i class="fas fa-chart-line text-info"></i> 
                                            Điểm thấp nhất: <strong><?php echo min(array_column($userResults, 'percentage')); ?>%</strong>
                                        </li>
                                        <li><i class="fas fa-clock text-primary"></i> 
                                            Bài gần nhất: <strong><?php echo date('d/m/Y', strtotime($userResults[0]['completed_at'])); ?></strong>
                                        </li>
                                        <li><i class="fas fa-target text-success"></i> 
                                            Mục tiêu: Đạt 90% ở tất cả các bài
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php if (!empty($userResults)): ?>
// Create performance chart
const ctx = document.getElementById('performanceChart').getContext('2d');
const performanceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?php 
            $labels = array_reverse(array_map(function($result) {
                return "'" . date('d/m', strtotime($result['completed_at'])) . "'";
            }, array_slice($userResults, 0, 10)));
            echo implode(',', $labels);
        ?>],
        datasets: [{
            label: 'Điểm số (%)',
            data: [<?php 
                $scores = array_reverse(array_map(function($result) {
                    return $result['percentage'];
                }, array_slice($userResults, 0, 10)));
                echo implode(',', $scores);
            ?>],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Tiến độ điểm số theo thời gian'
            }
        }
    }
});
<?php endif; ?>
</script>

<?php include '../views/layout/footer.php'; ?>