<?php
/**
 * Lembar Panduan Asesor (9 Langkah Kerja BNSP Skenario 3)
 */
require_once __DIR__ . '/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Asesor BNSP - Sistem Inventaris</title>
    <link rel="stylesheet" href="assets/css/native.css?v=<?= time() ?>">
    <style>
        .step-box {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #2563eb;
            padding: 14px 18px;
            margin-bottom: 14px;
            border-radius: 3px;
        }
        .step-box h3 {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .code-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 10px;
            font-family: Consolas, Monaco, monospace;
            font-size: 12px;
            color: #0f172a;
            border-radius: 3px;
            margin-top: 6px;
            display: block;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-header">
            <div>
                <h1>Panduan Jawaban Asesor (9 Langkah Kerja)</h1>
                <small style="color: #64748b;">Standar Unit: <strong>TIK.PR08.007.01</strong> &amp; <strong>TIK.PR08.009.01</strong></small>
            </div>
            <div class="nav-links">
                <a href="index.php">&larr; Kembali ke Data Produk</a>
            </div>
        </div>

        <!-- Langkah 1 & 5 -->
        <div class="step-box">
            <h3>Langkah 1 &amp; 5: Menjelaskan Kebutuhan Software</h3>
            <p>Aplikasi web ini membutuhkan lingkungan runtime:</p>
            <ul style="margin-left: 20px; margin-top: 5px;">
                <li><strong>Bahasa Pemrograman:</strong> PHP versi 8.x dengan ekstensi <code>pdo_mysql</code>, <code>session</code>, <code>openssl</code>.</li>
                <li><strong>Basis Data (DBMS):</strong> MySQL 8.x / MariaDB (Port 3306).</li>
                <li><strong>Web Server:</strong> Nginx atau Apache (HTTP Port 80 / 443).</li>
            </ul>
        </div>

        <!-- Langkah 2 -->
        <div class="step-box">
            <h3>Langkah 2: Mempersiapkan Security (Keamanan)</h3>
            <p>3 Pilar keamanan yang diterapkan pada kode:</p>
            <ul style="margin-left: 20px; margin-top: 5px;">
                <li><strong>Anti-SQL Injection:</strong> Seluruh query memakai <em>PDO Prepared Statements</em> (<code>$pdo->prepare()</code>).</li>
                <li><strong>Anti-XSS:</strong> Sanitasi output form/tabel memakai fungsi <code>clean()</code> (<code>htmlspecialchars()</code>).</li>
                <li><strong>Keamanan Password:</strong> Enkripsi hash menggunakan <code>password_hash()</code> dan verifikasi <code>password_verify()</code>.</li>
            </ul>
        </div>

        <!-- Langkah 3 -->
        <div class="step-box">
            <h3>Langkah 3: Menggunakan Sintaks Khusus MySQL (Relasi, Agregasi, Filter)</h3>
            <p>Query yang digunakan pada file <code>index.php</code>:</p>
            <span class="code-box">SELECT p.*, k.nama_kategori FROM produk p INNER JOIN kategori k ON p.kategori_id = k.id WHERE p.nama_produk LIKE :search ORDER BY p.id DESC;</span>
            <span class="code-box">SELECT COUNT(*) as total_item, SUM(stok) as total_stok, SUM(harga_jual * stok) as total_aset FROM produk;</span>
        </div>

        <!-- Langkah 4 -->
        <div class="step-box">
            <h3>Langkah 4: Pengaksesan Basis Data (Koneksi PDO)</h3>
            <p>Koneksi dibuat di <code>koneksi.php</code> dengan objek <code>new PDO(...)</code> dan penanganan error <code>try...catch (PDOException $e)</code>.</p>
        </div>

        <!-- Langkah 6 & 7 -->
        <div class="step-box">
            <h3>Langkah 6 &amp; 7: Konsep Array &amp; Variabel Superglobal PHP</h3>
            <ul style="margin-left: 20px;">
                <li><strong>Array Asosiatif:</strong> Hasil data database (contoh: <code>$p['nama_produk']</code>, <code>$p['harga_jual']</code>).</li>
                <li><strong>Variabel Superglobal:</strong> <code>$_GET</code> (filter/cari), <code>$_POST</code> (form simpan), dan <code>$_SESSION</code> (login).</li>
            </ul>
        </div>

        <!-- Langkah 8 -->
        <div class="step-box">
            <h3>Langkah 8: Menerapkan Fungsi Buatan Sendiri</h3>
            <p>Fungsi kustom buatan sendiri di <code>koneksi.php</code>:</p>
            <span class="code-box">function clean($data) { return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8'); }</span>
            <span class="code-box">function rupiah($angka) { return 'Rp ' . number_format((float)$angka, 0, ',', '.'); }</span>
        </div>

        <!-- Langkah 9 -->
        <div class="step-box">
            <h3>Langkah 9: Manipulasi Data Basis Data (CRUD Lengkap)</h3>
            <ul style="margin-left: 20px;">
                <li><strong>CREATE:</strong> File <code>tambah.php</code> &rarr; Query <code>INSERT INTO produk ...</code></li>
                <li><strong>READ:</strong> File <code>index.php</code> &rarr; Query <code>SELECT ... INNER JOIN ...</code></li>
                <li><strong>UPDATE:</strong> File <code>edit.php</code> &rarr; Query <code>UPDATE produk SET ...</code></li>
                <li><strong>DELETE:</strong> File <code>hapus.php</code> &rarr; Query <code>DELETE FROM produk WHERE id = ...</code></li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 15px;">
            <a href="index.php" class="btn btn-primary">&larr; Kembali ke Data Produk</a>
        </div>

        <div class="footer-simple">
            <p>Smart Inventory Pro &mdash; Standar Kompetensi Kerja Nasional Indonesia (SKKNI)</p>
        </div>
    </div>
</body>
</html>
