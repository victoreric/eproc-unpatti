<?php
require '../config/db.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Token verifikasi tidak ditemukan.");
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE verify_token = :token LIMIT 1");
$stmt->execute([':token' => $token]);
$user = $stmt->fetch();

if (!$user) {
    die("Token tidak valid atau sudah digunakan.");
}

// Update status user
$stmt = $pdo->prepare("
    UPDATE users 
    SET email_verified = 1, is_active = 1, verify_token = NULL 
    WHERE id = :id
");
$stmt->execute([':id' => $user['id']]);

echo "
<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Berhasil</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
</head>
<body>

<div class='container mt-5' style='max-width: 600px;'>
    <div class='card shadow-lg'>
        <div class='card-body text-center'>
            <h3 class='text-success'>Verifikasi Email Berhasil</h3>
            <p>Akun Anda sudah aktif. Silakan login menggunakan username dan password.</p>
            <a href='index.php' class='btn btn-primary'>Login Sekarang</a>
        </div>
    </div>
</div>

</body>
</html>
";
