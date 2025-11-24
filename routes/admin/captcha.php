<?php
// Admin route: captcha settings (uses unified admin layout)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../core/config.php';

rp_start_session();
rp_require_admin();

$loginType  = rp_get_setting('login_captcha_type', 'none');
$signupType = rp_get_setting('signup_captcha_type', 'none');
$siteKey    = rp_get_setting('recaptcha_site_key', '');
$secretKey  = rp_get_setting('recaptcha_secret_key', '');
$message    = '';
$error      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginTypeInput  = $_POST['login_type'] ?? 'none';
    $signupTypeInput = $_POST['signup_type'] ?? 'none';
    $siteKeyInput    = isset($_POST['site_key']) ? trim($_POST['site_key']) : '';
    $secretKeyInput  = isset($_POST['secret_key']) ? trim($_POST['secret_key']) : '';

    $allowed = ['none', 'math', 'google'];
    if (!in_array($loginTypeInput, $allowed, true)) {
        $loginTypeInput = 'none';
    }
    if (!in_array($signupTypeInput, $allowed, true)) {
        $signupTypeInput = 'none';
    }

    try {
        $pdo = rp_get_pdo();
        $stmt = $pdo->prepare('INSERT INTO ' . RP_DB_PREFIX . 'settings(`key`,`value`) VALUES(:k,:v)
            ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');

        $stmt->execute([':k' => 'login_captcha_type', ':v' => $loginTypeInput]);
        $stmt->execute([':k' => 'signup_captcha_type', ':v' => $signupTypeInput]);
        $stmt->execute([':k' => 'recaptcha_site_key', ':v' => $siteKeyInput]);
        $stmt->execute([':k' => 'recaptcha_secret_key', ':v' => $secretKeyInput]);

        $loginType  = $loginTypeInput;
        $signupType = $signupTypeInput;
        $siteKey    = $siteKeyInput;
        $secretKey  = $secretKeyInput;
        $message    = 'Captcha settings updated.';
    } catch (Throwable $e) {
        $error = 'Unable to save captcha settings.';
    }
}

$currentAdmin = rp_current_user();

require __DIR__ . '/../../views/admin/captcha.php';