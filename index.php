<?php
session_start();

$validtoken = "admin#1234";
$message = "";

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['enter'])) {
    $token = $_POST['token'];
    if ($token === $validtoken) {
        $_SESSION['user'] = "loggedin";
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
                <select id="themepick" class="themeselect" onchange="switchtheme(this.value)">
                    <option value="themelight">light</option>
                    <option value="themedark">dark</option>
                </select>
                <button type="submit" name="enter" class="enterbtn">enter</button>
            </div>
        </form>
    </div>

    <a href="https://id.pinterest.com/pin/1027102258785807962/" target="_blank" class="cornerlink">
        <img src="https://id.pinterest.com/pin/1027102258785807962/" alt="ref" class="cornerimage">
    </a>

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
