<?php
/**
 * ==============================================================================
 * SKRIP HAPUS PRODUK (PHP NATIVE MURNI)
 * Memenuhi Kriteria Uji BNSP:
 * - Langkah Kerja 9: Manipulasi Data Basis Data (DELETE)
 * ==============================================================================
 */
require_once __DIR__ . '/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // Langkah Kerja 9: Menjalankan Perintah DELETE FROM
    $stmt = $pdo->prepare("DELETE FROM produk WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header('Location: index.php?pesan=hapus_sukses');
exit;
