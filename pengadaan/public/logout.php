<?php
// session_start();
// session_unset();
// session_destroy();
// header('Location: index.php');
// exit;


session_start();
require_once __DIR__ . '/../config/db.php';

// ===== Hapus token remember me di database =====
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL, remember_token_expire = NULL WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
}

// ===== Hapus cookie remember_token =====
if (isset($_COOKIE['remember_token'])) {
    setcookie(
        'remember_token',
        '',
        [
            'expires' => time() - 3600, // kadaluarsa
            'path' => '/',
            'secure' => false, // ubah ke true jika pakai HTTPS
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}

// ===== Hapus semua session =====
$_SESSION = [];
session_unset();
session_destroy();

// ===== Redirect ke halaman login =====
header('Location: index.php');
exit;
