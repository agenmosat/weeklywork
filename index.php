<?php
session_start();

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

if (isset($_POST['enter'])) {
    $token = $_POST['token'];
    if (ADMIN_TOKEN !== "" && $token === ADMIN_TOKEN) {
        $_SESSION['user'] = "loggedin";
        $_SESSION['role'] = "admin";
        header("Location: admin.php");
        exit();
    } elseif (USER_TOKEN !== "" && $token === USER_TOKEN) {
        $_SESSION['user'] = "loggedin";
        $_SESSION['role'] = "user";
        header("Location: dashboard.php");
        exit();
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
            <div class="error"><?php echo $message; ?></div>
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

<script>
function toggleshow() {
    var field = document.getElementById("token");
    if (field.type === "password") {
        field.type = "text";
    } else {
        field.type = "password";
    }
}
</script>

</body>
</html>
