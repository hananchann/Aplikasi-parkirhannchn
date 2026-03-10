<?php
session_start();
require_once __DIR__ . '/../../controllers/AuthController.php';
AuthController::checkRole(['owner']);

require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../models/Transaksi.php';

$transaksiModel = new Transaksi($conn);

// Get date filter
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get data
$transaksi_list = $transaksiModel->getRekapTransaksi($start_date, $end_date);
$total_pendapatan = $transaksiModel->getTotalPendapatan($start_date, $end_date);
$total_transaksi = $transaksiModel->getTotalTransaksi($start_date, $end_date);

$page_title = "Dashboard Owner";
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-chart-line me-2"></i>Dashboard Owner</h2>
    
    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-filter me-2"></i>Filter Laporan</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="start_date" 
                           value="<?php echo $start_date; ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" name="end_date" 
                           value="<?php echo $end_date; ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stats-card success">
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h3>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                <p>Total Pendapatan</p>
                <small class="text-muted">
                    <?php echo date('d/m/Y', strtotime($start_date)); ?> - <?php echo date('d/m/Y', strtotime($end_date)); ?>
                </small>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="stats-card primary">
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3><?php echo $total_transaksi; ?></h3>
                <p>Total Transaksi</p>
                <small class="text-muted">
                    <?php echo date('d/m/Y', strtotime($start_date)); ?> - <?php echo date('d/m/Y', strtotime($end_date)); ?>
                </small>
            </div>
        </div>
    </div>
    
    <!-- Transaction Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-table me-2"></i>Detail Transaksi</h5>
            <button onclick="window.print()" class="btn btn-success btn-sm no-print">
                <i class="fas fa-print me-2"></i>Cetak Laporan
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Plat Nomor</th>
                            <th>Jenis Kendaraan</th>
                            <th>Area</th>
                            <th>Durasi</th>
                            <th>Total Bayar</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($transaksi_list) > 0): ?>
                            <?php foreach ($transaksi_list as $index => $t): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($t['waktu_keluar'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($t['plat_nomor']); ?></strong></td>
                                <td><?php echo htmlspecialchars($t['jenis_kendaraan']); ?></td>
                                <td><?php echo htmlspecialchars($t['nama_area']); ?></td>
                                <td><?php echo $t['durasi']; ?> jam</td>
                                <td>Rp <?php echo number_format($t['total_bayar'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($t['nama_petugas']); ?></td>
                            </tr>
                            <?php
    endforeach; ?>
                            <tr class="table-active">
                                <td colspan="6" class="text-end"><strong>TOTAL:</strong></td>
                                <td colspan="2"><strong>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></strong></td>
                            </tr>
                        <?php
else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">Tidak ada transaksi pada periode ini</td>
                            </tr>
                        <?php
endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Summary Info -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Laporan</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Periode:</strong></p>
                            <p><?php echo date('d F Y', strtotime($start_date)); ?> - <?php echo date('d F Y', strtotime($end_date)); ?></p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Total Transaksi:</strong></p>
                            <p><?php echo $total_transaksi; ?> transaksi</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Rata-rata Pendapatan:</strong></p>
                            <p>Rp <?php echo $total_transaksi > 0 ? number_format($total_pendapatan / $total_transaksi, 0, ',', '.') : 0; ?> per transaksi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, .sidebar, .topbar {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
    }
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>

    </div></div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
