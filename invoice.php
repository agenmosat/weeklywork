<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : "john doe";
$address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : "123 market street";
$city = isset($_POST['city']) ? htmlspecialchars($_POST['city']) : "springfield 97477";
$subtotal = isset($_POST['subtotal']) ? htmlspecialchars($_POST['subtotal']) : "25.00";
$shipping = isset($_POST['shipping']) ? htmlspecialchars($_POST['shipping']) : "5.00";
$total = isset($_POST['total']) ? htmlspecialchars($_POST['total']) : "30.00";
$method = isset($_POST['method']) ? htmlspecialchars($_POST['method']) : "crypto escrow";

if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

$ordernumber = "onion" . rand(10000, 99999);
$ispaid = ($method !== "cash on delivery");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>order receipt - darkfonder.net</title>
    <link rel="stylesheet" href="style.css?v=3">
</head>
<body class="marketpage themelight">

    <div class="topbar">
        <div class="brand">darkfonder.net</div>
        <form action="dashboard.php" method="get" class="searchbox">
            <input type="text" name="q" placeholder="search items..." class="searchinput">
            <button type="submit" class="searchbtn">search</button>
        </form>
        <div class="themeswitcher">
            <span class="themelabel">theme</span>
            <select id="themepick" class="themeselect" onchange="switchtheme(this.value)">
                <option value="themelight">light</option>
                <option value="themedark">dark</option>
            </select>
        </div>
        <div class="topnav">
            <a href="dashboard.php" class="navlink">home</a>
            <a href="cart.php" class="navlink">cart (0)</a>
            <a href="dashboard.php?logout=1" class="navlink">logout</a>
        </div>
    </div>

    <div class="layout">
        <div class="sidebar">
            <div class="sidetitle">categories</div>
            <a href="dashboard.php" class="sidelink">all items</a>
            <a href="dashboard.php?cat=weapons" class="sidelink">weapons (firearms)</a>
            <a href="dashboard.php?cat=electronics" class="sidelink">electronics</a>
            <a href="dashboard.php?cat=collectibles" class="sidelink">collectibles</a>
            <a href="dashboard.php?cat=homegoods" class="sidelink">homegoods</a>


        </div>

        <div class="content">
            <div class="receiptbox">
                <div class="receiptstatus">
                    <?php echo $ispaid ? "order confirmed: encrypted in darkweb escrow" : "order placed: waiting dead drop payment"; ?>
                </div>
                <div class="heading">order receipt</div>

                <div class="receiptrow">
                    <span class="detaildesc">order identifier</span>
                    <span class="itemname">#<?php echo $ordernumber; ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">receiver handle</span>
                    <span class="detaildesc"><?php echo $name; ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">drop coordinates</span>
                    <span class="detaildesc"><?php echo $address . ", " . $city; ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">subtotal</span>
                    <span class="detaildesc">$<?php echo $subtotal; ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">stealth shipping</span>
                    <span class="detaildesc">$<?php echo $shipping; ?></span>
                </div>
                <div class="receiptrow">
                    <span class="itemname">total</span>
                    <span class="detailprice">$<?php echo $total; ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">payment method</span>
                    <span class="detaildesc"><?php echo $method; ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">escrow status</span>
                    <span class="detailstatus">
                        <?php echo $ispaid ? "locked in multi-sig escrow" : "pending cash settlement"; ?>
                    </span>
                </div>

                <div class="actionrow">
                    <a href="dashboard.php" class="buybtn">continue shopping</a>
                </div>
            </div>
        </div>
    </div>

<script>
function switchtheme(theme) {
    document.body.className = "marketpage " + theme;
    localStorage.setItem("theme", theme);
}
var savedtheme = localStorage.getItem("theme");
if (savedtheme) {
    document.body.className = "marketpage " + savedtheme;
    var sel = document.getElementById("themepick");
    if (sel) {
        sel.value = savedtheme;
    }
}
</script>

</body>
</html>
