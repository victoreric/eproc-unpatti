<?php
// vendor/upload_documents.php (atau path sesuai proyek)
// Pastikan file ini disimpan di folder vendor/ atau public/vendor sesuai struktur Anda.

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// Pastikan role adalah vendor
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

// Inisialisasi pesan
$success_message = '';
$error_message = '';

// --- Helper untuk error upload PHP ---
function uploadErrorMessage($code)
{
    $errors = [
        UPLOAD_ERR_OK => 'There is no error, the file uploaded with success.',
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
    ];
    return $errors[$code] ?? 'Unknown upload error';
}

// Proses upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $doc_type = trim($_POST['doc_type'] ?? '');
    $valid_from = $_POST['valid_from'] ?: null;
    $valid_to = $_POST['valid_to'] ?: null;

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
                $error_message = "File tidak diperbolehkan. Hanya PDF";
            } elseif ($size > $max_size) {
                $error_message = "Ukuran file melebihi 5 MB.";
            } else {
                // Tentukan lokasi storage (ABSOLUTE) dan path yang disimpan ke DB (relative/public)
                // Sesuaikan lokasi storage_root jika folder Anda berbeda
                // $storage_root = __DIR__ . "/eproc_uploads";
                $storage_root = __DIR__ . "/../../../../../storage_secure/eproc_uploads";
                // pastikan storage root ada, jika tidak buat
                if (!is_dir($storage_root)) {
                    if (!mkdir($storage_root, 0775, true)) {
                        $error_message = "Folder storage tidak dapat dibuat. Periksa permission.";
                    }
                }

                if (empty($error_message)) {
                    $company_dir = $storage_root . "/company_docs/$company_id/";
                    if (!is_dir($company_dir)) {
                        if (!mkdir($company_dir, 0775, true)) {
                            $error_message = "Gagal membuat folder untuk menyimpan file. Periksa permission.";
                        }
                    }
                }

                if (empty($error_message)) {
                    // buat nama file aman untuk disimpan
                    $safe_name = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($orig_name, PATHINFO_FILENAME));
                    $new_filename = time() . "_" . $safe_name . "." . $ext;
                    $server_path = $company_dir . $new_filename; // absolute path on server
                    // path yang akan disimpan ke DB relatif terhadap root project (untuk link)
                    $db_path = "eproc_uploads/company_docs/$company_id/" . $new_filename;

                    // pastikan folder writable
                    if (!is_writable(dirname($server_path))) {
                        $error_message = "Folder tujuan tidak dapat ditulis oleh PHP. Periksa permission (chmod).";
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
                            $error_message = "Gagal memindahkan file ke direktori tujuan. Periksa permission dan path.";
                        }
                    }
                }
            }
        }
    }
}

// Ambil semua dokumen perusahaan
$stmt = $pdo->prepare("SELECT * FROM company_documents WHERE company_id = :cid ORDER BY uploaded_at DESC");
$stmt->execute([':cid' => $company_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// setelah proses backend, include menu_public supaya alert muncul di UI dengan benar
include 'menu_public.php';
?>

<!-- content -->
<div class="col-12 grid-margin">
    <div class="card">
        <div class="card-body">
            <div class="container mt-4">

                <?php
                // ALERT NOTIFICATION dari delete_document.php
                if (isset($_GET['del'])) {
                    if ($_GET['del'] === 'success') {
                        echo '<div class="alert alert-success alert-dismissible fade show">
                Dokumen berhasil dihapus.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
                    } elseif ($_GET['del'] === 'success_file_missing') {
                        echo '<div class="alert alert-warning alert-dismissible fade show">
                Dokumen dihapus dari database, namun file fisik sudah tidak ditemukan.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
                    } elseif ($_GET['del'] === 'error') {
                        $msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Terjadi kesalahan.';
                        echo '<div class="alert alert-danger alert-dismissible fade show">
                Error: ' . $msg . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
                    }
                }
                ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow">
                    <div class="card-header bg-info text-white text-center">
                        <h4 class="mb-0">Upload Dokumen Legal Perusahaan</h4>
                    </div>

                    <div class="card-body">

                        <form method="POST" enctype="multipart/form-data">

                            <div class="mb-3">
                                <label>Jenis Dokumen</label>
                                <select name="doc_type" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="SIUP">SIUP</option>
                                    <option value="NPWP">NPWP</option>
                                    <option value="Akta">Akta Perusahaan</option>
                                    <option value="KTP Direktur">KTP Direktur</option>
                                    <option value="Dokumen Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>File Dokumen</label>
                                <input type="file" name="doc_file" class="form-control" accept="application/pdf" required>
                                <div class="form-text text-danger">* Hanya PDF, Maks 5MB.</div>
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

                        <hr>

                        <h5>Daftar Dokumen Anda</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mt-2">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis</th>
                                        <th>Nama File</th>
                                        <th>Status</th>
                                        <th>Komentar Admin</th>
                                        <th>Masa Berlaku</th>
                                        <th>Upload</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php $no = 1;
                                    foreach ($documents as $doc): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($doc['doc_type']) ?></td>
                                            <td><?= htmlspecialchars($doc['file_name_orig']) ?></td>

                                            <td>
                                                <?php if ($doc['status'] === "approved"): ?>
                                                    <span class="badge bg-success">Approved</span>
                                                <?php elseif ($doc['status'] === "rejected"): ?>
                                                    <span class="badge bg-danger">Rejected</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php endif; ?>
                                            </td>

                                            <td><?= nl2br(htmlspecialchars($doc['notes'] ?? '-')) ?></td>

                                            <td>
                                                <?= $doc['valid_from'] ?: '-' ?> s/d <?= $doc['valid_to'] ?: '-' ?>
                                            </td>

                                            <td><?= $doc['uploaded_at'] ?></td>

                                            <td>
                                                <?php if (!empty($doc['file_path'])): ?>
                                                    <!-- <a href="../<?= ltrim($doc['file_path'], '/') ?>" class="btn btn-primary btn-sm" target="_blank">Lihat</a> -->
                                                    <a href="view_document.php?id=<?= $doc['id'] ?>"
                                                        class="btn btn-primary btn-sm" target="_blank">
                                                        Lihat
                                                    </a>

                                                <?php endif; ?>
                                                <a href="update_document.php?id=<?= $doc['id'] ?>" class="btn btn-warning btn-sm">Update</a>
                                                <a href="delete_document.php?id=<?= $doc['id'] ?>" onclick="return confirm('Hapus dokumen?')" class="btn btn-danger btn-sm">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- PANGGIL footer.PHP -->
<?php include 'footer.php'; ?>