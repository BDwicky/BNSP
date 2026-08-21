<?php
/**
 * ==============================================================================
 * HALAMAN UTAMA: DATA PRODUK INVENTARIS (PHP NATIVE MURNI)
 * Memenuhi Kriteria Uji BNSP Skenario 3:
 * - Unit 1: TIK.PR08.007.01 (Basis Data MySQL)
 * - Unit 2: TIK.PR08.009.01 (Aplikasi Web Berbasis PHP)
 * - Langkah Kerja 3: Menggunakan Sintaks Khusus MySQL (INNER JOIN, Agregasi COUNT, SUM)
 * - Langkah Kerja 6 & 7: Variabel Superglobal $_GET dan Array Asosiatif
 * - Langkah Kerja 9: Menampilkan Data (READ)
 * ==============================================================================
 */
require_once __DIR__ . '/koneksi.php';

// Cek Autentikasi Login (Langkah Kerja 2)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Langkah Kerja 6 & 7: Menangkap parameter pencarian dari $_GET
$search = clean($_GET['search'] ?? '');
$kategori_id = (int)($_GET['kategori_id'] ?? 0);

// Ambil Kategori untuk filter dropdown
$katStmt = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
$kategoriList = $katStmt->fetchAll();

// Langkah Kerja 3 & 9: Query SQL Relasional INNER JOIN dengan Filter
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
// Langkah Kerja 6: Menampung data dalam Array Asosiatif
$products = $stmt->fetchAll();

// Langkah Kerja 3: Sintaks Khusus Agregasi MySQL (COUNT, SUM)
$statStmt = $pdo->query("SELECT COUNT(*) as total_item, SUM(stok) as total_stok, SUM(harga_jual * stok) as total_aset FROM produk");
$stat = $statStmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Inventaris Barang</title>
    <style>
        body { font-family: sans-serif; margin: 20px; line-height: 1.5; }
        .menu-bar { background: #eee; padding: 10px; border: 1px solid #ccc; margin-bottom: 15px; }
        .menu-bar a { margin-right: 15px; text-decoration: none; font-weight: bold; color: #0066cc; }
        .pesan-sukses { background: #e6ffe6; border: 1px solid #b3ffb3; padding: 8px; margin-bottom: 15px; color: #006600; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        th { background: #f2f2f2; }
        .easter-trigger { cursor: pointer; user-select: none; }
    </style>
</head>
<body>

    <h2 id="mainTitle" class="easter-trigger">SISTEM INVENTARIS BARANG</h2>
    <p>
        Pengguna Aktif: <strong><?= clean($_SESSION['nama_lengkap']) ?></strong> | 
        Peran: <strong><?= clean($_SESSION['role']) ?></strong>
    </p>

    <!-- Navigasi Menu Sederhana Standar -->
    <div class="menu-bar">
        <a href="index.php"><strong>[ Data Produk ]</strong></a>
        <a href="tambah.php"><strong>[ + Tambah Produk Baru ]</strong></a>
        <a href="logout.php" onclick="return confirm('Yakin ingin logout?');" style="color: red;"><strong>[ Logout ]</strong></a>
    </div>

    <!-- Notifikasi Pesan -->
    <?php if (isset($_GET['pesan'])): ?>
        <?php if ($_GET['pesan'] === 'tambah_sukses'): ?>
            <div class="pesan-sukses">Sukses: Data produk baru berhasil ditambahkan ke basis data!</div>
        <?php elseif ($_GET['pesan'] === 'edit_sukses'): ?>
            <div class="pesan-sukses">Sukses: Perubahan data produk berhasil disimpan!</div>
        <?php elseif ($_GET['pesan'] === 'hapus_sukses'): ?>
            <div class="pesan-sukses">Sukses: Data produk berhasil dihapus dari basis data!</div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Ringkasan Agregasi Data (Langkah Kerja 3) -->
    <fieldset style="margin-bottom: 15px; padding: 10px;">
        <legend><strong>Ringkasan Statistik Basis Data (MySQL Agregasi)</strong></legend>
        <table style="width: auto; border: none;">
            <tr>
                <td style="border: none; padding-right: 20px;">Total Jenis Produk: <strong><?= number_format($stat['total_item']) ?> Item</strong></td>
                <td style="border: none; padding-right: 20px;">Total Stok Keseluruhan: <strong><?= number_format($stat['total_stok']) ?> Unit</strong></td>
                <td style="border: none;">Total Nilai Aset: <strong><?= rupiah($stat['total_aset']) ?></strong></td>
            </tr>
        </table>
    </fieldset>

    <!-- Form Filter & Pencarian (Langkah Kerja 6: $_GET) -->
    <form method="GET" action="index.php" style="margin-bottom: 15px; background: #fafafa; padding: 10px; border: 1px solid #ddd;">
        <label for="search">Cari Nama/Kode Produk:</label>
        <input type="text" id="search" name="search" value="<?= clean($search) ?>" placeholder="Kata kunci...">

        <label for="kategori_id" style="margin-left: 10px;">Kategori:</label>
        <select id="kategori_id" name="kategori_id">
            <option value="0">-- Semua Kategori --</option>
            <?php foreach ($kategoriList as $k): ?>
                <option value="<?= $k['id'] ?>" <?= ($kategori_id == $k['id']) ? 'selected' : '' ?>>
                    <?= clean($k['nama_kategori']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" style="margin-left: 10px; padding: 3px 10px;">Cari Data</button>
        <?php if (!empty($search) || $kategori_id > 0): ?>
            <a href="index.php" style="margin-left: 5px; font-size: 12px;">[Reset Filter]</a>
        <?php endif; ?>
    </form>

    <div style="margin-bottom: 5px;">
        <strong>Tabel Data Master Produk:</strong>
    </div>

    <!-- Tabel Data (Langkah Kerja 9: READ) -->
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">No</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th style="text-align: center;">Stok</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center; width: 120px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; color: #888; padding: 15px;">
                        Data produk tidak ditemukan pada basis data.
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($products as $p): ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td><?= clean($p['kode_produk']) ?></td>
                        <td><?= clean($p['nama_produk']) ?></td>
                        <td><?= clean($p['nama_kategori']) ?></td>
                        <td><?= rupiah($p['harga_beli']) ?></td>
                        <td><?= rupiah($p['harga_jual']) ?></td>
                        <td style="text-align: center;"><?= (int)$p['stok'] ?> <?= clean($p['satuan']) ?></td>
                        <td style="text-align: center;">
                            <?php if ($p['stok'] > 10): ?>
                                <span>Tersedia</span>
                            <?php elseif ($p['stok'] > 0): ?>
                                <span>Menipis</span>
                            <?php else: ?>
                                <span style="color: red;">Habis</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="edit.php?id=<?= $p['id'] ?>">[Edit]</a> | 
                            <a href="hapus.php?id=<?= $p['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data <?= addslashes($p['nama_produk']) ?>?');" style="color: red;">[Hapus]</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <hr style="margin-top: 25px;">
    <p style="margin-bottom: 5px;"><small>&copy; <?= date('Y') ?> &mdash; Sistem Inventaris Barang</small></p>
    <div style="margin-top: 8px;">
        <span id="rocketEasterEgg" title="🚀" style="cursor: pointer; font-size: 16px; opacity: 0.35; transition: opacity 0.2s, transform 0.2s; display: inline-block;">🚀</span>
    </div>

    <!-- Script Easter Egg (Klik Roket, Shortcut Ctrl+Shift+M, atau Klik 3x Judul) -->
    <script>
        let clickCount = 0;
        let timer = null;

        function triggerModern() {
            window.location.href = '../index.php?mode=pro';
        }

        // Trigger Roket
        const rocket = document.getElementById('rocketEasterEgg');
        if (rocket) {
            rocket.addEventListener('mouseenter', () => { rocket.style.opacity = '1'; rocket.style.transform = 'scale(1.25)'; });
            rocket.addEventListener('mouseleave', () => { rocket.style.opacity = '0.35'; rocket.style.transform = 'scale(1)'; });
            rocket.addEventListener('click', triggerModern);
        }

        // Trigger Klik 3x Judul
        const titleEl = document.getElementById('mainTitle');
        if (titleEl) {
            titleEl.addEventListener('click', () => {
                clickCount++;
                clearTimeout(timer);
                if (clickCount >= 3) {
                    triggerModern();
                } else {
                    timer = setTimeout(() => { clickCount = 0; }, 1000);
                }
            });
        }

        // Trigger Shortcut Keyboard
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.shiftKey && (e.key === 'M' || e.key === 'm')) {
                e.preventDefault();
                triggerModern();
            }
        });
    </script>
</body>
</html>
