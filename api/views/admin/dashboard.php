<?php
session_start();
require_once __DIR__ . '/../../controllers/AuthController.php';
AuthController::checkRole(['admin']);

require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Transaksi.php';

$userModel = new User($conn);
$transaksiModel = new Transaksi($conn);

// Get statistics
$total_admin = $userModel->countByRole('admin');
$total_petugas = $userModel->countByRole('petugas');
$total_owner = $userModel->countByRole('owner');
$total_pendapatan = $transaksiModel->getTotalPendapatan();
$total_transaksi = $transaksiModel->getTotalTransaksi();

$page_title = "Dashboard Admin";
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-home me-2"></i>Dashboard Admin</h2>
    
    <div class="row g-4">
        <!-- Stats Cards -->
        <div class="col-md-3">
            <div class="stats-card primary">
                <div class="icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3><?php echo $total_admin; ?></h3>
                <p>Total Admin</p>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card success">
                <div class="icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3><?php echo $total_petugas; ?></h3>
                <p>Total Petugas</p>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card warning">
                <div class="icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h3><?php echo $total_owner; ?></h3>
                <p>Total Owner</p>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card danger">
                <div class="icon">
                    <i class="fas fa-car"></i>
                </div>
                <h3><?php echo $total_transaksi; ?></h3>
                <p>Total Transaksi</p>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-money-bill-wave me-2"></i>Total Pendapatan</h5>
                </div>
                <div class="card-body">
                    <h2 class="text-primary">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h2>
                    <p class="text-muted mb-0">Total pendapatan dari semua transaksi</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Selamat Datang</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">Halo, <strong><?php echo $_SESSION['nama']; ?></strong>!</p>
                    <p class="mb-0">Anda login sebagai <span class="badge bg-primary"><?php echo strtoupper($_SESSION['role']); ?></span></p>
                    <hr>
                    <p class="mb-0 text-muted">Gunakan menu di sidebar untuk mengelola sistem parkir.</p>
                </div>
            </div>
        </div>
    </div>
</div>

    </div> <!-- Close content-wrapper -->
</div> <!-- Close main-content -->

<?php include __DIR__ . '/../layouts/footer.php'; ?>
