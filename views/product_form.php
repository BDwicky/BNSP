<?php
/**
 * View Form Tambah & Edit Produk
 * Memenuhi Langkah Kerja 2 (Validasi Form & CSRF), 8 (OOP Handler), 9 (CREATE & UPDATE MySQL)
 */
$productModel = new Product();
$categoryModel = new Category();
$categories = $categoryModel->getAllWithProductCount();

$isEdit = ($action === 'product_edit');
$productId = (int)($_GET['id'] ?? 0);

$product = [
    'id' => 0,
    'kode_produk' => '',
    'nama_produk' => '',
    'kategori_id' => '',
    'harga_beli' => '',
    'harga_jual' => '',
    'stok' => '0',
    'satuan' => 'Unit'
];

if ($isEdit) {
    $existing = $productModel->getById($productId);
    if (!$existing) {
        setFlash('error', 'Data produk tidak ditemukan.');
        header('Location: index.php?action=products');
        exit;
    }
    $product = $existing;
}

$errors = [];

// Proses POST Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    // 1. Verifikasi CSRF (Langkah Kerja 2: Security)
    if (!verifyCsrfToken($token)) {
        $errors['general'][] = 'Token keamanan tidak valid. Silakan ulangi pengiriman form.';
    } else {
        // 2. Ambil & Sanitasi Data Input (Langkah Kerja 2 & 6: Variabel Internal $_POST)
        $formData = [
            'kategori_id' => trim($_POST['kategori_id'] ?? ''),
            'kode_produk' => trim($_POST['kode_produk'] ?? ''),
            'nama_produk' => trim($_POST['nama_produk'] ?? ''),
            'harga_beli'  => trim($_POST['harga_beli'] ?? ''),
            'harga_jual'  => trim($_POST['harga_jual'] ?? ''),
            'stok'        => trim($_POST['stok'] ?? ''),
            'satuan'      => trim($_POST['satuan'] ?? 'Unit')
        ];

        // 3. Validasi Form via OOP Validator
        $validator = new Validator($formData);
        $validator->required('kode_produk', 'Kode Produk')
                  ->required('nama_produk', 'Nama Produk')
                  ->required('kategori_id', 'Kategori')
                  ->required('harga_beli', 'Harga Beli')
                  ->numericNonNegative('harga_beli', 'Harga Beli')
                  ->required('harga_jual', 'Harga Jual')
                  ->numericNonNegative('harga_jual', 'Harga Jual')
                  ->required('stok', 'Stok')
                  ->numericNonNegative('stok', 'Stok');

        // Cek Keunikan Kode Produk
        if ($productModel->isCodeExists($formData['kode_produk'], $isEdit ? $productId : null)) {
            $validator->required('kode_unik', 'Kode Produk')->getErrors();
            $errors['kode_produk'][] = 'Kode Produk "' . htmlspecialchars($formData['kode_produk']) . '" sudah terdaftar pada sistem.';
        }

        if ($validator->isValid() && empty($errors)) {
            if ($isEdit) {
                // UPDATE (Langkah Kerja 9)
                $success = $productModel->update($productId, $formData);
                $msg = "Produk '{$formData['nama_produk']}' berhasil diperbarui!";
            } else {
                // CREATE (Langkah Kerja 9)
                $success = $productModel->create($formData);
                $msg = "Produk baru '{$formData['nama_produk']}' berhasil ditambahkan ke basis data!";
            }

            if ($success) {
                setFlash('success', $msg);
                header('Location: index.php?action=products');
                exit;
            } else {
                $errors['general'][] = 'Terjadi kesalahan sistem saat menyimpan data ke database.';
            }
        } else {
            $errors = array_merge($errors, $validator->getErrors());
            // Pertahankan nilai form saat ada validasi gagal
            $product = array_merge($product, $formData);
        }
    }
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas <?= $isEdit ? 'fa-pen-to-square' : 'fa-plus-circle' ?>" style="color: var(--primary);"></i>
            <?= $isEdit ? 'Edit Data Produk' : 'Tambah Produk Baru' ?>
        </h1>
        <p class="page-subtitle"><?= $isEdit ? 'Perbarui informasi produk dan stok dalam database.' : 'Tambahkan item baru ke dalam tabel produk.' ?></p>
    </div>
    <div>
        <a href="index.php?action=products" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-file-signature"></i> Formulir Master Produk
        </div>
        <span class="badge badge-primary">Prepared Statement PDO</span>
    </div>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <?php foreach ($errors['general'] as $err): ?>
                    <div><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

        <div class="form-grid">
            <!-- Kode Produk -->
            <div class="form-group">
                <label class="form-label" for="kode_produk">
                    <i class="fas fa-barcode"></i> Kode Produk <span style="color: var(--danger);">*</span>
                </label>
                <input type="text" id="kode_produk" name="kode_produk" class="form-control" 
                       placeholder="Contoh: PRD-009" 
                       value="<?= htmlspecialchars($product['kode_produk'] ?? '') ?>" required>
                <?php if (!empty($errors['kode_produk'])): ?>
                    <small style="color: var(--danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">
                        <?= htmlspecialchars($errors['kode_produk'][0]) ?>
                    </small>
                <?php endif; ?>
            </div>

            <!-- Kategori -->
            <div class="form-group">
                <label class="form-label" for="kategori_id">
                    <i class="fas fa-layer-group"></i> Kategori Produk (Relasi FK) <span style="color: var(--danger);">*</span>
                </label>
                <select id="kategori_id" name="kategori_id" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ((string)$product['kategori_id'] === (string)$cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nama_kategori']) ?> (<?= htmlspecialchars($cat['kode_kategori']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['kategori_id'])): ?>
                    <small style="color: var(--danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">
                        <?= htmlspecialchars($errors['kategori_id'][0]) ?>
                    </small>
                <?php endif; ?>
            </div>
        </div>

        <!-- Nama Produk -->
        <div class="form-group">
            <label class="form-label" for="nama_produk">
                <i class="fas fa-tag"></i> Nama Lengkap Produk <span style="color: var(--danger);">*</span>
            </label>
            <input type="text" id="nama_produk" name="nama_produk" class="form-control" 
                   placeholder="Contoh: SSD NVMe 1TB Kingston KC3000" 
                   value="<?= htmlspecialchars($product['nama_produk'] ?? '') ?>" required>
            <?php if (!empty($errors['nama_produk'])): ?>
                <small style="color: var(--danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">
                    <?= htmlspecialchars($errors['nama_produk'][0]) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="form-grid">
            <!-- Harga Beli -->
            <div class="form-group">
                <label class="form-label" for="harga_beli">
                    <i class="fas fa-money-bill-wave"></i> Harga Beli (Rp) <span style="color: var(--danger);">*</span>
                </label>
                <input type="number" id="harga_beli" name="harga_beli" class="form-control" 
                       placeholder="0" step="1000" min="0" 
                       value="<?= htmlspecialchars($product['harga_beli'] ?? '') ?>" required>
                <?php if (!empty($errors['harga_beli'])): ?>
                    <small style="color: var(--danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">
                        <?= htmlspecialchars($errors['harga_beli'][0]) ?>
                    </small>
                <?php endif; ?>
            </div>

            <!-- Harga Jual -->
            <div class="form-group">
                <label class="form-label" for="harga_jual">
                    <i class="fas fa-receipt"></i> Harga Jual (Rp) <span style="color: var(--danger);">*</span>
                </label>
                <input type="number" id="harga_jual" name="harga_jual" class="form-control" 
                       placeholder="0" step="1000" min="0" 
                       value="<?= htmlspecialchars($product['harga_jual'] ?? '') ?>" required>
                <?php if (!empty($errors['harga_jual'])): ?>
                    <small style="color: var(--danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">
                        <?= htmlspecialchars($errors['harga_jual'][0]) ?>
                    </small>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-grid">
            <!-- Stok -->
            <div class="form-group">
                <label class="form-label" for="stok">
                    <i class="fas fa-cubes-stacked"></i> Jumlah Stok <span style="color: var(--danger);">*</span>
                </label>
                <input type="number" id="stok" name="stok" class="form-control" 
                       placeholder="0" min="0" 
                       value="<?= htmlspecialchars((string)($product['stok'] ?? 0)) ?>" required>
                <?php if (!empty($errors['stok'])): ?>
                    <small style="color: var(--danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">
                        <?= htmlspecialchars($errors['stok'][0]) ?>
                    </small>
                <?php endif; ?>
            </div>

            <!-- Satuan -->
            <div class="form-group">
                <label class="form-label" for="satuan">
                    <i class="fas fa-weight-scale"></i> Satuan Produk
                </label>
                <input type="text" id="satuan" name="satuan" class="form-control" 
                       placeholder="Contoh: Unit, Pcs, Box, Lusin, Set" 
                       value="<?= htmlspecialchars($product['satuan'] ?? 'Unit') ?>" required>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem; justify-content: flex-end;">
            <a href="index.php?action=products" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Produk Baru' ?>
            </button>
        </div>
    </form>
</div>
