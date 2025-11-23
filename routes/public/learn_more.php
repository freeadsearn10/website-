<?php
// Public route: See how it works
require_once __DIR__ . '/../../config.php';

$url = rp_get_setting('see_how_it_works_url', '');
if (!$url) {
    header('Location: /');
} else {
    header('Location: ' . $url);
}
exit;