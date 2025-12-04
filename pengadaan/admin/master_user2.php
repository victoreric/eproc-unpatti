<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Hanya admin yang boleh mengakses
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    die("Akses ditolak. Hanya admin yang dapat mengakses halaman ini.");
}

// ======= FUNGSI HELPER =======
function generateRandomPassword($length = 10)
{
    return substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$_'), 0, $length);
}

function sendResetEmail($to, $username, $newPassword)
{
    $subject = "Reset Password Akun Anda";
    $message = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h3>Reset Password Berhasil</h3>
            <p>Halo <b>$username</b>,</p>
            <p>Password akun Anda telah direset oleh administrator. Berikut adalah password baru Anda:</p>
            <p style='font-size:16px;'><b>$newPassword</b></p>
            <p>Silakan login dan ubah password Anda setelah masuk.</p>
            <hr>
            <small>Email ini dikirim otomatis oleh sistem. Mohon tidak membalas email ini.</small>
        </body>
        </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Admin Sistem <noreply@university.ac.id>" . "\r\n";

    // Pastikan mail() aktif di server kamu
    mail($to, $subject, $message, $headers);
}

// ======= HANDLE REQUEST =======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // === ADD USER ===
    if ($action === 'add') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $full_name = trim($_POST['full_name']);
        $phone = trim($_POST['phone']);
        $role_id = (int) $_POST['role_id'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Cek username/email unik
        $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
        $check->execute([':username' => $username, ':email' => $email]);
        if ($check->fetchColumn() > 0) {
            echo "<script>alert('Username atau email sudah digunakan!');</script>";
        } else {
            $password_plain = generateRandomPassword();
            $password_hash = password_hash($password_plain, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, full_name, phone, role_id, is_active, created_at) 
                VALUES (:username, :email, :password_hash, :full_name, :phone, :role_id, :is_active, NOW())");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password_hash' => $password_hash,
                ':full_name' => $full_name,
                ':phone' => $phone,
                ':role_id' => $role_id,
                ':is_active' => $is_active
            ]);

            sendResetEmail($email, $username, $password_plain);
            echo "<script>alert('User berhasil ditambahkan dan password dikirim ke email!');window.location='master_user.php';</script>";
            exit;
        }
    }

    // === EDIT USER ===
    if ($action === 'edit') {
        $id = (int) $_POST['id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $full_name = trim($_POST['full_name']);
        $phone = trim($_POST['phone']);
        $role_id = (int) $_POST['role_id'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        $stmt = $pdo->prepare("UPDATE users SET username=:username, email=:email, full_name=:full_name, phone=:phone, role_id=:role_id, is_active=:is_active, updated_at=NOW() WHERE id=:id");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':full_name' => $full_name,
            ':phone' => $phone,
            ':role_id' => $role_id,
            ':is_active' => $is_active,
            ':id' => $id
        ]);
        echo "<script>alert('User berhasil diperbarui!');window.location='master_user.php';</script>";
        exit;
    }

    // === DELETE USER ===
    if ($action === 'delete') {
        $id = (int) $_POST['id'];
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
        echo "<script>alert('User berhasil dihapus!');window.location='master_user.php';</script>";
        exit;
    }

    // === RESET PASSWORD ===
    if ($action === 'reset') {
        $id = (int) $_POST['id'];
        $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $newPassword = generateRandomPassword();
            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            $pdo->prepare("UPDATE users SET password_hash=?, updated_at=NOW() WHERE id=?")->execute([$hash, $id]);
            sendResetEmail($user['email'], $user['username'], $newPassword);
            echo "<script>alert('Password baru telah dikirim ke email pengguna.');window.location='master_user.php';</script>";
            exit;
        }
    }
}

// === AMBIL DATA USER UNTUK LIST ===
$users = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Master User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <h3 class="text-center mb-4">👥 Master User Management</h3>
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <span>Daftar User</span>
                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">+ Tambah User</button>
            </div>
            <div class="card-body">
                <table id="userTable" class="table table-bordered table-striped align-middle table-sm">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Role</th>
                            <th>Aktif</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['username']) ?></td>
                                <td><?= htmlspecialchars($u['full_name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= htmlspecialchars($u['phone']) ?></td>
                                <td class="text-center"><?= $u['role_id'] == 1 ? '<span class="badge bg-danger">Admin</span>' : '<span class="badge bg-success">Vendor</span>' ?></td>
                                <td class="text-center"><?= $u['is_active'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
                                <td><?= htmlspecialchars($u['created_at']) ?></td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm"
                                        onclick="editUser(<?= $u['id'] ?>,'<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>','<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>','<?= htmlspecialchars($u['full_name'], ENT_QUOTES) ?>','<?= htmlspecialchars($u['phone'], ENT_QUOTES) ?>',<?= $u['role_id'] ?>,<?= $u['is_active'] ?>)">✏️</button>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Hapus user ini?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <button class="btn btn-danger btn-sm">🗑️</button>
                                    </form>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Reset password user ini?');">
                                        <input type="hidden" name="action" value="reset">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <button class="btn btn-info btn-sm text-white">🔑</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalAdd" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Tambah User Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
                        <div class="mb-3"><label>Nama Lengkap</label><input type="text" name="full_name" class="form-control" required></div>
                        <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                        <div class="mb-3"><label>No. Telepon</label><input type="text" name="phone" class="form-control"></div>
                        <div class="mb-3"><label>Role</label>
                            <select name="role_id" class="form-select">
                                <option value="1">Admin Universitas</option>
                                <option value="3">Vendor/Perusahaan</option>
                            </select>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="aktifAdd" checked>
                            <label class="form-check-label" for="aktifAdd">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3"><label>Username</label><input type="text" name="username" id="edit_username" class="form-control" required></div>
                        <div class="mb-3"><label>Nama Lengkap</label><input type="text" name="full_name" id="edit_full_name" class="form-control" required></div>
                        <div class="mb-3"><label>Email</label><input type="email" name="email" id="edit_email" class="form-control" required></div>
                        <div class="mb-3"><label>No. Telepon</label><input type="text" name="phone" id="edit_phone" class="form-control"></div>
                        <div class="mb-3"><label>Role</label>
                            <select name="role_id" id="edit_role_id" class="form-select">
                                <option value="1">Admin Universitas</option>
                                <option value="3">Vendor/Perusahaan</option>
                            </select>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                            <label class="form-check-label" for="edit_is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-warning">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script>
        $(function() {
            $('#userTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        className: 'btn btn-success btn-sm',
                        text: '📗 Excel'
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger btn-sm',
                        text: '📕 PDF'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-secondary btn-sm',
                        text: '🖨️ Print'
                    }
                ],
                pageLength: 10,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Tidak ada data ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)"
                }
            });
        });

        function editUser(id, username, email, full_name, phone, role_id, is_active) {
            $('#edit_id').val(id);
            $('#edit_username').val(username);
            $('#edit_email').val(email);
            $('#edit_full_name').val(full_name);
            $('#edit_phone').val(phone);
            $('#edit_role_id').val(role_id);
            $('#edit_is_active').prop('checked', is_active == 1);
            new bootstrap.Modal(document.getElementById('modalEdit')).show();
        }
    </script>
</body>

</html>