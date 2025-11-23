<?php
require_once __DIR__ . '/config.php';

rp_ensure_installed();
rp_require_login();
?>
<?php readfile(__DIR__ . '/portal.html'); ?>