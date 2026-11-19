<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/transaksi_helper.php';

init_transaksi_tables($koneksi);
csrf_verify();

$id = (int)($_POST['id'] ?? 0);

if ($id > 0) {
    $q1 = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM penjualan_detail WHERE id_product = $id");
    $r1 = mysqli_fetch_assoc($q1);
    $q2 = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pembelian_detail WHERE id_product = $id");
    $r2 = mysqli_fetch_assoc($q2);
    $q3 = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM stok WHERE id_product = $id");
    $r3 = mysqli_fetch_assoc($q3);

    $terkait = (int)$r1['total'] + (int)$r2['total'] + (int)$r3['total'];
    if ($terkait > 0) {
        header('Location: product.php?status=error_relasi');
        exit;
    }

    mysqli_query($koneksi, "DELETE FROM products WHERE id = $id");
}

header('Location: product.php?status=hapus');
exit;
