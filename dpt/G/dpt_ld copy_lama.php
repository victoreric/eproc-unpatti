<?php
include 'menu.php';
include '../link.php';
?>

<div>
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="index"><i class="fas fa-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">DPT</a></li>
    <li class="breadcrumb-item"><a href="dpt_ld">Lihat Data</a></li>
  </ul>
</div>

<div class="container">
<div class="card mb-5">
  <div class="card-header bg-primary text-white text-center h6"> Data Perusahaan
  </div>

  <div class="card-body table-responsive">
    <?php
    $id_vendor=$_SESSION['id_vendor'];
    $queri="SELECT * FROM data_vendor WHERE id_vendor='$id_vendor'";
    $sql=mysqli_query($conn, $queri);
    $hasil=mysqli_fetch_assoc($sql);
    $cek=mysqli_num_rows($sql);
    if($cek!=1){
        echo "Belum ada data perusahaan";
        echo "<p>";
        echo "<a href='dpt_ld_i' class='btn btn-lg btn-primary' role='button'>Tambahkan data perusahaan</a>";
    }else{
    ?>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="identitas-tab" data-toggle="tab" data-target="#identitas" type="button" role="tab" aria-controls="identitas" aria-selected="true">Identitas Perusahaan</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="alamat-tab" data-toggle="tab" data-target="#alamat" type="button" role="tab" aria-controls="alamat" aria-selected="false">Alamat</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pengurus-tab" data-toggle="tab" data-target="#pengurus" type="button" role="tab" aria-controls="pengurus" aria-selected="false">Pengurus</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="rekening-tab" data-toggle="tab" data-target="#rekening" type="button" role="tab" aria-controls="rekening" aria-selected="false">Rekening</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pajak-tab" data-toggle="tab" data-target="#pajak" type="button" role="tab" aria-controls="pajak" aria-selected="false">Pajak</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="ijin-tab" data-toggle="tab" data-target="#ijin" type="button" role="tab" aria-controls="ijin" aria-selected="false">Ijin Usaha</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tenagakerja-tab" data-toggle="tab" data-target="#tenagakerja" type="button" role="tab" aria-controls="tenagakerja" aria-selected="false">Tenaga Kerja</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="Keuangan-tab" data-toggle="tab" data-target="#Keuangan" type="button" role="tab" aria-controls="Keuangan" aria-selected="false">Keuangan</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pelengkap-tab" data-toggle="tab" data-target="#pelengkap" type="button" role="tab" aria-controls="pelengkap" aria-selected="false">Dokumen Pelengkap</button>
    </li>
    </ul>

    <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="identitas" role="tabpanel" aria-labelledby="identitas-tab">

    <table class="table table-hover table-bordered mt-3">
    <tr>
        <th>ID Anggota</th>
        <th><?php echo $hasil['id_vendor'] ?></th>
    </tr>
    <tr>
        <th>Nama awal</th>
        <th><?php echo $hasil['nama_awal'] ?></th>
    </tr>
    <tr>
        <th>Nama Perusahaan</th>
        <th><?php echo $hasil['nama_vendor'] ?></th>
    </tr>
    <tr>
        <th>Nama akhir</th>
        <th><?php echo $hasil['nama_akhir'] ?></th>
    </tr>
    <tr>
        <th>Tanggal berdiri</th>
        <th><?php echo $hasil['tanggal_berdiri'] ?></th>
    </tr>
    <tr>
        <th>Kepemilikan</th>
        <th><?php echo $hasil['kepemilikan'] ?></th>
    </tr> 
    <tr>
        <th>Nomor Telepon</th>
        <th><?php echo $hasil['telp'] ?></th>
    </tr>
    <tr>
        <th>Website</th>
        <th><?php echo $hasil['website'] ?></th>
    </tr>

    <tr>
        <th>Akta Pendirian Perusahaan</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_akta'] ?>'target=blank><?php echo $hasil['file_akta'] ?>  </a>
        </th>
    </tr>

    <tr>
        <th>Kop Surat Perusahaan</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_kop_surat'] ?>'target=blank><?php echo $hasil['file_kop_surat'] ?>  </a>
        </th>
    </tr>

    <tr>
        <th>Daftar Pengalaman</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_pengalaman'] ?>'target=blank><?php echo $hasil['file_pengalaman'] ?> </a>
        </th>  
    </tr>  
    </table>
        <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=identitas&id= <?php echo $hasil['id_data'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="alamat" role="tabpanel" aria-labelledby="alamat-tab">
    <table class="table table-hover table-hover table-bordered mt-3">
        <?php 
        $queri="SELECT * FROM alamat WHERE id_vendor='$id_vendor'";
        $sql=mysqli_query($conn, $queri);
        $hasil=mysqli_fetch_assoc($sql);
        ?> 
    <tr>
        <th>Provinsi</th>
        <th><?php echo $hasil['provinsi'] ?></th>
    </tr>
    <tr>
        <th>Kota/ Kabupaten</th>
        <th><?php echo $hasil['kota'] ?></th>
    </tr>
    <tr>
        <th>Kecamatan</th>
        <th><?php echo $hasil['kecamatan'] ?></th>
    </tr>
    <tr>
        <th>Kelurahan</th>
        <th><?php echo $hasil['kelurahan'] ?></th>
    </tr>
    <tr>
        <th>Jalan</th>
        <th><?php echo $hasil['jalan'] ?></th>
    </tr>
    <tr>
        <th>Kode Pos</th>
        <th><?php echo $hasil['kodepos'] ?></th>
    </tr>

    <tr>
        <th>Surat Keterangan Domisili</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_domisili'] ?>'target=blank><?php echo $hasil['file_domisili'] ?> </a>
    </th>     
    </table>
    <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=alamat&id= <?php echo $hasil['id_alamat'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pengurus" role="tabpanel" aria-labelledby="pengurus-tab">
    <table class="table table-hover table-bordered mt-3">
    <?php
        $queri="SELECT * FROM pengurus WHERE id_vendor='$id_vendor'";
        $sql=mysqli_query($conn, $queri);
        $hasil=mysqli_fetch_assoc($sql);
    ?>
    <tr>
        <th>Nama Pemilik</th>
        <th><?php echo $hasil['pemilik'] ?></th>
    </tr>
    <tr>
        <th>Jenis Identitas Pemilik</th>
        <th><?php echo $hasil['kartu_pemilik'] ?></th>
    </tr>
    <tr>
        <th>No. Identitas Pemilik</th>
        <th><?php echo $hasil['no_kartu_pemilik'] ?></th>
    </tr>
    <tr>
        <th>Identitas Pemilik</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_kartu_pemilik'] ?>' target=blank><?php echo $hasil['file_kartu_pemilik'] ?> </a>
        </th>
    </tr> 
    
    <tr>
        <th>Nama Direktur</th>
        <th><?php echo $hasil['direktur'] ?></th>
    </tr>
    <tr>
        <th>Jenis Identitas Direktur</th>
        <th><?php echo $hasil['kartu_direktur'] ?></th>
    </tr>
    <tr>
        <th>No. Identitas Direktur</th>
        <th><?php echo $hasil['no_kartu_direktur'] ?></th>
    </tr>

    <tr>
        <th>Identitas Direksi</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_kartu_direktur'] ?>' target=blank><?php echo $hasil['file_kartu_direktur'] ?> </a>
    </th>
    </tr>          
    </table>
        <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=pengurus&id= <?php echo $hasil['id_pengurus'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
    </div>


    <div class="tab-pane fade" id="rekening" role="tabpanel" aria-labelledby="rekening-tab">
    <table class="table table-hover table-bordered mt-3">
    <?php
    $queri="SELECT * FROM rekening WHERE id_vendor='$id_vendor'";
    $sql=mysqli_query($conn, $queri);
    $cek=mysqli_num_rows($sql);
    $hasil=mysqli_fetch_assoc($sql);
    if($cek!=1){
        echo "belum ada data";
    }else {
    ?> 
    <tr>
        <th>Nomor Rekening Perusahaan</th>
        <th><?php echo $hasil['no_rekening'] ?></th>
    </tr>
    <tr>    
        <th>Pemilik Rekening</th>
        <th><?php echo $hasil['pemilik_rekening'] ?></th>
    </tr>
    <tr>
        <th>Nama Bank</th>
        <th><?php echo $hasil['nama_bank'] ?></th>
    </tr>
    <tr>
        <th>Cabang bank</th>
        <th><?php echo $hasil['cabang_bank'] ?></th>
    </tr>

    <tr>
        <th>Fotocopy Buku Rekening</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_buku_rekening'] ?>' target=blank><?php echo $hasil['file_buku_rekening'] ?> </a>
        </th>
    </tr>

    <tr>
        <th>Fotocopy Rekening Koran 3 Bulan Terakhir</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_rek_koran'] ?>' target=blank><?php echo $hasil['file_rek_koran'] ?> </a>
        </th>
    </tr>
    <?php } ?>
    </table>
        <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=rekening&id= <?php echo $hasil['id_rekening'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pajak" role="tabpanel" aria-labelledby="pajak-tab">
    <table class="table table-hover table-bordered mt-3">
    <?php
    $queri="SELECT * FROM pajak WHERE id_vendor='$id_vendor'";
    $sql=mysqli_query($conn, $queri);
    $hasil=mysqli_fetch_assoc($sql);
    ?>   
    <tr>
        <th>NPWP Perusahaan</th>
        <th><?php echo $hasil['npwp_vendor'] ?></th>
    </tr>
    <tr>
        <th>Fotocopy NPWP Perusahaan</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_npwp_vendor'] ?>' target=blank><?php echo $hasil['file_npwp_vendor'] ?> </a>
        </th>
    </tr>
    <tr>
        <th>NPWP Direktur</th>
        <th><?php echo $hasil['npwp_direktur'] ?></th>
    </tr>
    <tr>
        <th>Fotocopy NPWP Direktur</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_npwp_direktur'] ?>' target=blank><?php echo $hasil['file_npwp_direktur'] ?> </a>
        </th>
    </tr>
    <tr>
        <th>Fotocopy Tanda Daftar Pajak</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_tanda_daftar'] ?>' target=blank><?php echo $hasil['file_tanda_daftar'] ?> </a>
        </th>
    </tr>
    <tr>
        <th>Fotocopy Lapor Pajak</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_lapor_pajak'] ?>' target=blank><?php echo $hasil['file_lapor_pajak'] ?> </a>
        </th>
    </tr>
    </table>
    <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=pajak&id= <?php echo $hasil['id_pajak'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
    </div>
    
    <div class="tab-pane fade" id="ijin" role="tabpanel" aria-labelledby="ijin-tab">
    <table class="table table-hover table-bordered mt-3">
    <?php
    $queri="SELECT * FROM ijin_usaha WHERE id_vendor='$id_vendor'";
    $sql=mysqli_query($conn, $queri);
    $hasil=mysqli_fetch_assoc($sql);
    ?>
    <tr>
        <th>Kualifikasi Usaha</th>
        <th><?php echo $hasil['kualifikasi_usaha'] ?></th>
    </tr>
    <tr>
        <th>Kualifikasi Pengadaan</th>
        <th><?php echo $hasil['kualifikasi_pengadaan'] ?></th>
    </tr>
    <tr>
        <th>Nomor PKP</th>
        <th><?php echo $hasil['pkp'] ?></th>
    </tr>
    <tr>
        <th>NIB</th>
        <th><?php echo $hasil['nib'] ?></th>
    </tr>
    <tr>
        <th>Fotocopy Ijin Usaha</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_ijin_usaha'] ?>'target=blank><?php echo $hasil['file_ijin_usaha'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Fotocopy NIB</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_nib'] ?>'target=blank><?php echo $hasil['file_nib'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Fotocopy Sertifikat Badan Usaha</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_sert_badan_usaha'] ?>'target=blank><?php echo $hasil['file_sert_badan_usaha'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Fotocopy SKA Konstruksi</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_ska_konstruksi'] ?>'target=blank><?php echo $hasil['file_ska_konstruksi'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Fotocopy SKT Konstruksi</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_skt_konstruksi'] ?>'target=blank><?php echo $hasil['file_skt_konstruksi'] ?>  </a>
        </th>
    </tr>
    </table>
        <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=ijinusaha&id= <?php echo $hasil['id_ijin'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tenagakerja" role="tabpanel" aria-labelledby="tenagakerja-tab">
    <table class="table table-hover table-bordered mt-3">
    <?php
    $queri="SELECT * FROM tenagakerja WHERE id_vendor='$id_vendor'";
    $sql=mysqli_query($conn, $queri);
    $hasil=mysqli_fetch_assoc($sql);
    ?>
    <tr>
        <th>Dokumen Tenaga Teknis</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_dok_teknik'] ?>'target=blank><?php echo $hasil['file_dok_teknik'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Kumpulan KTP Tenaga Teknis</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_ktp'] ?>'target=blank><?php echo $hasil['file_ktp'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Kumpulan SKT Tenaga Teknis</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_skt'] ?>'target=blank><?php echo $hasil['file_skt'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Kumpulan Ijazah Tenaga Teknis</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_ijazah'] ?>'target=blank><?php echo $hasil['file_ijazah'] ?>  </a>
        </th>
    </tr>
    </table>
    <div class="form-group row">
        <div class="mx-auto">
        <a href='dpt_u?aksi=tenagakerja&id= <?php echo $hasil['id_tenagakerja'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
        </div>
    </div>
    </div>

    <div class="tab-pane fade" id="Keuangan" role="tabpanel" aria-labelledby="Keuangan-tab">
    <table class="table table-hover table-bordered mt-3">
    <?php
    $queri="SELECT * FROM keuangan WHERE id_vendor='$id_vendor'";
    $sql=mysqli_query($conn, $queri);
    $hasil=mysqli_fetch_assoc($sql);
    ?>
    <tr>
        <th>Laporan Neraca Perusahaan</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_neraca'] ?>'target=blank><?php echo $hasil['file_neraca'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Laporan Laba Rugi Perusahaan</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_labarugi'] ?>'target=blank><?php echo $hasil['file_labarugi'] ?>  </a>
        </th>
    </tr>
    </table>
        <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=keuangan&id= <?php echo $hasil['id_keuangan'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pelengkap" role="tabpanel" aria-labelledby="pelengkap-tab">
    <table class="table table-hover table-bordered mt-3"> 
    <?php
    $queri="SELECT * FROM dokumen WHERE id_vendor='$id_vendor'";
    $sql=mysqli_query($conn, $queri);
    $hasil=mysqli_fetch_assoc($sql);
    ?>
    <tr>
        <th>Formulir Keiikutsertaan</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_ikutserta'] ?>'target=blank > <?php echo $hasil['file_ikutserta'] ?>  &nbsp &nbsp
        <span class="glyphicon glyphicon-eye-open"></span></a>
    </th>
    </tr>
    
    <tr>
        <th>Surat Kuasa</th>
        <th>
            <a href='dpt_ld_v?id=<?php echo $hasil['file_suratkuasa'] ?>'target=blank ><?php echo $hasil['file_suratkuasa'] ?>  &nbsp &nbsp <span class='glyphicon glyphicon-eye-open'></span> </a>
    </th>
    </tr>
    
    <tr>
        <th>Formulir Pendaftaran</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasil['file_pendaftaran'] ?>'target=blank><?php echo $hasil['file_pendaftaran'] ?></a>
    </th>
    </tr>                
    </table>
    <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=dokumen&id= <?php echo $hasil['id_dokumen'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
    </div>
</div>
        
<?php } ?>

</div>
</div>
</div>

<?php
include '../footer.php';
?>