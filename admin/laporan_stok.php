<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/transaksi_helper.php';

init_transaksi_tables($koneksi);

$threshold_input = trim($_GET['threshold'] ?? ($_GET['min_stock'] ?? ''));
$threshold = ($threshold_input !== '' && is_numeric($threshold_input)) ? (int)$threshold_input : null;

$active_limit = ($threshold !== null) ? $threshold : 5;

if ($threshold !== null) {
    $stmt = mysqli_prepare($koneksi, "SELECT id, name, price, stock, category FROM products WHERE stock <= ? ORDER BY stock ASC, name ASC");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $threshold);
        mysqli_stmt_execute($stmt);
        $res_products = mysqli_stmt_get_result($stmt);
    } else {
        $res_products = false;
    }
} else {
    $res_products = mysqli_query($koneksi, "SELECT id, name, price, stock, category FROM products ORDER BY name ASC");
}

$stats = [
    'total_items' => 0,
    'total_stock' => 0,
    'low_stock' => 0,
    'out_stock' => 0
];
$q_stats = mysqli_query($koneksi, "SELECT COUNT(id) AS total_items, COALESCE(SUM(stock), 0) AS total_stock, COALESCE(SUM(CASE WHEN stock <= 5 THEN 1 ELSE 0 END), 0) AS low_stock, COALESCE(SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END), 0) AS out_stock FROM products");
if ($q_stats && ($row_stats = mysqli_fetch_assoc($q_stats))) {
    $stats['total_items'] = (int)$row_stats['total_items'];
    $stats['total_stock'] = (int)$row_stats['total_stock'];
    $stats['low_stock'] = (int)$row_stats['low_stock'];
    $stats['out_stock'] = (int)$row_stats['out_stock'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>stock report - darkfonder.net</title>
    <link rel="stylesheet" href="../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../partials/topbar.php'; ?>

    <div class="layout">
        <div class="sidebar">
            <div class="sidetitle">admin menu</div>
            <a href="admin.php" class="sidelink">dashboard</a>
            <a href="product/product.php" class="sidelink">product</a>
            <a href="cashier/cashier.php" class="sidelink">cashier</a>
            <a href="supplier/supplier.php" class="sidelink">supplier</a>
            <a href="penjualan/penjualan.php" class="sidelink">sales</a>
            <a href="pembelian/pembelian.php" class="sidelink">purchases</a>
            <a href="checklist.php" class="sidelink">checklist</a>
            <div class="sidetitle">reports</div>
            <a href="laporan_penjualan.php" class="sidelink">sales report</a>
            <a href="laporan_pembelian.php" class="sidelink">purchase report</a>
            <a href="laporan_stok.php" class="sidelink">stock report</a>
            <a href="laporan_laba.php" class="sidelink">profit report</a>
        </div>

        <div class="content">
            <div class="heading">stock inventory report</div>

            <div class="emptybox">
                <div class="filterbox">
                    <form method="get" action="laporan_stok.php" class="filterform">
                        <label for="threshinput" class="filterlabel">minimum stock threshold</label>
                        <input type="number" id="threshinput" name="threshold" class="forminput dateinput" value="<?= $threshold !== null ? htmlspecialchars((string)$threshold) : '' ?>" placeholder="e.g. 5" min="0">
                        <button type="submit" class="enterbtn">filter</button>
                        <a href="laporan_stok.php" class="printbtn" style="text-decoration:none">reset</a>
                        <button type="button" class="printbtn" onclick="window.print()">print report</button>
                    </form>
                </div>

                <div class="cardgrid">
                    <div class="summarycard">
                        <div class="cardtitle">total products</div>
                        <div class="cardvalue"><?= $stats['total_items'] ?></div>
                    </div>
                    <div class="summarycard">
                        <div class="cardtitle">total stock units</div>
                        <div class="cardvalue"><?= $stats['total_stock'] ?></div>
                    </div>
                    <div class="summarycard">
                        <div class="cardtitle">low stock alerts</div>
                        <div class="cardvalue"><?= $stats['low_stock'] ?></div>
                    </div>
                    <div class="summarycard">
                        <div class="cardtitle">out of stock</div>
                        <div class="cardvalue"><?= $stats['out_stock'] ?></div>
                    </div>
                </div>

                <table class="carttable">
                    <tr class="cartheader">
                        <th class="cartcell">id</th>
                        <th class="cartcell">product name</th>
                        <th class="cartcell">category</th>
                        <th class="cartcell">unit price</th>
                        <th class="cartcell">stock units</th>
                        <th class="cartcell">status</th>
                    </tr>
                    <?php if ($res_products && mysqli_num_rows($res_products) > 0): ?>
                        <?php while ($prod = mysqli_fetch_assoc($res_products)): ?>
                            <?php $stk = (int)$prod['stock']; ?>
                            <tr class="cartrow">
                                <td class="cartcell"><?= (int)$prod['id'] ?></td>
                                <td class="cartcell"><?= htmlspecialchars($prod['name']) ?></td>
                                <td class="cartcell"><?= htmlspecialchars($prod['category'] ?? '-') ?></td>
                                <td class="cartcell"><?= format_uang($prod['price']) ?></td>
                                <td class="cartcell"><?= $stk ?></td>
                                <td class="cartcell">
                                    <?php if ($stk <= 0): ?>
                                        <span class="pillno">out of stock</span>
                                    <?php elseif ($stk <= $active_limit): ?>
                                        <span class="pillalert">low stock</span>
                                    <?php else: ?>
                                        <span class="pillok">in stock</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr class="cartrow">
                            <td class="cartcell" colspan="6">no products found matching the criteria.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

<script src="../assets/main.js"></script>
</body>
</html>
