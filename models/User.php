<?php
/**
 * User Model
 * Handles all user-related database operations
 */

class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Authenticate user login
     */
    public function login($username, $password)
    {
        $stmt = $this->conn->prepare("SELECT id_user, nama, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Get all users
     */
    public function getAllUsers()
    {
        $stmt = $this->conn->prepare("SELECT id_user, nama, username, role, created_at FROM users ORDER BY created_at DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get user by ID
     */
    public function getUserById($id)
    {
        $stmt = $this->conn->prepare("SELECT id_user, nama, username, role, created_at FROM users WHERE id_user = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Create new user
     */
    public function createUser($nama, $username, $password, $role)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $username, $hashed_password, $role);
        return $stmt->execute();
    }

    /**
     * Update user
     */
    public function updateUser($id, $nama, $username, $role, $password = null)
    {
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE users SET nama = ?, username = ?, password = ?, role = ? WHERE id_user = ?");
            $stmt->bind_param("ssssi", $nama, $username, $hashed_password, $role, $id);
        }
        else {
            $stmt = $this->conn->prepare("UPDATE users SET nama = ?, username = ?, role = ? WHERE id_user = ?");
            $stmt->bind_param("sssi", $nama, $username, $role, $id);
        }
        return $stmt->execute();
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id_user = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Count users by role
     */
    public function countByRole($role)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = ?");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>
