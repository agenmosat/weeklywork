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

$query = mysqli_query($koneksi, "SELECT p.*, s.nama_supplier, s.kota FROM purchases p LEFT JOIN suppliers s ON p.id_supplier = s.id_supplier WHERE DATE(p.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_akhir' ORDER BY p.tanggal DESC");

$total_pengeluaran = 0;
$total_transaksi = 0;
$daftar_pembelian = [];
$rekap_supplier = [];

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $daftar_pembelian[] = $row;
        if (($row['status'] ?? '') !== 'batal') {
            $total_pengeluaran += (float)$row['total'];
            $total_transaksi++;

            $sid = (int)$row['id_supplier'];
            if (!isset($rekap_supplier[$sid])) {
                $rekap_supplier[$sid] = [
                    'id_supplier' => $sid,
                    'nama_supplier' => $row['nama_supplier'] ?? 'Unknown',
                    'kota' => $row['kota'] ?? '-',
                    'total_transaksi' => 0,
                    'subtotal' => 0
                ];
            }
            $rekap_supplier[$sid]['total_transaksi']++;
            $rekap_supplier[$sid]['subtotal'] += (float)$row['total'];
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
    <link rel="stylesheet" href="../../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../../home/partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../../home/partials/sidebaradmin.php'; ?>

        <div class="content">
            <div class="heading">purchase summary report</div>

            <div class="emptybox">
                <div class="filterbox">
                    <form method="get" action="laporanpembelian.php" class="filterform">
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
                                <td class="cartcell"><?= htmlspecialchars($pb['nama_supplier'] ?? 'Unknown') ?></td>
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
