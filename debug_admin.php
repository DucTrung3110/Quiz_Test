
<?php
require_once 'config/config.php';

echo "<h2>Debug Admin Dashboard</h2>";

try {
    global $pdo;
    
    // Check total quizzes
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM quizzes");
    $quiz_count = $stmt->fetchColumn();
    echo "<p><strong>Total Quizzes:</strong> $quiz_count</p>";
    
    // Check total results
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM results");
    $result_count = $stmt->fetchColumn();
    echo "<p><strong>Total Results:</strong> $result_count</p>";
    
    // Show recent results
    $stmt = $pdo->prepare("
        SELECT r.*, q.title as quiz_title, 
               COALESCE(u.full_name, r.student_name, 'Guest User') as student_name
        FROM results r 
        JOIN quizzes q ON r.quiz_id = q.id 
        LEFT JOIN users u ON r.user_id = u.id
        ORDER BY r.completed_at DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recent_results = $stmt->fetchAll();
    
    echo "<h3>Recent Results (" . count($recent_results) . " found):</h3>";
    if ($recent_results) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Student</th><th>Quiz</th><th>Score</th><th>Date</th></tr>";
        foreach ($recent_results as $result) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($result['student_name']) . "</td>";
            echo "<td>" . htmlspecialchars($result['quiz_title']) . "</td>";
            echo "<td>" . $result['percentage'] . "%</td>";
            echo "<td>" . $result['completed_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No results found.</p>";
    }
    
    // Show quiz list
    $stmt = $pdo->query("SELECT id, title, status, created_at FROM quizzes ORDER BY created_at DESC LIMIT 5");
    $quizzes = $stmt->fetchAll();
    
    echo "<h3>Recent Quizzes:</h3>";
    if ($quizzes) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Created</th></tr>";
        foreach ($quizzes as $quiz) {
            echo "<tr>";
            echo "<td>" . $quiz['id'] . "</td>";
            echo "<td>" . htmlspecialchars($quiz['title']) . "</td>";
            echo "<td>" . $quiz['status'] . "</td>";
            echo "<td>" . $quiz['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No quizzes found.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<p><a href="backend/index.php">Go to Admin Dashboard</a></p>
