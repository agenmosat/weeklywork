<?php
require_once __DIR__ . '/includes/session.php';

if (file_exists(__DIR__ . '/token.php')) {
    require_once __DIR__ . '/token.php';
} else {
    require_once __DIR__ . '/token.example.php';
}

require_once __DIR__ . '/config/database.php';

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

if (isset($_POST['enter'])) {
    csrf_verify();
    $token = trim($_POST['token'] ?? '');
    if (defined('ADMIN_TOKEN') && ADMIN_TOKEN !== "" && $token === ADMIN_TOKEN) {
        $_SESSION['user'] = "superroot";
        $_SESSION['role'] = "admin";
        unset($_SESSION[$attempts_key]);
        header("Location: admin/admin.php");
        exit();
    } elseif (defined('USER_TOKEN') && USER_TOKEN !== "" && $token === USER_TOKEN) {
        $_SESSION['user'] = "user";
        $_SESSION['role'] = "user";
        unset($_SESSION[$attempts_key]);
        header("Location: home/dashboard.php");
        exit();
    } elseif (isset($koneksi) && $koneksi) {
        $stmt = mysqli_prepare($koneksi, "SELECT id_kasir, nama_kasir FROM kasir WHERE nama_kasir = ? AND status = 'aktif' LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $token);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                $_SESSION['user'] = $row['nama_kasir'];
                $_SESSION['role'] = "cashier";
                $_SESSION['id_kasir'] = (int)$row['id_kasir'];
                header("Location: home/dashboard.php");
                exit();
            }
        }
        $message = "invalid token";
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
            <div class="error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="" method="post" class="form">
            <?= csrf_field(); ?>
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

<script src="main.js"></script>

</body>
</html>
