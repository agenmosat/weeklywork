<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/csrf.php';

$timeout = 1800;

if (isset($_SESSION['user'])) {
    if (isset($_SESSION['lastactivity']) && (time() - $_SESSION['lastactivity'] > $timeout)) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        $base_url = file_exists('index.php') ? 'index.php?timeout=1' : (file_exists('../index.php') ? '../index.php?timeout=1' : '../../index.php?timeout=1');
        header("Location: " . $base_url);
        exit();
    }
    $_SESSION['lastactivity'] = time();
}
