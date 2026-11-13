<?php
session_start();

// Secure session cookies
session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']),
    'samesite' => 'Lax'
]);

require_once "items.php";

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$checkoutitems = [];
$subtotal = 0.0;
$shipping = 5.00;

if (isset($_GET['id'])) {
    $directid = (int)$_GET['id'];
    $directqty = isset($_GET['qty']) ? max(1, (int)$_GET['qty']) : 1;
    if (isset($products[$directid]) && $products[$directid]['stock'] >= $directqty) {
        $p = $products[$directid];
        $itemtotal = $p['price'] * $directqty;
        $checkoutitems[] = [
            "name" => $p['name'],
            "qty" => $directqty,
            "price" => $p['price'],
            "total" => $itemtotal
        ];
        $subtotal = $itemtotal;
    }
} elseif (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $cid => $qty) {
        if (isset($products[$cid]) && $products[$cid]['stock'] >= $qty) {
            $p = $products[$cid];
            $itemtotal = $p['price'] * $qty;
            $subtotal += $itemtotal;
            $checkoutitems[] = [
                "name" => $p['name'],
                "qty" => $qty,
                "price" => $p['price'],
                "total" => $itemtotal
            ];
        }
    }
}

if (empty($checkoutitems)) {
    header("Location: cart.php");
    exit();
}

$total = $subtotal + $shipping;

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
    <title>checkout - darkfonder.net</title>
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
            <a href="cart.php" class="backlink">&laquo; back to cart</a>
            <div class="heading">shipping checkout</div>

            <form action="payment.php" method="post" class="formbox">
                <input type="hidden" name="subtotal" value="<?php echo number_format($subtotal, 2); ?>">
                <input type="hidden" name="shipping" value="<?php echo number_format($shipping, 2); ?>">
                <input type="hidden" name="total" value="<?php echo number_format($total, 2); ?>">

                <div class="detailbox">
                    <div class="detailinfo">
                        <div class="sidetitle">stealth shipping drop</div>
                        <div class="formrow">
                            <label class="formlabel">receiver handle / name</label>
                            <input type="text" name="name" class="forminput" value="john doe" required>
                        </div>
                        <div class="formrow">
                            <label class="formlabel">dead drop / delivery address</label>
                            <input type="text" name="address" class="forminput" value="123 market street" required>
                        </div>
                        <div class="formrow">
                            <label class="formlabel">city and zip code</label>
                            <input type="text" name="city" class="forminput" value="springfield 97477" required>
                        </div>

                        <div class="sidetitle">vacuum stealth packaging</div>
                        <div class="formrow">
                            <label>
                                <input type="radio" name="shippingmethod" value="standard" checked> stealth tracked dispatch - $5.00
                            </label>
                        </div>
                    </div>

                    <div class="summarybox">
                        <div class="summarytitle">order summary</div>
                        <?php foreach ($checkoutitems as $ci): ?>
                            <div class="summaryrow">
                                <span><?php echo htmlspecialchars($ci['name']); ?> (x<?php echo $ci['qty']; ?>)</span>
                                <span>$<?php echo number_format($ci['total'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="summaryrow">
                            <span>shipping fee</span>
                            <span>$<?php echo number_format($shipping, 2); ?></span>
                        </div>
                        <div class="summarytotal">total: $<?php echo number_format($total, 2); ?></div>
                        <button type="submit" class="buybtn">proceed to payment</button>
                    </div>
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
