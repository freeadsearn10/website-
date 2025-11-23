<?php
require_once __DIR__ . '/config.php';

// Try to ensure install; if install not done this will redirect to installer.
try {
    rp_ensure_installed();
} catch (Throwable $e) {
    // ignore, installer redirect already sent
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Page not found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            background: radial-gradient(circle at top left, #1b1fdc, #00b5ff);
            color: var(--text-main);
        }

        .shell {
            max-width: 520px;
            width: 100%;
            padding: 32px 20px;
        }

        .card {
            background: rgba(7, 13, 62, 0.94);
            border-radius: 18px;
            padding: 26px 24px 24px;
            box-shadow:
                0 24px 60px rgba(0, 0, 0, 0.45),
                0 0 0 1px rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            text-align: left;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.7);
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
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 13px;
            color: var(--text-sub);
            margin-bottom: 18px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            border-radius: 999px;
            border: none;
            background: #ffffff;
            color: #111827;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.45);
        }

        .btn-primary span {
            margin-left: 6px;
        }

        @media (max-width: 480px) {
            .card {
                padding-inline: 18px;
            }
        }
    </style>
</head>
<body>
<div class="shell">
    <div class="card">
        <div class="badge">
            <span class="badge-dot"></span>
            <span>404 · Page not found</span>
        </div>
        <h1>We couldn't find that page</h1>
        <p class="subtitle">
            The link you followed may be broken or the page may have been removed.
        </p>
        <form method="post" action="return-home">
            <button class="btn-primary" type="submit">
                Return to home
                <span>→</span>
            </button>
        </form>
    </div>
</div>
</body>
</html>