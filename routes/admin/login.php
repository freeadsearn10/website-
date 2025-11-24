<?php
// Admin-only login route with its own UI.
// URL: /admin-login
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../core/config.php';

rp_start_session();

// If already logged in as admin, go straight to /admin
$current = rp_current_user();
if ($current && isset($current['role']) && $current['role'] === 'admin') {
    header('Location: /admin');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($email === '' || $password === '') {
        $error = 'Please enter email and password.';
    } else {
        try {
            $pdo = rp_get_pdo();
            $sql = 'SELECT id, password_hash, role, status FROM ' . RP_DB_PREFIX . 'users WHERE email = :email LIMIT 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(':email' => $email));
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $error = 'Invalid credentials.';
            } elseif ($user['status'] !== 'active') {
                $error = 'Your account is banned or inactive.';
            } elseif ($user['role'] !== 'admin') {
                $error = 'This login is only for admin accounts.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                header('Location: /admin');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Login failed.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --primary-start: #0f172a;
            --primary-end: #1e293b;
            --accent: #22c55e;
            --accent-soft: rgba(34, 197, 94, 0.2);
            --accent-strong: #16a34a;
            --text-main: #f9fafb;
            --text-muted: #94a3b8;
            --card-bg: rgba(15, 23, 42, 0.92);
            --border-soft: rgba(148, 163, 184, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(circle at top left, #1e293b, #020617);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
        }

        .admin-login-shell {
            width: 100%;
            max-width: 960px;
            padding: 32px 20px;
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
            gap: 32px;
        }

        .admin-login-brand {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-top {
            margin-bottom: 24px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .brand-mark {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: conic-gradient(from 140deg, #22c55e, #38bdf8, #6366f1, #22c55e);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #0f172a;
            font-weight: 700;
            font-size: 16px;
        }

        .brand-text-main {
            font-weight: 700;
            letter-spacing: 0.12em;
            font-size: 15px;
            text-transform: uppercase;
        }

        .brand-text-sub {
            font-size: 11px;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .brand-title {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .brand-desc {
            font-size: 13px;
            color: var(--text-muted);
            max-width: 360px;
            line-height: 1.6;
        }

        .brand-metric-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 22px;
        }

        .brand-metric {
            padding: 8px 10px;
            border-radius: 10px;
            border: 1px solid var(--border-soft);
            font-size: 11px;
            color: var(--text-muted);
        }

        .brand-metric strong {
            display: block;
            font-size: 14px;
            color: var(--text-main);
        }

        .admin-login-card {
            border-radius: 18px;
            background: var(--card-bg);
            border: 1px solid var(--border-soft);
            box-shadow:
                0 24px 70px rgba(0, 0, 0, 0.7),
                0 0 0 1px rgba(15, 23, 42, 0.5);
            padding: 22px 22px 20px;
        }

        .admin-login-header {
            margin-bottom: 18px;
        }

        .admin-login-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 8px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.6);
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .admin-login-badge span.dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: radial-gradient(circle at 30% 30%, #fff, var(--accent));
        }

        .admin-login-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .admin-login-subtitle {
            font-size: 12px;
            color: var(--text-muted);
        }

        .admin-form-group {
            margin-top: 16px;
            margin-bottom: 14px;
        }

        .admin-form-label {
            display: block;
            font-size: 12px;
            margin-bottom: 4px;
            color: var(--text-muted);
        }

        .admin-form-input {
            width: 100%;
            padding: 9px 10px;
            border-radius: 8px;
            border: 1px solid rgba(148, 163, 184, 0.6);
            background: rgba(15, 23, 42, 0.7);
            color: var(--text-main);
            font-size: 13px;
            outline: none;
            transition: border-color 0.16s ease, box-shadow 0.16s ease, background 0.16s ease;
        }

        .admin-form-input:focus {
            border-color: var(--accent);
            background: rgba(15, 23, 42, 0.95);
            box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.4);
        }

        .admin-form-input::placeholder {
            color: #64748b;
        }

        .admin-login-footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .admin-remember {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .admin-remember input {
            width: 13px;
            height: 13px;
            border-radius: 3px;
            border: 1px solid rgba(148, 163, 184, 0.7);
            accent-color: var(--accent);
        }

        .admin-login-button {
            margin-top: 16px;
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, var(--accent-strong), #22c55e);
            color: #0f172a;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            box-shadow: 0 18px 45px rgba(34, 197, 94, 0.4);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .admin-login-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 24px 60px rgba(34, 197, 94, 0.55);
        }

        .admin-login-button span.arrow {
            font-size: 14px;
        }

        .admin-login-error {
            margin-top: 10px;
            font-size: 12px;
            color: #fecaca;
            background: rgba(239, 68, 68, 0.15);
            border-radius: 8px;
            padding: 6px 8px;
            border: 1px solid rgba(248, 113, 113, 0.4);
        }

        .admin-login-links {
            margin-top: 12px;
            font-size: 11px;
            color: var(--text-muted);
        }

        .admin-login-links a {
            color: var(--accent);
            text-decoration: none;
        }

        .admin-login-links a:hover {
            text-decoration: underline;
        }

        @media (max-width: 880px) {
            .admin-login-shell {
                grid-template-columns: minmax(0, 1fr);
                gap: 20px;
            }

            body {
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            .admin-login-shell {
                padding-inline: 14px;
            }

            .admin-login-card {
                padding-inline: 18px;
            }
        }
    </style>
</head>
<body>
<div class="admin-login-shell">
    <section class="admin-login-brand">
        <div class="brand-top">
            <div class="brand-logo">
                <div class="brand-mark">R</div>
                <div>
                    <div class="brand-text-main">Refine Panel</div>
                    <div class="brand-text-sub">PREMIUM RATE SMS</div>
                </div>
            </div>
            <div class="brand-title">Admin Console</div>
            <p class="brand-desc">
                Dedicated login for Refine Panel administrators. Manage partners, payouts, routes and platform
                configuration from a single secure dashboard.
            </p>
        </div>

        <div class="brand-metric-row">
            <div class="brand-metric">
                Active partners
                <strong>32</strong>
            </div>
            <div class="brand-metric">
                Today’s OTP volume
                <strong>148k</strong>
            </div>
            <div class="brand-metric">
                Pending approvals
                <strong>5</strong>
            </div>
        </div>
    </section>

    <section class="admin-login-card">
        <header class="admin-login-header">
            <div class="admin-login-badge">
                <span class="dot"></span>
                <span>Admin login</span>
            </div>
            <div class="admin-login-title">Sign in as administrator</div>
            <p class="admin-login-subtitle">
                Use your admin credentials. Normal user accounts cannot access this console.
            </p>
        </header>

        <form method="post" action="/admin-login">
            <div class="admin-form-group">
                <label class="admin-form-label" for="email">Admin email</label>
                <input class="admin-form-input" type="email" id="email" name="email"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                       placeholder="admin@refinepanel.com">
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="password">Password</label>
                <input class="admin-form-input" type="password" id="password" name="password"
                       placeholder="Enter your admin password">
            </div>

            <div class="admin-login-footer-row">
                <label class="admin-remember">
                    <input type="checkbox">
                    <span>Remember on this device</span>
                </label>
                <a href="/login" style="color:var(--text-muted);text-decoration:none;">Back to user login</a>
            </div>

            <button class="admin-login-button" type="submit">
                Sign in to admin
                <span class="arrow">→</span>
            </button>

            <?php if ($error): ?>
                <div class="admin-login-error">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="admin-login-links">
                Need help? <a href="/support">Contact technical support</a>.
            </div>
        </form>
    </section>
</div>
</body>
</html>