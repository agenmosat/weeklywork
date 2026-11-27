<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

$cari = trim($_GET['cari'] ?? '');
if ($cari !== '') {
    $result = mysqli_execute_query($koneksi, 'SELECT * FROM products WHERE name LIKE CONCAT("%", ?, "%") OR category LIKE CONCAT("%", ?, "%") OR description LIKE CONCAT("%", ?, "%") ORDER BY id', [$cari, $cari, $cari]);
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

    <?php require __DIR__ . '/../../home/partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../../home/partials/sidebaradmin.php'; ?>

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
                        <th class="cartcell">unit</th>
                        <th class="cartcell">location</th>
                        <th class="cartcell">rating</th>
                        <th class="cartcell">sold</th>
                        <th class="cartcell">category</th>
                        <th class="cartcell">action</th>
                    </tr>
                    <?php while ($p = mysqli_fetch_assoc($result)): ?>
                    <tr class="cartrow">
                        <td class="cartcell"><?= (int) $p['id'] ?></td>
                        <td class="cartcell">
                            <?php
                            $imgpath = !empty($p['image']) ? $p['image'] : 'images/no-image.webp';
                            $imgsrc = preg_match('#^https?://#', $imgpath) ? $imgpath : '../../home/' . $imgpath;
                            ?>
                            <img src="<?= htmlspecialchars($imgsrc) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy" onerror="this.src='../../home/images/no-image.webp'" style="width:40px;height:40px;object-fit:cover;">
                        </td>
                        <td class="cartcell"><?= htmlspecialchars($p['name']) ?></td>
                        <td class="cartcell"><?= htmlspecialchars(number_format((float) $p['price'], 2)) ?></td>
                        <td class="cartcell"><?= (int) $p['stock'] ?></td>
                        <td class="cartcell"><?= htmlspecialchars($p['unit'] ?? 'buah') ?></td>
                        <td class="cartcell"><?= htmlspecialchars($p['lokasi'] ?? 'Surabaya') ?></td>
                        <td class="cartcell">&#9733; <?= htmlspecialchars($p['rating'] ?? '4.9') ?></td>
                        <td class="cartcell"><?= (int) ($p['terjual'] ?? 0) ?></td>
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
