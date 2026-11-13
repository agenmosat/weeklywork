<?php
session_start();
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

$cartcount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cartcount += $qty;
    }
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
if (!isset($products[$id])) {
    $id = 1;
}
$product = $products[$id];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - darkfonder.net</title>
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

        </div>

        <div class="content">
            <a href="dashboard.php" class="backlink">&laquo; back to items</a>

            <div class="detailbox">
                <div class="detailimage">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="productimage">
                </div>
                <div class="detailinfo">
                    <span class="tagitem"><?php echo htmlspecialchars($product['category']); ?></span>
                    <div class="detailtitle"><?php echo htmlspecialchars($product['name']); ?></div>
                    <div class="detailprice">$<?php echo number_format($product['price'], 2); ?></div>
                    <div class="detaildesc"><?php echo htmlspecialchars($product['desc']); ?></div>
                    <div class="detailstatus">in stock: <?php echo $product['stock']; ?> units available [verified stash]</div>

                    <form action="cart.php" method="post" class="qtyform">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <div class="qtybox">
                            <label class="formlabel">select quantity:</label>
                            <input type="number" name="qty" value="1" min="1" max="<?php echo $product['stock']; ?>" class="qtyinput" required>
                        </div>
                        <div class="actionrow">
                            <button type="submit" name="action" value="buy" class="buybtn">buy now</button>
                            <button type="submit" name="action" value="cart" class="cartbtn">add to cart</button>
                        </div>
                    </form>
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
