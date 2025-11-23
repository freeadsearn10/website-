<?php
// Auth route: signup
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config.php';

rp_start_session();

$error = '';
$signupCaptchaType = function_exists('rp_get_setting') ? rp_get_setting('signup_captcha_type', 'none') : 'none';
$recaptchaSiteKey = function_exists('rp_get_setting') ? rp_get_setting('recaptcha_site_key', '') : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $phone    = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $country  = isset($_POST['country']) ? trim($_POST['country']) : '';
    $teamId   = isset($_POST['team_id']) ? trim($_POST['team_id']) : '';
    $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Captcha check
    if ($signupCaptchaType === 'math') {
        $answer = isset($_POST['captcha_answer']) ? trim($_POST['captcha_answer']) : '';
        $expected = isset($_SESSION['signup_math_answer']) ? $_SESSION['signup_math_answer'] : '';
        if ($answer === '' || strval($expected) !== $answer) {
            $error = 'Captcha is incorrect.';
        }
    } elseif ($signupCaptchaType === 'google') {
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
        if ($name === '' || $email === '' || $password === '') {
            $error = 'Name, email and password are required.';
        } else {
            try {
                $pdo = rp_get_pdo();

                // Check if email already exists
                $stmt = $pdo->prepare('SELECT id FROM ' . RP_DB_PREFIX . 'users WHERE email = :email LIMIT 1');
                $stmt->execute(array(':email' => $email));
                if ($stmt->fetch()) {
                    $error = 'This email is already registered.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);

                    $sql = 'INSERT INTO ' . RP_DB_PREFIX . 'users
                            (name, email, password_hash, role, phone, country, team_id, status)
                            VALUES (:name, :email, :hash, \'user\', :phone, :country, :team_id, \'active\')';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(array(
                        ':name'    => $name,
                        ':email'   => $email,
                        ':hash'    => $hash,
                        ':phone'   => $phone,
                        ':country' => $country,
                        ':team_id' => $teamId,
                    ));

                    $userId = $pdo->lastInsertId();
                    $_SESSION['user_id'] = $userId;

                    header('Location: /portal');
                    exit;
                }
            } catch (Exception $e) {
                $error = 'Signup failed.';
            }
        }
    }
}

// Prepare math captcha
$signupMathQuestion = '';
if ($signupCaptchaType === 'math') {
    $x = rand(1, 9);
    $y = rand(1, 9);
    $_SESSION['signup_math_answer'] = $x + $y;
    $signupMathQuestion = $x . ' + ' . $y . ' = ?';
}

// View reuses signup.html styles/layout
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Sign Up</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        <?php
        $html = file_get_contents(__DIR__ . '/../../signup.html');
        if ($html !== false && preg_match('~<style>(.*?)</style>~s', $html, $m)) {
            echo $m[1];
        }
        ?>
        .signup-error {
            margin-bottom: 10px;
            font-size: 12px;
            color: #b91c1c;
        }
    </style>
</head>
<body data-page="signup">
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
                    Create your partner account to start monetizing OTP and transactional SMS routes with
                    Refine Panel.
                </p>
            </div>

            <div class="brand-cta-block">
                <p>Already have an account?</p>
                <p><a href="/login">Log in here</a> to access your dashboard.</p>
            </div>

            <div class="brand-footer">
                By signing up you agree to our <a href="#">terms</a> and <a href="#">conditions</a>.
            </div>
        </section>

        <section class="auth-form">
            <header class="form-header">
                <h1 class="form-title">Create partner account</h1>
                <p class="form-subtitle">Fill in the details below to request access.</p>
                <?php if ($error): ?>
                    <p class="signup-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </header>

            <form method="post" action="/signup">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="full-name">Full name</label>
                        <input class="form-input" id="full-name" name="full_name" type="text"
                               value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                               placeholder="Your name">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Mobile number</label>
                        <input class="form-input" id="phone" name="phone" type="tel"
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                               placeholder="+8801XXXXXXXXX">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="country">Country</label>
                        <select class="form-select" id="country" name="country">
                            <?php
                            $countries = array('', 'Bangladesh', 'India', 'Pakistan', 'Indonesia', 'Philippines', 'Other');
                            $selectedCountry = isset($_POST['country']) ? $_POST['country'] : '';
                            foreach ($countries as $c) {
                                $label = $c === '' ? 'Select your country' : $c;
                                $value = $c;
                                $sel = ($value === $selectedCountry) ? ' selected' : '';
                                echo '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' .
                                     htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="team-id">Teams ID</label>
                        <input class="form-input" id="team-id" name="team_id" type="text"
                               value="<?php echo isset($_POST['team_id']) ? htmlspecialchars($_POST['team_id'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                               placeholder="Your Teams / reseller ID">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email address</label>
                        <input class="form-input" id="email" name="email" type="email"
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                               placeholder="you@example.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input class="form-input" id="password" name="password" type="password"
                               placeholder="Choose a password">
                    </div>

                    <?php if ($signupCaptchaType === 'math'): ?>
                        <div class="form-group">
                            <label class="form-label" for="captcha_answer">Captcha: <?php echo htmlspecialchars($signupMathQuestion, ENT_QUOTES, 'UTF-8'); ?></label>
                            <input class="form-input" type="text" id="captcha_answer" name="captcha_answer" placeholder="Answer">
                        </div>
                    <?php elseif ($signupCaptchaType === 'google' && $recaptchaSiteKey): ?>
                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptchaSiteKey, ENT_QUOTES, 'UTF-8'); ?>"></div>
                        </div>
                    <?php endif; ?>
                </div>

                <button class="btn-primary" type="submit">Create account</button>

                <p class="form-extra">
                    We will review your information and contact you on Teams or email with next steps.
                </p>
            </form>
        </section>
    </div>
</div>
<?php if ($signupCaptchaType === 'google' && $recaptchaSiteKey): ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
</body>
</html>