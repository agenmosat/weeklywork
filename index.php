<?php
require_once __DIR__ . '/session.php';

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
        header("Location: admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

if (isset($_POST['enter'])) {
    $token = trim($_POST['token'] ?? '');
    if (defined('ADMIN_TOKEN') && ADMIN_TOKEN !== "" && $token === ADMIN_TOKEN) {
        $_SESSION['user'] = "superroot";
        $_SESSION['role'] = "admin";
        header("Location: admin.php");
        exit();
    } elseif (defined('USER_TOKEN') && USER_TOKEN !== "" && $token === USER_TOKEN) {
        $_SESSION['user'] = "user";
        $_SESSION['role'] = "user";
        header("Location: dashboard.php");
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
                header("Location: dashboard.php");
                exit();
            }
        }
        $message = "invalid token";
    } else {
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
<body class="page">

    <div class="card">
        <div class="title">access restricted</div>
        <div class="subtitle">input token to decrypt session</div>

        <?php if ($message != ""): ?>
            <div class="error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="" method="post" class="form">
            <div class="field">
                <input type="password" name="token" id="token" class="tokeninput" placeholder="enter token..." required>
                <button type="button" id="togglebtn" class="togglebtn" onclick="toggleshow()">?</button>
            </div>
            <div class="btnwrap">
                <button type="submit" name="enter" class="enterbtn">enter</button>
            </div>
        </form>
    </div>

<script src="main.js"></script>

</body>
</html>
