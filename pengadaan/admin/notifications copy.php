<?php
// admin/notifications.php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// Pastikan admin
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../index.php");
    exit;
}

// =============== HANDLE: CLEAR NOTIFICATIONS ===============
if (isset($_GET['clear']) && $_GET['clear'] == '1') {
    $pdo->query("DELETE FROM notifications");
    header("Location: notifications.php?msg=cleared");
    exit;
}

include 'header&menu_admin.php';

// Ambil semua notifikasi
$stmt = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tandai dibaca
$pdo->query("UPDATE notifications SET is_read = 1");

$msg = $_GET['msg'] ?? '';
?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Notifikasi</h3>

        <div>
            <?php if ($msg === 'cleared'): ?>
                <span class="badge bg-success me-2">Notifikasi dibersihkan</span>
            <?php endif; ?>

            <a href="notifications.php?clear=1"
                class="btn btn-danger btn-sm"
                onclick="return confirm('Hapus semua notifikasi?')">
                Clear Notifikasi
            </a>
        </div>
    </div>

    <hr>

    <?php if (empty($notifications)): ?>
        <div class="alert alert-info">Belum ada notifikasi.</div>

    <?php else: ?>

        <table id="notifTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Pesan</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($notifications as $row): ?>

                    <?php
                    // ===================== LOGIKA PENENTUAN LINK ======================
                    $company_id = $row['company_id'] ?? null;
                    $section = $row['section'] ?? null;
                    $type = $row['type'] ?? '';

                    // Default link tidak ada
                    $url = "#";

                    // Mapping berdasarkan kolom SECTION
                    $sectionMap = [
                        'identitas'   => 'identitas',
                        'dokumen'     => 'dokumen',
                        'akta'        => 'akta',
                        'pemilik'     => 'pemilik',
                        'jenis_usaha' => 'jenis_usaha'
                    ];

                    // Jika section tersedia & company_id tersedia
                    if (!empty($company_id) && !empty($section) && isset($sectionMap[$section])) {
                        $secID = $sectionMap[$section];
                        $url = "verifikasi_penyedia.php?company_id={$company_id}&tab={$secID}#{$secID}";
                    }

                    // ===================== FALLBACK BERDASARKAN TYPE ======================
                    if ($url === "#" && !empty($company_id)) {

                        if ($type === "update_identitas") {
                            $url = "verifikasi_penyedia.php?company_id={$company_id}&tab=identitas#identitas";
                        }

                        if ($type === "update_dokumen") {
                            $url = "verifikasi_penyedia.php?company_id={$company_id}&tab=dokumen#dokumen";
                        }

                        if ($type === "update_akta") {
                            $url = "verifikasi_penyedia.php?company_id={$company_id}&tab=akta#akta";
                        }

                        // Tambahan type lain bisa ditambah disini
                    }

                    // Jika COMPANY_ID tidak ada (NULL), tetap tampilkan pesan tanpa link
                    $message_html = htmlspecialchars($row['message']);
                    $created_at = $row['created_at'] ? date("d-m-Y H:i", strtotime($row['created_at'])) : '';
                    ?>

                    <tr>
                        <td>
                            <?php if ($url !== "#" && !empty($company_id)): ?>
                                <a href="<?= $url ?>" class="text-decoration-none fw-bold">
                                    <?= $message_html ?>
                                </a>
                            <?php else: ?>
                                <?= $message_html ?>
                            <?php endif; ?>
                        </td>

                        <td><?= $created_at ?></td>
                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>

    <?php endif; ?>

</div>

<?php include 'footer_admin.php'; ?>

<!-- DataTables Script -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#notifTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
            "order": [
                [1, "desc"]
            ]
        });
    });
</script>