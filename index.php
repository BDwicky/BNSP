<?php
/**
 * Entry Point Utama (Router & Controller Sederhana)
 * Memenuhi:
 * - Unit TIK.PR08.007.01 (Basis Data MySQL)
 * - Unit TIK.PR08.009.01 (Aplikasi Web Berbasis PHP)
 */

require_once __DIR__ . '/config/config.php';

// Pastikan user terautentikasi (Kecuali halaman login)
checkAuth();

// Tangkap action dari query parameter $_GET (Langkah Kerja 6 & 7: Superglobals)
$action = $_GET['action'] ?? 'dashboard';

// Handle Action: Hapus Produk (DELETE via POST)
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

// Router View Loader
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

// Render Template
require_once __DIR__ . '/views/header.php';
require_once __DIR__ . '/' . $viewFile;
require_once __DIR__ . '/views/footer.php';
