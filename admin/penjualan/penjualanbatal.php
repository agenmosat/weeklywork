<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/transaksihelper.php';

init_transaksi_tables($koneksi);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
}

$id = (int)($_POST['id'] ?? ($_POST['id_penjualan'] ?? ($_GET['id'] ?? ($_GET['id_penjualan'] ?? 0))));

if ($id > 0) {
    $ok = batal_penjualan($koneksi, $id);
    if ($ok) {
        header('Location: penjualan.php?status=batal');
        exit;
    } else {
        header('Location: penjualan.php?status=gagal');
        exit;
    }
}

header('Location: penjualan.php');
exit;
