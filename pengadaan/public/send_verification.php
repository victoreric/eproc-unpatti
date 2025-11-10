<?php
// public/send_verification.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // pastikan folder vendor ada

function sendVerificationEmail($email, $verify_token)
{
    $verification_link = "http://localhost/eproc-unpatti/pengadaan/public/verify.php?token=" . $verify_token;

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'pbjunpatti@gmail.com'; // ganti
        $mail->Password   = 'okwm ffve apzn yyhs';             // gunakan app password gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('pbjunpatti@gmail.com', 'E-Proc UNPATTI');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verifikasi Akun Penyedia UNPATTI';
        $mail->Body    = "
            <h3>Verifikasi Email Anda</h3>
            <p>Terima kasih telah mendaftar sebagai penyedia barang/jasa UNPATTI.</p>
            <p>Klik link berikut untuk melakukan verifikasi akun Anda:</p>
            <p><a href='$verification_link'>$verification_link</a></p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
