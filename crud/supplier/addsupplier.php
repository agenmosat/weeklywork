<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

$error = '';
$nama_supplier = '';
$kota = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_supplier = trim($_POST['nama_supplier'] ?? '');
    $kota = trim($_POST['kota'] ?? '');

    if ($nama_supplier === '') {
        $error = 'nama supplier wajib diisi.';
    } elseif (mb_strlen($nama_supplier) > 50) {
        $error = 'nama supplier maksimal 50 karakter.';
    } elseif (mb_strlen($kota) > 30) {
        $error = 'kota maksimal 30 karakter.';
    } else {
        $stmt = mysqli_prepare($koneksi, 'INSERT INTO supplier (nama_supplier, kota) VALUES (?, ?)');
        mysqli_stmt_bind_param($stmt, 'ss', $nama_supplier, $kota);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: supplier.php?status=tambah');
            exit;
        }
        $error = 'gagal menyimpan supplier.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crud supplier - add</title>
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
            <div class="heading">crud supplier - add</div>
            <div class="emptybox formbox">
                <div class="sidetitle">form tambah supplier</div>

                <?php if ($error !== ''): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="addsupplier.php" class="form">
                    <div class="formrow">
                        <label class="formlabel" for="nama_supplier">nama supplier</label>
                        <input class="forminput" type="text" id="nama_supplier" name="nama_supplier" maxlength="50" value="<?= htmlspecialchars($nama_supplier) ?>" required>
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="kota">kota</label>
                        <input class="forminput" type="text" id="kota" name="kota" maxlength="30" value="<?= htmlspecialchars($kota) ?>">
                    </div>
                    <div class="btnwrap">
                        <a href="supplier.php" class="sidelink">batal</a>
                        <button type="submit" class="enterbtn">simpan</button>
                    </div>
                </form>
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
