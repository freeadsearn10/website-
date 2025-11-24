<?php
// Auth route: logout
require_once __DIR__ . '/../../config.php';

rp_start_session();
session_unset();
session_destroy();

header('Location: /login');
exit;