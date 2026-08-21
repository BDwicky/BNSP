<?php
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
    <title>Panduan Asesor (9 Langkah) - Mode Sederhana</title>
    <link rel="stylesheet" href="../assets/css/simple.css">
    <style>
        .step-box {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .step-title {
            font-size: 15px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .code-snippet {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            font-family: monospace;
            font-size: 13px;
            color: #0f172a;
            border-radius: 3px;
            margin-top: 6px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1>Panduan Jawaban Asesor (9 Langkah Kerja)</h1>
                <small style="color: #64748b;">Rangkuman Singkat Standar SKKNI BNSP Pemrograman Web (Skenario 3)</small>
            </div>
            <nav>
                <a href="index.php">&larr; Kembali ke Data Produk</a>
            </nav>
        </header>

        <!-- Langkah 1 & 5 -->
        <div class="step-box">
            <div class="step-title">Langkah 1 &amp; 5: Menjelaskan Kebutuhan Software</div>
            <p><strong>Jawaban ke Asesor:</strong> Aplikasi ini membutuhkan server dengan spesifikasi:</p>
            <ul style="margin-left: 20px; margin-top: 5px;">
                <li><strong>Bahasa Pemrograman:</strong> PHP versi 8.x (dengan ekstensi <code>pdo_mysql</code>, <code>session</code>, <code>openssl</code>).</li>
                <li><strong>Basis Data (DBMS):</strong> MySQL 8.x / MariaDB.</li>
                <li><strong>Web Server:</strong> Nginx atau Apache (HTTP Port 80 / 443).</li>
            </ul>
        </div>

        <!-- Langkah 2 -->
        <div class="step-box">
            <div class="step-title">Langkah 2: Mempersiapkan Aspek Keamanan (Security)</div>
            <p><strong>Jawaban ke Asesor:</strong> 3 pilar keamanan yang diterapkan:</p>
            <ul style="margin-left: 20px; margin-top: 5px;">
                <li><strong>Anti-SQL Injection:</strong> Menggunakan PDO Prepared Statements (<code>prepare()</code> dan <code>execute()</code>).</li>
                <li><strong>Anti-XSS:</strong> Sanitasi output menggunakan <code>htmlspecialchars()</code> pada fungsi <code>clean()</code>.</li>
                <li><strong>Keamanan Password:</strong> Enkripsi hash menggunakan fungsi standar <code>password_hash()</code> dan verifikasi dengan <code>password_verify()</code>.</li>
            </ul>
        </div>

        <!-- Langkah 3 -->
        <div class="step-box">
            <div class="step-title">Langkah 3: Menggunakan Sintaks Khusus MySQL</div>
            <p><strong>Jawaban ke Asesor:</strong> Contoh query relasional dan agregasi yang digunakan:</p>
            <span class="code-snippet">SELECT p.*, k.nama_kategori FROM produk p INNER JOIN kategori k ON p.kategori_id = k.id;</span>
            <span class="code-snippet">SELECT COUNT(*) as total_item, SUM(stok) as total_stok, SUM(harga_jual * stok) as total_aset FROM produk;</span>
        </div>

        <!-- Langkah 4 -->
        <div class="step-box">
            <div class="step-title">Langkah 4: Pengaksesan Basis Data (Koneksi)</div>
            <p><strong>Jawaban ke Asesor:</strong> Koneksi dibuat di file <code>koneksi.php</code> menggunakan objek <code>new PDO(...)</code> dengan penanganan error <code>try...catch (PDOException $e)</code>.</p>
        </div>

        <!-- Langkah 6 & 7 -->
        <div class="step-box">
            <div class="step-title">Langkah 6 &amp; 7: Konsep Variabel &amp; Array PHP</div>
            <p><strong>Jawaban ke Asesor:</strong></p>
            <ul style="margin-left: 20px; margin-top: 5px;">
                <li><strong>Array Asosiatif:</strong> Mengambil hasil database (<code>$row['nama_produk']</code>, <code>$row['harga_jual']</code>).</li>
                <li><strong>Variabel Superglobal:</strong> <code>$_GET</code> (untuk fitur pencarian/filter), <code>$_POST</code> (untuk form simpan data), dan <code>$_SESSION</code> (untuk status login).</li>
            </ul>
        </div>

        <!-- Langkah 8 -->
        <div class="step-box">
            <div class="step-title">Langkah 8: Menerapkan Fungsi Buatan Sendiri</div>
            <p><strong>Jawaban ke Asesor:</strong> Menggunakan fungsi pembantu di <code>koneksi.php</code> seperti:</p>
            <span class="code-snippet">function clean($data) { return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8'); }</span>
            <span class="code-snippet">function rupiah($angka) { return 'Rp ' . number_format($angka, 0, ',', '.'); }</span>
        </div>

        <!-- Langkah 9 -->
        <div class="step-box">
            <div class="step-title">Langkah 9: Manipulasi Data (CRUD)</div>
            <p><strong>Jawaban ke Asesor:</strong></p>
            <ul style="margin-left: 20px; margin-top: 5px;">
                <li><strong>CREATE:</strong> File <code>tambah.php</code> (Query <code>INSERT INTO produk ...</code>).</li>
                <li><strong>READ:</strong> File <code>index.php</code> (Query <code>SELECT ... INNER JOIN ...</code>).</li>
                <li><strong>UPDATE:</strong> File <code>edit.php</code> (Query <code>UPDATE produk SET ...</code>).</li>
                <li><strong>DELETE:</strong> File <code>hapus.php</code> (Query <code>DELETE FROM produk WHERE id = ...</code>).</li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php" class="btn btn-primary">&larr; Kembali ke Halaman Utama</a>
        </div>

        <footer>
            <p>Smart Inventory Pro &mdash; Rangkuman Asesmen BNSP Skenario 3</p>
        </footer>
    </div>
</body>
</html>
