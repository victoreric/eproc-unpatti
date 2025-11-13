<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// Cek role vendor
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
    die("Company ID tidak ditemukan!");
}

if (!isset($_GET['id'])) {
    die("ID dokumen tidak tersedia.");
}

$doc_id = (int)$_GET['id'];

// Pastikan dokumen milik company ini
$stmt = $pdo->prepare("SELECT * FROM company_documents WHERE id = :id AND company_id = :cid");
$stmt->execute([':id' => $doc_id, ':cid' => $company_id]);
$document = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$document) {
    die("Dokumen tidak ditemukan atau tidak memiliki akses!");
}

$success_message = "";
$error_message = "";

// ========== PROSES UPDATE ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $doc_type = trim($_POST['doc_type']);
    $valid_from = $_POST['valid_from'] ?: null;
    $valid_to = $_POST['valid_to'] ?: null;

    if (empty($doc_type)) {
        $error_message = "Jenis dokumen wajib dipilih.";
    }

    // Jika upload file baru
    $new_file_uploaded = isset($_FILES['doc_file']) && $_FILES['doc_file']['error'] !== UPLOAD_ERR_NO_FILE;

    $db_path = $document['file_path'];
    $file_name_orig = $document['file_name_orig'];
    $file_size = $document['file_size'];

    // ========== Jika user upload file baru ==========
    if ($new_file_uploaded) {

        $file = $_FILES['doc_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error_message = "Upload error: " . $file['error'];
        } else {

            // Validasi PDF
            $orig_name = $file['name'];
            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            $size = (int)$file['size'];
            $max_size = 5 * 1024 * 1024;

            if ($ext !== 'pdf') {
                $error_message = "Hanya PDF diizinkan!";
            } elseif ($size > $max_size) {
                $error_message = "File melebihi 5MB.";
            } else {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $real_mime = $finfo->file($file['tmp_name']);

                if ($real_mime !== 'application/pdf') {
                    $error_message = "File bukan PDF yang valid.";
                }
            }

            if (empty($error_message)) {
                // Hapus file lama
                $old_path = __DIR__ . "/../../../../../storage_secure/" . $document['file_path'];
                if (file_exists($old_path)) {
                    unlink($old_path);
                }

                // Folder baru
                $storage_root = __DIR__ . "/../../../../../storage_secure/eproc_uploads/company_docs/$company_id/";
                if (!is_dir($storage_root)) {
                    mkdir($storage_root, 0775, true);
                }

                // Nama file baru
                $safe_name = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($orig_name, PATHINFO_FILENAME));
                $new_filename = time() . "_" . $safe_name . ".pdf";

                $server_path = $storage_root . $new_filename;
                $db_path = "eproc_uploads/company_docs/$company_id/" . $new_filename;

                // Pindahkan file baru
                if (!move_uploaded_file($file['tmp_name'], $server_path)) {
                    $error_message = "Gagal menyimpan file baru.";
                } else {
                    $file_name_orig = $orig_name;
                    $file_size = $size;
                }
            }
        }
    }

    // ========== Jika tidak ada error → update DB ==========
    if (empty($error_message)) {

        $update = $pdo->prepare("
            UPDATE company_documents
            SET doc_type = :doc_type,
                file_path = :file_path,
                file_name_orig = :file_name_orig,
                file_size = :file_size,
                valid_from = :valid_from,
                valid_to = :valid_to,
                status = 'pending',     -- otomatis pending lagi
                notes = NULL            -- reset catatan admin
            WHERE id = :id AND company_id = :cid
        ");

        $update->execute([
            ':doc_type' => $doc_type,
            ':file_path' => $db_path,
            ':file_name_orig' => $file_name_orig,
            ':file_size' => $file_size,
            ':valid_from' => $valid_from,
            ':valid_to' => $valid_to,
            ':id' => $doc_id,
            ':cid' => $company_id
        ]);

        $success_message = "Dokumen berhasil diperbarui. Menunggu verifikasi admin.";

        // Refresh data
        $stmt->execute([':id' => $doc_id, ':cid' => $company_id]);
        $document = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

include 'menu_public.php';
?>

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Update Dokumen</h4>
        </div>

        <div class="card-body">

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= $success_message ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label>Jenis Dokumen</label>
                    <select name="doc_type" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <option value="formulirikut" <?= $document['doc_type'] == 'formulirikut' ? 'selected' : '' ?>>Formulir Keikutsertaan</option>
                        <option value="formulirdaftar" <?= $document['doc_type'] == 'formulirdaftar' ? 'selected' : '' ?>>Formulir Pendaftaran</option>
                        <option value="SIUP" <?= $document['doc_type'] == 'SIUP' ? 'selected' : '' ?>>SIUP</option>
                        <option value="NPWP" <?= $document['doc_type'] == 'NPWP' ? 'selected' : '' ?>>NPWP</option>
                        <option value="Akta" <?= $document['doc_type'] == 'Akta' ? 'selected' : '' ?>>Akta</option>
                        <option value="KTP Direktur" <?= $document['doc_type'] == 'KTP Direktur' ? 'selected' : '' ?>>KTP Direktur</option>
                        <option value="Dokumen Lainnya" <?= $document['doc_type'] == 'Dokumen Lainnya' ? 'selected' : '' ?>>Dokumen Lainnya</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>File PDF Baru (opsional)</label>
                    <input type="file" name="doc_file" class="form-control" accept="application/pdf">
                    <div class="form-text text-danger">Kosongkan jika tidak mengganti file.</div>
                </div>

                <div class="mb-3">
                    <label>Masa Berlaku (opsional)</label>
                    <div class="row">
                        <div class="col">
                            <input type="date" name="valid_from" class="form-control"
                                value="<?= $document['valid_from'] ?>">
                        </div>
                        <div class="col">
                            <input type="date" name="valid_to" class="form-control"
                                value="<?= $document['valid_to'] ?>">
                        </div>
                    </div>
                </div>

                <button class="btn btn-warning">Update Dokumen</button>
                <a href="upload_documents.php" class="btn btn-secondary">Kembali</a>
            </form>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>