<?php
// pages/admin/users.php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireRole('admin');
define('PAGE_TITLE', 'Manajemen Pengguna');

$msg = ''; $msgType = 'success';

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if ($delId !== (int)$_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id = $delId");
        $msg = 'Pengguna berhasil dihapus.';
    } else {
        $msg = 'Tidak dapat menghapus akun sendiri.'; $msgType = 'warning';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int)($_POST['id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $nama     = trim($_POST['nama'] ?? '');
    $hakakses = $_POST['hakakses'] ?? 'dosen';
    $nim      = ($hakakses === 'mahasiswa') ? trim($_POST['nim'] ?? '') : null;
    $password = trim($_POST['password'] ?? '');

    if ($id > 0) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username=?,nama=?,hakakses=?,nim=?,password=? WHERE id=?");
            $stmt->bind_param("sssssi", $username,$nama,$hakakses,$nim,$hash,$id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?,nama=?,hakakses=?,nim=? WHERE id=?");
            $stmt->bind_param("ssssi", $username,$nama,$hakakses,$nim,$id);
        }
        $msg = 'Data pengguna berhasil diperbarui.';
    } else {
        if (!$password) { $msg = 'Password wajib diisi untuk pengguna baru.'; $msgType = 'danger'; goto endform; }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username,nama,password,hakakses,nim) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $username,$nama,$hash,$hakakses,$nim);
        $msg = 'Pengguna berhasil ditambahkan.';
    }
    $stmt->execute();
    if ($stmt->error) { $msg = 'Error: '.$stmt->error; $msgType = 'danger'; }
    endform:
}

$editData = null;
if (isset($_GET['edit'])) {
    $editData = $conn->query("SELECT * FROM users WHERE id=".(int)$_GET['edit'])->fetch_assoc();
}

$users = $conn->query("SELECT * FROM users ORDER BY hakakses, nama");
require_once '../../includes/header.php';
?>

<div class="page-heading">
    <h1>Manajemen Pengguna</h1>
    <p>Kelola akun admin, dosen, dan mahasiswa.</p>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?> alert-dismissible fade show">
    <i class="bi bi-info-circle me-2"></i><?= htmlspecialchars($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="bi bi-person-plus me-2 text-primary"></i><?= $editData ? 'Edit' : 'Tambah' ?> Pengguna</h5>
        <form method="POST" id="userForm">
            <?php if ($editData): ?><input type="hidden" name="id" value="<?= $editData['id'] ?>"><?php endif; ?>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Role</label>
                    <select name="hakakses" class="form-select" id="hakaksesSelect">
                        <?php foreach (['admin','dosen','mahasiswa'] as $r): ?>
                        <option value="<?= $r ?>" <?= ($editData['hakakses'] ?? 'dosen') === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Username / NIM</label>
                    <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($editData['username'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($editData['nama'] ?? '') ?>">
                </div>
                <div class="col-md-2 nim-field" style="display:<?= ($editData['hakakses'] ?? '') === 'mahasiswa' ? 'block' : 'none' ?>">
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" class="form-control" value="<?= htmlspecialchars($editData['nim'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Password <?= $editData ? '<small class="text-muted">(kosongkan jika tidak diubah)</small>' : '' ?></label>
                    <input type="password" name="password" class="form-control" <?= $editData ? '' : 'required' ?> placeholder="<?= $editData ? 'Kosongkan jika tidak berubah' : 'Password baru' ?>">
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i><?= $editData ? 'Simpan' : 'Tambah' ?>
                </button>
                <?php if ($editData): ?>
                <a href="users.php" class="btn btn-outline-secondary">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title"><i class="bi bi-people me-2 text-primary"></i>Daftar Pengguna</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr><th>#</th><th>Username</th><th>Nama</th><th>NIM</th><th>Role</th><th>Dibuat</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php $no=1; while ($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><code style="font-family:'DM Mono',monospace;font-size:13px"><?= htmlspecialchars($row['username']) ?></code></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= $row['nim'] ? '<code style="font-family:\'DM Mono\',monospace;font-size:12px">'.$row['nim'].'</code>' : '—' ?></td>
                    <td><span class="badge-role badge-<?= $row['hakakses'] ?>"><?= ucfirst($row['hakakses']) ?></span></td>
                    <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                    <td>
                        <a href="users.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary py-0 px-2"><i class="bi bi-pencil"></i></a>
                        <?php if ($row['id'] !== (int)$_SESSION['user_id']): ?>
                        <a href="users.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger py-0 px-2 ms-1"
                           onclick="return confirm('Hapus pengguna ini?')"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('hakaksesSelect').addEventListener('change', function() {
    const nimField = document.querySelector('.nim-field');
    nimField.style.display = this.value === 'mahasiswa' ? 'block' : 'none';
});
</script>

<?php require_once '../../includes/footer.php'; ?>
