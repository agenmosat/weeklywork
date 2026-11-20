<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/items.php';
require_once __DIR__ . '/../includes/gateway.php';
require_once __DIR__ . '/../includes/transaksihelper.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php?login=1");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
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

$directid = (int)($_POST['direct_id'] ?? 0);
$directqty = max(1, (int)($_POST['direct_qty'] ?? 1));

$result = hitung_pesanan($products, $source, $directid, $directqty, $_SESSION['cart'] ?? []);
$purchaseditems = $result['items'];
$subtotal = $result['subtotal'];
$shipping = $result['shipping'];
$total = $result['total'];

if (empty($purchaseditems)) {
    header("Location: cart.php");
    exit();
}
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

$fulladdress = $address . ", " . $city;
$stmtpesanan = mysqli_prepare($koneksi, "INSERT INTO pesanan (nomor_pesanan, nama_pembeli, alamat, total, metode_pembayaran, status_pembayaran) VALUES (?, ?, ?, ?, ?, ?)");
if ($stmtpesanan) {
    mysqli_stmt_bind_param($stmtpesanan, "sssdss", $ordernumber, $name, $fulladdress, $total, $method, $statuspembayaran);
    mysqli_stmt_execute($stmtpesanan);
}

if ($source === "cart") {
    unset($_SESSION['cart']);
}
$pagetitle = "order receipt - darkfonder.net";
require __DIR__ . '/../partials/header.php';
?>

    <?php require __DIR__ . '/../partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

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

<?php require __DIR__ . '/../partials/footer.php'; ?>
