<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

$cari = trim($_GET['cari'] ?? '');
if ($cari !== '') {
    $stmt = mysqli_prepare($koneksi, 'SELECT id, name, price, stock, category, description FROM products WHERE name LIKE CONCAT("%", ?, "%") OR category LIKE CONCAT("%", ?, "%") OR description LIKE CONCAT("%", ?, "%") ORDER BY id');
    mysqli_stmt_bind_param($stmt, 'sss', $cari, $cari, $cari);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($koneksi, 'SELECT id, name, price, stock, category, description FROM products ORDER BY id');
}

$pesan = ['tambah' => 'produk berhasil ditambahkan.', 'edit' => 'produk berhasil diubah.', 'hapus' => 'produk berhasil dihapus.'][$_GET['status'] ?? ''] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crud product</title>
    <link rel="stylesheet" href="../../style.css?v=3">
</head>
<body class="marketpage themelight">

    <div class="topbar">
        <div class="brand">darkfonder.net</div>
        <div class="themeswitcher">
            <span class="themelabel">theme</span>
            <select id="themepick" class="themeselect" onchange="switchtheme(this.value)">
                <option value="themelight">light</option>
                <option value="themedark">dark</option>
            </select>
        </div>

        <div class="topnav">
            <a href="../../dashboard.php" class="navlink">store</a>
            <a href="../../admin.php" class="navlink">admin</a>
            <a href="../../admin.php?logout=1" class="navlink">logout</a>
        </div>
    </div>

    <div class="layout">
        <div class="sidebar">
            <div class="sidetitle">admin menu</div>
            <a href="../../admin.php" class="sidelink">dashboard</a>
            <a href="product.php" class="sidelink">product</a>
            <a href="../cashier/cashier.php" class="sidelink">cashier</a>
            <a href="../supplier/supplier.php" class="sidelink">supplier</a>
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
                        <th class="cartcell">name</th>
                        <th class="cartcell">price</th>
                        <th class="cartcell">stock</th>
                        <th class="cartcell">category</th>
                        <th class="cartcell">action</th>
                    </tr>
                    <?php while ($p = mysqli_fetch_assoc($result)): ?>
                    <tr class="cartrow">
                        <td class="cartcell"><?= (int) $p['id'] ?></td>
                        <td class="cartcell"><?= htmlspecialchars($p['name']) ?></td>
                        <td class="cartcell"><?= htmlspecialchars(number_format((float) $p['price'], 2)) ?></td>
                        <td class="cartcell"><?= (int) $p['stock'] ?></td>
                        <td class="cartcell"><?= htmlspecialchars($p['category']) ?></td>
                        <td class="cartcell">
                            <div class="actionrow">
                                <a href="editproduct.php?id=<?= (int) $p['id'] ?>" class="sidelink">edit</a>
                                <form method="post" action="delproduct.php" style="display:inline">
                                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                    <button type="submit" class="enterbtn">del</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>

                <div class="backlink"><a href="../../admin.php" class="sidelink">back to admin</a></div>
            </div>
        </div>
    </div>

<script>
function switchtheme(theme) {
    document.body.className = "marketpage " + theme;
    localStorage.setItem("theme", theme);
}
var savedtheme = localStorage.getItem("theme");
if (savedtheme) {
    document.body.className = "marketpage " + savedtheme;
}
</script>

</body>
</html>
