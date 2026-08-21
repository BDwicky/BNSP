<?php
/**
 * View Dashboard
 * Memenuhi Langkah Kerja 3 (Agregasi Data SQL), Langkah Kerja 6 & 7 (Array/Variabel), dan Library Pre-existing (Chart.js)
 */
$productModel = new Product();
$categoryModel = new Category();

$stats = $productModel->getSummaryStats();
$categories = $categoryModel->getAllWithProductCount();
$stockChartData = $productModel->getStockPerCategory();
$recentProducts = $productModel->getAll('', 0, 5, 0);

// Menyiapkan data array untuk Chart.js (Langkah Kerja 6 & 7: Pengolahan Array PHP)
$chartLabels = array_column($stockChartData, 'nama_kategori');
$chartValues = array_map('intval', array_column($stockChartData, 'total_stok'));
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-chart-pie" style="color: var(--primary);"></i> Dashboard Inventaris</h1>
        <p class="page-subtitle">Ringkasan statistik data barang, kalkulasi nilai aset, dan status stok terkini.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="index.php?action=asesor" class="btn btn-secondary">
            <i class="fas fa-graduation-cap"></i> Panduan Uji Asesor
        </a>
        <a href="index.php?action=product_create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Produk Baru
        </a>
    </div>
</div>

<!-- Stats Summary Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-box-open"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Jenis Produk</div>
            <div class="stat-value"><?= number_format($stats['total_produk'] ?? 0) ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-cubes"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Stok Fisik</div>
            <div class="stat-value"><?= number_format($stats['total_stok'] ?? 0) ?> <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-secondary);">Item</span></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-coins"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Nilai Aset Jual</div>
            <div class="stat-value" style="font-size: 1.35rem;"><?= formatRupiah($stats['total_aset_jual'] ?? 0) ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Perlu Restock / Habis</div>
            <div class="stat-value"><?= (int)($stats['produk_menipis'] ?? 0) + (int)($stats['produk_habis'] ?? 0) ?> <span style="font-size: 0.9rem; font-weight: 500; color: var(--warning);">Item</span></div>
        </div>
    </div>
</div>

<!-- Charts & Summary Layout -->
<div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Chart.js Pre-existing Component -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-chart-pie" style="color: var(--secondary);"></i> Distribusi Stok per Kategori
            </div>
            <span class="badge badge-info"><i class="fas fa-code"></i> Chart.js</span>
        </div>
        <div style="height: 280px; position: relative;">
            <canvas id="inventoryChart" 
                    data-labels='<?= json_encode($chartLabels) ?>' 
                    data-values='<?= json_encode($chartValues) ?>'>
            </canvas>
        </div>
    </div>

    <!-- Overview Kategori & Produk Terbaru -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-layer-group" style="color: var(--primary);"></i> Rekap Kategori Master Data
            </div>
            <a href="index.php?action=products" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Produk</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($cat['kode_kategori']) ?></code></td>
                            <td><strong><?= htmlspecialchars($cat['nama_kategori']) ?></strong></td>
                            <td><span class="badge badge-primary"><?= (int)$cat['total_produk'] ?> Produk</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tabel Produk Terbaru -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-clock-rotate-left" style="color: var(--accent);"></i> 5 Produk Inventaris Terbaru (MySQL Query: LIMIT 5)
        </div>
        <a href="index.php?action=products" class="btn btn-primary btn-sm">
            <i class="fas fa-list"></i> Kelola Seluruh Data Produk
        </a>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga Jual</th>
                    <th>Stok &amp; Status</th>
                    <th>Tanggal Input</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentProducts)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data produk.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentProducts as $prod): ?>
                        <tr>
                            <td><strong style="color: var(--primary);"><?= htmlspecialchars($prod['kode_produk']) ?></strong></td>
                            <td><?= htmlspecialchars($prod['nama_produk']) ?></td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($prod['nama_kategori']) ?></span></td>
                            <td><?= formatRupiah($prod['harga_jual']) ?></td>
                            <td><?= getStatusBadge($prod['status'], (int)$prod['stok']) ?></td>
                            <td style="font-size: 0.82rem; color: var(--text-muted);"><?= formatTanggalIndo($prod['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
