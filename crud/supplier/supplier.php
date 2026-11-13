<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

$result = mysqli_query($koneksi, 'SELECT id_supplier, nama_supplier, kota FROM supplier ORDER BY id_supplier');

$pesan = ['tambah' => 'supplier berhasil ditambahkan.', 'edit' => 'supplier berhasil diubah.', 'hapus' => 'supplier berhasil dihapus.'][$_GET['status'] ?? ''] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crud supplier</title>
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
            <a href="../product/product.php" class="sidelink">product</a>
            <a href="../cashier/cashier.php" class="sidelink">cashier</a>
            <a href="supplier.php" class="sidelink">supplier</a>
        </div>

        <div class="content">
            <div class="heading">crud supplier</div>
            <div class="emptybox">
                <div class="sidetitle">supplier list</div>

                <?php if ($pesan !== ''): ?>
                    <div class="error"><?= htmlspecialchars($pesan) ?></div>
                <?php endif; ?>

                <div class="actionrow">
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
                                <a href="delsupplier.php?id_supplier=<?= (int) $s['id_supplier'] ?>" class="sidelink">del</a>
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
    var sel = document.getElementById("themepick");
    if (sel) {
        sel.value = savedtheme;
    }
}
</script>

</body>
</html>
