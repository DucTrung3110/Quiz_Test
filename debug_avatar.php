
<?php
session_start();

// Debug avatar upload issues
echo "<h2>Avatar Debug Information</h2>";

$uploadDir = "assets/uploads/avatars/";
$actualUploadDir = "assets/uploads/avatars/"; // Path from root
$frontendUploadDir = "../assets/uploads/avatars/"; // Path from frontend folder
$userId = $_SESSION['user_id'] ?? 'not_logged_in';

echo "<p><strong>User ID:</strong> " . $userId . "</p>";
echo "<p><strong>Upload Directory:</strong> " . $uploadDir . "</p>";
echo "<p><strong>Directory (from root) exists:</strong> " . (is_dir($actualUploadDir) ? 'Yes' : 'No') . "</p>";
echo "<p><strong>Directory (from frontend) exists:</strong> " . (is_dir($frontendUploadDir) ? 'Yes' : 'No') . "</p>";
echo "<p><strong>Directory writable:</strong> " . (is_writable($actualUploadDir) ? 'Yes' : 'No') . "</p>";

// Check both directories for files
$dirsToCheck = [
    'From root' => $actualUploadDir,
    'From frontend path' => $frontendUploadDir
];

foreach ($dirsToCheck as $label => $dir) {
    echo "<h4>$label ($dir):</h4>";
    if (is_dir($dir)) {
        $files = scandir($dir);
        echo "<p><strong>Files in directory:</strong></p>";
        echo "<ul>";
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $fullPath = $dir . $file;
                echo "<li>" . $file . " (Size: " . filesize($fullPath) . " bytes)</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p><em>Directory does not exist</em></p>";
    }
}

// Check if user has uploaded avatar in both locations
$extensions = ['.jpg', '.jpeg', '.png', '.gif'];
$avatarFound = false;

echo "<h4>Checking for user $userId avatar:</h4>";

foreach ($dirsToCheck as $label => $dir) {
    echo "<p><strong>$label:</strong></p>";
    foreach ($extensions as $ext) {
        $serverPath = $dir . $userId . $ext;
        echo "<span style='margin-left: 20px;'>Checking: " . $serverPath . " - ";
        if (file_exists($serverPath)) {
            echo "<strong style='color: green;'>FOUND!</strong><br>";
            echo "<span style='margin-left: 40px;'>File size: " . filesize($serverPath) . " bytes</span><br>";
            echo "<span style='margin-left: 40px;'><img src='" . $serverPath . "' style='max-width: 200px;' alt='Avatar preview'></span><br>";
            $avatarFound = true;
        } else {
            echo "<span style='color: red;'>Not found</span><br>";
        }
    }
}

if (!$avatarFound) {
    echo "<p><em>No avatar found for user " . $userId . " in any location</em></p>";
}
?>
