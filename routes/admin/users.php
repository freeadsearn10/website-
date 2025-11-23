<?php
// Admin users management page: list users and basic actions (ban/unban/delete).
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../core/config.php';

rp_start_session();
rp_require_admin();

$pdo = rp_get_pdo();
$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

    if ($userId > 0) {
        try {
            if ($action === 'ban') {
                $stmt = $pdo->prepare("UPDATE " . RP_DB_PREFIX . "users SET status = 'banned' WHERE id = :id");
                $stmt->execute([':id' => $userId]);
                $message = 'User banned.';
            } elseif ($action === 'unban') {
                $stmt = $pdo->prepare("UPDATE " . RP_DB_PREFIX . "users SET status = 'active' WHERE id = :id");
                $stmt->execute([':id' => $userId]);
                $message = 'User reactivated.';
            } elseif ($action === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM " . RP_DB_PREFIX . "users WHERE id = :id");
                $stmt->execute([':id' => $userId]);
                $message = 'User deleted.';
            }
        } catch (Throwable $e) {
            $error = 'Action failed.';
        }
    }
}

// Fetch users list
$users = [];
try {
    $stmt = $pdo->query('SELECT id, name, email, role, status, team_id, created_at 
        FROM ' . RP_DB_PREFIX . 'users 
        ORDER BY id DESC');
    $users = $stmt->fetchAll() ?: [];
} catch (Throwable $e) {
    $users = [];
    $error = 'Unable to load users.';
}

$currentAdmin = rp_current_user();

require __DIR__ . '/../../views/admin/users.php';