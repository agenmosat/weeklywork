<?php
$query = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: admin/pembelian/pembelian_tambah.php" . $query);
exit;
