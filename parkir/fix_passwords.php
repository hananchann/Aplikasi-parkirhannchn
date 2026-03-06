<?php
/**
 * Fix Password Hashes
 * This script generates correct password hashes for the default users
 */

// Generate password hashes
$admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
$petugas_hash = password_hash('petugas123', PASSWORD_DEFAULT);
$owner_hash = password_hash('owner123', PASSWORD_DEFAULT);

echo "Password hashes generated:\n\n";
echo "Admin (admin123):\n$admin_hash\n\n";
echo "Petugas (petugas123):\n$petugas_hash\n\n";
echo "Owner (owner123):\n$owner_hash\n\n";

// Update database
require_once 'config/koneksi.php';

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $admin_hash);
$stmt->execute();

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'petugas'");
$stmt->bind_param("s", $petugas_hash);
$stmt->execute();

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'owner'");
$stmt->bind_param("s", $owner_hash);
$stmt->execute();

echo "Database updated successfully!\n";
echo "You can now login with:\n";
echo "- admin / admin123\n";
echo "- petugas / petugas123\n";
echo "- owner / owner123\n";
?>
