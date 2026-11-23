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
$ordernumber = "TB-" . date("Ymd") . "-" . rand(100, 999);
$gatewayres = processgatewaypayment($ordernumber, $total, $method, $name);
$txnid = $gatewayres['transaction_id'] ?? ("TXN" . rand(10000, 99999));
$escrowhash = $gatewayres['escrow_hash'] ?? "";
$gatewaysuccess = $gatewayres['success'] ?? false;
$ispaid = ($method !== "cash on delivery" && $gatewaysuccess);
$statuspembayaran = $ispaid ? "lunas" : ($method === "cash on delivery" ? "menunggu cod" : "belum lunas");

foreach ($purchaseditems as $item) {
    $stmtstock = mysqli_prepare($koneksi, "UPDATE products SET stock = GREATEST(0, stock - ?), terjual = terjual + ? WHERE id = ?");
    if ($stmtstock) {
        mysqli_stmt_bind_param($stmtstock, "iii", $item['qty'], $item['qty'], $item['id']);
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
$pagetitle = "nota pesanan - fivefonders.net";
require __DIR__ . '/../partials/header.php';
?>

    <?php require __DIR__ . '/../partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="content">
            <div class="receiptbox">
                <div class="receiptstatus">
                    <?php echo $ispaid ? "pesanan berhasil: pembayaran telah lunas" : "pesanan berhasil: menunggu pembayaran di tempat (cod)"; ?>
                </div>
                <div class="heading">nota pesanan</div>

                <div class="receiptrow">
                    <span class="detaildesc">nomor pesanan</span>
                    <span class="itemname">No. <?php echo htmlspecialchars($ordernumber); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">id transaksi</span>
                    <span class="itemname"><?php echo htmlspecialchars($txnid); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">nama penerima</span>
                    <span class="detaildesc"><?php echo htmlspecialchars($name); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">alamat pengiriman</span>
                    <span class="detaildesc"><?php echo htmlspecialchars($fulladdress); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">daftar barang</span>
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
                    <span class="detaildesc">subtotal belanja</span>
                    <span class="detaildesc">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">ongkos kirim armada</span>
                    <span class="detaildesc">Rp <?php echo number_format($shipping, 0, ',', '.'); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="itemname">total pembayaran</span>
                    <span class="detailprice">Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">metode pembayaran</span>
                    <span class="detaildesc"><?php echo htmlspecialchars($method); ?></span>
                </div>
                <div class="receiptrow">
                    <span class="detaildesc">status transaksi</span>
                    <span class="detailstatus">
                        <?php echo $ispaid ? "lunas" : "menunggu pembayaran tunai"; ?>
                    </span>
                </div>

                <div class="actionrow">
                    <a href="dashboard.php" class="buybtn">kembali belanja</a>
                    <button type="button" onclick="window.print()" class="cartbtn">cetak nota</button>
                </div>
            </div>
        </div>
    </div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
