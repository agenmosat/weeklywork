<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/items.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    if (isset($_POST['action'])) {
        $itemid = (int)$_POST['id'];
        $itemqty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;
        if (isset($products[$itemid])) {
            $availablestock = $products[$itemid]['stock'];
            if ($availablestock > 0) {
                if ($_POST['action'] === "buy") {
                    $buyqty = min($itemqty, $availablestock);
                    header("Location: checkout.php?id=" . $itemid . "&qty=" . $buyqty);
                    exit();
                }
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                $currentincart = $_SESSION['cart'][$itemid] ?? 0;
                $newcartqty = min($currentincart + $itemqty, $availablestock);
                if ($newcartqty > 0) {
                    $_SESSION['cart'][$itemid] = $newcartqty;
                }
            }
        }
        header("Location: cart.php");
        exit();
    }

    if (isset($_POST['updatecart']) && isset($_POST['qty'])) {
        foreach ($_POST['qty'] as $qid => $newqty) {
            $qid = (int)$qid;
            $newqty = (int)$newqty;
            if (isset($products[$qid])) {
                $maxstock = $products[$qid]['stock'];
                if ($newqty > 0 && $maxstock > 0) {
                    $_SESSION['cart'][$qid] = min($newqty, $maxstock);
                } else {
                    unset($_SESSION['cart'][$qid]);
                }
            } else {
                unset($_SESSION['cart'][$qid]);
            }
        }
        header("Location: cart.php");
        exit();
    }
}

if (isset($_GET['add'])) {
    $addid = (int)$_GET['add'];
    if (isset($products[$addid])) {
        $availablestock = $products[$addid]['stock'];
        if ($availablestock > 0) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            $currentincart = $_SESSION['cart'][$addid] ?? 0;
            if ($currentincart < $availablestock) {
                $_SESSION['cart'][$addid] = $currentincart + 1;
            }
        }
    }
    header("Location: cart.php");
    exit();
}

if (isset($_GET['remove'])) {
    $removeid = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$removeid])) {
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
            $realqty = min($qty, $p['stock']);
            if ($realqty <= 0) {
                unset($_SESSION['cart'][$cid]);
                continue;
            }
            $_SESSION['cart'][$cid] = $realqty;
            $itemtotal = $p['price'] * $realqty;
            $subtotal += $itemtotal;
            $cartcount += $realqty;
            $cartitems[] = [
                "id" => $p['id'],
                "name" => $p['name'],
                "price" => $p['price'],
                "stock" => $p['stock'],
                "qty" => $realqty,
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
    <link rel="stylesheet" href="../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

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
                    <?= csrf_field(); ?>
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
                                    <a href="cart.php?remove=<?php echo $c['id']; ?>" class="removelink" onclick="return confirmremove();">remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <div class="summarytotal">subtotal: $<?php echo number_format($subtotal, 2); ?></div>

                    <div class="actionrow">
                        <a href="dashboard.php" class="backlink">&laquo; continue shopping</a>
                        <button type="submit" name="updatecart" class="cartbtn">update cart</button>
                        <a href="cart.php?clear=1" class="cartbtn" onclick="return confirmclear();">clear cart</a>
                        <a href="checkout.php" class="buybtn">proceed to checkout</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

<script src="../assets/main.js"></script>

</body>
</html>
