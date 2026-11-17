<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/items.php';
require_once __DIR__ . '/gateway.php';

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
$method = isset($_POST['method']) ? trim($_POST['method']) : "crypto escrow";
$source = isset($_POST['source']) ? trim($_POST['source']) : "cart";

$purchaseditems = [];
$subtotal = 0.0;
$shipping = 5.00;

if ($source === "direct") {
    $directid = (int)($_POST['direct_id'] ?? 0);
    $directqty = max(1, (int)($_POST['direct_qty'] ?? 1));
    if (isset($products[$directid])) {
        $p = $products[$directid];
        $realqty = min($directqty, $p['stock']);
        if ($realqty > 0) {
            $itemtotal = $p['price'] * $realqty;
            $subtotal += $itemtotal;
            $purchaseditems[] = [
                "id" => $p['id'],
                "name" => $p['name'],
                "qty" => $realqty,
                "price" => $p['price'],
                "total" => $itemtotal
            ];
        }
    }
} elseif (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $cid => $qty) {
        if (isset($products[$cid])) {
            $p = $products[$cid];
            $realqty = min((int)$qty, $p['stock']);
            if ($realqty > 0) {
                $itemtotal = $p['price'] * $realqty;
                $subtotal += $itemtotal;
                $purchaseditems[] = [
                    "id" => $p['id'],
                    "name" => $p['name'],
                    "qty" => $realqty,
                    "price" => $p['price'],
                    "total" => $itemtotal
                ];
            }
        }
    }
}

if (empty($purchaseditems)) {
    header("Location: cart.php");
    exit();
}

$total = $subtotal + $shipping;
$ordernumber = "onion" . rand(10000, 99999);
$gatewayres = processgatewaypayment($ordernumber, $total, $method, $name);
$txnid = $gatewayres['transaction_id'] ?? ("TXN" . rand(10000, 99999));
$escrowhash = $gatewayres['escrow_hash'] ?? "";
$gatewaysuccess = $gatewayres['success'] ?? false;
$ispaid = ($method !== "cash on delivery" && $gatewaysuccess);
$statuspembayaran = $ispaid ? "paid (escrow)" : ($method === "cash on delivery" ? "pending" : "failed");

foreach ($purchaseditems as $item) {
    $stmtstock = mysqli_prepare($koneksi, "UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");
    if ($stmtstock) {
        mysqli_stmt_bind_param($stmtstock, "ii", $item['qty'], $item['id']);
        mysqli_stmt_execute($stmtstock);
    }
}

mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_pesanan VARCHAR(50) NOT NULL,
    nama_pembeli VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    metode_pembayaran VARCHAR(50) NOT NULL,
    status_pembayaran VARCHAR(50) NOT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$fulladdress = $address . ", " . $city;
$stmtpesanan = mysqli_prepare($koneksi, "INSERT INTO pesanan (nomor_pesanan, nama_pembeli, alamat, total, metode_pembayaran, status_pembayaran) VALUES (?, ?, ?, ?, ?, ?)");
if ($stmtpesanan) {
    mysqli_stmt_bind_param($stmtpesanan, "sssdss", $ordernumber, $name, $fulladdress, $total, $method, $statuspembayaran);
    mysqli_stmt_execute($stmtpesanan);
}

if ($source === "cart") {
    unset($_SESSION['cart']);
}
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

    <?php require __DIR__ . '/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/sidebar.php'; ?>

        <div class="content">
            <div class="receiptbox">
                <div class="receiptstatus">
                    <?php echo $ispaid ? "order confirmed: encrypted in darkweb escrow" : "order placed: waiting dead drop payment"; ?>
                </div>
                <div class="heading">order receipt</div>

                <div class="receiptrow">
                    <span class="detaildesc">order identifier</span>
                    <span class="itemname">#<?php echo htmlspecialchars($ordernumber); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">gateway transaction id</span>
                    <span class="itemname"><?php echo htmlspecialchars($txnid); ?></span>
                </div>
                <?php if ($escrowhash !== ""): ?>
                    <div class="receiptrow">
                        <span class="detaildesc">escrow hash</span>
                        <span class="detaildesc"><?php echo htmlspecialchars($escrowhash); ?></span>
                    </div>
                <?php endif; ?>
                <div class="receiptrow">
                    <span class="detaildesc">receiver handle</span>
                    <span class="detaildesc"><?php echo htmlspecialchars($name); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">drop coordinates</span>
                    <span class="detaildesc"><?php echo htmlspecialchars($fulladdress); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">purchased items</span>
                    <span class="detaildesc">
                        <?php
                        $itemnames = [];
                        foreach ($purchaseditems as $pi) {
                            $itemnames[] = htmlspecialchars($pi['name']) . " (x" . $pi['qty'] . ")";
                        }
                        echo implode(", ", $itemnames);
                        ?>
                    </span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">subtotal</span>
                    <span class="detaildesc">$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">stealth shipping</span>
                    <span class="detaildesc">$<?php echo number_format($shipping, 2); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="itemname">total</span>
                    <span class="detailprice">$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">payment method</span>
                    <span class="detaildesc"><?php echo htmlspecialchars($method); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">escrow status</span>
                    <span class="detailstatus">
                        <?php echo $ispaid ? "locked in multi-sig escrow" : "pending cash settlement"; ?>
                    </span>
                </div>

                <div class="actionrow">
                    <a href="dashboard.php" class="buybtn">continue shopping</a>
                    <button type="button" onclick="window.print()" class="cartbtn">print invoice</button>
                </div>
            </div>
        </div>
    </div>

<script src="main.js"></script>

</body>
</html>
