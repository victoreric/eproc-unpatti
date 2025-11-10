<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/db.php';
require '../includes/functions.php';
require 'send_verification.php';

$error_message = "";
$success_message = "";

if (isset($_POST['register'])) {

    $company_name = trim($_POST['company_name']);
    $email        = trim($_POST['email']);
    $phone        = trim($_POST['phone']);
    $username     = trim($_POST['username']);
    $password     = trim($_POST['password']);
    $confirm_pass = trim($_POST['confirm_password']);

    // Validasi dasar
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid.";
    } elseif (!preg_match('/^[0-9+]+$/', $phone)) {
        $error_message = "Nomor telepon hanya boleh angka dan tanda +.";
    } elseif (strlen($username) < 8) {
        $error_message = "Username minimal 8 karakter.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password minimal 8 karakter.";
    } elseif ($password !== $confirm_pass) {
        $error_message = "Password dan Konfirmasi Password tidak sama.";
    } else {

        // Cek duplikasi username/email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);

        if ($stmt->rowCount() > 0) {
            $error_message = "Username atau Email sudah digunakan.";
        } else {

            // Hash password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Token verifikasi
            $verify_token = bin2hex(random_bytes(16));

            $role_vendor_id = 3;

            // Insert user
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password_hash, full_name, phone, role_id, is_active, verify_token, email_verified, created_at)
                VALUES (:username, :email, :password_hash, :full_name, :phone, :role_id, 0, :verify_token, 0, NOW())
            ");
            $stmt->execute([
                ':username'      => $username,
                ':email'         => $email,
                ':password_hash' => $password_hash,
                ':full_name'     => $company_name,
                ':phone'         => $phone,
                ':role_id'       => $role_vendor_id,
                ':verify_token'  => $verify_token
            ]);

            $user_id = $pdo->lastInsertId();

            // Buat code member
            $year = date("Y");
            $code_member = "UNPBJ_" . $year . $user_id;

            // Insert perusahaan
            $stmt = $pdo->prepare("
                INSERT INTO companies (user_id, name, email, phone, code_member, created_at) 
                VALUES (:user_id, :name, :email, :phone, :code_member, NOW())
            ");
            $stmt->execute([
                ':user_id'     => $user_id,
                ':name'        => $company_name,
                ':email'       => $email,
                ':phone'       => $phone,
                ':code_member' => $code_member
            ]);

            // Kirim email verifikasi
            sendVerificationEmail($email, $verify_token);

            $success_message = "Pendaftaran berhasil! Silakan cek email untuk verifikasi akun.";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Pendaftaran Penyedia UNPATTI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container mt-5" style="max-width: 600px;">

        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h4>Pendaftaran Penyedia UNPATTI</h4>
            </div>

            <div class="card-body">

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error:</strong> <?= $error_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Berhasil!</strong> <?= $success_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" id="registerForm">

                    <div class="mb-3">
                        <label>Nama Perusahaan</label>
                        <input type="text" name="company_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Email Perusahaan</label>
                        <input type="email" name="email" class="form-control"
                            pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>No. Telp</label>
                        <input type="text" name="phone" id="phone"
                            class="form-control"
                            pattern="^[0-9+]+$"
                            title="Hanya angka dan tanda + yang diperbolehkan"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Username (minimal 8 karakter)</label>
                        <input type="text" name="username" minlength="8"
                            class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Password (minimal 8 karakter)</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password"
                                minlength="8" class="form-control" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)">👁</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Konfirmasi Password</label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" id="confirm_password"
                                minlength="8" class="form-control" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password', this)">👁</button>
                        </div>
                        <small id="passAlert" class="text-danger d-none">Password tidak sama!</small>
                    </div>

                    <button type="submit" name="register" class="btn btn-success w-100">Daftar</button>

                    <div class="mt-3 text-center">
                        Sudah punya akun? <a href="index.php">Login disini</a>
                    </div>

                </form>

            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Nomor telepon hanya angka dan +
        document.getElementById('phone').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+]/g, "");
        });

        // Validasi konfirmasi password realtime
        const pass = document.getElementById('password');
        const confirm = document.getElementById('confirm_password');
        const alertPass = document.getElementById('passAlert');

        function checkPasswordMatch() {
            if (confirm.value === "") {
                alertPass.classList.add('d-none');
                return;
            }
            if (pass.value !== confirm.value) {
                alertPass.classList.remove('d-none');
            } else {
                alertPass.classList.add('d-none');
            }
        }

        pass.addEventListener('input', checkPasswordMatch);
        confirm.addEventListener('input', checkPasswordMatch);

        // Validasi saat submit
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            if (pass.value !== confirm.value) {
                e.preventDefault();
                alertPass.classList.remove('d-none');
            }
        });

        // Auto-hide alert setelah 10 detik
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 10000);
    </script>

    <script>
        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            if (field.type === "password") {
                field.type = "text";
                btn.textContent = "🙈";
            } else {
                field.type = "password";
                btn.textContent = "👁";
            }
        }
    </script>


</body>

</html>