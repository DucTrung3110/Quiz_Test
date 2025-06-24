
<?php
require_once 'config/config.php';

echo "<h2>Debug Admin Dashboard Data</h2>";

try {
    global $pdo;
    
    // Check if results table exists and has data
    echo "<h3>Database Structure Check:</h3>";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'results'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✓ Results table exists</p>";
        
        // Check results count
        $stmt = $pdo->query("SELECT COUNT(*) FROM results");
        $count = $stmt->fetchColumn();
        echo "<p>Total results in database: <strong>$count</strong></p>";
        
        if ($count > 0) {
            // Show sample results
            echo "<h3>Sample Results:</h3>";
            $stmt = $pdo->query("SELECT r.*, q.title as quiz_title FROM results r LEFT JOIN quizzes q ON r.quiz_id = q.id ORDER BY r.completed_at DESC LIMIT 5");
            $results = $stmt->fetchAll();
            
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Student Name</th><th>Quiz Title</th><th>Score</th><th>Percentage</th><th>Date</th></tr>";
            foreach ($results as $result) {
                echo "<tr>";
                echo "<td>" . $result['id'] . "</td>";
                echo "<td>" . htmlspecialchars($result['student_name'] ?: 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($result['quiz_title'] ?: 'N/A') . "</td>";
                echo "<td>" . $result['score'] . "/" . $result['total_questions'] . "</td>";
                echo "<td>" . $result['percentage'] . "%</td>";
                echo "<td>" . $result['completed_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p>❌ Results table does not exist</p>";
    }
    
    // Test AdminController
    echo "<h3>AdminController Test:</h3>";
    $controller = new AdminController();
    $dashboardData = $controller->getDashboardData();
    
    echo "<p>Total quizzes: " . $dashboardData['total_quizzes'] . "</p>";
    echo "<p>Recent results count: " . count($dashboardData['recent_results']) . "</p>";
    
    if (!empty($dashboardData['recent_results'])) {
        echo "<h4>Recent Results from Controller:</h4>";
        echo "<pre>" . print_r($dashboardData['recent_results'], true) . "</pre>";
    } else {
        echo "<p>❌ No recent results returned from controller</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></p>";
}
?>
