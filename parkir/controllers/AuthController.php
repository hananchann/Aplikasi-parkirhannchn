<?php
/**
 * Authentication Controller
 * Handles login, logout, and session management
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private $userModel;
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->userModel = new User($db);
    }

    /**
     * Process login
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = sanitize($_POST['username']);
            $password = $_POST['password'];

            $user = $this->userModel->login($username, $password);

            if ($user) {
                // Set session
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Log activity
                logActivity($this->conn, $user['id_user'], "Login ke sistem");

                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header('Location: /views/admin/dashboard.php');
                        break;
                    case 'petugas':
                        header('Location: /views/petugas/dashboard.php');
                        break;
                    case 'owner':
                        header('Location: /views/owner/dashboard.php');
                        break;
                }
                exit();
            }
            else {
                return "Username atau password salah!";
            }
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        if (isset($_SESSION['user_id'])) {
            logActivity($this->conn, $_SESSION['user_id'], "Logout dari sistem");
        }
        session_destroy();
        header('Location: /index.php');
        exit();
    }

    /**
     * Check if user is authenticated
     */
    public static function checkAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /index.php');
            exit();
        }
    }

    /**
     * Check user role
     */
    public static function checkRole($allowed_roles)
    {
        self::checkAuth();
        if (!in_array($_SESSION['role'], $allowed_roles)) {
            header('Location: /index.php');
            exit();
        }
    }
}
?>
