<?php
if (!isset($_SESSION)) {
    session_start();
}

// Jika bukan admin, redirect
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../index.php");
    exit;
}

$username = $_SESSION['username'];
?>
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
                            <li><a class="dropdown-item" href="verifikasi_document.php">Verifikasi Dokumen</a></li>
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
                                <li><a class="dropdown-item" href="../admin/users.php">Daftar User</a></li>
                                <li><a class="dropdown-item" href="../admin/create_user.php">Tambah User</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

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