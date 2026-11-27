<?php

require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../home/includes/transaksihelper.php';

init_transaksi_tables($koneksi);

csrf_verify();

$id = (int) ($_POST['id_kasir'] ?? 0);
if ($id > 0) {
    $tgl = date('Y-m-d');
    mysqli_execute_query($koneksi, "UPDATE cashiers SET status = 'nonaktif', tanggal_keluar = ? WHERE id_kasir = ?", [$tgl, $id]);
}

header('Location: cashier.php?status=hapus');
exit;
