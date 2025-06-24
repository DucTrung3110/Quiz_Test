
<?php
require_once '../config/config.php';

$userController = new UserController();

// Check if user is logged in
if (!$userController->isAuthenticated()) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';
$user = $userController->getCurrentUser();
$userResults = $userController->getUserResults();

// Handle avatar upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/uploads/avatars/';
        // Ensure directory exists with proper permissions
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                $error = 'Không thể tạo thư mục uploads';
            }
        } else {
            // Check if directory is writable
            if (!is_writable($uploadDir)) {
                chmod($uploadDir, 0777);
            }
        }
        
        // Check file size (limit to 5MB)
        if ($_FILES['avatar']['size'] > 5 * 1024 * 1024) {
            $error = 'File ảnh quá lớn. Vui lòng chọn file nhỏ hơn 5MB';
        } else {
            // Get file extension
            $imageInfo = getimagesize($_FILES['avatar']['tmp_name']);
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            
            if ($imageInfo === false || !in_array($imageInfo['mime'], $allowedTypes)) {
                $error = 'Vui lòng chọn file ảnh hợp lệ (JPG, PNG, GIF)';
            } else {
                $extension = ($imageInfo['mime'] === 'image/jpeg') ? '.jpg' : 
                            (($imageInfo['mime'] === 'image/png') ? '.png' : '.gif');
                $fileName = $_SESSION['user_id'] . $extension;
                $uploadPath = $uploadDir . $fileName;
            
                // Remove old avatar files if they exist
                $oldFiles = glob($uploadDir . $_SESSION['user_id'] . '.*');
                foreach ($oldFiles as $oldFile) {
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
                    $success = 'Tải ảnh đại diện thành công';
                } else {
                    $error = 'Không thể tải ảnh lên. Vui lòng kiểm tra quyền thư mục';
                }
            }
        }
    } else {
        $error = 'Vui lòng chọn file ảnh';
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $email = $_POST['email'] ?? '';
    $fullName = $_POST['full_name'] ?? '';

    try {
        $userController->updateProfile($email, $fullName);
        $success = 'Profile updated successfully';
        $user = $userController->getCurrentUser(); // Refresh user data
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    try {
        $userController->changePassword($currentPassword, $newPassword, $confirmPassword);
        $success = 'Password changed successfully';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include '../views/layout/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Thông tin cá nhân</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" id="username" 
                                           value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h4>Đổi mật khẩu</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="change_password" value="1">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Đổi mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Thông tin tài khoản</h5>
                </div>
                <div class="card-body text-center">
                    <?php 
                    $uploadDir = "../assets/uploads/avatars/";
                    // Use relative path for web display in Replit
                    $webPath = "../assets/uploads/avatars/";
                    $userId = $_SESSION['user_id'];
                    $extensions = ['.jpg', '.jpeg', '.png', '.gif'];
                    $avatarSrc = "https://via.placeholder.com/150x150/6c757d/ffffff?text=User";
                    
                    // Check for existing avatar with any extension
                    foreach ($extensions as $ext) {
                        $serverPath = $uploadDir . $userId . $ext;
                        if (file_exists($serverPath)) {
                            $avatarSrc = $webPath . $userId . $ext . "?v=" . time(); // Add cache busting
                            break;
                        }
                    }
                    
                    // Debug information (remove in production)
                    if (isset($_GET['debug'])) {
                        echo "<!-- Debug Info: ";
                        echo "Upload Dir: " . $uploadDir . " ";
                        echo "Web Path: " . $webPath . " ";
                        echo "User ID: " . $userId . " ";
                        echo "Avatar Src: " . $avatarSrc . " ";
                        echo "Directory exists: " . (is_dir($uploadDir) ? 'Yes' : 'No') . " ";
                        echo "Directory writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . " ";
                        echo "-->";
                    }
                    ?>
                    <img src="<?php echo $avatarSrc; ?>" class="rounded-circle img-fluid mb-3" alt="Avatar" style="width: 150px; height: 150px; object-fit: cover;">

                    <form method="POST" enctype="multipart/form-data" class="mb-3">
                        <div class="mb-2">
                            <input type="file" class="form-control" name="avatar" accept="image/*" id="avatarInput">
                        </div>
                        <button type="submit" name="upload_avatar" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-upload"></i> Cập nhật ảnh
                        </button>
                    </form>

                    <h5 class="mt-3"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>Thống kê cá nhân</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3><?php echo count($userResults); ?></h3>
                        <p class="text-muted">Số bài quiz đã làm</p>
                    </div>

                    <?php if (!empty($userResults)): ?>
                        <hr>
                        <h6>Kết quả gần đây:</h6>
                        <?php foreach (array_slice($userResults, 0, 5) as $result): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small><?php echo htmlspecialchars($result['quiz_title']); ?></small>
                                <span class="badge <?php echo $result['percentage'] >= 70 ? 'bg-success' : ($result['percentage'] >= 50 ? 'bg-warning' : 'bg-danger'); ?>">
                                    <?php echo $result['percentage']; ?>%
                                </span>
                            </div>
                        <?php endforeach; ?>

                        <?php if (count($userResults) > 5): ?>
                            <small class="text-muted">và <?php echo count($userResults) - 5; ?> kết quả khác...</small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../views/layout/footer.php'; ?>
