<?php
// includes/header.php
if (!defined('PAGE_TITLE')) define('PAGE_TITLE', APP_NAME);
$user = getUser();
$role = $user['hakakses'] ?? '';

$navLinks = [];
if ($role === 'admin') {
    $navLinks = [
        ['url' => APP_URL . '/pages/admin/dashboard.php',  'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
        ['url' => APP_URL . '/pages/admin/mahasiswa.php',  'icon' => 'bi-people-fill',  'label' => 'Data Mahasiswa'],
        ['url' => APP_URL . '/pages/admin/nilai.php',      'icon' => 'bi-journal-check', 'label' => 'Nilai'],
        ['url' => APP_URL . '/pages/admin/users.php',      'icon' => 'bi-person-gear',  'label' => 'Pengguna'],
    ];
} elseif ($role === 'dosen') {
    $navLinks = [
        ['url' => APP_URL . '/pages/dosen/dashboard.php', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
        ['url' => APP_URL . '/pages/dosen/nilai.php',     'icon' => 'bi-journal-check', 'label' => 'Input Nilai'],
    ];
} elseif ($role === 'mahasiswa') {
    $navLinks = [
        ['url' => APP_URL . '/pages/mahasiswa/dashboard.php', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
        ['url' => APP_URL . '/pages/mahasiswa/nilai.php',     'icon' => 'bi-bar-chart-fill', 'label' => 'Nilai Saya'],
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= PAGE_TITLE . ' — ' . APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= APP_URL ?>/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
        <div class="brand-text">
            <span class="brand-name">AkademikMHS</span>
            <span class="brand-role"><?= ucfirst($role) ?></span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($navLinks as $link): ?>
        <a href="<?= $link['url'] ?>" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], basename($link['url'])) !== false) ? 'active' : '' ?>">
            <i class="bi <?= $link['icon'] ?>"></i>
            <span><?= $link['label'] ?></span>
        </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($user['nama'] ?? 'U', 0, 1)) ?></div>
            <div class="user-detail">
                <span class="user-name"><?= htmlspecialchars($user['nama'] ?? '') ?></span>
                <span class="user-role"><?= ucfirst($role) ?></span>
            </div>
        </div>
        <a href="<?= APP_URL ?>/logout.php" class="logout-btn" title="Logout">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="main-wrapper">
    <!-- Top bar -->
    <header class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-title"><?= PAGE_TITLE ?></div>
        <div class="topbar-right">
            <span class="badge-role badge-<?= $role ?>"><?= ucfirst($role) ?></span>
        </div>
    </header>

    <main class="main-content">
