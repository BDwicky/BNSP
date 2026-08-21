<?php
/**
 * View Panduan & Demonstrasi Uji Asesor (9 Langkah Kerja BNSP)
 * Khusus dirancang untuk menjawab dan mendemonstrasikan setiap butir penilaian Asesor
 */
require_once __DIR__ . '/../config/environment.php';

$envInfo = getSystemEnvironmentInfo();
$db = Database::getInstance()->getConnection();

// Data uji untuk demonstrasi array & variabel
$sampleIndexedArray = ['Komputer', 'Printer', 'Alat Pengolah Data', 'Router'];
$sampleAssociativeArray = [
    'nama_unit' => 'TIK.PR08.009.01',
    'judul' => 'Membuat Program Aplikasi Web Berbasis PHP',
    'status' => 'Kompeten'
];
$sampleMultiArray = [
    ['id' => 1, 'item' => 'Laptop Asus', 'kategori' => 'Elektronik', 'stok' => 15],
    ['id' => 2, 'item' => 'Router Mikrotik', 'kategori' => 'Jaringan', 'stok' => 12]
];

// Query agregasi SQL langsung untuk demo Langkah 3
$stmtDemoJoin = $db->query("
    SELECT k.nama_kategori, COUNT(p.id) AS total_item, COALESCE(SUM(p.stok), 0) AS total_stok
    FROM kategori k
    LEFT JOIN produk p ON k.id = p.kategori_id
    GROUP BY k.id, k.nama_kategori
");
$demoJoinResults = $stmtDemoJoin->fetchAll();
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-graduation-cap" style="color: var(--secondary);"></i> Panduan &amp; Bukti Demonstrasi Uji Asesor</h1>
        <p class="page-subtitle">Peta kesesuaian implementasi teknis kode sumber terhadap 9 Langkah Kerja Skenario 3.</p>
    </div>
    <div>
        <a href="index.php?action=products" class="btn btn-primary">
            <i class="fas fa-play-circle"></i> Buka Aplikasi Inventaris
        </a>
    </div>
</div>

<!-- Step Selector Buttons (Langkah Kerja 1 - 9) -->
<div class="step-nav">
    <button class="step-btn active" data-step="1">
        <div class="step-number">1</div>
        <div><strong>Langkah 1 &amp; 5</strong><br><small style="color: var(--text-muted);">Kebutuhan Software</small></div>
    </button>
    <button class="step-btn" data-step="2">
        <div class="step-number">2</div>
        <div><strong>Langkah 2</strong><br><small style="color: var(--text-muted);">Mempersiapkan Security</small></div>
    </button>
    <button class="step-btn" data-step="3">
        <div class="step-number">3</div>
        <div><strong>Langkah 3</strong><br><small style="color: var(--text-muted);">Sintaks Khusus MySQL</small></div>
    </button>
    <button class="step-btn" data-step="4">
        <div class="step-number">4</div>
        <div><strong>Langkah 4</strong><br><small style="color: var(--text-muted);">Pengaksesan Database</small></div>
    </button>
    <button class="step-btn" data-step="6">
        <div class="step-number">6</div>
        <div><strong>Langkah 6 &amp; 7</strong><br><small style="color: var(--text-muted);">Variabel &amp; Array PHP</small></div>
    </button>
    <button class="step-btn" data-step="8">
        <div class="step-number">8</div>
        <div><strong>Langkah 8</strong><br><small style="color: var(--text-muted);">Fungsi &amp; Kelas (OOP)</small></div>
    </button>
    <button class="step-btn" data-step="9">
        <div class="step-number">9</div>
        <div><strong>Langkah 9</strong><br><small style="color: var(--text-muted);">Manipulasi Data (CRUD)</small></div>
    </button>
</div>

<!-- ======================================================================== -->
<!-- TAB 1: Kebutuhan Software (Langkah 1 & 5) -->
<!-- ======================================================================== -->
<div id="step-content-1" class="step-content active">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-server" style="color: var(--primary);"></i> Langkah Kerja 1 &amp; 5: Menjelaskan Kebutuhan Software
            </div>
            <span class="badge badge-success"><i class="fas fa-check"></i> Siap Diuji</span>
        </div>

        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
            Aplikasi ini dibangun menggunakan arsitektur web modern yang membutuhkan runtime interpreter PHP, database relasional MySQL, dan web server. Berikut adalah inspeksi spesifikasi software yang sedang berjalan saat ini:
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h4 style="color: var(--primary); margin-bottom: 0.75rem;"><i class="fab fa-php"></i> PHP Runtime Environment</h4>
                <ul style="list-style: none; font-size: 0.88rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 0.4rem;">
                    <li><strong>PHP Version:</strong> <?= $envInfo['php_environment']['versi'] ?></li>
                    <li><strong>SAPI:</strong> <?= $envInfo['php_environment']['sapi'] ?></li>
                    <li><strong>Operating System:</strong> <?= $envInfo['php_environment']['os'] ?></li>
                    <li><strong>Memory Limit:</strong> <?= $envInfo['php_environment']['memory_limit'] ?></li>
                </ul>
            </div>

            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h4 style="color: var(--accent); margin-bottom: 0.75rem;"><i class="fas fa-database"></i> Database Management System (DBMS)</h4>
                <ul style="list-style: none; font-size: 0.88rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 0.4rem;">
                    <li><strong>Driver:</strong> MySQL / MariaDB (PDO Enabled)</li>
                    <li><strong>Host / Port:</strong> <?= DB_HOST ?>:<?= DB_PORT ?></li>
                    <li><strong>Database Name:</strong> <code><?= DB_NAME ?></code></li>
                    <li><strong>Charset:</strong> <?= DB_CHARSET ?></li>
                </ul>
            </div>

            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h4 style="color: var(--success); margin-bottom: 0.75rem;"><i class="fas fa-puzzle-piece"></i> Ekstensi PHP yang Aktif</h4>
                <div style="display: flex; gap: 0.4rem; flex-wrap: wrap; margin-top: 0.5rem;">
                    <?php foreach ($envInfo['php_environment']['extensions_loaded'] as $ext => $loaded): ?>
                        <span class="badge <?= $loaded ? 'badge-success' : 'badge-danger' ?>">
                            <i class="fas <?= $loaded ? 'fa-check' : 'fa-times' ?>"></i> <?= htmlspecialchars($ext) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================================== -->
<!-- TAB 2: Security (Langkah 2) -->
<!-- ======================================================================== -->
<div id="step-content-2" class="step-content">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-shield-halved" style="color: var(--danger);"></i> Langkah Kerja 2: Mempersiapkan Security
            </div>
            <span class="badge badge-success"><i class="fas fa-shield-alt"></i> Dilindungi 4 Lapisan Keamanan</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
            <!-- 1. Anti SQL Injection -->
            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h4 style="color: var(--primary); margin-bottom: 0.5rem;"><i class="fas fa-lock"></i> 1. PDO Prepared Statements</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
                    Menjamin parameter query dipisahkan secara ketat dari logika SQL sehingga kebal terhadap serangan <em>SQL Injection</em>.
                </p>
                <div class="code-block">
<span class="code-comment">// Parameter di-bind secara aman:</span>
<span class="code-var">$stmt</span> = <span class="code-var">$this</span>->db->prepare(<span class="code-str">"SELECT * FROM produk WHERE id = :id"</span>);
<span class="code-var">$stmt</span>->bindValue(<span class="code-str">':id'</span>, <span class="code-var">$id</span>, PDO::PARAM_INT);
<span class="code-var">$stmt</span>->execute();
                </div>
            </div>

            <!-- 2. Sanitasi XSS -->
            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h4 style="color: var(--success); margin-bottom: 0.5rem;"><i class="fas fa-code"></i> 2. Sanitasi Output (Anti-XSS)</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
                    Seluruh output ke browser di-escape dengan fungsi <code>htmlspecialchars(..., ENT_QUOTES, 'UTF-8')</code>.
                </p>
                <div class="code-block">
<span class="code-func">function</span> <span class="code-func">sanitize</span>(?<span class="code-keyword">string</span> <span class="code-var">$data</span>): <span class="code-keyword">string</span> {
    <span class="code-keyword">return</span> htmlspecialchars(trim(<span class="code-var">$data</span> ?? <span class="code-str">''</span>), ENT_QUOTES, <span class="code-str">'UTF-8'</span>);
}
                </div>
            </div>

            <!-- 3. Password Hashing -->
            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h4 style="color: var(--warning); margin-bottom: 0.5rem;"><i class="fas fa-key"></i> 3. Hashing Password Modern</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
                    Menggunakan algoritma hash standar industri <code>password_hash()</code> dan <code>password_verify()</code> tanpa plain-text.
                </p>
                <div class="code-block">
<span class="code-var">$hash</span> = <span class="code-func">password_hash</span>(<span class="code-var">$password</span>, PASSWORD_DEFAULT);
<span class="code-keyword">if</span> (<span class="code-func">password_verify</span>(<span class="code-var">$inputPassword</span>, <span class="code-var">$user</span>[<span class="code-str">'password'</span>])) {
    <span class="code-comment">// Login Sukses</span>
}
                </div>
            </div>

            <!-- 4. Proteksi CSRF -->
            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h4 style="color: var(--info); margin-bottom: 0.5rem;"><i class="fas fa-user-shield"></i> 4. Proteksi CSRF &amp; Session</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
                    Setiap form POST diverifikasi menggunakan random 32-byte cryptographic token yang disimpan di <code>$_SESSION</code>.
                </p>
                <div class="code-block">
<span class="code-comment">// Token CSRF saat ini:</span>
<span class="code-var">$_SESSION</span>[<span class="code-str">'csrf_token'</span>] = <span class="code-str">"<?= substr($_SESSION['csrf_token'] ?? '', 0, 16) ?>..."</span>;
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================================== -->
<!-- TAB 3: Sintaks Khusus MySQL (Langkah 3) -->
<!-- ======================================================================== -->
<div id="step-content-3" class="step-content">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-database" style="color: var(--primary);"></i> Langkah Kerja 3: Menggunakan Sintaks-sintaks Khusus MySQL/PostgreSQL
            </div>
            <span class="badge badge-primary">DDL, DML, Relasi &amp; Agregasi</span>
        </div>

        <p style="color: var(--text-secondary); margin-bottom: 1.25rem;">
            Berikut adalah bukti penerapan query relasional (<code>INNER/LEFT JOIN</code>), klausa pengelompokan (<code>GROUP BY</code>), dan fungsi agregasi (<code>COUNT</code>, <code>SUM</code>):
        </p>

        <div class="code-block">
<span class="code-keyword">SELECT</span> k.nama_kategori, <span class="code-func">COUNT</span>(p.id) <span class="code-keyword">AS</span> total_item, <span class="code-func">COALESCE</span>(<span class="code-func">SUM</span>(p.stok), 0) <span class="code-keyword">AS</span> total_stok
<span class="code-keyword">FROM</span> kategori k
<span class="code-keyword">LEFT JOIN</span> produk p <span class="code-keyword">ON</span> k.id = p.kategori_id
<span class="code-keyword">GROUP BY</span> k.id, k.nama_kategori;
        </div>

        <h4 style="margin: 1.25rem 0 0.75rem; color: var(--text-primary);">Hasil Eksekusi Query Nyata dari Database Saat Ini:</h4>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Total Jenis Item (COUNT)</th>
                        <th>Akumulasi Jumlah Stok (SUM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($demoJoinResults as $row): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['nama_kategori']) ?></strong></td>
                            <td><span class="badge badge-info"><?= $row['total_item'] ?> Item</span></td>
                            <td><span class="badge badge-success"><?= $row['total_stok'] ?> Unit</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ======================================================================== -->
<!-- TAB 4: Pengaksesan Database (Langkah 4) -->
<!-- ======================================================================== -->
<div id="step-content-4" class="step-content">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-network-wired" style="color: var(--accent);"></i> Langkah Kerja 4: Melakukan Pengaksesan Database
            </div>
            <span class="badge badge-info">Design Pattern: Singleton Connection</span>
        </div>

        <p style="color: var(--text-secondary); margin-bottom: 1rem;">
            Pengaksesan basis data diimplementasikan pada file <code>classes/Database.php</code> menggunakan class PDO dengan pola Singleton. Hal ini menjamin efisiensi memori karena hanya 1 koneksi yang dibuka selama <em>lifecycle</em> request.
        </p>

        <div class="code-block">
<span class="code-keyword">class</span> <span class="code-func">Database</span> {
    <span class="code-keyword">private static</span> ?Database <span class="code-var">$instance</span> = <span class="code-keyword">null</span>;
    <span class="code-keyword">private</span> ?PDO <span class="code-var">$connection</span> = <span class="code-keyword">null</span>;

    <span class="code-keyword">private function</span> <span class="code-func">__construct</span>() {
        <span class="code-var">$dsn</span> = <span class="code-str">"mysql:host="</span> . DB_HOST . <span class="code-str">";dbname="</span> . DB_NAME . <span class="code-str">";charset=utf8mb4"</span>;
        <span class="code-var">$this</span>->connection = <span class="code-keyword">new</span> PDO(<span class="code-var">$dsn</span>, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    <span class="code-keyword">public static function</span> <span class="code-func">getInstance</span>(): Database {
        <span class="code-keyword">if</span> (self::<span class="code-var">$instance</span> === <span class="code-keyword">null</span>) { self::<span class="code-var">$instance</span> = <span class="code-keyword">new</span> Database(); }
        <span class="code-keyword">return</span> self::<span class="code-var">$instance</span>;
    }
}
        </div>
    </div>
</div>

<!-- ======================================================================== -->
<!-- TAB 6: Array & Variabel Internal (Langkah 6 & 7) -->
<!-- ======================================================================== -->
<div id="step-content-6" class="step-content">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-code-branch" style="color: var(--warning);"></i> Langkah Kerja 6 &amp; 7: Konsep Array, Variabel, dan Variabel Internal PHP
            </div>
            <span class="badge badge-warning">Superglobals &amp; Data Structures</span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <!-- Array Structures -->
            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h4 style="color: var(--primary); margin-bottom: 0.75rem;"><i class="fas fa-list-ol"></i> 1. Tipe-Tipe Array dalam PHP</h4>
                <div style="font-size: 0.85rem; color: var(--text-secondary);">
                    <p><strong>a. Indexed Array:</strong></p>
                    <div class="code-block" style="margin: 0.4rem 0 1rem;"><?= htmlspecialchars(json_encode($sampleIndexedArray, JSON_PRETTY_PRINT)) ?></div>

                    <p><strong>b. Associative Array:</strong></p>
                    <div class="code-block" style="margin: 0.4rem 0 1rem;"><?= htmlspecialchars(json_encode($sampleAssociativeArray, JSON_PRETTY_PRINT)) ?></div>

                    <p><strong>c. Multidimensional Array:</strong></p>
                    <div class="code-block" style="margin: 0.4rem 0;"><?= htmlspecialchars(json_encode($sampleMultiArray, JSON_PRETTY_PRINT)) ?></div>
                </div>
            </div>

            <!-- Internal Superglobals -->
            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h4 style="color: var(--secondary); margin-bottom: 0.75rem;"><i class="fas fa-globe"></i> 2. Variabel Internal (Superglobal)</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
                    Data nyata yang tersimpan di dalam variabel superglobal server saat ini:
                </p>

                <div style="font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.75rem;">
                    <div>
                        <span class="badge badge-primary">$_GET</span>
                        <div class="code-block" style="margin: 0.3rem 0;"><?= htmlspecialchars(json_encode($_GET)) ?></div>
                    </div>
                    <div>
                        <span class="badge badge-success">$_SESSION</span>
                        <div class="code-block" style="margin: 0.3rem 0;">
                            <?= htmlspecialchars(json_encode([
                                'user_id' => $_SESSION['user_id'] ?? null,
                                'username' => $_SESSION['username'] ?? null,
                                'nama_lengkap' => $_SESSION['nama_lengkap'] ?? null,
                                'role' => $_SESSION['role'] ?? null
                            ])) ?>
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-info">$_SERVER (Ringkasan)</span>
                        <div class="code-block" style="margin: 0.3rem 0;">
                            <?= htmlspecialchars(json_encode([
                                'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? '',
                                'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? '',
                                'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? ''
                            ])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================================== -->
<!-- TAB 8: Fungsi & Kelas (Langkah 8) -->
<!-- ======================================================================== -->
<div id="step-content-8" class="step-content">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-cubes" style="color: var(--success);"></i> Langkah Kerja 8: Menerapkan Fungsi dan Kelas (OOP)
            </div>
            <span class="badge badge-success">Object-Oriented Programming</span>
        </div>

        <p style="color: var(--text-secondary); margin-bottom: 1.25rem;">
            Aplikasi mengadopsi prinsip Object-Oriented Programming (OOP) dengan memisahkan tanggung jawab kelas (Single Responsibility) dan fungsi pembantu (Helper functions):
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h4 style="color: var(--primary); margin-bottom: 0.5rem;"><i class="fas fa-sitemap"></i> Kelas-Kelas Utama dalam Sistem</h4>
                <ul style="font-size: 0.88rem; color: var(--text-secondary); list-style: none; display: flex; flex-direction: column; gap: 0.5rem;">
                    <li><code>classes/Database.php</code>: Pengelola koneksi tunggal PDO (Singleton).</li>
                    <li><code>classes/Product.php</code>: Model entitas produk, kalkulasi stok, dan CRUD.</li>
                    <li><code>classes/Category.php</code>: Model kategori master data relasional.</li>
                    <li><code>classes/User.php</code>: Pengelola user, hashing bcrypt, dan otentikasi.</li>
                    <li><code>classes/Validator.php</code>: Validasi form berantai (Fluent interface).</li>
                </ul>
            </div>

            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h4 style="color: var(--accent); margin-bottom: 0.5rem;"><i class="fas fa-code"></i> Fungsi-Fungsi Khusus (Helpers)</h4>
                <ul style="font-size: 0.88rem; color: var(--text-secondary); list-style: none; display: flex; flex-direction: column; gap: 0.5rem;">
                    <li><code>formatRupiah($angka)</code>: Konversi numerik ke format IDR mata uang.</li>
                    <li><code>formatTanggalIndo($datetime)</code>: Format penanggalan berbahasa Indonesia.</li>
                    <li><code>sanitize($input)</code>: Filter proteksi injeksi karakter berbahaya.</li>
                    <li><code>generateCsrfToken()</code> &amp; <code>verifyCsrfToken()</code>: Keamanan form.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================================== -->
<!-- TAB 9: Manipulasi Data (Langkah 9) -->
<!-- ======================================================================== -->
<div id="step-content-9" class="step-content">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-wrench" style="color: var(--info);"></i> Langkah Kerja 9: Melakukan Koneksi Database dan Manipulasi Data (CRUD)
            </div>
            <span class="badge badge-success">Create, Read, Update, Delete</span>
        </div>

        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
            Operasi manipulasi data telah terintegrasi penuh ke database MySQL <code>db_bnsp_inventaris</code>:
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border-left: 4px solid var(--success);">
                <h4 style="color: var(--success);"><i class="fas fa-plus"></i> CREATE</h4>
                <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0.5rem 0;">Menambah record produk baru dengan validasi dan penentuan status stok otomatis.</p>
                <a href="index.php?action=product_create" class="btn btn-primary btn-sm">Uji Tambah</a>
            </div>

            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border-left: 4px solid var(--primary);">
                <h4 style="color: var(--primary);"><i class="fas fa-eye"></i> READ</h4>
                <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0.5rem 0;">Menampilkan data dengan live search, filter kategori, dan pembagian halaman (pagination).</p>
                <a href="index.php?action=products" class="btn btn-secondary btn-sm">Uji Read</a>
            </div>

            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border-left: 4px solid var(--warning);">
                <h4 style="color: var(--warning);"><i class="fas fa-edit"></i> UPDATE</h4>
                <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0.5rem 0;">Memperbarui informasi harga, kategori, dan jumlah stok barang secara dinamis.</p>
                <a href="index.php?action=products" class="btn btn-warning btn-sm">Uji Update</a>
            </div>

            <div style="background: var(--bg-surface); padding: 1.25rem; border-radius: var(--radius-md); border-left: 4px solid var(--danger);">
                <h4 style="color: var(--danger);"><i class="fas fa-trash"></i> DELETE</h4>
                <p style="font-size: 0.82rem; color: var(--text-secondary); margin: 0.5rem 0;">Menghapus data dengan konfirmasi keamanan dialog dan proteksi token CSRF.</p>
                <a href="index.php?action=products" class="btn btn-danger btn-sm">Uji Delete</a>
            </div>
        </div>
    </div>
</div>
