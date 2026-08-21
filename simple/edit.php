<?php
/**
 * ==============================================================================
 * FORM EDIT PRODUK (PHP NATIVE MURNI)
 * Memenuhi Kriteria Uji BNSP:
 * - Langkah Kerja 9: Manipulasi Data Basis Data (UPDATE)
 * - Langkah Kerja 2: Keamanan PDO Prepared Statements
 * ==============================================================================
 */
require_once __DIR__ . '/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$error = '';

// Ambil data produk yang akan diedit dari basis data
$stmt = $pdo->prepare("SELECT * FROM produk WHERE id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    die("Data produk tidak ditemukan! <a href='index.php'>Kembali ke Tabel</a>");
}

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_produk = clean($_POST['kode_produk'] ?? '');
    $nama_produk = clean($_POST['nama_produk'] ?? '');
    $kategori_id = (int)($_POST['kategori_id'] ?? 0);
    $harga_beli  = (float)($_POST['harga_beli'] ?? 0);
    $harga_jual  = (float)($_POST['harga_jual'] ?? 0);
    $stok        = (int)($_POST['stok'] ?? 0);
    $satuan      = clean($_POST['satuan'] ?? 'Unit');

    if (empty($kode_produk) || empty($nama_produk) || $kategori_id <= 0) {
        $error = 'Semua kolom wajib diisi!';
    } else {
        // Cek duplikasi kode produk pada data lain
        $cekStmt = $pdo->prepare("SELECT id FROM produk WHERE kode_produk = :kode AND id != :id");
        $cekStmt->execute([':kode' => $kode_produk, ':id' => $id]);
        if ($cekStmt->fetch()) {
            $error = "Kode produk '{$kode_produk}' sudah digunakan oleh data lain!";
        } else {
            // Langkah Kerja 9: Menjalankan Perintah UPDATE
            $status = ($stok > 0) ? 'tersedia' : 'habis';
            $sql = "UPDATE produk SET 
                        kategori_id = :kategori_id,
                        kode_produk = :kode_produk,
                        nama_produk = :nama_produk,
                        harga_beli  = :harga_beli,
                        harga_jual  = :harga_jual,
                        stok        = :stok,
                        satuan      = :satuan,
                        status      = :status
                    WHERE id = :id";
            
            $updateStmt = $pdo->prepare($sql);
            $simpan = $updateStmt->execute([
                ':kategori_id' => $kategori_id,
                ':kode_produk' => $kode_produk,
                ':nama_produk' => $nama_produk,
                ':harga_beli'  => $harga_beli,
                ':harga_jual'  => $harga_jual,
                ':stok'        => $stok,
                ':satuan'      => $satuan,
                ':status'      => $status,
                ':id'          => $id
            ]);

            if ($simpan) {
                header('Location: index.php?pesan=edit_sukses');
                exit;
            } else {
                $error = 'Gagal memperbarui data di database.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Produk</title>
    <style>
        body { font-family: sans-serif; margin: 20px; line-height: 1.5; }
        /* Navigasi Menu Header: Container Transparan, Tombol Abu-abu Klasik Win XP */
        .menu-bar {
            background: transparent;
            padding: 2px 0;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: Tahoma, 'Segoe UI', sans-serif;
        }
        .xp-btn {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            font-family: Tahoma, sans-serif;
            font-size: 11px;
            color: #000000;
            text-decoration: none;
            background: #d4d0c8;
            border: 1px solid #808080;
            border-radius: 2px;
            cursor: pointer;
            user-select: none;
        }
        .xp-btn:hover {
            background: #c2beb6;
            border-color: #555555;
            color: #000000;
        }
        .error { color: red; font-weight: bold; margin-bottom: 10px; }
        table.form-table td { padding: 6px; }
    </style>
</head>
<body>

    <h2>EDIT DATA PRODUK: <?= clean($product['nama_produk']) ?></h2>
    <div class="menu-bar">
        <a href="index.php" class="xp-btn"><strong>&larr; [ Kembali ke Data Produk ]</strong></a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= $id ?>">
        <table class="form-table">
            <tr>
                <td><strong>Kode Produk:</strong></td>
                <td><input type="text" name="kode_produk" value="<?= clean($product['kode_produk']) ?>" required></td>
            </tr>
            <tr>
                <td><strong>Kategori:</strong></td>
                <td>
                    <select name="kategori_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategoriList as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= ((int)$product['kategori_id'] === (int)$k['id']) ? 'selected' : '' ?>>
                                <?= clean($k['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><strong>Nama Produk:</strong></td>
                <td><input type="text" name="nama_produk" size="40" value="<?= clean($product['nama_produk']) ?>" required></td>
            </tr>
            <tr>
                <td><strong>Harga Beli (Rp):</strong></td>
                <td><input type="number" name="harga_beli" min="0" value="<?= clean($product['harga_beli']) ?>" required></td>
            </tr>
            <tr>
                <td><strong>Harga Jual (Rp):</strong></td>
                <td><input type="number" name="harga_jual" min="0" value="<?= clean($product['harga_jual']) ?>" required></td>
            </tr>
            <tr>
                <td><strong>Jumlah Stok:</strong></td>
                <td><input type="number" name="stok" min="0" value="<?= clean($product['stok']) ?>" required></td>
            </tr>
            <tr>
                <td><strong>Satuan:</strong></td>
                <td><input type="text" name="satuan" value="<?= clean($product['satuan']) ?>" required></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit" style="padding: 5px 15px; cursor: pointer;">Simpan Perubahan (UPDATE)</button>
                    <a href="index.php" style="margin-left: 10px;">Batal</a>
                </td>
            </tr>
        </table>
    </form>

    <script>
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.shiftKey && (e.key === 'M' || e.key === 'm')) {
                e.preventDefault();
                window.location.href = '../index.php?mode=pro';
            }
        });
    </script>
</body>
</html>
