<?php
$adm = file_exists('admin.php') ? '' : '../';
$store = file_exists('admin.php') ? '../home/dashboard.php' : '../../home/dashboard.php';
?>
<div class="sidebar">
    <div class="sidetitle">admin menu</div>
    <a href="<?= $adm ?>admin.php" class="sidelink">dashboard</a>
    <a href="<?= $adm ?>product/product.php" class="sidelink">product</a>
    <a href="<?= $adm ?>cashier/cashier.php" class="sidelink">cashier</a>
    <a href="<?= $adm ?>supplier/supplier.php" class="sidelink">supplier</a>
    <a href="<?= $adm ?>penjualan/penjualan.php" class="sidelink">sales</a>
    <a href="<?= $adm ?>pembelian/pembelian.php" class="sidelink">purchases</a>
    <a href="<?= $adm ?>checklist.php" class="sidelink">checklist</a>
    <div class="sidetitle">reports</div>
    <a href="<?= $adm ?>laporanpenjualan.php" class="sidelink">sales report</a>
    <a href="<?= $adm ?>laporanpembelian.php" class="sidelink">purchase report</a>
    <a href="<?= $adm ?>laporanstok.php" class="sidelink">stock report</a>
    <a href="<?= $adm ?>laporanlaba.php" class="sidelink">profit report</a>
    <div class="sidetitle">account</div>
    <a href="<?= $store ?>" class="sidelink">view store</a>
</div>
