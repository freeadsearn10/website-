<?php
// Show all errors (for debugging 500 issues)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

rp_start_session();

// Basic admin check without relying on helper functions that may not exist
$user = null;
if (function_exists('rp_current_user')) {
    $user = rp_current_user();
}

// If not logged in or not admin, send to login
if (!$user || !isset($user['role']) || $user['role'] !== 'admin') {
    header('Location: login');
    exit;
}

// Serve the static admin HTML UI
readfile(__DIR__ . '/admin.html');