<?php
$query = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: admin/penjualan/penjualan_detail.php" . $query);
exit;
