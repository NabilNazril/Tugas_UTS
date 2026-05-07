<?php
// includes/config.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'akademik_sistem');

define('APP_NAME', 'Sistem Akademik');
define('APP_URL', 'http://localhost/akademik');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:20px;background:#fee;border:1px solid #f00;border-radius:8px;">
        <strong>Koneksi Database Gagal!</strong><br>' . $conn->connect_error . '
    </div>');
}

$conn->set_charset('utf8mb4');
