<?php
session_start();
require_once '../../controllers/AuthController.php';
AuthController::checkRole(['petugas']);

require_once '../../config/koneksi.php';
require_once '../../models/Transaksi.php';
require_once '../../models/Kendaraan.php';
require_once '../../models/Area.php';

$transaksiModel = new Transaksi($conn);
$kendaraanModel = new Kendaraan($conn);
$areaModel = new Area($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plat_nomor = strtoupper(sanitize($_POST['plat_nomor']));
    $id_kendaraan = $_POST['id_kendaraan'];
    $id_area = $_POST['id_area'];
    $id_petugas = $_SESSION['user_id'];

    // Check if vehicle is already parked
    $existing = $transaksiModel->getTransaksiAktif($plat_nomor);
    if ($existing) {
        $_SESSION['error'] = "Kendaraan dengan plat nomor $plat_nomor masih parkir!";
    }
    else {
        $transaksiModel->kendaraanMasuk($plat_nomor, $id_kendaraan, $id_area, $id_petugas);
        logActivity($conn, $_SESSION['user_id'], "Input kendaraan masuk: $plat_nomor");
        $_SESSION['success'] = "Kendaraan berhasil masuk!";
    }

    header('Location: masuk.php');
    exit();
}

$kendaraans = $kendaraanModel->getAllKendaraan();
$areas = $areaModel->getAllArea();

$page_title = "Kendaraan Masuk";
include '../layouts/header.php';
include '../layouts/sidebar.php';
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-sign-in-alt me-2"></i>Kendaraan Masuk</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $_SESSION['success'];
    unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php
endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $_SESSION['error'];
    unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php
endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-car me-2"></i>Form Kendaraan Masuk</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label">Plat Nomor</label>
                            <input type="text" class="form-control form-control-lg" name="plat_nomor" 
                                   placeholder="Contoh: B 1234 XYZ" required autofocus style="text-transform: uppercase;">
                            <small class="text-muted">Masukkan plat nomor kendaraan</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Jenis Kendaraan</label>
                            <select class="form-select form-select-lg" name="id_kendaraan" required>
                                <option value="">Pilih Jenis Kendaraan</option>
                                <?php foreach ($kendaraans as $k): ?>
                                    <option value="<?php echo $k['id_kendaraan']; ?>">
                                        <?php echo htmlspecialchars($k['jenis_kendaraan']); ?>
                                    </option>
                                <?php
endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Area Parkir</label>
                            <select class="form-select form-select-lg" name="id_area" required>
                                <option value="">Pilih Area Parkir</option>
                                <?php foreach ($areas as $a): ?>
                                    <option value="<?php echo $a['id_area']; ?>">
                                        <?php echo htmlspecialchars($a['nama_area']); ?> (Kapasitas: <?php echo $a['kapasitas']; ?>)
                                    </option>
                                <?php
endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-2"></i>Simpan Kendaraan Masuk
                            </button>
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-info-circle me-2"></i>Informasi</h5>
                </div>
                <div class="card-body">
                    <p><strong>Waktu Masuk:</strong></p>
                    <p class="text-primary fs-5" id="current-time"></p>
                    <hr>
                    <p><strong>Petugas:</strong></p>
                    <p><?php echo $_SESSION['nama']; ?></p>
                    <hr>
                    <p class="mb-0 text-muted small">
                        <i class="fas fa-lightbulb me-1"></i>
                        Pastikan data yang diinput sudah benar sebelum menyimpan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update current time
function updateTime() {
    const now = new Date();
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    };
    document.getElementById('current-time').textContent = now.toLocaleDateString('id-ID', options);
}
updateTime();
setInterval(updateTime, 1000);
</script>

    </div></div>
<?php include '../layouts/footer.php'; ?>
