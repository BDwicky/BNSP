<?php
/**
 * View Product List (READ, SEARCH, FILTER, PAGINATION, DELETE)
 * Memenuhi Langkah Kerja 3 (Sintaks MySQL), 6 & 7 (Array/Superglobal $_GET), 9 (Manipulasi Data)
 */
$productModel = new Product();
$categoryModel = new Category();

// Tangkap parameter query dari superglobal $_GET (Langkah Kerja 6 & 7)
$search = trim($_GET['search'] ?? '');
$kategoriId = (int)($_GET['kategori_id'] ?? 0);
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 6;
$offset = ($page - 1) * $limit;

// Ambil data produk & total count
$products = $productModel->getAll($search, $kategoriId, $limit, $offset);
$totalRows = $productModel->countAll($search, $kategoriId);
$totalPages = max(1, ceil($totalRows / $limit));
$categories = $categoryModel->getAllWithProductCount();
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-boxes" style="color: var(--primary);"></i> Manajemen Data Produk</h1>
        <p class="page-subtitle">Pencarian, filtering kategori relasional, dan pengelolaan data master inventaris.</p>
    </div>
    <div>
        <a href="index.php?action=product_create" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Tambah Produk Baru
        </a>
    </div>
</div>

<!-- Filter & Search Bar Form (Memanfaatkan $_GET) -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="index.php" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        <input type="hidden" name="action" value="products">

        <div style="flex: 2; min-width: 240px;">
            <label class="form-label" for="search"><i class="fas fa-search"></i> Pencarian Produk</label>
            <input type="text" id="search" name="search" class="form-control" placeholder="Cari nama atau kode produk..." value="<?= htmlspecialchars($search) ?>">
        </div>

        <div style="flex: 1.5; min-width: 200px;">
            <label class="form-label" for="kategori_id"><i class="fas fa-filter"></i> Filter Kategori</label>
            <select id="kategori_id" name="kategori_id" class="form-control">
                <option value="0">-- Semua Kategori --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($kategoriId == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nama_kategori']) ?> (<?= $cat['total_produk'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Terapkan Filter
            </button>
            <?php if (!empty($search) || $kategoriId > 0): ?>
                <a href="index.php?action=products" class="btn btn-secondary" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i> Reset
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Product Table Card -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-table-list"></i> Daftar Inventaris (Total: <?= $totalRows ?> Data)
        </div>
        <span style="font-size: 0.82rem; color: var(--text-muted);">Halaman <?= $page ?> dari <?= $totalPages ?></span>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Stok &amp; Satuan</th>
                    <th>Status</th>
                    <th style="text-align: center; width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                            <i class="fas fa-inbox" style="font-size: 2.5rem; margin-bottom: 0.5rem; display: block; opacity: 0.5;"></i>
                            Tidak ada data produk yang sesuai dengan kriteria pencarian.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = $offset + 1; foreach ($products as $prod): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong style="color: var(--primary); font-family: monospace; font-size: 0.95rem;"><?= htmlspecialchars($prod['kode_produk']) ?></strong></td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-primary);"><?= htmlspecialchars($prod['nama_produk']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">ID: #<?= $prod['id'] ?> | Terakhir update: <?= date('d/m/Y', strtotime($prod['updated_at'])) ?></div>
                            </td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($prod['nama_kategori']) ?></span></td>
                            <td><?= formatRupiah($prod['harga_beli']) ?></td>
                            <td><strong style="color: #34d399;"><?= formatRupiah($prod['harga_jual']) ?></strong></td>
                            <td>
                                <strong><?= (int)$prod['stok'] ?></strong>
                                <span style="font-size: 0.8rem; color: var(--text-secondary);"><?= htmlspecialchars($prod['satuan']) ?></span>
                            </td>
                            <td><?= getStatusBadge($prod['status'], (int)$prod['stok']) ?></td>
                            <td>
                                <div style="display: flex; gap: 0.4rem; justify-content: center;">
                                    <a href="index.php?action=product_edit&id=<?= $prod['id'] ?>" class="btn btn-secondary btn-sm btn-icon" title="Edit Produk">
                                        <i class="fas fa-edit" style="color: #38bdf8;"></i>
                                    </a>
                                    <form method="POST" action="index.php?action=product_delete" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                        <input type="hidden" name="id" value="<?= $prod['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm btn-icon btn-delete-confirm" data-name="<?= htmlspecialchars($prod['nama_produk']) ?>" title="Hapus Produk">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Navigation -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="index.php?action=products&page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&kategori_id=<?= $kategoriId ?>" class="page-item">
                    <i class="fas fa-chevron-left"></i> Prev
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="index.php?action=products&page=<?= $i ?>&search=<?= urlencode($search) ?>&kategori_id=<?= $kategoriId ?>" class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="index.php?action=products&page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&kategori_id=<?= $kategoriId ?>" class="page-item">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
