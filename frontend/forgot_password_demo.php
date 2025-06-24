<?php
require_once '../config/config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = 'Vui lòng nhập địa chỉ email.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Địa chỉ email không hợp lệ.';
    } else {
        global $pdo;
        
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now
            
            // Update user with reset token
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
            $stmt->execute([$reset_token, $expires, $email]);
            
            $message = 'Liên kết đặt lại mật khẩu đã được tạo.<br><br>
                       <strong>Trong môi trường demo:</strong><br>
                       Sử dụng link sau để đặt lại mật khẩu:<br>
                       <a href="reset_password_demo.php?token=' . $reset_token . '" class="btn btn-primary" style="color: white; text-decoration: none; padding: 8px 16px; background: #007bff; border-radius: 4px; display: inline-block; margin-top: 10px;">Đặt Lại Mật Khẩu</a>';
        } else {
            $error = 'Không tìm thấy tài khoản với địa chỉ email này.';
        }
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
                        <i class="fas fa-key fa-3x text-warning"></i>
                        <h3 class="mt-2">Quên Mật Khẩu</h3>
                        <p class="text-muted">Nhập email để đặt lại mật khẩu</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($message): ?>
                        <div class="alert alert-success">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Địa chỉ Email</label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   placeholder="demo@example.com">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">Gửi Yêu Cầu</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <p class="mb-0"><a href="login.php">Quay lại đăng nhập</a></p>
                        <p class="mb-0"><a href="register.php">Đăng ký tài khoản mới</a></p>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded">
                        <small class="text-muted">
                            <strong>Email demo để test:</strong><br>
                            demo@example.com
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../views/layout/footer.php'; ?>