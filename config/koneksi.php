<?php
/**
 * Database Connection Configuration
 * Sistem Informasi Parkir
 */

// Database credentials - Use environment variables if available (for Vercel), otherwise fallback to local
define('DB_HOST', getenv('DB_HOST') ?: (defined('LOCAL_DB_HOST') ? LOCAL_DB_HOST : 'localhost'));
define('DB_USER', getenv('DB_USER') ?: (defined('LOCAL_DB_USER') ? LOCAL_DB_USER : 'root'));
define('DB_PASS', getenv('DB_PASS') ?: (defined('LOCAL_DB_PASS') ? LOCAL_DB_PASS : ''));
define('DB_NAME', getenv('DB_NAME') ?: (defined('LOCAL_DB_NAME') ? LOCAL_DB_NAME : 'db_parkir'));

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Function to log activity
function logActivity($conn, $id_user, $aktivitas)
{
    try {
        // Check if user exists before logging
        $check_stmt = $conn->prepare("SELECT id_user FROM users WHERE id_user = ?");
        $check_stmt->bind_param("i", $id_user);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        // Only log if user exists
        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("INSERT INTO log_aktivitas (id_user, aktivitas, waktu) VALUES (?, ?, NOW())");
            $stmt->bind_param("is", $id_user, $aktivitas);
            $stmt->execute();
            $stmt->close();
        }
        $check_stmt->close();
    }
    catch (Exception $e) {
        // Silently fail - don't crash the application if logging fails
        error_log("Failed to log activity: " . $e->getMessage());
    }
}

// Function to sanitize input
function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
