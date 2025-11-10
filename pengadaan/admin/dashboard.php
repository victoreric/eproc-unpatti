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
    <title>Dashboard - E-proc UNPATTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <!-- PANGGIL MENU.PHP -->
    <?php include 'menu.php'; ?>

    <!-- CONTENT -->
    <div class="container mt-4">
        <div class="alert alert-success shadow-sm">
            <h4>Selamat datang, <?= htmlspecialchars($username) ?>!</h4>
            <p>Anda login sebagai: <strong><?= $role ?></strong></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>