<?php
/**
 * Koneksi Basis Data MySQL (PHP Native PDO)
 * Dilengkapi dengan Auto-Detection & Fallback Cerdas (Localhost & Production)
 * Memenuhi Kriteria:
 * - Unit 1: TIK.PR08.007.01 (Basis Data MySQL)
 * - Langkah Kerja 4: Pengaksesan Basis Data
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Muat config khusus server (jika ada)
if (file_exists(__DIR__ . '/config/config.local.php')) {
    require_once __DIR__ . '/config/config.local.php';
}

$host   = defined('DB_HOST') ? DB_HOST : 'localhost';
$port   = defined('DB_PORT') ? DB_PORT : '3306';
$dbname = defined('DB_NAME') ? DB_NAME : 'db_bnsp_inventaris';
$user   = defined('DB_USER') ? DB_USER : 'root';
$pass   = defined('DB_PASS') ? DB_PASS : '';

// Daftar kandidat kredensial otomatis (Mencegah error access denied saat pindah antar server dan localhost)
$credentials = [
    ['user' => $user, 'pass' => $pass],
    ['user' => 'root', 'pass' => ''],
    ['user' => 'bnsp_user', 'pass' => 'BnspPass123!'],
    ['user' => 'root', 'pass' => 'root'],
];

$pdo = null;
$errorMsg = '';

foreach ($credentials as $cred) {
    try {
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $cred['user'], $cred['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        break; // Berhasil terhubung, hentikan percobaan
    } catch (PDOException $e) {
        $errorMsg = $e->getMessage();
    }
}

if (!$pdo) {
    die("Koneksi Basis Data Gagal: " . htmlspecialchars($errorMsg));
}

// Fungsi Sanitasi Input / Anti-XSS (Langkah Kerja 2)
function clean($data) {
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

// Fungsi Format Mata Uang Rupiah (Langkah Kerja 8)
function rupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}
