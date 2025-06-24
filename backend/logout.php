
<?php
require_once '../config/config.php';

$controller = new AdminController();
$controller->logout();

header('Location: login.php');
exit();
?>
