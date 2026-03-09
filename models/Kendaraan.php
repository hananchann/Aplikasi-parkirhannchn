<?php
/**
 * Kendaraan Model
 * Handles vehicle type operations
 */

class Kendaraan
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Get all kendaraan
     */
    public function getAllKendaraan()
    {
        $stmt = $this->conn->prepare("SELECT * FROM kendaraan ORDER BY jenis_kendaraan ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get kendaraan by ID
     */
    public function getKendaraanById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM kendaraan WHERE id_kendaraan = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Create new kendaraan
     */
    public function createKendaraan($jenis_kendaraan, $keterangan)
    {
        $stmt = $this->conn->prepare("INSERT INTO kendaraan (jenis_kendaraan, keterangan) VALUES (?, ?)");
        $stmt->bind_param("ss", $jenis_kendaraan, $keterangan);
        return $stmt->execute();
    }

    /**
     * Update kendaraan
     */
    public function updateKendaraan($id, $jenis_kendaraan, $keterangan)
    {
        $stmt = $this->conn->prepare("UPDATE kendaraan SET jenis_kendaraan = ?, keterangan = ? WHERE id_kendaraan = ?");
        $stmt->bind_param("ssi", $jenis_kendaraan, $keterangan, $id);
        return $stmt->execute();
    }

    /**
     * Delete kendaraan
     */
    public function deleteKendaraan($id)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM kendaraan WHERE id_kendaraan = ?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }
        catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1451) { // Foreign key constraint fails
                return false;
            }
            throw $e;
        }
    }
}
?>
