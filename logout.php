<?php
// Simple logout script: clear session and go to login page.
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

rp_start_session();
session_unset();
session_destroy();

header('Location: login');
exit;