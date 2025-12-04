<!-- HEADER DAN MENU ADMIN
 CREATE BY : VICTOR ERIC -->

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// MULAI SESSION HANYA SEKALI
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';


// Jika bukan admin, redirect
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../index.php");
    exit;
}

$username = $_SESSION['username'];
$role_id  = $_SESSION['role_id'];

// Cek role admin (Superadmin = 1)
// if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
//     header("Location: index.php");
//     exit;
// }

$username = $_SESSION['username'];
$role_id  = $_SESSION['role_id'];

switch ($role_id) {
    case 1:
        $role = 'Superadmin';
        break;
    case 2:
        $role = 'Admin Instansi';
        break;
    case 3:
        $role = 'Vendor';
        break;
    default:
        $role = 'User';
        break;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard ADMIN - E-proc UNPATTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

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


    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">E-Proc UNPATTI</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarMenu" aria-controls="navbarMenu"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav me-auto">

                    <!-- MENU UNTUK SUPERADMIN & ADMIN INSTANSI -->
                    <?php if ($role_id == 1 || $role_id == 2): ?>

                        <!-- Manajemen Vendor -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="vendorMenu"
                                role="button" data-bs-toggle="dropdown">
                                Manajemen Vendor
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="list_perusahaan.php">Daftar perusahaan</a></li>

                                <!-- <li><a class="dropdown-item" href="verifikasi_company_identitas.php">Verifikasi identitas</a></li> -->

                            </ul>
                        </li>

                        <!-- Pengadaan -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="pengadaanMenu"
                                role="button" data-bs-toggle="dropdown">
                                Pengadaan
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="../pengadaan/create.php">Buat Pengadaan</a></li>
                                <li><a class="dropdown-item" href="../pengadaan/list.php">Daftar Pengadaan</a></li>
                                <li><a class="dropdown-item" href="../pengadaan/penilaian.php">Penilaian Penawaran</a></li>
                            </ul>
                        </li>

                        <!-- User Management (khusus Superadmin) -->
                        <?php if ($role_id == 1): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userMenu"
                                    role="button" data-bs-toggle="dropdown">
                                    User Management
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="master_user.php">Daftar User</a></li>
                                    <!-- <li><a class="dropdown-item" href="create_user.php">Tambah User</a></li> -->
                                    <li><a class="dropdown-item" href="master_jenis_dokumen.php">Jenis Dokumen User</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <!-- notify -->
                        <?php
                        // $notif_count = 0;

                        // $result = $dsn->query("SELECT COUNT(*) AS total FROM notifications WHERE is_read = 0");
                        // if ($row = $result->fetch_assoc()) {
                        //     $notif_count = $row['total'];
                        // }
                        $notif_count = 0;

                        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM notifications WHERE is_read = 0");
                        $row = $stmt->fetch();
                        $notif_count = $row['total'] ?? 0;

                        ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="notifications.php">
                                🔔 Notifikasi
                                <?php if ($notif_count > 0): ?>
                                    <span style="background:red;color:white;padding:3px 7px;border-radius:50%;">
                                        <?= $notif_count ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>

                    <?php endif; ?>

                    <!-- MENU UNTUK VENDOR -->
                    <?php if ($role_id == 3): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../vendor/profile.php">Profil Perusahaan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../vendor/pengadaan_tersedia.php">Pengadaan Tersedia</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../vendor/penawaran_saya.php">Penawaran Saya</a>
                        </li>
                    <?php endif; ?>

                </ul>

                <div class="d-flex">
                    <span class="text-white me-3">Halo, <strong><?= htmlspecialchars($username) ?></strong></span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
                </div>

            </div>
        </div>
    </nav>