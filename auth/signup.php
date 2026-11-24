<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

if (isset($_SESSION['user'])) {
    header("Location: ../home/dashboard.php");
    exit();
}

$tablecheck = mysqli_query($koneksi, "SHOW TABLES LIKE 'users'");
if ($tablecheck && mysqli_num_rows($tablecheck) === 0) {
    mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(50) NOT NULL,
        role ENUM('admin','kasir') DEFAULT 'kasir',
        id_kasir INT NULL,
        status ENUM('aktif','nonaktif') DEFAULT 'aktif',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

$message = "";
$username = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $captcha = strtoupper(trim($_POST['captcha'] ?? ''));
    $sessioncaptcha = strtoupper((string)($_SESSION['captchacode'] ?? ''));

    if ($captcha === '' || $captcha !== $sessioncaptcha) {
        $message = "captcha salah";
    } elseif ($username === '' || $password === '') {
        $message = "username dan password wajib diisi";
    } elseif (strlen($username) < 3) {
        $message = "username minimal 3 karakter";
    } elseif (strlen($username) > 30) {
        $message = "username maksimal 30 karakter";
    } elseif (strlen($password) < 4) {
        $message = "password minimal 4 karakter";
    } else {
        $stmt = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if (mysqli_fetch_assoc($res)) {
                $message = "username sudah digunakan";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $ins = mysqli_prepare($koneksi, "INSERT INTO users (username, password, name, role, status) VALUES (?, ?, ?, 'kasir', 'aktif')");
                if ($ins) {
                    mysqli_stmt_bind_param($ins, "sss", $username, $hashed, $username);
                    if (mysqli_stmt_execute($ins)) {
                        $newid = (int)mysqli_insert_id($koneksi);
                        unset($_SESSION['captchacode']);
                        $_SESSION['user'] = $username;
                        $_SESSION['username'] = $username;
                        $_SESSION['role'] = 'kasir';
                        $_SESSION['id_pengguna'] = $newid;
                        header("Location: ../home/dashboard.php");
                        exit();
                    }
                }
                $message = "gagal mendaftar akun";
            }
        } else {
            $message = "terjadi kesalahan database";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>daftar</title>
    <link rel="stylesheet" href="../assets/style.css?v=5">
</head>
<body class="page themelight">

    <div class="card">
        <div class="title">daftar</div>
        <div class="subtitle">buat akun baru anda</div>

        <?php if ($message != ""): ?>
            <div class="error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="signup.php" method="post" class="form">
            <?= csrf_field(); ?>
            <div class="field">
                <input type="text" name="username" id="username" class="tokeninput" placeholder="nama pengguna" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="field">
                <input type="password" name="password" id="token" class="tokeninput" placeholder="kata sandi" required>
                <button type="button" id="togglebtn" class="togglebtn" onclick="toggleshow()">?</button>
            </div>
            <div class="field">
                <div class="captchawrap">
                    <img src="../api/captcha.php" alt="captcha" class="captchaimg" onclick="this.src='../api/captcha.php?r='+Math.random()" title="klik untuk ganti captcha">
                    <input type="text" name="captcha" class="tokeninput captchainput" placeholder="kode captcha" required autocomplete="off">
                </div>
            </div>
            <div class="btncenter" style="display:flex;justify-content:center;margin-top:20px">
                <button type="submit" class="enterbtn">daftar</button>
            </div>
            <a href="../home/dashboard.php" class="sidelink" style="margin-top:14px;text-align:center;display:block">&laquo; lanjutkan sebagai tamu</a>
            <div class="cardrow" style="display:flex;justify-content:space-between;align-items:center;margin-top:16px">
                <div class="themeswitcher">
                    <span class="themelabel">tema</span>
                    <select id="themepick" class="themeselect" onchange="switchtheme(this.value)">
                        <option value="themelight">terang</option>
                        <option value="themedark">gelap</option>
                    </select>
                </div>
                <div class="cardfoot" style="text-align:right;margin-left:auto">
                    sudah punya akun? <a href="../index.php?login=1" class="cardlink">masuk disini</a>
                </div>
            </div>
        </form>
    </div>

<script src="../assets/main.js"></script>

</body>
</html>
