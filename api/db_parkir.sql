-- ================================================
-- DATABASE: Sistem Informasi Parkir
-- ================================================

-- Create database
CREATE DATABASE IF NOT EXISTS db_parkir;
USE db_parkir;

-- ================================================
-- TABLE: users
-- ================================================
CREATE TABLE users (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'petugas', 'owner') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- TABLE: tarif
-- ================================================
CREATE TABLE tarif (
    id_tarif INT PRIMARY KEY AUTO_INCREMENT,
    jenis_kendaraan VARCHAR(50) NOT NULL,
    harga_per_jam INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- TABLE: area_parkir
-- ================================================
CREATE TABLE area_parkir (
    id_area INT PRIMARY KEY AUTO_INCREMENT,
    nama_area VARCHAR(100) NOT NULL,
    kapasitas INT NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- TABLE: kendaraan
-- ================================================
CREATE TABLE kendaraan (
    id_kendaraan INT PRIMARY KEY AUTO_INCREMENT,
    jenis_kendaraan VARCHAR(50) NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- TABLE: transaksi
-- ================================================
CREATE TABLE transaksi (
    id_transaksi INT PRIMARY KEY AUTO_INCREMENT,
    plat_nomor VARCHAR(20) NOT NULL,
    id_kendaraan INT NOT NULL,
    id_area INT NOT NULL,
    id_petugas INT NOT NULL,
    waktu_masuk DATETIME NOT NULL,
    waktu_keluar DATETIME NULL,
    durasi INT NULL,
    total_bayar INT NULL,
    status ENUM('parkir', 'selesai') DEFAULT 'parkir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kendaraan) REFERENCES kendaraan(id_kendaraan) ON DELETE RESTRICT,
    FOREIGN KEY (id_area) REFERENCES area_parkir(id_area) ON DELETE RESTRICT,
    FOREIGN KEY (id_petugas) REFERENCES users(id_user) ON DELETE RESTRICT,
    INDEX idx_kendaraan (id_kendaraan),
    INDEX idx_area (id_area),
    INDEX idx_petugas (id_petugas),
    INDEX idx_status (status),
    INDEX idx_plat (plat_nomor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- TABLE: log_aktivitas
-- ================================================
CREATE TABLE log_aktivitas (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    aktivitas TEXT NOT NULL,
    waktu DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
    INDEX idx_user (id_user),
    INDEX idx_waktu (waktu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- DEFAULT DATA: Users
-- Password: admin123, petugas123, owner123 (hashed with password_hash)
-- ================================================
INSERT INTO users (nama, username, password, role) VALUES
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Petugas Parkir', 'petugas', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petugas'),
('Owner Parkir', 'owner', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'owner');

-- ================================================
-- DEFAULT DATA: Jenis Kendaraan
-- ================================================
INSERT INTO kendaraan (jenis_kendaraan, keterangan) VALUES
('Motor', 'Sepeda motor dan sejenisnya'),
('Mobil', 'Mobil pribadi dan sejenisnya'),
('Truk', 'Truk dan kendaraan besar');

-- ================================================
-- DEFAULT DATA: Tarif Parkir
-- ================================================
INSERT INTO tarif (jenis_kendaraan, harga_per_jam) VALUES
('Motor', 2000),
('Mobil', 5000),
('Truk', 10000);

-- ================================================
-- DEFAULT DATA: Area Parkir
-- ================================================
INSERT INTO area_parkir (nama_area, kapasitas, keterangan) VALUES
('Area A', 50, 'Area parkir motor'),
('Area B', 30, 'Area parkir mobil'),
('Area C', 10, 'Area parkir truk dan kendaraan besar');

-- ================================================
-- SAMPLE DATA: Transaksi (for testing)
-- ================================================
INSERT INTO transaksi (plat_nomor, id_kendaraan, id_area, id_petugas, waktu_masuk, waktu_keluar, durasi, total_bayar, status) VALUES
('B 1234 XYZ', 2, 2, 2, '2026-02-11 08:00:00', '2026-02-11 10:00:00', 2, 10000, 'selesai'),
('B 5678 ABC', 1, 1, 2, '2026-02-11 09:00:00', '2026-02-11 12:00:00', 3, 6000, 'selesai'),
('B 9012 DEF', 2, 2, 2, '2026-02-11 10:00:00', NULL, NULL, NULL, 'parkir');

-- ================================================
-- SAMPLE DATA: Log Aktivitas
-- ================================================
INSERT INTO log_aktivitas (id_user, aktivitas, waktu) VALUES
(1, 'Login ke sistem', '2026-02-11 08:00:00'),
(2, 'Login ke sistem', '2026-02-11 08:05:00'),
(2, 'Input kendaraan masuk: B 1234 XYZ', '2026-02-11 08:10:00');

-- ================================================
-- END OF DATABASE SCRIPT
-- ================================================
