<?php
/**
 * Process Login
 * Handles login form submission
 */

session_start();
require_once 'config/koneksi.php';
require_once 'controllers/AuthController.php';

$authController = new AuthController($conn);
$error = $authController->login();

if ($error) {
    $_SESSION['error'] = $error;
    header('Location: /index.php');
    exit();
}
?>
