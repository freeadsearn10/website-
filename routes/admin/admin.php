<?php
// Admin dashboard route: real DB-backed stats + responsive sidebar dashboard.
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../core/config.php';

rp_start_session();
rp_require_admin();

$pdo = rp_get_pdo();

// Basic stats
$stats = [
    'total_users'   => 0,
    'active_users'  => 0,
    'banned_users'  => 0,
    'admin_users'   => 0,
];

try {
    $stats['total_users'] = (int)$pdo->query('SELECT COUNT(*) FROM ' . RP_DB_PREFIX . 'users')->fetchColumn();
    $stats['active_users'] = (int)$pdo->query("SELECT COUNT(*) FROM " . RP_DB_PREFIX . "users WHERE status = 'active'")->fetchColumn();
    $stats['banned_users'] = (int)$pdo->query("SELECT COUNT(*) FROM " . RP_DB_PREFIX . "users WHERE status = 'banned'")->fetchColumn();
    $stats['admin_users'] = (int)$pdo->query("SELECT COUNT(*) FROM " . RP_DB_PREFIX . "users WHERE role = 'admin'")->fetchColumn();
} catch (Throwable $e) {
    // Leave defaults if stats query fails
}

// Latest users
$latestUsers = [];
try {
    $stmt = $pdo->query('SELECT id, name, email, role, status, team_id, created_at 
        FROM ' . RP_DB_PREFIX . 'users 
        ORDER BY id DESC 
        LIMIT 8');
    $latestUsers = $stmt->fetchAll() ?: [];
} catch (Throwable $e) {
    $latestUsers = [];
}

// Current admin
$currentAdmin = rp_current_user();

require __DIR__ . '/../../views/admin/dashboard.php';