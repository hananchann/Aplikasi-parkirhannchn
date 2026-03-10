<?php
/**
 * Database Connection Configuration
 * Sistem Informasi Parkir
 * Updated for Vercel and Cloud Databases (SSL/Port support)
 */

// Helper to get environment variable with fallback
function get_db_env($name, $default = '')
{
    return getenv($name) ?: ($_ENV[$name] ?? $default);
}

// Database credentials
$db_host_full = get_db_env('DB_HOST', 'localhost');
$db_port = get_db_env('DB_PORT', '3306');

// Parse port from host if host is in host:port format
if (strpos($db_host_full, ':') !== false) {
    list($db_host, $extracted_port) = explode(':', $db_host_full);
    $db_port = $extracted_port;
}
else {
    $db_host = $db_host_full;
}

$db_user = get_db_env('DB_USER', 'root');
$db_pass = get_db_env('DB_PASS', '');
$db_name = get_db_env('DB_NAME', 'db_parkir');
$db_ssl = get_db_env('DB_SSL', 'false');

// Create connection using mysqli_init for SSL support
$conn = mysqli_init();

if (!$conn) {
    die("mysqli_init failed");
}

// Set SSL if requested
if (strtolower($db_ssl) === 'true') {
    // MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT is often needed for cloud providers
    // unless you provide a specific CA certificate.
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
    $flags = MYSQLI_CLIENT_SSL;
}
else {
    $flags = 0;
}

// Connect to database
$connected = mysqli_real_connect(
    $conn,
    $db_host,
    $db_user,
    $db_pass,
    $db_name,
    $db_port,
    NULL,
    $flags
);

if (!$connected) {
    $error = mysqli_connect_error();
    $errno = mysqli_connect_errno();
    $ssl_msg = (strtolower($db_ssl) === 'true') ? " (SSL Enabled)" : " (SSL Disabled)";
    die("Koneksi database gagal: $error (Code: $errno)$ssl_msg. Pastikan Host, Port, dan SSL di Vercel sudah sesuai dengan dashboard Aiven/Cloud DB Anda. Host: $db_host, Port: $db_port");
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
