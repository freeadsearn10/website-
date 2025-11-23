<?php
require_once __DIR__ . '/config.php';

rp_ensure_installed();
rp_start_session();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Please enter email and password.';
    } else {
        try {
            $pdo = rp_get_pdo();
            $stmt = $pdo->prepare('SELECT id, password_hash, status FROM ' . RP_DB_PREFIX . 'users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $error = 'Invalid credentials.';
            } elseif ($user['status'] !== 'active') {
                $error = 'Your account is banned or inactive.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                header('Location: portal');
                exit;
            }
        } catch (Throwable $e) {
            $error = 'Login failed.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Account Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        <?php
        // inline the CSS from login.html to keep styles in sync
        $loginHtml = file_get_contents(__DIR__ . '/login.html');
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

            <form method="post" action="login">
                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <input class="form-input" type="email" id="email" name="email" placeholder="you@example.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-input" type="password" id="password" name="password" placeholder="Enter your password">
                </div>

                <div class="form-row-inline">
                    <label class="checkbox">
                        <input type="checkbox">
                        <span>Remember me</span>
                    </label>
                    <a class="link-muted" href="#">Forgot password?</a>
                </div>

                <button class="btn-primary" type="submit">Log in</button>

                <p class="login-extra">
                    Need an account? <a class="link-muted" href="signup" data-signup="true">Sign up</a><br>
                    Having trouble signing in? <a class="link-muted" href="#">Contact support</a>.
                </p>
            </form>
        </section>
    </div>
</div>
</body>
</html>