<?php
// includes/functions.php

/**
 * Cek validasi login user
 */

// ===== Fungsi Login User =====
function loginUser($pdo, $username, $password)
{
    try {
        $sql = "SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':email' => $username
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            return ['success' => true, 'user' => $user];
        }
        return ['success' => false, 'message' => 'Username atau password salah.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Kesalahan sistem: ' . $e->getMessage()];
    }
}

// ===== Fungsi Set Remember Me =====
function setRememberMe($pdo, $userId)
{
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+7 days'));
    $hashedToken = hash('sha256', $token);

    $stmt = $pdo->prepare("UPDATE users SET remember_token = :token, remember_token_expire = :expiry WHERE id = :id");
    $stmt->execute([
        ':token' => $hashedToken,
        ':expiry' => $expiry,
        ':id' => $userId
    ]);

    // Simpan ke cookie (plaintext token, bukan hash)
    setcookie(
        'remember_token',
        $userId . ':' . $token,
        [
            'expires' => time() + (86400 * 7),
            'path' => '/',
            'secure' => false,   // ubah ke true jika pakai HTTPS
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}

// ===== Fungsi Check Remember Me =====
function checkRememberMe($pdo)
{
    if (!isset($_COOKIE['remember_token'])) return false;

    $cookie = $_COOKIE['remember_token'];
    if (strpos($cookie, ':') === false) return false;

    [$userId, $token] = explode(':', $cookie, 2);
    $hashedToken = hash('sha256', $token);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id AND remember_token = :token AND remember_token_expire > NOW()");
    $stmt->execute([
        ':id' => $userId,
        ':token' => $hashedToken
    ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];
        return true;
    }

    return false;
}
