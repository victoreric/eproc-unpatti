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
    <div class="row container-fluid">
        <!--firstCol  -->
        <div class="col-sm-4"></div>
        <!-- secondCol -->
        <div class="col-sm-4">
            <div class="card my-5">
                <div class="card-body">
                    <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Formulir Pendafataran <br> 
                Penyedia Barang/ Jasa</h1>
                 </div>
                <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama">Nama Perusahaan:</label>
                    <input type="text" class="form-control" id="nama" name='nama'>
                    </div>
                <div class="form-group">
                  <label for="email">Email Perusahaan:</label>
                  <input type="email" class="form-control" id="email" name='email'>
                </div>
            
               <div class="form-group">
                    <label for="hp">Nomor Handphone (menggunakan WhatsApp)</label>
                    <input type="number" class="form-control " name="hp" id='hp' placeholder="" required>
                </div>
                <div class="form-group">
                  <label for="pwd">Password untuk Login ke Aplikasi UPBJ:</label>
                  <input type="password" class="form-control" id="pwd" name='password'>
               </div>   
                    
                        <div class="col"><button type="submit" name="simpan" class="btn btn-primary btn-user btn-block">Daftar Akun </button></div>

                        <div class="col mt-2"><button type="reset" name="reset" class="btn btn-warning btn-user">Reset</button></div>
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
    $nama=$_POST['nama'];
    $email=$_POST['email'];
    $hp=$_POST['hp'];
    $password=MD5($_POST['password']);

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

    $queri="INSERT INTO login_vendor (id_vendor,nama_vendor,email_vendor,hp,password,level,active) VALUES ('UNPBJ$new_id_vendor','$nama','$email','$hp','$password','0','N')";
    $sql=mysqli_query($conn, $queri);

    if($sql){ 
       echo "<script> alert ('Terima Kasih. Anda telah melakukan pendaftaran. Akun Anda belum bisa digunakan. Admin akan memverifikasi informasi yang anda berikan setelah itu akan mengaktifkan akun anda. harap menunggu. '); window.location='index'; </script>" ;
    }
    else
    {
    echo "Maaf, Terjadi kesalahan saat mencoba untuk menyimpan data ke database.";
    echo "<br><a href='index'>Kembali Ke Form</a>";
}

}
?>