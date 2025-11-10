<?php
include 'menu.php';
include '../link.php';
ob_start()
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
  <div class="card-header bg-primary text-white text-center h6"> Data Perusahaan</div>
  <div class="card-body table-responsive">
    <?php $id_vendor=$_SESSION['id_vendor']; ?>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="identitas-tab" data-toggle="tab" data-target="#identitas" type="button" role="tab" aria-controls="identitas" aria-selected="true">Identitas Perusahaan</button>
        </li>

    <?php 
    $query="SELECT * FROM data_vendor WHERE id_vendor='$id_vendor'";
    $sql=mysqli_query($conn,$query);
    $cekRes=mysqli_num_rows($sql);
    if ($cekRes!=1){
        echo "</ul>";
    }
    else 
    {
    ?>    
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
    <?php } ?>

    <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="identitas" role="tabpanel" aria-labelledby="identitas-tab">
        <?php  
        $id_vendor=$_SESSION['id_vendor'];

        $queriId="SELECT * FROM data_vendor WHERE id_vendor='$id_vendor'";
        $sqlId=mysqli_query($conn,$queriId);
        $hasilId=mysqli_fetch_assoc($sqlId);
        $cekID=mysqli_num_rows($sqlId);
        
        if($cekID!=1){ ?>
            Belum ada data
            <br>
            <!-- Button to Open the Modal -->
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal_identitas">
            Klik untuk lengkapi data
            </button>

            <!-- The Modal -->
            <div class="modal" id="myModal_identitas">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Data Perusahaan</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
                    <div class="form-group row"> 
                        <label class="col-md-2 col-sm-12 col-form-label" for="id_vendor">ID Anggota:</label>
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
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-sm-12  col-form-label" for="nama_akhir">Nama akhir</label>
                            <div class="col-md-10 col-sm-12 ">
                            <select name="nama_akhir" id="nama_akhir" class="form-control" >
                                    <option value =""> -- Pilih nama akhir--  </option>
                                    <option value='Tbk'> Tbk </option>
                                    <option value='Ltd'> Ltd </option> 
                                    <option value=''> Tidak ada </option>            
                                </select>
                            </div>
                        </div>
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
                            <option value="Swasta">Private</option>
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

                        <hr>
                            <div class="form-group row">
                                <div class="mx-auto">
                                <button name="simpanId" type="submit" class="btn btn-success">Simpan</button>
                                <a class="btn btn-danger" href="dpt_ld" role="button">Cancel</a>
                                </div>
                            </div>
                </form>
                </div>

                <?php
                // saveProcess
                if(isset($_POST['simpanId'])){
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
                    move_uploaded_file($tmp1, $path1);

                    $img2=$_FILES['kop_surat']['name'];
                    $tmp2 = $_FILES['kop_surat']['tmp_name'];
                    $newimg2 = $id_vendor.$img2;
                    $path2 = "../dokumenVendor/".$newimg2;
                    move_uploaded_file($tmp2, $path2);

                    $img3=$_FILES['pengalaman']['name'];
                    $tmp3 = $_FILES['pengalaman']['tmp_name'];
                    $newimg3 = $id_vendor.$img3;
                    $path3 = "../dokumenVendor/".$newimg3;
                    move_uploaded_file($tmp3, $path3);

                    $queri="INSERT INTO data_vendor(id_vendor, nama_awal, nama_vendor, nama_akhir, tanggal_berdiri, kepemilikan, telp, website, file_akta, file_kop_surat, file_pengalaman) VALUES ('$id_vendor','$nama_awal','$nama','$nama_akhir','$tanggal_berdiri','$kepemilikan','$telp','$website','$newimg1','$newimg2','$newimg3')";
                    $sql=mysqli_query($conn,$queri);
                    
                    if($sql){
                        echo "<script>alert ('Berhasil manambahkan Identitas Perusahaan');window.location='dpt_ld'</script>";
                    }
                    else {
                    echo "<script>alert ('ERROR..! Terjadi kegagalan dalam proses penyimpanan data');</script>";
                    } 

                }
             ?>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>
            </div>
            </div>

    <?php }
    else{
    ?>
    <table class="table table-hover table-bordered mt-3">
    <tr>
        <th>ID Anggota</th>
        <th><?php echo $hasilId['id_vendor'] ?></th>
    </tr>
    <tr>
        <th>Nama awal</th>
        <th><?php echo $hasilId['nama_awal'] ?></th>
    </tr>
    <tr>
        <th>Nama Perusahaan</th>
        <th><?php echo $hasilId['nama_vendor'] ?></th>
    </tr>
    <tr>
        <th>Nama akhir</th>
        <th><?php echo $hasilId['nama_akhir'] ?></th>
    </tr>
    <tr>
        <th>Tanggal berdiri</th>
        <th><?php echo $hasilId['tanggal_berdiri'] ?></th>
    </tr>
    <tr>
        <th>Kepemilikan</th>
        <th><?php echo $hasilId['kepemilikan'] ?></th>
    </tr> 
    <tr>
        <th>Nomor Telepon</th>
        <th><?php echo $hasilId['telp'] ?></th>
    </tr>
    <tr>
        <th>Website</th>
        <th><?php echo $hasilId['website'] ?></th>
    </tr>

    <tr>
        <th>Akta Pendirian Perusahaan</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilId['file_akta'] ?>'target=blank><?php echo $hasilId['file_akta'] ?>  </a>
        </th>
    </tr>

    <tr>
        <th>Kop Surat Perusahaan</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilId['file_kop_surat'] ?>'target=blank><?php echo $hasilId['file_kop_surat'] ?>  </a>
        </th>
    </tr>

    <tr>
        <th>Daftar Pengalaman</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilId['file_pengalaman'] ?>'target=blank><?php echo $hasilId['file_pengalaman'] ?> </a>
        </th>  
    </tr>   
    </table>
    <?php $id=$hasilId['id_vendor']; ?>
        <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=identitas&id=<?php echo $id;?>' class='btn btn-sm btn-info fas fa-edit'> Ubah</a>
            </div>
        </div>
    <?php } ?>
    </div>

<!-- alamatVendor -->
    <div class="tab-pane fade" id="alamat" role="tabpanel" aria-labelledby="alamat-tab">
    
    <?php 
        $queriAlamat="SELECT * FROM alamat WHERE id_vendor='$id_vendor'";
        $sqlAlamat=mysqli_query($conn, $queriAlamat);
        $hasilAlamat=mysqli_fetch_assoc($sqlAlamat);
        $cekAlamat=mysqli_num_rows($sqlAlamat);
        
        if($cekAlamat!=1){ ?>
            Belum ada data
            <br>
            <!-- Button to Open the Modal -->
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal_alamat">
            Klik untuk lengkapi data alamat
            </button>

            <!-- The Modal -->
            <div class="modal" id="myModal_alamat">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Alamat Perusahaan</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group row"> 
                        <label class="col-md-2 col-sm-12 col-form-label" for="id_vendor">ID Anggota:</label>
                        <div class="col-md-10 col-sm-12">
                        <input name='id_vendor' type="text" class="form-control" id="id_vendor" value="<?php echo $_SESSION['id_vendor']?> " readonly> 
                        </div>
                    </div>

                    <div class="form-group row"> 
                        <label class="col-md-2 col-sm-12 col-form-label" for="provinsi"> Provinsi:</label>
                        <div class="col-md-10 col-sm-12">
                        <select name="provinsi" id="provinsi" class="form-control" required>
                            <option value="">--silahkan pilih provinsi--</option>
                            <?php
                            $query=mysqli_query($conn, "SELECT * FROM provinsi ORDER BY id_prov");
                            while($row=mysqli_fetch_array($query)){
                            ?>
                            <option value="<?php echo $row['id_prov']; ?>"> <?php echo $row['nama_prov'] ?> </option>
                            <?php } ?>
                        </select>
                        </div>
                    </div>

                    <div class="form-group row"> 
                    <label class="col-md-2 col-sm-12 col-form-label" for="kabupaten"> Kabupaten/ Kota:</label>
                    <div class="col-md-10 col-sm-12">
                    <select name="kabupaten" id="kabupaten" class="form-control">
                        <option value="">--silahkan pilih kabupaten/ Kota--</option>
                        <?php
                        $query=mysqli_query($conn,"SELECT *, provinsi.id_prov, provinsi.nama_prov 
                        FROM kabupaten
                        INNER JOIN provinsi ON kabupaten.id_prov=provinsi.id_prov 
                        ORDER BY nama_kab");
                        while($row=mysqli_fetch_array($query)){ 
                        ?>
                        <option id="kabupaten" class="<?php echo $row['id_prov']; ?>" value="<?php echo $row['id_kab']; ?>"> <?php  echo $row['nama_kab']?></option>
                        <?php } ?>
                    </select>  
                    </div>
                </div>

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
                
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button name="simpanAlamat" type="submit" class="btn btn-sm btn-success mx-auto">Simpan</button>
                </div>
                </form>

                <?php
                // saveProcess
                if(isset($_POST['simpanAlamat'])){
                    $id_vendor=$_POST['id_vendor'];
                    $provinsi=$_POST['provinsi'];
                    $kabupaten=$_POST['kabupaten'];
                    $kecamatan=$_POST['kecamatan'];
                    $kelurahan=$_POST['kelurahan'];
                    $jalan=$_POST['jalan'];
                    $kodepos=$_POST['kodepos'];

                    $img4=$_FILES['domisili']['name'];
                    $tmp4 = $_FILES['domisili']['tmp_name'];
                    $newimg4 = $id_vendor.$img4;
                    $path4 = "../dokumenVendor/".$newimg4;
                    move_uploaded_file($tmp4, $path4);

                    $queri="INSERT INTO alamat(id_vendor, provinsi, kabupaten, kecamatan, kelurahan, jalan, kodepos, file_domisili) VALUES ('$id_vendor','$provinsi','$kabupaten','$kecamatan','$kelurahan','$jalan','$kodepos','$newimg4')";
                    $sql=mysqli_query($conn,$queri);
                    
                    if($sql){  
                        echo "<script>alert ('Berhasil manambahkan data');window.location='dpt_ld'</script>";
                    }
                    else {
                    echo "<script>alert ('ERROR..! Terjadi kegagalan dalam proses penyimpanan data');</script>";
                    } 
                }
                // endSaveProcess
             ?>
            </div>
            </div>
            </div>
        <?php }
    else{
    ?>
    <table class="table table-hover table-hover table-bordered mt-3">
    
        <?php
            $query= "SELECT *, provinsi.*, kabupaten.*
                    FROM alamat
                    INNER JOIN provinsi ON alamat.provinsi=provinsi.id_prov
                    INNER JOIN kabupaten on alamat.kabupaten=kabupaten.id_kab";
                   
            $sql = mysqli_query($conn, $query);
            $res= mysqli_fetch_array($sql);
        ?>

    <tr>
        <th>Provinsi</th>
        <th><?php echo $res['nama_prov'] ?></th>
    </tr>

    
    <tr>
        <th>Kota/ Kabupaten</th>
        <th><?php echo $res['nama_kab'] ?></th>
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
        <th><a href='dpt_ld_v?id=<?php echo $hasilAlamat['file_domisili'] ?>'target=blank><?php echo $hasilAlamat['file_domisili'] ?> </a>
    </th>     
    </table>
    <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=alamat&id= <?php echo $hasilAlamat['id_alamat'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
        <?php } ?>
    </div>
 <!-- endAlamatVendor -->
    
 <!-- pengurusVendor -->
    <div class="tab-pane fade" id="pengurus" role="tabpanel" aria-labelledby="pengurus-tab">

    <?php 
        $queriPeng="SELECT * FROM pengurus WHERE id_vendor='$id_vendor'";
        $sqlPeng=mysqli_query($conn, $queriPeng);
        $hasilPeng=mysqli_fetch_assoc($sqlPeng);
        $cekPeng=mysqli_num_rows($sqlPeng);
        if($cekPeng!=1)
        { ?>
            Belum ada data
        <br>
        <!-- Button to Open the Modal -->
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal_pengurus">
            Klik untuk lengkapi data pengurus
            </button>

        <!-- The Modal -->
        <div class="modal" id="myModal_pengurus">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
        
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title">Pengurus Perusahaan</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group row"> 
            <label class="col-md-2 col-sm-12 col-form-label" for="id_vendor">ID Anggota:</label>
            <div class="col-md-10 col-sm-12">
            <input name='id_vendor' type="text" class="form-control" id="id_vendor" value="<?php echo $_SESSION['id_vendor']?> " readonly> 
            </div>
        </div>
        <div class="form-group row">
        <label class="col-md-2 col-sm-12  col-form-label" for="pemilik">Nama Pemilik</label>
        <div class="col-md-10 col-sm-12 ">
        <input name="pemilik" type="text" class="form-control" id="pemilik" required>
        </div> 
        </div>
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

        </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button name="simpanPeng" type="submit" class="btn btn-sm btn-success mx-auto">Simpan</button>
                </div>
        </form>

        <?php
        // saveProcess
        if(isset($_POST['simpanPeng'])){
            $id_vendor=$_POST['id_vendor'];
            $pemilik=$_POST['pemilik'];
            $jenis_id=$_POST['jenis_id'];
            $no_id_pemilik=$_POST['no_id_pemilik'];

            $img5=$_FILES['kartuPemilik']['name'];
            $tmp5 = $_FILES['kartuPemilik']['tmp_name'];
            $newimg5 = $id_vendor.$img5;
            $path5 = "../dokumenVendor/".$newimg5;
            move_uploaded_file($tmp5, $path5);

            $direktur=$_POST['direktur'];
            $jenis_id_direktur=$_POST['jenis_id_direktur'];
            $no_id_direktur=$_POST['no_id_direktur'];

            $img6=$_FILES['kartuDirektur']['name'];
            $tmp6 = $_FILES['kartuDirektur']['tmp_name'];
            $newimg6 = $id_vendor.$img6;
            $path6 = "../dokumenVendor/".$newimg6;
            move_uploaded_file($tmp6, $path6);

            $query="INSERT INTO pengurus(id_vendor, pemilik, kartu_pemilik, no_kartu_pemilik, direktur, kartu_direktur, no_kartu_direktur, file_kartu_pemilik, file_kartu_direktur) VALUES ('$id_vendor','$pemilik','$jenis_id','$no_id_pemilik','$direktur','$jenis_id_direktur','$no_id_direktur','$newimg5 ','$newimg6')";
            $sql=mysqli_query($conn,$query);
            if($sql){
               echo "<script>alert ('Berhasil manambahkan Identitas Perusahaan');window.location='dpt_ld'</script>";
            }
            else{
                echo "<script>alert ('ERROR..! Terjadi kegagalan dalam proses penyimpanan data');</script>";
            }
        }
        // endSaveProcess
        ?>
        </div>
        </div>
        </div>

    <?php 
    }
    else{ ?>

    <table class="table table-hover table-bordered mt-3">
    <tr>
        <th>Nama Pemilik</th>
        <th><?php echo $hasilPeng['pemilik'] ?></th>
    </tr>
    <tr>
        <th>Jenis Identitas Pemilik</th>
        <th><?php echo $hasilPeng['kartu_pemilik'] ?></th>
    </tr>
    <tr>
        <th>No. Identitas Pemilik</th>
        <th><?php echo $hasilPeng['no_kartu_pemilik'] ?></th>
    </tr>
    <tr>
        <th>Identitas Pemilik</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilPeng['file_kartu_pemilik'] ?>' target=blank><?php echo $hasilPeng['file_kartu_pemilik'] ?> </a>
        </th>
    </tr> 
    
    <tr>
        <th>Nama Direktur</th>
        <th><?php echo $hasilPeng['direktur'] ?></th>
    </tr>
    <tr>
        <th>Jenis Identitas Direktur</th>
        <th><?php echo $hasilPeng['kartu_direktur'] ?></th>
    </tr>
    <tr>
        <th>No. Identitas Direktur</th>
        <th><?php echo $hasilPeng['no_kartu_direktur'] ?></th>
    </tr>

    <tr>
        <th>Identitas Direksi</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilPeng['file_kartu_direktur'] ?>' target=blank><?php echo $hasilPeng['file_kartu_direktur'] ?> </a>
    </th>
    </tr>          
    </table>
        <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=pengurus&id= <?php echo $hasilPeng['id_pengurus'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
        <?php } ?>
    </div>


<!-- rekening -->
    <div class="tab-pane fade" id="rekening" role="tabpanel" aria-labelledby="rekening-tab">
   
    <?php
    $queriRek="SELECT * FROM rekening WHERE id_vendor='$id_vendor'";
    $sqlRek=mysqli_query($conn, $queriRek);
    $cekRek=mysqli_num_rows($sqlRek);
    $hasilRek=mysqli_fetch_assoc($sqlRek);
    
    if($cekRek!=1){ ?>
        Belum ada data
        <br>
        <!-- Button to Open the Modal -->
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal_rekening">
            Klik untuk lengkapi data Rekening Perusahaan
            </button>

        <!-- The Modal -->
        <div class="modal" id="myModal_rekening">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
        
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title">Rekening Perusahaan</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">

        <div class="form-group row"> 
            <label class="col-md-2 col-sm-12 col-form-label" for="id_vendor">ID Anggota:</label>
            <div class="col-md-10 col-sm-12">
            <input name='id_vendor' type="text" class="form-control" id="id_vendor" value="<?php echo $_SESSION['id_vendor']?> " readonly> 
            </div>
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

    </div>
        <!-- Modal footer -->
        <div class="modal-footer">
            <button name="simpanRek" type="submit" class="btn btn-sm btn-success mx-auto">Simpan</button>
        </div>
    </form>

    <?php
    // saveProcess
    if(isset($_POST['simpanRek'])){
        $id_vendor=$_POST['id_vendor'];
        $no_rekening=$_POST['no_rekening'];
        $pemilik_rekening=$_POST['pemilik_rekening'];
        $nama_bank=$_POST['nama_bank'];
        $cabang_bank=$_POST['cabang_bank'];

        $img7=$_FILES['file_buku_rekening']['name'];
        $tmp7 = $_FILES['file_buku_rekening']['tmp_name'];
        $newimg7 = $id_vendor.$img7;
        $path7 = "../dokumenVendor/".$newimg7;
        move_uploaded_file($tmp7, $path7);

        $img8=$_FILES['file_rek_koran']['name'];
        $tmp8 = $_FILES['file_rek_koran']['tmp_name'];
        $newimg8 = $id_vendor.$img8;
        $path8 = "../dokumenVendor/".$newimg8;
        move_uploaded_file($tmp8, $path8);

        $query="INSERT INTO rekening(id_vendor, no_rekening, pemilik_rekening, nama_bank, cabang_bank, file_rek_koran, file_buku_rekening) VALUES ('$id_vendor','$no_rekening','$pemilik_rekening','$nama_bank','$cabang_bank','$newimg7','$newimg8')";
        $sql=mysqli_query($conn,$query);
        if($sql){
            echo "<script>alert ('Berhasil manambahkan Data Rekening');window.location='dpt_ld'</script>";
        }
        else {
        echo "<script>alert ('ERROR..! Terjadi kegagalan dalam proses penyimpanan data');</script>";
        }

    }
    // endSaveProcess
    ?>
        </div>
        </div>
        </div>
    <?php }
    else{ ?> 
    <table class="table table-hover table-hover table-bordered mt-3">
    <tr>
        <th>Nomor Rekening Perusahaan</th>
        <th><?php echo $hasilRek['no_rekening'] ?></th>
    </tr>
    <tr>    
        <th>Pemilik Rekening</th>
        <th><?php echo $hasilRek['pemilik_rekening'] ?></th>
    </tr>
    <tr>
        <th>Nama Bank</th>
        <th><?php echo $hasilRek['nama_bank'] ?></th>
    </tr>
    <tr>
        <th>Cabang bank</th>
        <th><?php echo $hasilRek['cabang_bank'] ?></th>
    </tr>

    <tr>
        <th>Fotocopy Buku Rekening</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilRek['file_buku_rekening'] ?>' target=blank><?php echo $hasilRek['file_buku_rekening'] ?> </a>
        </th>
    </tr>

    <tr>
        <th>Fotocopy Rekening Koran 3 Bulan Terakhir</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilRek['file_rek_koran'] ?>' target=blank><?php echo $hasilRek['file_rek_koran'] ?> </a>
        </th>
    </tr>
    </table>
    <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=rekening&id= <?php echo $hasilRek['id_rekening'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
    </div>
    <?php } ?>
    </div>
   
        
<!-- EndRekening -->

<!-- pajak -->
    <div class="tab-pane fade" id="pajak" role="tabpanel" aria-labelledby="pajak-tab">

    <?php 
        $queriPajak="SELECT * FROM pajak WHERE id_vendor='$id_vendor'";
        $sqlPajak=mysqli_query($conn, $queriPajak);
        $hasilPajak=mysqli_fetch_assoc($sqlPajak);
        $cekPajak=mysqli_num_rows($sqlPajak);
        if($cekPajak!=1){ ?>
            Belum ada data
        <br>
        <!-- Button to Open the Modal -->
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal_pajak">
            Klik untuk lengkapi data Pajak
            </button>

        <!-- The Modal -->
        <div class="modal" id="myModal_pajak">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
        
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title">Pajak Perusahaan</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
            
        <!-- Modal body -->
        <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">

        <div class="form-group row"> 
            <label class="col-md-2 col-sm-12 col-form-label" for="id_vendor">ID Anggota:</label>
            <div class="col-md-10 col-sm-12">
            <input name='id_vendor' type="text" class="form-control" id="id_vendor" value="<?php echo $_SESSION['id_vendor']?> " readonly> 
            </div>
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

    </div>
        <!-- Modal footer -->
        <div class="modal-footer">
            <button name="simpanPajak" type="submit" class="btn btn-sm btn-success mx-auto">Simpan</button>
        </div>
    </form>

    <?php
        // saveProcess
        if(isset($_POST['simpanPajak'])){
            $id_vendor=$_POST['id_vendor'];
            $npwp_vendor=$_POST['npwp_vendor'];
        $npwp_direktur=$_POST['npwp_direktur'];

        $img9=$_FILES['file_npwp_vendor']['name'];
        $tmp9 = $_FILES['file_npwp_vendor']['tmp_name'];
        $newimg9 = $id_vendor.$img9;
        $path9 = "../dokumenVendor/".$newimg9;
        move_uploaded_file($tmp9, $path9);

        $img10=$_FILES['file_npwp_direktur']['name'];
        $tmp10 = $_FILES['file_npwp_direktur']['tmp_name'];
        $newimg10 = $id_vendor.$img10;
        $path10 = "../dokumenVendor/".$newimg10;
        move_uploaded_file($tmp10, $path10);

        $img11=$_FILES['file_tanda_daftar']['name'];
        $tmp11 = $_FILES['file_tanda_daftar']['tmp_name'];
        $newimg11 = $id_vendor.$img11;
        $path11 = "../dokumenVendor/".$newimg11;
        move_uploaded_file($tmp11, $path11);

        $img12=$_FILES['file_lapor_pajak']['name'];
        $tmp12 = $_FILES['file_lapor_pajak']['tmp_name'];
        $newimg12 = $id_vendor.$img12;
        $path12 = "../dokumenVendor/".$newimg12;
        move_uploaded_file($tmp12, $path12);
            
        $query="INSERT INTO pajak(id_vendor, npwp_vendor, npwp_direktur, file_tanda_daftar, file_npwp_vendor, file_npwp_direktur, file_lapor_pajak) VALUES ('$id_vendor','$npwp_vendor','$npwp_direktur','$newimg11','$newimg9','$newimg10','$newimg12')";
        $sql=mysqli_query($conn,$query);
        if($sql){
               echo "<script>alert ('Berhasil manambahkan data');window.location='dpt_ld'</script>";
            }
            else{
                echo "<script>alert ('ERROR..! Terjadi kegagalan dalam proses penyimpanan data');</script>";
            }
        }
        // endSaveProcess
        ?>
        </div>
        </div>
        </div>
        
        <?php 
    }
    else{ ?>
    <table class="table table-hover table-bordered mt-3">
     
    <tr>
        <th>NPWP Perusahaan</th>
        <th><?php echo $hasilPajak['npwp_vendor'] ?></th>
    </tr>
    <tr>
        <th>Fotocopy NPWP Perusahaan</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilPajak['file_npwp_vendor'] ?>' target=blank><?php echo $hasilPajak['file_npwp_vendor'] ?> </a>
        </th>
    </tr>
    <tr>
        <th>NPWP Direktur</th>
        <th><?php echo $hasilPajak['npwp_direktur'] ?></th>
    </tr>
    <tr>
        <th>Fotocopy NPWP Direktur</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilPajak['file_npwp_direktur'] ?>' target=blank><?php echo $hasilPajak['file_npwp_direktur'] ?> </a>
        </th>
    </tr>
    <tr>
        <th>Fotocopy Tanda Daftar Pajak</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilPajak['file_tanda_daftar'] ?>' target=blank><?php echo $hasilPajak['file_tanda_daftar'] ?> </a>
        </th>
    </tr>
    <tr>
        <th>Fotocopy Lapor Pajak</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilPajak['file_lapor_pajak'] ?>' target=blank><?php echo $hasilPajak['file_lapor_pajak'] ?> </a>
        </th>
    </tr>
    </table>
    <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=pajak&id= <?php echo $hasilPajak['id_pajak'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
    <?php } ?>
    </div>
<!-- endPajak -->

  <!-- ijinUsaha -->
    <div class="tab-pane fade" id="ijin" role="tabpanel" aria-labelledby="ijin-tab">

    <?php
    $queriIjin="SELECT * FROM ijin_usaha WHERE id_vendor='$id_vendor'";
    $sqlIjin=mysqli_query($conn, $queriIjin);
    $cekIjin=mysqli_num_rows($sqlIjin);
    $hasilIjin=mysqli_fetch_assoc($sqlIjin);
    
    if($cekIjin!=1){ ?>
        Belum ada data
        <br>
        <!-- Button to Open the Modal -->
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal_ijin">
            Klik untuk lengkapi data Ijin Usaha Perusahaan
            </button>

        <!-- The Modal -->
        <div class="modal" id="myModal_ijin">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
        
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title">Ijin Usaha Perusahaan</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">

        <div class="form-group row"> 
            <label class="col-md-2 col-sm-12 col-form-label" for="id_vendor">ID Anggota:</label>
            <div class="col-md-10 col-sm-12">
            <input name='id_vendor' type="text" class="form-control" id="id_vendor" value="<?php echo $_SESSION['id_vendor']?> " readonly> 
            </div>
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

    </div>
        <!-- Modal footer -->
        <div class="modal-footer">
            <button name="simpanIjin" type="submit" class="btn btn-sm btn-success mx-auto">Simpan</button>
        </div>
    </form>

    <?php
    // saveProcess
    if(isset($_POST['simpanIjin'])){
        $id_vendor=$_POST['id_vendor'];
        $kualifikasi_usaha=$_POST['kualifikasi_usaha'];
        $kualifikasi_pengadaan=$_POST['kualifikasi_pengadaan'];
        $pkp=$_POST['pkp'];
        $nib=$_POST['nib'];

        $img13=$_FILES['file_ijin_usaha']['name'];
        $tmp13 = $_FILES['file_ijin_usaha']['tmp_name'];
        $newimg13 = $id_vendor.$img13;
        $path13 = "../dokumenVendor/".$newimg13;
        move_uploaded_file($tmp13, $path13);

        $img14=$_FILES['file_nib']['name'];
        $tmp14 = $_FILES['file_nib']['tmp_name'];
        $newimg14 = $id_vendor.$img14;
        $path14 = "../dokumenVendor/".$newimg14;
        move_uploaded_file($tmp14, $path14);

        $img15=$_FILES['file_sert_badan_usaha']['name'];
        $tmp15 = $_FILES['file_sert_badan_usaha']['tmp_name'];
        $newimg15 = $id_vendor.$img15;
        $path15 = "../dokumenVendor/".$newimg15;
        move_uploaded_file($tmp15, $path15);

        $img16=$_FILES['file_ska_konstruksi']['name'];
        $tmp16 = $_FILES['file_ska_konstruksi']['tmp_name'];
        $newimg16 = $id_vendor.$img16;
        $path16 = "../dokumenVendor/".$newimg16;
        move_uploaded_file($tmp16, $path16);

        $img17=$_FILES['file_skt_konstruksi']['name'];
        $tmp17 = $_FILES['file_skt_konstruksi']['tmp_name'];
        $newimg17 = $id_vendor.$img17;
        $path17 = "../dokumenVendor/".$newimg17;
        move_uploaded_file($tmp17, $path17);

        $query="INSERT INTO ijin_usaha(id_vendor, kualifikasi_usaha, kualifikasi_pengadaan, pkp, nib, file_ijin_usaha, file_nib, file_sert_badan_usaha, file_ska_konstruksi, file_skt_konstruksi) VALUES ('$id_vendor','$kualifikasi_usaha','$kualifikasi_pengadaan','$pkp','$nib','$newimg13','$newimg14','$newimg15','$newimg16','$newimg17')";
        $sql=mysqli_query($conn,$query);
        if($sql){
            echo "<script>alert ('Berhasil manambahkan Data');window.location='dpt_ld'</script>";
        }
        else {
        echo "<script>alert ('ERROR..! Terjadi kegagalan dalam proses penyimpanan data');</script>";
        }
    }
    // endSaveProcess
    ?>
        </div>
        </div>
        </div>
    <?php }
    else{ ?> 
    <table class="table table-hover table-bordered mt-3">
    
    <tr>
        <th>Kualifikasi Usaha</th>
        <th><?php echo $hasilIjin['kualifikasi_usaha'] ?></th>
    </tr>
    <tr>
        <th>Kualifikasi Pengadaan</th>
        <th><?php echo $hasilIjin['kualifikasi_pengadaan'] ?></th>
    </tr>
    <tr>
        <th>Nomor PKP</th>
        <th><?php echo $hasilIjin['pkp'] ?></th>
    </tr>
    <tr>
        <th>NIB</th>
        <th><?php echo $hasilIjin['nib'] ?></th>
    </tr>
    <tr>
        <th>Fotocopy Ijin Usaha</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilIjin['file_ijin_usaha'] ?>'target=blank><?php echo $hasilIjin['file_ijin_usaha'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Fotocopy NIB</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilIjin['file_nib'] ?>'target=blank><?php echo $hasilIjin['file_nib'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Fotocopy Sertifikat Badan Usaha</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilIjin['file_sert_badan_usaha'] ?>'target=blank><?php echo $hasilIjin['file_sert_badan_usaha'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Fotocopy SKA Konstruksi</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilIjin['file_ska_konstruksi'] ?>'target=blank><?php echo $hasilIjin['file_ska_konstruksi'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Fotocopy SKT Konstruksi</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilIjin['file_skt_konstruksi'] ?>'target=blank><?php echo $hasilIjin['file_skt_konstruksi'] ?>  </a>
        </th>
    </tr>
    </table>
        <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=ijinusaha&id= <?php echo $hasilIjin['id_ijin'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
    <?php } ?>
    </div>
<!-- endIjinUsaha -->

<!-- tenagakerja -->
    <div class="tab-pane fade" id="tenagakerja" role="tabpanel" aria-labelledby="tenagakerja-tab">

    <?php
    $queriKerja="SELECT * FROM tenagakerja WHERE id_vendor='$id_vendor'";
    $sqlKerja=mysqli_query($conn, $queriKerja);
    $cekKerja=mysqli_num_rows($sqlKerja);
    $hasilKerja=mysqli_fetch_assoc($sqlKerja);
    
    if($cekKerja!=1){ ?>
        Belum ada data
        <br>
        <!-- Button to Open the Modal -->
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal_tenagakerja">
            Klik untuk lengkapi data Tenaga Kerja Perusahaan
            </button>

        <!-- The Modal -->
        <div class="modal" id="myModal_tenagakerja">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
        
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title">Tenaga Kerja Perusahaan</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">

        <div class="form-group row"> 
            <label class="col-md-2 col-sm-12 col-form-label" for="id_vendor">ID Anggota:</label>
            <div class="col-md-10 col-sm-12">
            <input name='id_vendor' type="text" class="form-control" id="id_vendor" value="<?php echo $_SESSION['id_vendor']?> " readonly> 
            </div>
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

    </div>
        <!-- Modal footer -->
        <div class="modal-footer">
            <button name="simpanKerja" type="submit" class="btn btn-sm btn-success mx-auto">Simpan</button>
        </div>
    </form>

    <?php
    // saveProcess
    if(isset($_POST['simpanKerja'])){
        $id_vendor=$_POST['id_vendor'];
        $img18=$_FILES['file_dok_teknik']['name'];
        $tmp18 = $_FILES['file_dok_teknik']['tmp_name'];
        $newimg18 = $id_vendor.$img18;
        $path18 = "../dokumenVendor/".$newimg18;
        move_uploaded_file($tmp18, $path18);

        $img19=$_FILES['file_ktp']['name'];
        $tmp19 = $_FILES['file_ktp']['tmp_name'];
        $newimg19 = $id_vendor.$img19;
        $path19 = "../dokumenVendor/".$newimg19;
        move_uploaded_file($tmp19, $path19);

        $img20=$_FILES['file_skt']['name'];
        $tmp20 = $_FILES['file_skt']['tmp_name'];
        $newimg20 = $id_vendor.$img20;
        $path20 = "../dokumenVendor/".$newimg20;
        move_uploaded_file($tmp20, $path20);

        $img21=$_FILES['file_ijazah']['name'];
        $tmp21 = $_FILES['file_ijazah']['tmp_name'];
        $newimg21 = $id_vendor.$img21;
        $path21 = "../dokumenVendor/".$newimg21;
        move_uploaded_file($tmp21, $path21);

        $query="INSERT INTO tenagakerja(id_vendor, file_dok_teknik, file_ktp, file_skt, file_ijazah) VALUES ('$id_vendor','$newimg18 ','$newimg19','$newimg20','$newimg21')";
        $sql=mysqli_query($conn,$query);
        if($sql){
            echo "<script>alert ('Berhasil manambahkan Data');window.location='dpt_ld'</script>";
        }
        else {
        echo "<script>alert ('ERROR..! Terjadi kegagalan dalam proses penyimpanan data');</script>";
        }
    }
    // endSaveProcess
    ?>
        </div>
        </div>
        </div>
    <?php }
    else{ ?> 
    <table class="table table-hover table-bordered mt-3">
    <tr>
        <th>Dokumen Tenaga Teknis</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilKerja['file_dok_teknik'] ?>'target=blank><?php echo $hasilKerja['file_dok_teknik'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Kumpulan KTP Tenaga Teknis</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilKerja['file_ktp'] ?>'target=blank><?php echo $hasilKerja['file_ktp'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Kumpulan SKT Tenaga Teknis</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilKerja['file_skt'] ?>'target=blank><?php echo $hasilKerja['file_skt'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Kumpulan Ijazah Tenaga Teknis</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilKerja['file_ijazah'] ?>'target=blank><?php echo $hasilKerja['file_ijazah'] ?>  </a>
        </th>
    </tr>
    </table>
    <div class="form-group row">
        <div class="mx-auto">
        <a href='dpt_u?aksi=tenagakerja&id= <?php echo $hasilKerja['id_tenagakerja'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
        </div>
    </div>
    <?php } ?>
    </div>
<!-- endTenagaKerja -->

<!-- keuangan -->
    <div class="tab-pane fade" id="Keuangan" role="tabpanel" aria-labelledby="Keuangan-tab">

    <?php
    $queriKeuangan="SELECT * FROM keuangan WHERE id_vendor='$id_vendor'";
    $sqlKeuangan=mysqli_query($conn, $queriKeuangan);
    $cekKeuangan=mysqli_num_rows($sqlKeuangan);
    $hasilKeuangan=mysqli_fetch_assoc($sqlKeuangan);
    
    if($cekKeuangan!=1){ ?>
        Belum ada data
        <br>
        <!-- Button to Open the Modal -->
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal_Keuangan">
            Klik untuk lengkapi data Keuangan Perusahaan
            </button>

        <!-- The Modal -->
        <div class="modal" id="myModal_Keuangan">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
        
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title">Keuangan Perusahaan</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">

        <div class="form-group row"> 
            <label class="col-md-2 col-sm-12 col-form-label" for="id_vendor">ID Anggota:</label>
            <div class="col-md-10 col-sm-12">
            <input name='id_vendor' type="text" class="form-control" id="id_vendor" value="<?php echo $_SESSION['id_vendor']?> " readonly> 
            </div>
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

    </div>
        <!-- Modal footer -->
        <div class="modal-footer">
            <button name="simpanKeuangan" type="submit" class="btn btn-sm btn-success mx-auto">Simpan</button>
        </div>
    </form>

    <?php
    // saveProcess
    if(isset($_POST['simpanKeuangan'])){
        $id_vendor=$_POST['id_vendor'];
        $img22=$_FILES['file_neraca']['name'];
        $tmp22 = $_FILES['file_neraca']['tmp_name'];
        $newimg22 = $id_vendor.$img22;
        $path22 = "../dokumenVendor/".$newimg22;
        move_uploaded_file($tmp22, $path22);

        $img23=$_FILES['file_labarugi']['name'];
        $tmp23 = $_FILES['file_labarugi']['tmp_name'];
        $newimg23 = $id_vendor.$img23;
        $path23 = "../dokumenVendor/".$newimg23;
        move_uploaded_file($tmp23, $path23);


        $query="INSERT INTO keuangan(id_vendor, file_neraca, file_labarugi) VALUES ('$id_vendor','$newimg22','$newimg23')";
        $sql=mysqli_query($conn,$query);
        if($sql){
            echo "<script>alert ('Berhasil manambahkan Data Keuangan');window.location='dpt_ld'</script>";
        }
        else {
        echo "<script>alert ('ERROR..! Terjadi kegagalan dalam proses penyimpanan data');</script>";
        }
    }
    // endSaveProcess
    ?>
        </div>
        </div>
        </div>
    <?php }
    else{ ?> 
    <table class="table table-hover table-bordered mt-3">
    <tr>
        <th>Laporan Neraca Perusahaan</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilKeuangan['file_neraca'] ?>'target=blank><?php echo $hasilKeuangan['file_neraca'] ?>  </a>
        </th>
    </tr>
    <tr>
        <th>Laporan Laba Rugi Perusahaan</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilKeuangan['file_labarugi'] ?>'target=blank><?php echo $hasilKeuangan['file_labarugi'] ?>  </a>
        </th>
    </tr>
    </table>
        <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=keuangan&id= <?php echo $hasilKeuangan['id_keuangan'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
    <?php } ?>
    </div>
<!-- endKeuangan -->

<!-- dokumenPelengkap -->
    <div class="tab-pane fade" id="pelengkap" role="tabpanel" aria-labelledby="pelengkap-tab">
    
    <?php
    $queriPelengkap="SELECT * FROM dokumen WHERE id_vendor='$id_vendor'";
    $sqlPelengkap=mysqli_query($conn, $queriPelengkap);
    $cekPelengkap=mysqli_num_rows($sqlPelengkap);
    $hasilPelengkap=mysqli_fetch_assoc($sqlPelengkap);
    
    if($cekPelengkap!=1){ ?>
        Belum ada data
        <br>
        <!-- Button to Open the Modal -->
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal_pelengkap">
            Klik untuk lengkapi data
            </button>

        <!-- The Modal -->
        <div class="modal" id="myModal_pelengkap">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
        
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title">Dokumen Pelengkap</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">

        <div class="form-group row"> 
            <label class="col-md-2 col-sm-12 col-form-label" for="id_vendor">ID Anggota:</label>
            <div class="col-md-10 col-sm-12">
            <input name='id_vendor' type="text" class="form-control" id="id_vendor" value="<?php echo $_SESSION['id_vendor']?> " readonly> 
            </div>
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

    </div>
        <!-- Modal footer -->
        <div class="modal-footer">
            <button name="simpanPelengkap" type="submit" class="btn btn-sm btn-success mx-auto">Simpan</button>
        </div>
    </form>

    <?php
    // saveProcess
    if(isset($_POST['simpanPelengkap'])){
        $id_vendor=$_POST['id_vendor'];
        $img24=$_FILES['file_ikutserta']['name'];
        $tmp24 = $_FILES['file_ikutserta']['tmp_name'];
        $newimg24 = $id_vendor.$img24;
        $path24 = "../dokumenVendor/".$newimg24;
        move_uploaded_file($tmp24, $path24);

        $img25=$_FILES['file_suratkuasa']['name'];
        $tmp25 = $_FILES['file_suratkuasa']['tmp_name'];
        $newimg25 = $id_vendor.$img25;
        $path25 = "../dokumenVendor/".$newimg25;
        move_uploaded_file($tmp25, $path25);

        $img26=$_FILES['file_pendaftaran']['name'];
        $tmp26 = $_FILES['file_pendaftaran']['tmp_name'];
        $newimg26 = $id_vendor.$img26;
        $path26 = "../dokumenVendor/".$newimg26;
        move_uploaded_file($tmp26, $path26);

        $query="INSERT INTO dokumen(id_vendor, file_ikutserta, file_suratkuasa, file_pendaftaran) VALUES ('$id_vendor','$newimg24','$newimg25','$newimg26')";
        $sql=mysqli_query($conn,$query);
        if($sql){
            echo "<script>alert ('Berhasil manambahkan Data');window.location='dpt_ld'</script>";
        }
        else {
        echo "<script>alert ('ERROR..! Terjadi kegagalan dalam proses penyimpanan data');</script>";
        }
    }
    // endSaveProcess
    ?>
        </div>
        </div>
        </div>
    <?php }
    else{ ?> 
    <table class="table table-hover table-bordered mt-3"> 
    <tr>
        <th>Formulir Keiikutsertaan</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilPelengkap['file_ikutserta'] ?>'target=blank > <?php echo $hasilPelengkap['file_ikutserta'] ?>  &nbsp &nbsp
        <span class="glyphicon glyphicon-eye-open"></span></a>
    </th>
    </tr>
    
    <tr>
        <th>Surat Kuasa</th>
        <th>
            <a href='dpt_ld_v?id=<?php echo $hasilPelengkap['file_suratkuasa'] ?>'target=blank ><?php echo $hasilPelengkap['file_suratkuasa'] ?>  &nbsp &nbsp <span class='glyphicon glyphicon-eye-open'></span> </a>
    </th>
    </tr>
    
    <tr>
        <th>Formulir Pendaftaran</th>
        <th><a href='dpt_ld_v?id=<?php echo $hasilPelengkap['file_pendaftaran'] ?>'target=blank><?php echo $hasilPelengkap['file_pendaftaran'] ?></a>
    </th>
    </tr>                
    </table>
    <div class="form-group row">
            <div class="mx-auto">
            <a href='dpt_u?aksi=dokumen&id= <?php echo $hasilPelengkap['id_dokumen'] ;?> ' class='btn btn-sm btn-info fas fa-edit' > edit</a>
            </div>
        </div>
    <?php } ?>
    </div>
<!-- endDokumenPelengkap -->
</div>
        
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

<!-- scriptForComboBoxBertingkat -->
<script src="../vendor/jquery/jquery-1.10.2.min.js"></script>
<script src="../vendor/jquery/jquery.chained.min.js"></script>
<script>
    $("#kabupaten").chained("#provinsi");
</script>

<?php
ob_flush() ;
include '../footer.php';
?>