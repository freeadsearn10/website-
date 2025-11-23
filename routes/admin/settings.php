<?php
// Admin route: public link settings (see how it works, support)
require_once __DIR__ . '/../../config.php';

rp_start_session();

$user = null;
if (function_exists('rp_current_user')) {
    $user = rp_current_user();
}

if (!$user || !isset($user['role']) || $user['role'] !== 'admin') {
    header('Location: /login');
    exit;
}

require __DIR__ . '/../../admin-settings.php';