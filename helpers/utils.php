<?php
/**
 * Helper Utilities & Functions
 * Memenuhi Langkah Kerja 2 (Security) & Langkah Kerja 8 (Menerapkan Fungsi)
 */

/**
 * 1. Fungsi Sanitasi Input untuk Mencegah XSS (Langkah Kerja 2)
 */
function sanitize(?string $data): string {
    if ($data === null) {
        return '';
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * 2. Fungsi Format Angka ke Rupiah (Langkah Kerja 8: Fungsi & Return Value)
 */
function formatRupiah($angka): string {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

/**
 * 3. Fungsi Format Tanggal Indonesia
 */
function formatTanggalIndo(string $datetime): string {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $timestamp = strtotime($datetime);
    $tgl = date('d', $timestamp);
    $bln = $bulan[(int)date('m', $timestamp)];
    $thn = date('Y', $timestamp);
    $jam = date('H:i', $timestamp);
    return "$tgl $bln $thn, $jam WIB";
}

/**
 * 4. Fungsi Menghasilkan Badge HTML Status Stok
 */
function getStatusBadge(string $status, int $stok): string {
    if ($stok <= 0 || $status === 'habis') {
        return '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Habis (0)</span>';
    } elseif ($stok <= 5 || $status === 'menipis') {
        return '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Menipis (' . $stok . ')</span>';
    } else {
        return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Tersedia (' . $stok . ')</span>';
    }
}

/**
 * 5. Manajemen Notifikasi Flash Message Menggunakan $_SESSION (Langkah Kerja 6 & 7)
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * 6. Keamanan CSRF Token (Langkah Kerja 2: Security)
 */
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * 7. Pemeriksaan Status Autentikasi Pengguna
 */
function checkAuth(): void {
    if (!isset($_SESSION['user_id'])) {
        setFlash('warning', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        header('Location: login.php');
        exit;
    }
}

function currentUser(): ?array {
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? '',
            'nama_lengkap' => $_SESSION['nama_lengkap'] ?? '',
            'role' => $_SESSION['role'] ?? 'admin'
        ];
    }
    return null;
}
