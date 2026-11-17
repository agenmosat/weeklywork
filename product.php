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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$notfound = false;

if ($id <= 0 || !isset($products[$id])) {
    $notfound = true;
    http_response_code(404);
} else {
    $product = $products[$id];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $notfound ? "404 not found" : htmlspecialchars($product['name']); ?> - darkfonder.net</title>
    <link rel="stylesheet" href="style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/sidebar.php'; ?>

        <div class="content">
            <a href="dashboard.php" class="backlink">&laquo; back to store</a>

            <?php if ($notfound): ?>
                <div class="emptybox">
                    <div class="sidetitle">404 - product not found</div>
                    <div class="detaildesc">the requested product does not exist or has been removed.</div>
                    <a href="dashboard.php" class="buybtn">back to store</a>
                </div>
            <?php else: ?>
                <div class="detailbox">
                    <div class="detailimage">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="productimage">
                    </div>
                    <div class="detailinfo">
                        <span class="tagitem"><?php echo htmlspecialchars($product['category']); ?></span>
                        <div class="detailtitle"><?php echo htmlspecialchars($product['name']); ?></div>
                        <div class="detailprice">$<?php echo number_format($product['price'], 2); ?></div>
                        <div class="detaildesc"><?php echo htmlspecialchars($product['desc']); ?></div>
                        <div class="detailstatus">
                            <?php if ($product['stock'] > 0): ?>
                                in stock: <?php echo $product['stock']; ?> units available [verified stash]
                            <?php else: ?>
                                out of stock
                            <?php endif; ?>
                        </div>

                        <?php if ($product['stock'] > 0): ?>
                            <form action="cart.php" method="post" class="qtyform">
                                <?= csrf_field(); ?>
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
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

<script src="main.js"></script>

</body>
</html>
