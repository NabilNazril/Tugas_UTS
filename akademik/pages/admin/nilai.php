<?php
// pages/admin/nilai.php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireRole('admin');
define('PAGE_TITLE', 'Rekap Nilai');

$rows = $conn->query("SELECT * FROM mahasiswa ORDER BY nim, mata_kuliah");
require_once '../../includes/header.php';

function gradeClass($h) {
    $h = substr(trim($h), 0, 1);
    return 'grade-' . ($h ?: 'E');
}
?>

<div class="page-heading">
    <h1>Rekap Nilai Mahasiswa</h1>
    <p>Tampilan seluruh nilai mahasiswa dalam satu halaman.</p>
</div>

<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title"><i class="bi bi-journal-check me-2 text-primary"></i>Semua Nilai</span>
        <span class="text-muted" style="font-size:13px"><?= $rows->num_rows ?> entri</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>NIM</th><th>Nama</th><th>Prodi</th><th>Sem</th>
                    <th>Mata Kuliah</th><th>SKS</th>
                    <th>Tugas</th><th>UTS</th><th>UAS</th>
                    <th>Akhir</th><th>Grade</th><th>Bobot</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $rows->fetch_assoc()): ?>
                <tr>
                    <td><code style="font-family:'DM Mono',monospace;font-size:12px"><?= $row['nim'] ?></code></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['program_studi']) ?></td>
                    <td><?= $row['semester'] ?></td>
                    <td><?= htmlspecialchars($row['mata_kuliah']) ?></td>
                    <td><?= $row['sks'] ?></td>
                    <td><?= $row['nilai_tugas'] ?></td>
                    <td><?= $row['nilai_uts'] ?></td>
                    <td><?= $row['nilai_uas'] ?></td>
                    <td><strong><?= $row['nilai_akhir'] ?></strong></td>
                    <td><span class="grade-badge <?= gradeClass($row['huruf']) ?>"><?= $row['huruf'] ?></span></td>
                    <td><?= $row['bobot'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
