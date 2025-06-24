<?php
class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * User registration
     */
    public function register($username, $email, $password, $confirmPassword, $fullName) {
        // Validate input
        if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
            throw new Exception("All fields are required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Validate password strength
        if (strlen($password) < 8) {
            throw new Exception("Mật khẩu phải có ít nhất 8 ký tự");
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new Exception("Mật khẩu phải chứa ít nhất 1 chữ cái viết hoa");
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new Exception("Mật khẩu phải chứa ít nhất 1 chữ cái viết thường");
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new Exception("Mật khẩu phải chứa ít nhất 1 chữ số");
        }

        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            throw new Exception("Mật khẩu phải chứa ít nhất 1 ký tự đặc biệt (!@#$%^&*,.?\":{}|<>)");
        }

        if ($password !== $confirmPassword) {
            throw new Exception("Password confirmation does not match");
        }

        // Register user
        $userId = $this->userModel->register($username, $email, $password, $fullName);

        // Auto login after registration
        $user = $this->userModel->getUserById($userId);
        if ($user) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            $_SESSION['user_logged_in'] = true; // Add a specific user login flag
        }

        return $userId;
    }

    /**
     * User login
     */
    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            throw new Exception("Username and password are required");
        }

        $user = $this->userModel->login($username, $password);

        if ($user) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            $_SESSION['user_logged_in'] = true; // Add a specific user login flag

            return $user;
        } else {
            throw new Exception("Invalid username or password");
        }
    }

    /**
     * User logout
     */
    public function logout() {
        // Chỉ xóa session của user, không ảnh hưởng đến admin
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['full_name']);
        unset($_SESSION['email']);
        unset($_SESSION['logged_in']);
        unset($_SESSION['user_logged_in']);
        return true;
    }

    /**
     * Check if user is logged in
     */
    public function isAuthenticated() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_logged_in']) && !empty($_SESSION['user_logged_in']);
    }

    /**
     * Get current user info
     */
    public function getCurrentUser() {
        if ($this->isAuthenticated()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'full_name' => $_SESSION['full_name'],
                'email' => $_SESSION['email']
            ];
        }
        return false;
    }

    /**
     * Update user profile
     */
    public function updateProfile($email, $fullName) {
        if (!$this->isAuthenticated()) {
            throw new Exception("User not authenticated");
        }

        $userId = $_SESSION['user_id'];
        try {
            if (empty($email) || empty($fullName)) {
                throw new Exception("Email and full name are required");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            $result = $this->userModel->updateProfile($userId, $email, $fullName);

            if ($result) {
                // Update session
                $_SESSION['email'] = $email;
                $_SESSION['full_name'] = $fullName;

                return [
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ];
            } else {
                throw new Exception("Failed to update profile");
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Change password
     */
    public function changePassword($currentPassword, $newPassword, $confirmPassword) {
        if (!$this->isAuthenticated()) {
            throw new Exception("User not authenticated");
        }

        if ($newPassword !== $confirmPassword) {
            throw new Exception("New password and confirmation do not match");
        }

        $userId = $_SESSION['user_id'];
        try {
            if (empty($currentPassword) || empty($newPassword)) {
                throw new Exception("Current password and new password are required");
            }

            // Validate new password strength
            if (strlen($newPassword) < 8) {
                throw new Exception("Mật khẩu mới phải có ít nhất 8 ký tự");
            }

            if (!preg_match('/[A-Z]/', $newPassword)) {
                throw new Exception("Mật khẩu mới phải chứa ít nhất 1 chữ cái viết hoa");
            }

            if (!preg_match('/[a-z]/', $newPassword)) {
                throw new Exception("Mật khẩu mới phải chứa ít nhất 1 chữ cái viết thường");
            }

            if (!preg_match('/[0-9]/', $newPassword)) {
                throw new Exception("Mật khẩu mới phải chứa ít nhất 1 chữ số");
            }

            if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $newPassword)) {
                throw new Exception("Mật khẩu mới phải chứa ít nhất 1 ký tự đặc biệt (!@#$%^&*,.?\":{}|<>)");
            }

            // Verify current password
            $user = $this->userModel->getUserById($userId);
            if (!$user || !password_verify($currentPassword, $user['password'])) {
                throw new Exception("Current password is incorrect");
            }

            $result = $this->userModel->changePassword($userId, $newPassword);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Password changed successfully'
                ];
            } else {
                throw new Exception("Failed to change password");
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get user's quiz history
     */
    public function getUserHistory($userId) {
        try {
            return $this->userModel->getUserResults($userId);
        } catch (Exception $e) {
            error_log("Error in UserController::getUserHistory: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get current user's quiz results
     */
    public function getUserResults() {
        if (!$this->isAuthenticated()) {
            return [];
        }

        try {
            $userId = $_SESSION['user_id'];
            return $this->userModel->getUserResults($userId);
        } catch (Exception $e) {
            error_log("Error in UserController::getUserResults: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate password reset token (demo functionality)
     */
    public function generateResetToken($email) {
        try {
            $user = $this->userModel->getUserByEmail($email);
            if (!$user) {
                throw new Exception("Email not found");
            }

            // Generate token (for demo purposes)
            $token = bin2hex(random_bytes(32));

            return [
                'success' => true,
                'token' => $token,
                'message' => 'Reset token generated (demo mode)'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
?>