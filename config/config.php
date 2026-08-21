<?php
/**
 * Konfigurasi Utama Aplikasi
 * Memenuhi Kriteria: Unit TIK.PR08.007.01 & TIK.PR08.009.01
 */

// Mulai session secara aman jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Muat Konfigurasi Khusus Server (config.local.php) jika ada
// File ini diabaikan oleh Git agar kredensial server tidak hilang saat git pull/update.
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// 2. Konfigurasi Database Default (Gunakan nilai dari config.local.php jika sudah didefinisikan)
defined('DB_HOST')    || define('DB_HOST', 'localhost');
defined('DB_PORT')    || define('DB_PORT', '3306');
defined('DB_NAME')    || define('DB_NAME', 'db_bnsp_inventaris');
defined('DB_USER')    || define('DB_USER', 'root');
defined('DB_PASS')    || define('DB_PASS', '');
defined('DB_CHARSET') || define('DB_CHARSET', 'utf8mb4');

// 3. Konfigurasi Aplikasi & Environment
defined('APP_NAME')    || define('APP_NAME', 'Smart Inventory Pro');
defined('APP_VERSION') || define('APP_VERSION', '1.0.0 (BNSP Skenario 3)');
defined('BASE_URL')    || define('BASE_URL', 'http://localhost:8000');

// 4. Autoloading Classes (Langkah Kerja 8: OOP & Class Structure)
spl_autoload_register(function ($className) {
    $file = __DIR__ . '/../classes/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// 5. Muat Helper Functions (Sanitasi, CSRF, Formatter Rupiah, dll)
require_once __DIR__ . '/../helpers/utils.php';
