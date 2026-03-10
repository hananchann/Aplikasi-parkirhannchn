<?php
session_start();
require_once __DIR__ . '/../../controllers/AuthController.php';
AuthController::checkRole(['petugas']);

require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../models/Transaksi.php';

$transaksiModel = new Transaksi($conn);

// Get transaction ID
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$id_transaksi = $_GET['id'];
$transaksi = $transaksiModel->getTransaksiById($id_transaksi);

if (!$transaksi || $transaksi['status'] !== 'selesai') {
    header('Location: dashboard.php');
    exit();
}

$page_title = "Struk Parkir";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 20px;
            }
        }
        
        .struk-container {
            max-width: 400px;
            margin: 30px auto;
            border: 2px solid #1E3A8A;
            border-radius: 10px;
            padding: 20px;
            background: white;
        }
        
        .struk-header {
            text-align: center;
            border-bottom: 2px dashed #1E3A8A;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .struk-header h2 {
            color: #1E3A8A;
            margin: 0;
            font-weight: 700;
        }
        
        .struk-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #ccc;
        }
        
        .struk-row:last-child {
            border-bottom: none;
        }
        
        .struk-label {
            font-weight: 600;
            color: #64748B;
        }
        
        .struk-value {
            font-weight: 600;
            text-align: right;
        }
        
        .struk-total {
            background: #1E3A8A;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }
        
        .struk-total h3 {
            margin: 0;
            font-size: 28px;
        }
        
        .struk-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #1E3A8A;
            font-size: 12px;
            color: #64748B;
        }
    </style>
</head>
<body>
    <div class="struk-container print-area">
        <div class="struk-header">
            <i class="fas fa-parking fa-3x text-primary mb-2"></i>
            <h2>SISTEM PARKIR</h2>
            <p class="mb-0">Struk Pembayaran</p>
        </div>
        
        <div class="struk-body">
            <div class="struk-row">
                <span class="struk-label">No. Transaksi:</span>
                <span class="struk-value">#<?php echo str_pad($transaksi['id_transaksi'], 6, '0', STR_PAD_LEFT); ?></span>
            </div>
            
            <div class="struk-row">
                <span class="struk-label">Plat Nomor:</span>
                <span class="struk-value"><strong><?php echo htmlspecialchars($transaksi['plat_nomor']); ?></strong></span>
            </div>
            
            <div class="struk-row">
                <span class="struk-label">Jenis Kendaraan:</span>
                <span class="struk-value"><?php echo htmlspecialchars($transaksi['jenis_kendaraan']); ?></span>
            </div>
            
            <div class="struk-row">
                <span class="struk-label">Area Parkir:</span>
                <span class="struk-value"><?php echo htmlspecialchars($transaksi['nama_area']); ?></span>
            </div>
            
            <div class="struk-row">
                <span class="struk-label">Waktu Masuk:</span>
                <span class="struk-value"><?php echo date('d/m/Y H:i', strtotime($transaksi['waktu_masuk'])); ?></span>
            </div>
            
            <div class="struk-row">
                <span class="struk-label">Waktu Keluar:</span>
                <span class="struk-value"><?php echo date('d/m/Y H:i', strtotime($transaksi['waktu_keluar'])); ?></span>
            </div>
            
            <div class="struk-row">
                <span class="struk-label">Durasi Parkir:</span>
                <span class="struk-value"><?php echo $transaksi['durasi']; ?> Jam</span>
            </div>
            
            <div class="struk-row">
                <span class="struk-label">Petugas:</span>
                <span class="struk-value"><?php echo htmlspecialchars($transaksi['nama_petugas']); ?></span>
            </div>
        </div>
        
        <div class="struk-total">
            <p class="mb-1">TOTAL BAYAR</p>
            <h3>Rp <?php echo number_format($transaksi['total_bayar'], 0, ',', '.'); ?></h3>
        </div>
        
        <div class="struk-footer">
            <p class="mb-1">Terima kasih atas kunjungan Anda</p>
            <p class="mb-0">Dicetak: <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
    </div>
    
    <div class="text-center no-print mb-4">
        <button onclick="window.print()" class="btn btn-primary btn-lg me-2">
            <i class="fas fa-print me-2"></i>Cetak Struk
        </button>
        <a href="dashboard.php" class="btn btn-secondary btn-lg">
            <i class="fas fa-home me-2"></i>Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
