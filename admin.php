<?php
require_once __DIR__ . '/config.php';

rp_ensure_installed();
rp_require_admin();

// For now we reuse the existing static admin.html UI.
// Later this can be wired fully to the database.
readfile(__DIR__ . '/admin.html');