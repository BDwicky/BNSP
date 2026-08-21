<?php
/**
 * ==============================================================================
 * HALAMAN LOGIN (PHP NATIVE MURNI TANPA CSS)
 * Memenuhi Kriteria Uji BNSP:
 * - Unit 2: TIK.PR08.009.01 (Aplikasi Web Berbasis PHP)
 * - Langkah Kerja 2: Keamanan Autentikasi Password Hashing BCRYPT
 * - Langkah Kerja 6 & 7: Variabel Superglobal $_POST & $_SESSION
 * ==============================================================================
 */
require_once __DIR__ . '/koneksi.php';

// Jika sudah login, redirect ke index.php
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$pesanError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Langkah Kerja 6 & 7: Menangkap variabel superglobal $_POST
    $username = clean($_POST['username'] ?? '');
    $password = clean($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $pesanError = 'Username dan Password tidak boleh kosong!';
    } else {
        // Langkah Kerja 2: Anti-SQL Injection dengan Prepared Statement
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        // Langkah Kerja 2: Verifikasi Enkripsi Hash BCRYPT
        if ($user && password_verify($password, $user['password'])) {
            // Langkah Kerja 6 & 7: Menyimpan Sesi ke $_SESSION
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role']         = $user['role'];

            header('Location: index.php');
            exit;
        } else {
            $pesanError = 'Username atau Password salah! (Default: admin / admin123)';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistem Inventaris (Uji BNSP Skenario 3)</title>
    <style>
        body { font-family: sans-serif; margin: 30px; line-height: 1.6; }
        .box { width: 350px; padding: 20px; border: 1px solid #999; margin: 40px auto; }
        .error { color: red; font-weight: bold; margin-bottom: 10px; }
        .easter-title { cursor: pointer; user-select: none; }
    </style>
</head>
<body>

    <div class="box">
        <h2 id="loginTitle" class="easter-title" title="">LOGIN SISTEM INVENTARIS</h2>
        <p><small>Aplikasi Manajemen Stok &amp; Barang</small></p>
        <hr>

        <?php if (!empty($pesanError)): ?>
            <div class="error"><?= $pesanError ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <p>
                <label for="username"><strong>Username:</strong></label><br>
                <input type="text" id="username" name="username" style="width: 100%; padding: 6px;" placeholder="admin" required autofocus>
            </p>
            <p>
                <label for="password"><strong>Password:</strong></label><br>
                <input type="password" id="password" name="password" style="width: 100%; padding: 6px;" placeholder="admin123" required>
            </p>
            <p>
                <button type="submit" style="padding: 6px 15px; cursor: pointer;">Masuk / Login</button>
            </p>
        </form>

        <hr>
        <p><small>Akun Pengujian Asesor: <strong>admin</strong> / <strong>admin123</strong></small></p>
        <button type="button" onclick="document.getElementById('username').value='admin';document.getElementById('password').value='admin123';" style="padding: 4px 8px; font-size: 11px;">
            Isi Otomatis Akun Admin
        </button>
    </div>

    <!-- Script Easter Egg: Shortcut Ctrl+Shift+M atau Klik 3x Judul -->
    <script>
        let clicks = 0;
        let timer = null;

        function launchPro() {
            window.location.href = '../index.php?mode=pro';
        }

        document.getElementById('loginTitle').addEventListener('click', () => {
            clicks++;
            clearTimeout(timer);
            if (clicks >= 3) {
                launchPro();
            } else {
                timer = setTimeout(() => { clicks = 0; }, 1000);
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.shiftKey && (e.key === 'M' || e.key === 'm')) {
                e.preventDefault();
                launchPro();
            }
        });
    </script>
</body>
</html>
