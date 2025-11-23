<?php
// Public route: Support link
require_once __DIR__ . '/../../config.php';

$url = rp_get_setting('support_url', '');
if (!$url) {
    header('Location: /');
} else {
    header('Location: ' . $url);
}
exit;