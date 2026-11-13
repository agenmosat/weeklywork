<?php
session_start();

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $itemid = (int)$_POST['id'];
    $itemqty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;
    if (isset($products[$itemid])) {
        if ($_POST['action'] === "buy") {
            header("Location: checkout.php?id=" . $itemid . "&qty=" . $itemqty);
            exit();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$itemid])) {
            $_SESSION['cart'][$itemid] += $itemqty;
        } else {
            $_SESSION['cart'][$itemid] = $itemqty;
        }
    }
    header("Location: cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updatecart']) && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $qid => $newqty) {
        $qid = (int)$qid;
        $newqty = (int)$newqty;
        if ($newqty > 0) {
            $_SESSION['cart'][$qid] = $newqty;
        } else {
            unset($_SESSION['cart'][$qid]);
        }
    }
    header("Location: cart.php");
    exit();
}

if (isset($_GET['add'])) {
    $addid = (int)$_GET['add'];
    if (isset($products[$addid])) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$addid])) {
            $_SESSION['cart'][$addid]++;
        } else {
            $_SESSION['cart'][$addid] = 1;
        }
    }
    header("Location: cart.php");
    exit();
}

if (isset($_GET['remove'])) {
    $removeid = (int)$_GET['remove'];
    if (isset($products[$removeid])) {
        unset($_SESSION['cart'][$removeid]);
    }
    header("Location: cart.php");
    exit();
}

if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}

$cartcount = 0;
$subtotal = 0.0;
$cartitems = [];

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $cid => $qty) {
        if (isset($products[$cid])) {
            $p = $products[$cid];
            $itemtotal = $p['price'] * $qty;
            $subtotal += $itemtotal;
            $cartcount += $qty;
            $cartitems[] = [
                "id" => $p['id'],
                "name" => $p['name'],
                "price" => $p['price'],
                "stock" => $p['stock'],
                "qty" => $qty,
                "total" => $itemtotal
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>shopping cart - darkfonder.net</title>
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
            <div class="heading">shopping cart</div>

            <?php if (empty($cartitems)): ?>
                <div class="emptybox">
                    <div class="sidetitle">your cart is currently empty</div>
                    <div class="detaildesc">you have no items in your cart. find something you like in our marketplace!</div>
                    <a href="dashboard.php" class="buybtn">browse marketplace</a>
                </div>
            <?php else: ?>
                <form action="cart.php" method="post" class="cartbox">
                    <table class="carttable">
                        <tr class="cartheader">
                            <th class="cartcell">item</th>
                            <th class="cartcell">price</th>
                            <th class="cartcell">quantity</th>
                            <th class="cartcell">subtotal</th>
                            <th class="cartcell">action</th>
                        </tr>
                        <?php foreach ($cartitems as $c): ?>
                            <tr class="cartrow">
                                <td class="cartcell"><?php echo htmlspecialchars($c['name']); ?></td>
                                <td class="cartcell">$<?php echo number_format($c['price'], 2); ?></td>
                                <td class="cartcell">
                                    <input type="number" name="qty[<?php echo $c['id']; ?>]" value="<?php echo $c['qty']; ?>" min="1" max="<?php echo $c['stock']; ?>" class="qtyinput">
                                </td>
                                <td class="cartcell">$<?php echo number_format($c['total'], 2); ?></td>
                                <td class="cartcell">
                                    <a href="cart.php?remove=<?php echo $c['id']; ?>" class="removelink">remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <div class="summarytotal">subtotal: $<?php echo number_format($subtotal, 2); ?></div>

                    <div class="actionrow">
                        <a href="dashboard.php" class="backlink">&laquo; continue shopping</a>
                        <button type="submit" name="updatecart" class="cartbtn">update cart</button>
                        <a href="cart.php?clear=1" class="cartbtn">clear cart</a>
                        <a href="checkout.php" class="buybtn">proceed to checkout</a>
                    </div>
                </form>
            <?php endif; ?>
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
