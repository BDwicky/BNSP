<?php
/**
 * ==============================================================================
 * ENTRY POINT UTAMA SISTEM INVENTARIS
 * DEFAULT: Mode Sederhana Murni (Uji BNSP Native)
 * EASTER EGG: Mode Modern Pro (Dashboard)
 * ==============================================================================
 */
require_once __DIR__ . '/koneksi.php';

// 1. Cek pergantian mode via parameter query ?mode= (dan langsung bersihkan URL)
if (isset($_GET['mode'])) {
    if ($_GET['mode'] === 'pro') {
        $_SESSION['app_mode'] = 'pro';
    } elseif ($_GET['mode'] === 'simple') {
        $_SESSION['app_mode'] = 'simple';
    }
    // Bersihkan URL dari query parameter ?mode=... agar address bar tetap bersih & ringkas
    header('Location: index.php');
    exit;
}

// 2. Cek status autentikasi login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ==============================================================================
// 3. JIKA MODE PRO DIAKTIFKAN (EASTER EGG MODE)
// ==============================================================================
if (isset($_SESSION['app_mode']) && $_SESSION['app_mode'] === 'pro') {
    require_once __DIR__ . '/config/config.php';
    
    $action = $_GET['action'] ?? 'dashboard';

    // Handle Action: Hapus Produk di Mode Pro
    if ($action === 'product_delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        $id = (int)($_POST['id'] ?? 0);

        if (!verifyCsrfToken($token)) {
            setFlash('error', 'Token keamanan CSRF tidak valid.');
        } elseif ($id <= 0) {
            setFlash('error', 'ID produk tidak valid.');
        } else {
            $productModel = new Product();
            $product = $productModel->getById($id);

            if ($product && $productModel->delete($id)) {
                setFlash('success', "Produk '{$product['nama_produk']}' berhasil dihapus.");
            } else {
                setFlash('error', 'Gagal menghapus produk dari basis data.');
            }
        }

        header('Location: index.php?action=products');
        exit;
    }

    // Router View Loader untuk Mode Modern
    $pageTitle = 'Dashboard';
    $viewFile = 'views/dashboard.php';

    switch ($action) {
        case 'products':
            $pageTitle = 'Data Produk';
            $viewFile = 'views/product_list.php';
            break;
        case 'product_create':
            $pageTitle = 'Tambah Produk';
            $viewFile = 'views/product_form.php';
            break;
        case 'product_edit':
            $pageTitle = 'Edit Produk';
            $viewFile = 'views/product_form.php';
            break;
        case 'asesor':
            $pageTitle = 'Panduan Uji Asesor';
            $viewFile = 'views/asesor_guide.php';
            break;
        case 'dashboard':
        default:
            $pageTitle = 'Dashboard';
            $viewFile = 'views/dashboard.php';
            break;
    }

    require_once __DIR__ . '/views/header.php';
    require_once __DIR__ . '/' . $viewFile;
    require_once __DIR__ . '/views/footer.php';
    exit;
}

// ==============================================================================
// 4. DEFAULT: TAMPILAN SIMPLE NATIVE MURNI (STANDAR UJI BNSP)
// ==============================================================================
$search = clean($_GET['search'] ?? '');
$kategori_id = (int)($_GET['kategori_id'] ?? 0);

// Ambil Kategori untuk filter dropdown
$katStmt = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
$kategoriList = $katStmt->fetchAll();

// Query SQL Relasional INNER JOIN dengan Filter
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

// Agregasi Data
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
        .xp-btn:active {
            background: #b0aca4;
            border-color: #333333;
            color: #000000;
        }
        .xp-sep {
            display: inline-block;
            width: 0;
            height: 14px;
            border-left: 1px solid #999999;
            margin: 0 2px;
        }
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

    <!-- Navigasi Menu Bergaya Windows Classic Toolbar (Abu-abu) -->
    <div class="menu-bar">
        <a href="index.php" class="xp-btn"><strong>[ Data Produk ]</strong></a>
        <a href="tambah.php" class="xp-btn"><strong>[ + Tambah Produk Baru ]</strong></a>
        <div class="xp-sep"></div>
        <a href="logout.php" onclick="return confirm('Yakin ingin logout?');" class="xp-btn"><strong>[ Logout ]</strong></a>
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

    <!-- Running Text Copyright Seamless Infinite Loop (Nyambung Tanpa Kosong) -->
    <style>
        .ticker-wrap {
            position: relative;
            width: 260px;
            height: 22px;
            background: #fafafa;
            border: 1px solid #ccc;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 5px;
            white-space: nowrap;
            display: flex;
            align-items: center;
            user-select: none;
            cursor: pointer;
            transition: background 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        }
        .ticker-wrap:hover {
            background: #1e293b;
            border-color: #0f172a;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        /* Efek Tabrakan / Impact Shake saat 1x Klik */
        .ticker-wrap.crash-active {
            animation: crashImpact 0.45s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }
        @keyframes crashImpact {
            0% { transform: scale(1) translate(0, 0); }
            15% { transform: scale(0.92) translate(-8px, 1px) rotate(-1.5deg); }
            30% { transform: scale(1.06) translate(7px, -2px) rotate(1.2deg); }
            50% { transform: scale(0.97) translate(-4px, 1px) rotate(-0.6deg); }
            70% { transform: scale(1.02) translate(2px, -1px); }
            85% { transform: scale(0.99) translate(-1px, 0); }
            100% { transform: scale(1) translate(0, 0) rotate(0deg); }
        }
        /* Gelombang Splash Shockwave */
        .crash-shockwave {
            position: absolute;
            width: 8px;
            height: 8px;
            border: 2px solid #ef4444;
            border-radius: 50%;
            pointer-events: none;
            transform: translate(-50%, -50%) scale(0.2);
            animation: shockwaveExpand 0.5s ease-out forwards;
            z-index: 10;
        }
        @keyframes shockwaveExpand {
            0% {
                transform: translate(-50%, -50%) scale(0.2);
                opacity: 1;
                border-color: #ef4444;
            }
            50% {
                border-color: #f59e0b;
            }
            100% {
                transform: translate(-50%, -50%) scale(6);
                opacity: 0;
                border-color: #38bdf8;
            }
        }
        /* Percikan Partikel Splash */
        .crash-spark {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            transform: translate(-50%, -50%);
            animation: sparkFly 0.5s cubic-bezier(0.25, 1, 0.5, 1) forwards;
            z-index: 11;
        }
        @keyframes sparkFly {
            0% {
                transform: translate(-50%, -50%) scale(1.3);
                opacity: 1;
            }
            100% {
                transform: translate(calc(-50% + var(--tx)), calc(-50% + var(--ty))) scale(0);
                opacity: 0;
            }
        }
        .ticker-move {
            display: inline-flex;
            align-items: center;
            height: 100%;
            width: max-content;
            animation: tickerContinuous 14s linear infinite;
        }
        .ticker-block {
            display: inline-flex;
            align-items: center;
            height: 100%;
            padding-right: 0;
            font-size: 11px;
            color: #333;
            font-family: monospace;
            line-height: 1;
            white-space: nowrap;
            transition: color 0.25s ease;
        }
        .ticker-wrap:hover .ticker-block {
            color: #f8fafc;
        }
        /* Pengecualian Khusus Ukuran Bar Miring (Full-Height Kolom) */
        .ticker-bar {
            font-size: 24px;
            font-weight: 900;
            line-height: 22px;
            height: 22px;
            color: #000;
            letter-spacing: -3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            transition: color 0.25s ease;
        }
        .ticker-wrap:hover .ticker-bar {
            color: #38bdf8;
        }
        @keyframes tickerContinuous {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>
    <div class="ticker-wrap">
        <div class="ticker-move">
            <div class="ticker-block">
                <span>&copy; <?= date('Y') ?> &mdash; Uji Kompetensi BNSP &bull; Sistem Inventaris Barang &bull; Bagus Dwicky Primananda</span><span class="ticker-bar">///</span>
            </div>
            <div class="ticker-block">
                <span>&copy; <?= date('Y') ?> &mdash; Uji Kompetensi BNSP &bull; Sistem Inventaris Barang &bull; Bagus Dwicky Primananda</span><span class="ticker-bar">///</span>
            </div>
        </div>
    </div>

    <!-- Script Easter Egg Rahasia (Klik 2x Running Text, Shortcut Ctrl+Shift+M, atau Klik 3x Judul) -->
    <script>
        let clickCount = 0;
        let timer = null;

        function triggerModern() {
            window.location.href = 'index.php?mode=pro';
        }

        const tickerWrap = document.querySelector('.ticker-wrap');
        if (tickerWrap) {
            // Trigger 1x Click: Efek Splash / Tabrakan
            tickerWrap.addEventListener('click', (e) => {
                tickerWrap.classList.remove('crash-active');
                void tickerWrap.offsetWidth; // trigger reflow
                tickerWrap.classList.add('crash-active');

                // Koordinat titik klik
                const rect = tickerWrap.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const clickY = e.clientY - rect.top;

                // Gelombang kejut (shockwave)
                const shockwave = document.createElement('div');
                shockwave.className = 'crash-shockwave';
                shockwave.style.left = clickX + 'px';
                shockwave.style.top = clickY + 'px';
                tickerWrap.appendChild(shockwave);
                setTimeout(() => shockwave.remove(), 550);

                // Percikan partikel warna-warni splash
                const colors = ['#ef4444', '#f59e0b', '#38bdf8', '#10b981', '#a855f7', '#ffffff'];
                for (let i = 0; i < 14; i++) {
                    const spark = document.createElement('div');
                    spark.className = 'crash-spark';
                    spark.style.left = clickX + 'px';
                    spark.style.top = clickY + 'px';
                    spark.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    
                    const angle = Math.random() * Math.PI * 2;
                    const velocity = 25 + Math.random() * 50;
                    const tx = Math.cos(angle) * velocity;
                    const ty = Math.sin(angle) * velocity;
                    const size = 3 + Math.random() * 4;
                    
                    spark.style.width = size + 'px';
                    spark.style.height = size + 'px';
                    spark.style.setProperty('--tx', tx + 'px');
                    spark.style.setProperty('--ty', ty + 'px');
                    
                    tickerWrap.appendChild(spark);
                    setTimeout(() => spark.remove(), 500);
                }
            });

            // Trigger Double Click (2x Klik): Direct ke Mode Modern
            tickerWrap.addEventListener('dblclick', triggerModern);
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
