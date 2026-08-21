<?php
/**
 * Kelas User (Otentikasi & Keamanan Pengguna)
 * Memenuhi Langkah Kerja 2 (Security: Hashing) & Langkah Kerja 8 (OOP)
 */

class User {
    private PDO $db;
    private string $table = 'users';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Memvalidasi login user menggunakan password_verify
     */
    public function authenticate(string $username, string $password): ?array {
        $query = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':username', trim($username));
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Hilangkan hash password sebelum disimpan ke return/session untuk keamanan
            unset($user['password']);
            return $user;
        }

        return null;
    }

    /**
     * Mengambil data user berdasarkan ID
     */
    public function getById(int $id): ?array {
        $query = "SELECT id, username, nama_lengkap, role, created_at FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Mendaftarkan/Menambah user baru dengan password_hash BCRYPT
     */
    public function register(string $username, string $password, string $namaLengkap, string $role = 'admin'): bool {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO {$this->table} (username, password, nama_lengkap, role) 
                  VALUES (:username, :password, :nama_lengkap, :role)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':username' => trim($username),
            ':password' => $hash,
            ':nama_lengkap' => trim($namaLengkap),
            ':role' => $role
        ]);
    }
}
