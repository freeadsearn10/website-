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
            } elseif ($action === 'update') {
                $name   = isset($_POST['name']) ? trim($_POST['name']) : '';
                $email  = isset($_POST['email']) ? trim($_POST['email']) : '';
                $role   = isset($_POST['role']) ? trim($_POST['role']) : 'user';
                $teamId = isset($_POST['team_id']) ? trim($_POST['team_id']) : '';

                if ($name === '' || $email === '') {
                    $error = 'Name and email are required for update.';
                } else {
                    if (!in_array($role, ['user', 'admin'], true)) {
                        $role = 'user';
                    }
                    $stmt = $pdo->prepare("UPDATE " . RP_DB_PREFIX . "users 
                        SET name = :name, email = :email, role = :role, team_id = :team_id 
                        WHERE id = :id");
                    $stmt->execute([
                        ':name'    => $name,
                        ':email'   => $email,
                        ':role'    => $role,
                        ':team_id' => $teamId,
                        ':id'      => $userId,
                    ]);
                    $message = 'User updated.';
                }
            }
        } catch (Throwable $e) {
            $error = 'Action failed.';
        }
    }
}

// Optional edit selection
$editUser = null;
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
if ($editId > 0) {
    try {
        $stmt = $pdo->prepare('SELECT id, name, email, role, status, team_id FROM ' . RP_DB_PREFIX . 'users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $editId]);
        $editUser = $stmt->fetch() ?: null;
    } catch (Throwable $e) {
        $editUser = null;
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