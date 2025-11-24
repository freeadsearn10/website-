<?php
// Route used by "Access Your Dashboard" button on landing page.
require_once __DIR__ . '/../../config.php';

rp_start_session();
$user = null;
if (function_exists('rp_current_user')) {
    $user = rp_current_user();
}

if ($user) {
    header('Location: /portal');
} else {
    header('Location: /login');
}
exit;