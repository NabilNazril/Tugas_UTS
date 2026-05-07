<?php
// reset_password.php
// Letakkan file ini di C:\xampp\htdocs\akademik\reset_password.php
// Buka: http://localhost/akademik/reset_password.php

$password = 'password';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h3>Hash baru untuk password '<b>$password</b>':</h3>";
echo "<code style='font-size:14px;background:#eee;padding:10px;display:block;word-break:break-all'>$hash</code>";
echo "<br>";
echo "<p>Gunakan hash di atas untuk update database.</p>";

// Koneksi ke database dan langsung update
$conn = new mysqli('localhost', 'root', '', 'akademik_sistem');
if (!$conn->connect_error) {
    // Update semua password sekaligus
    $conn->query("UPDATE users SET password = '$hash'");
    echo "<p style='color:green;font-weight:bold'>✅ Password semua akun berhasil direset ke: <b>password</b></p>";
    echo "<p>Silakan <a href='http://localhost/akademik'>klik di sini untuk login</a></p>";
} else {
    echo "<p style='color:red'>❌ Gagal konek DB: " . $conn->connect_error . "</p>";
}
?>
