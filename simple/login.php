<?php
require_once __DIR__ . '/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = clean($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Username dan Password wajib diisi.';
    } else {
        // Query dengan Prepared Statement (Anti SQL-Injection)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];

            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau Password salah! (Default: admin / admin123)';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Inventaris Sederhana</title>
    <link rel="stylesheet" href="../assets/css/simple.css">
</head>
<body>
    <div class="container" style="max-width: 400px; margin-top: 50px;">
        <h2 style="text-align: center; margin-bottom: 5px;">Halaman Login</h2>
        <p style="text-align: center; color: #64748b; font-size: 13px; margin-bottom: 20px;">Aplikasi Web PHP Native (BNSP Skenario 3)</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">Login</button>
        </form>

        <div style="margin-top: 20px; text-align: center; font-size: 12px; color: #64748b;">
            <p>Akun Pengujian: <strong>admin</strong> / <strong>admin123</strong></p>
            <button type="button" class="btn btn-secondary btn-sm" style="margin-top: 8px;" onclick="document.getElementById('username').value='admin';document.getElementById('password').value='admin123';">
                Isi Otomatis Akun Admin
            </button>
        </div>

        <div style="margin-top: 20px; text-align: center; font-size: 12px; border-top: 1px solid #e2e8f0; padding-top: 10px;">
            <a href="../index.php" style="color: #64748b; text-decoration: none;">&larr; Kembali ke Mode Modern</a>
        </div>
    </div>
</body>
</html>
