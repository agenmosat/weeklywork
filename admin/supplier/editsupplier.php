<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../home/includes/transaksihelper.php';

init_transaksi_tables($koneksi);

$id_supplier = (int) ($_GET['id_supplier'] ?? 0);
$error = '';
$nama_supplier = '';
$kota = '';

if ($id_supplier <= 0) {
    header('Location: supplier.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id_supplier = (int) ($_POST['id_supplier'] ?? 0);
    $nama_supplier = trim($_POST['nama_supplier'] ?? '');
    $kota = trim($_POST['kota'] ?? '');

    if ($id_supplier <= 0 || $nama_supplier === '') {
        $error = 'nama supplier wajib diisi.';
    } elseif (mb_strlen($nama_supplier) > 50) {
        $error = 'nama supplier maksimal 50 karakter.';
    } elseif (mb_strlen($kota) > 30) {
        $error = 'kota maksimal 30 karakter.';
    } else {
        $stmt = mysqli_prepare($koneksi, 'UPDATE suppliers SET nama_supplier = ?, kota = ? WHERE id_supplier = ?');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssi', $nama_supplier, $kota, $id_supplier);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: supplier.php?status=edit');
                exit;
            }
        }
        $error = 'gagal mengubah supplier.';
    }
} else {
    $stmt = mysqli_prepare($koneksi, 'SELECT id_supplier, nama_supplier, kota FROM suppliers WHERE id_supplier = ?');
    $row = null;
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id_supplier);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) {
            $row = mysqli_fetch_assoc($res);
        }
    }
    if (!$row) {
        header('Location: supplier.php');
        exit;
    }
    $nama_supplier = $row['nama_supplier'];
    $kota = $row['kota'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crud supplier - edit</title>
    <link rel="stylesheet" href="../../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../../home/partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../../home/partials/sidebaradmin.php'; ?>

        <div class="content">
            <div class="heading">crud supplier - edit #<?= (int) $id_supplier ?></div>
            <div class="emptybox formbox">
                <div class="sidetitle">form edit supplier</div>

                <?php if ($error !== ''): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="editsupplier.php?id_supplier=<?= (int) $id_supplier ?>" class="form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_supplier" value="<?= (int) $id_supplier ?>">
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

<script src="../../assets/main.js"></script>

</body>
</html>
