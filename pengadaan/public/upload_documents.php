<?php

//  * vendor/upload_documents.php
//  * --------------------------------------------
//  * Halaman upload dokumen perusahaan oleh vendor
//  * --------------------------------------------


require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// Pastikan role = Vendor
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil company_id vendor
$stmt = $pdo->prepare("SELECT id FROM companies WHERE user_id = :uid LIMIT 1");
$stmt->execute([':uid' => $user_id]);
$company_id = $stmt->fetchColumn();

if (!$company_id) {
    die("Company ID tidak ditemukan. Hubungi admin.");
}

$success_message = '';
$error_message = '';

/* -----------------------------
   Helper Pesan Error Upload
----------------------------- */
function uploadErrorMessage($code)
{
    $errors = [
        UPLOAD_ERR_OK => 'Tidak ada error, upload berhasil.',
        UPLOAD_ERR_INI_SIZE => 'Ukuran file melebihi batas upload_max_filesize di php.ini.',
        UPLOAD_ERR_FORM_SIZE => 'Ukuran file melebihi batas MAX_FILE_SIZE dari form.',
        UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian.',
        UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload.',
        UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary hilang.',
        UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk.',
        UPLOAD_ERR_EXTENSION => 'Ekstensi PHP menghentikan upload.',
    ];
    return $errors[$code] ?? 'Error upload tidak diketahui.';
}

/* -----------------------------
   Proses Upload File
----------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $doc_type = trim($_POST['doc_type'] ?? '');
    $valid_from = $_POST['valid_from'] ?: null;
    $valid_to   = $_POST['valid_to'] ?: null;

    if (empty($doc_type)) {
        $error_message = "Jenis dokumen wajib dipilih.";
    } elseif (!isset($_FILES['doc_file'])) {
        $error_message = "File belum dipilih.";
    } else {
        $file = $_FILES['doc_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error_message = "Upload error: " . uploadErrorMessage($file['error']);
        } else {
            $allowed_ext = ['pdf'];
            $max_size = 5 * 1024 * 1024; // 5 MB
            $orig_name = $file['name'];
            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            $mime = $file['type'];
            $size = (int)$file['size'];

            if (!in_array($ext, $allowed_ext)) {
                $error_message = "File tidak diperbolehkan. Hanya PDF.";
            } elseif ($size > $max_size) {
                $error_message = "Ukuran file melebihi 5 MB.";
            } else {
                // Tentukan lokasi penyimpanan aman
                $storage_root = "/Applications/XAMPP/storage_secure/eproc_uploads";
                if (!is_dir($storage_root)) {
                    mkdir($storage_root, 0775, true);
                }

                $company_dir = $storage_root . "/company_docs/$company_id/";
                if (!is_dir($company_dir)) {
                    mkdir($company_dir, 0775, true);
                }

                $safe_name = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($orig_name, PATHINFO_FILENAME));
                $new_filename = time() . "_" . $safe_name . "." . $ext;
                $server_path = $company_dir . $new_filename;

                // Path relatif untuk disimpan di DB
                $db_path = "eproc_uploads/company_docs/$company_id/" . $new_filename;

                if (!is_writable(dirname($server_path))) {
                    $error_message = "Folder tujuan tidak dapat ditulis. Periksa permission.";
                } else {
                    if (move_uploaded_file($file['tmp_name'], $server_path)) {
                        // Simpan ke DB
                        $insert = $pdo->prepare("
                            INSERT INTO company_documents 
                            (company_id, doc_type, file_path, file_name_orig, mime_type, file_size, uploaded_by, valid_from, valid_to)
                            VALUES
                            (:company_id, :doc_type, :file_path, :file_name_orig, :mime_type, :file_size, :uploaded_by, :valid_from, :valid_to)
                        ");
                        $insert->execute([
                            ':company_id' => $company_id,
                            ':doc_type' => $doc_type,
                            ':file_path' => $db_path,
                            ':file_name_orig' => $orig_name,
                            ':mime_type' => $mime,
                            ':file_size' => $size,
                            ':uploaded_by' => $user_id,
                            ':valid_from' => $valid_from ?: null,
                            ':valid_to' => $valid_to ?: null
                        ]);

                        $success_message = "Dokumen berhasil diunggah.";
                    } else {
                        $error_message = "Gagal memindahkan file. Periksa permission.";
                    }
                }
            }
        }
    }
}


/* -----------------------------
   Ambil Jenis Dokumen & Data Upload
----------------------------- */
$stmt_types = $pdo->query("SELECT kode_dokumen, nama_dokumen FROM master_jenis_dokumen WHERE aktif = 'Y' ORDER BY nama_dokumen ASC");
$jenis_dokumen = $stmt_types->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT d.*, m.nama_dokumen 
    FROM company_documents d
    LEFT JOIN master_jenis_dokumen m ON d.doc_type = m.kode_dokumen
    WHERE d.company_id = :cid 
    ORDER BY d.uploaded_at DESC
");
$stmt->execute([':cid' => $company_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'menu_public.php';
?>


<!-- ============================== -->
<!--        TAMPILAN HTML           -->
<!-- ============================== -->
<div class="container mt-4">
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($success_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($error_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white text-center">
            <h4 class="mb-0">Upload Dokumen Legal Perusahaan</h4>
        </div>

        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Jenis Dokumen</label>
                    <select name="doc_type" class="form-select" required>
                        <option value="">-- Pilih Jenis Dokumen --</option>
                        <?php foreach ($jenis_dokumen as $jenis): ?>
                            <option value="<?= htmlspecialchars($jenis['kode_dokumen']) ?>">
                                <?= htmlspecialchars($jenis['nama_dokumen']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>File Dokumen</label>
                    <input type="file" name="doc_file" class="form-control" accept="application/pdf" required>
                    <div class="form-text text-danger">* Hanya PDF, maksimal 5 MB.</div>
                </div>

                <div class="mb-3">
                    <label>Masa Berlaku (Opsional)</label>
                    <div class="row">
                        <div class="col">
                            <input type="date" name="valid_from" class="form-control">
                        </div>
                        <div class="col">
                            <input type="date" name="valid_to" class="form-control">
                        </div>
                    </div>
                </div>

                <button type="submit" name="upload" class="btn btn-success">Upload Dokumen</button>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-secondary text-white text-center">
            <h5 class="mb-0">Daftar Dokumen Anda</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Jenis Dokumen</th>
                            <th>Nama File</th>
                            <th>Status</th>
                            <th>Komentar Admin</th>
                            <th>Masa Berlaku</th>
                            <th>Tanggal Upload</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Belum ada dokumen diunggah.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($documents as $doc): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($doc['nama_dokumen'] ?? $doc['doc_type']) ?></td>
                                    <td><?= htmlspecialchars($doc['file_name_orig']) ?></td>
                                    <td>
                                        <?php if ($doc['status'] === 'approved'): ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php elseif ($doc['status'] === 'rejected'): ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= nl2br(htmlspecialchars($doc['notes'] ?? '-')) ?></td>
                                    <td><?= ($doc['valid_from'] ?: '-') . " s/d " . ($doc['valid_to'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($doc['uploaded_at']) ?></td>
                                    <td>
                                        <a href="view_document.php?id=<?= $doc['id'] ?>" class="btn btn-primary btn-sm mb-1" target="_blank">Lihat</a>
                                        <a href="update_document.php?id=<?= $doc['id'] ?>" class="btn btn-warning btn-sm mb-1">Update</a>
                                        <a href="delete_document.php?id=<?= $doc['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus dokumen ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>