<?php
// pages/mahasiswa/nilai.php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireRole('mahasiswa');
define('PAGE_TITLE', 'Nilai Saya');

$nim = $_SESSION['nim'];
$rows = $conn->query("SELECT * FROM mahasiswa WHERE nim = '$nim' ORDER BY semester, mata_kuliah");

// IPK
$ipkRes = $conn->query("
    SELECT SUM(bobot * sks) / SUM(sks) as ipk, SUM(sks) as total_sks
    FROM mahasiswa WHERE nim = '$nim' AND nilai_akhir > 0
")->fetch_assoc();

require_once '../../includes/header.php';

function gradeClass($h) {
    $h = substr(trim($h), 0, 1);
    return 'grade-' . ($h ?: 'E');
}
?>

<div class="page-heading">
    <h1>Nilai Saya</h1>
    <p>NIM: <strong style="font-family:'DM Mono',monospace"><?= $nim ?></strong></p>
</div>

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="ipk-card">
            <div style="font-size:12px;opacity:.8;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">IPK</div>
            <div class="ipk-number"><?= $ipkRes['ipk'] ? number_format($ipkRes['ipk'],2) : '0.00' ?></div>
            <div class="ipk-label">Total <?= $ipkRes['total_sks'] ?? 0 ?> SKS</div>
        </div>
    </div>
</div>

<!-- Nilai table -->
<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title"><i class="bi bi-table me-2 text-primary"></i>Detail Nilai Per Mata Kuliah</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th><th>Mata Kuliah</th><th>SKS</th>
                    <th>Tugas</th><th>UTS</th><th>UAS</th>
                    <th>Nilai Akhir</th><th>Progress</th><th>Grade</th><th>Bobot</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; while ($row = $rows->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($row['mata_kuliah']) ?></strong></td>
                    <td><?= $row['sks'] ?></td>
                    <td><?= $row['nilai_tugas'] ?></td>
                    <td><?= $row['nilai_uts'] ?></td>
                    <td><?= $row['nilai_uas'] ?></td>
                    <td><strong><?= $row['nilai_akhir'] ?></strong></td>
                    <td style="min-width:120px">
                        <div class="nilai-bar">
                            <div class="nilai-bar-fill" data-width="<?= $row['nilai_akhir'] ?>" style="width:0%"></div>
                        </div>
                        <small class="text-muted"><?= $row['nilai_akhir'] ?>%</small>
                    </td>
                    <td><span class="grade-badge <?= gradeClass($row['huruf']) ?>"><?= $row['huruf'] ?></span></td>
                    <td><?= $row['bobot'] ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if ($rows->num_rows === 0): ?>
                <tr><td colspan="10" class="text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px"></i>
                    Nilai belum tersedia
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
