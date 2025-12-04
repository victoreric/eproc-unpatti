<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include librari phpmailer
include('phpmailer/Exception.php');
include('phpmailer/PHPMailer.php');
include('phpmailer/SMTP.php');
include '../link.php';


$email_pengirim = 'pbjunpatti@gmail.com'; // Isikan dengan email pengirim
$nama_pengirim = 'Pokja Unpatti'; // Isikan dengan nama pengirim
$email_penerima = $_POST['email_penerima']; // Ambil email penerima dari inputan form
// $subjek = $_POST['subjek']; // Ambil subjek dari inputan form
$subjek = 'Verifikasi Pendaftaran DPT';
// $pesan = $_POST['pesan']; // Ambil pesan dari inputan form
// $attachment = $_FILES['attachment']['name']; // Ambil nama file yang di upload

// to get new value for id_vendor
$queri1 = "SELECT * FROM register ORDER BY id_register_vendor DESC LIMIT 1";
$sql1 = mysqli_query($conn, $queri1);
$hasil1 = mysqli_fetch_assoc($sql1);
$periksa = mysqli_num_rows($sql1);
if ($periksa != 0) {
    $new_id_vendor = $hasil1['id_register_vendor'] + 1;
} else {
    $new_id_vendor = 1;
}
// prosesRegistration
$user_id = $_POST['user_id'];
$passwordNon = $_POST['password'];
$password = MD5($_POST['password']);
$nama = $_POST['nama'];

$hash1 = MD5($_POST['nama']);
$hash2 = MD5($_POST['email_penerima']);
$hash = $hash1 . '_' . $hash2;

$queri_cek = "SELECT * FROM register";
$sql_cek = mysqli_query($conn, $queri_cek);
$hasil = mysqli_fetch_array($sql_cek);
$email_vendor = $hasil['email_vendor'];
$nama_vendor = $hasil['nama_vendor'];
if ($email_penerima == $email_vendor) {
    echo die("<script> alert ('Email telah terdaftar. Silahkan gunakan email yang lain'); window.location='reg'; </script>");
}
if ($nama == $nama_vendor) {
    echo die("<script> alert ('Nama Perusahaan telah terdaftar. Mohon Periksa Kembali'); window.location='reg'; </script>");
}

$queri = "INSERT INTO register (id_vendor, user_id, password ,  nama_vendor, email_vendor, level, active, hash ) VALUES ('UNPBJ$new_id_vendor','$user_id','$password','$nama','$email_penerima','0','N','$hash')";
$sql = mysqli_query($conn, $queri);

if ($queri) {
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

    // Load file content.php
    ob_start();
    include "content.php";

    $content = ob_get_contents(); // Ambil isi file content.php dan masukan ke variabel $content
    ob_end_clean();


    $mail->Subject = $subjek;
    $mail->Body = $content;
    // $mail->AddEmbeddedImage('image/unpattiLogo.png', 'logo_mynotescode', 'logo.png'); // Aktifkan jika ingin menampilkan gambar dalam email
    $send = $mail->send();
    if ($send) { // Jika Email berhasil dikirim
        echo "<h1>Link Aktivasi telah dikirim ke Email Anda</h1> <br />
        <a href='http://localhost/dpt-main'>Kembali ke DPT</a>";
    } else { // Jika Email gagal dikirim
        echo "<h1>Email gagal dikirim</h1><br /><a href='http://localhost/dpt-main/'>Kembali ke DPT</a>";
        // echo '<h1>ERROR<br /><small>Error while sending email: '.$mail->getError().'</small></h1>'; // Aktifkan untuk mengetahui error message
    }

    // if(empty($attachment)){ // Jika tanpa attachment
    //     $send = $mail->send();

    //     if($send){ // Jika Email berhasil dikirim
    //         echo "<h1>Email berhasil dikirim</h1><br /><a href='index.php'>Kembali ke Form</a>";
    //     }else{ // Jika Email gagal dikirim
    //         echo "<h1>Email gagal dikirim</h1><br /><a href='index.php'>Kembali ke Form</a>";
    // echo '<h1>ERROR<br /><small>Error while sending email: '.$mail->getError().'</small></h1>'; // Aktifkan untuk mengetahui error message
    //     }
    // }else{ // Jika dengan attachment
    //     $tmp = $_FILES['attachment']['tmp_name'];
    //     $size = $_FILES['attachment']['size'];

    //     if($size <= 25000000){ // Jika ukuran file <= 25 MB (25.000.000 bytes)
    //         $mail->addAttachment($tmp, $attachment); // Add file yang akan di kirim

    //         $send = $mail->send();

    //         if($send){ // Jika Email berhasil dikirim
    //             echo "<h1>Email berhasil dikirim</h1><br /><a href='index.php'>Kembali ke Form</a>";
    //         }else{ // Jika Email gagal dikirim
    //             echo "<h1>Email gagal dikirim</h1><br /><a href='index.php'>Kembali ke Form</a>";
    //             // echo '<h1>ERROR<br /><small>Error while sending email: '.$mail->getError().'</small></h1>'; // Aktifkan untuk mengetahui error message
    //         }
    //     }else{ // Jika Ukuran file lebih dari 25 MB
    //         echo "<h1>Ukuran file attachment maksimal 25 MB</h1><br /><a href='index.php'>Kembali ke Form</a>";
    //     }
    // }

}
