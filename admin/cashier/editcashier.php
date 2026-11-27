<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../home/includes/transaksihelper.php';

init_transaksi_tables($koneksi);

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
        if (mysqli_execute_query($koneksi, 'UPDATE cashiers SET nama_kasir = ?, status = ?, tanggal_keluar = ? WHERE id_kasir = ?', [$nama_kasir, $status, $tanggal_keluar, $id_kasir])) {
            header('Location: cashier.php?status=edit');
            exit;
        }
        $error = 'gagal mengubah kasir.';
    }
} else {
    $res = mysqli_execute_query($koneksi, 'SELECT id_kasir, nama_kasir, status, tanggal_keluar FROM cashiers WHERE id_kasir = ?', [$id_kasir]);
    $row = $res ? mysqli_fetch_assoc($res) : null;
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

    <?php require __DIR__ . '/../../home/partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../../home/partials/sidebaradmin.php'; ?>

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
