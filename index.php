
<?php
// Main entry point for Quiz System
require_once 'config/config.php';

// Check if database connection is working
try {
    global $pdo;
    $pdo->query("SELECT 1");
    // Redirect to frontend if database is working
    header('Location: frontend/index.php');
    exit();
} catch (PDOException $e) {
    // If database connection fails, show setup page
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quiz System - Database Setup Required</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h4 class="mb-0">Database Setup Required</h4>
                        </div>
                        <div class="card-body">
                            <p>The database connection is not working. Please run the setup script first.</p>
                            <p><strong>Error:</strong> <?php echo htmlspecialchars($e->getMessage()); ?></p>
                            <div class="mt-3">
                                <a href="setup_mysql.php" class="btn btn-primary">Run Database Setup</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>
