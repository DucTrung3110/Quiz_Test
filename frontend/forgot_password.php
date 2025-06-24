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
            // Generate reset token - use server's current timezone
            $reset_token = bin2hex(random_bytes(32));
            $expires_timestamp = time() + 3600; // 1 hour from now
            $expires = date('Y-m-d H:i:s', $expires_timestamp);
            
            // Debug logging
            error_log("DEBUG FORGOT PASSWORD - Generated token: " . $reset_token);
            error_log("DEBUG FORGOT PASSWORD - Current timestamp: " . time() . " (" . date('Y-m-d H:i:s') . ")");
            error_log("DEBUG FORGOT PASSWORD - Expires timestamp: " . $expires_timestamp . " (" . $expires . ")");
            error_log("DEBUG FORGOT PASSWORD - Email: " . $email);
            
            // Update user with reset token
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
            $result = $stmt->execute([$reset_token, $expires, $email]);
            
            if ($result) {
                // Verify the token was saved
                $verify_stmt = $pdo->prepare("SELECT reset_token, reset_token_expires FROM users WHERE email = ?");
                $verify_stmt->execute([$email]);
                $saved_data = $verify_stmt->fetch();
                error_log("DEBUG FORGOT PASSWORD - Saved token: " . $saved_data['reset_token']);
                error_log("DEBUG FORGOT PASSWORD - Saved expires: " . $saved_data['reset_token_expires']);
                
                $message = 'Liên kết đặt lại mật khẩu đã được tạo.<br><br>
                           <strong>Trong môi trường demo:</strong><br>
                           <p><strong>Token:</strong> <code>' . $reset_token . '</code></p>
                           <p><strong>Hết hạn:</strong> ' . $expires . '</p>
                           Sử dụng link sau để đặt lại mật khẩu:<br>
                           <a href="reset_password_demo.php?token=' . $reset_token . '" class="btn btn-primary" style="color: white; text-decoration: none; padding: 8px 16px; background: #007bff; border-radius: 4px; display: inline-block; margin-top: 10px;">Đặt Lại Mật Khẩu</a>';
            } else {
                $error = 'Có lỗi xảy ra khi tạo mã đặt lại mật khẩu.';
            }
        } else {
            $error = 'Không tìm thấy tài khoản với địa chỉ email này.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Quên Mật Khẩu</h2>
                <p>Nhập địa chỉ email để đặt lại mật khẩu</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Địa chỉ Email:</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Gửi Yêu Cầu</button>
                </div>
            </form>

            <div class="auth-links">
                <a href="login.php">Quay lại đăng nhập</a>
                <a href="register.php">Đăng ký tài khoản mới</a>
            </div>
        </div>
    </div>
</body>
</html>