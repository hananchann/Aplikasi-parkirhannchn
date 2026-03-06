<?php
/**
 * Logout Handler
 */

session_start();
require_once 'config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    logActivity($conn, $_SESSION['user_id'], "Logout dari sistem");
}

session_destroy();
header('Location: index.php');
exit();
?>
