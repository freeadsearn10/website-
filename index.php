<?php
require_once __DIR__ . '/config.php';

// If not installed, this will redirect to install.php and exit
rp_ensure_installed();

// After install, just render the static landing page HTML
readfile(__DIR__ . '/index.html');