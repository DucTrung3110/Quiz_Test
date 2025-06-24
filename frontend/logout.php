<?php
require_once '../config/config.php';

$userController = new UserController();
$userController->logout();

header('Location: login.php');
exit();
?>