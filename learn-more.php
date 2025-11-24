<?php
// Redirect for "See how it works" link, configurable from admin settings.
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

$url = rp_get_setting('see_how_it_works_url', '');
if (!$url) {
    // Fallback: go to landing
    header('Location: ./');
} else {
    header('Location: ' . $url);
}
exit;