<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

// PANGGIL HEADER DAN MENU ADMIN
include 'header&menu_admin.php';

// --- Cek role admin ---
if ($_SESSION['role_id'] != 1) {
    die("<h3>ACCESS DENIED</h3>");
}

// Ambil seluruh perusahaan
$stmt = $pdo->query("SELECT c.*, u.username 
                     FROM companies c 
                     LEFT JOIN users u ON c.user_id = u.id
                     ORDER BY c.id DESC");
$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container main-panel mt-4">
    <div class="content-wrapper">

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Verifikasi Identitas Perusahaan</h4>
            </div>

            <div class="card-body">

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Nama Perusahaan</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($companies as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['username'] ?></td>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td><?= $row['phone'] ?></td>
                                <td>
                                    <?php
                                    $status = $row['status'] ?? "draft";
                                    $badge = "secondary";

                                    switch ($status) {
                                        case "draft":
                                            $badge = "secondary";
                                            break;
                                        case "submitted":
                                            $badge = "warning";
                                            break;
                                        case "under_review":
                                            $badge = "info";
                                            break;
                                        case "verified":
                                            $badge = "success";
                                            break;
                                        case "rejected":
                                            $badge = "danger";
                                            break;
                                    }
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= strtoupper($status) ?></span>
                                </td>
                                <td>
                                    <a href="verifikasi_identitas.php?id=<?= $row['id'] ?>"
                                        class="btn btn-sm btn-primary">
                                        Verifikasi
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>

            </div>
        </div>

    </div>
</div>

<?php include 'footer_admin.php'; ?>