<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = (int) $_GET['id'];
$section = $_GET['section'] ?? 'identitas';

// tandai sebagai sudah dibaca
$upd = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
$upd->execute([$id, $_SESSION['user_id']]);

// redirect ke tab terkait
header("Location: company_data.php#{$section}");
exit;
