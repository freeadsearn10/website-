<?php
// Redirect for "Support" nav link, configurable from admin settings.
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

$url = rp_get_setting('support_url', '');
if (!$url) {
    header('Location: ./');
} else {
    header('Location: ' . $url);
}
exit;