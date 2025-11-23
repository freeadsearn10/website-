<?php
// Decide where to send user when clicking "Access Your Dashboard".
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

rp_start_session();

$user = null;
if (function_exists('rp_current_user')) {
    $user = rp_current_user();
}

if ($user) {
    header('Location: portal');
} else {
    header('Location: login');
}
exit;