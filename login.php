<?php
require_once __DIR__ . '/config/config.php';

// Jika sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php?action=dashboard');
    exit;
}

$error = '';
$username = '';
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi CSRF Token (Langkah Kerja 2: Security)
    if (!verifyCsrfToken($token)) {
        $error = 'Sesi tidak valid / CSRF token kadaluarsa. Silakan muat ulang halaman.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Username dan Password wajib diisi.';
    } else {
        $userModel = new User();
        $user = $userModel->authenticate($username, $password);

        if ($user) {
            // Set session (Langkah Kerja 6 & 7: Variabel Internal $_SESSION)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];

            setFlash('success', "Selamat datang kembali, {$user['nama_lengkap']}!");
            header('Location: index.php?action=dashboard');
            exit;
        } else {
            $error = 'Username atau Password salah. (Gunakan admin / admin123)';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Smart Inventory BNSP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- SweetAlert2 Pre-existing component for Toast -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Flash / Error Toast Data -->
    <?php if (!empty($error)): ?>
        <div id="flash-data" data-type="error" data-message="<?= htmlspecialchars($error) ?>"></div>
    <?php elseif ($flash): ?>
        <div id="flash-data" data-type="<?= htmlspecialchars($flash['type']) ?>" data-message="<?= htmlspecialchars($flash['message']) ?>"></div>
    <?php endif; ?>

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="brand-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <h1 style="font-size: 1.45rem; font-weight: 800; margin-bottom: 0.25rem;">Smart Inventory Pro</h1>
                <p style="color: var(--text-secondary); font-size: 0.85rem;">Sistem Demonstrasi Uji Kompetensi BNSP Skenario 3</p>
            </div>

            <form action="login.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <div class="form-group">
                    <label class="form-label" for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Contoh: admin" value="<?= htmlspecialchars($username) ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.85rem;">
                    <i class="fas fa-sign-in-alt"></i> Masuk ke Sistem
                </button>
            </form>

            <div style="margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color); font-size: 0.82rem; color: var(--text-muted); text-align: center;">
                <p style="margin-bottom: 0.65rem; font-weight: 600; color: var(--text-secondary);">Akun Default untuk Uji Asesor:</p>
                <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('username').value='admin';document.getElementById('password').value='admin123';showToast('info', 'Kredensial Admin berhasil diisikan!');">
                        <i class="fas fa-key"></i> Isi Akun Admin
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
