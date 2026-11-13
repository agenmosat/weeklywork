<?php
session_start();

session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']),
    'samesite' => 'Lax'
]);

require_once __DIR__ . '/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    session_destroy();
}

header("Location: index.php");
exit;