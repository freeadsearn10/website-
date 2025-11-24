<?php
// Auth route: login
require_once __DIR__ . '/../../config.php';

if (function_exists('rp_ensure_installed')) {
    rp_ensure_installed();
}
rp_start_session();

// If already logged in, go straight to portal
if (rp_current_user()) {
    header('Location: /portal');
    exit;
}

$error = '';
$loginCaptchaType = function_exists('rp_get_setting') ? rp_get_setting('login_captcha_type', 'none') : 'none';
$recaptchaSiteKey = function_exists('rp_get_setting') ? rp_get_setting('recaptcha_site_key', '') : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Captcha check
    if ($loginCaptchaType === 'math') {
        $answer = isset($_POST['captcha_answer']) ? trim($_POST['captcha_answer']) : '';
        $expected = isset($_SESSION['login_math_answer']) ? $_SESSION['login_math_answer'] : '';
        if ($answer === '' || strval($expected) !== $answer) {
            $error = 'Captcha is incorrect.';
        }
    } elseif ($loginCaptchaType === 'google') {
        $token = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
        $secret = function_exists('rp_get_setting') ? rp_get_setting('recaptcha_secret_key', '') : '';
        if ($secret && $token) {
            $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secret) .
                '&response=' . urlencode($token) . '&remoteip=' . urlencode($_SERVER['REMOTE_ADDR']);
            $verifyResponse = @file_get_contents($verifyUrl);
            $ok = false;
            if ($verifyResponse !== false) {
                $data = json_decode($verifyResponse, true);
                $ok = isset($data['success']) && $data['success'];
            }
            if (!$ok) {
                $error = 'Captcha verification failed.';
            }
        } else {
            $error = 'Captcha verification failed.';
        }
    }

    if ($error === '') {
        if ($email === '' || $password === '') {
            $error = 'Please enter email and password.';
        } else {
            try {
                $pdo = rp_get_pdo();
                $stmt = $pdo->prepare('SELECT id, password_hash, status FROM ' . RP_DB_PREFIX . 'users WHERE email = :email LIMIT 1');
                $stmt->execute(array(':email' => $email));
                $user = $stmt->fetch();

                if (!$user || !password_verify($password, $user['password_hash'])) {
                    $error = 'Invalid credentials.';
                } elseif ($user['status'] !== 'active') {
                    $error = 'Your account is banned or inactive.';
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['flash_success'] = 'Login successful. Welcome back.';
                    header('Location: /portal');
                    exit;
                }
            } catch (Exception $e) {
                $error = 'Login failed.';
            }
        }
    }
}

// Prepare math captcha for display
$loginMathQuestion = '';
if ($loginCaptchaType === 'math') {
    $a = rand(1, 9);
    $b = rand(1, 9);
    $_SESSION['login_math_answer'] = $a + $b;
    $loginMathQuestion = $a . ' + ' . $b . ' = ?';
}

// Simple view (reuses existing HTML structure)
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Account Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        <?php
        // Inline CSS from the original login.html (kept same as previous login.php)
        $loginHtml = file_get_contents(__DIR__ . '/../../login.html');
        if ($loginHtml !== false && preg_match('~<style>(.*?)</style>~s', $loginHtml, $m)) {
            echo $m[1];
        }
        ?>
    </style>
</head>
<body data-page="login">
<div class="bg-bubbles">
    <div class="bubble big"></div>
    <div class="bubble small"></div>
    <div class="bubble tiny"></div>
    <div class="bubble blur"></div>
</div>

<div class="auth-shell">
    <div class="auth-card">
        <section class="auth-brand">
            <div class="auth-logo-block">
                <div class="brand-eyebrow">Refine Panel</div>
                <div class="brand-title-line1">Refine</div>
                <div class="brand-title-line2">Panel</div>
                <div class="brand-premium">Premium SMS</div>
                <p class="brand-sub">
                    Your trusted premium SMS numbers partner. Connect your apps and start earning from OTP and
                    transactional traffic.
                </p>
            </div>

            <div class="brand-cta-block">
                <p>Don’t have an account?</p>
                <p><a href="#">Contact us</a> to become a partner.</p>
            </div>

            <div class="brand-footer">
                Read our <a href="#">terms</a> and <a href="#">conditions</a>
            </div>
        </section>

        <section class="auth-login">
            <header class="login-header">
                <h1 class="login-title">Account Login</h1>
                <p class="login-subtitle">Sign in to access your dashboard and manage SMS routes.</p>
                <?php if ($error): ?>
                    <p style="margin-top:8px;font-size:12px;color:#b91c1c;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </header>

            <form method="post" action="/login">
                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <input class="form-input" type="email" id="email" name="email" placeholder="you@example.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-input" type="password" id="password" name="password" placeholder="Enter your password">
                </div>

                <?php if ($loginCaptchaType === 'math'): ?>
                    <div class="form-group">
                        <label class="form-label" for="captcha_answer">Captcha: <?php echo htmlspecialchars($loginMathQuestion, ENT_QUOTES, 'UTF-8'); ?></label>
                        <input class="form-input" type="text" id="captcha_answer" name="captcha_answer" placeholder="Answer">
                    </div>
                <?php elseif ($loginCaptchaType === 'google' && $recaptchaSiteKey): ?>
                    <div class="form-group">
                        <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptchaSiteKey, ENT_QUOTES, 'UTF-8'); ?>"></div>
                    </div>
                <?php endif; ?>

                <div class="form-row-inline">
                    <label class="checkbox">
                        <input type="checkbox">
                        <span>Remember me</span>
                    </label>
                    <a class="link-muted" href="#">Forgot password?</a>
                </div>

                <button class="btn-primary" type="submit">Log in</button>

                <p class="login-extra">
                    Need an account? <a class="link-muted" href="/signup">Sign up</a><br>
                    Having trouble signing in? <a class="link-muted" href="#">Contact support</a>.
                </p>
            </form>
        </section>
    </div>
</div>
<?php if ($loginCaptchaType === 'google' && $recaptchaSiteKey): ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
</body>
</html>