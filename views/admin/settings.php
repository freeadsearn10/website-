<?php
// Variables: $seeUrl, $supportUrl, $signupEnabled (bool), $message, $error, $currentAdmin
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Public link & signup settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-border: #1f2937;
            --sidebar-text: #e5e7eb;
            --sidebar-muted: #6b7280;
            --sidebar-active: #22c55e;

            --topbar-bg: #020617;
            --page-bg: #020617;
            --card-bg: #020617;
            --card-inner-bg: #0f172a;
            --card-border: #1f2937;

            --accent: #22c55e;
            --accent-soft: rgba(34, 197, 94, 0.15);
            --danger: #ef4444;
            --danger-soft: rgba(248, 113, 113, 0.18);

            --text-main: #f9fafb;
            --text-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(circle at top left, #020617, #020617);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .admin-layout {
            display: grid;
            grid-template-columns: 260px minmax(0, 1fr);
            width: 100%;
            min-height: 100vh;
        }

        .sidebar {
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            padding: 18px 14px 14px;
            position: relative;
            z-index: 20;
            transition: transform 0.25s ease-out;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
        }

        .sidebar-logo {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: conic-gradient(from 140deg, #22c55e, #38bdf8, #6366f1, #22c55e);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            color: #020617;
        }

        .sidebar-title-main {
            font-size: 13px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .sidebar-title-sub {
            font-size: 11px;
            color: var(--sidebar-muted);
            letter-spacing: 0.2em;
            text-transform: uppercase;
        }

        .sidebar-nav {
            flex: 1;
            margin-top: 10px;
            font-size: 13px;
        }

        .sidebar-section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: var(--sidebar-muted);
            margin: 14px 8px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 9px;
            border-radius: 8px;
            color: var(--sidebar-text);
            cursor: pointer;
            transition: background 0.18s ease, color 0.18s ease, transform 0.1s ease;
        }

        .nav-item span.icon {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--sidebar-muted);
        }

        .nav-item.active {
            background: rgba(34, 197, 94, 0.2);
            color: var(--sidebar-active);
        }

        .nav-item.active span.icon {
            background: var(--sidebar-active);
        }

        .nav-item:hover {
            background: rgba(15, 23, 42, 0.8);
            transform: translateX(1px);
        }

        .sidebar-footer {
            margin-top: 10px;
            font-size: 11px;
            color: var(--sidebar-muted);
        }

        .sidebar-footer a {
            color: var(--sidebar-text);
        }

        .sidebar-footer a:hover {
            text-decoration: underline;
        }

        .topbar {
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--sidebar-border);
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 500;
        }

        .topbar-subtitle {
            font-size: 11px;
            color: var(--text-muted);
        }

        .hamburger {
            display: none;
            width: 28px;
            height: 28px;
            border-radius: 999px;
            border: 1px solid var(--sidebar-border);
            background: #020617;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .hamburger span,
        .hamburger span::before,
        .hamburger span::after {
            content: "";
            display: block;
            width: 14px;
            height: 2px;
            border-radius: 999px;
            background: #e5e7eb;
            position: relative;
        }

        .hamburger span::before {
            position: absolute;
            top: -4px;
        }

        .hamburger span::after {
            position: absolute;
            top: 4px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .topbar-admin {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-avatar {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            background: radial-gradient(circle at 30% 30%, #22c55e, #0f172a);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #e5e7eb;
        }

        .badge-pill {
            padding: 3px 8px;
            border-radius: 999px;
            border: 1px solid var(--sidebar-border);
            background: rgba(15, 23, 42, 0.9);
            font-size: 11px;
        }

        .content-area {
            background: radial-gradient(circle at top left, #020617, #020617);
            min-height: calc(100vh - 44px);
            padding: 16px 18px 20px;
        }

        .card-panel {
            background: var(--card-inner-bg);
            border-radius: 14px;
            padding: 14px 14px 12px;
            border: 1px solid var(--card-border);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.45);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .card-title {
            font-size: 13px;
            font-weight: 600;
        }

        .card-subtitle {
            font-size: 11px;
            color: var(--text-muted);
        }

        .chip {
            border-radius: 999px;
            border: 1px solid var(--card-border);
            padding: 3px 8px;
            font-size: 11px;
            color: var(--text-muted);
        }

        .settings-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
            gap: 16px;
            margin-top: 8px;
        }

        .alert {
            margin-bottom: 10px;
            font-size: 11px;
            border-radius: 8px;
            padding: 6px 8px;
        }

        .alert-success {
            background: var(--accent-soft);
            color: var(--accent);
            border: 1px solid rgba(34, 197, 94, 0.5);
        }

        .alert-error {
            background: var(--danger-soft);
            color: var(--danger);
            border: 1px solid rgba(248, 113, 113, 0.5);
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .form-input {
            width: 100%;
            padding: 9px 10px;
            border-radius: 8px;
            border: 1px solid var(--card-border);
            background: #020617;
            color: var(--text-main);
            font-size: 13px;
            outline: none;
            transition: border-color 0.16s ease, box-shadow 0.16s ease, background 0.16s ease;
        }

        .form-input:focus {
            border-color: var(--accent);
            background: rgba(15, 23, 42, 0.95);
            box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.4);
        }

        .form-input::placeholder {
            color: #64748b;
        }

        .btn-primary {
            margin-top: 12px;
            width: 100%;
            padding: 9px 12px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: #0f172a;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 18px 45px rgba(34, 197, 94, 0.4);
        }

        .btn-primary:hover {
            box-shadow: 0 24px 60px rgba(34, 197, 94, 0.55);
        }

        .side-block {
            border-radius: 10px;
            border: 1px solid var(--card-border);
            background: rgba(15, 23, 42, 0.8);
            padding: 10px 10px 9px;
            font-size: 11px;
            color: var(--text-muted);
        }

        .side-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .side-row strong {
            color: var(--text-main);
        }

        .toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
        }

        .toggle-label {
            font-size: 12px;
            color: var(--text-muted);
        }

        .switch {
            position: relative;
            width: 36px;
            height: 20px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            inset: 0;
            background-color: #1f2937;
            border-radius: 999px;
            cursor: pointer;
            transition: background-color 0.18s ease;
        }

        .slider::before {
            content: "";
            position: absolute;
            height: 16px;
            width: 16px;
            left: 2px;
            top: 2px;
            border-radius: 999px;
            background-color: #0b1120;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
            box-shadow: 0 2px 5px rgba(15, 23, 42, 0.25);
        }

        .switch input:checked + .slider {
            background-color: #22c55e;
        }

        .switch input:checked + .slider::before {
            transform: translateX(16px);
        }

        @media (max-width: 960px) {
            .admin-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                width: 240px;
                transform: translateX(-100%);
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .hamburger {
                display: inline-flex;
            }
        }

        @media (max-width: 800px) {
            .settings-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 720px) {
            .content-area {
                padding-inline: 12px;
            }
        }
    </style>
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar" id="adminSidebar">
        <div>
            <div class="sidebar-header">
                <div class="sidebar-logo">R</div>
                <div>
                    <div class="sidebar-title-main">Refine Panel</div>
                    <div class="sidebar-title-sub">ADMIN CONSOLE</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="sidebar-section-title">Main</div>
                <a class="nav-item" href="/admin">
                    <span class="icon"></span>
                    <span>Dashboard</span>
                </a>
                <a class="nav-item" href="/admin-users">
                    <span class="icon"></span>
                    <span>Users</span>
                </a>

                <div class="sidebar-section-title">Configuration</div>
                <a class="nav-item active" href="/admin-settings">
                    <span class="icon"></span>
                    <span>Public links</span>
                </a>
                <a class="nav-item" href="/admin-theme">
                    <span class="icon"></span>
                    <span>Theme &amp; colour</span>
                </a>
                <a class="nav-item" href="/admin-captcha">
                    <span class="icon"></span>
                    <span>Captcha rules</span>
                </a>
            </nav>
        </div>

        <div class="sidebar-footer">
            Logged in as <strong><?php echo htmlspecialchars($currentAdmin['email'] ?? 'admin', ENT_QUOTES, 'UTF-8'); ?></strong><br>
            <a href="/logout">Sign out</a> · <a href="/">View site</a>
        </div>
    </aside>

    <div>
        <header class="topbar">
            <div class="topbar-left">
                <button class="hamburger" id="sidebarToggle" aria-label="Toggle sidebar">
                    <span></span>
                </button>
                <div>
                    <div class="topbar-title">Public links & signup</div>
                    <div class="topbar-subtitle">Control landing links and whether signup is open</div>
                </div>
            </div>

            <div class="topbar-right">
                <span class="badge-pill">Admin</span>
                <div class="topbar-admin">
                    <div class="topbar-avatar">
                        <?php
                        $name = $currentAdmin['name'] ?? '';
                        $initial = $name !== '' ? strtoupper(substr($name, 0, 1)) : 'A';
                        echo htmlspecialchars($initial, ENT_QUOTES, 'UTF-8');
                        ?>
                    </div>
                    <div>
                        <div style="font-size:12px;"><?php echo htmlspecialchars($currentAdmin['name'] ?? 'Administrator', ENT_QUOTES, 'UTF-8'); ?></div>
                        <div style="font-size:10px;color:var(--text-muted);">Configuration</div>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-area">
            <section class="card-panel">
                <div class="card-header">
                    <div>
                        <div class="card-title">Public link settings</div>
                        <div class="card-subtitle">Control docs/support URLs and signup availability</div>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="settings-grid">
                    <div>
                        <form method="post" action="/admin-settings">
                            <div class="form-group">
                                <label class="form-label" for="see_url">“See how it works” URL</label>
                                <input class="form-input" type="text" id="see_url" name="see_url"
                                       value="<?php echo htmlspecialchars($seeUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                       placeholder="https://your-docs-or-landing.com/how-it-works">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="support_url">Support URL</label>
                                <input class="form-input" type="text" id="support_url" name="support_url"
                                       value="<?php echo htmlspecialchars($supportUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                       placeholder="https://your-support-portal.com">
                            </div>

                            <div class="toggle-row">
                                <div class="toggle-label">
                                    Allow new partner signup<br>
                                    <span style="font-size:10px;color:var(--text-muted);">
                                        Controls access to /signup and “Sign up” links
                                    </span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="signup_enabled" value="1" <?php echo $signupEnabled ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <button class="btn-primary" type="submit">Save settings</button>
                        </form>
                    </div>

                    <div class="side-block">
                        <div class="side-row">
                            <span>Current signup status</span>
                            <strong><?php echo $signupEnabled ? 'Enabled' : 'Disabled'; ?></strong>
                        </div>
                        <div style="margin-top:8px;">
                            - When enabled:
                            <br>• “Sign up” links are visible
                            <br>• /signup page is accessible
                        </div>
                        <div style="margin-top:6px;">
                            - When disabled:
                            <br>• Sign up links should be hidden on landing/login
                            <br>• /signup should redirect to /login (already handled)
                        </div>
                        <div style="margin-top:8px;">
                            Use this to temporarily pause onboarding while keeping existing partners active.
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<script>
    (function () {
        var sidebar = document.getElementById('adminSidebar');
        var toggle = document.getElementById('sidebarToggle');

        if (!sidebar || !toggle) return;

        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', function (e) {
            if (window.innerWidth > 960) return;
            if (!sidebar.classList.contains('open')) return;
            var insideSidebar = e.target.closest('#adminSidebar');
            var insideToggle = e.target.closest('#sidebarToggle');
            if (!insideSidebar && !insideToggle) {
                sidebar.classList.remove('open');
            }
        });
    })();
</script>
</body>
</html>