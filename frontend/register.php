<?php
require_once '../config/config.php';

$userController = new UserController();
$error = '';
$success = '';

// Redirect if already logged in
if ($userController->isAuthenticated()) {
    header('Location: index.php');
    exit();
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullName = $_POST['full_name'] ?? '';
    
    try {
        $userController->register($username, $email, $password, $confirmPassword, $fullName);
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
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x text-primary"></i>
                        <h3 class="mt-2">Đăng Ký</h3>
                        <p class="text-muted">Tạo tài khoản để làm bài quiz</p>
                    </div>
                    
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
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên *</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập *</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                            <div class="form-text">Tối thiểu 3 ký tự</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu *</label>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="password" name="password" required
                                       oninput="checkPasswordStrength(this.value)">
                                <i class="fas fa-eye position-absolute end-0 top-50 translate-middle-y me-3" 
                                   style="cursor: pointer;" onclick="togglePassword('password')"></i>
                            </div>
                            <div class="password-requirements mt-2">
                                <small class="text-muted">Mật khẩu phải có:</small>
                                <ul class="list-unstyled mt-1" style="font-size: 0.875em;">
                                    <li id="length-check"><i class="fas fa-times text-danger"></i> Ít nhất 8 ký tự</li>
                                    <li id="uppercase-check"><i class="fas fa-times text-danger"></i> 1 chữ cái viết hoa</li>
                                    <li id="lowercase-check"><i class="fas fa-times text-danger"></i> 1 chữ cái viết thường</li>
                                    <li id="number-check"><i class="fas fa-times text-danger"></i> 1 chữ số</li>
                                    <li id="special-check"><i class="fas fa-times text-danger"></i> 1 ký tự đặc biệt (!@#$%^&*,.?\":{}|<>)</li>
                                </ul>
                            </div>
                            <div class="password-strength mt-2">
                                <div class="progress" style="height: 5px;">
                                    <div id="password-strength-bar" class="progress-bar" style="width: 0%"></div>
                                </div>
                                <small id="password-strength-text" class="text-muted"></small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu *</label>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <i class="fas fa-eye position-absolute end-0 top-50 translate-middle-y me-3" 
                                   style="cursor: pointer;" onclick="togglePassword('confirm_password')"></i>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Đăng Ký</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="mb-0">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling;
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function checkPasswordStrength(password) {
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };

    // Update requirement checks
    updateRequirement('length-check', requirements.length);
    updateRequirement('uppercase-check', requirements.uppercase);
    updateRequirement('lowercase-check', requirements.lowercase);
    updateRequirement('number-check', requirements.number);
    updateRequirement('special-check', requirements.special);

    // Calculate strength
    const metRequirements = Object.values(requirements).filter(Boolean).length;
    const strengthPercentage = (metRequirements / 5) * 100;
    
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    
    strengthBar.style.width = strengthPercentage + '%';
    
    if (metRequirements < 2) {
        strengthBar.className = 'progress-bar bg-danger';
        strengthText.textContent = 'Rất yếu';
        strengthText.className = 'text-danger';
    } else if (metRequirements < 3) {
        strengthBar.className = 'progress-bar bg-warning';
        strengthText.textContent = 'Yếu';
        strengthText.className = 'text-warning';
    } else if (metRequirements < 4) {
        strengthBar.className = 'progress-bar bg-info';
        strengthText.textContent = 'Trung bình';
        strengthText.className = 'text-info';
    } else if (metRequirements < 5) {
        strengthBar.className = 'progress-bar bg-primary';
        strengthText.textContent = 'Mạnh';
        strengthText.className = 'text-primary';
    } else {
        strengthBar.className = 'progress-bar bg-success';
        strengthText.textContent = 'Rất mạnh';
        strengthText.className = 'text-success';
    }
}

function updateRequirement(elementId, isMet) {
    const element = document.getElementById(elementId);
    const icon = element.querySelector('i');
    
    if (isMet) {
        icon.className = 'fas fa-check text-success';
    } else {
        icon.className = 'fas fa-times text-danger';
    }
}

// Validate form before submission
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Mật khẩu xác nhận không khớp!');
        return;
    }
    
    // Check all password requirements
    const requirements = [
        password.length >= 8,
        /[A-Z]/.test(password),
        /[a-z]/.test(password),
        /[0-9]/.test(password),
        /[!@#$%^&*(),.?":{}|<>]/.test(password)
    ];
    
    if (!requirements.every(Boolean)) {
        e.preventDefault();
        alert('Mật khẩu chưa đáp ứng đủ các yêu cầu!');
        return;
    }
});
</script>

<?php include '../views/layout/footer.php'; ?>