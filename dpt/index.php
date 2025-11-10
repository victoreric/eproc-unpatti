<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Aplikasi Pengadaan Barang & Jasa">
    <meta name="author" content="Victor EP">
    <title>DPT UPBJ - Unpatti</title>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="vendor/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<!-- <body class="bg-gradient-primary"> -->

<body class="" background="vendor/img/butterfly-bg.jpg">
    <!-- login Form -->
    <?php
    session_start();
    if (isset($_SESSION['email_vendor'])) {
        if ($_SESSION['level'] == '0' && $_SESSION['active'] == 'Y') {
            header('location:G');
        } else if ($_SESSION['level'] == '0' && $_SESSION['active'] == 'N') {
            echo "<script> alert ('User dan Pasword belum diaktifkan. Hubungi administrator'); window.location='index'; </script>";
        } else {
            echo "<script> alert ('Silahkan Login'); window.location='index'; </script>";
        }
    }
    include "link.php";
    ob_start()
    ?>
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6">
                                <img src="assets/img/bgprocurement.png" alt="login" class="col-lg-12 d-none d-lg-block">
                            </div>

                            <div class="col-lg-6">
                                <div class="p-2">
                                    <div class="text-center">
                                        <img src="assets/img/unpattilogo.png" alt="logo" class="logo" width='200'>
                                        <h1 class="h4 text-gray-900 mt-3 mb-4">E-Proc Unpatti </h1>
                                        <h4 class="h4 text-gray-900 mt-3 mb-4">LOGIN VENDOR</h4>
                                    </div>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                        <div class="form-group">
                                            <input type="text" name='user_id' class="form-control form-control-user" placeholder="Masukan User ID">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name='password' class="form-control form-control-user" id="" placeholder="Password">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember Me</label>
                                            </div>
                                        </div>
                                        <input class='btn btn-primary btn-user btn-block' type="submit" name="login" id="login" value='Login'>
                                    </form>

                                    <hr>
                                    <div class="text-center">
                                        <a class="small" data-toggle='modal' data-target='#myModal' href=''>Lupa Password?</a>
                                    </div>
                                    <div class="text-center">
                                        Belum punya akun?<a class="small" href="R"> Daftar disini!</a>
                                    </div>
                                    <nav class="login-card-footer-nav mt-5">
                                        <a href="">Terms of use |</a>
                                        <a href="">Privacy policy | </a>
                                        <a class="" href="../">E-proc Unpatti</a>
                                    </nav>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div class="modal fade" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">DPT UPBJ - Unpatti</h4>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    Akun Demo : <br>
                    angkasa@gmail.com -> angkasa<br>
                    tes@gmail.com -> tes ;
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <?php
    // loginProses
    if (isset($_POST['login'])) {
        $user_id = $_POST['user_id'];
        $password = MD5($_POST['password']);

        $query = "SELECT * FROM register WHERE user_id='$user_id' AND password='$password'";
        $sql = mysqli_query($conn, $query);
        $cek = mysqli_num_rows($sql);

        if ($cek == 1) {
            $hasil = mysqli_fetch_array($sql);
            session_start();
            $_SESSION['id_register_vendor'] = $hasil['id_register_vendor'];
            $_SESSION['id_vendor'] = $hasil['id_vendor'];
            $_SESSION['user_id'] = $hasil['user_id'];
            $_SESSION['nama_vendor'] = $hasil['nama_vendor'];
            //   $_SESSION['pkp']=$hasil['pkp'];
            //   $_SESSION['kualifikasi_usaha']=$hasil['kualifikasi_usaha'];
            //   $_SESSION['alamat']=$hasil['alamat'];
            //   $_SESSION['provinsi']=$hasil['provinsi'];
            //   $_SESSION['kota']=$hasil['kota'];
            //   $_SESSION['kodepos']=$hasil['kodepos'];
            //   $_SESSION['telepon']=$hasil['telepon'];
            $_SESSION['email_vendor'] = $hasil['email_vendor'];
            //   $_SESSION['hp']=$hasil['hp'];
            //   $_SESSION['website']=$hasil['website'];
            //   $_SESSION['file_kartu_direktur']=$hasil['file_kartu_direktur'];
            //   $_SESSION['file_npwp']=$hasil['file_npwp'];
            //   $_SESSION['file_ijin_usaha']=$hasil['file_ijin_usaha'];
            //   $_SESSION['file_siujk']=$hasil['file_siujk'];
            //   $_SESSION['file_nib']=$hasil['file_nib'];
            //   $_SESSION['file_akta']=$hasil['file_akta'];
            //   $_SESSION['file_ikutserta']=$hasil['file_ikutserta'];
            //   $_SESSION['file_suratkuasa']=$hasil['file_suratkuasa'];
            //   $_SESSION['file_pendaftaran']=$hasil['file_pendaftaran'];
            $_SESSION['level'] = $hasil['level'];
            $_SESSION['active'] = $hasil['active'];

            if ($_SESSION['active'] == 'Y') {
                header('location:G');
            } else {
                session_destroy();
                echo "<script> alert ('User dan Pasword belum diaktifkan..! Hubungi Team IT UPT-PBJ'); window.location='index'; </script>";
            }
        } else {
            echo  "<script> alert ('Username dan Pasword belum Terdaftar..! Silahkan lakukan proses pendaftaran'); window.location='index'; </script>";
        }
    }
    ob_flush()
    ?>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="vendor/js/sb-admin-2.min.js"></script>
</body>

</html>