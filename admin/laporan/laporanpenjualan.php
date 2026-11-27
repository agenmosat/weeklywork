<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../home/includes/transaksihelper.php';

init_transaksi_tables($koneksi);

$tgl_mulai = $_GET['tgl_mulai'] ?? ($_GET['dari'] ?? date('Y-m-01'));
$tgl_akhir = $_GET['tgl_akhir'] ?? ($_GET['sampai'] ?? date('Y-m-d'));

if ($tgl_mulai > $tgl_akhir) {
    $temp = $tgl_mulai;
    $tgl_mulai = $tgl_akhir;
    $tgl_akhir = $temp;
}

$query = mysqli_query($koneksi, "SELECT * FROM sales WHERE DATE(tanggal) BETWEEN '$tgl_mulai' AND '$tgl_akhir' ORDER BY tanggal DESC");

$total_pendapatan = 0;
$total_transaksi = 0;
$daftar_penjualan = [];

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $daftar_penjualan[] = $row;
        if (($row['status'] ?? '') !== 'batal') {
            $total_pendapatan += (float)$row['total'];
            $total_transaksi++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sales report - darkfonder.net</title>
    <link rel="stylesheet" href="../../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../../home/partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../../home/partials/sidebaradmin.php'; ?>

        <div class="content">
            <div class="heading">sales summary report</div>

            <div class="emptybox">
                <div class="filterbox">
                    <form method="get" action="laporanpenjualan.php" class="filterform">
                        <label for="tglmulai" class="filterlabel">start date</label>
                        <input type="date" id="tglmulai" name="tgl_mulai" class="forminput dateinput" value="<?= htmlspecialchars($tgl_mulai) ?>">
                        <label for="tglakhir" class="filterlabel">end date</label>
                        <input type="date" id="tglakhir" name="tgl_akhir" class="forminput dateinput" value="<?= htmlspecialchars($tgl_akhir) ?>">
                        <button type="submit" class="enterbtn">filter</button>
                        <button type="button" class="printbtn" onclick="window.print()">print report</button>
                    </form>
                </div>

                <div class="cardgrid">
                    <div class="summarycard">
                        <div class="cardtitle">total revenue</div>
                        <div class="cardvalue"><?= format_uang($total_pendapatan) ?></div>
                    </div>
                    <div class="summarycard">
                        <div class="cardtitle">total transactions</div>
                        <div class="cardvalue"><?= $total_transaksi ?></div>
                    </div>
                </div>

                <table class="carttable">
                    <tr class="cartheader">
                        <th class="cartcell">id</th>
                        <th class="cartcell">transaction #</th>
                        <th class="cartcell">date</th>
                        <th class="cartcell">customer</th>
                        <th class="cartcell">total</th>
                        <th class="cartcell">status</th>
                    </tr>
                    <?php if (!empty($daftar_penjualan)): ?>
                        <?php foreach ($daftar_penjualan as $pj): ?>
                            <tr class="cartrow">
                                <td class="cartcell"><?= (int)$pj['id_penjualan'] ?></td>
                                <td class="cartcell"><?= htmlspecialchars($pj['nomor_transaksi']) ?></td>
                                <td class="cartcell"><?= htmlspecialchars($pj['tanggal']) ?></td>
                                <td class="cartcell"><?= htmlspecialchars($pj['nama_pelanggan']) ?></td>
                                <td class="cartcell"><?= format_uang($pj['total']) ?></td>
                                <td class="cartcell">
                                    <?php if (($pj['status'] ?? '') === 'batal'): ?>
                                        <span class="pillno">batal</span>
                                    <?php else: ?>
                                        <span class="pillok">selesai</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="cartrow">
                            <td class="cartcell" colspan="6">no sales transactions found for this period.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

<script src="../assets/main.js"></script>
</body>
</html>
