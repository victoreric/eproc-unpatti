<?php
session_start();
require '../config/db.php';

// ========================================================
// CEK AKSES ADMIN
// ========================================================
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    die("Akses ditolak. Hanya admin yang bisa mengakses halaman ini.");
}

// ========================================================
// FUNGSI HAPUS USER
// ========================================================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Hapus perusahaan jika role_id = 3
    $pdo->prepare("DELETE FROM companies WHERE user_id=:id")->execute([':id' => $id]);
    $pdo->prepare("DELETE FROM users WHERE id=:id")->execute([':id' => $id]);

    header("Location: master_user.php?msg=deleted");
    exit;
}

// ========================================================
// RESET PASSWORD USER
// ========================================================
if (isset($_POST['reset_pass'])) {
    $id = intval($_POST['user_id']);
    $newpass = password_hash("12345678", PASSWORD_BCRYPT);

    $pdo->prepare("UPDATE users SET password_hash=:p WHERE id=:id")
        ->execute([':p' => $newpass, ':id' => $id]);

    header("Location: master_user.php?msg=reset");
    exit;
}

// ========================================================
// SIMPAN TAMBAH USER
// ========================================================
if (isset($_POST['save_user'])) {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $username  = trim($_POST['username']);
    $role_id   = intval($_POST['role_id']);

    // Cek duplikasi
    $check = $pdo->prepare("SELECT id FROM users WHERE email=:email OR username=:username");
    $check->execute([':email' => $email, ':username' => $username]);

    if ($check->rowCount() > 0) {
        $error = "Username atau Email sudah digunakan.";
    } else {
        $password_hash = password_hash("12345678", PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, full_name, phone, role_id, is_active, email_verified, created_at)
            VALUES (:u, :e, :p, :f, :ph, :r, 1, 1, NOW())
        ");
        $stmt->execute([
            ':u'  => $username,
            ':e'  => $email,
            ':p'  => $password_hash,
            ':f'  => $full_name,
            ':ph' => $phone,
            ':r'  => $role_id
        ]);

        header("Location: master_user.php?msg=added");
        exit;
    }
}

// ========================================================
// UPDATE USER
// ========================================================
if (isset($_POST['update_user'])) {
    $id        = intval($_POST['user_id']);
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $username  = trim($_POST['username']);
    $role_id   = intval($_POST['role_id']);

    // Cek duplikasi email/username kecuali miliknya sendiri
    $check = $pdo->prepare("
        SELECT id FROM users 
        WHERE (email=:email OR username=:username) AND id!=:id
    ");
    $check->execute([':email' => $email, ':username' => $username, ':id' => $id]);

    if ($check->rowCount() > 0) {
        $error = "Username atau Email sudah digunakan oleh user lain.";
    } else {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET username=:u, email=:e, full_name=:f, phone=:p, role_id=:r 
            WHERE id=:id
        ");
        $stmt->execute([
            ':u' => $username,
            ':e' => $email,
            ':f' => $full_name,
            ':p' => $phone,
            ':r' => $role_id,
            ':id' => $id
        ]);

        header("Location: master_user.php?msg=updated");
        exit;
    }
}

// ========================================================
// AMBIL DATA USER
// ========================================================
$users = $pdo->query("
    SELECT 
        u.id, u.username, u.full_name, u.email, u.phone, u.is_active, 
        u.created_at, u.role_id,
        r.name AS role_name
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    ORDER BY u.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// AMBIL ROLE
$roles = $pdo->query("SELECT id, name FROM roles ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>

<head>
    <title>Master User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">

        <h3 class="mb-4">Master User</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?php
                if ($_GET['msg'] == "added") echo "User berhasil ditambahkan.";
                if ($_GET['msg'] == "updated") echo "User berhasil diperbarui.";
                if ($_GET['msg'] == "deleted") echo "User berhasil dihapus.";
                if ($_GET['msg'] == "reset") echo "Password user berhasil direset ke '12345678'.";
                ?>
            </div>
        <?php endif; ?>

        <!-- BUTTON TAMBAH -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAdd">+ Tambah User</button>

        <!-- TABLE USER -->
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>No HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= $u['full_name'] ?></td>
                        <td><?= $u['username'] ?></td>
                        <td><?= $u['email'] ?></td>
                        <td><?= $u['role_name'] ?></td>
                        <td>
                            <?php if ($u['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Non Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $u['phone'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit<?= $u['id'] ?>">Edit</button>

                            <form action="" method="POST" class="d-inline">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button class="btn btn-sm btn-secondary" name="reset_pass"
                                    onclick="return confirm('Reset password user ini? Password baru: 12345678');">
                                    Reset PW
                                </button>
                            </form>

                            <a href="master_user.php?delete=<?= $u['id'] ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Hapus user ini?')">
                                Hapus
                            </a>
                        </td>
                    </tr>

                    <!-- MODAL EDIT USER -->
                    <div class="modal fade" id="modalEdit<?= $u['id'] ?>">
                        <div class="modal-dialog">
                            <form method="POST" class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5>Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">

                                    <div class="mb-2">
                                        <label>Nama Lengkap</label>
                                        <input type="text" name="full_name" class="form-control"
                                            value="<?= $u['full_name'] ?>" required>
                                    </div>

                                    <div class="mb-2">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control"
                                            value="<?= $u['email'] ?>" required>
                                    </div>

                                    <div class="mb-2">
                                        <label>No HP</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="<?= $u['phone'] ?>">
                                    </div>

                                    <div class="mb-2">
                                        <label>Username</label>
                                        <input type="text" minlength="8" name="username"
                                            class="form-control" value="<?= $u['username'] ?>" required>
                                    </div>

                                    <div class="mb-2">
                                        <label>Role</label>
                                        <select name="role_id" class="form-control">
                                            <?php foreach ($roles as $r): ?>
                                                <option value="<?= $r['id'] ?>"
                                                    <?= $u['role_id'] == $r['id'] ? "selected" : "" ?>>
                                                    <?= $r['name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-success" name="update_user">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php endforeach; ?>
            </tbody>
        </table>


        <!-- MODAL TAMBAH USER -->
        <div class="modal fade" id="modalAdd">
            <div class="modal-dialog">
                <form method="POST" class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5>Tambah User Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-2">
                            <label>Nama Lengkap</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>No HP</label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label>Username</label>
                            <input type="text" name="username" minlength="8"
                                class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Role User</label>
                            <select name="role_id" class="form-control">
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <small class="text-muted">
                            * Password default untuk user baru: <b>12345678</b>
                        </small>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success" name="save_user">Simpan</button>
                    </div>
                </form>
            </div>
        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>