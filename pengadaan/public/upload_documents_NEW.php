<?php
// vendor/upload_documents.php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// Pastikan role vendor
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

$success_message = "";
$error_message = "";

// Helper pesan error upload
function uploadErrorMessage($code)
{
    $errors = [
        UPLOAD_ERR_OK => 'Tidak ada masalah upload.',
        UPLOAD_ERR_INI_SIZE => 'File melebihi batas php.ini.',
        UPLOAD_ERR_FORM_SIZE => 'File melebihi batas form.',
        UPLOAD_ERR_PARTIAL => 'File ter-upload sebagian.',
        UPLOAD_ERR_NO_FILE => 'Tidak ada file.',
        UPLOAD_ERR_NO_TMP_DIR => 'Temp folder hilang.',
        UPLOAD_ERR_CANT_WRITE => 'Tidak bisa write ke disk.',
        UPLOAD_ERR_EXTENSION => 'Upload diblokir extension.',
    ];
    return $errors[$code] ?? 'Unknown upload error';
}

//
// =============================
// PROSES UPLOAD PDF
// =============================
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

            // Validasi PDF
            $max_size = 5 * 1024 * 1024; // 5 MB
            $orig_name = $file['name'];
            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            $size = (int)$file['size'];

            // 1 – ekstensi wajib pdf
            if ($ext !== 'pdf') {
                $error_message = "File harus berformat PDF.";
            }
            // 2 – ukuran
            elseif ($size > $max_size) {
                $error_message = "Ukuran file melebihi 5 MB.";
            }
            // 3 – cek MIME asli
            else {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $real_mime = $finfo->file($file['tmp_name']);

                if ($real_mime !== 'application/pdf') {
                    $error_message = "File tidak valid. Hanya PDF asli yang diperbolehkan.";
                }
            }

            // Jika PDF valid → proses lanjut
            if (empty($error_message)) {

                // Lokasi penyimpanan secure
                $storage_root = __DIR__ . "/../../../../../storage_secure/eproc_uploads";

                if (!is_dir($storage_root)) {
                    mkdir($storage_root, 0775, true);
                }

                $company_dir = $storage_root . "/company_docs/$company_id/";
                if (!is_dir($company_dir)) {
                    mkdir($company_dir, 0775, true);
                }

                // Nama aman
                $safe_name = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($orig_name, PATHINFO_FILENAME));
                $new_filename = time() . "_" . $safe_name . ".pdf";

                $server_path = $company_dir . $new_filename;
                $db_path = "eproc_uploads/company_docs/$company_id/" . $new_filename;

                if (!move_uploaded_file($file['tmp_name'], $server_path)) {
                    $error_message = "Gagal memindahkan file ke direktori tujuan.";
                } else {

                    // Simpan DB
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
                        ':orig_name' => $orig_name,
                        ':file_size' => $size,
                        ':uploaded_by' => $user_id,
                        ':valid_from' => $valid_from ?: null,
                        ':valid_to' => $valid_to ?: null
                    ]);

                    $success_message = "Dokumen berhasil diunggah.";
                }
            }
        }
    }
}

// Ambil dokumen
$stmt = $pdo->prepare("SELECT * FROM company_documents WHERE company_id = :cid ORDER BY uploaded_at DESC");
$stmt->execute([':cid' => $company_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load menu vendor
include 'menu_public.php';
?>

<!-- UI -->
<div class="col-12 grid-margin">
    <div class="card">
        <div class="card-body">
            <div class="container mt-4">

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= htmlspecialchars($success_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= htmlspecialchars($error_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0">Upload Dokumen Legal Perusahaan (PDF Only)</h4>
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
                                    <option value="Dokumen Lainnya">Dokumen Lainnya</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Upload PDF</label>
                                <input type="file" name="doc_file" class="form-control" accept="application/pdf" required>
                                <div class="form-text text-danger">* Hanya PDF asli, maks 5MB</div>
                            </div>

                            <div class="mb-3">
                                <label>Masa Berlaku (opsional)</label>
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

                                            <td><?= $doc['valid_from'] ?: '-' ?> s/d <?= $doc['valid_to'] ?: '-' ?></td>
                                            <td><?= $doc['uploaded_at'] ?></td>

                                            <td>
                                                <?php if (!empty($doc['file_path'])): ?>
                                                    <a href="../<?= ltrim($doc['file_path'], '/') ?>" target="_blank" class="btn btn-primary btn-sm">Lihat</a>
                                                <?php endif; ?>

                                                <a href="update_document.php?id=<?= $doc['id'] ?>" class="btn btn-warning btn-sm">Update</a>

                                                <a href="delete_document.php?id=<?= $doc['id'] ?>"
                                                    onclick="return confirm('Hapus dokumen?')"
                                                    class="btn btn-danger btn-sm">Delete</a>
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

<?php include 'footer.php'; ?>