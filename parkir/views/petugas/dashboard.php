<?php
session_start();
require_once '../../controllers/AuthController.php';
AuthController::checkRole(['petugas']);

require_once '../../config/koneksi.php';
require_once '../../models/Transaksi.php';

$transaksiModel = new Transaksi($conn);

// Get active parking list
$transaksi_aktif = $transaksiModel->getTransaksiAktifList();

$page_title = "Dashboard Petugas";
include '../layouts/header.php';
include '../layouts/sidebar.php';
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-home me-2"></i>Dashboard Petugas</h2>
    
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stats-card success">
                <div class="icon">
                    <i class="fas fa-car"></i>
                </div>
                <h3><?php echo count($transaksi_aktif); ?></h3>
                <p>Kendaraan Sedang Parkir</p>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="mb-3">Selamat Datang, <?php echo $_SESSION['nama']; ?>!</h5>
                    <div class="d-grid gap-2">
                        <a href="masuk.php" class="btn btn-success btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Kendaraan Masuk
                        </a>
                        <a href="keluar.php" class="btn btn-danger btn-lg">
                            <i class="fas fa-sign-out-alt me-2"></i>Kendaraan Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-parking me-2"></i>Kendaraan Sedang Parkir</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Plat Nomor</th>
                            <th>Jenis Kendaraan</th>
                            <th>Area</th>
                            <th>Waktu Masuk</th>
                            <th>Durasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($transaksi_aktif) > 0): ?>
                            <?php foreach ($transaksi_aktif as $index => $t):
        $waktu_masuk = strtotime($t['waktu_masuk']);
        $durasi_detik = time() - $waktu_masuk;
        $durasi_jam = floor($durasi_detik / 3600);
        $durasi_menit = floor(($durasi_detik % 3600) / 60);
?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><strong><?php echo htmlspecialchars($t['plat_nomor']); ?></strong></td>
                                <td><?php echo htmlspecialchars($t['jenis_kendaraan']); ?></td>
                                <td><?php echo htmlspecialchars($t['nama_area']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($t['waktu_masuk'])); ?></td>
                                <td><?php echo $durasi_jam . ' jam ' . $durasi_menit . ' menit'; ?></td>
                            </tr>
                            <?php
    endforeach; ?>
                        <?php
else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada kendaraan yang sedang parkir</td>
                            </tr>
                        <?php
endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    </div></div>
<?php include '../layouts/footer.php'; ?>
