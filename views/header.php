<?php
/**
 * Global Header Template
 */
$currentPage = basename($_SERVER['PHP_SELF']);
$currentAction = $_GET['action'] ?? 'dashboard';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?> - BNSP Skenario 3</title>
    <!-- Pre-existing Components: Google Fonts & Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Pre-existing Component: Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="main-content">
        <!-- Top Navigation Bar -->
        <header class="navbar">
            <a href="index.php?action=dashboard" class="brand-logo">
                <div class="brand-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <div>
                    <div>Smart Inventory</div>
                    <span class="brand-badge">BNSP SKENARIO 3</span>
                </div>
            </a>

            <nav>
                <ul class="nav-links">
                    <li>
                        <a href="index.php?action=dashboard" class="nav-link <?= ($currentAction === 'dashboard') ? 'active' : '' ?>">
                            <i class="fas fa-chart-pie"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="index.php?action=products" class="nav-link <?= in_array($currentAction, ['products', 'product_create', 'product_edit']) ? 'active' : '' ?>">
                            <i class="fas fa-box"></i> Data Produk
                        </a>
                    </li>
                    <li>
                        <a href="index.php?action=asesor" class="nav-link <?= ($currentAction === 'asesor') ? 'active' : '' ?>" style="border: 1px dashed var(--primary);">
                            <i class="fas fa-graduation-cap"></i> Panduan Asesor (9 Langkah)
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="user-profile">
                <div class="user-avatar">
                    <?= strtoupper(substr($user['nama_lengkap'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="user-info">
                    <div class="name"><?= htmlspecialchars($user['nama_lengkap'] ?? 'Guest') ?></div>
                    <div class="role"><i class="fas fa-shield-alt"></i> <?= htmlspecialchars(strtoupper($user['role'] ?? 'Admin')) ?></div>
                </div>
                <a href="logout.php" class="btn btn-secondary btn-sm" title="Logout" style="margin-left: 0.5rem;">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </header>

        <!-- Flash Message Alerts -->
        <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <i class="fas <?= $flash['type'] === 'success' ? 'fa-check-circle' : ($flash['type'] === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle') ?>"></i>
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
        <?php endif; ?>
