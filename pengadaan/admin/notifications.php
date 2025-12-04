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

// Ambil semua notifikasi (terbaru di atas)
$stmt = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tandai semua notifikasi sudah dibaca
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
                    <th>Section</th>
                    <th>Tipe</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>

            <?php foreach ($notifications as $row): ?>

                <?php
                // ===================== AMBIL DATA DASAR ======================
                $company_id = $row['company_id'] ?? null;
                $section    = $row['section']    ?? null;
                $type       = $row['type']       ?? '';
                $message    = $row['message']    ?? '';
                $created_at = $row['created_at'] ?? null;

                // ===================== MAPPING SECTION → TAB ID ======================
                // Tab ID di verifikasi_penyedia.php:
                // identitas, alamat, ijinusaha, akta, keuangan, pajak, pemilik, tenaga
                $sectionMap = [
                    // identitas perusahaan
                    'identitas'    => 'identitas',

                    // alamat perusahaan
                    'alamat'       => 'alamat',

                    // ijin usaha (beberapa kemungkinan nama section)
                    'ijinusaha'    => 'ijinusaha',
                    'ijin_usaha'   => 'ijinusaha',
                    'ijin-usaha'   => 'ijinusaha',

                    // akta perusahaan
                    'akta'         => 'akta',

                    // keuangan
                    'keuangan'     => 'keuangan',

                    // pajak
                    'pajak'        => 'pajak',

                    // pemilik & pengurus
                    'pemilik'      => 'pemilik',
                    'pengurus'     => 'pemilik',

                    // tenaga ahli
                    'tenaga_ahli'  => 'tenaga',
                    'tenaga'       => 'tenaga'
                ];

                // Default: tidak ada link
                $url = "#";

                // 1) Cek berdasarkan SECTION (versi baru pakai section)
                if (!empty($company_id) && !empty($section) && isset($sectionMap[$section])) {
                    $tabId = $sectionMap[$section];
                    $url   = "verifikasi_penyedia.php?company_id={$company_id}&tab={$tabId}#{$tabId}";
                }

                // 2) Fallback berdasarkan TYPE (untuk notifikasi lama yang pakai type = update_*)
                if ($url === "#" && !empty($company_id)) {
                    if ($type === "update_identitas") {
                        $url = "verifikasi_penyedia.php?company_id={$company_id}&tab=identitas#identitas";
                    } elseif ($type === "update_dokumen") {
                        // kalau dulu ada tab 'dokumen', sesuaikan jika sekarang pakai tab lain
                        $url = "verifikasi_penyedia.php?company_id={$company_id}&tab=identitas#identitas";
                    } elseif ($type === "update_akta") {
                        $url = "verifikasi_penyedia.php?company_id={$company_id}&tab=akta#akta";
                    }
                    // Tambah fallback lainnya di sini kalau perlu
                }

                // Format tanggal
                $created_at_fmt = $created_at ? date("d-m-Y H:i", strtotime($created_at)) : '';

                // Safety output
                $message_html = htmlspecialchars($message);
                $section_html = htmlspecialchars($section ?? '-');
                $type_html    = htmlspecialchars($type ?: 'info');
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
                    <td><?= $section_html ?></td>
                    <td><?= $type_html ?></td>
                    <td><?= $created_at_fmt ?></td>
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
                [3, "desc"] // urut berdasarkan kolom tanggal
            ]
        });
    });
</script>
