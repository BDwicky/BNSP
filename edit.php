<?php
/**
 * Halaman Edit Produk (Langkah Kerja 9: UPDATE)
 */
require_once __DIR__ . '/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$error = '';

// Ambil data produk yang akan diedit dari database
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
        $error = 'Semua kolom bertanda bintang wajib diisi!';
    } else {
        // Cek duplikasi kode produk pada ID produk lain
        $cekStmt = $pdo->prepare("SELECT id FROM produk WHERE kode_produk = :kode AND id != :id");
        $cekStmt->execute([':kode' => $kode_produk, ':id' => $id]);
        if ($cekStmt->fetch()) {
            $error = "Kode produk '{$kode_produk}' sudah digunakan oleh produk lain!";
        } else {
            // UPDATE data ke MySQL (Langkah Kerja 9)
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Produk - PHP Native</title>
    <link rel="stylesheet" href="assets/css/native.css?v=<?= time() ?>">
</head>
<body>
    <div class="container" style="max-width: 650px;">
        <div class="main-header">
            <h1>Edit Produk: <?= clean($product['nama_produk']) ?></h1>
            <div class="nav-links">
                <a href="index.php">&larr; Kembali ke Tabel</a>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="edit.php?id=<?= $id ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="kode_produk">Kode Produk: <span style="color: red;">*</span></label>
                    <input type="text" id="kode_produk" name="kode_produk" class="form-control" value="<?= clean($product['kode_produk']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="kategori_id">Kategori: <span style="color: red;">*</span></label>
                    <select id="kategori_id" name="kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategoriList as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= ((int)$product['kategori_id'] === (int)$k['id']) ? 'selected' : '' ?>>
                                <?= clean($k['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="nama_produk">Nama Produk: <span style="color: red;">*</span></label>
                <input type="text" id="nama_produk" name="nama_produk" class="form-control" value="<?= clean($product['nama_produk']) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="harga_beli">Harga Beli (Rp):</label>
                    <input type="number" id="harga_beli" name="harga_beli" class="form-control" min="0" value="<?= clean($product['harga_beli']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="harga_jual">Harga Jual (Rp):</label>
                    <input type="number" id="harga_jual" name="harga_jual" class="form-control" min="0" value="<?= clean($product['harga_jual']) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="stok">Jumlah Stok:</label>
                    <input type="number" id="stok" name="stok" class="form-control" min="0" value="<?= clean($product['stok']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="satuan">Satuan:</label>
                    <input type="text" id="satuan" name="satuan" class="form-control" value="<?= clean($product['satuan']) ?>" required>
                </div>
            </div>

            <div style="margin-top: 15px; display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>

        <div class="footer-simple">
            <p>Aplikasi Web Sistem Inventaris &mdash; Uji Kompetensi BNSP Pemrograman Web</p>
        </div>
    </div>
</body>
</html>
