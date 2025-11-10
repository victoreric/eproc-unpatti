<?php
include 'menuA.php';
include '../link.php';
?>

<div class="small">
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="index"><i class="fas fa-home"></i></a></li>
    <li class="breadcrumb-item"><a href="ld_dpt">DPT</a></li>
    <li class="breadcrumb-item"><a href="#">Detail Data</a></li>
  </ul>
</div>

<div class="container-fluid small"> 
<div class="card mb-5">
  <div class="card-header bg-primary text-white text-center h6"> Data Administrasi Perusahaan
  </div>

  
    <div class="card-body table-responsive">

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
            <button class="nav-link" id="Dokumen-tab" data-toggle="tab" data-target="#Dokumen" type="button" role="tab" aria-controls="Dokumen" aria-selected="false">Dokumen Pelengkap</button>
        </li>
        </ul>

<!-- data perusahaan -->
<?php 
    // echo $_GET['id'];
    $a=$_GET['id'];
    $queri="SELECT * FROM data_vendor WHERE id_vendor='$a';";
    $sql=mysqli_query($conn,$queri);
    $hasil=mysqli_fetch_assoc($sql);
    $cekhasil=mysqli_num_rows($sql);
    ?>
        <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="identitas" role="tabpanel" aria-labelledby="identitas-tab">

        <?php
           if($cekhasil!=1){
                echo "<th>Data belum diisi oleh Perusahaan</th>";
                } 
            else {
            ?>

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
                    <th>Website</th>
                    <th><?php echo $hasil['website'] ?></th>
                </tr>
                <tr>
                    <th>Akta Pendirian</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasil['file_akta'] ?>'target=blank><?php echo $hasil['file_akta'] ?>  </a>
        </th>
                </tr>
                <th>KOP Surat Perusahaan</th>
                <th><a href='ld_dpt_v?id=<?php echo $hasil['file_kop_surat'] ?>'target=blank><?php echo $hasil['file_kop_surat'] ?>  </a>
        </th>
                </tr>
                <tr>
                    <th>Daftar Pengalaman Perusahaan</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasil['file_pengalaman'] ?>'target=blank><?php echo $hasil['file_pengalaman'] ?> </a>
                </tr>
        </table>
        <?php } ?>
        </div>

<!-- alamatVendor -->
    <?php 
        $a=$_GET['id'];
        $queriAlamat="SELECT * FROM alamat WHERE id_vendor='$a'";
        $sqlAlamat=mysqli_query($conn, $queriAlamat);
        $hasilAlamat=mysqli_fetch_assoc($sqlAlamat);
        $cekAlamat=mysqli_num_rows($sqlAlamat);
    ?> 
        <div class="tab-pane fade" id="alamat" role="tabpanel" aria-labelledby="alamat-tab">

        <?php
           if($cekAlamat!=1){
                echo "<th>Data belum diisi oleh Perusahaan</th>";
                } 
            else {
            ?>

        <table class="table table-hover table-hover table-bordered mt-3">
            <tr>
                <th>Provinsi</th>
                <th><?php echo $hasilAlamat['provinsi'] ?></th>
            </tr>
            <tr>
                <th>Kota/ Kabupaten</th>
                <th><?php echo $hasilAlamat['kota'] ?></th>
            </tr>
            <tr>
                <th>Kecamatan</th>
                <th><?php echo $hasilAlamat['kecamatan'] ?></th>
            </tr>
            <tr>
                    <th>Kelurahan</th>
                    <th><?php echo $hasilAlamat['kelurahan'] ?></th>
                </tr>
                <tr>
                    <th>Jalan</th>
                    <th><?php echo $hasilAlamat['jalan'] ?></th>
                </tr>
                <tr>
                    <th>Kode Pos</th>
                    <th><?php echo $hasilAlamat['kodepos'] ?></th>
                </tr>
                <tr>
                    <th>Surat Keterangan Domisili</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilAlamat['file_domisili'] ?>'target=blank><?php echo $hasilAlamat['file_domisili'] ?>  </a>
                    </th>
                </tr>
        </table>
        <?php } ?>
        </div>

<!-- Pengurus -->
    <?php 
        $a=$_GET['id'];
        $queriPengurus="SELECT * FROM pengurus WHERE id_vendor='$a'";
        $sqlPengurus=mysqli_query($conn, $queriPengurus);
        $hasilPengurus=mysqli_fetch_assoc($sqlPengurus);
        $cekPengurus=mysqli_num_rows($sqlPengurus);
    ?>

<div class="tab-pane fade" id="pengurus" role="tabpanel" aria-labelledby="pengurus-tab">
        <?php
           if($cekPengurus!=1){
                echo "<th>Data belum diisi oleh Perusahaan</th>";
                } 
            else {
            ?>

    <table class="table table-hover table-bordered mt-3">
        <tr>
            <th>Pemilik Perusahaan</th>
            <th><?php echo $hasilPengurus['pemilik'] ?></th>
        </tr>

        <tr>    
            <th>kartu_pemilik </th>
            <th><?php echo $hasilPengurus['kartu_pemilik'] ?></th>
        </tr>

        <tr>
            <th>no_kartu_pemilik</th>
            <th><?php echo $hasilPengurus['no_kartu_pemilik'] ?></th>
        </tr>

        <tr>
            <th>direktur</th>
            <th><?php echo $hasilPengurus['direktur'] ?></th>
        </tr>

        <tr>
            <th>kartu_direktur</th>
            <th><?php echo $hasilPengurus['kartu_direktur'] ?></th>
        </tr>

        <tr>
            <th>no_kartu_direktur</th>
            <th><?php echo $hasilPengurus['no_kartu_direktur'] ?></th>
        </tr>

        <tr>
            <th>file_kartu_pemilik</th>
            <th><a href='ld_dpt_v?id=<?php echo $hasilPengurus['file_kartu_pemilik'] ?>'target=blank><?php echo $hasilPengurus['file_kartu_pemilik'] ?>  </a>
            </th>
        </tr>

        <tr>
            <th>file_kartu_direktur</th>
            <th><a href='ld_dpt_v?id=<?php echo $hasilPengurus['file_kartu_direktur'] ?>'target=blank><?php echo $hasilPengurus['file_kartu_direktur'] ?>  </a>
            </th>
        </tr>

        </table>
    <?php
    }
    ?>
</div>


<!-- Rekening -->
    <?php 
        $a=$_GET['id'];
        $queri="SELECT * FROM rekening WHERE id_vendor='$a'";
        $sql=mysqli_query($conn, $queri);
        $hasilRekening=mysqli_fetch_assoc($sql);
        $cekRekening=mysqli_num_rows($sql);
    ?>
        <div class="tab-pane fade" id="rekening" role="tabpanel" aria-labelledby="rekening-tab">
        <?php
           if($cekRekening!=1){
                echo "<th>Data belum diisi oleh Perusahaan</th>";
                } 
            else {
            ?>

        <table class="table table-hover table-bordered mt-3">
                <tr>
                    <th>Nomor Rekening Utama</th>
                    <th><?php echo $hasilRekening['no_rekening'] ?></th>
                </tr>
                <tr>    
                    <th>Pemilik Rekening</th>
                    <th><?php echo $hasilRekening['pemilik_rekening'] ?></th>
                </tr>
                <tr>
                    <th>Nama Bank</th>
                    <th><?php echo $hasilRekening['nama_bank'] ?></th>
                </tr>
                <tr>
                    <th>Cabang bank</th>
                    <th><?php echo $hasilRekening['cabang_bank'] ?></th>
                </tr>

                <tr>
                    <th>file_rek_koran</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilRekening['file_rek_koran'] ?>'target=blank><?php echo $hasilRekening['file_rek_koran'] ?>  </a>
                    </th>
                </tr>
                <tr>
                    <th>file_buku_rekening</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilRekening['file_buku_rekening'] ?>'target=blank><?php echo $hasilRekening['file_buku_rekening'] ?>  </a>
                    </th>
                </tr>
                </table>
        <?php
        }
        ?>
</div>

<!-- Pajak -->
<?php 
        $a=$_GET['id'];
        $queri="SELECT * FROM pajak WHERE id_vendor='$a'";
        $sql=mysqli_query($conn, $queri);
        $hasilPajak=mysqli_fetch_assoc($sql);
        $cekPajak=mysqli_num_rows($sql);
    ?>
        <div class="tab-pane fade" id="pajak" role="tabpanel" aria-labelledby="pajak-tab">
        <?php
           if($cekPajak!=1){
                echo "<th>Data belum diisi oleh Perusahaan</th>";
                } 
            else {
            ?>

        <table class="table table-hover table-bordered mt-3">
                <tr>
                    <th>NPWP Perusahaan</th>
                    <th><?php echo $hasilPajak['npwp_vendor'] ?></th>
                </tr>
                <tr>    
                    <th>npwp_direktur</th>
                    <th><?php echo $hasilPajak['npwp_direktur'] ?></th>
                </tr>
                <tr>
                    <th>file_tanda_daftar</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilPajak['file_tanda_daftar'] ?>'target=blank><?php echo $hasilPajak['file_tanda_daftar'] ?>  </a>
                    </th>
                </tr>
                <tr>
                    <th>file_npwp_vendor</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilPajak['file_npwp_vendor'] ?>'target=blank><?php echo $hasilPajak['file_npwp_vendor'] ?>  </a>
                    </th>
                </tr>
                <tr>
                    <th>file_npwp_direktur</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilPajak['file_npwp_direktur'] ?>'target=blank><?php echo $hasilPajak['file_npwp_direktur'] ?>  </a>
                    </th>
                </tr>
                <tr>
                    <th>file_lapor_pajak</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilPajak['file_lapor_pajak'] ?>'target=blank><?php echo $hasilPajak['file_lapor_pajak'] ?>  </a>
                    </th>
                </tr>
               
                </table>
        <?php
        }
        ?>
</div>

<!-- Ijin -->
<?php 
        $a=$_GET['id'];
        $queri="SELECT * FROM ijin_usaha WHERE id_vendor='$a'";
        $sql=mysqli_query($conn, $queri);
        $hasilIjin=mysqli_fetch_assoc($sql);
        $cekIjin=mysqli_num_rows($sql);
    ?>
        <div class="tab-pane fade" id="ijin" role="tabpanel" aria-labelledby="ijin-tab">
        <?php
           if($cekIjin!=1){
                echo "<th>Data belum diisi oleh Perusahaan</th>";
                } 
            else {
            ?>

        <table class="table table-hover table-bordered mt-3">
                <tr>
                    <th>kualifikasi_usaha</th>
                    <th><?php echo $hasilIjin['kualifikasi_usaha'] ?></th>
                </tr>
                <tr>    
                    <th>kualifikasi_pengadaan</th>
                    <th><?php echo $hasilIjin['kualifikasi_pengadaan'] ?></th>
                </tr>
                <tr>
                    <th>pkp</th>
                    <th><?php echo $hasilIjin['pkp'] ?></th>
                </tr>
                <tr>
                    <th>nib</th>
                    <th><?php echo $hasilIjin['nib'] ?></th>
                </tr>
                 
                <tr>
                    <th>file_ijin_usaha</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilIjin['file_ijin_usaha'] ?>'target=blank><?php echo $hasilIjin['file_ijin_usaha'] ?>  </a>
                    </th>
                </tr>

                <tr>
                    <th>file_nib</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilIjin['file_nib'] ?>'target=blank><?php echo $hasilIjin['file_nib'] ?>  </a>
                    </th>
                </tr>
                <tr>
                    <th>file_sert_badan_usaha</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilIjin['file_sert_badan_usaha'] ?>'target=blank><?php echo $hasilIjin['file_sert_badan_usaha'] ?>  </a>
                    </th>
                </tr>
                <tr>
                    <th>file_ska_konstruksi</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilIjin['file_ska_konstruksi'] ?>'target=blank><?php echo $hasilIjin['file_ska_konstruksi'] ?>  </a>
                    </th>
                </tr>
                <tr>
                    <th>file_skt_konstruksi</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilIjin['file_skt_konstruksi'] ?>'target=blank><?php echo $hasilIjin['file_skt_konstruksi'] ?>  </a>
                    </th>
                </tr>
            </table>
        <?php
        }
        ?>
</div>

<!-- TenagaKerja -->
<?php 
        $a=$_GET['id'];
        $queri="SELECT * FROM tenagakerja WHERE id_vendor='$a'";
        $sql=mysqli_query($conn, $queri);
        $hasilTenaga=mysqli_fetch_assoc($sql);
        $cekTenaga=mysqli_num_rows($sql);
    ?>
        <div class="tab-pane fade" id="tenagakerja" role="tabpanel" aria-labelledby="tenagakerja-tab">
        <?php
           if($cekTenaga!=1){
                echo "<th>Data belum diisi oleh Perusahaan</th>";
                } 
            else {
            ?>

        <table class="table table-hover table-bordered mt-3">
                <tr>
                    <th>file_dok_teknik</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilTenaga['file_dok_teknik'] ?>'target=blank><?php echo $hasilTenaga['file_dok_teknik'] ?>  </a>
                    </th>
                </tr>
                <tr>
                    <th>file_ktp</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilTenaga['file_ktp'] ?>'target=blank><?php echo $hasilTenaga['file_ktp'] ?>  </a>
                    </th>
                </tr>
                <tr>
                    <th>file_skt</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilTenaga['file_skt'] ?>'target=blank><?php echo $hasilTenaga['file_skt'] ?>  </a>
                    </th>
                </tr>
                <tr>
                    <th>file_ijazah</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilTenaga['file_ijazah'] ?>'target=blank><?php echo $hasilTenaga['file_ijazah'] ?>  </a>
                    </th>
                </tr>
                </table>
        <?php
        }
        ?>
</div>

<!-- Keuangan -->
<?php 
        $a=$_GET['id'];
        $queri="SELECT * FROM Keuangan WHERE id_vendor='$a'";
        $sql=mysqli_query($conn,$queri);
        $hasilKeuangan=mysqli_fetch_assoc($sql);
        $cekKeuangan=mysqli_num_rows($sql);
    ?>
        <div class="tab-pane fade" id="Keuangan" role="tabpanel" aria-labelledby="Keuangan-tab">
        <?php
           if($cekKeuangan!=1){
                echo "<th>Data belum diisi oleh Perusahaan</th>";
                } 
            else {
            ?>

        <table class="table table-hover table-bordered mt-3">
                <tr>
                    <th>file_neraca</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilKeuangan['file_neraca'] ?>'target=blank><?php echo $hasilKeuangan['file_neraca'] ?>  </a>
                    </th>
                </tr>
                <tr>
                    <th>file_labarugi</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilKeuangan['file_labarugi'] ?>'target=blank><?php echo $hasilKeuangan['file_labarugi'] ?>  </a>
                    </th>
                </tr>
                </table>
        <?php
        }
        ?>
</div>

<!-- Dokumen pelengkap -->

<?php 
        $a=$_GET['id'];
        $queri="SELECT * FROM dokumen WHERE id_vendor='$a'";
        $sql=mysqli_query($conn, $queri);
        $hasilDokumen=mysqli_fetch_assoc($sql);
        $cekDokumen=mysqli_num_rows($sql);
    ?>
        <div class="tab-pane fade" id="Dokumen" role="tabpanel" aria-labelledby="Dokumen-tab">

        <?php
           if($cekKeuangan!=1){
                echo "<th>Data belum diisi oleh Perusahaan</th>";
                } 
            else {
            ?>

        <table class="table table-hover table-bordered mt-3">
                <tr>
                    <th>Formulir file_ikutserta</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilDokumen['file_ikutserta'] ?>'target=_blank > <?php echo $hasilDokumen['file_ikutserta'] ?>  &nbsp &nbsp
                    <span class="glyphicon glyphicon-eye-open"></span></a>
                    </th>
                </tr>

                <tr>
                    <th>file_suratkuasa</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilDokumen['file_suratkuasa'] ?>'target=_blank > <?php echo $hasilDokumen['file_suratkuasa'] ?>  &nbsp &nbsp
                    <span class="glyphicon glyphicon-eye-open"></span></a>
                    </th>
                </tr>

                <tr>
                    <th>file_pendaftaran</th>
                    <th><a href='ld_dpt_v?id=<?php echo $hasilDokumen['file_pendaftaran'] ?>'target=_blank > <?php echo $hasilDokumen['file_pendaftaran'] ?>  &nbsp &nbsp
                    <span class="glyphicon glyphicon-eye-open"></span></a>
                    </th>
                </tr>               
        </table>
        <?php 
        }
        ?>

        </div>
        </div>


  </div>
</div>
</div>

<?php
include '../footer.php';
?>