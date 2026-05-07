<?php
// pages/admin/dashboard.php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireRole('admin');

define('PAGE_TITLE', 'Dashboard Admin');

// Stats
$totalMhs   = $conn->query("SELECT COUNT(DISTINCT nim) as c FROM mahasiswa")->fetch_assoc()['c'];
$totalMatkul= $conn->query("SELECT COUNT(DISTINCT mata_kuliah) as c FROM mahasiswa")->fetch_assoc()['c'];
$totalUsers = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$avgNilai   = $conn->query("SELECT ROUND(AVG(nilai_akhir),2) as c FROM mahasiswa WHERE nilai_akhir > 0")->fetch_assoc()['c'];

// Recent mahasiswa
$recentMhs = $conn->query("
    SELECT nim, nama, program_studi, semester, COUNT(*) as jml_matkul
    FROM mahasiswa
    GROUP BY nim, nama, program_studi, semester
    ORDER BY MAX(created_at) DESC
    LIMIT 5
");

require_once '../../includes/header.php';
?>

<div class="page-heading">
    <h1>Dashboard Admin</h1>
    <p>Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?>. Berikut ringkasan data akademik.</p>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-value"><?= $totalMhs ?></div>
                <div class="stat-label">Total Mahasiswa</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-journal-bookmark-fill"></i></div>
            <div>
                <div class="stat-value"><?= $totalMatkul ?></div>
                <div class="stat-label">Mata Kuliah</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon yellow"><i class="bi bi-person-gear"></i></div>
            <div>
                <div class="stat-value"><?= $totalUsers ?></div>
                <div class="stat-label">Total Pengguna</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon cyan"><i class="bi bi-bar-chart-fill"></i></div>
            <div>
                <div class="stat-value"><?= $avgNilai ?? '—' ?></div>
                <div class="stat-label">Rata-rata Nilai</div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Mahasiswa -->
<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title"><i class="bi bi-clock-history me-2 text-primary"></i>Mahasiswa Terbaru</span>
        <a href="mahasiswa.php" class="btn btn-sm btn-primary">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Program Studi</th>
                    <th>Semester</th>
                    <th>Jml Matkul</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $recentMhs->fetch_assoc()): ?>
                <tr>
                    <td><code style="font-family:'DM Mono',monospace;font-size:13px"><?= $row['nim'] ?></code></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['program_studi']) ?></td>
                    <td><span class="badge bg-primary">Sem <?= $row['semester'] ?></span></td>
                    <td><?= $row['jml_matkul'] ?> matkul</td>
                </tr>
                <?php endwhile; ?>
                <?php if ($recentMhs->num_rows === 0): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data mahasiswa</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
