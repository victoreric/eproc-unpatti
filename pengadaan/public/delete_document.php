<?php
session_start();
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// Hanya vendor (role_id = 3)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil company_id vendor
$stmt = $pdo->prepare("SELECT id FROM companies WHERE user_id = :uid LIMIT 1");
$stmt->execute([':uid' => $user_id]);
$company_id = $stmt->fetchColumn();

if (!$company_id) {
    // Mengembalikan ke halaman upload dengan pesan error
    header("Location: upload_documents.php?del=error&msg=" . urlencode("Company ID tidak ditemukan"));
    exit;
}

// Validasi id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: upload_documents.php?del=error&msg=" . urlencode("ID dokumen tidak valid"));
    exit;
}

$doc_id = (int) $_GET['id'];

// Ambil info dokumen, pastikan milik company ini
$stmt = $pdo->prepare("SELECT * FROM company_documents WHERE id = :id AND company_id = :cid LIMIT 1");
$stmt->execute([':id' => $doc_id, ':cid' => $company_id]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doc) {
    header("Location: upload_documents.php?del=error&msg=" . urlencode("Dokumen tidak ditemukan atau bukan milik Anda."));
    exit;
}

// Hitung absolute path file di server.
// Karena di DB Anda menyimpan 'eproc_uploads/company_docs/..' (tanpa storage_secure prefix),
// kita perlu menambahkan prefix storage_secure sesuai lokasi penyimpanan.
$storage_root = realpath(__DIR__ . "/../../../../../storage_secure"); // ubah jika lokasi storage berbeda

if ($storage_root === false) {
    // Jika storage root tidak ditemukan, coba alternatif relatif (toleransi)
    $possible_path = __DIR__ . "/../../storage_secure/" . ltrim($doc['file_path'], '/');
    $server_file = realpath($possible_path);
} else {
    $server_file = realpath($storage_root . '/' . ltrim($doc['file_path'], '/'));
}

// Jika realpath gagal (false), coba membangun manual path tanpa realpath
if (!$server_file) {
    $manual = __DIR__ . "/../../../../../storage_secure/" . ltrim($doc['file_path'], '/');
    $server_file = $manual;
}

// Hapus file fisik jika ada
$deleted_file = false;
if ($server_file && file_exists($server_file)) {
    // Pastikan file berada di dalam storage_secure untuk menghindari path traversal
    $storage_real = realpath(__DIR__ . "/../../../../../storage_secure");
    if ($storage_real && strpos(realpath($server_file), $storage_real) === 0) {
        if (@unlink($server_file)) {
            $deleted_file = true;
        }
    } else {
        // Jika tidak dapat memastikan lokasi, kita tidak menghapus untuk keamanan
        // Set deleted_file tetap false
    }
}

// Hapus record DB
$stmt = $pdo->prepare("DELETE FROM company_documents WHERE id = :id AND company_id = :cid");
$stmt->execute([':id' => $doc_id, ':cid' => $company_id]);

// Redirect kembali ke halaman upload dengan pesan
if ($deleted_file) {
    header("Location: upload_documents.php?del=success");
} else {
    // File mungkin sudah terhapus sebelumnya atau tidak dapat dihapus; tetap redirect sukses DB
    header("Location: upload_documents.php?del=success_file_missing");
}
exit;
