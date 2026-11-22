<?php
$topbarcartcount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $topbarcartcount += $qty;
    }
}
$topbarquery = isset($_GET['q']) ? trim($_GET['q']) : "";
$topbarbase = '';
if (file_exists('config/database.php')) {
    $topbarbase = '';
} elseif (file_exists('../config/database.php')) {
    $topbarbase = '../';
} elseif (file_exists('../../config/database.php')) {
    $topbarbase = '../../';
}
?>
<div class="topbar">
    <a href="<?php echo $topbarbase; ?>home/dashboard.php" class="brand">TB. Maju Jaya</a>
    <form action="<?php echo $topbarbase; ?>home/dashboard.php" method="get" class="searchbox">
        <input type="text" name="q" placeholder="cari bahan bangunan..." class="searchinput" value="<?php echo htmlspecialchars($topbarquery); ?>">
        <button type="submit" class="searchbtn">cari</button>
    </form>
    <div class="topnav">
        <a href="<?php echo $topbarbase; ?>home/dashboard.php" class="navlink">katalog</a>
        <a href="<?php echo $topbarbase; ?>home/about.php" class="navlink">tentang kami</a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === "admin"): ?>
            <a href="<?php echo $topbarbase; ?>admin/admin.php" class="navlink">admin</a>
        <?php endif; ?>
        <a href="<?php echo $topbarbase; ?>home/cart.php" class="navlink cartlink">keranjang <span class="cartcount"><?php echo $topbarcartcount; ?></span></a>
        <?php if (isset($_SESSION['user'])): ?>
            <?php $topuser = is_array($_SESSION['user']) ? ($_SESSION['user']['username'] ?? 'user') : $_SESSION['user']; ?>
            <span class="userbadge"><?php echo htmlspecialchars($topuser); ?></span>
            <form method="post" action="<?php echo $topbarbase . 'logout.php'; ?>" class="logoutform">
                <button type="submit" class="logoutbtn">keluar</button>
            </form>
        <?php else: ?>
            <span class="userbadge">tamu</span>
            <a href="<?php echo $topbarbase; ?>index.php?login=1" class="navlink">masuk</a>
        <?php endif; ?>
        <div class="themeswitcher">
            <select id="themepick" class="themeselect" onchange="switchtheme(this.value)" aria-label="theme">
                <option value="themelight">terang</option>
                <option value="themedark">gelap</option>
            </select>
        </div>
    </div>
</div>
