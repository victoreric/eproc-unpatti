 <!-- PANGGIL MENU.PHP -->
 <?php include 'menu_public.php'; ?>

 <!-- content -->

 <!-- <div class="row">
     <div class="col-md-12 grid-margin">
         <div class="d-flex justify-content-between flex-wrap">
             <div class="d-flex align-items-end flex-wrap">
                 <div class="me-md-3 me-xl-5">
                     <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?></h2>
                     <p>Status akun Anda: <span class="text-warning">Belum Diverifikasi</span></p>

                     <div class="card shadow-sm">

                     </div>
                 </div>

             </div>

         </div>
     </div>
 </div> -->

 <div class="col-12 grid-margin">
     <div class="card">
         <div class="card-body">
             <div class="container mt-4">

                 <?php if ($success_message): ?>
                     <div class="alert alert-success"><?= $success_message ?></div>
                 <?php endif; ?>
                 <?php if ($error_message): ?>
                     <div class="alert alert-danger"><?= $error_message ?></div>
                 <?php endif; ?>

                 <div class="card shadow">
                     <div class="card-header bg-primary text-white">
                         <h4 class="mb-0">Upload Dokumen Legal Perusahaan</h4>
                     </div>
                     <div class="card-body">
                         <form method="POST" enctype="multipart/form-data">
                             <div class="mb-3">
                                 <label>Jenis Dokumen</label>
                                 <select name="doc_type" class="form-select" required>
                                     <option value="">-- Pilih --</option>
                                     <option value="SIUP">SIUP</option>
                                     <option value="NPWP">NPWP</option>
                                     <option value="Akta">Akta Perusahaan</option>
                                     <option value="Lainnya">Lainnya</option>
                                 </select>
                             </div>

                             <div class="mb-3">
                                 <label>File</label>
                                 <input type="file" name="doc_file" class="form-control" required>
                                 <small class="text-muted">Hanya PDF/JPG/PNG, maksimal 5MB.</small>
                             </div>

                             <button type="submit" name="upload" class="btn btn-success">Upload</button>
                         </form>

                         <hr>

                         <h5>Dokumen Tersimpan</h5>
                         <table class="table table-bordered table-striped mt-2">
                             <thead class="table-dark">
                                 <tr>
                                     <th>No</th>
                                     <th>Jenis Dokumen</th>
                                     <th>File</th>
                                     <th>Tanggal Upload</th>
                                     <th>Aksi</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php $no = 1;
                                    foreach ($documents as $doc): ?>
                                     <tr>
                                         <td><?= $no++ ?></td>
                                         <td><?= htmlspecialchars($doc['type']) ?></td>
                                         <td><?= htmlspecialchars($doc['filename']) ?></td>
                                         <td><?= $doc['uploaded_at'] ?></td>
                                         <td>
                                             <a href="../uploads/<?= $user_id ?>/<?= $doc['filename'] ?>" class="btn btn-primary btn-sm" target="_blank">Lihat</a>
                                             <a href="delete_document.php?id=<?= $doc['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus dokumen ini?')">Hapus</a>
                                         </td>
                                     </tr>
                                 <?php endforeach; ?>
                             </tbody>
                         </table>

                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>



 <!-- End content -->

 <!-- PANGGIL footer.PHP -->
 <?php include 'footer.php'; ?>