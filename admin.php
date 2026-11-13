<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: dashboard.php");
    exit();
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
    <title>admin - darkfonder.net</title>
    <link rel="stylesheet" href="style.css?v=3">
</head>
<body class="marketpage themelight">

    <div class="topbar">
        <div class="brand">darkfonder.net</div>
        <div class="topnav">
            <a href="dashboard.php" class="navlink">store</a>
            <a href="admin.php" class="navlink">admin</a>
            <a href="admin.php?logout=1" class="navlink">logout</a>
        </div>
    </div>

    <div class="layout">
        <div class="sidebar">
            <div class="sidetitle">admin menu</div>
            <a href="admin.php" class="sidelink">dashboard</a>
            <a href="crud/product/product.php" class="sidelink">product</a>
            <a href="crud/cashier/cashier.php" class="sidelink">cashier</a>
            <a href="crud/supplier/supplier.php" class="sidelink">supplier</a>
        </div>

        <div class="content">
            <div class="heading">admin dashboard</div>
            <div class="emptybox">
                <div class="sidetitle">welcome superroot</div>
                <div class="detaildesc">admin dashboard access granted.</div>
            </div>
        </div>
    </div>
</body>
</html>
