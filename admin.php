<?php
require_once __DIR__ . '/config.php';

rp_ensure_installed();
rp_require_admin();

// For now we reuse the existing static admin.html UI for user demo tools.
// Theme settings are handled in a dedicated page: admin-theme.php.
readfile(__DIR__ . '/admin.html');