<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/transaksi_helper.php';

init_transaksi_tables($koneksi);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
}

$id = (int)($_POST['id'] ?? ($_POST['id_pembelian'] ?? ($_GET['id'] ?? ($_GET['id_pembelian'] ?? 0))));

if ($id > 0) {
    $ok = batal_pembelian($koneksi, $id);
    if ($ok) {
        header('Location: pembelian.php?status=batal');
        exit;
    } else {
        header('Location: pembelian.php?status=error_stok_kurang');
        exit;
    }
}

header('Location: pembelian.php');
exit;
