<?php
// company_identitas.php
// Menampilkan & mengupdate identitas perusahaan (akses user role_id = 3)

// START: Pastikan tidak ada output sebelum redirect => proses POST dulu
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// pastikan user login dan role vendor (role_id = 3)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit;
}
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    // jika bukan vendor, redirect aman ke home
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data perusahaan (jika ada)
$stmt = $pdo->prepare("SELECT * FROM companies WHERE user_id = ?");
$stmt->execute([$user_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);
$is_new = !$company;

// PROSES FORM (POST) — lakukan sebelum include header/menu agar header() bisa dipanggil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // sanitasi dasar
    $ownership   = isset($_POST['ownership']) ? $_POST['ownership'] : 'Swasta';
    $established = $_POST['established'] ?: null;
    $website     = $_POST['website'] ?: null;

    if ($is_new) {
        // generate code_member jika belum ada (atau sesuaikan sesuai kebijakan Anda)
        $generated_code = "UNPBJ-" . date("Ym") . $user_id;

        // ambil user info untuk mengisi nama/email/phone
        $u = $pdo->prepare("SELECT full_name, email, phone FROM users WHERE id = ? LIMIT 1");
        $u->execute([$user_id]);
        $ud = $u->fetch(PDO::FETCH_ASSOC);

        $sql = "INSERT INTO companies 
                (user_id, code_member, name, email, phone, ownership, established, website, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $ins = $pdo->prepare($sql);
        $ins->execute([
            $user_id,
            $generated_code,
            $ud['full_name'] ?? '',
            $ud['email'] ?? '',
            $ud['phone'] ?? '',
            $ownership,
            $established,
            $website
        ]);

        // refresh $company/is_new
        header("Location: company_identitas.php?success=1");
        exit;
    } else {
        // UPDATE
        $sql = "UPDATE companies 
                SET ownership = ?, established = ?, website = ?, updated_at = NOW()
                WHERE user_id = ?";
        $upd = $pdo->prepare($sql);
        $upd->execute([$ownership, $established, $website, $user_id]);

        // Notify
        require_once "../includes/notify.php";

        $companyName = $company['name'] ?? 'Perusahaan';

        createNotification(
            $pdo,
            $_SESSION['user_id'],                // user pembuat
            "update_identitas",                  // type
            "Perusahaan {$companyName} melakukan perubahan data identitas.",
            $company['id'],                      // company_id
            "identitas"                          // section
        );

        // redirect kembali dengan indikator sukses
        header("Location: company_identitas.php?update=1");
        exit;
    }
}

// ------------------------------------------------------------------
// Setelah proses POST selesai (atau tidak ada POST), kita boleh output
// ------------------------------------------------------------------
include 'public_header.php';
include 'public_menu.php';
?>

<style>
    /* Pastikan option select berwarna hitam */
    select.form-select option {
        color: #000 !important;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <!-- <div class="col-md-12 grid-margin stretch-card">
                <div class="card bg-light"> -->

            <div class="container py-1">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white text-center">
                        <h4 class="mb-0">Identitas Perusahaan</h4>
                    </div>
                    <div class="card-body">

                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success">Data berhasil disimpan.</div>
                        <?php endif; ?>

                        <?php if (isset($_GET['update'])): ?>
                            <div class="alert alert-success">Data berhasil diperbarui.</div>
                        <?php endif; ?>

                        <form method="POST" novalidate>

                            <!-- STATUS (badge) -->
                            <?php
                            $status = $company['status'] ?? 'draft';
                            $badge_map = [
                                'draft' => 'secondary',
                                'submitted' => 'primary',
                                'under_review' => 'warning',
                                'verified' => 'success',
                                'rejected' => 'danger'
                            ];
                            $badge_class = $badge_map[$status] ?? 'secondary';
                            ?>
                            <div class="mb-3">
                                <label class="form-label"><strong>Status Verifikasi</strong></label><br>
                                <span class="badge bg-<?= htmlspecialchars($badge_class) ?> px-3 py-2" style="font-size:1rem;">
                                    <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $status))) ?>
                                </span>
                            </div>

                            <!-- NOTES ADMIN (readonly) -->
                            <div class="mb-4">
                                <label class="form-label"><strong>Catatan dari Admin</strong></label>
                                <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($company['notes'] ?? '') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <!-- CODE MEMBER -->
                                    <div class="mb-3">
                                        <label class="form-label">Kode Member</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($company['code_member'] ?? '') ?>" readonly>
                                    </div>

                                    <!-- NAMA PERUSAHAAN -->
                                    <div class="mb-3">
                                        <label class="form-label">Nama Perusahaan</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($company['name'] ?? '') ?>" readonly>
                                    </div>

                                    <!-- EMAIL -->
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="<?= htmlspecialchars($company['email'] ?? '') ?>" readonly>
                                    </div>

                                    <!-- PHONE -->
                                    <div class="mb-3">
                                        <label class="form-label">Telepon</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($company['phone'] ?? '') ?>" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- OWNERSHIP -->
                                    <div class="mb-3">
                                        <label class="form-label">Jenis Kepemilikan</label>
                                        <select name="ownership" class="form-select" required>
                                            <option value="Swasta" <?= (($company['ownership'] ?? '') === 'Swasta') ? 'selected' : '' ?>>Swasta</option>
                                            <option value="Publik" <?= (($company['ownership'] ?? '') === 'Publik') ? 'selected' : '' ?>>Publik</option>
                                        </select>
                                    </div>

                                    <!-- ESTABLISHED -->
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Berdiri</label>
                                        <input type="date" name="established" class="form-control" value="<?= htmlspecialchars($company['established'] ?? '') ?>">
                                    </div>

                                    <!-- WEBSITE -->
                                    <div class="mb-3">
                                        <label class="form-label">Website</label>
                                        <input type="url" name="website" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($company['website'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success px-4">
                                    <?= $is_new ? 'Simpan Identitas' : 'Update Identitas' ?>
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

            <!-- </div>
            </div> -->
        </div>
    </div>

    <?php include 'public_footer.php'; ?>
</div>