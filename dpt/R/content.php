<html>
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="content-type">
</head>
<body>
    <!-- <div style="float: left;margin-right: 10px;">
        <img src="cid:logo_mynotescode" alt="Logo" style="height: 50px">
    </div> -->

    <h2 style="margin-bottom: 0;">Verifikasi Pendaftaran </h2><br>
   <h4> Daftar Penyedia Terpilih pada Universitas Pattimura Ambon</h4>
    <!-- https://upbj.victoreric.my.id/ -->

    <div style="clear: both"></div>
    <hr />

    <div style="text-align: justify">
        Selamat datang, <?php echo $nama; ?>
        <br>
        Anda telah mendaftar pada Sistem Informasi Daftar Penyedia Terpilih. <br>
        Username : <?php echo $user_id; ?> <br>
        Password : <?php echo $passwordNon;  ?> <br>
        <br>
        Mohon tidak memberikan username dan pasword kepada pihak lain yang tidak bertanggungjawab.<br>
        <br>
        Klik dibawah ini : <br>
         <a href='http://localhost/dpt-main/index'><?php echo $hash; ?> </a>
         <br> untuk mengaktifkan keanggotaan anda.
        
        <?php 
            // echo $pesan; // Tampilkan isi pesan 
        ?>
    </div>
</body>
</html>
