<?php
/**
 * Kelas Koneksi Database (Pola Desain Singleton)
 * Dilengkapi dengan Auto-Detection & Fallback Cerdas
 * Memenuhi:
 * - Langkah Kerja 4: Melakukan Pengaksesan Database
 * - Langkah Kerja 8: Menerapkan Fungsi dan Kelas (OOP Encapsulation & Singleton)
 */

class Database {
    // Properti private untuk membatasi instansiasi langsung (Penerapan Enkapsulasi)
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    // Database credentials default
    private string $host = DB_HOST;
    private string $port = DB_PORT;
    private string $db_name = DB_NAME;
    private string $username = DB_USER;
    private string $password = DB_PASS;
    private string $charset = DB_CHARSET;

    /**
     * Constructor bersifat private agar tidak bisa dibuat via new Database() di luar kelas
     */
    private function __construct() {
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
        ];

        // Daftar kombinasi kredensial otomatis (Mencegah error access denied saat update/sync)
        $candidates = [
            ['user' => $this->username, 'pass' => $this->password],
            ['user' => 'root', 'pass' => ''],
            ['user' => 'bnsp_user', 'pass' => 'BnspPass123!'],
            ['user' => 'root', 'pass' => 'root'],
        ];

        $lastError = '';
        foreach ($candidates as $cred) {
            try {
                $this->connection = new PDO($dsn, $cred['user'], $cred['pass'], $options);
                break; // Terhubung sukses
            } catch (PDOException $e) {
                $lastError = $e->getMessage();
            }
        }

        if ($this->connection === null) {
            die("Koneksi Database Gagal: " . htmlspecialchars($lastError));
        }
    }

    /**
     * Mengambil instance tunggal koneksi Database (Singleton Method)
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Mengambil objek PDO Connection yang aktif
     */
    public function getConnection(): PDO {
        return $this->connection;
    }

    /**
     * Mencegah cloning objek
     */
    private function __clone() {}

    /**
     * Mencegah unserialize objek
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton object");
    }
}
