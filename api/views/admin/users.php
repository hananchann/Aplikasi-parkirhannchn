<?php
session_start();
require_once __DIR__ . '/../../controllers/AuthController.php';
AuthController::checkRole(['admin']);

require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../models/User.php';

$userModel = new User($conn);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $userModel->createUser(
                    sanitize($_POST['nama']),
                    sanitize($_POST['username']),
                    $_POST['password'],
                    $_POST['role']
                );
                logActivity($conn, $_SESSION['user_id'], "Menambah user baru: " . $_POST['username']);
                $_SESSION['success'] = "User berhasil ditambahkan!";
                break;

            case 'update':
                $password = !empty($_POST['password']) ? $_POST['password'] : null;
                $userModel->updateUser(
                    $_POST['id_user'],
                    sanitize($_POST['nama']),
                    sanitize($_POST['username']),
                    $_POST['role'],
                    $password
                );
                logActivity($conn, $_SESSION['user_id'], "Mengupdate user: " . $_POST['username']);
                $_SESSION['success'] = "User berhasil diupdate!";
                break;

            case 'delete':
                // Prevent deleting yourself
                if ($_POST['id_user'] == $_SESSION['user_id']) {
                    $_SESSION['error'] = "Anda tidak dapat menghapus akun Anda sendiri!";
                    break;
                }

                $user = $userModel->getUserById($_POST['id_user']);

                // Check if user exists
                if (!$user) {
                    $_SESSION['error'] = "User tidak ditemukan!";
                    break;
                }

                // Log activity BEFORE deleting to avoid foreign key constraint error
                logActivity($conn, $_SESSION['user_id'], "Menghapus user: " . $user['username']);

                // Delete the user
                if ($userModel->deleteUser($_POST['id_user'])) {
                    $_SESSION['success'] = "User berhasil dihapus!";
                }
                else {
                    $_SESSION['error'] = "Gagal menghapus user!";
                }
                break;
        }
        header('Location: users.php');
        exit();
    }
}

$users = $userModel->getAllUsers();

$page_title = "Kelola User";
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users me-2"></i>Kelola User</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus me-2"></i>Tambah User
        </button>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php
    echo $_SESSION['success'];
    unset($_SESSION['success']);
?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php
endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php
    echo $_SESSION['error'];
    unset($_SESSION['error']);
?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php
endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list me-2"></i>Daftar User</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $index => $user): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($user['nama']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td>
                                <?php
    $badge_class = '';
    switch ($user['role']) {
        case 'admin':
            $badge_class = 'bg-primary';
            break;
        case 'petugas':
            $badge_class = 'bg-success';
            break;
        case 'owner':
            $badge_class = 'bg-warning';
            break;
    }
?>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo strtoupper($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display:inline;" onsubmit="return confirmDelete()">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_user" value="<?php echo $user['id_user']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php
endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id_user" id="edit_id_user">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" id="edit_nama" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="edit_username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" id="edit_role" required>
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('edit_id_user').value = user.id_user;
    document.getElementById('edit_nama').value = user.nama;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_role').value = user.role;
    
    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();
}
</script>

    </div> <!-- Close content-wrapper -->
</div> <!-- Close main-content -->

<?php include __DIR__ . '/../layouts/footer.php'; ?>
