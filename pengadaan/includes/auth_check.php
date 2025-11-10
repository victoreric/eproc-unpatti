<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

// Jika session kosong, coba login otomatis dari cookie remember me
if (!isset($_SESSION['user_id'])) {
    if (!checkRememberMe($pdo)) {
        // Tidak ada session dan cookie tidak valid → redirect ke login
        header('Location: ../public/index.php');
        exit;
    }
}

// Optional: Anda bisa tambahkan pembatasan role di sini, contoh:
// if ($_SESSION['role_id'] != 1) {
//     header('HTTP/1.1 403 Forbidden');
//     echo "Akses ditolak.";
//     exit;
// }
