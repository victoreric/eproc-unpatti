  <?php
  include 'header&menu_admin.php';
  ?>

  <!-- CONTENT -->
  <div class="container mt-4">
    <div class="alert alert-success shadow-sm">
      <?php

      $company_id = isset($_GET['company_id']) ? (int)$_GET['company_id'] : 0;

      if ($company_id <= 0) {
        die("Parameter company_id tidak valid.");
      }

      // ambil info perusahaan
      $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = :id LIMIT 1");
      $stmt->execute([':id' => $company_id]);
      $company = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$company) {
        die("Perusahaan tidak ditemukan.");
      }

      // query dokumen milik perusahaan ini
      $stmt = $pdo->prepare("
    SELECT 
        d.*, 
        c.name AS company_name,
        u.username
    FROM company_documents d
    JOIN companies c ON d.company_id = c.id
    JOIN users u ON d.uploaded_by = u.id
    WHERE d.company_id = :cid
    ORDER BY d.uploaded_at DESC
");
      $stmt->execute([':cid' => $company_id]);
      $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
      ?>

      <div class="col-12 grid-margin">
        <div class="card">
          <div class="card-body">

            <h3>Verifikasi Dokumen Vendor: <span class="text-primary"><?= htmlspecialchars($company['name']) ?></span></h3>
            <p><strong>Email:</strong> <?= htmlspecialchars($company['email']) ?> &nbsp; | &nbsp;
              <strong>Telepon:</strong> <?= htmlspecialchars($company['phone']) ?>
            </p>

            <div class="table-responsive mt-4">
              <table id="dokumenTable" class="table table-striped table-bordered">
                <thead class="table-dark">
                  <tr>
                    <th>No</th>
                    <th>Jenis Dokumen</th>
                    <th>Nama File</th>
                    <th>Status</th>
                    <th>Upload</th>
                    <th>Vendor</th>
                    <th>Aksi</th>
                  </tr>
                </thead>

                <tbody>
                  <?php if (count($documents) == 0): ?>
                    <tr>
                      <td colspan="7" class="text-center text-muted">Belum ada dokumen diunggah.</td>
                    </tr>
                  <?php else: ?>
                    <?php $no = 1;
                    foreach ($documents as $doc): ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($doc['doc_type']) ?></td>
                        <td><?= htmlspecialchars($doc['file_name_orig']) ?></td>

                        <td>
                          <?php if ($doc['status'] == 'approved'): ?>
                            <span class="badge bg-success">Approved</span>
                          <?php elseif ($doc['status'] == 'rejected'): ?>
                            <span class="badge bg-danger">Rejected</span>
                          <?php else: ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                          <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($doc['uploaded_at']) ?></td>
                        <td><?= htmlspecialchars($doc['username']) ?></td>

                        <td>
                          <a href="view_document_admin.php?id=<?= $doc['id'] ?>"
                            class="btn btn-primary btn-sm" target="_blank">Lihat</a>

                          <a href="verify_document.php?id=<?= $doc['id'] ?>"
                            class="btn btn-warning btn-sm">Verifikasi</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <div class="mt-3">
              <a href="list_perusahaan.php" class="btn btn-secondary">← Kembali ke Daftar Perusahaan</a>
            </div>

          </div>
        </div>
      </div>




    </div>
  </div>






  <?php
  include 'footer_admin.php';
  ?>