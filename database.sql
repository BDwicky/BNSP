-- ==============================================================================
-- SKENARIO 3 UJI KOMPETENSI BNSP: PEMROGRAMAN WEB
-- Unit: TIK.PR08.007.01 (Membuat Program Basis Data Berbasis MySQL/PostgreSQL)
-- Unit: TIK.PR08.009.01 (Membuat Program Aplikasi Web Berbasis PHP)
-- Basis Data: db_bnsp_inventaris
-- ==============================================================================

-- 1. DDL: Membuat Database
CREATE DATABASE IF NOT EXISTS `db_bnsp_inventaris`
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `db_bnsp_inventaris`;

-- 2. DDL: Tabel `users` (Otentikasi & Keamanan)
DROP TABLE IF EXISTS `transaksi`;
DROP TABLE IF EXISTS `produk`;
DROP TABLE IF EXISTS `kategori`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `nama_lengkap` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'operator') DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. DDL: Tabel `kategori` (Master Data Kategori)
CREATE TABLE `kategori` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kode_kategori` VARCHAR(20) NOT NULL UNIQUE,
    `nama_kategori` VARCHAR(100) NOT NULL,
    `deskripsi` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. DDL: Tabel `produk` (Relasi ke `kategori` dengan Foreign Key)
CREATE TABLE `produk` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kategori_id` INT NOT NULL,
    `kode_produk` VARCHAR(30) NOT NULL UNIQUE,
    `nama_produk` VARCHAR(150) NOT NULL,
    `harga_beli` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `harga_jual` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `stok` INT NOT NULL DEFAULT 0,
    `satuan` VARCHAR(20) NOT NULL DEFAULT 'Unit',
    `status` ENUM('tersedia', 'menipis', 'habis') DEFAULT 'tersedia',
    `gambar` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_produk_kategori` 
        FOREIGN KEY (`kategori_id`) 
        REFERENCES `kategori` (`id`) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    INDEX `idx_kode_produk` (`kode_produk`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB;

-- 5. DDL: Tabel `transaksi` (Log Transaksi Masuk/Keluar)
CREATE TABLE `transaksi` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kode_transaksi` VARCHAR(50) NOT NULL UNIQUE,
    `produk_id` INT NOT NULL,
    `tipe_transaksi` ENUM('masuk', 'keluar') NOT NULL,
    `jumlah` INT NOT NULL,
    `total_harga` DECIMAL(12,2) NOT NULL,
    `keterangan` VARCHAR(255) NULL,
    `tanggal` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_transaksi_produk` 
        FOREIGN KEY (`produk_id`) 
        REFERENCES `produk` (`id`) 
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================================================================
-- DML: SEED DATA (DATA AWAL UNTUK DEMO & PENGUJIAN ASESOR)
-- ==============================================================================

-- Seed Users (Password default: 'admin123' di-hash menggunakan bcrypt/password_hash)
INSERT INTO `users` (`username`, `password`, `nama_lengkap`, `role`) VALUES
('admin', '$2y$10$aapKB9.k9jwPCzoxnIOXwOUU6C17w4cMcDiz3ltMURt7pjw78gCxK', 'Administrator BNSP', 'admin'),
('operator', '$2y$10$aapKB9.k9jwPCzoxnIOXwOUU6C17w4cMcDiz3ltMURt7pjw78gCxK', 'Staff Operator', 'operator');
-- Catatan: Hash di atas mewakili string 'admin123'

-- Seed Kategori
INSERT INTO `kategori` (`kode_kategori`, `nama_kategori`, `deskripsi`) VALUES
('KAT-ELK', 'Elektronik & Gadget', 'Peralatan elektronik komputer, laptop, dan peripheral pendukung'),
('KAT-ATK', 'Alat Tulis Kantor', 'Perlengkapan alat tulis kantor, kertas, dan dokumen kerja'),
('KAT-NET', 'Jaringan & Server', 'Perangkat jaringan seperti router, switch, kabel LAN, dan access point'),
('KAT-ACS', 'Aksesoris & Storage', 'Flashdisk, harddisk eksternal, mouse, dan keyboard');

-- Seed Produk
INSERT INTO `produk` (`kategori_id`, `kode_produk`, `nama_produk`, `harga_beli`, `harga_jual`, `stok`, `satuan`, `status`) VALUES
(1, 'PRD-001', 'Laptop Asus ExpertBook Core i5', 8500000.00, 9800000.00, 15, 'Unit', 'tersedia'),
(1, 'PRD-002', 'Monitor Dell 24 Inch Full HD IPS', 1800000.00, 2250000.00, 8, 'Unit', 'tersedia'),
(2, 'PRD-003', 'Kertas HVS A4 80gr PaperOne (Box)', 210000.00, 245000.00, 3, 'Box', 'menipis'),
(2, 'PRD-004', 'Ballpoint Gel Hitam Pilot G2 (Lusin)', 120000.00, 150000.00, 25, 'Lusin', 'tersedia'),
(3, 'PRD-005', 'Router Mikrotik RB750Gr3', 680000.00, 820000.00, 12, 'Unit', 'tersedia'),
(3, 'PRD-006', 'Kabel LAN Cat6 Belden (Roll 305m)', 1450000.00, 1750000.00, 2, 'Roll', 'menipis'),
(4, 'PRD-007', 'SSD Eksternal Samsung T7 1TB', 1600000.00, 1950000.00, 0, 'Pcs', 'habis'),
(4, 'PRD-008', 'Logitech Wireless Keyboard & Mouse MK220', 230000.00, 285000.00, 20, 'Set', 'tersedia');

-- Seed Transaksi
INSERT INTO `transaksi` (`kode_transaksi`, `produk_id`, `tipe_transaksi`, `jumlah`, `total_harga`, `keterangan`, `tanggal`) VALUES
('TRX-202608-001', 1, 'masuk', 10, 85000000.00, 'Pengadaan inventaris awal laptop', '2026-08-01'),
('TRX-202608-002', 1, 'keluar', 2, 19600000.00, 'Distribusi divisi IT Support', '2026-08-05'),
('TRX-202608-003', 3, 'keluar', 5, 1225000.00, 'Pengambilan ATK operasional kantor', '2026-08-10'),
('TRX-202608-004', 5, 'masuk', 12, 8160000.00, 'Restock router mikrotik', '2026-08-15');

-- ==============================================================================
-- CONTOH SINTAKS KHUSUS SQL UNTUK DEMONSTRASI KE ASESOR (Langkah Kerja 3):
-- ==============================================================================
-- 1. INNER JOIN dengan Agregasi:
-- SELECT k.nama_kategori, COUNT(p.id) AS total_produk, SUM(p.stok) AS total_stok, AVG(p.harga_jual) AS rata_harga
-- FROM kategori k
-- LEFT JOIN produk p ON k.id = p.kategori_id
-- GROUP BY k.id, k.nama_kategori;
--
-- 2. Filter & Pencarian dengan LIKE:
-- SELECT p.*, k.nama_kategori 
-- FROM produk p 
-- INNER JOIN kategori k ON p.kategori_id = k.id 
-- WHERE p.nama_produk LIKE '%Laptop%' OR p.kode_produk LIKE '%PRD%';
-- ==============================================================================
