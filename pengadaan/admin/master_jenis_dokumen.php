<?php
// /**
//  * admin/master_jenis_dokumen.php
//  * CRUD Master Jenis Dokumen + DataTables (Admin Only)
//  */

// session_start();
// require_once __DIR__ . '/../includes/auth_check.php';
// require_once __DIR__ . '/../config/db.php';

// pastikan hanya admin
// if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
//     http_response_code(403);
//     die("Akses ditolak. Hanya admin yang dapat mengakses halaman ini.");
// }

include 'header&menu_admin.php';

$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// /* =======================================================
//    HANDLE: ADD / EDIT / DELETE
// ======================================================= */
try {
    if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $kode = trim($_POST['kode_dokumen']);
        $nama = trim($_POST['nama_dokumen']);
        $aktif = ($_POST['aktif'] ?? 'Y') === 'Y' ? 'Y' : 'N';

        if ($kode === '' || $nama === '') {
            $error = "Kode dan nama dokumen wajib diisi.";
        } else {
            $check = $pdo->prepare("SELECT COUNT(*) FROM master_jenis_dokumen WHERE kode_dokumen = :kode");
            $check->execute([':kode' => $kode]);
            if ($check->fetchColumn() > 0) {
                $error = "Kode dokumen sudah digunakan.";
            } else {
                $insert = $pdo->prepare("INSERT INTO master_jenis_dokumen (kode_dokumen, nama_dokumen, aktif) VALUES (:kode, :nama, :aktif)");
                $insert->execute([':kode' => $kode, ':nama' => $nama, ':aktif' => $aktif]);
                $message = "Jenis dokumen berhasil ditambahkan.";
                $action = 'list';
            }
        }
    } elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)$_POST['id'];
        $kode = trim($_POST['kode_dokumen']);
        $nama = trim($_POST['nama_dokumen']);
        $aktif = ($_POST['aktif'] ?? 'Y') === 'Y' ? 'Y' : 'N';

        if ($kode === '' || $nama === '') {
            $error = "Kode dan nama dokumen wajib diisi.";
        } else {
            $update = $pdo->prepare("UPDATE master_jenis_dokumen SET kode_dokumen=:kode, nama_dokumen=:nama, aktif=:aktif WHERE id=:id");
            $update->execute([
                ':kode' => $kode,
                ':nama' => $nama,
                ':aktif' => $aktif,
                ':id' => $id
            ]);
            $message = "Jenis dokumen berhasil diperbarui.";
            $action = 'list';
        }
    } elseif ($action === 'delete' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $pdo->prepare("DELETE FROM master_jenis_dokumen WHERE id=:id")->execute([':id' => $id]);
        $message = "Jenis dokumen berhasil dihapus.";
        $action = 'list';
    }
} catch (Exception $e) {
    $error = "Terjadi kesalahan: " . $e->getMessage();
}

// /* =======================================================
//    QUERY DATA (LIST / EDIT)
// ======================================================= */
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM master_jenis_dokumen WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $data_edit = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$data_edit) {
        $error = "Data tidak ditemukan.";
        $action = 'list';
    }
}

if ($action === 'list') {
    $stmt = $pdo->query("SELECT * FROM master_jenis_dokumen ORDER BY nama_dokumen ASC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


<!-- CONTENT -->
<!-- =================================================== -->
<!--                HTML + BOOTSTRAP UI                 -->
<!-- =================================================== -->
<div class="container mt-4 mb-5">
    <h3 class="text-center mb-4">Master Jenis Dokumen</h3>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>


    <?php if ($action === 'list'): ?>
        <div class="d-flex justify-content-end mb-3">
            <a href="?action=add" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Jenis Dokumen
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <table id="dokumenTable" class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Kode</th>
                            <th>Nama Dokumen</th>
                            <th width="10%">Aktif</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($rows as $r): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($r['kode_dokumen']) ?></td>
                                    <td><?= htmlspecialchars($r['nama_dokumen']) ?></td>
                                    <td class="text-center">
                                        <?= $r['aktif'] === 'Y'
                                            ? '<span class="badge bg-success">Aktif</span>'
                                            : '<span class="badge bg-secondary">Nonaktif</span>' ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="?action=edit&id=<?= $r['id'] ?>" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="?action=delete&id=<?= $r['id'] ?>"
                                            onclick="return confirm('Hapus data ini?')"
                                            class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <?php
        $isEdit = ($action === 'edit');
        $data = $data_edit ?? ['kode_dokumen' => '', 'nama_dokumen' => '', 'aktif' => 'Y'];
        ?>
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><?= $isEdit ? 'Edit Jenis Dokumen' : 'Tambah Jenis Dokumen' ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label>Kode Dokumen</label>
                        <input type="text" name="kode_dokumen" class="form-control" maxlength="50"
                            value="<?= htmlspecialchars($data['kode_dokumen']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Nama Dokumen</label>
                        <input type="text" name="nama_dokumen" class="form-control" maxlength="255"
                            value="<?= htmlspecialchars($data['nama_dokumen']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Status Aktif</label>
                        <select name="aktif" class="form-select">
                            <option value="Y" <?= $data['aktif'] === 'Y' ? 'selected' : '' ?>>Aktif</option>
                            <option value="N" <?= $data['aktif'] === 'N' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="?action=list" class="btn btn-secondary">
                            <i class="bi bi-arrow-left-circle"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>


<?php include 'footer_admin.php'; ?>