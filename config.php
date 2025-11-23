<?php
// Basic configuration file for Refine Panel.
// This file will be overwritten by install.php when you run the installer.

define('RP_DB_HOST', 'localhost');
define('RP_DB_NAME', 'refine_panel');
define('RP_DB_USER', 'root');
define('RP_DB_PASS', '');
define('RP_DB_PREFIX', 'rp_');

function rp_get_pdo(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . RP_DB_HOST . ';dbname=' . RP_DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, RP_DB_USER, RP_DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

function rp_get_setting(string $key, $default = null)
{
    $pdo = rp_get_pdo();
    $stmt = $pdo->prepare('SELECT value FROM ' . RP_DB_PREFIX . "settings WHERE `key` = :key LIMIT 1");
    $stmt->execute([':key' => $key]);
    $row = $stmt->fetch();
    if (!$row) {
        return $default;
    }
    return $row['value'];
}

function rp_is_signup_enabled(): bool
{
    $v = rp_get_setting('signup_enabled', '1');
    return $v === '1';
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
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $pdo = rp_get_pdo();
    $stmt = $pdo->prepare('SELECT id, name, email, role, status FROM ' . RP_DB_PREFIX . 'users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    if (!$user) {
        unset($_SESSION['user_id']);
        return null;
    }
    return $user;
}

function rp_require_admin(): void
{
    $user = rp_current_user();
    if (!$user || $user['role'] !== 'admin') {
        header('Location: login.php');
        exit;
    }
}