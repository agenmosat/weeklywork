<?php

require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

csrf_verify();

$id = (int) ($_POST['id_kasir'] ?? 0);
if ($id > 0) {
    $tgl = date('Y-m-d');
    $stmt = mysqli_prepare($koneksi, "UPDATE kasir SET status = 'nonaktif', tanggal_keluar = ? WHERE id_kasir = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', $tgl, $id);
        mysqli_stmt_execute($stmt);
    }
}

header('Location: cashier.php?status=hapus');
exit;
