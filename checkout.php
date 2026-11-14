<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/items.php';

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
$directid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$directqty = isset($_GET['qty']) ? max(1, (int)$_GET['qty']) : 1;
$source = "cart";

if ($directid > 0 && isset($products[$directid])) {
    $p = $products[$directid];
    $directqty = min($directqty, $p['stock']);
    if ($directqty > 0) {
        $source = "direct";
        $itemtotal = $p['price'] * $directqty;
        $checkoutitems[] = [
            "id" => $p['id'],
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
            $realqty = min((int)$qty, $p['stock']);
            if ($realqty <= 0) {
                continue;
            }
            $itemtotal = $p['price'] * $realqty;
            $subtotal += $itemtotal;
            $checkoutitems[] = [
                "id" => $p['id'],
                "name" => $p['name'],
                "qty" => $realqty,
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

    <?php require __DIR__ . '/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/sidebar.php'; ?>

        <div class="content">
            <a href="cart.php" class="backlink">&laquo; back to cart</a>
            <div class="heading">shipping checkout</div>

            <form action="payment.php" method="post" class="formbox">
                <input type="hidden" name="source" value="<?php echo htmlspecialchars($source); ?>">
                <?php if ($source === "direct"): ?>
                    <input type="hidden" name="direct_id" value="<?php echo $directid; ?>">
                    <input type="hidden" name="direct_qty" value="<?php echo $directqty; ?>">
                <?php endif; ?>

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

<script src="main.js"></script>

</body>
</html>
