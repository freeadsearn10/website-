<?php
require_once __DIR__ . '/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = trim($_POST['db_host'] ?? 'localhost');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = trim($_POST['db_pass'] ?? '');
    $dbPrefix = trim($_POST['db_prefix'] ?? 'rp_');

    $adminName = trim($_POST['admin_name'] ?? '');
    $adminEmail = trim($_POST['admin_email'] ?? '');
    $adminPass = trim($_POST['admin_pass'] ?? '');

    if ($dbName === '' || $dbUser === '' || $adminName === '' || $adminEmail === '' || $adminPass === '') {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $dsn = 'mysql:host=' . $dbHost . ';dbname=' . $dbName . ';charset=utf8mb4';
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // Create tables
            $settingsSql = "CREATE TABLE IF NOT EXISTS {$dbPrefix}settings (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `key` VARCHAR(64) NOT NULL UNIQUE,
                `value` VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

            $usersSql = "CREATE TABLE IF NOT EXISTS {$dbPrefix}users (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(190) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                role ENUM('admin','user') NOT NULL DEFAULT 'user',
                phone VARCHAR(50) DEFAULT NULL,
                country VARCHAR(80) DEFAULT NULL,
                team_id VARCHAR(80) DEFAULT NULL,
                status ENUM('active','banned') NOT NULL DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

            $pdo->exec($settingsSql);
            $pdo->exec($usersSql);

            // Insert default settings
            $pdo->prepare("INSERT INTO {$dbPrefix}settings(`key`,`value`) VALUES('signup_enabled','1')
                ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)")->execute();

            // Insert admin user
            $passwordHash = password_hash($adminPass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO {$dbPrefix}users(name,email,password_hash,role,status) VALUES(:name,:email,:hash,'admin','active')
                ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), role = 'admin', status = 'active'");
            $stmt->execute([
                ':name' => $adminName,
                ':email' => $adminEmail,
                ':hash' => $passwordHash,
            ]);

            // Write config.php
            $configContent = <<<PHP
<?php
define('RP_DB_HOST', '{$dbHost}');
define('RP_DB_NAME', '{$dbName}');
define('RP_DB_USER', '{$dbUser}');
define('RP_DB_PASS', '{$dbPass}');
define('RP_DB_PREFIX', '{$dbPrefix}');

function rp_get_pdo(): PDO
{
    static \$pdo = null;
    if (\$pdo instanceof PDO) {
        return \$pdo;
    }

    \$dsn = 'mysql:host=' . RP_DB_HOST . ';dbname=' . RP_DB_NAME . ';charset=utf8mb4';
    \$pdo = new PDO(\$dsn, RP_DB_USER, RP_DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return \$pdo;
}

function rp_get_setting(string \$key, \$default = null)
{
    \$pdo = rp_get_pdo();
    \$stmt = \$pdo->prepare('SELECT value FROM ' . RP_DB_PREFIX . "settings WHERE `key` = :key LIMIT 1");
    \$stmt->execute([':key' => \$key]);
    \$row = \$stmt->fetch();
    if (!\$row) {
        return \$default;
    }
    return \$row['value'];
}

function rp_is_signup_enabled(): bool
{
    \$v = rp_get_setting('signup_enabled', '1');
    return \$v === '1';
}

function rp_start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function rp_current_user(): ?array
{
    rp_start_session();
    if (!isset(\$_SESSION['user_id'])) {
        return null;
    }

    \$pdo = rp_get_pdo();
    \$stmt = \$pdo->prepare('SELECT id, name, email, role, status FROM ' . RP_DB_PREFIX . 'users WHERE id = :id LIMIT 1');
    \$stmt->execute([':id' => \$_SESSION['user_id']]);
    \$user = \$stmt->fetch();
    if (!\$user) {
        unset(\$_SESSION['user_id']);
        return null;
    }
    return \$user;
}

function rp_require_admin(): void
{
    \$user = rp_current_user();
    if (!\$user || \$user['role'] !== 'admin') {
        header('Location: login.php');
        exit;
    }
}
PHP;

            file_put_contents(__DIR__ . '/config.php', $configContent);

            $success = 'Installation completed. You can now log in with your admin account.';
        } catch (Throwable $e) {
            $error = 'Install failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refine Panel - Installer</title>
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
        .install-shell {
            width: 100%;
            max-width: 720px;
            padding: 40px 20px;
        }
        .install-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px 22px 22px;
            box-shadow:
                0 24px 60px rgba(15, 23, 42, 0.35),
                0 0 0 1px rgba(255, 255, 255, 0.9);
        }
        h1 {
            font-size: 22px;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 18px;
        }
        .section-title {
            font-size: 13px;
            font-weight: 600;
            margin-top: 12px;
            margin-bottom: 6px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        label {
            font-size: 12px;
            color: #4b5563;
        }
        input {
            padding: 8px 9px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            font-size: 13px;
        }
        input:focus {
            outline: none;
            border-color: #2b2eec;
            box-shadow: 0 0 0 1px rgba(37, 84, 255, 0.18);
        }
        .btn {
            margin-top: 18px;
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, #1b22d8, #2b2eec);
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 14px 35px rgba(37, 99, 235, 0.35);
        }
        .alert {
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 12px;
        }
        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
        }
        .alert-success {
            background: #dcfce7;
            color: #166534;
        }
        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>
</head>
<body>
<div class="install-shell">
    <div class="install-card">
        <h1>Refine Panel installer</h1>
        <p class="subtitle">Fill in your MySQL details and default admin account to install.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="section-title">Database</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="db_host">Host</label>
                    <input id="db_host" name="db_host" value="<?php echo htmlspecialchars($_POST['db_host'] ?? 'localhost', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="db_name">Database name</label>
                    <input id="db_name" name="db_name" required value="<?php echo htmlspecialchars($_POST['db_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="db_user">User</label>
                    <input id="db_user" name="db_user" required value="<?php echo htmlspecialchars($_POST['db_user'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="db_pass">Password</label>
                    <input id="db_pass" name="db_pass" type="password" value="<?php echo htmlspecialchars($_POST['db_pass'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="db_prefix">Table prefix</label>
                    <input id="db_prefix" name="db_prefix" value="<?php echo htmlspecialchars($_POST['db_prefix'] ?? 'rp_', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="section-title">Admin account</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="admin_name">Admin name</label>
                    <input id="admin_name" name="admin_name" required value="<?php echo htmlspecialchars($_POST['admin_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="admin_email">Admin email</label>
                    <input id="admin_email" name="admin_email" type="email" required value="<?php echo htmlspecialchars($_POST['admin_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="admin_pass">Admin password</label>
                    <input id="admin_pass" name="admin_pass" type="password" required>
                </div>
            </div>

            <button class="btn" type="submit">Install Refine Panel</button>
        </form>
    </div>
</div>
</body>
</html>