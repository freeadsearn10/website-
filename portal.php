<?php
// Show all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

rp_start_session();

// Simple login check without relying on helper functions that may not exist
$user = null;
if (function_exists('rp_current_user')) {
    $user = rp_current_user();
}

if (!$user) {
    header('Location: login');
    exit;
}

$flash = '';
if (!empty($_SESSION['flash_success'])) {
    $flash = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Static CSS copied from portal.html -->
    <style>
        :root {
            --primary-start: #2b2eec;
            --primary-end: #00b5ff;
            --accent: #f35bff;
            --text-main: #ffffff;
            --text-sub: #e4f2ff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, var(--primary-start), var(--primary-end));
            color: var(--text-main);
        }

        .portal-shell {
            max-width: 520px;
            width: 100%;
            padding: 32px 20px;
        }

        .portal-card {
            background: rgba(7, 13, 62, 0.9);
            border-radius: 18px;
            padding: 26px 24px 24px;
            box-shadow:
                0 24px 60px rgba(0, 0, 0, 0.45),
                0 0 0 1px rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(7, 18, 94, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.18);
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--text-sub);
            margin-bottom: 14px;
        }

        .badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: radial-gradient(circle at 30% 30%, #fff, var(--accent));
        }

        h1 {
            font-size: 22px;
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 13px;
            color: var(--text-sub);
            margin-bottom: 18px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 11px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.6);
            font-size: 11px;
            margin-bottom: 14px;
        }

        .pill span.label {
            color: var(--text-sub);
        }

        .pill span.status {
            padding: 2px 7px;
            border-radius: 999px;
            background: rgba(56, 189, 248, 0.15);
            color: #7dd3fc;
            font-size: 10px;
        }

        .list {
            font-size: 12px;
            color: var(--text-sub);
            margin-bottom: 18px;
        }

        .list li {
            margin-left: 16px;
            margin-bottom: 4px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 999px;
            border: none;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary {
            background: #ffffff;
            color: #1f2937;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.45);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-sub);
            border: 1px solid rgba(148, 163, 184, 0.7);
        }

        @media (max-width: 480px) {
            .portal-card {
                padding-inline: 18px;
            }
        }

        .flash-toast {
            position: fixed;
            top: 16px;
            right: 16px;
            max-width: 280px;
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(15, 23, 42, 0.96);
            border: 1px solid rgba(34, 197, 94, 0.6);
            color: #bbf7d0;
            font-size: 12px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            z-index: 50;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .flash-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .flash-close {
            border: none;
            background: none;
            color: #e5e7eb;
            font-size: 14px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php if ($flash): ?>
<div class="flash-toast" id="flashToast">
    <span><?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?></span>
    <button type="button" class="flash-close" aria-label="Close">×</button>
</div>
<?php endif; ?>
<div class="portal-shell">
    <div class="portal-card">
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

        <div class="actions">
            <a class="btn btn-primary" href="./">Back to landing</a>
            <a class="btn btn-secondary" href="logout">Log out</a>
        </div>
    </div>
</div>
<?php if ($flash): ?>
<script>
    (function () {
        var toast = document.getElementById('flashToast');
        if (!toast) return;
        setTimeout(function () {
            toast.classList.add('show');
        }, 50);

        var closeBtn = toast.querySelector('.flash-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                toast.classList.remove('show');
                setTimeout(function () {
                    if (toast && toast.parentNode) toast.parentNode.removeChild(toast);
                }, 250);
            });
        }

        setTimeout(function () {
            if (!toast) return;
            toast.classList.remove('show');
            setTimeout(function () {
                if (toast && toast.parentNode) toast.parentNode.removeChild(toast);
            }, 250);
        }, 3500);
    })();
</script>
<?php endif; ?>
</body>
</html>