<?php
/**
 * Kelas Model Produk (CRUD & Manipulasi Data Inventaris)
 * Memenuhi:
 * - Langkah Kerja 3: Sintaks khusus MySQL (JOIN, Agregasi, Filter)
 * - Langkah Kerja 8: Menerapkan Fungsi & Kelas (OOP)
 * - Langkah Kerja 9: Manipulasi Data (CRUD)
 */

class Product {
    private PDO $db;
    private string $table = 'produk';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * READ: Mengambil daftar produk dengan filter kategori, pencarian (search), dan pagination
     */
    public function getAll(string $search = '', int $kategori_id = 0, int $limit = 10, int $offset = 0): array {
        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(p.nama_produk LIKE :search OR p.kode_produk LIKE :search_code)";
            $params[':search'] = '%' . $search . '%';
            $params[':search_code'] = '%' . $search . '%';
        }

        if ($kategori_id > 0) {
            $conditions[] = "p.kategori_id = :kategori_id";
            $params[':kategori_id'] = $kategori_id;
        }

        $whereSql = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Sintaks Khusus MySQL: INNER JOIN & Pagination (LIMIT, OFFSET)
        $query = "SELECT p.*, k.nama_kategori, k.kode_kategori 
                  FROM {$this->table} p 
                  INNER JOIN kategori k ON p.kategori_id = k.id 
                  {$whereSql} 
                  ORDER BY p.id DESC 
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($query);

        // Bind parameters
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Menghitung total data produk untuk keperluan pagination & statistik
     */
    public function countAll(string $search = '', int $kategori_id = 0): int {
        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(p.nama_produk LIKE :search OR p.kode_produk LIKE :search_code)";
            $params[':search'] = '%' . $search . '%';
            $params[':search_code'] = '%' . $search . '%';
        }

        if ($kategori_id > 0) {
            $conditions[] = "p.kategori_id = :kategori_id";
            $params[':kategori_id'] = $kategori_id;
        }

        $whereSql = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $query = "SELECT COUNT(*) AS total FROM {$this->table} p {$whereSql}";

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    /**
     * READ: Mengambil satu produk berdasarkan ID (dengan data kategori)
     */
    public function getById(int $id): ?array {
        $query = "SELECT p.*, k.nama_kategori 
                  FROM {$this->table} p 
                  INNER JOIN kategori k ON p.kategori_id = k.id 
                  WHERE p.id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * CREATE: Menambah data produk baru (Prepared Statements anti-SQL Injection)
     */
    public function create(array $data): bool {
        // Tentukan status otomatis berdasarkan jumlah stok
        $stok = (int)$data['stok'];
        $status = 'tersedia';
        if ($stok <= 0) {
            $status = 'habis';
        } elseif ($stok <= 5) {
            $status = 'menipis';
        }

        $query = "INSERT INTO {$this->table} 
                  (kategori_id, kode_produk, nama_produk, harga_beli, harga_jual, stok, satuan, status) 
                  VALUES (:kategori_id, :kode_produk, :nama_produk, :harga_beli, :harga_jual, :stok, :satuan, :status)";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':kategori_id' => (int)$data['kategori_id'],
            ':kode_produk' => trim($data['kode_produk']),
            ':nama_produk' => trim($data['nama_produk']),
            ':harga_beli'  => (float)$data['harga_beli'],
            ':harga_jual'  => (float)$data['harga_jual'],
            ':stok'        => $stok,
            ':satuan'      => trim($data['satuan']),
            ':status'      => $status
        ]);
    }

    /**
     * UPDATE: Mengubah data produk yang sudah ada
     */
    public function update(int $id, array $data): bool {
        $stok = (int)$data['stok'];
        $status = 'tersedia';
        if ($stok <= 0) {
            $status = 'habis';
        } elseif ($stok <= 5) {
            $status = 'menipis';
        }

        $query = "UPDATE {$this->table} SET 
                    kategori_id = :kategori_id,
                    kode_produk = :kode_produk,
                    nama_produk = :nama_produk,
                    harga_beli  = :harga_beli,
                    harga_jual  = :harga_jual,
                    stok        = :stok,
                    satuan      = :satuan,
                    status      = :status
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':kategori_id' => (int)$data['kategori_id'],
            ':kode_produk' => trim($data['kode_produk']),
            ':nama_produk' => trim($data['nama_produk']),
            ':harga_beli'  => (float)$data['harga_beli'],
            ':harga_jual'  => (float)$data['harga_jual'],
            ':stok'        => $stok,
            ':satuan'      => trim($data['satuan']),
            ':status'      => $status,
            ':id'          => $id
        ]);
    }

    /**
     * DELETE: Menghapus produk berdasarkan ID
     */
    public function delete(int $id): bool {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Cek apakah kode produk sudah digunakan produk lain
     */
    public function isCodeExists(string $kode, ?int $excludeId = null): bool {
        $query = "SELECT id FROM {$this->table} WHERE kode_produk = :kode";
        if ($excludeId !== null) {
            $query .= " AND id != :excludeId";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':kode', $kode);
        if ($excludeId !== null) {
            $stmt->bindValue(':excludeId', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (bool)$stmt->fetch();
    }

    /**
     * Mendapatkan Ringkasan Statistik untuk Dashboard (Agregasi MySQL: COUNT, SUM, AVG)
     */
    public function getSummaryStats(): array {
        $query = "SELECT 
                    COUNT(*) AS total_produk,
                    COALESCE(SUM(stok), 0) AS total_stok,
                    COALESCE(SUM(stok * harga_beli), 0) AS total_aset_beli,
                    COALESCE(SUM(stok * harga_jual), 0) AS total_aset_jual,
                    SUM(CASE WHEN status = 'habis' OR stok = 0 THEN 1 ELSE 0 END) AS produk_habis,
                    SUM(CASE WHEN status = 'menipis' OR (stok > 0 AND stok <= 5) THEN 1 ELSE 0 END) AS produk_menipis,
                    SUM(CASE WHEN status = 'tersedia' AND stok > 5 THEN 1 ELSE 0 END) AS produk_tersedia
                  FROM {$this->table}";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Mengambil distribusi stok per kategori untuk grafik visualisasi
     */
    public function getStockPerCategory(): array {
        $query = "SELECT k.nama_kategori, COALESCE(SUM(p.stok), 0) AS total_stok, COUNT(p.id) AS total_produk
                  FROM kategori k
                  LEFT JOIN {$this->table} p ON k.id = p.kategori_id
                  GROUP BY k.id, k.nama_kategori
                  ORDER BY total_stok DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
