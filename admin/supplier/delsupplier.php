<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/transaksihelper.php';

init_transaksi_tables($koneksi);
csrf_verify();

$id = (int)($_POST['id_supplier'] ?? 0);

if ($id > 0) {
    $q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pembelian WHERE id_supplier = $id");
    $r = mysqli_fetch_assoc($q);
    if ((int)$r['total'] > 0) {
        header('Location: supplier.php?status=error_relasi');
        exit;
    }

    mysqli_query($koneksi, "DELETE FROM supplier WHERE id_supplier = $id");
}

header('Location: supplier.php?status=hapus');
exit;
