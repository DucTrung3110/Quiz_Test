<?php
require_once '../config/config.php';

$userController = new UserController();
$error = '';

// Redirect if already logged in
if ($userController->isAuthenticated()) {
    header('Location: index.php');
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $userController->login($username, $password);
        header('Location: index.php');
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include '../views/layout/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-user fa-3x text-primary"></i>
                        <h3 class="mt-2">Đăng Nhập</h3>
                        <p class="text-muted">Đăng nhập để làm bài quiz</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <div class="password-container">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Đăng Nhập</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="mb-0">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                        <p class="mb-0"><a href="forgot_password_demo.php">Quên mật khẩu?</a></p>
                    </div>
                    
                    <div class="mt-4 p-3 bg-light rounded">
                        <small class="text-muted">
                            <strong>Tài khoản demo:</strong><br>
                            Username: demo<br>
                            Password: demo123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../views/layout/footer.php'; ?>