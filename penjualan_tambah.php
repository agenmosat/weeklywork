<?php
$query = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: admin/penjualan/penjualan_tambah.php" . $query);
exit;
