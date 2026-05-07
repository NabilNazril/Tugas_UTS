<?php
// pages/dosen/nilai.php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireRole('dosen');
define('PAGE_TITLE', 'Input Nilai');

$msg = ''; $msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = (int)($_POST['id'] ?? 0);
    $n_tugas = (float)($_POST['nilai_tugas'] ?? 0);
    $n_uts   = (float)($_POST['nilai_uts'] ?? 0);
    $n_uas   = (float)($_POST['nilai_uas'] ?? 0);

    $nilai_akhir = ($n_tugas * 0.30) + ($n_uts * 0.30) + ($n_uas * 0.40);

    if ($nilai_akhir >= 85)      { $huruf = 'A';  $bobot = 4.00; }
    elseif ($nilai_akhir >= 80)  { $huruf = 'A-'; $bobot = 3.75; }
    elseif ($nilai_akhir >= 75)  { $huruf = 'B+'; $bobot = 3.50; }
    elseif ($nilai_akhir >= 70)  { $huruf = 'B';  $bobot = 3.00; }
    elseif ($nilai_akhir >= 65)  { $huruf = 'B-'; $bobot = 2.75; }
    elseif ($nilai_akhir >= 60)  { $huruf = 'C+'; $bobot = 2.50; }
    elseif ($nilai_akhir >= 55)  { $huruf = 'C';  $bobot = 2.00; }
    elseif ($nilai_akhir >= 50)  { $huruf = 'D';  $bobot = 1.00; }
    else                         { $huruf = 'E';  $bobot = 0.00; }

    $stmt = $conn->prepare("UPDATE mahasiswa SET nilai_tugas=?,nilai_uts=?,nilai_uas=?,nilai_akhir=?,huruf=?,bobot=? WHERE id=?");
    $stmt->bind_param("ddddsdi", $n_tugas,$n_uts,$n_uas,$nilai_akhir,$huruf,$bobot,$id);
    $stmt->execute();
    $msg = 'Nilai berhasil disimpan.';
}

// Search
$search = trim($_GET['search'] ?? '');
$searchSql = '';
if ($search) {
    $s = $conn->real_escape_string($search);
    $searchSql = "WHERE nim LIKE '%$s%' OR nama LIKE '%$s%' OR mata_kuliah LIKE '%$s%'";
}
$rows = $conn->query("SELECT * FROM mahasiswa $searchSql ORDER BY nim, mata_kuliah");

require_once '../../includes/header.php';

function gradeClass($h) {
    $h = substr(trim($h), 0, 1);
    return 'grade-' . ($h ?: 'E');
}
?>

<div class="page-heading">
    <h1>Input Nilai Mahasiswa</h1>
    <p>Klik tombol edit pada baris untuk mengubah nilai mahasiswa.</p>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?> alert-dismissible fade show">
    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title"><i class="bi bi-journal-check me-2 text-primary"></i>Data Nilai</span>
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIM/nama/matkul..." value="<?= htmlspecialchars($search) ?>" style="width:220px">
            <button class="btn btn-sm btn-primary">Cari</button>
            <?php if ($search): ?><a href="nilai.php" class="btn btn-sm btn-outline-secondary">Reset</a><?php endif; ?>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>NIM</th><th>Nama</th><th>Mata Kuliah</th><th>SKS</th>
                    <th>Tugas</th><th>UTS</th><th>UAS</th>
                    <th>Akhir</th><th>Grade</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $rows->fetch_assoc()): ?>
                <tr id="row-<?= $row['id'] ?>">
                    <td><code style="font-family:'DM Mono',monospace;font-size:12px"><?= $row['nim'] ?></code></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['mata_kuliah']) ?></td>
                    <td><?= $row['sks'] ?></td>
                    <!-- view mode -->
                    <td class="val-tugas"><?= $row['nilai_tugas'] ?></td>
                    <td class="val-uts"><?= $row['nilai_uts'] ?></td>
                    <td class="val-uas"><?= $row['nilai_uas'] ?></td>
                    <td><strong><?= $row['nilai_akhir'] ?></strong></td>
                    <td><span class="grade-badge <?= gradeClass($row['huruf']) ?>"><?= $row['huruf'] ?></span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2"
                            onclick="openEdit(<?= $row['id'] ?>, <?= $row['nilai_tugas'] ?>, <?= $row['nilai_uts'] ?>, <?= $row['nilai_uas'] ?>)">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit Nilai -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.2)">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label class="form-label">Nilai Tugas <span class="text-muted">(bobot 30%)</span></label>
                        <input type="number" step="0.01" min="0" max="100" name="nilai_tugas" id="editTugas" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nilai UTS <span class="text-muted">(bobot 30%)</span></label>
                        <input type="number" step="0.01" min="0" max="100" name="nilai_uts" id="editUts" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nilai UAS <span class="text-muted">(bobot 40%)</span></label>
                        <input type="number" step="0.01" min="0" max="100" name="nilai_uas" id="editUas" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEdit(id, tugas, uts, uas) {
    document.getElementById('editId').value = id;
    document.getElementById('editTugas').value = tugas;
    document.getElementById('editUts').value = uts;
    document.getElementById('editUas').value = uas;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php require_once '../../includes/footer.php'; ?>
