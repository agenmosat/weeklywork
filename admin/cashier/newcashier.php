<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../home/includes/transaksihelper.php';

init_transaksi_tables($koneksi);

$error = '';
$nama_kasir = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $nama_kasir = trim($_POST['nama_kasir'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if ($nama_kasir === '') {
        $error = 'nama kasir wajib diisi.';
    } elseif (mb_strlen($nama_kasir) > 50) {
        $error = 'nama kasir maksimal 50 karakter.';
    } elseif (!in_array($status, ['aktif', 'nonaktif'], true)) {
        $error = 'status harus aktif atau nonaktif.';
    } else {
        if (mysqli_execute_query($koneksi, 'INSERT INTO cashiers (nama_kasir, status) VALUES (?, ?)', [$nama_kasir, $status])) {
            header('Location: cashier.php?status=tambah');
            exit;
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
    <link rel="stylesheet" href="../../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../../home/partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../../home/partials/sidebaradmin.php'; ?>

        <div class="content">
            <div class="heading">crud cashier - add</div>
            <div class="emptybox formbox">
                <div class="sidetitle">form tambah kasir</div>

                <?php if ($error !== ''): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="newcashier.php" class="form">
                    <?= csrf_field() ?>
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

<script src="../../assets/main.js"></script>

</body>
</html>
