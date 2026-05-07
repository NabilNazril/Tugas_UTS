<?php
// pages/dosen/dashboard.php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireRole('dosen');
define('PAGE_TITLE', 'Dashboard Dosen');

$totalMhs   = $conn->query("SELECT COUNT(DISTINCT nim) as c FROM mahasiswa")->fetch_assoc()['c'];
$totalMatkul= $conn->query("SELECT COUNT(DISTINCT mata_kuliah) as c FROM mahasiswa")->fetch_assoc()['c'];
$avgNilai   = $conn->query("SELECT ROUND(AVG(nilai_akhir),2) as c FROM mahasiswa WHERE nilai_akhir > 0")->fetch_assoc()['c'];
$blmDinilai = $conn->query("SELECT COUNT(*) as c FROM mahasiswa WHERE nilai_akhir = 0")->fetch_assoc()['c'];

require_once '../../includes/header.php';
?>

<div class="page-heading">
    <h1>Dashboard Dosen</h1>
    <p>Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?>.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
            <div><div class="stat-value"><?= $totalMhs ?></div><div class="stat-label">Total Mahasiswa</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-journal-bookmark-fill"></i></div>
            <div><div class="stat-value"><?= $totalMatkul ?></div><div class="stat-label">Mata Kuliah</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon cyan"><i class="bi bi-graph-up"></i></div>
            <div><div class="stat-value"><?= $avgNilai ?? '—' ?></div><div class="stat-label">Rata-rata Nilai</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-exclamation-circle-fill"></i></div>
            <div><div class="stat-value"><?= $blmDinilai ?></div><div class="stat-label">Belum Dinilai</div></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-journal-check" style="font-size:48px;color:var(--primary)"></i>
        <h5 class="mt-3 fw-bold">Input & Kelola Nilai</h5>
        <p class="text-muted">Silakan menuju halaman Input Nilai untuk menambah atau mengubah nilai mahasiswa.</p>
        <a href="nilai.php" class="btn btn-primary px-5 mt-2">
            <i class="bi bi-pencil-square me-2"></i>Buka Input Nilai
        </a>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
