<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/theme.php';

rp_ensure_installed();
rp_require_login();

$user = rp_current_user();
$theme = rp_get_user_theme($user);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        <?php
        // base styles from portal.html to keep visual design
        $portalHtml = file_get_contents(__DIR__ . '/portal.html');
        if ($portalHtml !== false && preg_match('~<style>(.*?)</style>~s', $portalHtml, $m)) {
            echo $m[1];
        }
        // override core colors with theme
        echo "\n" . rp_theme_css($theme);
        ?>
        .theme-choices {
            margin-top: 18px;
            font-size: 12px;
            color: var(--text-sub);
        }
        .theme-choices-buttons {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }
        .theme-chip {
            border-radius: 999px;
            padding: 6px 10px;
            border: 1px solid rgba(148,163,184,0.7);
            background: rgba(15,23,42,0.9);
            color: var(--text-sub);
            font-size: 11px;
            cursor: pointer;
            text-decoration: none;
        }
        .theme-chip span.dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 999px;
            margin-right: 6px;
        }
        .theme-chip.blue span.dot { background: linear-gradient(135deg,#2b2eec,#00b5ff); }
        .theme-chip.green span.dot { background: linear-gradient(135deg,#0f766e,#22c55e); }
        .theme-chip.purple span.dot { background: linear-gradient(135deg,#4c1d95,#7c3aed); }
    </style>
</head>
<body>
<div class="portal-shell">
    <div class="portal-card theme-card-bg">
        <div class="badge">
            <span class="badge-dot"></span>
            <span>Refine Panel Portal</span>
        </div>
        <h1>Dashboard coming soon</h1>
        <p class="subtitle">
            You have successfully logged in. The full partner portal is under development.
        </p>

        <div class="pill">
            <span class="label">Next features</span>
            <span class="status">Planned</span>
        </div>

        <ul class="list">
            <li>Real-time OTP traffic and payout analytics</li>
            <li>Route configuration and reporting</li>
            <li>Team-level access control and audit logs</li>
        </ul>

        <div class="theme-choices">
            Theme colour
            <div class="theme-choices-buttons">
                <a class="theme-chip blue" href="user-theme-save?accent=blue">
                    <span class="dot"></span>Blue
                </a>
                <a class="theme-chip green" href="user-theme-save?accent=green">
                    <span class="dot"></span>Green
                </a>
                <a class="theme-chip purple" href="user-theme-save?accent=purple">
                    <span class="dot"></span>Purple
                </a>
            </div>
        </div>

        <div class="actions" style="margin-top: 18px;">
            <a class="btn btn-primary" href="./">Back to landing</a>
            <a class="btn btn-secondary" href="login">Log out</a>
        </div>
    </div>
</div>
</body>
</html>