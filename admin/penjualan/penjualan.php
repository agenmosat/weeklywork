<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../home/includes/csrf.php';
require_once __DIR__ . '/../../home/includes/transaksihelper.php';

init_transaksi_tables($koneksi);

$cari = trim($_GET['cari'] ?? '');
$chk_st = mysqli_query($koneksi, "SHOW COLUMNS FROM sales LIKE 'status'");
$cols = ($chk_st && mysqli_num_rows($chk_st) > 0)
    ? 'id_penjualan, nomor_transaksi, tanggal, nama_pelanggan, total, status'
    : 'id_penjualan, nomor_transaksi, tanggal, nama_pelanggan, total';

if ($cari !== '') {
    $searchterm = "%" . $cari . "%";
    $result = mysqli_execute_query($koneksi, "SELECT $cols FROM sales WHERE nomor_transaksi LIKE ? OR nama_pelanggan LIKE ? ORDER BY id_penjualan DESC", [$searchterm, $searchterm]);
} else {
    $result = mysqli_query($koneksi, "SELECT $cols FROM sales ORDER BY id_penjualan DESC");
}

$pesan = [
    'tambah' => 'sales transaction saved successfully.',
    'batal' => 'sales transaction cancelled successfully.',
    'gagal' => 'failed to cancel sales transaction.'
][$_GET['status'] ?? ''] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sales - darkfonder.net</title>
    <link rel="stylesheet" href="../../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../../home/partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../../home/partials/sidebaradmin.php'; ?>

        <div class="content">
            <div class="heading">sales transactions</div>
            <div class="emptybox">
                <div class="sidetitle">sales list</div>

                <?php if ($pesan !== ''): ?>
                    <div class="error"><?= htmlspecialchars($pesan) ?></div>
                <?php endif; ?>

                <div class="actionrow">
                    <form method="get" action="penjualan.php" class="form" style="margin:0">
                        <input type="text" name="cari" class="forminput" placeholder="search transactions..." value="<?= htmlspecialchars($cari) ?>">
                        <button type="submit" class="enterbtn">search</button>
                    </form>
                    <a href="penjualantambah.php" class="sidelink">+ add sale</a>
                </div>

                <table class="carttable">
                    <tr class="cartheader">
                        <th class="cartcell">id</th>
                        <th class="cartcell">transaction #</th>
                        <th class="cartcell">date</th>
                        <th class="cartcell">customer</th>
                        <th class="cartcell">total</th>
                        <th class="cartcell">status</th>
                        <th class="cartcell">action</th>
                    </tr>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="cartrow">
                            <td class="cartcell"><?= (int) $row['id_penjualan'] ?></td>
                            <td class="cartcell"><?= htmlspecialchars($row['nomor_transaksi']) ?></td>
                            <td class="cartcell"><?= htmlspecialchars($row['tanggal']) ?></td>
                            <td class="cartcell"><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                            <td class="cartcell"><?= format_uang($row['total']) ?></td>
                            <td class="cartcell">
                                <?php if (($row['status'] ?? '') === 'batal'): ?>
                                    <span class="pillno">batal</span>
                                <?php else: ?>
                                    <span class="pillok">selesai</span>
                                <?php endif; ?>
                            </td>
                            <td class="cartcell">
                                <div class="actionrow">
                                    <a href="penjualandetail.php?id=<?= (int) $row['id_penjualan'] ?>" class="cartbtn">detail</a>
                                    <?php if (($row['status'] ?? '') !== 'batal'): ?>
                                        <form method="post" action="penjualanbatal.php" style="display:inline" onsubmit="return confirm('cancel this sale?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $row['id_penjualan'] ?>">
                                            <button type="submit" class="enterbtn btndel">cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr class="cartrow">
                            <td class="cartcell" colspan="7">no sales transactions found.</td>
                        </tr>
                    <?php endif; ?>
                </table>

                <?php if ($cari !== ''): ?>
                    <div class="actionrow" style="margin-top:12px">
                        <a href="penjualan.php" class="backlink">view all transactions</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<script src="../../assets/main.js"></script>

</body>
</html>
