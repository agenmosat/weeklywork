<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../home/includes/transaksihelper.php';

init_transaksi_tables($koneksi);

$tables = ['products', 'kasir', 'supplier', 'pesanan', 'penjualan', 'penjualan_detail', 'pembelian', 'pembelian_detail'];
$status_tables = [];

foreach ($tables as $t) {
    $q = mysqli_query($koneksi, "SHOW TABLES LIKE '$t'");
    $exists = ($q && mysqli_num_rows($q) > 0);
    $count = 0;
    if ($exists) {
        $cq = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM $t");
        if ($cq) {
            $row = mysqli_fetch_assoc($cq);
            $count = (int)$row['total'];
        }
    }
    $status_tables[$t] = [
        'exists' => $exists,
        'count' => $count
    ];
}

$lowstock = 0;
$lsq = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM products WHERE stock < 5");
if ($lsq) {
    $lsrow = mysqli_fetch_assoc($lsq);
    $lowstock = (int)$lsrow['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>checklist - darkfonder.net</title>
    <link rel="stylesheet" href="../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../home/partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../home/partials/sidebaradmin.php'; ?>

        <div class="content">
            <div class="heading">system & database checklist</div>

            <div class="emptybox">
                <div class="sidetitle">database connection</div>
                <table class="carttable" style="margin-bottom:18px">
                    <tr class="cartrow">
                        <td class="cartcell" style="width:200px; font-weight:bold">connection status</td>
                        <td class="cartcell"><?= $koneksi ? 'connected' : 'connection failed' ?></td>
                    </tr>
                    <tr class="cartrow">
                        <td class="cartcell" style="font-weight:bold">database host</td>
                        <td class="cartcell"><?= DB_HOST ?></td>
                    </tr>
                    <tr class="cartrow">
                        <td class="cartcell" style="font-weight:bold">database name</td>
                        <td class="cartcell"><?= DB_NAME ?></td>
                    </tr>
                </table>

                <div class="sidetitle">system tables status</div>
                <table class="carttable" style="margin-bottom:18px">
                    <tr class="cartheader">
                        <th class="cartcell">table name</th>
                        <th class="cartcell">status</th>
                        <th class="cartcell">total records</th>
                    </tr>
                    <?php foreach ($status_tables as $tbl => $st): ?>
                    <tr class="cartrow">
                        <td class="cartcell"><?= htmlspecialchars($tbl) ?></td>
                        <td class="cartcell"><?= $st['exists'] ? 'ready' : 'not created' ?></td>
                        <td class="cartcell"><?= $st['count'] ?> records</td>
                    </tr>
                    <?php endforeach; ?>
                </table>

                <div class="sidetitle">inventory & module summary</div>
                <table class="carttable">
                    <tr class="cartrow">
                        <td class="cartcell" style="width:250px; font-weight:bold">low stock products (&lt; 5)</td>
                        <td class="cartcell"><?= $lowstock ?> products</td>
                    </tr>
                    <tr class="cartrow">
                        <td class="cartcell" style="font-weight:bold">csrf protection</td>
                        <td class="cartcell">enabled</td>
                    </tr>
                    <tr class="cartrow">
                        <td class="cartcell" style="font-weight:bold">session timeout</td>
                        <td class="cartcell">1800 seconds (30 minutes)</td>
                    </tr>
                </table>

                <div class="actionrow" style="margin-top:14px">
                    <a href="admin.php" class="sidelink">&laquo; back to dashboard</a>
                    <button type="button" onclick="window.print()" class="cartbtn">print checklist</button>
                </div>
            </div>
        </div>
    </div>

<script src="../assets/main.js"></script>

</body>
</html>
