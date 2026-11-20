<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

$cari = trim($_GET['cari'] ?? '');
if ($cari !== '') {
    $stmt = mysqli_prepare($koneksi, 'SELECT * FROM products WHERE name LIKE CONCAT("%", ?, "%") OR category LIKE CONCAT("%", ?, "%") OR description LIKE CONCAT("%", ?, "%") ORDER BY id');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'sss', $cari, $cari, $cari);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($koneksi, 'SELECT * FROM products ORDER BY id');
    }
} else {
    $result = mysqli_query($koneksi, 'SELECT * FROM products ORDER BY id');
}

$status = $_GET['status'] ?? $_GET['pesan'] ?? '';
$pesan = [
    'tambah' => 'produk berhasil ditambahkan.',
    'edit' => 'produk berhasil diubah.',
    'hapus' => 'produk berhasil dihapus.',
    'error_relasi' => 'data produk tidak dapat dihapus karena sudah memiliki riwayat transaksi.',
    'gagal_relasi' => 'data produk tidak dapat dihapus karena sudah memiliki riwayat transaksi.'
][$status] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crud product</title>
    <link rel="stylesheet" href="../../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../../partials/topbar.php'; ?>

    <div class="layout">
        <div class="sidebar">
            <div class="sidetitle">admin menu</div>
            <a href="../admin.php" class="sidelink">dashboard</a>
            <a href="product.php" class="sidelink">product</a>
            <a href="../cashier/cashier.php" class="sidelink">cashier</a>
            <a href="../supplier/supplier.php" class="sidelink">supplier</a>
            <a href="../penjualan/penjualan.php" class="sidelink">sales</a>
            <a href="../pembelian/pembelian.php" class="sidelink">purchases</a>
            <a href="../checklist.php" class="sidelink">checklist</a>
        </div>

        <div class="content">
            <div class="heading">crud product</div>
            <div class="emptybox">
                <div class="sidetitle">product list</div>

                <?php if ($pesan !== ''): ?>
                    <div class="error"><?= htmlspecialchars($pesan) ?></div>
                <?php endif; ?>

                <div class="actionrow">
                    <form method="get" action="product.php" class="form" style="margin:0">
                        <input type="text" name="cari" class="forminput" placeholder="cari produk..." value="<?= htmlspecialchars($cari) ?>">
                        <button type="submit" class="enterbtn">cari</button>
                    </form>
                    <a href="addproduct.php" class="sidelink">+ add product</a>
                </div>

                <table class="carttable">
                    <tr class="cartheader">
                        <th class="cartcell">id</th>
                        <th class="cartcell">image</th>
                        <th class="cartcell">name</th>
                        <th class="cartcell">price</th>
                        <th class="cartcell">stock</th>
                        <th class="cartcell">category</th>
                        <th class="cartcell">action</th>
                    </tr>
                    <?php while ($p = mysqli_fetch_assoc($result)): ?>
                    <tr class="cartrow">
                        <td class="cartcell"><?= (int) $p['id'] ?></td>
                        <td class="cartcell">
                            <?php $imgpath = !empty($p['image']) ? $p['image'] : 'images/glock17.png'; ?>
                            <img src="../../<?= htmlspecialchars($imgpath) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width:40px;height:40px;object-fit:cover;">
                        </td>
                        <td class="cartcell"><?= htmlspecialchars($p['name']) ?></td>
                        <td class="cartcell"><?= htmlspecialchars(number_format((float) $p['price'], 2)) ?></td>
                        <td class="cartcell"><?= (int) $p['stock'] ?></td>
                        <td class="cartcell"><?= htmlspecialchars($p['category']) ?></td>
                        <td class="cartcell">
                            <div class="actionrow">
                                <a href="editproduct.php?id=<?= (int) $p['id'] ?>" class="sidelink">edit</a>
                                <form method="post" action="delproduct.php" style="display:inline" onsubmit="return confirmdelete();">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
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
