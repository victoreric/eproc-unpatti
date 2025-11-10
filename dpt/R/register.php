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
                    <label class="col-md-2 col-sm-12 col-form-label" for="email"> Email:</label>
                    <div class="col-md-10 col-sm-12">
                    <input name='email' type="email" class="form-control" id="email" required> 
                    </div>
                </div>
                
                <div class="form-group row">
                    <div class="mx-auto">
                    <button type="submit" name="simpan" class="btn btn-sm btn-success btn-user">Daftar Akun </button>
                    <button type="reset" name="reset" class="btn btn-sm btn-primary btn-user">Reset</button> 
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
    $queri1="SELECT * FROM register ORDER BY id_register_vendor DESC LIMIT 1";
    $sql1=mysqli_query($conn,$queri1); 
    $hasil1=mysqli_fetch_assoc($sql1);
    $periksa=mysqli_num_rows($sql1);
        if($periksa!=0){
        $new_id_vendor=$hasil1['id_register_vendor']+1;
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
    $email=$_POST['email'];
   
    $hash1=MD5($_POST['nama']);
    $hash2=MD5($_POST['nama']);
    $hash=$hash1.'_'.$hash2;

    $queri_cek="SELECT * FROM register";
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

    $queri="INSERT INTO register (id_vendor, user_id, password ,  nama_vendor, email_vendor, level, active, hash ) VALUES ('UNPBJ$new_id_vendor','$user_id','$password','$nama','$email','0','N','$hash')";
    $sql=mysqli_query($conn, $queri);
    
    // Include librari phpmailer
    include('phpmailer/Exception.php');
    include('phpmailer/PHPMailer.php');
    include('phpmailer/SMTP.php');


    if($sql){ 
        
        // Aktifkan code ini jika ingin melakukan aktivasi melalui email
        //kirim email konfirmasi
        //terhubung ke folder php mailer
        
        
        

            // Include librari phpmailer
            include('phpmailer/Exception.php');
            include('phpmailer/PHPMailer.php');
            include('phpmailer/SMTP.php');


            $email_pengirim = ' pbjunpatti@gmail.com'; // Isikan dengan email pengirim
            $nama_pengirim = 'Pokja Unpatti'; // Isikan dengan nama pengirim
            $email_penerima = $_POST['email_penerima']; // Ambil email penerima dari inputan form
            $subjek = $_POST['subjek']; // Ambil subjek dari inputan form
            $pesan = $_POST['pesan']; // Ambil pesan dari inputan form
            $attachment = $_FILES['attachment']['name']; // Ambil nama file yang di upload

           
            $mail = new PHPMailer;
            $mail->isSMTP();

            $mail->Host = 'smtp.gmail.com';
            $mail->Username = $email_pengirim; // Email Pengirim
            //$mail->Password = 'password_akun_email_pengirim'; // Isikan dengan Password email pengirim
            $mail->Password = 'vhxgtfvojfekdvhw'; // Isikan dengan Password email pengirim
            $mail->Port = 465;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            // $mail->SMTPDebug = 2; // Aktifkan untuk melakukan debugging

            $mail->setFrom($email_pengirim, $nama_pengirim);
            $mail->addAddress($email_penerima, '');
            $mail->isHTML(true); // Aktifkan jika isi emailnya berupa html
                    
       

        echo "<script> alert ('Terima Kasih. Anda telah melakukan pendaftaran. Segera cek email anda untuk melakukan proses verifikasi '); window.location='index'; </script>" ;

        
        // Akhir Aktifkan code ini jika ingin melakukan aktivasi melalui email



    //    echo "<script> alert ('Terima Kasih. Anda telah melakukan pendaftaran. Akun Anda belum bisa digunakan. Admin akan memverifikasi informasi yang anda berikan setelah itu akan mengaktifkan akun anda. harap menunggu. '); window.location='index'; </script>" ;
    }
    else
    {
    echo "Maaf, Terjadi kesalahan saat mencoba untuk menyimpan data ke database.";
    echo "<br><a href='index'>Kembali Ke Form</a>";
}
    }

?>

