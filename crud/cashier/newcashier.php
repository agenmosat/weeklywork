<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

$error = '';
$nama_kasir = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kasir = trim($_POST['nama_kasir'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if ($nama_kasir === '') {
        $error = 'nama kasir wajib diisi.';
    } elseif (mb_strlen($nama_kasir) > 50) {
        $error = 'nama kasir maksimal 50 karakter.';
    } elseif (!in_array($status, ['aktif', 'nonaktif'], true)) {
        $error = 'status harus aktif atau nonaktif.';
    } else {
        $stmt = mysqli_prepare($koneksi, 'INSERT INTO kasir (nama_kasir, status) VALUES (?, ?)');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ss', $nama_kasir, $status);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: cashier.php?status=tambah');
                exit;
            }
        }
        $error = 'gagal menyimpan kasir.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crud cashier - add</title>
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
            <a href="cashier.php" class="sidelink">cashier</a>
            <a href="../supplier/supplier.php" class="sidelink">supplier</a>
        </div>

        <div class="content">
            <div class="heading">crud cashier - add</div>
            <div class="emptybox formbox">
                <div class="sidetitle">form tambah kasir</div>

                <?php if ($error !== ''): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="newcashier.php" class="form">
                    <div class="formrow">
                        <label class="formlabel" for="nama_kasir">nama kasir</label>
                        <input class="forminput" type="text" id="nama_kasir" name="nama_kasir" maxlength="50" value="<?= htmlspecialchars($nama_kasir) ?>" required>
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="status">status</label>
                        <select class="forminput" id="status" name="status">
                            <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>aktif</option>
                            <option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>nonaktif</option>
                        </select>
                    </div>
                    <div class="btnwrap">
                        <a href="cashier.php" class="sidelink">batal</a>
                        <button type="submit" class="enterbtn">simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="../../main.js"></script>

</body>
</html>
