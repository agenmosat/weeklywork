<?php
session_start();

session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']),
    'samesite' => 'Lax'
]);

require_once __DIR__ . '/includes/csrf.php';

session_destroy();

header("Location: index.php");
exit;