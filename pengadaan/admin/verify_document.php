<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// Cek role admin (Superadmin = 1)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit;
}
$username = $_SESSION['username'];
$role_id  = $_SESSION['role_id'];

// Validasi id dokumen
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: verifikasi_document.php?msg=" . urlencode("ID dokumen tidak valid") . "&type=danger");
    exit;
}

$doc_id = (int) $_GET['id'];

// Ambil data dokumen + perusahaan + vendor
$stmt = $pdo->prepare("
    SELECT d.*, 
           c.name AS company_name,
           u.username
    FROM company_documents d
    JOIN companies c ON d.company_id = c.id
    JOIN users u ON d.uploaded_by = u.id
    WHERE d.id = :id
    LIMIT 1
");
$stmt->execute([':id' => $doc_id]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doc) {
    header("Location: verifikasi_document.php?msg=" . urlencode("Dokumen tidak ditemukan") . "&type=danger");
    exit;
}

// Jika submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $status = $_POST['status'] ?? 'pending';
    $notes  = $_POST['notes'] ?? null;

    // Update status dokumen
    $update = $pdo->prepare("
        UPDATE company_documents 
        SET status = :status, notes = :notes 
        WHERE id = :id
    ");
    $update->execute([
        ':status' => $status,
        ':notes'  => $notes,
        ':id'     => $doc_id
    ]);

    // Redirect kembali ke daftar dokumen vendor
    header("Location: verifikasi_document.php?company_id=" . urlencode($doc['company_id']) . "&msg=" . urlencode("Status dokumen berhasil diperbarui!") . "&type=success");
    exit;
}

include 'menu.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Dokumen Vendor - E-Proc UNPATTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        iframe {
            width: 100%;
            height: 600px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-body">

                <h3 class="mb-4">Verifikasi Dokumen Vendor</h3>

                <!-- Tombol kembali -->
                <a href="verifikasi_document.php?company_id=<?= $doc['company_id'] ?>" class="btn btn-secondary btn-sm mb-3">
                    ← Kembali ke Daftar Dokumen
                </a>

                <div class="alert alert-info">
                    <strong>Informasi Dokumen</strong><br>
                    <b>Perusahaan:</b> <?= htmlspecialchars($doc['company_name']) ?><br>
                    <b>Vendor:</b> <?= htmlspecialchars($doc['username']) ?><br>
                    <b>Jenis Dokumen:</b> <?= htmlspecialchars($doc['doc_type']) ?><br>
                    <b>Nama File:</b> <?= htmlspecialchars($doc['file_name_orig']) ?><br>
                    <b>Status Saat Ini:</b>
                    <?php if ($doc['status'] == 'approved'): ?>
                        <span class="badge bg-success">Approved</span>
                    <?php elseif ($doc['status'] == 'rejected'): ?>
                        <span class="badge bg-danger">Rejected</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                    <?php endif; ?>
                </div>

                <!-- PREVIEW DOKUMEN LANGSUNG -->
                <!-- <div class="mb-4">
                    <h5 class="mb-3">Pratinjau Dokumen:</h5>
                    <?php
                    // $file_path = "/Applications/XAMPP/storage_secure" . htmlspecialchars($doc['file_name']);
                    // if (file_exists($file_path)) {
                    //     echo '<iframe src="' . $file_path . '#toolbar=1&navpanes=0&scrollbar=1"></iframe>';
                    // } else {
                    //     echo '<div class="alert alert-danger">File tidak ditemukan di server.</div>';
                    // }
                    ?>
                </div> -->
                <div class="mb-4">
                    <h5 class="mb-3">Pratinjau Dokumen:</h5>
                    <iframe src="view_document_admin.php?id=<?= $doc['id'] ?>#toolbar=1" allow="fullscreen"></iframe>
                </div>


                <!-- FORM VERIFIKASI -->
                <form method="POST" class="mt-4">
                    <div class="form-group mb-3">
                        <label>Status Verifikasi</label>
                        <select name="status" class="form-control" required>
                            <option value="pending" <?= $doc['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $doc['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $doc['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label>Catatan (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="4"><?= htmlspecialchars($doc['notes']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Simpan Verifikasi</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php include 'footer.php'; ?>