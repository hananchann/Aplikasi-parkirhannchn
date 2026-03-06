<?php
session_start();
require_once '../../controllers/AuthController.php';
AuthController::checkRole(['admin']);

require_once '../../config/koneksi.php';
require_once '../../models/Kendaraan.php';

$kendaraanModel = new Kendaraan($conn);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $kendaraanModel->createKendaraan(
                    sanitize($_POST['jenis_kendaraan']),
                    sanitize($_POST['keterangan'])
                );
                logActivity($conn, $_SESSION['user_id'], "Menambah jenis kendaraan: " . $_POST['jenis_kendaraan']);
                $_SESSION['success'] = "Jenis kendaraan berhasil ditambahkan!";
                break;

            case 'update':
                $kendaraanModel->updateKendaraan(
                    $_POST['id_kendaraan'],
                    sanitize($_POST['jenis_kendaraan']),
                    sanitize($_POST['keterangan'])
                );
                logActivity($conn, $_SESSION['user_id'], "Mengupdate jenis kendaraan: " . $_POST['jenis_kendaraan']);
                $_SESSION['success'] = "Jenis kendaraan berhasil diupdate!";
                break;

            case 'delete':
                $kendaraan = $kendaraanModel->getKendaraanById($_POST['id_kendaraan']);
                if ($kendaraanModel->deleteKendaraan($_POST['id_kendaraan'])) {
                    logActivity($conn, $_SESSION['user_id'], "Menghapus jenis kendaraan: " . $kendaraan['jenis_kendaraan']);
                    $_SESSION['success'] = "Jenis kendaraan berhasil dihapus!";
                }
                else {
                    $_SESSION['error'] = "Gagal menghapus! Jenis kendaraan ini masih digunakan dalam data transaksi.";
                }
                break;
        }
        header('Location: kendaraan.php');
        exit();
    }
}

$kendaraans = $kendaraanModel->getAllKendaraan();

$page_title = "Kelola Kendaraan";
include '../layouts/header.php';
include '../layouts/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-car me-2"></i>Kelola Jenis Kendaraan</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKendaraanModal">
            <i class="fas fa-plus me-2"></i>Tambah Jenis
        </button>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success'];
    unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php
endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $_SESSION['error'];
    unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php
endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list me-2"></i>Daftar Jenis Kendaraan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Kendaraan</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kendaraans as $index => $kendaraan): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($kendaraan['jenis_kendaraan']); ?></td>
                            <td><?php echo htmlspecialchars($kendaraan['keterangan']); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="editKendaraan(<?php echo htmlspecialchars(json_encode($kendaraan)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display:inline;" onsubmit="return confirmDelete()">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_kendaraan" value="<?php echo $kendaraan['id_kendaraan']; ?>">
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

<!-- Add Kendaraan Modal -->
<div class="modal fade" id="addKendaraanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Jenis Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label class="form-label">Jenis Kendaraan</label>
                        <input type="text" class="form-control" name="jenis_kendaraan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"></textarea>
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

<!-- Edit Kendaraan Modal -->
<div class="modal fade" id="editKendaraanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Jenis Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id_kendaraan" id="edit_id_kendaraan">
                    <div class="mb-3">
                        <label class="form-label">Jenis Kendaraan</label>
                        <input type="text" class="form-control" name="jenis_kendaraan" id="edit_jenis_kendaraan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" id="edit_keterangan" rows="3"></textarea>
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
function editKendaraan(kendaraan) {
    document.getElementById('edit_id_kendaraan').value = kendaraan.id_kendaraan;
    document.getElementById('edit_jenis_kendaraan').value = kendaraan.jenis_kendaraan;
    document.getElementById('edit_keterangan').value = kendaraan.keterangan;
    const modal = new bootstrap.Modal(document.getElementById('editKendaraanModal'));
    modal.show();
}
</script>

    </div></div>
<?php include '../layouts/footer.php'; ?>
