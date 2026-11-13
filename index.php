<?php
session_start();

session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']),
    'samesite' => 'Lax'
]);

require_once __DIR__ . '/token.php';

$message = "";

if (isset($_SESSION['user'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === "admin") {
        header("Location: admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

// Rate limit login attempts
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$attempts_key = "login_attempts_$ip";
if (!isset($_SESSION[$attempts_key])) {
    $_SESSION[$attempts_key] = ['count' => 0, 'last' => 0];
}
$now = time();
if ($now - $_SESSION[$attempts_key]['last'] > 900) {
    $_SESSION[$attempts_key]['count'] = 0;
}
if ($_SESSION[$attempts_key]['count'] >= 5) {
    $wait = 900 - ($now - $_SESSION[$attempts_key]['last']);
    $mins = ceil($wait / 60);
    $message = "too many attempts, try again in $mins minutes";
} elseif (isset($_POST['enter'])) {
    $token = $_POST['token'];
    if (ADMIN_TOKEN !== "" && hash_equals(ADMIN_TOKEN, $token)) {
        $_SESSION['user'] = "loggedin";
        $_SESSION['role'] = "admin";
        unset($_SESSION[$attempts_key]);
        header("Location: admin.php");
        exit();
    } elseif (USER_TOKEN !== "" && hash_equals(USER_TOKEN, $token)) {
        $_SESSION['user'] = "loggedin";
        $_SESSION['role'] = "user";
        unset($_SESSION[$attempts_key]);
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION[$attempts_key]['count']++;
        $_SESSION[$attempts_key]['last'] = $now;
        $message = "invalid token";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>secret</title>
    <link rel="stylesheet" href="style.css?v=3">
</head>
<body class="page themelight">

    <div class="card">
        <div class="title">access restricted</div>
        <div class="subtitle">input token to decrypt session</div>

        <?php if ($message != ""): ?>
            <div class="error"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="" method="post" class="form">
            <div class="field">
                <input type="password" name="token" id="token" class="tokeninput" placeholder="enter token..." required>
                <button type="button" id="togglebtn" class="togglebtn" onclick="toggleshow()">?</button>
            </div>
            <div class="btnwrap">
                <button type="submit" name="enter" class="enterbtn">enter</button>
                <div class="themeswitcher">
                    <span class="themelabel">theme</span>
                    <select id="themepick" class="themeselect" onchange="switchtheme(this.value)">
                        <option value="themelight">light</option>
                        <option value="themedark">dark</option>
                    </select>
                </div>
            </div>
    </div>

<script>
function toggleshow() {
    var field = document.getElementById("token");
    if (field.type === "password") {
        field.type = "text";
    } else {
        field.type = "password";
    }
}
function switchtheme(theme) {
    document.body.className = "page " + theme;
    localStorage.setItem("theme", theme);
}
var savedtheme = localStorage.getItem("theme");
if (savedtheme) {
    document.body.className = "page " + savedtheme;
    var sel = document.getElementById("themepick");
    if (sel) {
        sel.value = savedtheme;
    }
}
</script>

</body>
</html>
