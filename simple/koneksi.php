<?php
/**
 * ==============================================================================
 * KONEKSI BASIS DATA (PHP NATIVE PDO)
 * Memenuhi Kriteria Uji BNSP:
 * - Unit 1: TIK.PR08.007.01 (Basis Data MySQL)
 * - Langkah Kerja 4: Pengaksesan Basis Data (PDO Connection & Exception)
 * - Langkah Kerja 8: Menerapkan Fungsi Buatan Sendiri
 * - Langkah Kerja 2: Mempersiapkan Security (Anti-XSS Sanitasi)
 * ==============================================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi Database
$host   = 'localhost';
$port   = '3306';
$dbname = 'db_bnsp_inventaris';
$user   = 'root';
$pass   = '';

// Daftar fallback otomatis (agar otomatis jalan di Localhost Windows maupun Ubuntu Server)
$candidateUsers = [
    ['user' => $user,        'pass' => $pass],
    ['user' => 'bnsp_user',  'pass' => 'BnspPass123!'],
    ['user' => 'root',       'pass' => 'root'],
];

$pdo = null;
$koneksiError = '';

// Langkah Kerja 4: Pengaksesan Database menggunakan PDO & try...catch
foreach ($candidateUsers as $cred) {
    try {
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $cred['user'], $cred['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        break;
    } catch (PDOException $e) {
        $koneksiError = $e->getMessage();
    }
}

if (!$pdo) {
    die("Koneksi Basis Data Gagal: " . htmlspecialchars($koneksiError));
}

// Langkah Kerja 8 & Langkah Kerja 2: Fungsi Buatan Sendiri untuk Sanitasi Output (Anti-XSS)
function clean($data) {
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

// Langkah Kerja 8: Fungsi Buatan Sendiri untuk Format Rupiah
function rupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}
