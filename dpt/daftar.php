<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Aplikasi Pengadaan Barang & Jasa">
    <meta name="author" content="Victor Pattiradjawane">
    <title>DPT UPBJ - Unpatti</title>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="vendor/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<?php include 'link.php';?>
<!-- <body class="bg-gradient-primary"> -->
<body background="vendor/img/butterfly-bg.jpg">
    <div class="row container-fluid small">
        <!--firstCol  -->
        <div class="col-sm-2"></div>
        <!-- secondCol -->
        <div class="col-sm-8">
            <div class="card my-5">
                <div class="card-body">
                    <div class="text-center">
                    <h1 class="h5 text-gray-900 mb-4">Formulir Pendaftaran<br> Penyedia Barang/ Jasa</h1>
                 </div>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
                <div class="form-group row"> 
                    <label class="col-md-2 col-sm-12 col-form-label" for="user_id">User ID:</label>
                    <div class="col-md-10 col-sm-12">
                    <input name='user_id' type="text" class="form-control" id="user_id" required> 
                    </div>
                </div>
                <div class="form-group row"> 
                    <label class="col-md-2 col-sm-12 col-form-label" for="password"> Password:</label>
                    <div class="col-md-10 col-sm-12">
                    <input name='password' type="text" class="form-control" id="password" required> 
                    </div>
                </div>
                <div class="form-group row"> 
                    <label class="col-md-2 col-sm-12 col-form-label" for="nama"> Nama Perusahaan:</label>
                    <div class="col-md-10 col-sm-12">
                    <input name='nama' type="text" class="form-control" id="nama" required> 
                    </div>
                </div>
                <div class="form-group row"> 
                    <label class="col-md-2 col-sm-12 col-form-label" for="npwp"> NPWP Perusahaan:</label>
                    <div class="col-md-10 col-sm-12">
                    <input name='npwp' type="text" class="form-control" id="npwp" required> 
                    </div>
                </div>

                <div class="form-group row"> 
                    <label class="col-md-2 col-sm-12 col-form-label" for="pkp"> Nomor PKP:</label>
                    <div class="col-md-10 col-sm-12">
                    <input name='pkp' type="text" class="form-control" id="pkp" required> 
                    </div>
                </div>

                <div class="form-group row"> 
                    <label class="col-md-2 col-sm-12 col-form-label" for="kualifikasi_usaha"> Kualifikasi Usaha:</label>
                    <div class="col-md-10 col-sm-12">
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
                    <label class="col-md-2 col-sm-12 col-form-label" for="alamat"> Alamat:</label>
                    <div class="col-md-10 col-sm-12">
                    <input name='alamat' type="text" class="form-control" id="alamat" required> 
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
                    <label class="col-md-2 col-sm-12 col-form-label" for="kodepos"> Kode Pos:</label>
                    <div class="col-md-10 col-sm-12">
                    <input name='kodepos' type="number" class="form-control" id="kodepos" required> 
                    </div>
                </div>


                <div class="form-group row"> 
                    <label class="col-md-2 col-sm-12 col-form-label" for="telepon"> Telepon:</label>
                    <div class="col-md-10 col-sm-12">
                    <input name='telepon' type="number" class="form-control" id="telepon" required> 
                    </div>
                </div>
                <div class="form-group row"> 
                    <label class="col-md-2 col-sm-12 col-form-label" for="email"> Email:</label>
                    <div class="col-md-10 col-sm-12">
                    <input name='email' type="email" class="form-control" id="email" required> 
                    </div>
                </div>
                <div class="form-group row"> 
                    <label class="col-md-2 col-sm-12 col-form-label" for="hp"> Handphone:</label>
                    <div class="col-md-10 col-sm-12">
                    <input name='hp' type="number" class="form-control" id="hp" required> 
                    </div>
                </div>
                <div class="form-group row"> 
                    <label class="col-md-2 col-sm-12 col-form-label" for="website"> Website:</label>
                    <div class="col-md-10 col-sm-12">
                    <input name='website' type="text" class="form-control" id="website" required> 
                    </div>
                </div>

          
                <div class="alert alert-primary" role="alert">
                    Lengkapi persyaratan dengan meng-upload dokumen yang diminta sesuai syarat dibawah ini:
                    <ul>
                        <li>Hanya menerima type file *.PDF</li>
                        <li>Besar file <= 5MB per dokumen yang diupload</li>
                        <li>Dokumen yang diupload harus bisa dibaca dengan jelas untuk keperluan verifikasi</li>
                        <li><a href="download/Template_Surat Kuasa.docx">Download Formulir Keikutsertaan</a></li>
                        <li><a href="">Download Formulir Pendaftaran</a></li>
                        <li><a href="download/Template_Surat Kuasa.docx">Download contoh surat kuasa</a></li>

                    </ul>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-sm-12  col-form-label" for="file_kartu_direktur">KTP Direktur</label>
                    <div class="col-md-10 col-sm-12 ">
                        <input type="file" name="file_kartu_direktur" id="file_kartu_direktur" accept=".pdf" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-sm-12  col-form-label" for="file_npwp">NPWP Perusahaan</label>
                    <div class="col-md-10 col-sm-12 ">
                        <input type="file" name="file_npwp" id="file_npwp" accept=".pdf" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-sm-12  col-form-label" for="file_ijin_usaha">Surat Ijin Usaha Perusahaan</label>
                    <div class="col-md-10 col-sm-12 ">
                        <input type="file" name="file_ijin_usaha" id="file_ijin_usaha" accept=".pdf" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-sm-12  col-form-label" for="file_siujk">SIUJK</label>
                    <div class="col-md-10 col-sm-12 ">
                        <input type="file" name="file_siujk" id="file_siujk" accept=".pdf">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-sm-12  col-form-label" for="file_nib">NIB</label>
                    <div class="col-md-10 col-sm-12 ">
                        <input type="file" name="file_nib" id="file_nib" accept=".pdf" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-sm-12  col-form-label" for="file_akta">Akta Pendirian</label>
                    <div class="col-md-10 col-sm-12 ">
                        <input type="file" name="file_akta" id="file_akta" accept=".pdf" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-sm-12  col-form-label" for="file_ikutserta">Formulir Keikutsertaan</label>
                    <div class="col-md-10 col-sm-12 ">
                        <input type="file" name="file_ikutserta" id="file_ikutserta" accept=".pdf" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-sm-12  col-form-label" for="file_pendaftaran">Formulir Pendaftaran</label>
                    <div class="col-md-10 col-sm-12 ">
                        <input type="file" name="file_pendaftaran" id="file_pendaftaran" accept=".pdf" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-sm-12  col-form-label" for="file_suratkuasa">Surat Kuasa</label>
                    <div class="col-md-10 col-sm-12 ">
                        <input type="file" name="file_suratkuasa" id="file_suratkuasa" accept=".pdf">
                    </div>
                </div>
              
               
                <div class="form-group row">
                    <div class="mx-auto">
                    <button type="submit" name="simpan" class="btn btn-sm btn-success btn-user">Daftar Akun </button>

                    <button type="reset" name="reset" class="btn btn-sm btn-primary btn-user">Reset</button> 
                    <a button href="daftar.php" name="reset" class="btn btn-sm btn-primary btn-user">Reset</a> 
                    </div>
                </div>
                    
                </form>
                    <hr>
                    <div class="text-center">
                        <a class="small" href="index">Sudah Punya Akun? Login!</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- ThirdCol -->
        <div class="col-sm-4"></div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="vendor/js/sb-admin-2.min.js"></script>

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
<script src="vendor/jquery/jquery-1.10.2.min.js"></script>
<script src="vendor/jquery/jquery.chained.min.js"></script>
<script>
    $("#kabupaten").chained("#provinsi");
</script>


</body>
</html>

<?php
// to get new value for id_vendor
    $queri1="SELECT * FROM login_vendor ORDER BY id_login_vendor DESC LIMIT 1";
    $sql1=mysqli_query($conn,$queri1); 
    $hasil1=mysqli_fetch_assoc($sql1);
    $periksa=mysqli_num_rows($sql1);
        if($periksa!=0){
        $new_id_vendor=$hasil1['id_login_vendor']+1;
    }else{
        $new_id_vendor=1;
    }

    // $hasil1=mysqli_fetch_assoc($sql1);
    
    // if($periksa!=0){
        // $periksa=mysqli_num_rows($slq1);
    //     $new_id_vendor=$hasil1['id']+1;
    // }else{
        
    // }
// end to get new value for id_vendor

// prosesRegistration
if (isset($_POST['simpan'])) {
    $user_id=$_POST['user_id'];
    $password=MD5($_POST['password']);
    $nama=$_POST['nama'];
    $npwp=$_POST['npwp'];
    $pkp=$_POST['pkp'];
    $kualifikasi_usaha=$_POST['kualifikasi_usaha'];
    $alamat=$_POST['alamat'];
    $provinsi=$_POST['provinsi'];
    $kabupaten=$_POST['kabupaten'];
    $kodepos=$_POST['kodepos'];
    $telepon=$_POST['telepon'];
    $email=$_POST['email'];
    $hp=$_POST['hp'];
    $website=$_POST['website'];

    $id_vendor='UNPBJ'.$new_id_vendor;

    $img1=$_FILES['file_kartu_direktur']['name'];
    $tmp1 = $_FILES['file_kartu_direktur']['tmp_name'];
    $newimg1 = $id_vendor.$img1;
    $path1 = "dokumenVendor/".$newimg1;

    $img2=$_FILES['file_npwp']['name'];
    $tmp2 = $_FILES['file_npwp']['tmp_name'];
    $newimg2 = $id_vendor.$img2;
    $path2 = "dokumenVendor/".$newimg2;

    $img3=$_FILES['file_ijin_usaha']['name'];
    $tmp3 = $_FILES['file_ijin_usaha']['tmp_name'];
    $newimg3 = $id_vendor.$img3;
    $path3 = "dokumenVendor/".$newimg3;

    $img4=$_FILES['file_siujk']['name'];
    $tmp4 = $_FILES['file_siujk']['tmp_name'];
    $newimg4 = $id_vendor.$img4;
    $path4 = "dokumenVendor/".$newimg4;

    $img5=$_FILES['file_nib']['name'];
    $tmp5 = $_FILES['file_nib']['tmp_name'];
    $newimg5 = $id_vendor.$img5;
    $path5 = "dokumenVendor/".$newimg5;

    $img6=$_FILES['file_akta']['name'];
    $tmp6 = $_FILES['file_akta']['tmp_name'];
    $newimg6 = $id_vendor.$img6;
    $path6 = "dokumenVendor/".$newimg6;

    $img7=$_FILES['file_ikutserta']['name'];
    $tmp7 = $_FILES['file_ikutserta']['tmp_name'];
    $newimg7 = $id_vendor.$img7;
    $path7 = "dokumenVendor/".$newimg7;

    $img8=$_FILES['file_pendaftaran']['name'];
    $tmp8 = $_FILES['file_pendaftaran']['tmp_name'];
    $newimg8 = $id_vendor.$img8;
    $path8 = "dokumenVendor/".$newimg8;

    $img9=$_FILES['file_suratkuasa']['name'];
    $tmp9 = $_FILES['file_suratkuasa']['tmp_name'];
    $newimg9 = $id_vendor.$img9;
    $path9 = "dokumenVendor/".$newimg9;

    $hash1=MD5($_POST['nama']);
    $hash2=MD5($_POST['nama']);
    $hash=$hash1.'_'.$hash2;

    if(move_uploaded_file($tmp1, $path1)AND move_uploaded_file($tmp2, $path2)AND move_uploaded_file($tmp3, $path3)AND move_uploaded_file($tmp4, $path4)AND move_uploaded_file($tmp5, $path5)AND move_uploaded_file($tmp6, $path6)AND move_uploaded_file($tmp7, $path7)AND move_uploaded_file($tmp8, $path8)AND move_uploaded_file($tmp9, $path9))
    {

    $queri_cek="SELECT * FROM login_vendor";
    $sql_cek=mysqli_query($conn,$queri_cek);
    $hasil=mysqli_fetch_array($sql_cek);
    $email_vendor=$hasil['email_vendor'];
    $nama_vendor=$hasil['nama_vendor'];
    if($email==$email_vendor){
      echo die("<script> alert ('Email telah terdaftar. Silahkan gunakan email yang lain'); window.location='reg'; </script>");
    }
    if($nama==$nama_vendor){
      echo die("<script> alert ('Nama Perusahaan telah terdaftar. Mohon Periksa Kembali'); window.location='reg'; </script>");
    }

    $queri="INSERT INTO login_vendor (id_vendor, user_id, password ,  nama_vendor, npwp,pkp, kualifikasi_usaha, alamat, provinsi, kota, kodepos, telepon, email_vendor, hp, website, file_kartu_direktur,  file_npwp, file_ijin_usaha, file_siujk, file_nib, file_akta,  file_ikutserta ,file_suratkuasa ,file_pendaftaran, level, active, hash ) VALUES ('UNPBJ$new_id_vendor','$user_id','$password','$nama','$npwp','$pkp','$kualifikasi_usaha','$alamat','$provinsi','$kabupaten','$kodepos','$telepon','$email','$hp','$website','$newimg1','$newimg2','$newimg3','$newimg4','$newimg5','$newimg6','$newimg7','$newimg8','$newimg9','0','N','$hash')";
    $sql=mysqli_query($conn, $queri);

    if($sql){ 
        
        // Aktifkan code ini jika ingin melakukan aktivasi melalui email
        //kirim email konfirmasi
        //Jika server anda sudah dilengkapi email-server
        
        ini_set('display error',1);
        error_reporting(E_ALL);
        $from="admin@victoreric.info";
        $to=$email;
        $subject="Link Aktivasi";
        $message='Terima kasih sudah mendaftar sebagai penyedia barang/ Jasa pada UPT-PBJ Universitas Pattimura !'. "\r\n";
        $message .= 'Akun Anda sudah dibuat.'."\r\n";
        $message .='Anda dapat segera login dengan mengklik tautan dibawah ini.'."\r\n";
        
        $message.='-------------------------'."\r\n";
        $message .='User ID :'.$user_id.''."\r\n";
        $message .= 'Password :'.$password . ''."\r\n";
        $message.='-------------------------'."\r\n";
        $message .='Klik link konfirmasi dibawah ini'."\r\n";
        $message .='http://'.$_SERVER['SERVER_NAME'].'/activation.php?email='.$email.'&hash='.$hash.''."\r\n\n\n";
        
        $message .='Anda dapat mengabaikan ini, jika ini bukan Anda'."\r\n";
        						
        $headers="From: ".$from;
        mail($to,$subject,$message,$headers);

        // Akhir Aktifkan code ini jika ingin melakukan aktivasi melalui email



       echo "<script> alert ('Terima Kasih. Anda telah melakukan pendaftaran. Akun Anda belum bisa digunakan. Admin akan memverifikasi informasi yang anda berikan setelah itu akan mengaktifkan akun anda. harap menunggu. '); window.location='index'; </script>" ;
    }
    else
    {
    echo "Maaf, Terjadi kesalahan saat mencoba untuk menyimpan data ke database.";
    echo "<br><a href='index'>Kembali Ke Form</a>";
}
    }
}
?>

