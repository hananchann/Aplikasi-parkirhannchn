<?php
session_start();
require_once '../../controllers/AuthController.php';
AuthController::checkRole(['admin']);

require_once '../../config/koneksi.php';
require_once '../../models/Tarif.php';

$tarifModel = new Tarif($conn);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $tarifModel->createTarif(
                    sanitize($_POST['jenis_kendaraan']),
                    $_POST['harga_per_jam']
                );
                logActivity($conn, $_SESSION['user_id'], "Menambah tarif: " . $_POST['jenis_kendaraan']);
                $_SESSION['success'] = "Tarif berhasil ditambahkan!";
                break;

            case 'update':
                $tarifModel->updateTarif(
                    $_POST['id_tarif'],
                    sanitize($_POST['jenis_kendaraan']),
                    $_POST['harga_per_jam']
                );
                logActivity($conn, $_SESSION['user_id'], "Mengupdate tarif: " . $_POST['jenis_kendaraan']);
                $_SESSION['success'] = "Tarif berhasil diupdate!";
                break;

            case 'delete':
                $tarif = $tarifModel->getTarifById($_POST['id_tarif']);
                $tarifModel->deleteTarif($_POST['id_tarif']);
                logActivity($conn, $_SESSION['user_id'], "Menghapus tarif: " . $tarif['jenis_kendaraan']);
                $_SESSION['success'] = "Tarif berhasil dihapus!";
                break;
        }
        header('Location: tarif.php');
        exit();
    }
}

$tarifs = $tarifModel->getAllTarif();

$page_title = "Kelola Tarif";
include '../layouts/header.php';
include '../layouts/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-money-bill me-2"></i>Kelola Tarif Parkir</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTarifModal">
            <i class="fas fa-plus me-2"></i>Tambah Tarif
        </button>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $_SESSION['success'];
    unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php
endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list me-2"></i>Daftar Tarif</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Kendaraan</th>
                            <th>Harga Per Jam</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tarifs as $index => $tarif): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($tarif['jenis_kendaraan']); ?></td>
                            <td>Rp <?php echo number_format($tarif['harga_per_jam'], 0, ',', '.'); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($tarif['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="editTarif(<?php echo htmlspecialchars(json_encode($tarif)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display:inline;" onsubmit="return confirmDelete()">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_tarif" value="<?php echo $tarif['id_tarif']; ?>">
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

<!-- Add Tarif Modal -->
<div class="modal fade" id="addTarifModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Tarif</h5>
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
                        <label class="form-label">Harga Per Jam (Rp)</label>
                        <input type="number" class="form-control" name="harga_per_jam" required>
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

<!-- Edit Tarif Modal -->
<div class="modal fade" id="editTarifModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Tarif</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id_tarif" id="edit_id_tarif">
                    <div class="mb-3">
                        <label class="form-label">Jenis Kendaraan</label>
                        <input type="text" class="form-control" name="jenis_kendaraan" id="edit_jenis_kendaraan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Per Jam (Rp)</label>
                        <input type="number" class="form-control" name="harga_per_jam" id="edit_harga_per_jam" required>
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
function editTarif(tarif) {
    document.getElementById('edit_id_tarif').value = tarif.id_tarif;
    document.getElementById('edit_jenis_kendaraan').value = tarif.jenis_kendaraan;
    document.getElementById('edit_harga_per_jam').value = tarif.harga_per_jam;
    const modal = new bootstrap.Modal(document.getElementById('editTarifModal'));
    modal.show();
}
</script>

    </div></div>
<?php include '../layouts/footer.php'; ?>
