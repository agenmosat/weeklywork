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

$total_pengeluaran = 0.0;
$total_transaksi = 0;

$stmt_agg = mysqli_prepare($koneksi, "SELECT COUNT(id_pembelian) AS total_transaksi, COALESCE(SUM(total), 0) AS total_pengeluaran FROM pembelian WHERE tanggal BETWEEN ? AND ?");
if ($stmt_agg) {
    mysqli_stmt_bind_param($stmt_agg, "ss", $datetime_mulai, $datetime_akhir);
    mysqli_stmt_execute($stmt_agg);
    $res_agg = mysqli_stmt_get_result($stmt_agg);
    if ($res_agg && ($agg = mysqli_fetch_assoc($res_agg))) {
        $total_transaksi = (int)$agg['total_transaksi'];
        $total_pengeluaran = (float)$agg['total_pengeluaran'];
    }
}

$rekap_supplier = [];
$stmt_supp = mysqli_prepare($koneksi, "SELECT p.id_supplier, COALESCE(s.nama_supplier, 'unknown') AS nama_supplier, s.kota, COUNT(p.id_pembelian) AS total_transaksi, COALESCE(SUM(p.total), 0) AS subtotal FROM pembelian p LEFT JOIN supplier s ON p.id_supplier = s.id_supplier WHERE p.tanggal BETWEEN ? AND ? GROUP BY p.id_supplier, s.nama_supplier, s.kota ORDER BY subtotal DESC");
if ($stmt_supp) {
    mysqli_stmt_bind_param($stmt_supp, "ss", $datetime_mulai, $datetime_akhir);
    mysqli_stmt_execute($stmt_supp);
    $res_supp = mysqli_stmt_get_result($stmt_supp);
    if ($res_supp) {
        while ($row = mysqli_fetch_assoc($res_supp)) {
            $rekap_supplier[] = $row;
        }
    }
}

$daftar_pembelian = [];
$stmt_list = mysqli_prepare($koneksi, "SELECT p.id_pembelian, p.nomor_transaksi, p.tanggal, COALESCE(s.nama_supplier, 'unknown') AS nama_supplier, p.total, p.status FROM pembelian p LEFT JOIN supplier s ON p.id_supplier = s.id_supplier WHERE p.tanggal BETWEEN ? AND ? ORDER BY p.tanggal DESC, p.id_pembelian DESC");
if ($stmt_list) {
    mysqli_stmt_bind_param($stmt_list, "ss", $datetime_mulai, $datetime_akhir);
    mysqli_stmt_execute($stmt_list);
    $res_list = mysqli_stmt_get_result($stmt_list);
    if ($res_list) {
        while ($row = mysqli_fetch_assoc($res_list)) {
            $daftar_pembelian[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>purchase report - darkfonder.net</title>
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
            <div class="heading">purchase summary report</div>

            <div class="emptybox">
                <div class="filterbox">
                    <form method="get" action="laporan_pembelian.php" class="filterform">
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
                        <div class="cardtitle">total expenditure</div>
                        <div class="cardvalue"><?= format_uang($total_pengeluaran) ?></div>
                    </div>
                    <div class="summarycard">
                        <div class="cardtitle">total transactions</div>
                        <div class="cardvalue"><?= $total_transaksi ?></div>
                    </div>
                </div>

                <div class="sidetitle" style="margin-top:14px;margin-bottom:8px">supplier expenditure breakdown</div>
                <table class="carttable">
                    <tr class="cartheader">
                        <th class="cartcell">supplier id</th>
                        <th class="cartcell">supplier name</th>
                        <th class="cartcell">city</th>
                        <th class="cartcell">transactions</th>
                        <th class="cartcell">expenditure subtotal</th>
                    </tr>
                    <?php if (!empty($rekap_supplier)): ?>
                        <?php foreach ($rekap_supplier as $rs): ?>
                            <tr class="cartrow">
                                <td class="cartcell"><?= (int)$rs['id_supplier'] ?></td>
                                <td class="cartcell"><?= htmlspecialchars($rs['nama_supplier']) ?></td>
                                <td class="cartcell"><?= htmlspecialchars($rs['kota'] ?? '-') ?></td>
                                <td class="cartcell"><?= (int)$rs['total_transaksi'] ?></td>
                                <td class="cartcell"><?= format_uang($rs['subtotal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="cartrow">
                            <td class="cartcell" colspan="5">no supplier expenditure records found for this period.</td>
                        </tr>
                    <?php endif; ?>
                </table>

                <div class="sidetitle" style="margin-top:20px;margin-bottom:8px">purchase transactions detail</div>
                <table class="carttable">
                    <tr class="cartheader">
                        <th class="cartcell">id</th>
                        <th class="cartcell">transaction #</th>
                        <th class="cartcell">date</th>
                        <th class="cartcell">supplier</th>
                        <th class="cartcell">total</th>
                        <th class="cartcell">status</th>
                    </tr>
                    <?php if (!empty($daftar_pembelian)): ?>
                        <?php foreach ($daftar_pembelian as $pb): ?>
                            <tr class="cartrow">
                                <td class="cartcell"><?= (int)$pb['id_pembelian'] ?></td>
                                <td class="cartcell"><?= htmlspecialchars($pb['nomor_transaksi']) ?></td>
                                <td class="cartcell"><?= htmlspecialchars($pb['tanggal']) ?></td>
                                <td class="cartcell"><?= htmlspecialchars($pb['nama_supplier']) ?></td>
                                <td class="cartcell"><?= format_uang($pb['total']) ?></td>
                                <td class="cartcell">
                                    <?php if (($pb['status'] ?? '') === 'batal'): ?>
                                        <span class="pillno">batal</span>
                                    <?php else: ?>
                                        <span class="pillok">selesai</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="cartrow">
                            <td class="cartcell" colspan="6">no purchase transactions found for this period.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

<script src="../assets/main.js"></script>
</body>
</html>
