<?php
// Admin route: theme settings
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../theme.php';

rp_start_session();

$user = null;
if (function_exists('rp_current_user')) {
    $user = rp_current_user();
}

if (!$user || !isset($user['role']) || $user['role'] !== 'admin') {
    header('Location: /login');
    exit;
}

$theme = rp_get_theme();
$mode = $theme['mode'];
$accent = $theme['accent'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modeInput = $_POST['mode'] ?? 'dark';
    $accentInput = $_POST['accent'] ?? 'blue';

    $modeInput = in_array($modeInput, ['dark', 'light'], true) ? $modeInput : 'dark';
    $accentInput = in_array($accentInput, ['blue', 'green', 'purple'], true) ? $accentInput : 'blue';

    $pdo = rp_get_pdo();
    $stmt = $pdo->prepare('INSERT INTO ' . RP_DB_PREFIX . 'settings(`key`,`value`) VALUES(:k,:v)
        ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');

    $stmt->execute([':k' => 'theme_mode', ':v' => $modeInput]);
    $stmt->execute([':k' => 'theme_accent', ':v' => $accentInput]);

    $mode = $modeInput;
    $accent = $accentInput;
    $message = 'Theme updated.';
}

require __DIR__ . '/../../admin-theme.php';