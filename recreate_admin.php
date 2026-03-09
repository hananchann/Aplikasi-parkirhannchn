<?php
/**
 * Recreate Default Admin User
 */

require_once 'config/koneksi.php';

// Generate password hash for admin123
$admin_hash = password_hash('admin123', PASSWORD_DEFAULT);

// Check if admin user exists
$check = $conn->prepare("SELECT id_user FROM users WHERE username = 'admin'");
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Update existing admin
    $stmt = $conn->prepare("UPDATE users SET password = ?, nama = 'Administrator' WHERE username = 'admin'");
    $stmt->bind_param("s", $admin_hash);
    $stmt->execute();
    echo "Admin user password updated!\n";
}
else {
    // Create new admin
    $stmt = $conn->prepare("INSERT INTO users (nama, username, password, role) VALUES ('Administrator', 'admin', ?, 'admin')");
    $stmt->bind_param("s", $admin_hash);
    $stmt->execute();
    echo "Admin user created!\n";
}

echo "\nYou can now login with:\n";
echo "Username: admin\n";
echo "Password: admin123\n";
?>
