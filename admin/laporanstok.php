<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/transaksihelper.php';

init_transaksi_tables($koneksi);

$threshold = isset($_GET['threshold']) && is_numeric($_GET['threshold']) ? (int)$_GET['threshold'] : null;
$active_limit = $threshold !== null ? $threshold : 5;

if ($threshold !== null) {
    $res_products = mysqli_query($koneksi, "SELECT * FROM products WHERE stock <= $threshold ORDER BY stock ASC");
} else {
    $res_products = mysqli_query($koneksi, "SELECT * FROM products ORDER BY name ASC");
}

$q_all = mysqli_query($koneksi, "SELECT * FROM products");
$total_items = 0;
$total_stock = 0;
$low_stock = 0;
$out_stock = 0;

if ($q_all) {
    while ($row = mysqli_fetch_assoc($q_all)) {
        $total_items++;
        $total_stock += (int)$row['stock'];
        if ((int)$row['stock'] <= 5) {
            $low_stock++;
        }
        if ((int)$row['stock'] <= 0) {
            $out_stock++;
        }
    }
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
        <?php require __DIR__ . '/../partials/sidebaradmin.php'; ?>

        <div class="content">
            <div class="heading">stock inventory report</div>

            <div class="emptybox">
                <div class="filterbox">
                    <form method="get" action="laporanstok.php" class="filterform">
                        <label for="threshinput" class="filterlabel">minimum stock threshold</label>
                        <input type="number" id="threshinput" name="threshold" class="forminput dateinput" value="<?= $threshold !== null ? htmlspecialchars((string)$threshold) : '' ?>" placeholder="e.g. 5" min="0">
                        <button type="submit" class="enterbtn">filter</button>
                        <a href="laporanstok.php" class="printbtn" style="text-decoration:none">reset</a>
                        <button type="button" class="printbtn" onclick="window.print()">print report</button>
                    </form>
                </div>

                <div class="cardgrid">
                    <div class="summarycard">
                        <div class="cardtitle">total products</div>
                        <div class="cardvalue"><?= $total_items ?></div>
                    </div>
                    <div class="summarycard">
                        <div class="cardtitle">total stock units</div>
                        <div class="cardvalue"><?= $total_stock ?></div>
                    </div>
                    <div class="summarycard">
                        <div class="cardtitle">low stock alerts</div>
                        <div class="cardvalue"><?= $low_stock ?></div>
                    </div>
                    <div class="summarycard">
                        <div class="cardtitle">out of stock</div>
                        <div class="cardvalue"><?= $out_stock ?></div>
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
