<?php
// Admin page to configure public links (See how it works, Support, etc.)
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

$seeUrl = rp_get_setting('see_how_it_works_url', '');
$supportUrl = rp_get_setting('support_url', '');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seeInput = isset($_POST['see_url']) ? trim($_POST['see_url']) : '';
    $supportInput = isset($_POST['support_url']) ? trim($_POST['support_url']) : '';

    $pdo = rp_get_pdo();
    $stmt = $pdo->prepare('INSERT INTO ' . RP_DB_PREFIX . 'settings(`key`,`value`) VALUES(:k,:v)
        ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');

    $stmt->execute(array(':k' => 'see_how_it_works_url', ':v' => $seeInput));
    $stmt->execute(array(':k' => 'support_url', ':v' => $supportInput));

    $seeUrl = $seeInput;
    $supportUrl = $supportInput;
    $message = 'Links updated.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Link settings</title>
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
            max-width: 520px;
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
        .form-group {
            margin-bottom: 12px;
        }
        label {
            display: block;
            font-size: 12px;
            margin-bottom: 4px;
            color: #4b5563;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px 9px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            font-size: 13px;
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
        <h1>Public link settings</h1>
        <p class="subtitle">Control URLs for “See how it works” and “Support” links on the landing page.</p>

        <?php if ($message): ?>
            <div class="alert"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="see_url">See how it works URL</label>
                <input type="text" id="see_url" name="see_url"
                       value="<?php echo htmlspecialchars($seeUrl, ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="https://your-docs-or-landing.com/how-it-works">
                <small>Landing page “See how it works” link → /learn-more → this URL.</small>
            </div>

            <div class="form-group">
                <label for="support_url">Support URL</label>
                <input type="text" id="support_url" name="support_url"
                       value="<?php echo htmlspecialchars($supportUrl, ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="https://your-support-portal.com">
                <small>Top navbar “Support” menu → /support → this URL.</small>
            </div>

            <button class="btn" type="submit">Save links</button>
        </form>

        <div class="links">
            <a href="admin">Back to admin</a> ·
            <a href="./">View landing</a>
        </div>
    </div>
</div>
</body>
</html>