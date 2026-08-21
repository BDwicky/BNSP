<?php
require_once __DIR__ . '/koneksi.php';

// Cek autentikasi
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$search = clean($_GET['search'] ?? '');
$kategori_id = (int)($_GET['kategori_id'] ?? 0);

// Ambil Kategori untuk dropdown filter
$katStmt = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
$kategoriList = $katStmt->fetchAll();

// Query SQL Produk dengan JOIN dan Filter
$sql = "SELECT p.*, k.nama_kategori 
        FROM produk p 
        INNER JOIN kategori k ON p.kategori_id = k.id 
        WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (p.nama_produk LIKE :search OR p.kode_produk LIKE :search)";
    $params[':search'] = "%{$search}%";
}

if ($kategori_id > 0) {
    $sql .= " AND p.kategori_id = :kategori_id";
    $params[':kategori_id'] = $kategori_id;
}

$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Hitung Statistik Dasar (Langkah Kerja 3: Fungsi Agregasi MySQL)
$statStmt = $pdo->query("SELECT COUNT(*) as total_item, SUM(stok) as total_stok, SUM(harga_jual * stok) as total_aset FROM produk");
$stat = $statStmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Inventaris - Mode Sederhana</title>
    <link rel="stylesheet" href="../assets/css/simple.css">
</head>
<body>
    <div class="container">
        <!-- Banner Mode -->
        <div class="mode-banner">
            <span>🔹 <strong>Mode Sederhana (Simple Native Style):</strong> Tampilan simpel khas ujian praktik BNSP.</span>
            <a href="../index.php" style="color: #3b82f6; font-weight: bold; text-decoration: none;">Beralih ke Mode Modern &rarr;</a>
        </div>

        <header>
            <div>
                <h1>Aplikasi Inventaris Barang</h1>
                <small style="color: #64748b;">User Login: <strong><?= clean($_SESSION['nama_lengkap']) ?></strong> (Role: <?= clean($_SESSION['role']) ?>)</small>
            </div>
            <nav>
                <a href="index.php">Data Produk</a>
                <a href="tambah.php">+ Tambah Produk</a>
                <a href="panduan.php">Panduan Asesor</a>
                <a href="logout.php" style="color: #ef4444;" onclick="return confirm('Yakin ingin keluar?');">Logout</a>
            </nav>
        </header>

        <!-- Pesan Notifikasi Sederhana -->
        <?php if (isset($_GET['pesan'])): ?>
            <?php if ($_GET['pesan'] === 'tambah_sukses'): ?>
                <div class="alert alert-success">Data produk baru berhasil ditambahkan!</div>
            <?php elseif ($_GET['pesan'] === 'edit_sukses'): ?>
                <div class="alert alert-success">Data produk berhasil diperbarui!</div>
            <?php elseif ($_GET['pesan'] === 'hapus_sukses'): ?>
                <div class="alert alert-success">Data produk berhasil dihapus dari database!</div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Ringkasan Statistik Dasar -->
        <div style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 150px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 4px;">
                <div style="font-size: 11px; color: #64748b; font-weight: bold;">TOTAL PRODUK</div>
                <div style="font-size: 20px; font-weight: bold; color: #1e293b;"><?= number_format($stat['total_item']) ?> Item</div>
            </div>
            <div style="flex: 1; min-width: 150px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 4px;">
                <div style="font-size: 11px; color: #64748b; font-weight: bold;">TOTAL STOK FISIK</div>
                <div style="font-size: 20px; font-weight: bold; color: #1e293b;"><?= number_format($stat['total_stok']) ?> Unit</div>
            </div>
            <div style="flex: 1; min-width: 150px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 4px;">
                <div style="font-size: 11px; color: #64748b; font-weight: bold;">ESTIMASI NILAI ASET</div>
                <div style="font-size: 20px; font-weight: bold; color: #059669;"><?= rupiah($stat['total_aset']) ?></div>
            </div>
        </div>

        <!-- Filter dan Pencarian -->
        <form method="GET" action="" class="filter-box">
            <div style="flex: 2; min-width: 180px;">
                <input type="text" name="search" placeholder="Cari nama atau kode produk..." value="<?= clean($search) ?>">
            </div>
            <div style="flex: 1.5; min-width: 160px;">
                <select name="kategori_id">
                    <option value="0">-- Semua Kategori --</option>
                    <?php foreach ($kategoriList as $kat): ?>
                        <option value="<?= $kat['id'] ?>" <?= ($kategori_id == $kat['id']) ? 'selected' : '' ?>>
                            <?= clean($kat['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <?php if (!empty($search) || $kategori_id > 0): ?>
                    <a href="index.php" class="btn btn-secondary">Reset</a>
                <?php endif; ?>
            </div>
        </form>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
            <h3>Daftar Tabel Produk (Total: <?= count($products) ?>)</h3>
            <a href="tambah.php" class="btn btn-primary">+ Tambah Produk</a>
        </div>

        <!-- Tabel Data Produk -->
        <table>
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th style="text-align: center;">Stok</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center; width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: #64748b; padding: 20px;">
                            Tidak ada data produk yang ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($products as $p): ?>
                        <tr>
                            <td style="text-align: center;"><?= $no++ ?></td>
                            <td><strong><?= clean($p['kode_produk']) ?></strong></td>
                            <td><?= clean($p['nama_produk']) ?></td>
                            <td><?= clean($p['nama_kategori']) ?></td>
                            <td><?= rupiah($p['harga_beli']) ?></td>
                            <td><strong><?= rupiah($p['harga_jual']) ?></strong></td>
                            <td style="text-align: center;"><?= (int)$p['stok'] ?> <?= clean($p['satuan']) ?></td>
                            <td style="text-align: center;">
                                <?php if ($p['stok'] > 10): ?>
                                    <span style="color: #059669; font-weight: bold;">Tersedia</span>
                                <?php elseif ($p['stok'] > 0): ?>
                                    <span style="color: #d97706; font-weight: bold;">Menipis</span>
                                <?php else: ?>
                                    <span style="color: #dc2626; font-weight: bold;">Habis</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                <a href="hapus.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus produk <?= addslashes($p['nama_produk']) ?>?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <footer>
            <p>Smart Inventory Pro (Simple Native PHP) &mdash; Uji Kompetensi BNSP Pemrograman Web (Skenario 3)</p>
        </footer>
    </div>
</body>
</html>
