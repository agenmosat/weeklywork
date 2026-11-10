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

$name = isset($_POST['name']) ? $_POST['name'] : "john doe";
$address = isset($_POST['address']) ? $_POST['address'] : "123 market street";
$city = isset($_POST['city']) ? $_POST['city'] : "springfield 97477";
$subtotal = isset($_POST['subtotal']) ? $_POST['subtotal'] : "25.00";
$shipping = isset($_POST['shipping']) ? $_POST['shipping'] : "5.00";
$total = isset($_POST['total']) ? $_POST['total'] : "30.00";

$cartcount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cartcount += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>payment - darkfonder.net</title>
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
            <a href="cart.php" class="navlink">cart (<?php echo $cartcount; ?>)</a>
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
            <a href="checkout.php" class="backlink">&laquo; back to checkout</a>
            <div class="heading">payment gateway [escrow deposit]</div>

            <form action="invoice.php" method="post" class="formbox">
                <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
                <input type="hidden" name="address" value="<?php echo htmlspecialchars($address); ?>">
                <input type="hidden" name="city" value="<?php echo htmlspecialchars($city); ?>">
                <input type="hidden" name="subtotal" value="<?php echo htmlspecialchars($subtotal); ?>">
                <input type="hidden" name="shipping" value="<?php echo htmlspecialchars($shipping); ?>">
                <input type="hidden" name="total" value="<?php echo htmlspecialchars($total); ?>">

                <div class="paybox">
                    <div class="receiptnotice">
                        dispatch target: <strong><?php echo htmlspecialchars($name); ?></strong>, <?php echo htmlspecialchars($address); ?>, <?php echo htmlspecialchars($city); ?>
                    </div>

                    <div class="sidetitle">select settlement method</div>

                    <div class="paymethod">
                        <label>
                            <input type="radio" name="method" value="crypto escrow" checked> anonymous crypto escrow (xmr monero / btc bitcoin)
                        </label>
                    </div>

                    <div class="paymethod">
                        <label>
                            <input type="radio" name="method" value="credit card"> credit or debit card
                        </label>
                        <div class="cardinputs">
                            <div class="formrow">
                                <label class="formlabel">card number</label>
                                <input type="text" class="forminput" value="4111 2222 3333 4444">
                            </div>
                            <div class="formrow">
                                <label class="formlabel">expiry date</label>
                                <input type="text" class="forminput" value="12/28">
                            </div>
                            <div class="formrow">
                                <label class="formlabel">security code cvv</label>
                                <input type="text" class="forminput" value="123">
                            </div>
                        </div>
                    </div>

                    <div class="paymethod">
                        <label>
                            <input type="radio" name="method" value="bank transfer"> wire transfer
                        </label>
                    </div>

                    <div class="paymethod">
                        <label>
                            <input type="radio" name="method" value="cash on delivery"> dead drop cash delivery
                        </label>
                    </div>

                    <div class="summarytotal">amount to pay: $<?php echo htmlspecialchars($total); ?></div>

                    <button type="submit" class="buybtn">confirm and pay $<?php echo htmlspecialchars($total); ?></button>
                </div>
            </form>
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
