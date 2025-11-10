<?php
include 'menu.php';
include '../link.php';
?>

<div>
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="index"><i class="fas fa-home"></i></a></li>
    <li class="breadcrumb-item"><a href="dpt_ld">DPT</a></li>
    <li class="breadcrumb-item"><a href="dpt_ld_i">Tambah Data</a></li>
  </ul>
</div>

<div class="container-fluid">
<div class="card">
  <div class="card-header bg-primary text-white text-center h5">
  Pendaftaran Penyedia Barang dan Jasa
  </div>

  <div class="card-body">
  <form method="POST" action="" enctype="multipart/form-data">
    <!-- identitasVendor -->
    <div class="alert alert-primary" role="alert">
        Identitas Perusahaan
    </div>
        <div class="form-group row"> 
            <label class="col-md-2 col-sm-12 col-form-label" for="id_vendor">ID Penyedia:</label>
            <div class="col-md-10 col-sm-12">
            <input name='id_vendor' type="text" class="form-control" id="id_vendor" value="<?php echo $_SESSION['id_vendor']?> " readonly> 
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-12 col-md-2 col-form-label" for="nama_awal">Nama awal</label>
            <div class="col-md-10 col-sm-12">
            <select name="nama_awal" id="nama_awal" class="form-control" >
                <option value =""> -- Pilih nama awal--  </option>
                <option value='BUMD.'> BUMD </option>
                <option value='BUMN.'> BUMN </option>
                <option value='CV.'> CV </option>  
                <option value='Firma'> Firma </option>  
                <option value='Konsultan Perseorangan'> Konsultan Perseorangan </option>  
                <option value='Koperasi'> Koperasi </option>  
                <option value='Koperasi Bersama'> Koperasi Bersama </option>  
                <option value='Notaris'> Notaris </option>  
                <option value='PT.'> PT </option> 
                <option value='UD.'> UD </option> 
                <option value='Yayasan'> Yayasan </option>               
            </select>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12 col-form-label" for="nama">Nama perusahaan</label>
            <div class="col-md-10 col-sm-12">
            <input name="nama" type="text" class="form-control" id="nama" value="<?php echo $_SESSION['nama_vendor']?>" readonly>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="nama_akhir">Nama akhir</label>
            <div class="col-md-10 col-sm-12 ">
            <select name="nama_akhir" id="nama_akhir" class="form-control" >
                    <option value =""> -- Pilih nama akhir--  </option>
                    <option value='Tbk'> Tbk </option>
                    <option value='Ltd'> Ltd </option> 
                    <option value=''> Tidak ada </option>            
                </select>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="tanggal_berdiri">Tanggal pendirian</label>
            <div class="col-md-10 col-sm-12 ">
            <input name="tanggal_berdiri" type="date" class="form-control" id="tanggal_berdiri" required>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="kepemilikan">Kepemilikan</label>
            <div class="col-md-10 col-sm-12 ">
            <select name="kepemilikan" id="kepemilikan" class="form-control" required>
                <option value =""> -- Pilih Kepemilikan--  </option>
               <option value="Publik">Publik</option>
               <option value="Swasta">Swasta</option>
            </select>
        </div> </div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label " for="telp">Nomor Telepon</label>
            <div class="col-md-10 col-sm-12 ">
            <input name="telp" type="number" class="form-control" id="telp" required>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label " for="website">Website</label>
            <div class="col-md-10 col-sm-12 ">
            <input name="website" type="text" class="form-control" id="website" required>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="akta">Akta Pendirian</label>
            <div class="col-md-10 col-sm-12 ">
                <input type="file" name="akta" id="akta" accept=".pdf" required>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="kop_surat">Kop Surat</label>
            <div class="col-md-10 col-sm-12 ">
                <input type="file" name="kop_surat" id="kop_surat" accept=".pdf" required>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="pengalaman">Pengalaman Perusahaan</label>
            <div class="col-md-10 col-sm-12 ">
                <input type="file" name="pengalaman" id="pengalaman" accept=".pdf" required>
        </div></div>
        
        <!--  endIdentitasVendor -->

        <!-- alamatVendor -->
    <div class="alert alert-primary" role="alert">
        Alamat Perusahaan
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="provinsi">Provinsi</label>
        <div class="col-md-10 col-sm-12 ">
            <input name="provinsi" type="text" class="form-control" id="provinsi" required>
        </div>
    </div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="kota">Kota/ kabupaten</label>
            <div class="col-md-10 col-sm-12 ">
            <input name="kota" type="text" class="form-control" id="kota" required>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="kecamatan">Kecamatan</label>
            <div class="col-md-10 col-sm-12 ">
            <input name="kecamatan" type="text" class="form-control" id="kecamatan" required>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="kelurahan">Kelurahan</label>
            <div class="col-md-10 col-sm-12 ">
            <input name="kelurahan" type="text" class="form-control" id="kelurahan" required>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="jalan">Jalan, RT/RW dan Nomor</label>
            <div class="col-md-10 col-sm-12 ">
            <input name="jalan" type="text" class="form-control" id="jalan" required>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="kodepos">Kode pos</label>
            <div class="col-md-10 col-sm-12 ">
            <input name="kodepos" type="number" min="1" class="form-control" id="kodepos" required>
        </div></div>
    
        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="domisili">Surat Domisili</label>
            <div class="col-md-10 col-sm-12 ">
                <input type="file" name="domisili" id="domisili" accept=".pdf" required>
        </div></div>

     <!-- pengurus -->
     <div class="alert alert-primary" role="alert">
        Pengurus
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="pemilik">Nama Pemilik</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="pemilik" type="text" class="form-control" id="pemilik" required>
    </div> </div>

    <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="jenis_id">Jenis Identitas Pemilik</label>
            <div class="col-md-10 col-sm-12 ">
            <select name="jenis_id" id="jenis_id" class="form-control">
                <option value ="" disabled='true'> -- Pilih Jenis ID--  </option>
               <option value="KTP">KTP</option>
               <option value="Paspor">Paspor</option>
               <option value="SIM ">SIM </option>
               <option value="Lainnya">Lainnya</option>
            </select>
        </div> </div>

    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="no_id_pemilik">Nomor Identitas pemilik</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="no_id_pemilik" type="text" class="form-control" id="no_id_pemilik" required>
    </div> </div>

    <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="kartuPemilik">Identitas Pemilik</label>
            <div class="col-md-10 col-sm-12 ">
                <input type="file" name="kartuPemilik" id="kartuPemilik" accept=".pdf" required>
        </div></div>

    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="direktur">Nama Direktur</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="direktur" type="text" class="form-control" id="direktur" required>
        </div> 
    </div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="jenis_id_direktur">Jenis Identitas Direktur</label>
            <div class="col-md-10 col-sm-12 ">
            <select name="jenis_id_direktur" id="jenis_id_direktur" class="form-control">
                <option value =""> -- Pilih Jenis ID--  </option>
               <option value="KTP">KTP</option>
               <option value="Paspor">Paspor</option>
               <option value="SIM ">SIM </option>
               <option value="Lainnya">Lainnya</option>
            </select>
        </div> </div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="no_id_direktur">Nomor Identitas Direktur</label>
            <div class="col-md-10 col-sm-12 ">
            <input name="no_id_direktur" type="text" class="form-control" id="no_id_direktur" required>
        </div> </div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="kartuDirektur">Identitas Direktur</label>
            <div class="col-md-10 col-sm-12 ">
            <input type="file" name="kartuDirektur" id="kartuDirektur" accept=".pdf" required>
            </div>
        </div>

    <!-- rekening -->
    <div class="alert alert-primary" role="alert">
        Rekening
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="no_rekening">Nomor Rekening Perusahaan</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="no_rekening" type="text" class="form-control" id="no_rekening" required>
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="pemilik_rekening">Pemilik Rekening</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="pemilik_rekening" type="text" class="form-control" id="pemilik_rekening" required>
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="nama_bank">Nama Bank</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="nama_bank" type="text" class="form-control" id="nama_bank" required>
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="cabang_bank">Cabang bank</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="cabang_bank" type="text" class="form-control" id="cabang_bank" required>
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="file_buku_rekening">Fotocopy Buku Rekening</label>
        <div class="col-md-10 col-sm-12 ">
            <input type="file" name="file_buku_rekening" id="file_buku_rekening" accept=".pdf" required>
        </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_rek_koran">Fotocopy Rekening Koran 3 Bulan Terakhir</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_rek_koran" id="file_rek_koran" accept=".pdf" required>
    </div>
    </div>


    <!-- Pajak -->
    <div class="alert alert-primary" role="alert">
        Pajak
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="npwp_vendor">NPWP Perusahaan</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="npwp_vendor" type="text" class="form-control" id="npwp_vendor" required>
        </div> 
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_npwp_vendor">Fotocopy NPWP Perusahaan</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_npwp_vendor" id="file_npwp_vendor" accept=".pdf" required>
    </div>
    </div>

    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="npwp_direktur">NPWP Direktur</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="npwp_direktur" type="text" class="form-control" id="npwp_direktur" required>
        </div> 
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_npwp_direktur">Fotocopy NPWP Direktur</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_npwp_direktur" id="file_npwp_direktur" accept=".pdf" required>
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_tanda_daftar">Fotocopy Tanda Daftar Pajak</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_tanda_daftar" id="file_tanda_daftar" accept=".pdf" required>
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_lapor_pajak">Fotocopy Lapor Pajak</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_lapor_pajak" id="file_lapor_pajak" accept=".pdf" required>
    </div>
    </div>

    <!-- Ijin Usaha -->
    <div class="alert alert-primary" role="alert">
        Ijin Usaha
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="kualifikasi_usaha">Kualifikasi Usaha</label>
        <div class="col-md-10 col-sm-12 ">
        <select name="kualifikasi_usaha" id="kualifikasi_usaha" class="form-control" required>
            <option value ="" disabled='true'> -- Pilih jenis kualifikasi--  </option>
            <option value="Mikro">Mikro</option>
            <option value="Kecil">Kecil</option>
            <option value="Menengah">Menengah</option>
            <option value="Besar">Besar</option>
        </select>
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="kualifikasi_pengadaan">Kualifikasi Pengadaan</label>
        <div class="col-md-10 col-sm-12 ">
        <select name="kualifikasi_pengadaan" id="kualifikasi_pengadaan" class="form-control" required>
            <option value ="" disabled='true'> -- Pilih jenis kualifikasi--  </option>
            <option value="Konsultasi">Konsultasi</option>
            <option value="Jasa Konstruksi">Jasa Konstruksi</option>
            <option value="Pengadaan Barang">Pengadaan Barang</option>
            <option value="Jasa Lain">Jasa Lain</option>
        </select>
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="pkp">Nomor PKP</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="pkp" type="text" class="form-control" id="pkp" required>
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="nib">NIB</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="nib" type="text" class="form-control" id="nib" required>
        </div> 
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_ijin_usaha">Fotocopy Ijin Usaha</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_ijin_usaha" id="file_ijin_usaha" accept=".pdf" required>
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_nib">Fotocopy NIB</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_nib" id="file_nib" accept=".pdf" required>
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_sert_badan_usaha">Fotocopy Sertifikat Badan Usaha</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_sert_badan_usaha" id="file_sert_badan_usaha" accept=".pdf" required>
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_ska_konstruksi">Fotocopy SKA Konstruksi</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_ska_konstruksi" id="file_ska_konstruksi" accept=".pdf" required>
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_skt_konstruksi">Fotocopy SKT Konstruksi</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_skt_konstruksi" id="file_skt_konstruksi" accept=".pdf" required>
    </div>
    </div>

    <!-- TenagaKerja -->
    <div class="alert alert-primary" role="alert">
        Tenaga Kerja
    </div>
    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_dok_teknik">Dokumen Tenaga Teknis</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_dok_teknik" id="file_dok_teknik" accept=".pdf" required>
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_ktp">KTP Tenaga Teknis</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_ktp" id="file_ktp" accept=".pdf" required>
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_skt">SKT Tenaga Teknis</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_skt" id="file_skt" accept=".pdf" required>
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_ijazah">Ijazah Tenaga Teknis</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_ijazah" id="file_ijazah" accept=".pdf" required>
    </div>
    </div>

    <!-- Keuangan -->
    <div class="alert alert-primary" role="alert">
        Keuangan
    </div>
    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_neraca">Laporan Neraca Perusahaan</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_neraca" id="file_neraca" accept=".pdf" required>
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_labarugi">Laporan Laba Rugi Perusahaan</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_labarugi" id="file_labarugi" accept=".pdf" required>
    </div>
    </div>

    <!-- dokumen -->
    <div class="alert alert-primary" role="alert">
        Dokumen
    </div>
    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_ikutserta">Formulir Keikutsertaan</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_ikutserta" id="file_ikutserta" accept=".pdf" required>
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_suratkuasa">Surat Kuasa</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_suratkuasa" id="file_suratkuasa" accept=".pdf" required>
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_pendaftaran">Formulir Pendaftaran</label>
    <div class="col-md-10 col-sm-12 ">
        <input type="file" name="file_pendaftaran" id="file_pendaftaran" accept=".pdf" required>
    </div>
    </div>

    <hr>
    <div class="form-group row">
        <div class="mx-auto">
        <button name="simpan" type="submit" class="btn btn-success">Simpan</button>
        <a class="btn btn-danger" href="dpt_ld" role="button">Cancel</a>
        </div>
    </div>

    <?php
    if(isset($_POST['simpan'])){
        $id_vendor=$_POST['id_vendor'];
        $nama_awal=$_POST['nama_awal'];
        $nama=$_POST['nama'];
        $nama_akhir=$_POST['nama_akhir'];
        $tanggal_berdiri=$_POST['tanggal_berdiri'];
        $kepemilikan=$_POST['kepemilikan'];
        $telp=$_POST['telp'];
        $website=$_POST['website'];

        $img1=$_FILES['akta']['name'];
        $tmp1 = $_FILES['akta']['tmp_name'];
        $newimg1 = $id_vendor.$img1;
        $path1 = "../dokumenVendor/".$newimg1;

        $img2=$_FILES['kop_surat']['name'];
        $tmp2 = $_FILES['kop_surat']['tmp_name'];
        $newimg2 = $id_vendor.$img2;
        $path2 = "../dokumenVendor/".$newimg2;

        $img3=$_FILES['pengalaman']['name'];
        $tmp3 = $_FILES['pengalaman']['tmp_name'];
        $newimg3 = $id_vendor.$img3;
        $path3 = "../dokumenVendor/".$newimg3;

        $provinsi=$_POST['provinsi'];
        $kota=$_POST['kota'];
        $kecamatan=$_POST['kecamatan'];
        $kelurahan=$_POST['kelurahan'];
        $jalan=$_POST['jalan'];
        $kodepos=$_POST['kodepos'];

        $img4=$_FILES['domisili']['name'];
        $tmp4 = $_FILES['domisili']['tmp_name'];
        $newimg4 = $id_vendor.$img4;
        $path4 = "../dokumenVendor/".$newimg4;

        $pemilik=$_POST['pemilik'];
        $jenis_id=$_POST['jenis_id'];
        $no_id_pemilik=$_POST['no_id_pemilik'];

        $img5=$_FILES['kartuPemilik']['name'];
        $tmp5 = $_FILES['kartuPemilik']['tmp_name'];
        $newimg5 = $id_vendor.$img5;
        $path5 = "../dokumenVendor/".$newimg5;

        $direktur=$_POST['direktur'];
        $jenis_id_direktur=$_POST['jenis_id_direktur'];
        $no_id_direktur=$_POST['no_id_direktur'];

        $img6=$_FILES['kartuDirektur']['name'];
        $tmp6 = $_FILES['kartuDirektur']['tmp_name'];
        $newimg6 = $id_vendor.$img6;
        $path6 = "../dokumenVendor/".$newimg6;

        $no_rekening=$_POST['no_rekening'];
        $pemilik_rekening=$_POST['pemilik_rekening'];
        $nama_bank=$_POST['nama_bank'];
        $cabang_bank=$_POST['cabang_bank'];

        $img7=$_FILES['file_buku_rekening']['name'];
        $tmp7 = $_FILES['file_buku_rekening']['tmp_name'];
        $newimg7 = $id_vendor.$img7;
        $path7 = "../dokumenVendor/".$newimg7;

        $img8=$_FILES['file_rek_koran']['name'];
        $tmp8 = $_FILES['file_rek_koran']['tmp_name'];
        $newimg8 = $id_vendor.$img8;
        $path8 = "../dokumenVendor/".$newimg8;

        $npwp_vendor=$_POST['npwp_vendor'];
        $npwp_direktur=$_POST['npwp_direktur'];

        $img9=$_FILES['file_npwp_vendor']['name'];
        $tmp9 = $_FILES['file_npwp_vendor']['tmp_name'];
        $newimg9 = $id_vendor.$img9;
        $path9 = "../dokumenVendor/".$newimg9;

        $img10=$_FILES['file_npwp_direktur']['name'];
        $tmp10 = $_FILES['file_npwp_direktur']['tmp_name'];
        $newimg10 = $id_vendor.$img10;
        $path10 = "../dokumenVendor/".$newimg10;

        $img11=$_FILES['file_tanda_daftar']['name'];
        $tmp11 = $_FILES['file_tanda_daftar']['tmp_name'];
        $newimg11 = $id_vendor.$img11;
        $path11 = "../dokumenVendor/".$newimg11;

        $img12=$_FILES['file_lapor_pajak']['name'];
        $tmp12 = $_FILES['file_lapor_pajak']['tmp_name'];
        $newimg12 = $id_vendor.$img12;
        $path12 = "../dokumenVendor/".$newimg12;

        $kualifikasi_usaha=$_POST['kualifikasi_usaha'];
        $kualifikasi_pengadaan=$_POST['kualifikasi_pengadaan'];
        $pkp=$_POST['pkp'];
        $nib=$_POST['nib'];

        $img13=$_FILES['file_ijin_usaha']['name'];
        $tmp13 = $_FILES['file_ijin_usaha']['tmp_name'];
        $newimg13 = $id_vendor.$img13;
        $path13 = "../dokumenVendor/".$newimg13;

        $img14=$_FILES['file_nib']['name'];
        $tmp14 = $_FILES['file_nib']['tmp_name'];
        $newimg14 = $id_vendor.$img14;
        $path14 = "../dokumenVendor/".$newimg14;

        $img15=$_FILES['file_sert_badan_usaha']['name'];
        $tmp15 = $_FILES['file_sert_badan_usaha']['tmp_name'];
        $newimg15 = $id_vendor.$img15;
        $path15 = "../dokumenVendor/".$newimg15;

        $img16=$_FILES['file_ska_konstruksi']['name'];
        $tmp16 = $_FILES['file_ska_konstruksi']['tmp_name'];
        $newimg16 = $id_vendor.$img16;
        $path16 = "../dokumenVendor/".$newimg16;

        $img17=$_FILES['file_skt_konstruksi']['name'];
        $tmp17 = $_FILES['file_skt_konstruksi']['tmp_name'];
        $newimg17 = $id_vendor.$img17;
        $path17 = "../dokumenVendor/".$newimg17;

        $img18=$_FILES['file_dok_teknik']['name'];
        $tmp18 = $_FILES['file_dok_teknik']['tmp_name'];
        $newimg18 = $id_vendor.$img18;
        $path18 = "../dokumenVendor/".$newimg18;

        $img19=$_FILES['file_ktp']['name'];
        $tmp19 = $_FILES['file_ktp']['tmp_name'];
        $newimg19 = $id_vendor.$img19;
        $path19 = "../dokumenVendor/".$newimg19;

        $img20=$_FILES['file_skt']['name'];
        $tmp20 = $_FILES['file_skt']['tmp_name'];
        $newimg20 = $id_vendor.$img20;
        $path20 = "../dokumenVendor/".$newimg20;

        $img21=$_FILES['file_ijazah']['name'];
        $tmp21 = $_FILES['file_ijazah']['tmp_name'];
        $newimg21 = $id_vendor.$img21;
        $path21 = "../dokumenVendor/".$newimg21;

        $img22=$_FILES['file_neraca']['name'];
        $tmp22 = $_FILES['file_neraca']['tmp_name'];
        $newimg22 = $id_vendor.$img22;
        $path22 = "../dokumenVendor/".$newimg22;

        $img23=$_FILES['file_labarugi']['name'];
        $tmp23 = $_FILES['file_labarugi']['tmp_name'];
        $newimg23 = $id_vendor.$img23;
        $path23 = "../dokumenVendor/".$newimg23;

        $img24=$_FILES['file_ikutserta']['name'];
        $tmp24 = $_FILES['file_ikutserta']['tmp_name'];
        $newimg24 = $id_vendor.$img24;
        $path24 = "../dokumenVendor/".$newimg24;

        $img25=$_FILES['file_suratkuasa']['name'];
        $tmp25 = $_FILES['file_suratkuasa']['tmp_name'];
        $newimg25 = $id_vendor.$img25;
        $path25 = "../dokumenVendor/".$newimg25;

        $img26=$_FILES['file_pendaftaran']['name'];
        $tmp26 = $_FILES['file_pendaftaran']['tmp_name'];
        $newimg26 = $id_vendor.$img26;
        $path26 = "../dokumenVendor/".$newimg26;


        if(move_uploaded_file($tmp1, $path1)AND move_uploaded_file($tmp2, $path2)AND move_uploaded_file($tmp3, $path3)AND move_uploaded_file($tmp4, $path4)AND move_uploaded_file($tmp5, $path5)AND move_uploaded_file($tmp6, $path6)AND move_uploaded_file($tmp7, $path7)AND move_uploaded_file($tmp8, $path8)AND move_uploaded_file($tmp9, $path9)AND move_uploaded_file($tmp10, $path10)AND move_uploaded_file($tmp11, $path11)AND move_uploaded_file($tmp12, $path12)AND move_uploaded_file($tmp13, $path13)AND move_uploaded_file($tmp14, $path14)AND move_uploaded_file($tmp15, $path15)AND move_uploaded_file($tmp16, $path16)AND move_uploaded_file($tmp17, $path17)AND move_uploaded_file($tmp18, $path18)AND move_uploaded_file($tmp19, $path19)AND move_uploaded_file($tmp20, $path20)AND move_uploaded_file($tmp21, $path21)AND move_uploaded_file($tmp22, $path22)AND move_uploaded_file($tmp23, $path23)AND move_uploaded_file($tmp24, $path24)AND move_uploaded_file($tmp25, $path25)AND move_uploaded_file($tmp26, $path26))
        {	
        $queri="INSERT INTO data_vendor (id_vendor, nama_awal, nama_vendor, nama_akhir, tanggal_berdiri, kepemilikan, telp, website, file_akta, file_kop_surat, file_pengalaman) VALUES ('$id_vendor','$nama_awal','$nama','$nama_akhir','$tanggal_berdiri','$kepemilikan','$telp','$website','$newimg1','$newimg2','$newimg3')";

        $queriAlamat="INSERT INTO alamat(id_vendor, provinsi, kota, kecamatan, kelurahan, jalan, kodepos, file_domisili) VALUES ('$id_vendor','$provinsi','$kota','$kecamatan','$kelurahan','$jalan','$kodepos','$newimg4')";

        $queriPengurus="INSERT INTO pengurus(id_vendor, pemilik, kartu_pemilik, no_kartu_pemilik, direktur, kartu_direktur, no_kartu_direktur, file_kartu_pemilik, file_kartu_direktur) VALUES ('$id_vendor','$pemilik','$jenis_id','$no_id_pemilik','$direktur','$jenis_id_direktur','$no_id_direktur','$newimg5','$newimg6')";

        $queriRekening="INSERT INTO rekening(id_vendor, no_rekening, pemilik_rekening, nama_bank, cabang_bank, file_rek_koran, file_buku_rekening) VALUES ('$id_vendor','$no_rekening','$pemilik_rekening','$nama_bank','$cabang_bank','$newimg7','$newimg8')";

        $queriPajak="INSERT INTO pajak(id_vendor, npwp_vendor, npwp_direktur, file_tanda_daftar, file_npwp_vendor, file_npwp_direktur, file_lapor_pajak) VALUES ('$id_vendor','$npwp_vendor','$npwp_direktur','$newimg11','$newimg9','$newimg10','$newimg12')";

        $queriIjin="INSERT INTO ijin_usaha(id_vendor, kualifikasi_usaha, kualifikasi_pengadaan, pkp, nib, file_ijin_usaha, file_nib, file_sert_badan_usaha, file_ska_konstruksi, file_skt_konstruksi) VALUES ('$id_vendor','$kualifikasi_usaha','$kualifikasi_pengadaan','$pkp','$nib','$newimg13','$newimg14','$newimg15','$newimg16','$newimg17')";

        $queriTenaga="INSERT INTO tenagakerja(id_vendor, file_dok_teknik, file_ktp, file_skt, file_ijazah) VALUES ('$id_vendor','$newimg18','$newimg19','$newimg20','$newimg21')";

        $queriKeuangan="INSERT INTO keuangan(id_vendor, file_neraca, file_labarugi) VALUES ('$id_vendor','$newimg22','$newimg23')";

        $queriDokumen="INSERT INTO dokumen(id_vendor, file_ikutserta, file_suratkuasa, file_pendaftaran) VALUES ('$id_vendor','$newimg24','$newimg25','$newimg26')";
    
        $sql=mysqli_multi_query($conn,$queri);
        $sql=mysqli_multi_query($conn,$queriAlamat);
        $sql=mysqli_multi_query($conn,$queriPengurus);
        $sql=mysqli_multi_query($conn,$queriRekening);
        $sql=mysqli_multi_query($conn,$queriPajak);
        $sql=mysqli_multi_query($conn,$queriIjin);
        $sql=mysqli_multi_query($conn,$queriTenaga);
        $sql=mysqli_multi_query($conn,$queriKeuangan);
        $sql=mysqli_multi_query($conn,$queriDokumen);

        if($sql){
            echo "<script>alert ('Berhasil manambahkan Identitas Perusahaan');window.location='dpt_ld'</script>";
        }
        else {
        echo "<script>alert ('ERROR..! Terjadi kegagalan dalam proses penyimpanan data');</script>";
        }
    }
    }
    ?>  
    </form>

</div>
</div>
</div>



<!-- scriptValidasiFileSize -->
<script type="text/javascript">
var uploadField = document.getElementsByTagName("input");
for (var i = 0; i < uploadField.length; i++) {
uploadField[i].onchange = function() {
    if(this.files[0].size > 6000000){ // ini untuk ukuran 5MB, 1000000 untuk 1 MB.
       alert("Maaf. File Terlalu Besar ! Maksimal Upload File sebesar 6 MB");
       this.value = "";
    }};
};
</script> 

<?php
include '../footer.php';
?>