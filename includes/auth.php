<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/index.php');
        exit;
    }
}

function requireRole($roles) {
    requireLogin();
    if (!is_array($roles)) $roles = [$roles];
    if (!in_array($_SESSION['hakakses'], $roles)) {
        header('Location: ' . APP_URL . '/dashboard.php');
        exit;
    }
}

function getUser() {
    return $_SESSION ?? [];
}

function redirectDashboard() {
    switch ($_SESSION['hakakses']) {
        case 'admin':
            header('Location: ' . APP_URL . '/pages/admin/dashboard.php');
            break;
        case 'dosen':
            header('Location: ' . APP_URL . '/pages/dosen/dashboard.php');
            break;
        case 'mahasiswa':
            header('Location: ' . APP_URL . '/pages/mahasiswa/dashboard.php');
            break;
        default:
            header('Location: ' . APP_URL . '/index.php');
    }
    exit;
}

function logout() {
    session_destroy();
    header('Location: ' . APP_URL . '/index.php');
    exit;
}
