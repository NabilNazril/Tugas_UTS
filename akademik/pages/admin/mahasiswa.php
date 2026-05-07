<?php
// pages/admin/mahasiswa.php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireRole('admin');

define('PAGE_TITLE', 'Manajemen Mahasiswa');

$msg = '';
$msgType = 'success';

// DELETE
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM mahasiswa WHERE id = ?");
    $stmt->bind_param("i", $delId);
    $stmt->execute();
    $msg = 'Data berhasil dihapus.';
}

// INSERT / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = (int)($_POST['id'] ?? 0);
    $nim          = trim($_POST['nim'] ?? '');
    $nama         = trim($_POST['nama'] ?? '');
    $prodi        = trim($_POST['program_studi'] ?? '');
    $semester     = (int)($_POST['semester'] ?? 1);
    $matkul       = trim($_POST['mata_kuliah'] ?? '');
    $sks          = (int)($_POST['sks'] ?? 3);
    $n_tugas      = (float)($_POST['nilai_tugas'] ?? 0);
    $n_uts        = (float)($_POST['nilai_uts'] ?? 0);
    $n_uas        = (float)($_POST['nilai_uas'] ?? 0);

    // Hitung nilai akhir (bobot: tugas 30%, uts 30%, uas 40%)
    $nilai_akhir = ($n_tugas * 0.30) + ($n_uts * 0.30) + ($n_uas * 0.40);

    // Tentukan huruf & bobot
    if ($nilai_akhir >= 85)      { $huruf = 'A';  $bobot = 4.00; }
    elseif ($nilai_akhir >= 80)  { $huruf = 'A-'; $bobot = 3.75; }
    elseif ($nilai_akhir >= 75)  { $huruf = 'B+'; $bobot = 3.50; }
    elseif ($nilai_akhir >= 70)  { $huruf = 'B';  $bobot = 3.00; }
    elseif ($nilai_akhir >= 65)  { $huruf = 'B-'; $bobot = 2.75; }
    elseif ($nilai_akhir >= 60)  { $huruf = 'C+'; $bobot = 2.50; }
    elseif ($nilai_akhir >= 55)  { $huruf = 'C';  $bobot = 2.00; }
    elseif ($nilai_akhir >= 50)  { $huruf = 'D';  $bobot = 1.00; }
    else                         { $huruf = 'E';  $bobot = 0.00; }

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE mahasiswa SET nim=?,nama=?,program_studi=?,semester=?,mata_kuliah=?,sks=?,nilai_tugas=?,nilai_uts=?,nilai_uas=?,nilai_akhir=?,huruf=?,bobot=? WHERE id=?");
        $stmt->bind_param("sssisiddddsdi", $nim,$nama,$prodi,$semester,$matkul,$sks,$n_tugas,$n_uts,$n_uas,$nilai_akhir,$huruf,$bobot,$id);
        $msg = 'Data berhasil diperbarui.';
    } else {
        $stmt = $conn->prepare("INSERT INTO mahasiswa (nim,nama,program_studi,semester,mata_kuliah,sks,nilai_tugas,nilai_uts,nilai_uas,nilai_akhir,huruf,bobot) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssisiddddsd", $nim,$nama,$prodi,$semester,$matkul,$sks,$n_tugas,$n_uts,$n_uas,$nilai_akhir,$huruf,$bobot);
        $msg = 'Data berhasil ditambahkan.';
    }
    $stmt->execute();
    if ($stmt->error) { $msg = 'Error: ' . $stmt->error; $msgType = 'danger'; }
}

// EDIT: get existing data
$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM mahasiswa WHERE id = $editId");
    $editData = $res->fetch_assoc();
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
    <h1>Manajemen Data Mahasiswa</h1>
    <p>Tambah, edit, dan hapus data nilai mahasiswa.</p>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert">
    <i class="bi bi-<?= $msgType === 'success' ? 'check-circle' : 'x-circle' ?>-fill me-2"></i><?= htmlspecialchars($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Form tambah/edit -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="bi bi-<?= $editData ? 'pencil-square' : 'plus-circle' ?> me-2 text-primary"></i><?= $editData ? 'Edit Data' : 'Tambah Data' ?> Mahasiswa</h5>
        <form method="POST">
            <?php if ($editData): ?><input type="hidden" name="id" value="<?= $editData['id'] ?>"><?php endif; ?>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" class="form-control" required value="<?= htmlspecialchars($editData['nim'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nama Mahasiswa</label>
                    <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($editData['nama'] ?? '') ?>">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Program Studi</label>
                    <input type="text" name="program_studi" class="form-control" required value="<?= htmlspecialchars($editData['program_studi'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Semester</label>
                    <input type="number" name="semester" class="form-control" min="1" max="14" required value="<?= $editData['semester'] ?? 1 ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mata Kuliah</label>
                    <input type="text" name="mata_kuliah" class="form-control" required value="<?= htmlspecialchars($editData['mata_kuliah'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">SKS</label>
                    <input type="number" name="sks" class="form-control" min="1" max="6" required value="<?= $editData['sks'] ?? 3 ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nilai Tugas <small class="text-muted">(bobot 30%)</small></label>
                    <input type="number" step="0.01" min="0" max="100" name="nilai_tugas" class="form-control" value="<?= $editData['nilai_tugas'] ?? 0 ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nilai UTS <small class="text-muted">(bobot 30%)</small></label>
                    <input type="number" step="0.01" min="0" max="100" name="nilai_uts" class="form-control" value="<?= $editData['nilai_uts'] ?? 0 ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nilai UAS <small class="text-muted">(bobot 40%)</small></label>
                    <input type="number" step="0.01" min="0" max="100" name="nilai_uas" class="form-control" value="<?= $editData['nilai_uas'] ?? 0 ?>">
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i><?= $editData ? 'Simpan Perubahan' : 'Tambah Data' ?>
                </button>
                <?php if ($editData): ?>
                <a href="mahasiswa.php" class="btn btn-outline-secondary">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title"><i class="bi bi-table me-2 text-primary"></i>Data Mahasiswa</span>
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIM/nama/matkul..." value="<?= htmlspecialchars($search) ?>" style="width:220px">
            <button type="submit" class="btn btn-sm btn-primary">Cari</button>
            <?php if ($search): ?><a href="mahasiswa.php" class="btn btn-sm btn-outline-secondary">Reset</a><?php endif; ?>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th><th>NIM</th><th>Nama</th><th>Prodi</th><th>Sem</th>
                    <th>Mata Kuliah</th><th>SKS</th><th>Tugas</th><th>UTS</th><th>UAS</th>
                    <th>Akhir</th><th>Grade</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; while ($row = $rows->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
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
                    <td>
                        <span class="grade-badge <?= gradeClass($row['huruf']) ?>">
                            <?= $row['huruf'] ?>
                        </span>
                    </td>
                    <td>
                        <a href="mahasiswa.php?edit=<?= $row['id'] ?>" class="btn btn-xs btn-outline-primary btn-sm py-0 px-2" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="mahasiswa.php?delete=<?= $row['id'] ?>"
                            class="btn btn-xs btn-outline-danger btn-sm py-0 px-2 ms-1"
                            onclick="return confirm('Hapus data ini?')" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($rows->num_rows === 0): ?>
                <tr><td colspan="13" class="text-center text-muted py-4">Belum ada data</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
