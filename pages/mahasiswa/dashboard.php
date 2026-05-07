<?php
// pages/mahasiswa/dashboard.php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireRole('mahasiswa');
define('PAGE_TITLE', 'Dashboard Mahasiswa');

$nim = $_SESSION['nim'];

// Get profile from first record
$profile = $conn->query("SELECT * FROM mahasiswa WHERE nim = '$nim' LIMIT 1")->fetch_assoc();

// IPK calculation
$ipkRes = $conn->query("
    SELECT 
        SUM(bobot * sks) / SUM(sks) as ipk,
        SUM(sks) as total_sks,
        COUNT(*) as total_matkul
    FROM mahasiswa 
    WHERE nim = '$nim' AND nilai_akhir > 0
")->fetch_assoc();

$ipk = $ipkRes['ipk'] ? number_format($ipkRes['ipk'], 2) : '0.00';

require_once '../../includes/header.php';
?>

<div class="page-heading">
    <h1>Halo, <?= htmlspecialchars($profile['nama'] ?? $_SESSION['nama']) ?> 👋</h1>
    <p>Berikut ringkasan akademik Anda.</p>
</div>

<div class="row g-3 mb-4">
    <!-- IPK Card -->
    <div class="col-md-4">
        <div class="ipk-card">
            <div style="font-size:13px;opacity:.8;margin-bottom:8px;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Indeks Prestasi Kumulatif</div>
            <div class="ipk-number"><?= $ipk ?></div>
            <div class="ipk-label">dari skala 4.00</div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="row g-3 h-100">
            <div class="col-6">
                <div class="stat-card h-100">
                    <div class="stat-icon blue"><i class="bi bi-journal-bookmark-fill"></i></div>
                    <div>
                        <div class="stat-value"><?= $ipkRes['total_matkul'] ?? 0 ?></div>
                        <div class="stat-label">Mata Kuliah</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card h-100">
                    <div class="stat-icon green"><i class="bi bi-stack"></i></div>
                    <div>
                        <div class="stat-value"><?= $ipkRes['total_sks'] ?? 0 ?></div>
                        <div class="stat-label">Total SKS</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card h-100">
                    <div class="stat-icon yellow"><i class="bi bi-person-badge-fill"></i></div>
                    <div>
                        <div class="stat-value" style="font-size:16px"><?= htmlspecialchars($profile['program_studi'] ?? '—') ?></div>
                        <div class="stat-label">Program Studi</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card h-100">
                    <div class="stat-icon cyan"><i class="bi bi-calendar3"></i></div>
                    <div>
                        <div class="stat-value">Sem <?= $profile['semester'] ?? '—' ?></div>
                        <div class="stat-label">Semester Aktif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick link -->
<div class="card">
    <div class="card-body text-center py-4">
        <i class="bi bi-bar-chart-fill" style="font-size:40px;color:var(--primary)"></i>
        <h5 class="mt-2 fw-bold">Lihat Detail Nilai</h5>
        <p class="text-muted mb-3">Lihat detail nilai per mata kuliah beserta grafik progress.</p>
        <a href="nilai.php" class="btn btn-primary px-5">
            <i class="bi bi-eye me-2"></i>Lihat Nilai Saya
        </a>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
