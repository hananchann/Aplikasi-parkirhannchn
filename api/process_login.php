<?php
/**
 * Process Login
 * Handles login form submission
 */

session_start();
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/controllers/AuthController.php';

$authController = new AuthController($conn);
$error = $authController->login();

if ($error) {
    $_SESSION['error'] = $error;
    header('Location: /index.php');
    exit();
}
?>
