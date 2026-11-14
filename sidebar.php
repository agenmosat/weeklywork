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
?>
<div class="sidebar">
    <div class="sidetitle">categories</div>
    <a href="dashboard.php" class="sidelink">all items</a>
    <?php foreach ($sidecategories as $scat): ?>
        <a href="dashboard.php?cat=<?php echo urlencode($scat); ?>" class="sidelink"><?php echo htmlspecialchars($scat); ?></a>
    <?php endforeach; ?>

    <div class="sidetitle">sort by</div>
    <a href="dashboard.php?sort=low<?php echo $sidecatparam; ?>" class="sidelink">price: low to high</a>
    <a href="dashboard.php?sort=high<?php echo $sidecatparam; ?>" class="sidelink">price: high to low</a>
    <a href="dashboard.php?sort=name<?php echo $sidecatparam; ?>" class="sidelink">name: a to z</a>
</div>
