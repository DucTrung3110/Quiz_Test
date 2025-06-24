<?php
// Enable error reporting for debugging on infinityfree
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Global configuration settings
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Site configuration
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Quiz Test System');
}
if (!defined('SITE_URL')) {
    define('SITE_URL', 'https://quiztestsystem.wuaze.com');
}

// Admin credentials (simple authentication)
if (!defined('ADMIN_USERNAME')) {
    define('ADMIN_USERNAME', 'admin');
    define('ADMIN_PASSWORD', 'admin123');
}

// Include database configuration
require_once 'database.php';

// Autoload classes - prevent duplicate loading
if (!function_exists('custom_autoloader')) {
    function custom_autoloader($class) {
        // Prevent loading if class already exists
        if (class_exists($class, false)) {
            return;
        }
        
        $paths = [
            __DIR__ . '/../models/',
            __DIR__ . '/../controllers/',
            __DIR__ . '/models/',
            __DIR__ . '/controllers/'
        ];

        foreach ($paths as $path) {
            $file = $path . $class . '.php';
            if (file_exists($file)) {
                include_once $file;
                return;
            }
        }
    }
    spl_autoload_register('custom_autoloader');
}
?>