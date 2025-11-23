<?php
require_once __DIR__ . '/config.php';

rp_ensure_installed();
rp_require_login();

$user = rp_current_user();
$accent = $_GET['accent'] ?? '';

$allowed = ['blue', 'green', 'purple'];
if (in_array($accent, $allowed, true)) {
    $pdo = rp_get_pdo();
    $stmt = $pdo->prepare('UPDATE ' . RP_DB_PREFIX . 'users SET accent = :accent WHERE id = :id');
    $stmt->execute([
        ':accent' => $accent,
        ':id' => $user['id'],
    ]);
}

header('Location: portal');
exit;