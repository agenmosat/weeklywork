<?php
$sidecatparam = "";
$currentcat = isset($_GET['cat']) ? trim($_GET['cat']) : "";
if ($currentcat !== "") {
    $sidecatparam = "&cat=" . urlencode($currentcat);
}

$sidecategories = [];
if (isset($koneksi) && $koneksi) {
    $catres = mysqli_query($koneksi, "SELECT DISTINCT category FROM products ORDER BY category");
    if ($catres) {
        while ($catrow = mysqli_fetch_assoc($catres)) {
            $sidecategories[] = $catrow['category'];
        }
    }
}
if (empty($sidecategories) && isset($products)) {
    foreach ($products as $p) {
        if (!empty($p['category']) && !in_array($p['category'], $sidecategories)) {
            $sidecategories[] = $p['category'];
        }
    }
}

$alatberat = ['Excavator', 'Crane', 'Bulldozer', 'Wheel Loader', 'Forklift', 'Dump Truck', 'Concrete Mixer', 'Compactor', 'Motor Grader', 'Asphalt Paver', 'Concrete Pump', 'Genset'];
$catbahan = [];
$catalat = [];
foreach ($sidecategories as $c) {
    if (in_array($c, $alatberat)) {
        $catalat[] = $c;
    } else {
        $catbahan[] = $c;
    }
}
?>
<div class="sidebar sidebarkatalog">
    <div class="sidetitle">katalog utama</div>
    <a href="dashboard.php" class="sidelink">semua produk</a>

    <div class="sidetitle">bahan bangunan</div>
    <?php foreach ($catbahan as $scat): ?>
        <a href="dashboard.php?cat=<?php echo urlencode($scat); ?>" class="sidelink"><?php echo htmlspecialchars($scat); ?></a>
    <?php endforeach; ?>

    <div class="sidetitle">sewa alat berat</div>
    <?php foreach ($catalat as $scat): ?>
        <a href="dashboard.php?cat=<?php echo urlencode($scat); ?>" class="sidelink"><?php echo htmlspecialchars($scat); ?></a>
    <?php endforeach; ?>

    <div class="sidetitle">urutkan</div>
    <a href="dashboard.php?sort=low<?php echo $sidecatparam; ?>" class="sidelink">harga: terendah</a>
    <a href="dashboard.php?sort=high<?php echo $sidecatparam; ?>" class="sidelink">harga: tertinggi</a>
    <a href="dashboard.php?sort=name<?php echo $sidecatparam; ?>" class="sidelink">nama: a - z</a>
</div>
