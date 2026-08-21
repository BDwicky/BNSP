<?php
/**
 * ==============================================================================
 * LEMBAR JAWABAN & BUKTI 9 LANGKAH KERJA ASESOR BNSP (SKENARIO 3)
 * Unit: TIK.PR08.007.01 (Basis Data MySQL) & TIK.PR08.009.01 (Aplikasi Web PHP)
 * ==============================================================================
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
    <title>Lembar Jawaban Asesor (9 Langkah Kerja BNSP)</title>
    <style>
        body { font-family: sans-serif; margin: 20px; line-height: 1.6; }
        .menu-bar { background: #eee; padding: 10px; border: 1px solid #ccc; margin-bottom: 15px; }
        .menu-bar a { margin-right: 15px; text-decoration: none; font-weight: bold; color: #0066cc; }
        .step { background: #fafafa; border: 1px solid #ccc; padding: 12px 15px; margin-bottom: 15px; }
        .step h3 { margin-top: 0; color: #004488; }
        code { background: #f0f0f0; padding: 2px 5px; font-family: monospace; }
        pre { background: #f0f0f0; padding: 8px; border: 1px solid #ddd; overflow-x: auto; font-family: monospace; font-size: 12px; }
    </style>
</head>
<body>

    <h2>LEMBAR JAWABAN &amp; BUKTI UNJUK KERJA ASESOR BNSP</h2>
    <p>
        Skema: <strong>Pemrograman Web (Skenario 3)</strong> | 
        Unit: <strong>TIK.PR08.007.01</strong> &amp; <strong>TIK.PR08.009.01</strong>
    </p>

    <div class="menu-bar">
        <a href="index.php">&larr; Kembali ke Data Produk</a>
        <a href="../index.php?mode=pro" style="color: #6600cc;">Buka Mode Modern Dashboard</a>
    </div>

    <!-- Langkah 1 & 5 -->
    <div class="step">
        <h3>Langkah 1 &amp; 5: Menjelaskan Kebutuhan Perangkat Lunak (Software Requirement)</h3>
        <p><strong>Penjelasan ke Asesor:</strong> Aplikasi ini membutuhkan lingkungan runtime server:</p>
        <ul>
            <li><strong>Bahasa Pemrograman:</strong> PHP versi 8.x dengan ekstensi wajib <code>pdo_mysql</code>, <code>session</code>, <code>openssl</code>.</li>
            <li><strong>Basis Data (DBMS):</strong> MySQL 8.x / MariaDB (Port 3306).</li>
            <li><strong>Web Server:</strong> Nginx atau Apache (HTTP Port 80 / 443).</li>
        </ul>
    </div>

    <!-- Langkah 2 -->
    <div class="step">
        <h3>Langkah 2: Mempersiapkan Aspek Keamanan (Security)</h3>
        <p><strong>Penjelasan ke Asesor:</strong> 3 pilar keamanan yang diterapkan:</p>
        <ul>
            <li><strong>Anti-SQL Injection:</strong> Seluruh query menggunakan <em>PDO Prepared Statements</em> (<code>$pdo->prepare()</code>).</li>
            <li><strong>Anti-XSS:</strong> Sanitasi output pada tabel dan form menggunakan <code>htmlspecialchars()</code> pada fungsi <code>clean()</code>.</li>
            <li><strong>Keamanan Password:</strong> Password di-hash menggunakan algoritma BCRYPT via fungsi <code>password_hash()</code> dan diverifikasi via <code>password_verify()</code>.</li>
        </ul>
    </div>

    <!-- Langkah 3 -->
    <div class="step">
        <h3>Langkah 3: Menggunakan Sintaks Khusus MySQL (JOIN, Agregasi, Filter)</h3>
        <p><strong>Bukti Query pada <code>simple/index.php</code>:</strong></p>
        <pre>-- Query Relasi INNER JOIN dengan klausa WHERE LIKE
SELECT p.*, k.nama_kategori 
FROM produk p 
INNER JOIN kategori k ON p.kategori_id = k.id 
WHERE p.nama_produk LIKE :search 
ORDER BY p.id DESC;

-- Query Agregasi MySQL (COUNT &amp; SUM)
SELECT COUNT(*) as total_item, SUM(stok) as total_stok, SUM(harga_jual * stok) as total_aset FROM produk;</pre>
    </div>

    <!-- Langkah 4 -->
    <div class="step">
        <h3>Langkah 4: Pengaksesan Basis Data (Koneksi Database)</h3>
        <p><strong>Bukti Kode pada <code>simple/koneksi.php</code>:</strong></p>
        <pre>try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=db_bnsp_inventaris;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Koneksi Basis Data Gagal: " . $e->getMessage());
}</pre>
    </div>

    <!-- Langkah 6 & 7 -->
    <div class="step">
        <h3>Langkah 6 &amp; 7: Konsep Variabel &amp; Array PHP</h3>
        <p><strong>Penjelasan ke Asesor:</strong></p>
        <ul>
            <li><strong>Array Asosiatif:</strong> Menampung baris hasil database (<code>$p['nama_produk']</code>, <code>$p['harga_jual']</code>).</li>
            <li><strong>Variabel Superglobal:</strong> <code>$_GET</code> (filter/pencarian), <code>$_POST</code> (form input tambah/edit), dan <code>$_SESSION</code> (status login user).</li>
        </ul>
    </div>

    <!-- Langkah 8 -->
    <div class="step">
        <h3>Langkah 8: Menerapkan Fungsi Buatan Sendiri</h3>
        <p><strong>Bukti Fungsi pada <code>simple/koneksi.php</code>:</strong></p>
        <pre>function clean($data) {
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

function rupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}</pre>
    </div>

    <!-- Langkah 9 -->
    <div class="step">
        <h3>Langkah 9: Manipulasi Data Basis Data (CRUD Lengkap)</h3>
        <ul>
            <li><strong>CREATE (Tambah):</strong> File <code>simple/tambah.php</code> &rarr; Menjalankan <code>INSERT INTO produk ...</code></li>
            <li><strong>READ (Tampil):</strong> File <code>simple/index.php</code> &rarr; Menjalankan <code>SELECT ... INNER JOIN ...</code></li>
            <li><strong>UPDATE (Ubah):</strong> File <code>simple/edit.php</code> &rarr; Menjalankan <code>UPDATE produk SET ...</code></li>
            <li><strong>DELETE (Hapus):</strong> File <code>simple/hapus.php</code> &rarr; Menjalankan <code>DELETE FROM produk WHERE id = ...</code></li>
        </ul>
    </div>

    <p style="text-align: center; margin-top: 20px;">
        <a href="index.php"><strong>&larr; Kembali ke Halaman Utama Data Produk</strong></a>
    </p>

</body>
</html>
