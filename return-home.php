<?php
require_once __DIR__ . '/config.php';

rp_ensure_installed();
$user = rp_current_user();

if ($user) {
    header('Location: portal');
} else {
    header('Location: login');
}
exit;