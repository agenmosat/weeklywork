<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

$cari = trim($_GET['cari'] ?? '');
if ($cari !== '') {
    $stmt = mysqli_prepare($koneksi, 'SELECT id_supplier, nama_supplier, kota FROM supplier WHERE nama_supplier LIKE CONCAT("%", ?, "%") OR kota LIKE CONCAT("%", ?, "%") ORDER BY id_supplier');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $cari, $cari);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($koneksi, 'SELECT id_supplier, nama_supplier, kota FROM supplier ORDER BY id_supplier');
    }
} else {
    $result = mysqli_query($koneksi, 'SELECT id_supplier, nama_supplier, kota FROM supplier ORDER BY id_supplier');
}

$status = $_GET['status'] ?? $_GET['pesan'] ?? '';
$pesan = [
    'tambah' => 'supplier berhasil ditambahkan.',
    'edit' => 'supplier berhasil diubah.',
    'hapus' => 'supplier berhasil dihapus.',
    'error_relasi' => 'data supplier tidak dapat dihapus karena sudah memiliki riwayat transaksi.',
    'gagal_relasi' => 'data supplier tidak dapat dihapus karena sudah memiliki riwayat transaksi.'
][$status] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crud supplier</title>
    <link rel="stylesheet" href="../../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../../home/partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../../home/partials/sidebaradmin.php'; ?>

        <div class="content">
            <div class="heading">crud supplier</div>
            <div class="emptybox">
                <div class="sidetitle">supplier list</div>

                <?php if ($pesan !== ''): ?>
                    <div class="error"><?= htmlspecialchars($pesan) ?></div>
                <?php endif; ?>

                <div class="actionrow">
                    <form method="get" action="supplier.php" class="form" style="margin:0">
                        <input type="text" name="cari" class="forminput" placeholder="cari supplier..." value="<?= htmlspecialchars($cari) ?>">
                        <button type="submit" class="enterbtn">cari</button>
                    </form>
                    <a href="addsupplier.php" class="sidelink">+ add supplier</a>
                </div>

                <table class="carttable">
                    <tr class="cartheader">
                        <th class="cartcell">id</th>
                        <th class="cartcell">nama</th>
                        <th class="cartcell">kota</th>
                        <th class="cartcell">action</th>
                    </tr>
                    <?php while ($s = mysqli_fetch_assoc($result)): ?>
                    <tr class="cartrow">
                        <td class="cartcell"><?= (int) $s['id_supplier'] ?></td>
                        <td class="cartcell"><?= htmlspecialchars($s['nama_supplier']) ?></td>
                        <td class="cartcell"><?= htmlspecialchars($s['kota']) ?></td>
                        <td class="cartcell">
                            <div class="actionrow">
                                <a href="editsupplier.php?id_supplier=<?= (int) $s['id_supplier'] ?>" class="sidelink">edit</a>
                                <form method="post" action="delsupplier.php" style="display:inline" onsubmit="return confirmdelete();">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id_supplier" value="<?= (int) $s['id_supplier'] ?>">
                                    <button type="submit" class="enterbtn">del</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>

                <div class="backlink"><a href="../admin.php" class="sidelink">back to admin</a></div>
            </div>
        </div>
    </div>

<script src="../../assets/main.js"></script>

</body>
</html>
