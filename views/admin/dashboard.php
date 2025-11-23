<?php
// Variables available from route: $stats (array), $latestUsers (array), $currentAdmin (array).
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Admin Dashboard</title>
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

        .cards-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        .stat-card {
            background: var(--card-inner-bg);
            border-radius: 14px;
            padding: 12px 12px 11px;
            border: 1px solid var(--card-border);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.45);
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 12px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 11px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 600;
        }

        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 7px;
            border-radius: 999px;
            font-size: 10px;
        }

        .stat-pill.green {
            background: var(--accent-soft);
            color: var(--accent);
        }

        .stat-pill.red {
            background: var(--danger-soft);
            color: var(--danger);
        }

        .grid-main {
            display: grid;
            grid-template-columns: minmax(0, 2.1fr) minmax(0, 1.2fr);
            gap: 16px;
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

        .users-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .users-table th,
        .users-table td {
            padding: 7px 6px;
            border-bottom: 1px solid var(--card-border);
        }

        .users-table th {
            text-align: left;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 11px;
        }

        .users-table tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 10px;
        }

        .status-badge.active {
            background: rgba(34, 197, 94, 0.18);
            color: var(--accent);
        }

        .status-badge.banned {
            background: rgba(248, 113, 113, 0.2);
            color: var(--danger);
        }

        .role-pill {
            display: inline-flex;
            align-items: center;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 10px;
            background: rgba(99, 102, 241, 0.16);
            color: #a5b4fc;
        }

        .team-id {
            font-size: 11px;
            color: var(--text-muted);
        }

        .side-card-stat {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 8px;
            font-size: 12px;
        }

        .side-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: var(--text-muted);
        }

        .side-row strong {
            color: var(--text-main);
        }

        .side-cta {
            margin-top: 10px;
            font-size: 11px;
        }

        .side-cta a {
            color: var(--accent);
        }

        .side-cta a:hover {
            text-decoration: underline;
        }

        /* Mobile / tablet */
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
            .cards-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .grid-main {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 520px) {
            .cards-row {
                grid-template-columns: minmax(0, 1fr);
            }

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
                <a class="nav-item active" href="/admin">
                    <span class="icon"></span>
                    <span>Dashboard</span>
                </a>
                <a class="nav-item" href="#">
                    <span class="icon"></span>
                    <span>Users (soon)</span>
                </a>

                <div class="sidebar-section-title">Configuration</div>
                <a class="nav-item" href="/admin-settings">
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
                    <div class="topbar-title">Dashboard</div>
                    <div class="topbar-subtitle">Overview of users and platform status</div>
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
                        <div style="font-size:10px;color:var(--text-muted);">Full access</div>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-area">
            <section class="cards-row">
                <div class="stat-card">
                    <div class="stat-label">Total users</div>
                    <div class="stat-value"><?php echo (int)$stats['total_users']; ?></div>
                    <div class="stat-pill green">
                        <span>All accounts in system</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Active users</div>
                    <div class="stat-value"><?php echo (int)$stats['active_users']; ?></div>
                    <div class="stat-pill green">
                        <span>Can log in</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Banned / inactive</div>
                    <div class="stat-value"><?php echo (int)$stats['banned_users']; ?></div>
                    <div class="stat-pill red">
                        <span>Review regularly</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Admin accounts</div>
                    <div class="stat-value"><?php echo (int)$stats['admin_users']; ?></div>
                    <div class="stat-pill green">
                        <span>Use /admin-login</span>
                    </div>
                </div>
            </section>

            <section class="grid-main">
                <div class="card-panel">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Latest users</div>
                            <div class="card-subtitle">Recently created accounts</div>
                        </div>
                        <div class="chip">Database powered</div>
                    </div>

                    <table class="users-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name &amp; email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Team</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!$latestUsers): ?>
                            <tr>
                                <td colspan="5" style="padding:10px 6px;font-size:12px;color:var(--text-muted);">
                                    No users found yet. Create users via signup or installer admin.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($latestUsers as $u): ?>
                                <tr>
                                    <td><?php echo (int)$u['id']; ?></td>
                                    <td>
                                        <div style="font-size:12px;">
                                            <?php echo htmlspecialchars($u['name'] ?: '—', ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                        <div style="font-size:11px;color:var(--text-muted);">
                                            <?php echo htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="role-pill">
                                            <?php echo htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $u['status'] ?? 'active';
                                        $cls = $status === 'active' ? 'active' : 'banned';
                                        ?>
                                        <span class="status-badge <?php echo $cls; ?>">
                                            <?php echo htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="team-id">
                                            <?php echo htmlspecialchars($u['team_id'] ?? '—', ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-panel">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Quick system overview</div>
                            <div class="card-subtitle">High-level health of Refine Panel</div>
                        </div>
                    </div>

                    <div class="side-card-stat">
                        <div class="side-row">
                            <span>Signup status</span>
                            <strong>
                                <?php echo rp_is_signup_enabled() ? 'Enabled' : 'Disabled'; ?>
                            </strong>
                        </div>
                        <div class="side-row">
                            <span>Active admins</span>
                            <strong><?php echo (int)$stats['admin_users']; ?></strong>
                        </div>
                        <div class="side-row">
                            <span>Active users</span>
                            <strong><?php echo (int)$stats['active_users']; ?></strong>
                        </div>
                        <div class="side-row">
                            <span>Banned / inactive</span>
                            <strong style="color:var(--danger);"><?php echo (int)$stats['banned_users']; ?></strong>
                        </div>
                    </div>

                    <div class="side-cta">
                        Adjust settings:
                        <a href="/admin-settings">Public links</a>,
                        <a href="/admin-theme">Theme</a>,
                        <a href="/admin-captcha">Captcha</a>.
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

        // Close sidebar when clicking outside on mobile
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