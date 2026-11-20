<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

$id_kasir = (int) ($_GET['id_kasir'] ?? 0);
$error = '';
$nama_kasir = '';
$status = '';
$tanggal_keluar = null;

if ($id_kasir <= 0) {
    header('Location: cashier.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id_kasir = (int) ($_POST['id_kasir'] ?? 0);
    $nama_kasir = trim($_POST['nama_kasir'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $tanggal_keluar = trim($_POST['tanggal_keluar'] ?? '');
    if ($tanggal_keluar === '') {
        $tanggal_keluar = null;
    }

    if ($id_kasir <= 0 || $nama_kasir === '') {
        $error = 'nama kasir wajib diisi.';
    } elseif (mb_strlen($nama_kasir) > 50) {
        $error = 'nama kasir maksimal 50 karakter.';
    } elseif (!in_array($status, ['aktif', 'nonaktif'], true)) {
        $error = 'status harus aktif atau nonaktif.';
    } else {
        $stmt = mysqli_prepare($koneksi, 'UPDATE kasir SET nama_kasir = ?, status = ?, tanggal_keluar = ? WHERE id_kasir = ?');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssi', $nama_kasir, $status, $tanggal_keluar, $id_kasir);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: cashier.php?status=edit');
                exit;
            }
        }
        $error = 'gagal mengubah kasir.';
    }
} else {
    $stmt = mysqli_prepare($koneksi, 'SELECT id_kasir, nama_kasir, status, tanggal_keluar FROM kasir WHERE id_kasir = ?');
    $row = null;
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id_kasir);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) {
            $row = mysqli_fetch_assoc($res);
        }
    }
    if (!$row) {
        header('Location: cashier.php');
        exit;
    }
    $nama_kasir = $row['nama_kasir'];
    $status = $row['status'];
    $tanggal_keluar = $row['tanggal_keluar'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crud cashier - edit</title>
    <link rel="stylesheet" href="../../assets/style.css?v=3">
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
            <a href="../../home/dashboard.php" class="navlink">store</a>
            <a href="../admin.php" class="navlink">admin</a>
            <form method="post" action="../../logout.php" class="logoutform">
                <button type="submit" class="logoutbtn">logout</button>
            </form>
        </div>
    </div>

    <div class="layout">
        <div class="sidebar">
            <div class="sidetitle">admin menu</div>
            <a href="../admin.php" class="sidelink">dashboard</a>
            <a href="../product/product.php" class="sidelink">product</a>
            <a href="cashier.php" class="sidelink">cashier</a>
            <a href="../supplier/supplier.php" class="sidelink">supplier</a>
        </div>

        <div class="content">
            <div class="heading">crud cashier - edit #<?= (int) $id_kasir ?></div>
            <div class="emptybox formbox">
                <div class="sidetitle">form edit kasir</div>

                <?php if ($error !== ''): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="editcashier.php?id_kasir=<?= (int) $id_kasir ?>" class="form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_kasir" value="<?= (int) $id_kasir ?>">
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
                    <div class="formrow">
                        <label class="formlabel" for="tanggal_keluar">tgl keluar (opsional)</label>
                        <input class="forminput" type="date" id="tanggal_keluar" name="tanggal_keluar" value="<?= htmlspecialchars($tanggal_keluar ?? '') ?>">
                    </div>
                    <div class="btnwrap">
                        <a href="cashier.php" class="sidelink">batal</a>
                        <button type="submit" class="enterbtn">simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="../../assets/main.js"></script>

</body>
</html>
