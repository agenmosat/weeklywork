<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

$cari = trim($_GET['cari'] ?? '');
if ($cari !== '') {
    $stmt = mysqli_prepare($koneksi, 'SELECT id_kasir, nama_kasir, status, tanggal_keluar FROM kasir WHERE nama_kasir LIKE CONCAT("%", ?, "%") ORDER BY id_kasir');
    mysqli_stmt_bind_param($stmt, 's', $cari);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($koneksi, 'SELECT id_kasir, nama_kasir, status, tanggal_keluar FROM kasir ORDER BY id_kasir');
}

$pesan = ['tambah' => 'kasir berhasil ditambahkan.', 'edit' => 'kasir berhasil diubah.', 'hapus' => 'kasir berhasil dihapus.'][$_GET['status'] ?? ''] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crud cashier</title>
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
            <a href="../supplier/supplier.php" class="sidelink">supplier</a>
            <a href="cashier.php" class="sidelink">cashier</a>
        </div>

        <div class="content">
            <div class="heading">crud cashier</div>
            <div class="emptybox">
                <div class="sidetitle">kasir list</div>

                <?php if ($pesan !== ''): ?>
                    <div class="error"><?= htmlspecialchars($pesan) ?></div>
                <?php endif; ?>

                <div class="actionrow">
                    <form method="get" action="cashier.php" class="form" style="margin:0">
                        <input type="text" name="cari" class="forminput" placeholder="cari kasir..." value="<?= htmlspecialchars($cari) ?>">
                        <button type="submit" class="enterbtn">cari</button>
                    </form>
                    <a href="newcashier.php" class="sidelink">+ add cashier</a>
                </div>

                <table class="carttable">
                    <tr class="cartheader">
                        <th class="cartcell">id</th>
                        <th class="cartcell">nama</th>
                        <th class="cartcell">status</th>
                        <th class="cartcell">tgl keluar</th>
                        <th class="cartcell">action</th>
                    </tr>
                    <?php while ($k = mysqli_fetch_assoc($result)): ?>
                    <tr class="cartrow">
                        <td class="cartcell"><?= (int) $k['id_kasir'] ?></td>
                        <td class="cartcell"><?= htmlspecialchars($k['nama_kasir']) ?></td>
                        <td class="cartcell"><?= htmlspecialchars($k['status']) ?></td>
                        <td class="cartcell"><?= htmlspecialchars($k['tanggal_keluar'] ?? '-') ?></td>
                        <td class="cartcell">
                            <div class="actionrow">
                                <a href="editcashier.php?id_kasir=<?= (int) $k['id_kasir'] ?>" class="sidelink">edit</a>
                                <form method="post" action="offcashier.php" style="display:inline">
                                    <input type="hidden" name="id_kasir" value="<?= (int) $k['id_kasir'] ?>">
                                    <button type="submit" class="enterbtn">off</button>
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
    var sel = document.getElementById("themepick");
    if (sel) {
        sel.value = savedtheme;
    }
}
</script>

</body>
</html>
