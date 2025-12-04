<?php
// PANGGIL HEADER DAN MENU ADMIN
include 'header&menu_admin.php';
?>


<!-- CONTENT -->
<?php
// Ambil data perusahaan
$stmt = $pdo->query("
    SELECT c.*, u.email_verified, u.admin_verified, u.email 
    FROM companies c
    JOIN users u ON c.user_id = u.id
    ORDER BY c.created_at DESC ");

$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Daftar Perusahaan / Vendor</h4>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table id="dokumenTable" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>Email</th>
                            <th>Telp</th>
                            <th>Kode Member</th>
                            <th>Status Email</th>
                            <th>Status Admin</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($companies as $c): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($c['name']) ?></td>
                                <td><?= htmlspecialchars($c['email']) ?></td>
                                <td><?= htmlspecialchars($c['phone']) ?></td>
                                <td><?= htmlspecialchars($c['code_member']) ?></td>

                                <td>
                                    <?= $c['email_verified']
                                        ? '<span class="badge bg-success">Verified</span>'
                                        : '<span class="badge bg-warning">Pending</span>' ?>
                                </td>

                                <td>
                                    <?= $c['admin_verified']
                                        ? '<span class="badge bg-success">Approved</span>'
                                        : '<span class="badge bg-secondary">Pending</span>' ?>
                                </td>

                                <td>
                                    <?php
                                    // var_dump($row);
                                    // exit;
                                    ?>
                                    <!-- <a href="verifikasi_penyedia.php?company_id=<?= $c['id'] ?>"
                                        class="btn btn-info btn-sm mb-1">
                                        Verifikasi Dokumen
                                    </a> -->
                                    <?php
                                    if (!$c['admin_verified']):
                                    ?>
                                        <a href="verifikasi_penyedia.php?company_id=<?= $c['id'] ?>"
                                            class="btn btn-info btn-sm mb-1">
                                            Verifikasi Dokumen
                                        </a>
                                        <!-- <a href="verify_company.php?id=<?= $c['id'] ?>&action=approve" -->
                                        <!-- class="btn btn-success btn-sm w-100 mb-1">Verifikasi</a> -->
                                        <!-- <a href="verifikasi_document.php?company_id=<?= $c['id'] ?>"
                                            class="btn btn-warning btn-sm mb-1">
                                            Verifikasi Akhir
                                        </a> -->
                                        <!-- 
                                        <a href="verify_company.php?id=<?= $c['id'] ?>&action=reject"
                                            class="btn btn-danger btn-sm w-100">Tolak Keseluruhan</a> -->
                                    <?php else: ?>
                                        <span class="text-muted">Sudah diverifikasi</span>
                                    <?php endif; ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>

<!-- AKHIR CONTENT -->

<?php
include 'footer_admin.php';
?>

<!-- JQuery (WAJIB PALING ATAS sebelum Datatables) -->
<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->

<!-- Bootstrap JS -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

<!-- Datatables JS -->
<!-- <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script> -->
<!-- <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script> -->



<!-- Inisialisasi Datatables -->
<!-- <script>
    $(document).ready(function() {
        $('#vendorTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 20, 50, 100],
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "›",
                    "previous": "‹"
                }
            }
        });
    }); -->
</script>