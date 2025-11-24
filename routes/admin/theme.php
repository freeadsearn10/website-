<?php
// Admin route: theme settings (uses unified admin layout)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/theme.php';

rp_start_session();
rp_require_admin();

$theme  = rp_get_theme();
$mode   = $theme['mode'];
$accent = $theme['accent'];
$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modeInput   = $_POST['mode'] ?? 'dark';
    $accentInput = $_POST['accent'] ?? 'blue';

    $modeInput = in_array($modeInput, ['dark', 'light'], true) ? $modeInput : 'dark';
    $accentInput = in_array($accentInput, ['blue', 'green', 'purple'], true) ? $accentInput : 'blue';

    try {
        $pdo = rp_get_pdo();
        $stmt = $pdo->prepare('INSERT INTO ' . RP_DB_PREFIX . 'settings(`key`,`value`) VALUES(:k,:v)
            ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');

        $stmt->execute([':k' => 'theme_mode', ':v' => $modeInput]);
        $stmt->execute([':k' => 'theme_accent', ':v' => $accentInput]);

        $mode = $modeInput;
        $accent = $accentInput;
        $message = 'Theme updated.';
    } catch (Throwable $e) {
        $error = 'Unable to save theme settings.';
    }
}

$currentAdmin = rp_current_user();

require __DIR__ . '/../../views/admin/theme.php';