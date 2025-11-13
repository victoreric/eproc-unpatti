<?php
session_start();
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// Cek role admin
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    http_response_code(403);
    exit("Akses ditolak.");
}

// Validasi parameter id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    exit("ID dokumen tidak valid.");
}

$doc_id = (int) $_GET['id'];

// Ambil data dokumen
$stmt = $pdo->prepare("SELECT * FROM company_documents WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $doc_id]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doc) {
    http_response_code(404);
    exit("Dokumen tidak ditemukan di database.");
}

// Lokasi penyimpanan file
$storage_root = "/Applications/XAMPP/storage_secure";
$file_path = rtrim($storage_root, '/') . '/' . ltrim($doc['file_path'], '/');

// Validasi file fisik
if (!file_exists($file_path)) {
    http_response_code(404);
    exit("File tidak ditemukan di server. Path: " . htmlspecialchars($file_path));
}

// Bersihkan output buffer (hindari error "headers already sent")
if (ob_get_level()) ob_end_clean();

// Ambil ekstensi file
$ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

// Header dan output file
if ($ext === 'pdf') {
    header("Content-Type: application/pdf");
    header("Content-Disposition: inline; filename=\"" . basename($doc['file_name_orig']) . "\"");
    header("Content-Length: " . filesize($file_path));
    header("Cache-Control: public, must-revalidate, max-age=0");
    header("Pragma: public");
} else {
    // Jika bukan PDF (misal JPG, PNG, DOCX)
    $mime = !empty($doc['mime_type']) ? $doc['mime_type'] : 'application/octet-stream';
    header("Content-Type: $mime");
    header("Content-Disposition: inline; filename=\"" . basename($doc['file_name_orig']) . "\"");
}

// Kirim isi file ke browser
readfile($file_path);
exit;
