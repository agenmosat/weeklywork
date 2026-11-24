<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../home/includes/csrf.php';
require_once __DIR__ . '/../../home/includes/transaksihelper.php';

init_transaksi_tables($koneksi);

$cari = trim($_GET['cari'] ?? '');
$chk_st = mysqli_query($koneksi, "SHOW COLUMNS FROM pembelian LIKE 'status'");
$cols = ($chk_st && mysqli_num_rows($chk_st) > 0)
    ? 'p.id_pembelian, p.nomor_transaksi, p.tanggal, p.total, p.status, s.nama_supplier'
    : 'p.id_pembelian, p.nomor_transaksi, p.tanggal, p.total, s.nama_supplier';

if ($cari !== '') {
    $stmt = mysqli_prepare($koneksi, "SELECT $cols FROM pembelian p LEFT JOIN supplier s ON p.id_supplier = s.id_supplier WHERE p.nomor_transaksi LIKE ? OR s.nama_supplier LIKE ? ORDER BY p.id_pembelian DESC");
    $searchterm = "%" . $cari . "%";
    mysqli_stmt_bind_param($stmt, 'ss', $searchterm, $searchterm);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($koneksi, "SELECT $cols FROM pembelian p LEFT JOIN supplier s ON p.id_supplier = s.id_supplier ORDER BY p.id_pembelian DESC");
}

$pesan = [
    'tambah' => 'purchase transaction saved successfully.',
    'batal' => 'purchase transaction cancelled successfully.',
    'error_stok_kurang' => 'cannot cancel purchase: current stock is insufficient.',
    'gagal' => 'failed to cancel purchase transaction.'
][$_GET['status'] ?? ''] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>purchases - darkfonder.net</title>
    <link rel="stylesheet" href="../../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../../home/partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../../home/partials/sidebaradmin.php'; ?>

        <div class="content">
            <div class="heading">purchase transactions</div>
            <div class="emptybox">
                <div class="sidetitle">purchase list</div>

                <?php if ($pesan !== ''): ?>
                    <div class="error"><?= htmlspecialchars($pesan) ?></div>
                <?php endif; ?>

                <div class="actionrow">
                    <form method="get" action="pembelian.php" class="form" style="margin:0">
                        <input type="text" name="cari" class="forminput" placeholder="search transactions..." value="<?= htmlspecialchars($cari) ?>">
                        <button type="submit" class="enterbtn">search</button>
                    </form>
                    <a href="pembeliantambah.php" class="sidelink">+ add purchase</a>
                </div>

                <table class="carttable">
                    <tr class="cartheader">
                        <th class="cartcell">id</th>
                        <th class="cartcell">transaction #</th>
                        <th class="cartcell">date</th>
                        <th class="cartcell">supplier</th>
                        <th class="cartcell">total</th>
                        <th class="cartcell">status</th>
                        <th class="cartcell">action</th>
                    </tr>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="cartrow">
                            <td class="cartcell"><?= (int) $row['id_pembelian'] ?></td>
                            <td class="cartcell"><?= htmlspecialchars($row['nomor_transaksi']) ?></td>
                            <td class="cartcell"><?= htmlspecialchars($row['tanggal']) ?></td>
                            <td class="cartcell"><?= htmlspecialchars($row['nama_supplier'] ?? 'unknown') ?></td>
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
                                    <a href="pembeliandetail.php?id=<?= (int) $row['id_pembelian'] ?>" class="cartbtn">detail</a>
                                    <?php if (($row['status'] ?? '') !== 'batal'): ?>
                                        <form method="post" action="pembelianbatal.php" style="display:inline" onsubmit="return confirm('cancel this purchase?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $row['id_pembelian'] ?>">
                                            <button type="submit" class="enterbtn btndel">cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr class="cartrow">
                            <td class="cartcell" colspan="7">no purchase transactions found.</td>
                        </tr>
                    <?php endif; ?>
                </table>

                <?php if ($cari !== ''): ?>
                    <div class="actionrow" style="margin-top:12px">
                        <a href="pembelian.php" class="backlink">view all transactions</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<script src="../../assets/main.js"></script>

</body>
</html>
