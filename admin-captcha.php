<?php
// Admin page to control captchas for login and signup (none, math, Google reCAPTCHA).
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

rp_start_session();

$user = null;
if (function_exists('rp_current_user')) {
    $user = rp_current_user();
}

if (!$user || !isset($user['role']) || $user['role'] !== 'admin') {
    header('Location: login');
    exit;
}

$loginType = rp_get_setting('login_captcha_type', 'none');
$signupType = rp_get_setting('signup_captcha_type', 'none');
$siteKey = rp_get_setting('recaptcha_site_key', '');
$secretKey = rp_get_setting('recaptcha_secret_key', '');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginTypeInput = isset($_POST['login_type']) ? $_POST['login_type'] : 'none';
    $signupTypeInput = isset($_POST['signup_type']) ? $_POST['signup_type'] : 'none';
    $siteKeyInput = isset($_POST['site_key']) ? trim($_POST['site_key']) : '';
    $secretKeyInput = isset($_POST['secret_key']) ? trim($_POST['secret_key']) : '';

    $allowed = array('none', 'math', 'google');
    if (!in_array($loginTypeInput, $allowed, true)) {
        $loginTypeInput = 'none';
    }
    if (!in_array($signupTypeInput, $allowed, true)) {
        $signupTypeInput = 'none';
    }

    $pdo = rp_get_pdo();
    $stmt = $pdo->prepare('INSERT INTO ' . RP_DB_PREFIX . 'settings(`key`,`value`) VALUES(:k,:v)
        ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');

    $stmt->execute(array(':k' => 'login_captcha_type', ':v' => $loginTypeInput));
    $stmt->execute(array(':k' => 'signup_captcha_type', ':v' => $signupTypeInput));
    $stmt->execute(array(':k' => 'recaptcha_site_key', ':v' => $siteKeyInput));
    $stmt->execute(array(':k' => 'recaptcha_secret_key', ':v' => $secretKeyInput));

    $loginType = $loginTypeInput;
    $signupType = $signupTypeInput;
    $siteKey = $siteKeyInput;
    $secretKey = $secretKeyInput;
    $message = 'Captcha settings updated.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Captcha settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(145deg, #2b2eec, #00b5ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f172a;
        }
        .shell {
            width: 100%;
            max-width: 600px;
            padding: 32px 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 18px;
            padding: 24px 22px 22px;
            box-shadow:
                0 24px 60px rgba(15, 23, 42, 0.35),
                0 0 0 1px rgba(255, 255, 255, 0.9);
        }
        h1 {
            font-size: 20px;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 14px;
        }
        .section-title {
            font-size: 13px;
            font-weight: 600;
            margin-top: 12px;
            margin-bottom: 6px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        label {
            font-size: 12px;
            color: #4b5563;
        }
        .radio-row {
            display: flex;
            gap: 12px;
            margin-top: 4px;
            font-size: 12px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px 9px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            font-size: 13px;
            margin-top: 4px;
        }
        .btn {
            margin-top: 12px;
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, #1b22d8, #2b2eec);
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        .alert {
            margin-bottom: 10px;
            font-size: 12px;
            color: #166534;
            background: #dcfce7;
            padding: 6px 8px;
            border-radius: 6px;
        }
        .links {
            margin-top: 12px;
            font-size: 12px;
        }
        .links a {
            color: #1d4ed8;
            text-decoration: none;
        }
        small {
            display: block;
            font-size: 11px;
            color: #9ca3af;
            margin-top: 2px;
        }
    </style>
</head>
<body>
<div class="shell">
    <div class="card">
        <h1>Captcha settings</h1>
        <p class="subtitle">
            Configure captcha for login and signup. You can use simple math captcha or Google reCAPTCHA v2.
        </p>

        <?php if ($message): ?>
            <div class="alert"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="section-title">Login captcha</div>
            <div class="form-group">
                <div class="radio-row">
                    <label><input type="radio" name="login_type" value="none" <?php echo $loginType === 'none' ? 'checked' : ''; ?>> None</label>
                    <label><input type="radio" name="login_type" value="math" <?php echo $loginType === 'math' ? 'checked' : ''; ?>> Math</label>
                    <label><input type="radio" name="login_type" value="google" <?php echo $loginType === 'google' ? 'checked' : ''; ?>> Google reCAPTCHA</label>
                </div>
            </div>

            <div class="section-title">Signup captcha</div>
            <div class="form-group">
                <div class="radio-row">
                    <label><input type="radio" name="signup_type" value="none" <?php echo $signupType === 'none' ? 'checked' : ''; ?>> None</label>
                    <label><input type="radio" name="signup_type" value="math" <?php echo $signupType === 'math' ? 'checked' : ''; ?>> Math</label>
                    <label><input type="radio" name="signup_type" value="google" <?php echo $signupType === 'google' ? 'checked' : ''; ?>> Google reCAPTCHA</label>
                </div>
            </div>

            <div class="section-title">Google reCAPTCHA keys</div>
            <div class="form-group">
                <label for="site_key">Site key</label>
                <input type="text" id="site_key" name="site_key"
                       value="<?php echo htmlspecialchars($siteKey, ENT_QUOTES, 'UTF-8'); ?>">
                <small>From Google reCAPTCHA console (v2 “I’m not a robot” checkbox).</small>
            </div>
            <div class="form-group">
                <label for="secret_key">Secret key</label>
                <input type="text" id="secret_key" name="secret_key"
                       value="<?php echo htmlspecialchars($secretKey, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <button class="btn" type="submit">Save captcha settings</button>
        </form>

        <div class="links">
            <a href="admin">Back to admin</a> ·
            <a href="login">Test login form</a> ·
            <a href="signup">Test signup form</a>
        </div>
    </div>
</div>
</body>
</html>