<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/transaksi_helper.php';

init_transaksi_tables($koneksi);

$tgl_mulai = trim($_GET['tgl_mulai'] ?? ($_GET['dari'] ?? ''));
$tgl_akhir = trim($_GET['tgl_akhir'] ?? ($_GET['sampai'] ?? ''));

if (empty($tgl_mulai)) {
    $tgl_mulai = date('Y-m-01');
}
if (empty($tgl_akhir)) {
    $tgl_akhir = date('Y-m-d');
}
if ($tgl_mulai > $tgl_akhir) {
    $temp = $tgl_mulai;
    $tgl_mulai = $tgl_akhir;
    $tgl_akhir = $temp;
}

$datetime_mulai = $tgl_mulai . ' 00:00:00';
$datetime_akhir = $tgl_akhir . ' 23:59:59';

$total_sales = 0.0;
$count_sales = 0;

$stmt_sales = mysqli_prepare($koneksi, "SELECT COUNT(id_penjualan) AS count_sales, COALESCE(SUM(total), 0) AS total_sales FROM penjualan WHERE tanggal BETWEEN ? AND ?");
if ($stmt_sales) {
    mysqli_stmt_bind_param($stmt_sales, "ss", $datetime_mulai, $datetime_akhir);
    mysqli_stmt_execute($stmt_sales);
    $res_sales = mysqli_stmt_get_result($stmt_sales);
    if ($res_sales && ($row_sales = mysqli_fetch_assoc($res_sales))) {
        $count_sales = (int)$row_sales['count_sales'];
        $total_sales = (float)$row_sales['total_sales'];
    }
}

$total_cost = 0.0;
$count_cost = 0;

$stmt_cost = mysqli_prepare($koneksi, "SELECT COUNT(id_pembelian) AS count_cost, COALESCE(SUM(total), 0) AS total_cost FROM pembelian WHERE tanggal BETWEEN ? AND ?");
if ($stmt_cost) {
    mysqli_stmt_bind_param($stmt_cost, "ss", $datetime_mulai, $datetime_akhir);
    mysqli_stmt_execute($stmt_cost);
    $res_cost = mysqli_stmt_get_result($stmt_cost);
    if ($res_cost && ($row_cost = mysqli_fetch_assoc($res_cost))) {
        $count_cost = (int)$row_cost['count_cost'];
        $total_cost = (float)$row_cost['total_cost'];
    }
}

$gross_profit = $total_sales - $total_cost;
$profit_margin = ($total_sales > 0) ? (($gross_profit / $total_sales) * 100) : 0.0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>profit report - darkfonder.net</title>
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
            <div class="heading">gross profit estimation report</div>

            <div class="emptybox">
                <div class="filterbox">
                    <form method="get" action="laporan_laba.php" class="filterform">
                        <label for="tglmulai" class="filterlabel">start date</label>
                        <input type="date" id="tglmulai" name="tgl_mulai" class="forminput dateinput" value="<?= htmlspecialchars($tgl_mulai) ?>">
                        <label for="tglakhir" class="filterlabel">end date</label>
                        <input type="date" id="tglakhir" name="tgl_akhir" class="forminput dateinput" value="<?= htmlspecialchars($tgl_akhir) ?>">
                        <button type="submit" class="enterbtn">filter</button>
                        <a href="laporan_laba.php" class="printbtn" style="text-decoration:none">reset</a>
                        <button type="button" class="printbtn" onclick="window.print()">print report</button>
                    </form>
                </div>

                <div class="cardgrid">
                    <div class="summarycard">
                        <div class="cardtitle">total sales revenue</div>
                        <div class="cardvalue"><?= format_uang($total_sales) ?></div>
                    </div>
                    <div class="summarycard">
                        <div class="cardtitle">total purchase cost</div>
                        <div class="cardvalue"><?= format_uang($total_cost) ?></div>
                    </div>
                    <div class="summarycard">
                        <div class="cardtitle">estimated gross profit</div>
                        <div class="cardvalue"><?= format_uang($gross_profit) ?></div>
                    </div>
                    <div class="summarycard">
                        <div class="cardtitle">profit margin</div>
                        <div class="cardvalue"><?= number_format($profit_margin, 2) ?>%</div>
                    </div>
                </div>

                <div class="sidetitle" style="margin-top:14px;margin-bottom:8px">accounting summary</div>
                <table class="carttable">
                    <tr class="cartheader">
                        <th class="cartcell">financial component</th>
                        <th class="cartcell">transactions</th>
                        <th class="cartcell">amount</th>
                        <th class="cartcell">status indicator</th>
                    </tr>
                    <tr class="cartrow">
                        <td class="cartcell">sales revenue (omzet)</td>
                        <td class="cartcell"><?= $count_sales ?></td>
                        <td class="cartcell"><?= format_uang($total_sales) ?></td>
                        <td class="cartcell"><span class="pillok">revenue in</span></td>
                    </tr>
                    <tr class="cartrow">
                        <td class="cartcell">purchase cost (modal)</td>
                        <td class="cartcell"><?= $count_cost ?></td>
                        <td class="cartcell"><?= format_uang($total_cost) ?></td>
                        <td class="cartcell"><span class="pillno">cost out</span></td>
                    </tr>
                    <tr class="cartrow">
                        <td class="cartcell" style="font-weight:bold">estimated gross profit</td>
                        <td class="cartcell"><?= $count_sales + $count_cost ?></td>
                        <td class="cartcell" style="font-weight:bold"><?= format_uang($gross_profit) ?></td>
                        <td class="cartcell">
                            <?php if ($gross_profit >= 0): ?>
                                <span class="pillok">profit</span>
                            <?php else: ?>
                                <span class="pillno">loss</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="cartrow">
                        <td class="cartcell">profit margin</td>
                        <td class="cartcell">-</td>
                        <td class="cartcell"><?= number_format($profit_margin, 2) ?>%</td>
                        <td class="cartcell">
                            <?php if ($profit_margin >= 0): ?>
                                <span class="pillok">healthy</span>
                            <?php else: ?>
                                <span class="pillno">negative</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

<script src="../assets/main.js"></script>
</body>
</html>
