<?php
/**
 * Konfigurasi Utama Aplikasi Sistem Inventaris BNSP
 * Memenuhi Standar Unit: TIK.PR08.007.01 & TIK.PR08.009.01
 */

// Memulai session PHP jika belum aktif (Langkah Kerja 6 & 7: Variabel Internal $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi Database (MySQL)
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'db_bnsp_inventaris');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Konfigurasi Aplikasi
define('APP_NAME', 'Smart Inventory Pro');
define('APP_VERSION', '1.0.0 (BNSP Skenario 3)');
define('BASE_URL', 'http://localhost:8000');

// Autoload Classes sederhana
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load Helpers
require_once __DIR__ . '/../helpers/utils.php';
