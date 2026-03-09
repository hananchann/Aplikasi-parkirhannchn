<?php
session_start();
require_once '../../controllers/AuthController.php';
AuthController::checkRole(['admin']);

require_once '../../config/koneksi.php';
require_once '../../models/Area.php';

$areaModel = new Area($conn);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $areaModel->createArea(
                    sanitize($_POST['nama_area']),
                    $_POST['kapasitas'],
                    sanitize($_POST['keterangan'])
                );
                logActivity($conn, $_SESSION['user_id'], "Menambah area: " . $_POST['nama_area']);
                $_SESSION['success'] = "Area berhasil ditambahkan!";
                break;

            case 'update':
                $areaModel->updateArea(
                    $_POST['id_area'],
                    sanitize($_POST['nama_area']),
                    $_POST['kapasitas'],
                    sanitize($_POST['keterangan'])
                );
                logActivity($conn, $_SESSION['user_id'], "Mengupdate area: " . $_POST['nama_area']);
                $_SESSION['success'] = "Area berhasil diupdate!";
                break;

            case 'delete':
                $area = $areaModel->getAreaById($_POST['id_area']);
                $areaModel->deleteArea($_POST['id_area']);
                logActivity($conn, $_SESSION['user_id'], "Menghapus area: " . $area['nama_area']);
                $_SESSION['success'] = "Area berhasil dihapus!";
                break;
        }
        header('Location: area.php');
        exit();
    }
}

$areas = $areaModel->getAllArea();

$page_title = "Kelola Area";
include '../layouts/header.php';
include '../layouts/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-map-marked-alt me-2"></i>Kelola Area Parkir</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAreaModal">
            <i class="fas fa-plus me-2"></i>Tambah Area
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
            <h5><i class="fas fa-list me-2"></i>Daftar Area</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Area</th>
                            <th>Kapasitas</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($areas as $index => $area): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($area['nama_area']); ?></td>
                            <td><?php echo $area['kapasitas']; ?> kendaraan</td>
                            <td><?php echo htmlspecialchars($area['keterangan']); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="editArea(<?php echo htmlspecialchars(json_encode($area)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display:inline;" onsubmit="return confirmDelete()">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_area" value="<?php echo $area['id_area']; ?>">
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

<!-- Add Area Modal -->
<div class="modal fade" id="addAreaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Area</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label class="form-label">Nama Area</label>
                        <input type="text" class="form-control" name="nama_area" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas</label>
                        <input type="number" class="form-control" name="kapasitas" required>
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

<!-- Edit Area Modal -->
<div class="modal fade" id="editAreaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Area</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id_area" id="edit_id_area">
                    <div class="mb-3">
                        <label class="form-label">Nama Area</label>
                        <input type="text" class="form-control" name="nama_area" id="edit_nama_area" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas</label>
                        <input type="number" class="form-control" name="kapasitas" id="edit_kapasitas" required>
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
function editArea(area) {
    document.getElementById('edit_id_area').value = area.id_area;
    document.getElementById('edit_nama_area').value = area.nama_area;
    document.getElementById('edit_kapasitas').value = area.kapasitas;
    document.getElementById('edit_keterangan').value = area.keterangan;
    const modal = new bootstrap.Modal(document.getElementById('editAreaModal'));
    modal.show();
}
</script>

    </div></div>
<?php include '../layouts/footer.php'; ?>
