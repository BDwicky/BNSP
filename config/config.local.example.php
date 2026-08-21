<?php
/**
 * Local Server Configuration Override
 * Salin file ini menjadi config.local.php di server Anda:
 * cp config.local.example.php config.local.php
 * 
 * File config.local.php diabaikan oleh Git, sehingga TIDAK AKAN PERNAH
 * tertimpa saat melakukan git pull / git reset.
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'db_bnsp_inventaris');
define('DB_USER', 'bnsp_user');
define('DB_PASS', 'BnspPass123!');
define('DB_CHARSET', 'utf8mb4');

define('BASE_URL', 'https://bnsp.dwicky.dev');
