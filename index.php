<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/config/database.php';

$attempts_key = 'login_attempts';
$now = time();
$message = "";
$error = "";

if (isset($_SESSION['user'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] = "admin") {
        header("Location: admin/admin.php");
    } else {
        header("Location: home/dashboard.php");
    }
    exit();
}

if (!isset($_GET['login']) && !isset($_GET['status']) && !isset($_GET['timeout']) && !isset($_GET['redirect']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: home/dashboard.php");
    exit();
}

if (isset($_GET['timeout'])) {
    $message = "sesi berakhir, silakan masuk kembali";
} elseif (isset($_GET['status']) && $_GET['status'] === 'logout') {
    $message = "berhasil keluar";
} elseif (isset($_GET['redirect']) && $_GET['redirect'] === 'checkout') {
    $message = "silakan masuk untuk melanjutkan pembelian";
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
        $error = "Terlalu banyak percobaan login. Silakan tunggu " . $remaining . " menit.";
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
            if (defined('ADMIN_TOKEN') && ADMIN_TOKEN !== "" && ($username === ADMIN_TOKEN || $password === ADMIN_TOKEN)) {
                $login_ok = true;
                unset($_SESSION[$attempts_key]);
                $_SESSION['user'] = "superroot";
                $_SESSION['role'] = "admin";
                header("Location: admin/admin.php");
                exit();
            } elseif (defined('USER_TOKEN') && USER_TOKEN !== "" && ($username === USER_TOKEN || $password === USER_TOKEN)) {
                $login_ok = true;
                unset($_SESSION[$attempts_key]);
                $_SESSION['user'] = "user";
                $_SESSION['role'] = "user";
                $redirect = $_SESSION['redirect_url'] ?? '';
                unset($_SESSION['redirect_url']);
                if ($redirect !== '' && !str_starts_with($redirect, 'http')) {
                    header("Location: " . $redirect);
                } else {
                    header("Location: home/dashboard.php");
                }
                exit();
            } elseif (isset($koneksi) && $koneksi && $username !== '') {
                $stmt = mysqli_prepare($koneksi, "SELECT id_kasir, nama_kasir FROM kasir WHERE nama_kasir = ? AND status = 'aktif' LIMIT 1");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "s", $username);
                    mysqli_stmt_execute($stmt);
                    $res = mysqli_stmt_get_result($stmt);
                    if ($row = mysqli_fetch_assoc($res)) {
                        $login_ok = true;
                        unset($_SESSION[$attempts_key]);
                        $_SESSION['user'] = $row['nama_kasir'];
                        $_SESSION['role'] = "cashier";
                        $_SESSION['id_kasir'] = (int)$row['id_kasir'];
                        header("Location: home/dashboard.php");
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
            $error = "nama pengguna atau kata sandi salah";
        }
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: home/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>masuk</title>
    <link rel="stylesheet" href="assets/style.css?v=5">
</head>
<body class="page themelight">

    <div class="card">
        <div class="title">masuk</div>
        <div class="subtitle">masuk ke akun anda</div>

        <?php if ($error != ""): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($message != ""): ?>
            <div class="infomessage"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="" method="post" class="form">
            <?= csrf_field(); ?>
            <div class="field">
                <input type="text" name="username" id="username" class="tokeninput" placeholder="nama pengguna" required>
            </div>
            <div class="field">
                <input type="password" name="password" id="token" class="tokeninput" placeholder="kata sandi" required>
                <button type="button" id="togglebtn" class="togglebtn" onclick="toggleshow()">?</button>
            </div>
            <div class="btncenter" style="display:flex;justify-content:center;margin-top:20px">
                <button type="submit" name="enter" class="enterbtn">masuk</button>
            </div>
            <a href="home/dashboard.php" class="sidelink" style="margin-top:14px;text-align:center;display:block">&laquo; lanjutkan sebagai tamu</a>
            <div class="cardrow" style="display:flex;justify-content:space-between;align-items:center;margin-top:16px">
                <div class="themeswitcher">
                    <span class="themelabel">tema</span>
                    <select id="themepick" class="themeselect" onchange="switchtheme(this.value)">
                        <option value="themelight">terang</option>
                        <option value="themedark">gelap</option>
                    </select>
                </div>
                <div class="cardfoot" style="text-align:right;margin-left:auto">
                    belum ada akun? <a href="auth/signup.php" class="cardlink">daftar disini</a>
                </div>
            </div>
        </form>
    </div>

<script src="assets/main.js"></script>

</body>
</html>
