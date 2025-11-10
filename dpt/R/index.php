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
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="../vendor/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<?php include '../link.php'; ?>
<!-- <body class="bg-gradient-primary"> -->

<body background="../vendor/img/butterfly-bg.jpg">
    <div class="row container-fluid small">
        <!--firstCol  -->
        <div class="col-sm-2"></div>
        <!-- secondCol -->
        <div class="col-sm-8">
            <div class="card my-5">
                <div class="card-body">
                    <div class="text-center">
                        <h1 class="h5 text-gray-900 mb-4">Formulir Pendaftaran 2<br> Penyedia Barang/ Jasa</h1>
                    </div>
                    <form method="POST" action="send.php" enctype="multipart/form-data">
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
                                <input name='email_penerima' type="email" class="form-control" id="email" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="mx-auto">
                                <button type="submit" name="simpan" class="btn btn-sm btn-success btn-user">Daftar Akun </button>
                                <!-- <button type="reset" name="reset" class="btn btn-sm btn-primary btn-user">Reset</button>  -->
                            </div>
                        </div>

                    </form>
                    <hr>
                    <div class="text-center">
                        <a class="small" href="../index">Sudah Punya Akun? Login!</a>
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

    <!-- ------ -->
    <!-- <body>
    <div style="padding: 5px 30px;">
        <h1>Pendaftaran Penyedia </h1>
        <hr />

        <form method="post" action="send.php" enctype="multipart/form-data">
            
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
                    <input name='email_penerima' type="email" class="form-control" id="email" required> 
                    </div>
                </div>
                
                <div class="form-group row">
                    <div class="mx-auto">
                    <button type="submit" name="simpan" class="btn btn-sm btn-success btn-user">Daftar Akun </button>
                    <button type="reset" name="reset" class="btn btn-sm btn-primary btn-user">Reset</button> 
                    </div>
                </div> -->

    <!-- <div style="margin-bottom: 10px;">
                <label>Kepada</label><br />
                <input type="email" name="email_penerima" placeholder="Email Penerima" style="margin-top: 5px;width: 400px" />
            </div>
            <div style="margin-bottom: 10px;">
                <label>Subjek</label><br />
                <input type="text" name="subjek" placeholder="Subjek" style="margin-top: 5px;width: 400px" />
            </div>
            <div style="margin-bottom: 10px;">
                <label>Pesan</label><br />
                <textarea name="pesan" placeholder="Pesan" rows="8" style="margin-top: 5px;width: 400px"></textarea>
            </div>
            <div style="margin-bottom: 20px;">
                <label>Attachment</label><br />
                <input type="file" name="attachment" style="margin-top: 5px;width: 400px" />
            </div> -->

    <!-- <hr />
            <button type="submit">KIRIM EMAIL</button> -->
    <!-- </form> -->
    <!-- </div> -->
</body>

</html>