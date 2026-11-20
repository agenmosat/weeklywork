<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/transaksihelper.php';

init_transaksi_tables($koneksi);

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: penjualan.php');
    exit;
}

$stmt = mysqli_prepare($koneksi, 'SELECT id_penjualan, nomor_transaksi, tanggal, nama_pelanggan, total FROM penjualan WHERE id_penjualan = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$transaksi = mysqli_fetch_assoc($res);

if (!$transaksi) {
    header('Location: penjualan.php');
    exit;
}

$stmtdet = mysqli_prepare($koneksi, 'SELECT d.id_detail, d.harga, d.qty, d.subtotal, p.name AS nama_produk FROM penjualan_detail d LEFT JOIN products p ON d.id_product = p.id WHERE d.id_penjualan = ?');
mysqli_stmt_bind_param($stmtdet, 'i', $id);
mysqli_stmt_execute($stmtdet);
$items = mysqli_stmt_get_result($stmtdet);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sale details - darkfonder.net</title>
    <link rel="stylesheet" href="../../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../../partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../../partials/sidebaradmin.php'; ?>

        <div class="content">
            <div class="heading">sales transaction details</div>
            <div class="emptybox">
                <div class="sidetitle">transaction information</div>

                <table class="carttable" style="margin-bottom:18px">
                    <tr class="cartrow">
                        <td class="cartcell" style="width:180px"><strong>transaction #</strong></td>
                        <td class="cartcell"><?= htmlspecialchars($transaksi['nomor_transaksi']) ?></td>
                    </tr>
                    <tr class="cartrow">
                        <td class="cartcell"><strong>date</strong></td>
                        <td class="cartcell"><?= htmlspecialchars($transaksi['tanggal']) ?></td>
                    </tr>
                    <tr class="cartrow">
                        <td class="cartcell"><strong>customer name</strong></td>
                        <td class="cartcell"><?= htmlspecialchars($transaksi['nama_pelanggan']) ?></td>
                    </tr>
                    <tr class="cartrow">
                        <td class="cartcell"><strong>total amount</strong></td>
                        <td class="cartcell"><?= format_uang($transaksi['total']) ?></td>
                    </tr>
                </table>

                <div class="sidetitle">item list</div>
                <table class="carttable">
                    <tr class="cartheader">
                        <th class="cartcell">product</th>
                        <th class="cartcell">unit price</th>
                        <th class="cartcell">quantity</th>
                        <th class="cartcell">subtotal</th>
                    </tr>
                    <?php if ($items && mysqli_num_rows($items) > 0): ?>
                        <?php while ($item = mysqli_fetch_assoc($items)): ?>
                        <tr class="cartrow">
                            <td class="cartcell"><?= htmlspecialchars($item['nama_produk'] ?? 'unknown product') ?></td>
                            <td class="cartcell"><?= format_uang($item['harga']) ?></td>
                            <td class="cartcell"><?= (int)$item['qty'] ?></td>
                            <td class="cartcell"><?= format_uang($item['subtotal']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr class="cartrow">
                            <td class="cartcell" colspan="4">no item details recorded.</td>
                        </tr>
                    <?php endif; ?>
                </table>

                <div class="actionrow" style="margin-top:14px">
                    <a href="penjualan.php" class="backlink">back to sales list</a>
                </div>
            </div>
        </div>
    </div>

<script src="../../assets/main.js"></script>

</body>
</html>
