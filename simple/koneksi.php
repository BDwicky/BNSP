<?php
/**
 * Koneksi Database Sederhana (PDO)
 * Memenuhi:
 * - Unit TIK.PR08.007.01 (Basis Data MySQL)
 * - Langkah Kerja 4: Pengaksesan Basis Data
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek config server lokal jika ada
if (file_exists(__DIR__ . '/../config/config.local.php')) {
    require_once __DIR__ . '/../config/config.local.php';
}

$host = defined('DB_HOST') ? DB_HOST : 'localhost';
$port = defined('DB_PORT') ? DB_PORT : '3306';
$dbname = defined('DB_NAME') ? DB_NAME : 'db_bnsp_inventaris';
$user = defined('DB_USER') ? DB_USER : 'root';
$pass = defined('DB_PASS') ? DB_PASS : '';

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}

// Fungsi proteksi XSS sederhana
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Format Rupiah sederhana
function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
