<?php
/**
 * Tarif Model
 * Handles parking rate operations
 */

class Tarif
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Get all tarif
     */
    public function getAllTarif()
    {
        $stmt = $this->conn->prepare("SELECT * FROM tarif ORDER BY created_at DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get tarif by jenis kendaraan
     */
    public function getTarifByJenis($jenis)
    {
        $stmt = $this->conn->prepare("SELECT harga_per_jam FROM tarif WHERE jenis_kendaraan = ?");
        $stmt->bind_param("s", $jenis);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['harga_per_jam'] : 0;
    }

    /**
     * Get tarif by ID
     */
    public function getTarifById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tarif WHERE id_tarif = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Create new tarif
     */
    public function createTarif($jenis_kendaraan, $harga_per_jam)
    {
        $stmt = $this->conn->prepare("INSERT INTO tarif (jenis_kendaraan, harga_per_jam) VALUES (?, ?)");
        $stmt->bind_param("si", $jenis_kendaraan, $harga_per_jam);
        return $stmt->execute();
    }

    /**
     * Update tarif
     */
    public function updateTarif($id, $jenis_kendaraan, $harga_per_jam)
    {
        $stmt = $this->conn->prepare("UPDATE tarif SET jenis_kendaraan = ?, harga_per_jam = ? WHERE id_tarif = ?");
        $stmt->bind_param("sii", $jenis_kendaraan, $harga_per_jam, $id);
        return $stmt->execute();
    }

    /**
     * Delete tarif
     */
    public function deleteTarif($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM tarif WHERE id_tarif = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
