<?php
/**
 * Modul Informasi Kebutuhan Software & Lingkungan Eksekusi
 * Memenuhi Langkah Kerja 1 & 5: Menjelaskan Kebutuhan Software
 */

function getSystemEnvironmentInfo(): array {
    return [
        'web_server' => [
            'nama' => $_SERVER['SERVER_SOFTWARE'] ?? 'PHP Development Server / Apache',
            'host' => $_SERVER['HTTP_HOST'] ?? 'localhost:8000',
            'protokol' => $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1',
            'ip_klien' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ],
        'php_environment' => [
            'versi' => PHP_VERSION,
            'sapi' => PHP_SAPI,
            'os' => PHP_OS . ' (' . php_uname('s') . ' ' . php_uname('r') . ')',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . ' detik',
            'extensions_loaded' => [
                'pdo' => extension_loaded('pdo'),
                'pdo_mysql' => extension_loaded('pdo_mysql'),
                'mysqli' => extension_loaded('mysqli'),
                'session' => extension_loaded('session'),
                'mbstring' => extension_loaded('mbstring'),
                'openssl' => extension_loaded('openssl'),
                'json' => extension_loaded('json')
            ]
        ],
        'database_environment' => [
            'driver' => 'MySQL / MariaDB (via PDO & MySQLi)',
            'host' => DB_HOST,
            'port' => DB_PORT,
            'database' => DB_NAME,
            'charset' => DB_CHARSET
        ],
        'kebutuhan_minimal' => [
            'Software Utama' => [
                'Web Server' => 'Apache 2.4+ atau Nginx atau PHP Built-in Server',
                'Interpreter' => 'PHP 7.4+ (Direkomendasikan PHP 8.x)',
                'DBMS' => 'MySQL 5.7+ / MariaDB 10.3+ / MySQL 8.x',
                'Code Editor' => 'Visual Studio Code / Sublime Text / PHPStorm',
                'Web Browser' => 'Google Chrome / Microsoft Edge / Mozilla Firefox'
            ],
            'Modul / Ekstensi PHP Wajib' => [
                'PDO & PDO_MySQL' => 'Untuk koneksi database yang aman dengan Prepared Statements',
                'Session' => 'Untuk manajemen autentikasi user dan state session',
                'JSON' => 'Untuk serialisasi data dan komunikasi AJAX/REST',
                'OpenSSL' => 'Untuk enkripsi data dan hashing aman'
            ]
        ]
    ];
}
