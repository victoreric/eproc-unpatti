<?php
session_start();
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// cek admin
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    die("Akses ditolak.");
}

// validasi id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid.");
}

$doc_id = (int) $_GET['id'];

// ambil data dokumen
$stmt = $pdo->prepare("SELECT * FROM company_documents WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $doc_id]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doc) {
    die("Dokumen tidak ditemukan.");
}

// lokasi file disimpan
// $storage_root = realpath(__DIR__ . "/../../../storage_secure");
$storage_root = "/Applications/XAMPP/storage_secure";

$file_path = $storage_root . "/" . $doc['file_path'];

if (!file_exists($file_path)) {
    die("File tidak ditemukan di server.");
}

// header untuk PDF
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"" . $doc['file_name_orig'] . "\"");
header("Content-Length: " . filesize($file_path));

readfile($file_path);
exit;
