<?php
include 'menu.php';
include '../link.php';
?>

<div>
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="index"><i class="fas fa-home"></i></a></li>
    <li class="breadcrumb-item"><a href="dpt_ld">DPT</a></li>
    <li class="breadcrumb-item"><a href="">Edit Data</a></li>
  </ul>
</div>

<div class="container">
    <?php
    // Program Utama MENU SKP
    if (isset($_GET['aksi'])){
        switch($_GET['aksi']){
            case "identitas":
                identitas($conn);
                break;
            case "alamat":
                alamat($conn);
                break;
            case "pengurus":
                pengurus($conn);
                break;
            case "rekening":
                rekening($conn);
                break;
            case "pajak":
                pajak($conn);
                break;
            case "ijinusaha":
                ijinusaha($conn);
                break;
            case "tenagakerja":
                tenagakerja($conn);
                break;
            case "keuangan":
                keuangan($conn);
                break;
            case "dokumen":
                dokumen($conn);
                break;
            default:
                identitas($conn);
            }
    } else {
        identitas($conn);
    }
    ?>

<?php
// fungsi ubah identitas
function identitas($conn){ ?>

<div class="card mb-5">
  <div class="card-header bg-primary text-white text-center h5"> Edit Identitas Perusahaan</div>

  <div class="card-body">
    <?php
    $id_vendor=$_GET['id'];
    $queri="SELECT * FROM data_vendor WHERE id_vendor='$id_vendor'";
    $sql=mysqli_query($conn,$queri);
    $hasil=mysqli_fetch_assoc($sql);
    $cek=mysqli_num_rows($sql);
    ?>
    <form method="POST" action="" enctype="multipart/form-data">
    <!-- identitasVendor -->
    <!-- <div class="alert alert-primary" role="alert">
        Identitas Perusahaan
    </div> -->
        <div class="form-group row"> 
            <label class="col-md-2 col-sm-12 col-form-label" for="id_vendor">ID Anggota:</label>
            <div class="col-md-10 col-sm-12">
            <input name='id_vendor' type="text" class="form-control" id="id_vendor" value="<?php echo $hasil['id_vendor']?> " readonly> 
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-12 col-md-2 col-form-label" for="nama_awal">Nama awal</label>
            <div class="col-md-10 col-sm-12">
            <select name="nama_awal" id="nama_awal" class="form-control" >           
                <option value='BUMD.' <?php if($hasil['nama_awal']=='BUMD.'){echo 'selected';} ?> > BUMD </option>
                <option value='BUMN.' <?php if($hasil['nama_awal']=='BUMN.'){echo 'selected';} ?> > BUMN </option>
                <option value='CV.' <?php if($hasil['nama_awal']=='CV.'){echo 'selected';} ?> > CV </option>  
                <option value='Firma' <?php if($hasil['nama_awal']=='Firma'){echo 'selected';} ?> > Firma </option>  
                <option value='Konsultan Perseorangan' <?php if($hasil['nama_awal']=='Konsultan Perseorangan'){echo 'selected';} ?> > Konsultan Perseorangan </option>
                <option value='Koperasi Bersama' <?php if($hasil['nama_awal']=='Koperasi Bersama'){echo 'selected';} ?> > Koperasi Bersama </option>  
                <option value='Notaris' <?php if($hasil['nama_awal']=='Notaris'){echo 'selected';} ?> > Notaris </option>  
                <option value='PT.' <?php if($hasil['nama_awal']=='PT.'){echo 'selected';} ?>> PT </option> 
                <option value='UD.' <?php if($hasil['nama_awal']=='UD.'){echo 'selected';} ?>> UD </option> 
                <option value='Yayasan' <?php if($hasil['nama_awal']=='Yayasan'){echo 'selected';} ?>> Yayasan <option>             
            </select>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12 col-form-label" for="nama">Nama perusahaan</label>
            <div class="col-md-10 col-sm-12">
                <input name="nama" type="text" class="form-control" id="nama" value="<?php echo $hasil['nama_vendor']?>" readonly>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="nama_akhir">Nama akhir</label>
            <div class="col-md-10 col-sm-12 ">
            <select name="nama_akhir" id="nama_akhir" class="form-control">
                    <option value='Tbk' <?php if($hasil['nama_akhir']=='Tbk'){echo 'selected';} ?> > Tbk </option>
                    <option value='Ltd' <?php if($hasil['nama_akhir']=='Ltd'){echo 'selected';} ?> > Ltd </option> 
                    <option value='' <?php if($hasil['nama_akhir']==''){echo 'selected';} ?>> Tidak ada </option>         
                </select>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="tanggal_berdiri">Tanggal pendirian</label>
            <div class="col-md-10 col-sm-12 ">
            <input name="tanggal_berdiri" type="date" class="form-control" id="tanggal_berdiri" value="<?php echo $hasil['tanggal_berdiri'] ?>" required>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="kepemilikan">Kepemilikan</label>
            <div class="col-md-10 col-sm-12 ">
            <select name="kepemilikan" id="kepemilikan" class="form-control" required>
                <option value =""> -- Pilih Kepemilikan--  </option>
               <option value="Publik" <?php if($hasil['kepemilikan']=='Publik'){echo 'selected';} ?> >Publik</option>
               <option value="Swasta" <?php if($hasil['kepemilikan']=='Swasta'){echo 'selected';} ?> >Swasta</option>
            </select>
        </div> </div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label " for="telp">Nomor Telepon</label>
            <div class="col-md-10 col-sm-12 ">
            <input name="telp" type="number" class="form-control" id="telp" value="<?php echo $hasil['telp'] ?>" required>
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label " for="website">Website</label>
            <div class="col-md-10 col-sm-12 ">
                <input name="website" type="text" class="form-control" id="website" value="<?php echo $hasil['website'] ?>">
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="akta">Akta Pendirian</label>
            
            <div class="col-md-10 col-sm-12 ">
            <?php echo $hasil['file_akta'] ?> <br>
            <input type="checkbox" size="30px" name="ubahakta" value="true"> Ceklis jika ingin mengubah file<br>
            <input type="file" name="akta" id="akta" accept=".pdf">
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="kop_surat">Kop Surat</label>
            <div class="col-md-10 col-sm-12 ">
            <?php echo $hasil['file_kop_surat'] ?> <br>
            <input type="checkbox" size="30px" name="ubahkop" value="true"> Ceklis jika ingin mengubah file<br>
            <input type="file" name="kop_surat" id="kop_surat" accept=".pdf">
        </div></div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="pengalaman">Pengalaman Perusahaan</label>
            <div class="col-md-10 col-sm-12 ">
            <?php echo $hasil['file_pengalaman'] ?> <br>
            <input type="checkbox" size="30px" name="ubahpengalaman" value="true"> Ceklis jika ingin mengubah file<br>
            <input type="file" name="pengalaman" id="pengalaman" accept=".pdf">
            </div>
        </div>

        <hr>
        <div class="form-group row">
            <div class="mx-auto">
            <button name="ubah" type="submit" class="btn btn-success">Ubah</button>
            <a class="btn btn-danger" href="dpt_ld" role="button">Cancel</a>
            </div>
        </div>
    </form>
    <!--  endIdentitasVendor -->
  </div>
</div>
<?php
// editProsesIdentitas
if(isset($_POST['ubah'])){
    $nama_awal=$_POST['nama_awal'];
    $nama_akhir=$_POST['nama_akhir'];
    $tanggal_berdiri=$_POST['tanggal_berdiri'];
    $kepemilikan=$_POST['kepemilikan'];
    $telp=$_POST['telp'];
    $website=$_POST['website'];

    $queri="UPDATE data_vendor SET nama_awal='$nama_awal',nama_akhir='$nama_akhir',tanggal_berdiri='$tanggal_berdiri',kepemilikan='$kepemilikan',telp='$telp',website='$website' WHERE id_vendor='$id_vendor'";
    $sql=mysqli_query($conn,$queri);

    if(isset($_POST['ubahakta'])){
        $id_vendor=$hasil['id_vendor'];
        $img1=$_FILES['akta']['name'];
        $tmp1 = $_FILES['akta']['tmp_name'];
        $newimg1 = $id_vendor.$img1;
        $path1 = "../dokumenVendor/".$newimg1;
        echo $id_vendor;

        if(move_uploaded_file($tmp1, $path1)){
        $queri="UPDATE data_vendor SET file_akta='$newimg1' WHERE id_vendor='$id_vendor'";
        $sql=mysqli_query($conn,$queri);
        }
    }
    if(isset($_POST['ubahkop'])){
        $id_vendor=$hasil['id_vendor'];
        $img2=$_FILES['kop_surat']['name'];
        $tmp2 = $_FILES['kop_surat']['tmp_name'];
        $newimg2 = $id_vendor.$img2;
        $path2 = "../dokumenVendor/".$newimg2;

        if(move_uploaded_file($tmp2, $path2)){
        $queri="UPDATE data_vendor SET nama_awal='$nama_awal',nama_akhir='$nama_akhir',tanggal_berdiri='$tanggal_berdiri',kepemilikan='$kepemilikan',telp='$telp',website='$website', file_kop_surat='$newimg2' WHERE id_vendor='$id_vendor'";
        $sql=mysqli_query($conn,$queri);
        }
    }
    if(isset($_POST['ubahpengalaman'])){
        $id_vendor=$hasil['id_vendor'];
        $img3=$_FILES['pengalaman']['name'];
        $tmp3 = $_FILES['pengalaman']['tmp_name'];
        $newimg3 = $id_vendor.$img3;
        $path3 = "../dokumenVendor/".$newimg3;

        if(move_uploaded_file($tmp3, $path3)){
        $queri="UPDATE data_vendor SET nama_awal='$nama_awal',nama_akhir='$nama_akhir',tanggal_berdiri='$tanggal_berdiri',kepemilikan='$kepemilikan',telp='$telp',website='$website', file_pengalaman='$newimg3' WHERE id_vendor='$id_vendor'";
        $sql=mysqli_query($conn,$queri);
        }
    }
    if($sql)
    {    
    echo "<script> alert ('Identitas Perusahaan berhasil diedit');window.location='dpt_ld';</script>"; 
    }
    else {
    echo "Maaf, Terjadi kesalahan saat mencoba untuk menyimpan data ke database";
    } 
}
}
?>

<?php
// fungsi ubah alamat
function alamat($conn){
    $id=$_GET['id'];
    $queri="SELECT * FROM alamat WHERE id_alamat='$id'";
    $sql=mysqli_query($conn,$queri);
    $hasil=mysqli_fetch_assoc($sql);
?>
    <div class="card mb-5">
    <div class="card-header bg-primary text-white text-center h5"> Edit Alamat Perusahaan</div>

    <div class="card-body">
    <form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="provinsi">Provinsi</label>
        <div class="col-md-10 col-sm-12 ">
            <input name="provinsi" type="text" class="form-control" id="provinsi" value="<?php echo $hasil['provinsi']?>" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="kota">Kota/ kabupaten</label>
        <div class="col-md-10 col-sm-12 ">
            <input name="kota" type="text" class="form-control" id="kota" value="<?php echo $hasil['kota']?>" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="kecamatan">Kecamatan</label>
        <div class="col-md-10 col-sm-12 ">
            <input name="kecamatan" type="text" class="form-control" id="kecamatan" value="<?php echo $hasil['kecamatan']?>"  required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="kelurahan">Kelurahan</label>
        <div class="col-md-10 col-sm-12 ">
            <input name="kelurahan" type="text" class="form-control" id="kelurahan" value="<?php echo $hasil['kelurahan']?>" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="jalan">Jalan, RT/RW dan Nomor</label>
        <div class="col-md-10 col-sm-12 ">
            <input name="jalan" type="text" class="form-control" id="jalan" value="<?php echo $hasil['jalan']?>" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="kodepos">Kode pos</label>
        <div class="col-md-10 col-sm-12 ">
            <input name="kodepos" type="number" min="1" class="form-control" id="kodepos" value="<?php echo $hasil['kodepos']?>" required>
        </div>
    </div>
    
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="file_domisili">Surat Domisili</label>
        <div class="col-md-10 col-sm-12 ">
            <?php echo $hasil['file_domisili'] ?> <br>
            <input type="checkbox" size="30px" name="ubahdomisili" value="true"> Ceklis jika ingin mengubah file<br>
            <input type="file" name="file_domisili" id="file_domisili" accept=".pdf">
        </div>
    </div>

    <hr>
        <div class="form-group row">
            <div class="mx-auto">
            <button name="simpan" type="submit" class="btn btn-success">Simpan</button>
            <a class="btn btn-danger" href="dpt_ld" role="button">Cancel</a>
            </div>
        </div>
    </form>
    </div>
<?php
// editProsesAlamat
if(isset($_POST['simpan'])){
    $provinsi=$_POST['provinsi'];
    $kota=$_POST['kota'];
    $kecamatan=$_POST['kecamatan'];
    $kelurahan=$_POST['kelurahan'];
    $jalan=$_POST['jalan'];
    $kodepos=$_POST['kodepos'];

    $queri="UPDATE alamat SET provinsi='$provinsi',kota='$kota',kecamatan='$kecamatan',kelurahan='$kelurahan',jalan='$jalan',kodepos='$kodepos' WHERE id_alamat='$id'";
    $sql=mysqli_query($conn,$queri);

    if(isset($_POST['ubahdomisili'])){
        $id_vendor=$hasil['id_vendor'];
        $img4=$_FILES['file_domisili']['name'];
        $tmp4 = $_FILES['file_domisili']['tmp_name'];
        $newimg4 = $id_vendor.$img4;
        $path4 = "../dokumenVendor/".$newimg4;

        if(move_uploaded_file($tmp4, $path4)){
            $queri="UPDATE alamat SET provinsi='$provinsi',kota='$kota',kecamatan='$kecamatan',kelurahan='$kelurahan',jalan='$jalan',kodepos='$kodepos', file_domisili='$newimg4' WHERE id_alamat='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }

    if($sql)
    {    
    echo "<script> alert ('Alamat Perusahaan berhasil diedit');window.location='dpt_ld';</script>"; 
    }
    else {
    echo "Maaf, Terjadi kesalahan saat mencoba untuk menyimpan data ke database";
    } 
}
} 
?>

<?php
// fungsi ubah pengurus
function pengurus($conn){ 
    $id=$_GET['id'];
    $queri="SELECT * FROM pengurus WHERE id_pengurus='$id'";
    $sql=mysqli_query($conn,$queri);
    $hasil=mysqli_fetch_assoc($sql);
?>

<div class="card mb-5">
    <div class="card-header bg-primary text-white text-center h5"> Edit Pengurus Perusahaan</div>
    <div class="card-body">
    <form method="POST" action="" enctype="multipart/form-data">
    
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="pemilik">Nama Pemilik</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="pemilik" type="text" class="form-control" id="pemilik" value="<?php echo $hasil['pemilik']; ?>" required>
    </div> </div>

    <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="kartu_pemilik">Jenis Identitas Pemilik</label>
            <div class="col-md-10 col-sm-12 ">
            <select name="kartu_pemilik" id="kartu_pemilik" class="form-control">
                <option value =""> -- Pilih Jenis ID--  </option>
               <option value="KTP" <?php if($hasil['kartu_pemilik']=='KTP'){echo 'selected';}  ?>>KTP</option>
               <option value="Paspor" <?php if($hasil['kartu_pemilik']=='Paspor'){echo 'selected';} ?>>Paspor</option>
               <option value="SIM" <?php if($hasil['kartu_pemilik']=='SIM'){echo 'selected';} ?> >SIM </option>
               <option value="Lainnya" <?php if($hasil['kartu_pemilik']=='Lainnya'){echo 'selected';} ?> >Lainnya</option>
            </select>
        </div> </div>

    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="no_kartu_pemilik">Nomor Identitas pemilik</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="no_kartu_pemilik" type="text" class="form-control" id="no_kartu_pemilik" value="<?php echo $hasil['no_kartu_pemilik']; ?>" required>
    </div> </div>

    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="file_kartu_pemilik">Identitas Pemilik</label>
        <div class="col-md-10 col-sm-12 ">
            <?php echo $hasil['file_kartu_pemilik'] ?> <br>
            <input type="checkbox" size="30px" name="ubahkartupemilik" value="true"> Ceklis jika ingin mengubah file<br>
            <input type="file" name="file_kartu_pemilik" id="file_kartu_pemilik" accept=".pdf">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="direktur">Nama Direktur</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="direktur" type="text" class="form-control" id="direktur" value="<?php echo $hasil['direktur']; ?>" required>
        </div> 
    </div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="kartu_direktur">Jenis Identitas Direktur</label>
            <div class="col-md-10 col-sm-12 ">
            <select name="kartu_direktur" id="kartu_direktur" class="form-control">
                <option value =""> -- Pilih Jenis ID--  </option>
                <option value="KTP" <?php if($hasil['kartu_direktur']=='KTP'){echo 'selected';}  ?>>KTP</option>
               <option value="Paspor" <?php if($hasil['kartu_direktur']=='Paspor'){echo 'selected';} ?>>Paspor</option>
               <option value="SIM" <?php if($hasil['kartu_direktur']=='SIM'){echo 'selected';} ?> >SIM </option>
               <option value="Lainnya" <?php if($hasil['kartu_direktur']=='Lainnya'){echo 'selected';} ?> >Lainnya</option>
            </select>
        </div> </div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="no_kartu_direktur">Nomor Identitas Direktur</label>
            <div class="col-md-10 col-sm-12 ">
            <input name="no_kartu_direktur" type="text" class="form-control" id="no_kartu_direktur" value="<?php echo $hasil['no_kartu_direktur'] ?>" required>
        </div> </div>

        <div class="form-group row">
            <label class="col-md-2 col-sm-12  col-form-label" for="file_kartu_direktur">Identitas Direktur</label>
            <div class="col-md-10 col-sm-12 ">
            <?php echo $hasil['file_kartu_direktur'] ?> <br>
            <input type="checkbox" size="30px" name="ubahkartudirektur" value="true"> Ceklis jika ingin mengubah file<br>
            <input type="file" name="file_kartu_direktur" id="file_kartu_direktur" accept=".pdf">
            </div>
        </div>

        <hr>
        <div class="form-group row">
            <div class="mx-auto">
            <button name="simpan" type="submit" class="btn btn-success">Simpan</button>
            <a class="btn btn-danger" href="dpt_ld" role="button">Cancel</a>
            </div>
        </div>
    </form>
    </div>
</div>
<?php 
// editProsesPengurus
if(isset($_POST['simpan'])){
    $pemilik=$_POST['pemilik'];
    $kartu_pemilik=$_POST['kartu_pemilik'];
    $no_kartu_pemilik=$_POST['no_kartu_pemilik'];
    $direktur=$_POST['direktur'];
    $kartu_direktur=$_POST['kartu_direktur'];
    $no_kartu_direktur=$_POST['no_kartu_direktur'];

    $queri="UPDATE pengurus SET pemilik='$pemilik',kartu_pemilik='$kartu_pemilik',no_kartu_pemilik='$no_kartu_pemilik',direktur='$direktur',kartu_direktur='$kartu_direktur',no_kartu_direktur='$no_kartu_direktur' WHERE id_pengurus='$id'";
    $sql=mysqli_query($conn,$queri);

    if(isset($_POST['ubahkartudirektur'])){
        $id_vendor=$hasil['id_vendor'];
        $img6=$_FILES['file_kartu_direktur']['name'];
        $tmp6 = $_FILES['file_kartu_direktur']['tmp_name'];
        $newimg6 = $id_vendor.$img6;
        $path6 = "../dokumenVendor/".$newimg6;

        if(move_uploaded_file($tmp6, $path6)){
            $queri="UPDATE pengurus SET pemilik='$pemilik',kartu_pemilik='$kartu_pemilik',no_kartu_pemilik='$no_kartu_pemilik',direktur='$direktur',kartu_direktur='$kartu_direktur',no_kartu_direktur='$no_kartu_direktur', file_kartu_direktur='$newimg6' WHERE id_pengurus='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }

    if(isset($_POST['ubahkartupemilik'])){
        $id_vendor=$hasil['id_vendor'];
        $img5=$_FILES['file_kartu_pemilik']['name'];
        $tmp5 = $_FILES['file_kartu_pemilik']['tmp_name'];
        $newimg5 = $id_vendor.$img5;
        $path5 = "../dokumenVendor/".$newimg5;

        if(move_uploaded_file($tmp5, $path5)){
            $queri="UPDATE pengurus SET pemilik='$pemilik',kartu_pemilik='$kartu_pemilik',no_kartu_pemilik='$no_kartu_pemilik',direktur='$direktur',kartu_direktur='$kartu_direktur',no_kartu_direktur='$no_kartu_direktur', file_kartu_pemilik='$newimg5' WHERE id_pengurus='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }

    if($sql)
    {    
    echo "<script> alert ('Pengurus Perusahaan berhasil diedit');window.location='dpt_ld';</script>"; 
    }
    else {
    echo "Maaf, Terjadi kesalahan saat mencoba untuk menyimpan data ke database";
    } 
}
} ?>

<?php
// fungsi ubah pengurus
function rekening($conn){ 
    $id=$_GET['id'];
    $queri="SELECT * FROM rekening WHERE id_rekening='$id'";
    $sql=mysqli_query($conn,$queri);
    $hasil=mysqli_fetch_assoc($sql);
?>

<div class="card mb-5">
    <div class="card-header bg-primary text-white text-center h5"> Edit Rekening Perusahaan</div>
    <div class="card-body">
    <form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="no_rekening">Nomor Rekening Perusahaan</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="no_rekening" type="text" class="form-control" id="no_rekening" value="<?php echo $hasil['no_rekening']; ?>" required>
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="pemilik_rekening">Pemilik Rekening</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="pemilik_rekening" type="text" class="form-control" id="pemilik_rekening" value="<?php echo $hasil['pemilik_rekening']; ?>" required>
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="nama_bank">Nama Bank</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="nama_bank" type="text" class="form-control" id="nama_bank" value="<?php echo $hasil['nama_bank']; ?>" required>
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="cabang_bank">Cabang bank</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="cabang_bank" type="text" class="form-control" id="cabang_bank" value="<?php echo $hasil['cabang_bank']; ?>" required>
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="file_buku_rekening">Fotocopy Buku Rekening</label>
        <div class="col-md-10 col-sm-12 ">
            <?php echo $hasil['file_buku_rekening'] ?> <br>
            <input type="checkbox" size="30px" name="ubahbukurekening" value="true"> Ceklis jika ingin mengubah file<br>
            <input type="file" name="file_buku_rekening" id="file_buku_rekening" accept=".pdf" >
        </div>
    </div>

    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="file_rek_koran">Fotocopy Rekening Koran 3 Bulan Terakhir</label>
        <div class="col-md-10 col-sm-12 ">
            <?php echo $hasil['file_rek_koran'] ?> <br>
            <input type="checkbox" size="30px" name="ubahrekoran" value="true"> Ceklis jika ingin mengubah file<br>
            <input type="file" name="file_rek_koran" id="file_rek_koran" accept=".pdf" >
        </div>
    </div>
    <hr>
        <div class="form-group row">
            <div class="mx-auto">
            <button name="simpan" type="submit" class="btn btn-success">Simpan</button>
            <a class="btn btn-danger" href="dpt_ld" role="button">Cancel</a>
            </div>
        </div>
    </form>
    </div>
</div>
<?php 
// editProsesRekening
if(isset($_POST['simpan'])){
    $no_rekening=$_POST['no_rekening'];
    $pemilik_rekening=$_POST['pemilik_rekening'];
    $nama_bank=$_POST['nama_bank'];
    $cabang_bank=$_POST['cabang_bank'];

    $queri="UPDATE rekening SET no_rekening='$no_rekening',pemilik_rekening='$pemilik_rekening', nama_bank='$nama_bank',cabang_bank='$cabang_bank' WHERE id_rekening='$id'";
    $sql=mysqli_query($conn,$queri);

    if(isset($_POST['ubahbukurekening'])){
        $id_vendor=$hasil['id_vendor'];
        $img7=$_FILES['file_buku_rekening']['name'];
        $tmp7 = $_FILES['file_buku_rekening']['tmp_name'];
        $newimg7 = $id_vendor.$img7;
        $path7 = "../dokumenVendor/".$newimg7;

        if(move_uploaded_file($tmp7, $path7)){
            $queri="UPDATE rekening SET no_rekening='$no_rekening',pemilik_rekening='$pemilik_rekening', nama_bank='$nama_bank',cabang_bank='$cabang_bank', file_buku_rekening='$newimg7' WHERE id_rekening='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }

    if(isset($_POST['ubahrekoran'])){
        $id_vendor=$hasil['id_vendor'];
        $img8=$_FILES['file_rek_koran']['name'];
        $tmp8 = $_FILES['file_rek_koran']['tmp_name'];
        $newimg8 = $id_vendor.$img8;
        $path8 = "../dokumenVendor/".$newimg8;

        if(move_uploaded_file($tmp8, $path8)){
            $queri="UPDATE rekening SET no_rekening='$no_rekening',pemilik_rekening='$pemilik_rekening', nama_bank='$nama_bank',cabang_bank='$cabang_bank', file_rek_koran='$newimg8' WHERE id_rekening='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }

    if($sql)
    {    
    echo "<script> alert ('Rekening Perusahaan berhasil diedit');window.location='dpt_ld';</script>"; 
    }
    else {
    echo "Maaf, Terjadi kesalahan saat mencoba untuk menyimpan data ke database";
    } 
}
} ?>

<?php
// fungsi ubah pajak
function pajak($conn){ 
    $id=$_GET['id'];
    $queri="SELECT * FROM pajak WHERE id_pajak='$id'";
    $sql=mysqli_query($conn,$queri);
    $hasil=mysqli_fetch_assoc($sql);
?>

<div class="card mb-5">
    <div class="card-header bg-primary text-white text-center h5"> Edit Pajak Perusahaan</div>
    <div class="card-body">
    <form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="npwp_vendor">NPWP Perusahaan</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="npwp_vendor" type="text" class="form-control" id="npwp_vendor" value='<?php echo $hasil['npwp_vendor']; ?>' required>
        </div> 
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_npwp_vendor">Fotocopy NPWP Perusahaan</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_npwp_vendor'] ?> <br>
        <input type="checkbox" size="30px" name="ubahnpwpvendor" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_npwp_vendor" id="file_npwp_vendor" accept=".pdf">
    </div>
    </div>

    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="npwp_direktur">NPWP Direktur</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="npwp_direktur" type="text" class="form-control" id="npwp_direktur" value='<?php echo $hasil['npwp_direktur']; ?>' required>
        </div> 
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_npwp_direktur">Fotocopy NPWP Direktur</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_npwp_direktur'] ?> <br>
        <input type="checkbox" size="30px" name="ubahnpwpdirektur" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_npwp_direktur" id="file_npwp_direktur" accept=".pdf" >
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_tanda_daftar">Fotocopy Tanda Daftar Pajak</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_tanda_daftar'] ?> <br>
        <input type="checkbox" size="30px" name="ubahtandadaftar" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_tanda_daftar" id="file_tanda_daftar" accept=".pdf" >
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_lapor_pajak">Fotocopy Lapor Pajak</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_lapor_pajak'] ?> <br>
        <input type="checkbox" size="30px" name="ubahlaporpajak" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_lapor_pajak" id="file_lapor_pajak" accept=".pdf" >
    </div>
    </div>
    <hr>
        <div class="form-group row">
            <div class="mx-auto">
            <button name="simpan" type="submit" class="btn btn-success">Simpan</button>
            <a class="btn btn-danger" href="dpt_ld" role="button">Cancel</a>
            </div>
        </div>
        
    </form>
    </div>
</div>

<?php 
// editProsesPajak
if(isset($_POST['simpan'])){
    $npwp_vendor=$_POST['npwp_vendor'];
    $npwp_direktur=$_POST['npwp_direktur'];

    $queri="UPDATE pajak SET npwp_vendor='$npwp_vendor',npwp_direktur='$npwp_direktur' WHERE id_pajak='$id'";
    $sql=mysqli_query($conn,$queri);

    if(isset($_POST['ubahnpwpvendor'])){
    $id_vendor=$hasil['id_vendor'];
    $img9=$_FILES['file_npwp_vendor']['name'];
    $tmp9 = $_FILES['file_npwp_vendor']['tmp_name'];
    $newimg9 = $id_vendor.$img9;
    $path9 = "../dokumenVendor/".$newimg9;

    if(move_uploaded_file($tmp9, $path9)){
        $queri="UPDATE pajak SET file_npwp_vendor='$newimg9' WHERE id_pajak='$id'";
        $sql=mysqli_query($conn,$queri);
    }
    }

    if(isset($_POST['ubahnpwpdirektur'])){
        $id_vendor=$hasil['id_vendor'];
        $img10=$_FILES['file_npwp_direktur']['name'];
        $tmp10 = $_FILES['file_npwp_direktur']['tmp_name'];
        $newimg10 = $id_vendor.$img10;
        $path10 = "../dokumenVendor/".$newimg10;

    
        if(move_uploaded_file($tmp10, $path10)){
            $queri="UPDATE pajak SET file_npwp_direktur='$newimg10' WHERE id_pajak='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }

    if(isset($_POST['ubahtandadaftar'])){
        $id_vendor=$hasil['id_vendor'];
        $img11=$_FILES['file_tanda_daftar']['name'];
        $tmp11 = $_FILES['file_tanda_daftar']['tmp_name'];
        $newimg11 = $id_vendor.$img11;
        $path11 = "../dokumenVendor/".$newimg11;

        if(move_uploaded_file($tmp11, $path11)){
            $queri="UPDATE pajak SET file_tanda_daftar='$newimg11' WHERE id_pajak='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }

    if(isset($_POST['ubahlaporpajak'])){
        $id_vendor=$hasil['id_vendor'];
        $img12=$_FILES['file_lapor_pajak']['name'];
        $tmp12 = $_FILES['file_lapor_pajak']['tmp_name'];
        $newimg12 = $id_vendor.$img12;
        $path12 = "../dokumenVendor/".$newimg12;

        if(move_uploaded_file($tmp12, $path12)){
            $queri="UPDATE pajak SET file_lapor_pajak='$newimg12' WHERE id_pajak='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }

    if($sql)
    {    
    echo "<script> alert ('Pajak Perusahaan berhasil diedit');window.location='dpt_ld';</script>"; 
    }
    else {
    echo "Maaf, Terjadi kesalahan saat mencoba untuk menyimpan data ke database";
    }
}
} ?>

<?php
// fungsiUbahIjinUsaha
function ijinusaha($conn){
    $id=$_GET['id'];
    $queri="SELECT * FROM Ijin_usaha WHERE id_ijin='$id'";
    $sql=mysqli_query($conn,$queri);
    $hasil=mysqli_fetch_assoc($sql);
?>
<div class="card mb-5">
    <div class="card-header bg-primary text-white text-center h5"> Edit Ijin Usaha Perusahaan</div>

    <div class="card-body">
    <form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="kualifikasi_usaha">Kualifikasi Usaha</label>
        <div class="col-md-10 col-sm-12 ">
        <select name="kualifikasi_usaha" id="kualifikasi_usaha" class="form-control" required>
            <option value ="" disabled='true'> -- Pilih jenis kualifikasi--  </option>
            <option value="Mikro" <?php if($hasil['kualifikasi_usaha']=='Mikro'){echo 'selected';} ?> >Mikro</option>
            <option value="Kecil" <?php if($hasil['kualifikasi_usaha']=='Kecil'){echo 'selected';} ?> >Kecil</option>
            <option value="Menengah" <?php if($hasil['kualifikasi_usaha']=='Menengah'){echo 'selected';} ?> >Menengah</option>
            <option value="Besar" <?php if($hasil['kualifikasi_usaha']=='Besar'){echo 'selected';} ?> >Besar</option>
        </select>
        <!-- <input name="kualifikasi_usaha" type="text" class="form-control" id="kualifikasi_usaha" value="<?php echo $hasil['kualifikasi_usaha'] ?>" required> -->
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="kualifikasi_pengadaan">Kualifikasi Pengadaan</label>
        <div class="col-md-10 col-sm-12 ">
        <select name="kualifikasi_pengadaan" id="kualifikasi_pengadaan" class="form-control" required>
            <option value ="" disabled='true'> -- Pilih jenis kualifikasi--  </option>
            <option value="Konsultasi" <?php if($hasil['kualifikasi_pengadaan']=='Konsultasi'){echo 'selected';}  ?>>Konsultasi</option>
            <option value="Jasa Konstruksi" <?php if($hasil['kualifikasi_pengadaan']=='Jasa Konstruksi'){echo 'selected';}  ?>>Jasa Konstruksi</option>
            <option value="Pengadaan Barang" <?php if($hasil['kualifikasi_pengadaan']=='Pengadaan Barang'){echo 'selected';}  ?>>Pengadaan Barang</option>
            <option value="Jasa Lain" <?php if($hasil['kualifikasi_pengadaan']=='Jasa Lain'){echo 'selected';}  ?>>Jasa Lain</option>
        </select>
        <!-- <input name="kualifikasi_pengadaan" type="text" class="form-control" id="kualifikasi_pengadaan" value="<?php echo $hasil['kualifikasi_pengadaan'] ?>" required> -->
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="pkp">Nomor PKP</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="pkp" type="text" class="form-control" id="pkp" value="<?php echo $hasil['pkp'] ?>" required>
        </div> 
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="nib">NIB</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="nib" type="text" class="form-control" id="nib" value="<?php echo $hasil['nib'] ?>"  required>
        </div> 
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_ijin_usaha">Fotocopy Ijin Usaha</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_ijin_usaha'] ?> <br>
        <input type="checkbox" size="30px" name="ubahijinusaha" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_ijin_usaha" id="file_ijin_usaha" accept=".pdf" >
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_nib">Fotocopy NIB</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_nib'] ?> <br>
        <input type="checkbox" size="30px" name="ubahfilenib" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_nib" id="file_nib" accept=".pdf" >
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_sert_badan_usaha">Fotocopy Sertifikat Badan Usaha</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_sert_badan_usaha'] ?> <br>
        <input type="checkbox" size="30px" name="ubahsertbadanusaha" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_sert_badan_usaha" id="file_sert_badan_usaha" accept=".pdf" >
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_ska_konstruksi">Fotocopy SKA Konstruksi</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_ska_konstruksi'] ?> <br>
        <input type="checkbox" size="30px" name="ubahska" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_ska_konstruksi" id="file_ska_konstruksi" accept=".pdf" >
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_skt_konstruksi">Fotocopy SKT Konstruksi</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_skt_konstruksi'] ?> <br>
        <input type="checkbox" size="30px" name="ubahskt" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_skt_konstruksi" id="file_skt_konstruksi" accept=".pdf" >
    </div>
    </div>
    <hr>
        <div class="form-group row">
            <div class="mx-auto">
            <button name="simpan" type="submit" class="btn btn-success">Simpan</button>
            <a class="btn btn-danger" href="dpt_ld" role="button">Cancel</a>
            </div>
        </div>
    </form>
    </div>
</div>

<?php 
// editProsesIjinUsaha
if(isset($_POST['simpan'])){ 
    $kualifikasi_usaha=$_POST['kualifikasi_usaha'];
    $kualifikasi_pengadaan=$_POST['kualifikasi_pengadaan'];
    $pkp=$_POST['pkp'];
    $nib=$_POST['nib'];

    $queri="UPDATE ijin_usaha SET kualifikasi_usaha='$kualifikasi_usaha',kualifikasi_pengadaan='$kualifikasi_pengadaan',pkp='$pkp',nib='$nib' WHERE id_ijin='$id'";
    $sql=mysqli_query($conn,$queri);

    if(isset($_POST['ubahijinusaha'])){
        $id_vendor=$hasil['id_vendor'];
        $img13=$_FILES['file_ijin_usaha']['name'];
        $tmp13 = $_FILES['file_ijin_usaha']['tmp_name'];
        $newimg13 = $id_vendor.$img13;
        $path13 = "../dokumenVendor/".$newimg13;
    
        if(move_uploaded_file($tmp13, $path13)){
            $queri="UPDATE ijin_usaha SET file_ijin_usaha='$newimg13' WHERE id_ijin='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }
    if(isset($_POST['ubahfilenib'])){
        $id_vendor=$hasil['id_vendor'];
        $img14=$_FILES['file_nib']['name'];
        $tmp14 = $_FILES['file_nib']['tmp_name'];
        $newimg14 = $id_vendor.$img14;
        $path14 = "../dokumenVendor/".$newimg14;
    
        if(move_uploaded_file($tmp14, $path14)){
            $queri="UPDATE ijin_usaha SET file_nib='$newimg14' WHERE id_ijin='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }

    if(isset($_POST['ubahsertbadanusaha'])){
        $id_vendor=$hasil['id_vendor'];
        $img15=$_FILES['file_sert_badan_usaha']['name'];
        $tmp15 = $_FILES['file_sert_badan_usaha']['tmp_name'];
        $newimg15 = $id_vendor.$img15;
        $path15 = "../dokumenVendor/".$newimg15;
    
        if(move_uploaded_file($tmp15, $path15)){
            $queri="UPDATE ijin_usaha SET file_sert_badan_usaha='$newimg15' WHERE id_ijin='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }
    if(isset($_POST['ubahska'])){
        $id_vendor=$hasil['id_vendor'];
        $img16=$_FILES['file_ska_konstruksi']['name'];
        $tmp16 = $_FILES['file_ska_konstruksi']['tmp_name'];
        $newimg16 = $id_vendor.$img16;
        $path16 = "../dokumenVendor/".$newimg16;
    
        if(move_uploaded_file($tmp16, $path16)){
            $queri="UPDATE ijin_usaha SET file_ska_konstruksi='$newimg16' WHERE id_ijin='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }
    if(isset($_POST['ubahskt'])){
        $id_vendor=$hasil['id_vendor'];
        $img17=$_FILES['file_skt_konstruksi']['name'];
        $tmp17 = $_FILES['file_skt_konstruksi']['tmp_name'];
        $newimg17 = $id_vendor.$img17;
        $path17 = "../dokumenVendor/".$newimg17;
    
        if(move_uploaded_file($tmp17, $path17)){
            $queri="UPDATE ijin_usaha SET file_skt_konstruksi='$newimg17' WHERE id_ijin='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }
    if($sql)
    {    
    echo "<script> alert ('Ijin Usaha Perusahaan berhasil diedit');window.location='dpt_ld';</script>"; 
    }
    else {
    echo "Maaf, Terjadi kesalahan saat mencoba untuk menyimpan data ke database";
    } 
}
} ?>

<?php
// fungsiUbahTenagaKerja
function tenagakerja($conn){
    $id=$_GET['id'];
    $queri="SELECT * FROM tenagakerja WHERE id_tenagakerja='$id'";
    $sql=mysqli_query($conn,$queri);
    $hasil=mysqli_fetch_assoc($sql);
?>
<div class="card mb-5">
    <div class="card-header bg-primary text-white text-center h5"> Edit Tenaga Kerja Perusahaan</div>

    <div class="card-body">
    <form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_dok_teknik">Dokumen Tenaga Teknis</label>
    <div class="col-md-10 col-sm-12">
        <?php echo $hasil['file_dok_teknik'] ?> <br>
        <input type="checkbox" size="30px" name="ubahdokteknik" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_dok_teknik" id="file_dok_teknik" accept=".pdf">
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_ktp">KTP Tenaga Teknis</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_ktp'] ?> <br>
        <input type="checkbox" size="30px" name="ubahfilektp" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_ktp" id="file_ktp" accept=".pdf">
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_skt">SKT Tenaga Teknis</label>
    <div class="col-md-10 col-sm-12">
        <?php echo $hasil['file_skt'] ?> <br>
        <input type="checkbox" size="30px" name="ubahfileskt" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_skt" id="file_skt" accept=".pdf">
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_ijazah">Ijazah Tenaga Teknis</label>
    <div class="col-md-10 col-sm-12">
    <?php echo $hasil['file_ijazah'] ?> <br>
        <input type="checkbox" size="30px" name="ubahfileijazah" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_ijazah" id="file_ijazah" accept=".pdf">
    </div>
    </div>

    <hr>
        <div class="form-group row">
            <div class="mx-auto">
            <button name="simpan" type="submit" class="btn btn-success">Simpan</button>
            <a class="btn btn-danger" href="dpt_ld" role="button">Cancel</a>
            </div>
        </div>
    </form>
    </div>
</div>

<?php 
// prosesEditTenagaKerja
if(isset($_POST['simpan'])){
    if(isset($_POST['ubahdokteknik'])){
        $id_vendor=$hasil['id_vendor'];
        $img18=$_FILES['file_dok_teknik']['name'];
        $tmp18 = $_FILES['file_dok_teknik']['tmp_name'];
        $newimg18 = $id_vendor.$img18;
        $path18 = "../dokumenVendor/".$newimg18;

        if(move_uploaded_file($tmp18, $path18)){
            $queri="UPDATE tenagakerja SET file_dok_teknik='$newimg18' WHERE id_tenagakerja='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }
    if(isset($_POST['ubahfilektp'])){
        $id_vendor=$hasil['id_vendor'];
        $img19=$_FILES['file_ktp']['name'];
        $tmp19 = $_FILES['file_ktp']['tmp_name'];
        $newimg19 = $id_vendor.$img19;
        $path19 = "../dokumenVendor/".$newimg19;

        if(move_uploaded_file($tmp19, $path19)){
            $queri="UPDATE tenagakerja SET file_ktp='$newimg19' WHERE id_tenagakerja='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }
    if(isset($_POST['ubahfileskt'])){
        $id_vendor=$hasil['id_vendor'];
        $img20=$_FILES['file_skt']['name'];
        $tmp20 = $_FILES['file_skt']['tmp_name'];
        $newimg20 = $id_vendor.$img20;
        $path20 = "../dokumenVendor/".$newimg20;

        if(move_uploaded_file($tmp20, $path20)){
            $queri="UPDATE tenagakerja SET file_skt='$newimg20' WHERE id_tenagakerja='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }
    if(isset($_POST['ubahfileijazah'])){
        $id_vendor=$hasil['id_vendor'];
        $img21=$_FILES['file_ijazah']['name'];
        $tmp21 = $_FILES['file_ijazah']['tmp_name'];
        $newimg21 = $id_vendor.$img21;
        $path21 = "../dokumenVendor/".$newimg21;

        if(move_uploaded_file($tmp21, $path21)){
            $queri="UPDATE tenagakerja SET file_ijazah='$newimg21' WHERE id_tenagakerja='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }

    if($sql)
    {    
    echo "<script> alert ('Tenaga Kerja Perusahaan berhasil diedit');window.location='dpt_ld';</script>"; 
    }
    else {
    echo "Maaf, Terjadi kesalahan saat mencoba untuk menyimpan data ke database";
    }
}
} ?>

<?php
// fungsiUbahKeuangan
function keuangan($conn){
    $id=$_GET['id'];
    $queri="SELECT * FROM keuangan WHERE id_keuangan='$id'";
    $sql=mysqli_query($conn,$queri);
    $hasil=mysqli_fetch_assoc($sql);
?>
<div class="card mb-5">
    <div class="card-header bg-primary text-white text-center h5"> Edit Keuangan Perusahaan</div>

    <div class="card-body">
    <form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_neraca">Laporan Neraca Perusahaan</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_neraca'] ?> <br>
        <input type="checkbox" size="30px" name="ubahneraca" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_neraca" id="file_neraca" accept=".pdf">
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_labarugi">Laporan Laba Rugi Perusahaan</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_labarugi'] ?> <br>
        <input type="checkbox" size="30px" name="ubahlabarugi" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_labarugi" id="file_labarugi" accept=".pdf">
    </div>
    </div>
    <hr>
        <div class="form-group row">
            <div class="mx-auto">
            <button name="simpan" type="submit" class="btn btn-success">Simpan</button>
            <a class="btn btn-danger" href="dpt_ld" role="button">Cancel</a>
            </div>
        </div>
    </form>
    </div>
</div>

<?php 
// editProsesKeuangan
if(isset($_POST['simpan'])){
    if(isset($_POST['ubahneraca'])){
        $id_vendor=$hasil['id_vendor'];
        $img22=$_FILES['file_neraca']['name'];
        $tmp22 = $_FILES['file_neraca']['tmp_name'];
        $newimg22 = $id_vendor.$img22;
        $path22 = "../dokumenVendor/".$newimg22;

        if(move_uploaded_file($tmp22, $path22)){
            $queri="UPDATE keuangan SET file_neraca='$newimg22' WHERE id_keuangan='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }
    if(isset($_POST['ubahlabarugi'])){
        $id_vendor=$hasil['id_vendor'];
        $img23=$_FILES['file_labarugi']['name'];
        $tmp23 = $_FILES['file_labarugi']['tmp_name'];
        $newimg23 = $id_vendor.$img23;
        $path23 = "../dokumenVendor/".$newimg23;

        if(move_uploaded_file($tmp23, $path23)){
            $queri="UPDATE keuangan SET file_labarugi='$newimg23' WHERE id_keuangan='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }

    if($sql)
    {    
    echo "<script> alert ('Keuangan Perusahaan berhasil diedit');window.location='dpt_ld';</script>"; 
    }
    else {
    echo "Maaf, Terjadi kesalahan saat mencoba untuk menyimpan data ke database";
    }
}
}
?>

<?php
// fungsiUbahDokumenPelengkap
function dokumen($conn){
    $id=$_GET['id'];
    $queri="SELECT * FROM dokumen WHERE id_dokumen='$id'";
    $sql=mysqli_query($conn,$queri);
    $hasil=mysqli_fetch_assoc($sql);
?>
<div class="card mb-5">
    <div class="card-header bg-primary text-white text-center h5"> Edit Dokumen Pelengkap Perusahaan</div>

    <div class="card-body">
    <form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_ikutserta">Formulir Keikutsertaan</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_ikutserta'] ?> <br>
        <input type="checkbox" size="30px" name="ubahikutserta" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_ikutserta" id="file_ikutserta" accept=".pdf">
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_suratkuasa">Surat Kuasa</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_suratkuasa'] ?> <br>
        <input type="checkbox" size="30px" name="ubahsuratkuasa" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_suratkuasa" id="file_suratkuasa" accept=".pdf">
    </div>
    </div>

    <div class="form-group row">
    <label class="col-md-2 col-sm-12  col-form-label" for="file_pendaftaran">Formulir Pendaftaran</label>
    <div class="col-md-10 col-sm-12 ">
        <?php echo $hasil['file_pendaftaran'] ?> <br>
        <input type="checkbox" size="30px" name="ubahpendaftaran" value="true"> Ceklis jika ingin mengubah file<br>
        <input type="file" name="file_pendaftaran" id="file_pendaftaran" accept=".pdf">
    </div>
    </div>
    <hr>
        <div class="form-group row">
            <div class="mx-auto">
            <button name="simpan" type="submit" class="btn btn-success">Simpan</button>
            <a class="btn btn-danger" href="dpt_ld" role="button">Cancel</a>
            </div>
        </div>
    </form>
    </div>
</div>


<?php
// editProsesDokumenPelengkap
if(isset($_POST['simpan'])){
    if(isset($_POST['ubahikutserta'])){
        $id_vendor=$hasil['id_vendor'];
        $img24=$_FILES['file_ikutserta']['name'];
        $tmp24 = $_FILES['file_ikutserta']['tmp_name'];
        $newimg24 = $id_vendor.$img24;
        $path24 = "../dokumenVendor/".$newimg24;

        if(move_uploaded_file($tmp24, $path24)){
            $queri="UPDATE dokumen SET file_ikutserta='$newimg24' WHERE id_dokumen='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }
    if(isset($_POST['ubahsuratkuasa'])){
        $id_vendor=$hasil['id_vendor'];
        $img25=$_FILES['file_suratkuasa']['name'];
        $tmp25 = $_FILES['file_suratkuasa']['tmp_name'];
        $newimg25 = $id_vendor.$img25;
        $path25 = "../dokumenVendor/".$newimg25;

        if(move_uploaded_file($tmp25, $path25)){
            $queri="UPDATE dokumen SET file_suratkuasa='$newimg25' WHERE id_dokumen='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }
    if(isset($_POST['ubahpendaftaran'])){
        $id_vendor=$hasil['id_vendor'];
        $img26=$_FILES['file_pendaftaran']['name'];
        $tmp26 = $_FILES['file_pendaftaran']['tmp_name'];
        $newimg26 = $id_vendor.$img26;
        $path26 = "../dokumenVendor/".$newimg26;

        if(move_uploaded_file($tmp26, $path26)){
            $queri="UPDATE dokumen SET file_pendaftaran='$newimg26' WHERE id_dokumen='$id'";
            $sql=mysqli_query($conn,$queri);
        }
    }
    if($sql)
    {    
    echo "<script> alert ('Dokumen Pelengkap Perusahaan berhasil diedit');window.location='dpt_ld';</script>"; 
    }
    else {
    echo "Maaf, Terjadi kesalahan saat mencoba untuk menyimpan data ke database";
    }
}
}
?>

<!-- endcontainer -->
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