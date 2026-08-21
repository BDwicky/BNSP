<?php
/**
 * Global Header Template
 */
$currentPage = basename($_SERVER['PHP_SELF']);
$currentAction = $_GET['action'] ?? 'dashboard';
$user = currentUser();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?> - BNSP Skenario 3</title>
    <!-- Pre-existing Components: Google Fonts & Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
    <!-- Pre-existing Component: Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Pre-existing Component: SweetAlert2 (Toast & Confirmation) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Hidden Flash Data element for Toast notifications -->
    <?php if ($flash): ?>
        <div id="flash-data" 
             data-type="<?= htmlspecialchars($flash['type']) ?>" 
             data-message="<?= htmlspecialchars($flash['message']) ?>">
        </div>
    <?php endif; ?>

    <div class="app-wrapper">
        <!-- Top Navigation Bar -->
        <header class="navbar">
            <div class="navbar-brand-section">
                <a href="index.php?action=dashboard" class="brand-logo">
                    <div class="brand-icon">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <div class="brand-title">Smart Inventory</div>
                        <span class="brand-badge">BNSP SKENARIO 3</span>
                    </div>
                </a>

                <!-- Mobile Hamburger Toggle -->
                <button type="button" class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle Menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="navbar-nav" id="navbarMenu">
                <ul class="nav-links">
                    <li>
                        <a href="index.php?action=dashboard" class="nav-link <?= ($currentAction === 'dashboard') ? 'active' : '' ?>">
                            <i class="fas fa-chart-pie"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?action=products" class="nav-link <?= in_array($currentAction, ['products', 'product_create', 'product_edit']) ? 'active' : '' ?>">
                            <i class="fas fa-box"></i> <span>Data Produk</span>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?action=asesor" class="nav-link nav-link-asesor <?= ($currentAction === 'asesor') ? 'active' : '' ?>">
                            <i class="fas fa-graduation-cap"></i> <span>Panduan Asesor</span>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?mode=simple" class="nav-link" style="border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399;" title="Kembali ke Mode Standar Sederhana">
                            <i class="fas fa-arrow-left"></i> <span>Mode Standar</span>
                        </a>
                    </li>
                </ul>

                <div class="user-profile">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['nama_lengkap'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <div class="name"><?= htmlspecialchars($user['nama_lengkap'] ?? 'Guest') ?></div>
                        <div class="role"><i class="fas fa-shield-alt"></i> <?= htmlspecialchars(strtoupper($user['role'] ?? 'Admin')) ?></div>
                    </div>
                    <a href="logout.php" class="btn btn-secondary btn-sm btn-logout" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </nav>
        </header>

        <main class="main-content">
