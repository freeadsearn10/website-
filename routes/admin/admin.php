<?php
// Admin dashboard route: real DB-backed stats + responsive sidebar dashboard.
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../core/config.php';

rp_start_session();
rp_require_admin();

$pdo = rp_get_pdo();

// Stats + latest users derived from same dataset so they always match what admin-users shows.
$stats = [
    'total_users'   => 0,
    'active_users'  => 0,
    'banned_users'  => 0,
    'admin_users'   => 0,
];

$latestUsers = [];

try {
    $stmt = $pdo->query('SELECT id, name, email, role, status, team_id, created_at 
        FROM ' . RP_DB_PREFIX . 'users 
        ORDER BY id DESC');
    $rows = $stmt->fetchAll() ?: [];

    $stats['total_users'] = count($rows);

    foreach ($rows as $row) {
        $status = strtolower($row['status'] ?? '');
        $role   = strtolower($row['role'] ?? '');
        if ($status === 'active') {
            $stats['active_users']++;
        } elseif ($status === 'banned') {
            $stats['banned_users']++;
        }
        if ($role === 'admin') {
            $stats['admin_users']++;
        }
    }

    // Latest up to 8 users
    $latestUsers = array_slice($rows, 0, 8);
} catch (Throwable $e) {
    $latestUsers = [];
}

// Current admin
$currentAdmin = rp_current_user();

require __DIR__ . '/../../views/admin/dashboard.php';