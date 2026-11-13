<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
        $redirect = 'index.php?timeout=1';
        if (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], '/crud/') !== false) {
            $redirect = '../../index.php?timeout=1';
        }
        header("Location: " . $redirect);
        exit();
    }
    $_SESSION['lastactivity'] = time();
}
