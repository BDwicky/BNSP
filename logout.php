<?php
require_once __DIR__ . '/config/config.php';

// Menghancurkan session (Langkah Kerja 6 & 7: Manajemen $_SESSION)
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

// Redirect ke login dengan flash message
session_start();
setFlash('info', 'Anda telah berhasil logout dari sistem.');
header('Location: login.php');
exit;
