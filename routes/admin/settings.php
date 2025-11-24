<?php
// Admin route: public link settings (see how it works, support, signup toggle)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../core/config.php';

rp_start_session();
rp_require_admin();

$pdo = rp_get_pdo();

$seeUrl = rp_get_setting('see_how_it_works_url', '');
$supportUrl = rp_get_setting('support_url', '');
$signupEnabled = rp_is_signup_enabled();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seeInput = isset($_POST['see_url']) ? trim($_POST['see_url']) : '';
    $supportInput = isset($_POST['support_url']) ? trim($_POST['support_url']) : '';
    $signupInput = isset($_POST['signup_enabled']) && $_POST['signup_enabled'] === '1' ? '1' : '0';

    try {
        $stmt = $pdo->prepare('INSERT INTO ' . RP_DB_PREFIX . 'settings(`key`,`value`) VALUES(:k,:v)
            ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');

        $stmt->execute([':k' => 'see_how_it_works_url', ':v' => $seeInput]);
        $stmt->execute([':k' => 'support_url', ':v' => $supportInput]);
        $stmt->execute([':k' => 'signup_enabled', ':v' => $signupInput]);

        $seeUrl = $seeInput;
        $supportUrl = $supportInput;
        $signupEnabled = $signupInput === '1';
        $message = 'Settings updated.';
    } catch (Throwable $e) {
        $error = 'Unable to save settings.';
    }
}

$currentAdmin = rp_current_user();

require __DIR__ . '/../../views/admin/settings.php';