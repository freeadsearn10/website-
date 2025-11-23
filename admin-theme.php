<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/theme.php';

rp_ensure_installed();
rp_require_admin();

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
    $theme = ['mode' => $mode, 'accent' => $accent];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Theme settings</title>
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
        select {
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
    </style>
</head>
<body>
<div class="shell">
    <div class="card">
        <h1>Theme settings</h1>
        <p class="subtitle">Control global night mode and default accent colour for Refine Panel.</p>

        <?php if ($message): ?>
            <div class="alert"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="mode">Mode</label>
                <select id="mode" name="mode">
                    <option value="dark" <?php echo $mode === 'dark' ? 'selected' : ''; ?>>Dark</option>
                    <option value="light" <?php echo $mode === 'light' ? 'selected' : ''; ?>>Light</option>
                </select>
            </div>

            <div class="form-group">
                <label for="accent">Default accent colour</label>
                <select id="accent" name="accent">
                    <option value="blue" <?php echo $accent === 'blue' ? 'selected' : ''; ?>>Blue</option>
                    <option value="green" <?php echo $accent === 'green' ? 'selected' : ''; ?>>Green</option>
                    <option value="purple" <?php echo $accent === 'purple' ? 'selected' : ''; ?>>Purple</option>
                </select>
            </div>

            <button class="btn" type="submit">Save theme</button>
        </form>

        <div class="links">
            <a href="admin">Back to admin</a> · <a href="portal">Go to portal</a>
        </div>
    </div>
</div>
</body>
</html>