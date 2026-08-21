<?php
/**
 * Skrip Hapus Produk (Langkah Kerja 9: DELETE)
 */
require_once __DIR__ . '/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM produk WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header('Location: index.php?pesan=hapus_sukses');
exit;
