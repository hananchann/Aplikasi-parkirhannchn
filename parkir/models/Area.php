<?php
/**
 * Area Model
 * Handles parking area operations
 */

class Area
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Get all areas
     */
    public function getAllArea()
    {
        $stmt = $this->conn->prepare("SELECT * FROM area_parkir ORDER BY nama_area ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get area by ID
     */
    public function getAreaById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM area_parkir WHERE id_area = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Create new area
     */
    public function createArea($nama_area, $kapasitas, $keterangan)
    {
        $stmt = $this->conn->prepare("INSERT INTO area_parkir (nama_area, kapasitas, keterangan) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $nama_area, $kapasitas, $keterangan);
        return $stmt->execute();
    }

    /**
     * Update area
     */
    public function updateArea($id, $nama_area, $kapasitas, $keterangan)
    {
        $stmt = $this->conn->prepare("UPDATE area_parkir SET nama_area = ?, kapasitas = ?, keterangan = ? WHERE id_area = ?");
        $stmt->bind_param("sisi", $nama_area, $kapasitas, $keterangan, $id);
        return $stmt->execute();
    }

    /**
     * Delete area
     */
    public function deleteArea($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM area_parkir WHERE id_area = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Get area capacity status
     */
    public function getAreaStatus($id)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                a.nama_area,
                a.kapasitas,
                COUNT(t.id_transaksi) as terisi
            FROM area_parkir a
            LEFT JOIN transaksi t ON a.id_area = t.id_area AND t.status = 'parkir'
            WHERE a.id_area = ?
            GROUP BY a.id_area
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>
