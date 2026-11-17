<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/items.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
}

$name = isset($_POST['name']) ? trim($_POST['name']) : "john doe";
$address = isset($_POST['address']) ? trim($_POST['address']) : "123 market street";
$city = isset($_POST['city']) ? trim($_POST['city']) : "springfield 97477";
$source = isset($_POST['source']) ? $_POST['source'] : "cart";
$directid = isset($_POST['direct_id']) ? (int)$_POST['direct_id'] : 0;
$directqty = isset($_POST['direct_qty']) ? max(1, (int)$_POST['direct_qty']) : 1;

$subtotal = 0.0;
$shipping = 5.00;

if ($source === "direct" && $directid > 0 && isset($products[$directid])) {
    $p = $products[$directid];
    $directqty = min($directqty, $p['stock']);
    $subtotal = $p['price'] * $directqty;
} elseif (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $cid => $qty) {
        if (isset($products[$cid])) {
            $p = $products[$cid];
            $realqty = min((int)$qty, $p['stock']);
            $subtotal += $p['price'] * $realqty;
        }
    }
}

$total = $subtotal + $shipping;

if ($subtotal <= 0) {
    header("Location: cart.php");
    exit();
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

    <?php require __DIR__ . '/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/sidebar.php'; ?>

        <div class="content">
            <a href="checkout.php" class="backlink">&laquo; back to checkout</a>
            <div class="heading">payment gateway [escrow deposit]</div>

            <form action="invoice.php" method="post" class="formbox">
                <?= csrf_field(); ?>
                <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
                <input type="hidden" name="address" value="<?php echo htmlspecialchars($address); ?>">
                <input type="hidden" name="city" value="<?php echo htmlspecialchars($city); ?>">
                <input type="hidden" name="source" value="<?php echo htmlspecialchars($source); ?>">
                <?php if ($source === "direct"): ?>
                    <input type="hidden" name="direct_id" value="<?php echo $directid; ?>">
                    <input type="hidden" name="direct_qty" value="<?php echo $directqty; ?>">
                <?php endif; ?>

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

                    <div class="summarytotal">amount to pay: $<?php echo number_format($total, 2); ?></div>

                    <button type="submit" class="buybtn">confirm and pay $<?php echo number_format($total, 2); ?></button>
                </div>
            </form>
        </div>
    </div>

<script src="main.js"></script>

</body>
</html>
