<?php
session_start();
require_once __DIR__ . '/../../controllers/AuthController.php';
AuthController::checkRole(['petugas']);

require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../models/Transaksi.php';

$transaksiModel = new Transaksi($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plat_nomor = strtoupper(sanitize($_POST['plat_nomor']));
    $waktu_keluar = date('Y-m-d H:i:s');

    // Get active transaction
    $transaksi = $transaksiModel->getTransaksiAktif($plat_nomor);

    if (!$transaksi) {
        $_SESSION['error'] = "Kendaraan dengan plat nomor $plat_nomor tidak ditemukan atau sudah keluar!";
    }
    else {
        // Process exit
        $transaksiModel->kendaraanKeluar($transaksi['id_transaksi'], $waktu_keluar);
        logActivity($conn, $_SESSION['user_id'], "Proses kendaraan keluar: $plat_nomor");

        // Redirect to struk
        header('Location: struk.php?id=' . $transaksi['id_transaksi']);
        exit();
    }

    header('Location: keluar.php');
    exit();
}

// Search result
$transaksi = null;
if (isset($_GET['search'])) {
    $plat_nomor = strtoupper(sanitize($_GET['plat_nomor']));
    $transaksi = $transaksiModel->getTransaksiAktif($plat_nomor);
    if (!$transaksi) {
        $_SESSION['error'] = "Kendaraan dengan plat nomor $plat_nomor tidak ditemukan!";
    }
}

$page_title = "Kendaraan Keluar";
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-sign-out-alt me-2"></i>Kendaraan Keluar</h2>
    
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
            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-search me-2"></i>Cari Kendaraan</h5>
                </div>
                <div class="card-body">
                    <form method="GET">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" name="plat_nomor" 
                                   placeholder="Masukkan plat nomor..." required autofocus 
                                   style="text-transform: uppercase;"
                                   value="<?php echo isset($_GET['plat_nomor']) ? htmlspecialchars($_GET['plat_nomor']) : ''; ?>">
                            <button type="submit" name="search" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Transaction Details -->
            <?php if ($transaksi):
    $waktu_masuk = strtotime($transaksi['waktu_masuk']);
    $waktu_sekarang = time();
    $durasi_detik = $waktu_sekarang - $waktu_masuk;
    $durasi_jam = ceil($durasi_detik / 3600);

    // Get tarif
    $stmt = $conn->prepare("SELECT harga_per_jam FROM tarif WHERE jenis_kendaraan = ?");
    $stmt->bind_param("s", $transaksi['jenis_kendaraan']);
    $stmt->execute();
    $tarif_result = $stmt->get_result()->fetch_assoc();
    $harga_per_jam = $tarif_result['harga_per_jam'];
    $total_bayar = $harga_per_jam * $durasi_jam;
?>
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5><i class="fas fa-car me-2"></i>Detail Kendaraan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Plat Nomor:</th>
                            <td><h4 class="text-primary mb-0"><?php echo htmlspecialchars($transaksi['plat_nomor']); ?></h4></td>
                        </tr>
                        <tr>
                            <th>Jenis Kendaraan:</th>
                            <td><?php echo htmlspecialchars($transaksi['jenis_kendaraan']); ?></td>
                        </tr>
                        <tr>
                            <th>Area Parkir:</th>
                            <td><?php echo htmlspecialchars($transaksi['nama_area']); ?></td>
                        </tr>
                        <tr>
                            <th>Waktu Masuk:</th>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($transaksi['waktu_masuk'])); ?></td>
                        </tr>
                        <tr>
                            <th>Waktu Keluar:</th>
                            <td><?php echo date('d/m/Y H:i:s'); ?></td>
                        </tr>
                        <tr>
                            <th>Durasi Parkir:</th>
                            <td><strong><?php echo $durasi_jam; ?> Jam</strong></td>
                        </tr>
                        <tr>
                            <th>Tarif Per Jam:</th>
                            <td>Rp <?php echo number_format($harga_per_jam, 0, ',', '.'); ?></td>
                        </tr>
                        <tr class="table-active">
                            <th>Total Bayar:</th>
                            <td><h3 class="text-danger mb-0">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></h3></td>
                        </tr>
                    </table>
                    
                    <form method="POST" class="mt-4">
                        <input type="hidden" name="plat_nomor" value="<?php echo $transaksi['plat_nomor']; ?>">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-check me-2"></i>Proses Keluar & Cetak Struk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php
endif; ?>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-info-circle me-2"></i>Informasi</h5>
                </div>
                <div class="card-body">
                    <p><strong>Waktu Sekarang:</strong></p>
                    <p class="text-primary fs-5" id="current-time"></p>
                    <hr>
                    <p><strong>Petugas:</strong></p>
                    <p><?php echo $_SESSION['nama']; ?></p>
                    <hr>
                    <p class="mb-0 text-muted small">
                        <i class="fas fa-lightbulb me-1"></i>
                        Durasi parkir dihitung per jam (dibulatkan ke atas). Minimum 1 jam.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
<?php include __DIR__ . '/../layouts/footer.php'; ?>
