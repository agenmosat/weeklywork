<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

$id = (int) ($_POST['id_supplier'] ?? 0);

if ($id > 0) {
    $stmt = mysqli_prepare($koneksi, 'DELETE FROM supplier WHERE id_supplier = ?');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    }
}

header('Location: supplier.php?status=hapus');
exit;
