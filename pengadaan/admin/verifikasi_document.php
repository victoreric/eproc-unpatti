<?php
session_start();
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// pastikan admin
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../index.php");
    exit;
}
$username = $_SESSION['username'];
$role_id  = $_SESSION['role_id'];




$company_id = isset($_GET['company_id']) ? (int)$_GET['company_id'] : 0;

if ($company_id <= 0) {
    die("Parameter company_id tidak valid.");
}

// ambil info perusahaan
$stmt = $pdo->prepare("SELECT * FROM companies WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $company_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    die("Perusahaan tidak ditemukan.");
}

// query dokumen milik perusahaan ini
$stmt = $pdo->prepare("
    SELECT 
        d.*, 
        c.name AS company_name,
        u.username
    FROM company_documents d
    JOIN companies c ON d.company_id = c.id
    JOIN users u ON d.uploaded_by = u.id
    WHERE d.company_id = :cid
    ORDER BY d.uploaded_at DESC
");
$stmt->execute([':cid' => $company_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Perusahaan - E-Proc UNPATTI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
</head>

<body class="bg-light">
    <?php include 'menu.php'; ?>


    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">

                <h3>Verifikasi Dokumen Vendor: <span class="text-primary"><?= htmlspecialchars($company['name']) ?></span></h3>
                <p><strong>Email:</strong> <?= htmlspecialchars($company['email']) ?> &nbsp; | &nbsp;
                    <strong>Telepon:</strong> <?= htmlspecialchars($company['phone']) ?>
                </p>

                <div class="table-responsive mt-4">
                    <table id="docTable" class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Jenis Dokumen</th>
                                <th>Nama File</th>
                                <th>Status</th>
                                <th>Upload</th>
                                <th>Vendor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($documents) == 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada dokumen diunggah.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;
                                foreach ($documents as $doc): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($doc['doc_type']) ?></td>
                                        <td><?= htmlspecialchars($doc['file_name_orig']) ?></td>

                                        <td>
                                            <?php if ($doc['status'] == 'approved'): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php elseif ($doc['status'] == 'rejected'): ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php endif; ?>
                                        </td>

                                        <td><?= htmlspecialchars($doc['uploaded_at']) ?></td>
                                        <td><?= htmlspecialchars($doc['username']) ?></td>

                                        <td>
                                            <a href="view_document_admin.php?id=<?= $doc['id'] ?>"
                                                class="btn btn-primary btn-sm" target="_blank">Lihat</a>

                                            <a href="verify_document.php?id=<?= $doc['id'] ?>"
                                                class="btn btn-warning btn-sm">Verifikasi</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <a href="list_perusahaan.php" class="btn btn-secondary">← Kembali ke Daftar Perusahaan</a>
                </div>

            </div>
        </div>
    </div>

    <!-- JQuery + Bootstrap + Datatables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#docTable').DataTable({
                "pageLength": 10,
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    "paginate": {
                        "first": "Awal",
                        "last": "Akhir",
                        "next": "›",
                        "previous": "‹"
                    }
                }
            });
        });
    </script>

    <?php include 'footer.php'; ?>