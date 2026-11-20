<?php
require_once __DIR__ . '/includes/session.php';

if (file_exists(__DIR__ . '/token.php')) {
    require_once __DIR__ . '/token.php';
} elseif (file_exists(__DIR__ . '/token.example.php')) {
    require_once __DIR__ . '/token.example.php';
}

require_once __DIR__ . '/config/database.php';

$attempts_key = 'login_attempts';
$now = time();
$message = "";

if (isset($_GET['timeout'])) {
    $message = "session expired, please login again";
}

if (isset($_SESSION['user'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === "admin") {
        header("Location: admin/admin.php");
    } else {
        header("Location: home/dashboard.php");
    }
    exit();
}

$max_attempts = 5;
$lockout_seconds = 300;
$attempt_data = $_SESSION[$attempts_key] ?? ['count' => 0, 'last' => 0];
if (($now - $attempt_data['last']) > $lockout_seconds) {
    $_SESSION[$attempts_key] = ['count' => 0, 'last' => $now];
    $attempt_data = $_SESSION[$attempts_key];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['enter']) || isset($_POST['login']) || isset($_POST['username']))) {
    csrf_verify();
    if ($attempt_data['count'] >= $max_attempts) {
        $remaining = ceil(($lockout_seconds - ($now - $attempt_data['last'])) / 60);
        $message = "Terlalu banyak percobaan login. Silakan tunggu " . $remaining . " menit.";
    } else {
        $username = trim($_POST['username'] ?? $_POST['token'] ?? '');
        $password = (string)($_POST['password'] ?? $_POST['token'] ?? '');
        $login_ok = false;
        if (isset($koneksi) && $koneksi && $username !== '') {
    $stmt = mysqli_prepare($koneksi, "SELECT id, username, password, name, role FROM users WHERE username = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) {
            if (password_verify($password, $row['password'])) {
                $login_ok = true;
                if (isset($_SESSION[$attempts_key])) {
                    unset($_SESSION[$attempts_key]);
                }
                $_SESSION['user'] = $row['username'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['id_pengguna'] = (int)$row['id'];
                if ($row['role'] === 'admin') {
                    header("Location: admin/admin.php");
                } else {
                    header("Location: home/dashboard.php");
                }
                exit();
            }
        }
    }
}

        if (!$login_ok) {
            if (!isset($_SESSION[$attempts_key])) {
                $_SESSION[$attempts_key] = array('count' => 0, 'last' => $now);
            }
            $_SESSION[$attempts_key]['count']++;
            $_SESSION[$attempts_key]['last'] = $now;
            $message = "Username atau password salah";
        }
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
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>
<body class="page themelight">

    <div class="card">
        <div class="title">access restricted</div>
        <div class="subtitle">input token to decrypt session</div>

        <?php if ($message != ""): ?>
            <div class="error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="" method="post" class="form">
            <?= csrf_field(); ?>
            <div class="field">
                <input type="text" name="username" id="username" class="tokeninput" placeholder="username" required>
            </div>
            <div class="field">
                <input type="password" name="password" id="token" class="tokeninput" placeholder="password" required>
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
        </form>
    </div>

<script src="assets/main.js"></script>

</body>
</html>
