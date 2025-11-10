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

    <!-- PANGGIL MENU.PHP -->
    <?php include 'menu.php'; ?>

    <!-- CONTENT -->
    <?php
    // Ambil data perusahaan
    $stmt = $pdo->query("
    SELECT c.*, u.email_verified, u.admin_verified, u.email 
    FROM companies c
    JOIN users u ON c.user_id = u.id
    ORDER BY c.created_at DESC ");

    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="container mt-4">

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Daftar Perusahaan / Vendor</h4>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table id="vendorTable" class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Perusahaan</th>
                                <th>Email</th>
                                <th>Telp</th>
                                <th>Kode Member</th>
                                <th>Status Email</th>
                                <th>Status Admin</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($companies as $c): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($c['name']) ?></td>
                                    <td><?= htmlspecialchars($c['email']) ?></td>
                                    <td><?= htmlspecialchars($c['phone']) ?></td>
                                    <td><?= htmlspecialchars($c['code_member']) ?></td>

                                    <td>
                                        <?= $c['email_verified']
                                            ? '<span class="badge bg-success">Verified</span>'
                                            : '<span class="badge bg-warning">Pending</span>' ?>
                                    </td>

                                    <td>
                                        <?= $c['admin_verified']
                                            ? '<span class="badge bg-success">Approved</span>'
                                            : '<span class="badge bg-secondary">Pending</span>' ?>
                                    </td>

                                    <td>
                                        <?php if (!$c['admin_verified']): ?>
                                            <a href="verify_company.php?id=<?= $c['id'] ?>&action=approve"
                                                class="btn btn-success btn-sm w-100 mb-1">Verifikasi</a>

                                            <a href="verify_company.php?id=<?= $c['id'] ?>&action=reject"
                                                class="btn btn-danger btn-sm w-100">Tolak</a>
                                        <?php else: ?>
                                            <span class="text-muted">Sudah diverifikasi</span>
                                        <?php endif; ?>
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