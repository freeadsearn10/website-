<?php
// Variables from route: $users, $currentAdmin, $message, $error
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Admin Users</title>
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

        .user-actions {
            display: inline-flex;
            gap: 6px;
        }

        .user-action-btn {
            border: none;
            background: none;
            color: #e5e7eb;
            font-size: 11px;
            cursor: pointer;
            padding: 0;
        }

        .user-action-btn.ban {
            color: var(--danger);
        }

        .user-action-btn.unban {
            color: var(--accent);
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
                <a class="nav-item active" href="/admin-users">
                    <span class="icon"></span>
                    <span>Users</span>
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
                    <div class="topbar-title">Users</div>
                    <div class="topbar-subtitle">Manage all accounts in the Refine Panel</div>
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
                        <div style="font-size:10px;color:var(--text-muted);">User management</div>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-area">
            <section class="card-panel">
                <div class="card-header">
                    <div>
                        <div class="card-title">All users</div>
                        <div class="card-subtitle">Ban, unban or delete accounts</div>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div style="overflow:auto; max-height:480px;">
                    <table class="users-table">
                        <thead>
                        <tr>
                            <th style="width:50px;">ID</th>
                            <th>Name &amp; email</th>
                            <th style="width:80px;">Role</th>
                            <th style="width:90px;">Status</th>
                            <th style="width:120px;">Team</th>
                            <th style="width:160px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!$users): ?>
                            <tr>
                                <td colspan="6" style="padding:10px 6px;font-size:12px;color:var(--text-muted);">
                                    No users found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
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
                                    <td>
                                        <div class="user-actions">
                                            <?php if (($u['status'] ?? 'active') === 'active'): ?>
                                                <form method="post" action="/admin-users" style="display:inline;">
                                                    <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                                                    <input type="hidden" name="action" value="ban">
                                                    <button type="submit" class="user-action-btn ban">Ban</button>
                                                </form>
                                            <?php else: ?>
                                                <form method="post" action="/admin-users" style="display:inline;">
                                                    <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                                                    <input type="hidden" name="action" value="unban">
                                                    <button type="submit" class="user-action-btn unban">Unban</button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="post" action="/admin-users" style="display:inline;"
                                                  onsubmit="return confirm('Delete this user?');">
                                                <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button type="submit" class="user-action-btn">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
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