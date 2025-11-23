<?php
// Auth route: protected portal
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config.php';

rp_start_session();

$user = null;
if (function_exists('rp_current_user')) {
    $user = rp_current_user();
}

if (!$user) {
    header('Location: /login');
    exit;
}

// Reuse existing portal.php view for now
require __DIR__ . '/../../portal.php';