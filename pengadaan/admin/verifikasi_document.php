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



    <!-- CONTENT -->
    <?php
    require_once __DIR__ . '/../includes/auth_check.php';
    require_once __DIR__ . '/../config/db.php';

    // pastikan admin
    if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
        header("Location: ../index.php");
        exit;
    }

    // query dokumen + info perusahaan + vendor
    $stmt = $pdo->query("SELECT 
        d.*, 
        c.name AS company_name,
        u.username
    FROM company_documents d
    JOIN companies c ON d.company_id = c.id
    JOIN users u ON d.uploaded_by = u.id
    ORDER BY d.uploaded_at DESC
");

    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    include 'menu.php';
    ?>

    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">

                <h3>Verifikasi Dokumen Vendor</h3>

                <div class="table-responsive">
                    <table id="vendorTable" class="table table-striped table-bordered mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Perusahaan</th>
                                <th>Jenis Dokumen</th>
                                <th>Nama File</th>
                                <th>Status</th>
                                <th>Vendor</th>
                                <th>Upload</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $no = 1;
                            foreach ($documents as $doc): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($doc['company_name']) ?></td>
                                    <td><?= htmlspecialchars($doc['doc_type']) ?></td>
                                    <td><?= htmlspecialchars($doc['file_name_orig']) ?></td>

                                    <td>
                                        <?php if ($doc['status'] == 'approved'): ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php elseif ($doc['status'] == 'rejected'): ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?= htmlspecialchars($doc['username']) ?></td>
                                    <td><?= $doc['uploaded_at'] ?></td>

                                    <td>
                                        <a href="view_document_admin.php?id=<?= $doc['id'] ?>"
                                            class="btn btn-primary btn-sm" target="_blank">Lihat</a>

                                        <a href="verify_document.php?id=<?= $doc['id'] ?>"
                                            class="btn btn-warning btn-sm">Verifikasi</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- JQuery (WAJIB PALING ATAS sebelum Datatables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Datatables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>



    <!-- Inisialisasi Datatables -->
    <script>
        $(document).ready(function() {
            $('#vendorTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [5, 10, 20, 50, 100],
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

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

</body>

</html>

<?php include 'footer.php'; ?>