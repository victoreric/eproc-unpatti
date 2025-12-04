<?php
// verifikasi.php (ADMIN - verifikasi detail perusahaan)
// Pastikan file ini tidak mengeluarkan output sebelum header() dipanggil.

require_once __DIR__ . '/../includes/auth_check.php'; // cek session & role
require_once __DIR__ . '/../config/db.php';

// OPTIONAL: jika Anda sering mengalami "headers already sent" karena includes lain,
// Anda bisa aktifkan output buffering. Biasanya tidak diperlukan jika struktur benar.
// ob_start();

// validasi id perusahaan (GET)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // ke halaman list (verifikasi identitas)
    header("Location: verifikasi_company_identitas.php?msg=" . urlencode("ID perusahaan tidak valid") . "&type=danger");
    exit;
}

$company_id = (int) $_GET['id'];

// ----- PROSES POST (Update Verifikasi) -----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil input dengan sanitasi dasar
    $new_status = $_POST['status'] ?? '';
    $notes = $_POST['notes'] ?? '';

    // Validasi status
    $allowed = ['draft', 'submitted', 'under_review', 'verified', 'rejected'];
    if (!in_array($new_status, $allowed, true)) {
        // invalid status -> redirect kembali dengan error
        header("Location: verifikasi_company_identitas.php?msg=" . urlencode("Status tidak valid") . "&type=danger");
        exit;
    }

    // Update record companies
    $update = $pdo->prepare("UPDATE companies 
                             SET status = :status, notes = :notes, verified_by = :verified_by, verified_at = NOW(), updated_at = NOW()
                             WHERE id = :id");
    $update->execute([
        ':status' => $new_status,
        ':notes' => $notes,
        ':verified_by' => $_SESSION['user_id'],
        ':id' => $company_id
    ]);

    // Redirect kembali ke daftar dengan pesan sukses
    header("Location: verifikasi_company_identitas.php?msg=" . urlencode("Status perusahaan berhasil diperbarui") . "&type=success");
    exit;
}

// ----- AMBIL DATA PERUSAHAAN untuk ditampilkan -----
// lakukan SELECT setelah POST handling (karena POST mungkin redirect/exit)
$stmt = $pdo->prepare("SELECT c.*, u.username, u.email AS user_email
                       FROM companies c
                       LEFT JOIN users u ON c.user_id = u.id
                       WHERE c.id = :id
                       LIMIT 1");
$stmt->execute([':id' => $company_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    // tidak ditemukan
    header("Location: verifikasi_company_identitas.php?msg=" . urlencode("Perusahaan tidak ditemukan") . "&type=danger");
    exit;
}

// --- sekarang aman untuk include tampilan (header/menu) karena tidak ada lagi redirect pending ---
include 'header&menu_admin.php';
?>

<!-- START HTML -->
<div class="container main-panel">
    <div class="content-wrapper">
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">Verifikasi Identitas Perusahaan — <?= htmlspecialchars($company['name']) ?></h4>
            </div>

            <div class="card-body">
                <!-- Breadcrumb / kembali -->
                <a href="verifikasi_company_identitas.php" class="btn btn-secondary btn-sm mb-3">← Kembali ke daftar</a>

                <!-- Info dan status -->
                <div class="mb-3">
                    <label class="form-label"><strong>Status Saat Ini</strong></label>
                    <?php
                    $status = $company['status'] ?? 'draft';
                    $badge = 'secondary';
                    switch ($status) {
                        case 'draft':
                            $badge = 'secondary';
                            break;
                        case 'submitted':
                            $badge = 'warning';
                            break;
                        case 'under_review':
                            $badge = 'info';
                            break;
                        case 'verified':
                            $badge = 'success';
                            break;
                        case 'rejected':
                            $badge = 'danger';
                            break;
                    }
                    ?>
                    <div><span class="badge bg-<?= $badge ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $status))) ?></span></div>
                </div>

                <!-- CATATAN ADMIN (readonly) -->
                <div class="mb-4">
                    <label class="form-label"><strong>Catatan Admin</strong></label>
                    <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($company['notes'] ?? '') ?></textarea>
                </div>

                <!-- Form verifikasi (admin dapat ubah status & notes) -->
                <form method="POST" class="mb-4">
                    <div class="mb-3">
                        <label class="form-label"><strong>Ubah Status Verifikasi</strong></label>
                        <select name="status" class="form-select" required>
                            <option value="draft" <?= $company['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="submitted" <?= $company['status'] == 'submitted' ? 'selected' : '' ?>>Submitted</option>
                            <option value="under_review" <?= $company['status'] == 'under_review' ? 'selected' : '' ?>>Under Review</option>
                            <option value="verified" <?= $company['status'] == 'verified' ? 'selected' : '' ?>>Verified</option>
                            <option value="rejected" <?= $company['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Catatan (opsional)</strong></label>
                        <textarea name="notes" class="form-control" rows="4"><?= htmlspecialchars($company['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success">Simpan Verifikasi</button>
                    </div>
                </form>

                <hr>

                <!-- Tampilkan identitas perusahaan (readonly) - two-column -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Kode Member</label>
                            <input class="form-control" value="<?= htmlspecialchars($company['code_member'] ?? '') ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Perusahaan</label>
                            <input class="form-control" value="<?= htmlspecialchars($company['name'] ?? '') ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input class="form-control" value="<?= htmlspecialchars($company['email'] ?? ($company['user_email'] ?? '')) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Telepon</label>
                            <input class="form-control" value="<?= htmlspecialchars($company['phone'] ?? '') ?>" readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Kepemilikan</label>
                            <input class="form-control" value="<?= htmlspecialchars($company['ownership'] ?? '') ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Berdiri</label>
                            <input class="form-control" value="<?= htmlspecialchars($company['established'] ?? '') ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input class="form-control" value="<?= htmlspecialchars($company['website'] ?? '') ?>" readonly>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>

<?php
// Jika Anda menggunakan ob_start() sebelumnya, Anda bisa flush di sini:
// ob_end_flush();
?>