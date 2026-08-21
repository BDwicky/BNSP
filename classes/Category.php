<?php
/**
 * Kelas Model Kategori
 * Memenuhi Langkah Kerja 8 (Fungsi dan Kelas) & Langkah Kerja 9 (Koneksi & Manipulasi Data)
 */

class Category {
    private PDO $db;
    private string $table = 'kategori';

    // Properti Objek Kategori
    public ?int $id = null;
    public ?string $kode_kategori = null;
    public ?string $nama_kategori = null;
    public ?string $deskripsi = null;

    /**
     * Constructor menginisialisasi koneksi PDO dari Singleton
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Mengambil seluruh kategori beserta jumlah produk terkait (JOIN query)
     */
    public function getAllWithProductCount(): array {
        $query = "SELECT k.*, COUNT(p.id) AS total_produk 
                  FROM {$this->table} k 
                  LEFT JOIN produk p ON k.id = p.kategori_id 
                  GROUP BY k.id 
                  ORDER BY k.nama_kategori ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Mengambil kategori berdasarkan ID
     */
    public function getById(int $id): ?array {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Menambah kategori baru (Prepared Statement)
     */
    public function create(array $data): bool {
        $query = "INSERT INTO {$this->table} (kode_kategori, nama_kategori, deskripsi) 
                  VALUES (:kode, :nama, :deskripsi)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':kode' => $data['kode_kategori'],
            ':nama' => $data['nama_kategori'],
            ':deskripsi' => $data['deskripsi'] ?? null
        ]);
    }
}
