<?php
session_start();
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// hanya vendor
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    http_response_code(403);
    exit("Akses ditolak.");
}

$user_id = $_SESSION['user_id'];

// ambil company_id vendor
$stmt = $pdo->prepare("SELECT id FROM companies WHERE user_id = :uid LIMIT 1");
$stmt->execute([':uid' => $user_id]);
$company_id = $stmt->fetchColumn();

if (!$company_id) {
    exit("Company ID tidak ditemukan.");
}

// validasi id dokumen
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("ID tidak valid.");
}

$doc_id = (int) $_GET['id'];

// ambil data dokumen
$stmt = $pdo->prepare("
    SELECT * FROM company_documents 
    WHERE id = :id AND company_id = :cid LIMIT 1
");
$stmt->execute([':id' => $doc_id, ':cid' => $company_id]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doc) {
    exit("Dokumen tidak ditemukan atau bukan milik Anda.");
}

// tentukan lokasi file sebenarnya (storage_secure)
$storage_file = __DIR__ . "/../../../../../storage_secure/" . ltrim($doc['file_path'], '/');

// cek file fisik
if (!file_exists($storage_file)) {
    http_response_code(404);
    exit("File tidak ditemukan pada server.");
}

// kirim header file PDF
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"" . basename($doc['file_name_orig']) . "\"");
header("Content-Length: " . filesize($storage_file));

readfile($storage_file);
exit;
