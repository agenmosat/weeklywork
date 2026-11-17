<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';

session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']),
    'samesite' => 'Lax'
]);

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../home/dashboard.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

$orders = [];
$checktable = mysqli_query($koneksi, "SHOW TABLES LIKE 'pesanan'");
if ($checktable && mysqli_num_rows($checktable) > 0) {
    $orderres = mysqli_query($koneksi, "SELECT id, nomor_pesanan, nama_pembeli, total, metode_pembayaran, status_pembayaran, tanggal FROM pesanan ORDER BY id DESC LIMIT 10");
    if ($orderres) {
        while ($orow = mysqli_fetch_assoc($orderres)) {
            $orders[] = $orow;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin - darkfonder.net</title>
    <link rel="stylesheet" href="../style.css?v=3">
</head>
<body class="marketpage themelight">

    <div class="topbar">
        <div class="brand">darkfonder.net</div>
        <div class="themeswitcher">
            <span class="themelabel">theme</span>
            <select id="themepick" class="themeselect" onchange="switchtheme(this.value)">
                <option value="themelight">light</option>
                <option value="themedark">dark</option>
            </select>
        </div>
        <div class="topnav">
            <a href="../home/dashboard.php" class="navlink">store</a>
            <a href="admin.php" class="navlink">admin</a>
            <a href="../logout.php" class="navlink">logout</a>
        </div>
    </div>

    <div class="layout">
        <div class="sidebar">
            <div class="sidetitle">admin menu</div>
            <a href="admin.php" class="sidelink">dashboard</a>
            <a href="product/product.php" class="sidelink">product</a>
            <a href="cashier/cashier.php" class="sidelink">cashier</a>
            <a href="supplier/supplier.php" class="sidelink">supplier</a>
        </div>

        <div class="content">
            <div class="heading">admin dashboard</div>
            <div class="emptybox">
                <div class="sidetitle">welcome superroot</div>
                <div class="detaildesc">admin dashboard access granted.</div>
            </div>

            <div class="emptybox" style="margin-top:14px">
                <div class="sidetitle">recent orders</div>
                <?php if (empty($orders)): ?>
                    <div class="detaildesc">no orders recorded yet.</div>
                <?php else: ?>
                    <table class="carttable">
                        <tr class="cartheader">
                            <th class="cartcell">id</th>
                            <th class="cartcell">order #</th>
                            <th class="cartcell">buyer</th>
                            <th class="cartcell">total</th>
                            <th class="cartcell">method</th>
                            <th class="cartcell">status</th>
                            <th class="cartcell">date</th>
                        </tr>
                        <?php foreach ($orders as $o): ?>
                            <tr class="cartrow">
                                <td class="cartcell"><?php echo $o['id']; ?></td>
                                <td class="cartcell"><?php echo htmlspecialchars($o['nomor_pesanan']); ?></td>
                                <td class="cartcell"><?php echo htmlspecialchars($o['nama_pembeli']); ?></td>
                                <td class="cartcell">$<?php echo number_format((float)$o['total'], 2); ?></td>
                                <td class="cartcell"><?php echo htmlspecialchars($o['metode_pembayaran']); ?></td>
                                <td class="cartcell"><?php echo htmlspecialchars($o['status_pembayaran']); ?></td>
                                <td class="cartcell"><?php echo htmlspecialchars($o['tanggal']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

<script src="../main.js"></script>

</body>
</html>
