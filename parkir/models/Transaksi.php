<?php
/**
 * Transaksi Model
 * Handles parking transaction operations
 */

class Transaksi
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Record vehicle entry
     */
    public function kendaraanMasuk($plat_nomor, $id_kendaraan, $id_area, $id_petugas)
    {
        $waktu_masuk = date('Y-m-d H:i:s');
        $stmt = $this->conn->prepare("INSERT INTO transaksi (plat_nomor, id_kendaraan, id_area, id_petugas, waktu_masuk, status) VALUES (?, ?, ?, ?, ?, 'parkir')");
        $stmt->bind_param("siiis", $plat_nomor, $id_kendaraan, $id_area, $id_petugas, $waktu_masuk);
        return $stmt->execute();
    }

    /**
     * Get active parking by plate number
     */
    public function getTransaksiAktif($plat_nomor)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                t.*,
                k.jenis_kendaraan,
                a.nama_area,
                u.nama as nama_petugas
            FROM transaksi t
            JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan
            JOIN area_parkir a ON t.id_area = a.id_area
            JOIN users u ON t.id_petugas = u.id_user
            WHERE t.plat_nomor = ? AND t.status = 'parkir'
            ORDER BY t.waktu_masuk DESC
            LIMIT 1
        ");
        $stmt->bind_param("s", $plat_nomor);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Process vehicle exit
     */
    public function kendaraanKeluar($id_transaksi, $waktu_keluar)
    {
        // Get transaction data
        $stmt = $this->conn->prepare("
            SELECT t.waktu_masuk, k.jenis_kendaraan 
            FROM transaksi t
            JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan
            WHERE t.id_transaksi = ?
        ");
        $stmt->bind_param("i", $id_transaksi);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if (!$data) {
            return false;
        }

        // Calculate duration and cost
        $durasi = $this->hitungDurasi($data['waktu_masuk'], $waktu_keluar);
        $total_bayar = $this->hitungBiaya($data['jenis_kendaraan'], $durasi);

        // Update transaction
        $stmt = $this->conn->prepare("UPDATE transaksi SET waktu_keluar = ?, durasi = ?, total_bayar = ?, status = 'selesai' WHERE id_transaksi = ?");
        $stmt->bind_param("siii", $waktu_keluar, $durasi, $total_bayar, $id_transaksi);
        return $stmt->execute();
    }

    /**
     * Calculate parking duration in hours (rounded up)
     */
    private function hitungDurasi($waktu_masuk, $waktu_keluar)
    {
        $masuk = strtotime($waktu_masuk);
        $keluar = strtotime($waktu_keluar);
        $selisih_detik = $keluar - $masuk;
        $jam = ceil($selisih_detik / 3600); // Round up to nearest hour
        return $jam > 0 ? $jam : 1; // Minimum 1 hour
    }

    /**
     * Calculate parking cost
     */
    private function hitungBiaya($jenis_kendaraan, $durasi)
    {
        $stmt = $this->conn->prepare("SELECT harga_per_jam FROM tarif WHERE jenis_kendaraan = ?");
        $stmt->bind_param("s", $jenis_kendaraan);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $harga_per_jam = $row ? $row['harga_per_jam'] : 0;
        return $harga_per_jam * $durasi;
    }

    /**
     * Get transaction by ID
     */
    public function getTransaksiById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                t.*,
                k.jenis_kendaraan,
                a.nama_area,
                u.nama as nama_petugas
            FROM transaksi t
            JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan
            JOIN area_parkir a ON t.id_area = a.id_area
            JOIN users u ON t.id_petugas = u.id_user
            WHERE t.id_transaksi = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Get today's transactions
     */
    public function getTransaksiHariIni()
    {
        $stmt = $this->conn->prepare("
            SELECT 
                t.*,
                k.jenis_kendaraan,
                a.nama_area
            FROM transaksi t
            JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan
            JOIN area_parkir a ON t.id_area = a.id_area
            WHERE DATE(t.waktu_masuk) = CURDATE()
            ORDER BY t.waktu_masuk DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get active parking list
     */
    public function getTransaksiAktifList()
    {
        $stmt = $this->conn->prepare("
            SELECT 
                t.*,
                k.jenis_kendaraan,
                a.nama_area
            FROM transaksi t
            JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan
            JOIN area_parkir a ON t.id_area = a.id_area
            WHERE t.status = 'parkir'
            ORDER BY t.waktu_masuk DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get transaction recap with date filter
     */
    public function getRekapTransaksi($start_date = null, $end_date = null)
    {
        if ($start_date && $end_date) {
            $stmt = $this->conn->prepare("
                SELECT 
                    t.*,
                    k.jenis_kendaraan,
                    a.nama_area,
                    u.nama as nama_petugas
                FROM transaksi t
                JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan
                JOIN area_parkir a ON t.id_area = a.id_area
                JOIN users u ON t.id_petugas = u.id_user
                WHERE t.status = 'selesai' 
                AND DATE(t.waktu_masuk) BETWEEN ? AND ?
                ORDER BY t.waktu_keluar DESC
                LIMIT 100
            ");
            $stmt->bind_param("ss", $start_date, $end_date);
        }
        else {
            $stmt = $this->conn->prepare("
                SELECT 
                    t.*,
                    k.jenis_kendaraan,
                    a.nama_area,
                    u.nama as nama_petugas
                FROM transaksi t
                JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan
                JOIN area_parkir a ON t.id_area = a.id_area
                JOIN users u ON t.id_petugas = u.id_user
                WHERE t.status = 'selesai'
                ORDER BY t.waktu_keluar DESC
                LIMIT 100
            ");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get total revenue
     */
    public function getTotalPendapatan($start_date = null, $end_date = null)
    {
        if ($start_date && $end_date) {
            $stmt = $this->conn->prepare("
                SELECT COALESCE(SUM(total_bayar), 0) as total 
                FROM transaksi 
                WHERE status = 'selesai' 
                AND DATE(waktu_masuk) BETWEEN ? AND ?
            ");
            $stmt->bind_param("ss", $start_date, $end_date);
        }
        else {
            $stmt = $this->conn->prepare("
                SELECT COALESCE(SUM(total_bayar), 0) as total 
                FROM transaksi 
                WHERE status = 'selesai'
            ");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    /**
     * Get total transaction count
     */
    public function getTotalTransaksi($start_date = null, $end_date = null)
    {
        if ($start_date && $end_date) {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as total 
                FROM transaksi 
                WHERE status = 'selesai' 
                AND DATE(waktu_masuk) BETWEEN ? AND ?
            ");
            $stmt->bind_param("ss", $start_date, $end_date);
        }
        else {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as total 
                FROM transaksi 
                WHERE status = 'selesai'
            ");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>
